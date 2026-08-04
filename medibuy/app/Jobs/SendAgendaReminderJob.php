<?php

namespace App\Jobs;

use App\Mail\AgendaReminderMail;
use App\Models\AgendaEvent;
use App\Models\User;
use App\Notifications\AgendaReminderSystemNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendAgendaReminderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 120;

    public function __construct(public int $eventId)
    {
    }

    /**
     * Normaliza teléfono a México en formato: 52 + 10 dígitos.
     */
    protected function normalizeMxPhone(?string $value): ?string
    {
        if (!$value) {
            return null;
        }

        $digits = preg_replace('/\D+/', '', $value);

        if (!$digits) {
            return null;
        }

        if (strlen($digits) === 10) {
            return '52' . $digits;
        }

        if (str_starts_with($digits, '52') && strlen($digits) >= 12) {
            return '52' . substr($digits, -10);
        }

        $last10 = substr($digits, -10);

        if (strlen($last10) === 10) {
            return '52' . $last10;
        }

        return null;
    }

    public function handle(): void
    {
        $event = AgendaEvent::query()->find($this->eventId);

        if (!$event) {
            Log::warning('SendAgendaReminderJob: evento no encontrado', [
                'event_id' => $this->eventId,
            ]);

            return;
        }

        if ((int) $event->completed === 1) {
            Log::info('SendAgendaReminderJob: evento completado, no se envía', [
                'event_id' => $event->id,
            ]);

            return;
        }

        $tz = $event->timezone ?: config('app.timezone', 'America/Mexico_City');

        $userIds = is_array($event->user_ids) ? $event->user_ids : [];
        $userIds = array_values(array_unique(array_filter(array_map('intval', $userIds))));

        Log::info('SendAgendaReminderJob: iniciando', [
            'event_id' => $event->id,
            'title' => $event->title,
            'timezone' => $tz,
            'send_email' => (bool) $event->send_email,
            'send_whatsapp' => (bool) $event->send_whatsapp,
            'user_ids' => $userIds,
            'next_reminder_at' => optional($event->next_reminder_at)->toDateTimeString(),
        ]);

        if (!count($userIds)) {
            Log::warning('SendAgendaReminderJob: evento sin usuarios asignados', [
                'event_id' => $event->id,
            ]);

            $event->last_reminder_sent_at = now();
            $event->advanceAfterSending();
            $event->save();

            return;
        }

        $users = User::query()
            ->whereIn('id', $userIds)
            ->get(['id', 'name', 'email', 'phone']);

        if ($users->isEmpty()) {
            Log::warning('SendAgendaReminderJob: no se encontraron usuarios válidos', [
                'event_id' => $event->id,
                'user_ids' => $userIds,
            ]);

            $event->last_reminder_sent_at = now();
            $event->advanceAfterSending();
            $event->save();

            return;
        }

        $fecha = $event->start_at
            ? $event->start_at->copy()->setTimezone($tz)->format('d/m/Y')
            : '';

        $hora = $event->start_at
            ? $event->start_at->copy()->setTimezone($tz)->format('h:i a')
            : '';

        try {
            /*
            |--------------------------------------------------------------------------
            | EMAIL
            |--------------------------------------------------------------------------
            */
            if ((bool) $event->send_email) {
                foreach ($users as $user) {
                    if (!$user->email) {
                        Log::warning('Agenda: usuario sin email, omitido', [
                            'event_id' => $event->id,
                            'user_id' => $user->id,
                        ]);

                        continue;
                    }

                    try {
                        Mail::to($user->email)->send(new AgendaReminderMail($event, $user));

                        Log::info('Agenda: correo enviado', [
                            'event_id' => $event->id,
                            'user_id' => $user->id,
                            'to' => $user->email,
                        ]);
                    } catch (Throwable $mailEx) {
                        Log::error('Agenda: error enviando correo', [
                            'event_id' => $event->id,
                            'user_id' => $user->id,
                            'to' => $user->email,
                            'error' => $mailEx->getMessage(),
                        ]);
                    }
                }
            }

            /*
            |--------------------------------------------------------------------------
            | NOTIFICACIÓN INTERNA
            |--------------------------------------------------------------------------
            */
            foreach ($users as $user) {
                try {
                    $user->notify(new AgendaReminderSystemNotification($event));

                    Log::info('Agenda: notificación interna enviada', [
                        'event_id' => $event->id,
                        'user_id' => $user->id,
                    ]);
                } catch (Throwable $notifyEx) {
                    Log::error('Agenda: error enviando notificación interna', [
                        'event_id' => $event->id,
                        'user_id' => $user->id,
                        'error' => $notifyEx->getMessage(),
                    ]);
                }
            }

            /*
            |--------------------------------------------------------------------------
            | WHATSAPP
            |--------------------------------------------------------------------------
            */
            if ((bool) $event->send_whatsapp) {
                $waClass = \App\Services\WhatsApp\WhatsAppService::class;

                if (!class_exists($waClass)) {
                    Log::warning('WhatsAppService no disponible; WhatsApp omitido', [
                        'event_id' => $event->id,
                    ]);
                } else {
                    $wa = app($waClass);
                    $templateName = config('whatsapp.template_agenda', 'agenda_recordatorio');

                    foreach ($users as $user) {
                        $to = $this->normalizeMxPhone($user->phone);

                        if (!$to) {
                            Log::warning('Agenda: usuario sin teléfono válido MX, omitido WhatsApp', [
                                'event_id' => $event->id,
                                'user_id' => $user->id,
                                'raw_phone' => $user->phone,
                            ]);

                            continue;
                        }

                        try {
                            $params = [
                                $user->name ?: 'Usuario',
                                $event->title ?: 'Evento',
                                $fecha,
                                $hora,
                            ];

                            $response = $wa->sendTemplate($to, $templateName, $params, 'es_MX');

                            Log::info('Agenda: WhatsApp enviado', [
                                'event_id' => $event->id,
                                'user_id' => $user->id,
                                'phone' => $to,
                                'template' => $templateName,
                                'response' => $response,
                            ]);
                        } catch (Throwable $waEx) {
                            Log::error('Agenda: error enviando WhatsApp', [
                                'event_id' => $event->id,
                                'user_id' => $user->id,
                                'phone' => $to,
                                'error' => $waEx->getMessage(),
                            ]);
                        }
                    }
                }
            }

            /*
            |--------------------------------------------------------------------------
            | AVANZAR RECORDATORIO
            |--------------------------------------------------------------------------
            */
            $event->last_reminder_sent_at = now();
            $event->advanceAfterSending();
            $event->save();

            Log::info('SendAgendaReminderJob: terminado correctamente', [
                'event_id' => $event->id,
                'next_reminder_at' => optional($event->next_reminder_at)->toDateTimeString(),
            ]);
        } catch (Throwable $e) {
            Log::error('SendAgendaReminderJob: excepción general', [
                'event_id' => $event->id,
                'error' => $e->getMessage(),
            ]);

            report($e);

            throw $e;
        }
    }

    public function failed(Throwable $e): void
    {
        Log::error('SendAgendaReminderJob: failed', [
            'event_id' => $this->eventId,
            'error' => $e->getMessage(),
        ]);

        report($e);
    }
}