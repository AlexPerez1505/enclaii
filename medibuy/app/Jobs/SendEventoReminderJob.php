<?php

namespace App\Jobs;

use App\Models\Evento;
use App\Models\User;
use App\Notifications\EventoRecordatorioNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendEventoReminderJob
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $eventoId)
    {
        //
    }

    public int $tries   = 3;
    public int $timeout = 120;

    public function handle(): void
    {
        $evento = Evento::find($this->eventoId);

        if (! $evento) {
            Log::warning('SendEventoReminderJob: evento no encontrado', [
                'evento_id' => $this->eventoId,
            ]);
            return;
        }

        $tz = $evento->timezone ?: config('app.timezone', 'America/Mexico_City');

        Log::info('SendEventoReminderJob: iniciando', [
            'evento_id'           => $evento->id,
            'title'               => $evento->title,
            'start'               => optional($evento->start)->toDateTimeString(),
            'timezone'            => $tz,
            'next_reminder_at'    => optional($evento->next_reminder_at)->toDateTimeString(),
            'remind_offset_min'   => $evento->remind_offset_minutes,
            'repeat'              => $evento->repeat,
            'guests'              => $evento->guests,
        ]);

        try {
            $guestIds = $evento->guests ?? [];
            if (empty($guestIds)) {
                Log::info('SendEventoReminderJob: evento sin invitados', [
                    'evento_id' => $evento->id,
                ]);
                return;
            }

            $users = User::whereIn('id', $guestIds)->get();
            if ($users->isEmpty()) {
                Log::info('SendEventoReminderJob: no se encontraron usuarios invitados', [
                    'evento_id' => $evento->id,
                ]);
                return;
            }

            foreach ($users as $user) {
                // 📧 Correo
                $this->sendEmailReminder($evento, $user);

                // 📲 WhatsApp
                $this->sendWhatsappReminder($evento, $user);

                // 🔔 Notificación interna
                try {
                    $user->notify(new EventoRecordatorioNotification($evento));
                    Log::info('SendEventoReminderJob: notificación interna creada', [
                        'evento_id' => $evento->id,
                        'user_id'   => $user->id,
                    ]);
                } catch (Throwable $e) {
                    Log::error('SendEventoReminderJob: error creando notificación interna', [
                        'evento_id' => $evento->id,
                        'user_id'   => $user->id,
                        'error'     => $e->getMessage(),
                    ]);
                }
            }

            // Marcar como enviado y calcular siguiente recordatorio
            $evento->last_reminded_at = now('UTC');
            $evento->advanceAfterSending();
            $evento->save();

            Log::info('SendEventoReminderJob: terminado correctamente', [
                'evento_id'        => $evento->id,
                'next_reminder_at' => optional($evento->next_reminder_at)->toDateTimeString(),
            ]);
        } catch (Throwable $e) {
            Log::error('SendEventoReminderJob: excepción', [
                'evento_id' => $evento->id ?? $this->eventoId,
                'error'     => $e->getMessage(),
            ]);
            report($e);
            throw $e;
        }
    }

    protected function sendEmailReminder(Evento $evento, User $user): void
    {
        if (! $user->email) {
            return;
        }

        $tz = $evento->timezone ?: config('app.timezone', 'America/Mexico_City');

        $start = $evento->start
            ? $evento->start->copy()->setTimezone($tz)
            : null;

        $fmt = $start ? $start->isoFormat('DD/MM/YYYY HH:mm') : '-';

        $body = "Hola {$user->name},\n\n".
            "Te recordamos tu evento \"{$evento->title}\" para el {$fmt}.\n".
            "Ubicación: ".($evento->location ?: '-')."\n".
            "Notas: ".($evento->notes ?: '-')."\n\n".
            "Si necesitas reprogramar o tienes dudas, responde a este mensaje.\n\n".
            "GrupoMedibuy";

        try {
            Mail::raw($body, function ($m) use ($user, $evento) {
                $m->to($user->email)
                  ->subject('Recordatorio de evento: '.$evento->title);
            });

            Log::info('SendEventoReminderJob: correo enviado', [
                'evento_id' => $evento->id,
                'to'        => $user->email,
            ]);
        } catch (Throwable $e) {
            Log::error('SendEventoReminderJob: error enviando correo', [
                'evento_id' => $evento->id,
                'to'        => $user->email,
                'error'     => $e->getMessage(),
            ]);
        }
    }

    protected function sendWhatsappReminder(Evento $evento, User $user): void
    {
        if (! $user->phone) {
            return;
        }

        $token    = config('services.whatsapp.token');
        $phoneId  = config('services.whatsapp.phone_id');
        $version  = config('services.whatsapp.version', 'v21.0');

        $template = config('services.whatsapp.service_template_name', 'servicio_recordatorio_evento');
        $lang     = config('services.whatsapp.service_template_lang', 'es_MX');

        if (! $token || ! $phoneId) {
            Log::warning('SendEventoReminderJob: faltan credenciales de WhatsApp', [
                'evento_id' => $evento->id,
            ]);
            return;
        }

        // ✅ Tu DB guarda: 7293903384 (10 dígitos)
        // ✅ WhatsApp Cloud API requiere E164 sin "+" => 52 + 10 dígitos
        $raw = preg_replace('/\D+/', '', (string) $user->phone);

        if (strlen($raw) !== 10) {
            Log::warning('SendEventoReminderJob: teléfono inválido (se esperan 10 dígitos MX en DB)', [
                'evento_id' => $evento->id,
                'user_id'   => $user->id,
                'phone_raw' => $user->phone,
                'digits'    => $raw,
            ]);
            return;
        }

        $to = '52' . $raw; // 👈 aquí está el fix

        $tz = $evento->timezone ?: config('app.timezone', 'America/Mexico_City');

        $start = $evento->start
            ? $evento->start->copy()->setTimezone($tz)
            : null;

        $fecha = $start ? $start->isoFormat('DD [de] MMMM [de] YYYY HH:mm') : '-';

        // Anticipación (“X días” o “hoy”) — si es un offset pequeño quedará "hoy"
        $anticipacion = 'hoy';
        if ($evento->next_reminder_at && $evento->start) {
            $mins = $evento->start->diffInMinutes($evento->next_reminder_at, false);
            $days = (int) round(abs($mins) / 1440);
            if ($days > 1)      $anticipacion = "{$days} días";
            elseif ($days === 1) $anticipacion = "1 día";
        }

        $location = $evento->location ?: '-';
        $notes    = $evento->notes ?: '-';

        try {
            $resp = Http::withToken($token)->post(
                "https://graph.facebook.com/{$version}/{$phoneId}/messages",
                [
                    'messaging_product' => 'whatsapp',
                    'to'                => $to,
                    'type'              => 'template',
                    'template'          => [
                        'name'     => $template,
                        'language' => ['code' => $lang],
                        'components' => [[
                            'type'       => 'body',
                            'parameters' => [
                                ['type' => 'text', 'text' => $user->name],     // {{1}}
                                ['type' => 'text', 'text' => $evento->title],  // {{2}}
                                ['type' => 'text', 'text' => $fecha],          // {{3}}
                                ['type' => 'text', 'text' => $location],       // {{4}}
                                ['type' => 'text', 'text' => $notes],          // {{5}}
                                ['type' => 'text', 'text' => $anticipacion],   // {{6}}
                            ],
                        ]],
                    ],
                ]
            )->throw();

            Log::info('SendEventoReminderJob: WhatsApp enviado', [
                'evento_id' => $evento->id,
                'to'        => $to,
                'template'  => $template,
                'lang'      => $lang,
                'response'  => $resp->json(),
            ]);
        } catch (Throwable $e) {
            Log::error('SendEventoReminderJob: error enviando WhatsApp', [
                'evento_id' => $evento->id,
                'to'        => $to,
                'template'  => $template,
                'lang'      => $lang,
                'error'     => $e->getMessage(),
            ]);
        }
    }

    public function failed(Throwable $e): void
    {
        Log::error('SendEventoReminderJob failed', [
            'evento_id' => $this->eventoId,
            'error'     => $e->getMessage(),
        ]);
        report($e);
    }
}
