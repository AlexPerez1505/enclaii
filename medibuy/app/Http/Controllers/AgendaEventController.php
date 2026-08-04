<?php

namespace App\Http\Controllers;

use App\Models\AgendaEvent;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AgendaEventController extends Controller
{
    public function calendar()
    {
        return view('agenda.calendar');
    }

    public function summary()
    {
        return view('agenda.summary');
    }

    public function users()
    {
        return response()->json(
            User::query()
                ->select(['id', 'name', 'email', 'phone'])
                ->orderBy('name')
                ->get()
        );
    }

    public function feed(Request $r)
    {
        $start = $r->query('start');
        $end   = $r->query('end');

        $startDb = null;
        $endDb   = null;

        try {
            if ($start) {
                $startDb = Carbon::parse($start)->format('Y-m-d H:i:s');
            }

            if ($end) {
                $endDb = Carbon::parse($end)->format('Y-m-d H:i:s');
            }
        } catch (\Throwable $e) {
            Log::warning('Agenda feed: no se pudo parsear rango start/end: ' . $e->getMessage());
        }

        $query = AgendaEvent::query()
            ->with('users:id,name,email,phone')
            ->when($startDb, fn ($q) => $q->where('start_at', '>=', $startDb))
            ->when($endDb, fn ($q) => $q->where('start_at', '<=', $endDb));

        $events = $query->get()
            ->filter(fn ($e) => (bool) $e->start_at)
            ->map(function (AgendaEvent $e) {
                $startIso = $e->start_at ? $e->start_at->toIso8601String() : null;
                $endIso   = $e->end_at ? $e->end_at->toIso8601String() : null;
                $nextIso  = $e->next_reminder_at ? $e->next_reminder_at->toIso8601String() : null;

                return [
                    'id'     => $e->id,
                    'title'  => $e->title,
                    'start'  => $startIso,
                    'end'    => $endIso,
                    'allDay' => (bool) $e->all_day,

                    'extendedProps' => [
                        'description'           => $e->description,
                        'notes'                 => $e->notes,
                        'timezone'              => $e->timezone,
                        'repeat_rule'           => $e->repeat_rule,
                        'remind_offset_minutes' => $e->remind_offset_minutes,

                        'user_ids'              => $e->users->pluck('id')->values()->all(),
                        'send_email'            => (bool) $e->send_email,
                        'send_whatsapp'         => (bool) $e->send_whatsapp,

                        'next_reminder_at'      => $nextIso,

                        'completed'             => (bool) $e->completed,
                        'color'                 => $e->color ?: 'indigo',
                        'category'              => $e->category ?: 'general',
                        'priority'              => $e->priority ?: 'media',
                        'location'              => $e->location,
                    ],
                ];
            })
            ->values();

        return response()->json($events);
    }

    /**
     * IA: convierte texto libre en datos estructurados para llenar el modal.
     */
    public function aiParse(Request $r)
    {
        $data = $r->validate([
            'prompt'   => ['required', 'string', 'max:5000'],
            'timezone' => ['nullable', 'string', 'max:80'],
            'today'    => ['nullable', 'string', 'max:20'],
        ]);

        $prompt = trim($data['prompt']);
        $timezone = $data['timezone'] ?? config('app.timezone', 'America/Mexico_City');
        $today = $data['today'] ?? now($timezone)->format('Y-m-d');

        $fallback = $this->localAiFallback($prompt, $timezone, $today);

        $apiKey = config('services.openai.key');

        if (!$apiKey) {
            Log::warning('Agenda IA: OPENAI_API_KEY no configurada, usando fallback local.');

            return response()->json($fallback);
        }

        try {
            $response = Http::withToken($apiKey)
                ->timeout((int) config('services.openai.timeout', 45))
                ->acceptJson()
                ->asJson()
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => config('services.openai.primary', 'gpt-4o'),
                    'response_format' => [
                        'type' => 'json_object',
                    ],
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => implode("\n", [
                                'Eres un asistente experto en agenda empresarial.',
                                'Tu tarea es convertir una instrucción en español en JSON válido.',
                                'Devuelve únicamente JSON. No uses markdown. No expliques nada.',
                                '',
                                'Campos obligatorios:',
                                'title, date, start_time, end_time, all_day, category, priority, remind_offset_minutes, location, notes, color.',
                                '',
                                'Reglas:',
                                'date debe ser formato YYYY-MM-DD.',
                                'start_time debe ser formato HH:mm de 24 horas.',
                                'end_time debe ser formato HH:mm de 24 horas.',
                                'all_day debe ser boolean true o false.',
                                'category solo puede ser: administracion, sistemas, almacen, contabilidad, logistica, ventas, general.',
                                'priority solo puede ser: baja, media, alta.',
                                'remind_offset_minutes solo puede ser: 5, 15, 30, 60, 1440.',
                                'color solo puede ser: indigo, emerald, violet, rose, sky, amber.',
                                '',
                                'Si falta fecha, usa today.',
                                'Si dice hoy, usa today.',
                                'Si dice mañana, calcula el día siguiente con base en today.',
                                'Si dice pasado mañana, calcula dos días después de today.',
                                'Si falta hora, usa 09:00 a 10:00.',
                                'Si solo menciona una hora de inicio, calcula una duración de 1 hora.',
                                'Si dice todo el día, all_day debe ser true, start_time 09:00 y end_time 18:00.',
                                'Si no hay ubicación, usa string vacío.',
                                'Si no hay notas, resume la instrucción en notes.',
                                'El título debe ser breve, profesional y claro.',
                                'No inventes personas ni user_ids.',
                            ]),
                        ],
                        [
                            'role' => 'user',
                            'content' => json_encode([
                                'today' => $today,
                                'timezone' => $timezone,
                                'prompt' => $prompt,
                            ], JSON_UNESCAPED_UNICODE),
                        ],
                    ],
                ]);

            if (!$response->successful()) {
                Log::error('Agenda IA: error OpenAI', [
                    'status' => $response->status(),
                    'body' => $response->json(),
                ]);

                return response()->json($fallback);
            }

            $content = $response->json('choices.0.message.content');

            $json = json_decode($content, true);

            if (!is_array($json)) {
                Log::warning('Agenda IA: OpenAI no devolvió JSON válido', [
                    'raw' => $content,
                ]);

                return response()->json($fallback);
            }

            return response()->json($this->sanitizeAiResult($json, $prompt, $timezone, $today));
        } catch (\Throwable $e) {
            Log::error('Agenda IA: excepción procesando prompt', [
                'error' => $e->getMessage(),
            ]);

            return response()->json($fallback);
        }
    }

    protected function sanitizeAiResult(array $json, string $prompt, string $timezone, string $today): array
    {
        $categories = [
            'administracion',
            'sistemas',
            'almacen',
            'contabilidad',
            'logistica',
            'ventas',
            'general',
        ];

        $priorities = [
            'baja',
            'media',
            'alta',
        ];

        $colors = [
            'indigo',
            'emerald',
            'violet',
            'rose',
            'sky',
            'amber',
        ];

        $reminders = [
            5,
            15,
            30,
            60,
            1440,
        ];

        $fallback = $this->localAiFallback($prompt, $timezone, $today);

        $title = trim((string) ($json['title'] ?? $fallback['title']));
        $date = trim((string) ($json['date'] ?? $fallback['date']));
        $startTime = trim((string) ($json['start_time'] ?? $fallback['start_time']));
        $endTime = trim((string) ($json['end_time'] ?? $fallback['end_time']));
        $category = trim((string) ($json['category'] ?? $fallback['category']));
        $priority = trim((string) ($json['priority'] ?? $fallback['priority']));
        $color = trim((string) ($json['color'] ?? $fallback['color']));
        $location = trim((string) ($json['location'] ?? ''));
        $notes = trim((string) ($json['notes'] ?? $prompt));

        $allDay = filter_var($json['all_day'] ?? false, FILTER_VALIDATE_BOOLEAN);

        $remindOffset = (int) ($json['remind_offset_minutes'] ?? 15);

        if (!$title) {
            $title = $fallback['title'];
        }

        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            $date = $fallback['date'];
        }

        if (!preg_match('/^\d{2}:\d{2}$/', $startTime)) {
            $startTime = $fallback['start_time'];
        }

        if (!preg_match('/^\d{2}:\d{2}$/', $endTime)) {
            $endTime = $fallback['end_time'];
        }

        if (!in_array($category, $categories, true)) {
            $category = 'general';
        }

        if (!in_array($priority, $priorities, true)) {
            $priority = 'media';
        }

        if (!in_array($color, $colors, true)) {
            $color = 'indigo';
        }

        if (!in_array($remindOffset, $reminders, true)) {
            $remindOffset = 15;
        }

        return [
            'title' => mb_substr($title, 0, 180),
            'date' => $date,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'all_day' => $allDay,
            'category' => $category,
            'priority' => $priority,
            'remind_offset_minutes' => $remindOffset,
            'location' => mb_substr($location, 0, 255),
            'notes' => mb_substr($notes, 0, 4000),
            'color' => $color,
        ];
    }

    protected function localAiFallback(string $prompt, string $timezone, string $today): array
    {
        $text = mb_strtolower($prompt);
        $baseDate = Carbon::parse($today, $timezone);

        if (str_contains($text, 'pasado mañana')) {
            $date = $baseDate->copy()->addDays(2);
        } elseif (str_contains($text, 'mañana')) {
            $date = $baseDate->copy()->addDay();
        } else {
            $date = $baseDate->copy();
        }

        $allDay = str_contains($text, 'todo el día') || str_contains($text, 'todo el dia');

        $startTime = '09:00';
        $endTime = '10:00';

        if (preg_match('/\b([01]?\d|2[0-3]):([0-5]\d)\b/u', $text, $m)) {
            $hour = str_pad($m[1], 2, '0', STR_PAD_LEFT);
            $minute = $m[2];

            $startTime = "{$hour}:{$minute}";
            $endTime = Carbon::createFromFormat('H:i', $startTime)->addHour()->format('H:i');
        } elseif (preg_match('/\b([1-9]|1[0-2])\s*(am|pm)\b/u', $text, $m)) {
            $hour = (int) $m[1];
            $ampm = $m[2];

            if ($ampm === 'pm' && $hour < 12) {
                $hour += 12;
            }

            if ($ampm === 'am' && $hour === 12) {
                $hour = 0;
            }

            $startTime = str_pad((string) $hour, 2, '0', STR_PAD_LEFT) . ':00';
            $endTime = Carbon::createFromFormat('H:i', $startTime)->addHour()->format('H:i');
        }

        if ($allDay) {
            $startTime = '09:00';
            $endTime = '18:00';
        }

        $category = 'general';

        if (str_contains($text, 'sistema') || str_contains($text, 'software') || str_contains($text, 'soporte') || str_contains($text, 'api')) {
            $category = 'sistemas';
        } elseif (str_contains($text, 'almacén') || str_contains($text, 'almacen') || str_contains($text, 'inventario')) {
            $category = 'almacen';
        } elseif (str_contains($text, 'contabilidad') || str_contains($text, 'factura') || str_contains($text, 'pago')) {
            $category = 'contabilidad';
        } elseif (str_contains($text, 'logística') || str_contains($text, 'logistica') || str_contains($text, 'entrega')) {
            $category = 'logistica';
        } elseif (str_contains($text, 'venta') || str_contains($text, 'cliente')) {
            $category = 'ventas';
        } elseif (str_contains($text, 'administración') || str_contains($text, 'administracion') || str_contains($text, 'junta')) {
            $category = 'administracion';
        }

        $priority = 'media';

        if (str_contains($text, 'urgente') || str_contains($text, 'importante') || str_contains($text, 'alta prioridad')) {
            $priority = 'alta';
        } elseif (str_contains($text, 'baja prioridad') || str_contains($text, 'sin urgencia')) {
            $priority = 'baja';
        }

        $reminder = 15;

        if (str_contains($text, '5 minutos')) {
            $reminder = 5;
        } elseif (str_contains($text, '30 minutos')) {
            $reminder = 30;
        } elseif (str_contains($text, '1 hora') || str_contains($text, 'una hora')) {
            $reminder = 60;
        } elseif (str_contains($text, '1 día') || str_contains($text, 'un día') || str_contains($text, 'un dia')) {
            $reminder = 1440;
        }

        $title = $this->guessTitleFromPrompt($prompt);

        return [
            'title' => $title,
            'date' => $date->format('Y-m-d'),
            'start_time' => $startTime,
            'end_time' => $endTime,
            'all_day' => $allDay,
            'category' => $category,
            'priority' => $priority,
            'remind_offset_minutes' => $reminder,
            'location' => '',
            'notes' => $prompt,
            'color' => $priority === 'alta' ? 'rose' : 'indigo',
        ];
    }

    protected function guessTitleFromPrompt(string $prompt): string
    {
        $clean = trim(preg_replace('/\s+/', ' ', $prompt));

        if (!$clean) {
            return 'Nuevo evento';
        }

        $clean = preg_replace('/^(crear|agenda|agendar|programa|programar|recordar|recuérdame|recuerdame)\s+/iu', '', $clean);

        $title = mb_substr($clean, 0, 70);

        return ucfirst($title);
    }

    protected function datetimeLocalToDbString(string $input, string $timezone): string
    {
        $input = trim($input);

        try {
            if (
                preg_match('/T\d{2}:\d{2}:\d{2}/', $input) ||
                preg_match('/(Z|[+\-]\d{2}:?\d{2})$/i', $input)
            ) {
                $dt = Carbon::parse($input)->setTimezone($timezone);
            } else {
                $dt = Carbon::createFromFormat('Y-m-d\TH:i', $input, $timezone);
            }

            return $dt->format('Y-m-d H:i:s');
        } catch (\Throwable $ex) {
            Log::warning("datetimeLocalToDbString fallback parse '{$input}' tz '{$timezone}': " . $ex->getMessage());

            $dt = Carbon::parse($input)->setTimezone($timezone);

            return $dt->format('Y-m-d H:i:s');
        }
    }

    protected function normalizePayload(array $data): array
    {
        $tz = 'America/Mexico_City';

        $data['timezone'] = $tz;
        $data['send_email'] = true;
        $data['send_whatsapp'] = true;

        $data['user_ids'] = array_values(array_unique(array_map('intval', $data['user_ids'] ?? [])));

        $data['start_at'] = $this->datetimeLocalToDbString($data['start_at'], $tz);
        $data['end_at']   = $this->datetimeLocalToDbString($data['end_at'], $tz);

        $data['all_day']   = (bool) ($data['all_day'] ?? false);
        $data['completed'] = (bool) ($data['completed'] ?? false);

        $data['color']    = $data['color'] ?? 'indigo';
        $data['category'] = $data['category'] ?? 'general';
        $data['priority'] = $data['priority'] ?? 'media';
        $data['location'] = $data['location'] ?? null;
        $data['notes']    = $data['notes'] ?? null;

        return $data;
    }

    protected function rules(): array
    {
        return [
            'title'                 => ['required', 'string', 'max:180'],
            'description'           => ['nullable', 'string', 'max:2000'],
            'start_at'              => ['required', 'string'],
            'end_at'                => ['required', 'string'],
            'remind_offset_minutes' => ['required', 'integer', 'min:1', 'max:10080'],
            'repeat_rule'           => ['required', 'in:none,daily,weekly,monthly'],

            'user_ids'              => ['required', 'array', 'min:1'],
            'user_ids.*'            => ['integer', 'exists:users,id'],

            'send_email'            => ['nullable', 'boolean'],
            'send_whatsapp'         => ['nullable', 'boolean'],

            'all_day'               => ['nullable', 'boolean'],
            'completed'             => ['nullable', 'boolean'],
            'color'                 => ['nullable', 'in:indigo,rose,emerald,amber,sky,violet'],
            'category'              => ['nullable', 'in:administracion,sistemas,almacen,contabilidad,logistica,ventas,general'],
            'priority'              => ['nullable', 'in:baja,media,alta'],
            'location'              => ['nullable', 'string', 'max:255'],
            'notes'                 => ['nullable', 'string', 'max:4000'],
        ];
    }

    public function store(Request $r)
    {
        $data = $r->validate($this->rules());
        $data = $this->normalizePayload($data);

        $userIds = $data['user_ids'];
        unset($data['user_ids']);

        $event = new AgendaEvent($data);
        $event->computeNextReminder();
        $event->save();

        $event->users()->sync($userIds);

        return response()->json([
            'ok' => true,
            'id' => $event->id,
        ]);
    }

    public function show(AgendaEvent $agenda)
    {
        $agenda->load('users:id,name,email,phone');

        return response()->json([
            'id' => $agenda->id,
            'title' => $agenda->title,
            'description' => $agenda->description,
            'start_at' => $agenda->start_at,
            'end_at' => $agenda->end_at,
            'remind_offset_minutes' => $agenda->remind_offset_minutes,
            'repeat_rule' => $agenda->repeat_rule,
            'timezone' => $agenda->timezone,
            'send_email' => (bool) $agenda->send_email,
            'send_whatsapp' => (bool) $agenda->send_whatsapp,
            'all_day' => (bool) $agenda->all_day,
            'completed' => (bool) $agenda->completed,
            'color' => $agenda->color,
            'category' => $agenda->category,
            'priority' => $agenda->priority,
            'location' => $agenda->location,
            'notes' => $agenda->notes,
            'next_reminder_at' => $agenda->next_reminder_at,
            'last_reminder_sent_at' => $agenda->last_reminder_sent_at,
            'user_ids' => $agenda->users->pluck('id')->values()->all(),
            'users' => $agenda->users,
        ]);
    }

    public function update(Request $r, AgendaEvent $agenda)
    {
        $data = $r->validate($this->rules());
        $data = $this->normalizePayload($data);

        $userIds = $data['user_ids'];
        unset($data['user_ids']);

        $agenda->fill($data);
        $agenda->computeNextReminder();
        $agenda->save();

        $agenda->users()->sync($userIds);

        return response()->json(['ok' => true]);
    }

    public function destroy(AgendaEvent $agenda)
    {
        $agenda->users()->detach();
        $agenda->delete();

        return response()->json(['ok' => true]);
    }

    public function move(Request $r, AgendaEvent $agenda)
    {
        $data = $r->validate([
            'start_at' => ['required', 'string'],
            'end_at'   => ['nullable', 'string'],
        ]);

        $tz = $agenda->timezone ?: 'America/Mexico_City';

        $agenda->start_at = $this->datetimeLocalToDbString($data['start_at'], $tz);

        if (!empty($data['end_at'])) {
            $agenda->end_at = $this->datetimeLocalToDbString($data['end_at'], $tz);
        }

        $agenda->computeNextReminder();
        $agenda->save();

        return response()->json(['ok' => true]);
    }
    public function toggleCompleted(Request $r, AgendaEvent $agenda)
{
    $data = $r->validate([
        'completed' => ['nullable', 'boolean'],
    ]);

    $completed = array_key_exists('completed', $data)
        ? (bool) $data['completed']
        : ! (bool) $agenda->completed;

    $agenda->completed = $completed;

    if ($completed) {
        $agenda->next_reminder_at = null;
    } else {
        $agenda->computeNextReminder();
    }

    $agenda->save();

    return response()->json([
        'ok' => true,
        'id' => $agenda->id,
        'completed' => (bool) $agenda->completed,
        'next_reminder_at' => $agenda->next_reminder_at
            ? $agenda->next_reminder_at->toIso8601String()
            : null,
    ]);
}
}