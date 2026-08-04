<?php

namespace App\Http\Controllers;

use App\Models\Evento;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class EventoController extends Controller
{
    /** LISTA PARA FULLCALENDAR */
    public function index()
    {
        $hasWpp  = $this->hasWpp();
        $eventos = Evento::orderBy('start', 'asc')->get();

        $payload = $eventos->map(function (Evento $e) use ($hasWpp) {
            return [
                'id'    => (string) $e->id,
                'title' => $e->title,
                'start' => $this->asIso($e->start),
                // ya no usamos fin, lo dejamos null para un evento "puntual"
                'end'   => null,
                'allDay' => (bool) $e->all_day,
                'extendedProps' => [
                    'location'              => $e->location,
                    'repeat'                => $e->repeat,
                    'repeat_end'            => null,  // legacy
                    'guests'                => $e->guests ?? [],
                    'notes'                 => $e->notes,
                    'remind_offset_minutes' => $e->remind_offset_minutes,
                    'timezone'              => $e->timezone,
                    'wpp'                   => $hasWpp ? ($e->wpp ?: ['enabled' => true]) : null,
                ],
            ];
        });

        return response()->json($payload);
    }

    /** MOSTRAR UNO (si lo necesitas) */
    public function show($id)
    {
        $evento = Evento::findOrFail($id);
        return response()->json($evento);
    }

    /** CREAR DESDE EL MODAL */
    public function store(Request $request)
    {
        $hasWpp = $this->hasWpp();

        $data = $request->validate([
            'title'                 => 'required|string|max:255',
            'location'              => 'nullable|string|max:1024',
            'start'                 => 'required|date',
            'all_day'               => 'nullable|boolean',
            'guests'                => 'nullable|array',
            'guests.*'              => 'nullable',
            'notes'                 => 'nullable|string',
            'repeat'                => 'required|in:none,daily,weekly,monthly',
            'timezone'              => 'nullable|string|max:80',
            'remind_offset_minutes' => 'required|integer|min:1|max:10080',
        ]);

        // Normalizar fecha
        $start = Carbon::parse($data['start']);

        $evento = new Evento();
        $evento->title      = $data['title'];
        $evento->location   = $data['location'] ?? null;
        $evento->start      = $start;
        $evento->end        = $start; // por si la columna sigue existiendo
        $evento->all_day    = (bool) ($data['all_day'] ?? false);
        $evento->guests     = $data['guests'] ?? [];
        $evento->notes      = $data['notes'] ?? null;
        $evento->repeat     = $data['repeat'] ?? 'none';
        $evento->timezone   = $data['timezone'] ?: config('app.timezone', 'America/Mexico_City');
        $evento->remind_offset_minutes = $data['remind_offset_minutes'];

        // Siempre queremos WhatsApp (y correo) activos
        if ($hasWpp) {
            $evento->wpp = ['enabled' => true];
        }

        // Calcula el next_reminder_at en base a start/offset/repeat
        $evento->computeNextReminder();
        $evento->save();

        return response()->json(['success' => true, 'id' => $evento->id], 201);
    }

    /** ACTUALIZAR (incluye drag & drop desde el calendario) */
    public function update($id, Request $request)
    {
        $evento = Evento::findOrFail($id);
        $hasWpp = $this->hasWpp();

        $data = $request->validate([
            'title'                 => 'nullable|string|max:255',
            'location'              => 'nullable|string|max:1024',
            'start'                 => 'nullable|date',
            'all_day'               => 'nullable|boolean',
            'guests'                => 'nullable|array',
            'guests.*'              => 'nullable',
            'notes'                 => 'nullable|string',
            'repeat'                => 'nullable|in:none,daily,weekly,monthly',
            'timezone'              => 'nullable|string|max:80',
            'remind_offset_minutes' => 'nullable|integer|min:1|max:10080',
        ]);

        if (array_key_exists('start', $data) && $data['start']) {
            $evento->start = Carbon::parse($data['start']);
            $evento->end   = $evento->start;
        }

        if (array_key_exists('title', $data)) {
            $evento->title = $data['title'] ?? $evento->title;
        }
        if (array_key_exists('location', $data)) {
            $evento->location = $data['location'] ?? null;
        }
        if (array_key_exists('all_day', $data)) {
            $evento->all_day = (bool) $data['all_day'];
        }
        if (array_key_exists('guests', $data)) {
            $evento->guests = $data['guests'] ?? [];
        }
        if (array_key_exists('notes', $data)) {
            $evento->notes = $data['notes'] ?? null;
        }
        if (array_key_exists('repeat', $data)) {
            $evento->repeat = $data['repeat'] ?? 'none';
        }
        if (array_key_exists('timezone', $data) && $data['timezone']) {
            $evento->timezone = $data['timezone'];
        }
        if (array_key_exists('remind_offset_minutes', $data) && $data['remind_offset_minutes']) {
            $evento->remind_offset_minutes = $data['remind_offset_minutes'];
        }

        // Refrescamos el siguiente recordatorio
        $evento->computeNextReminder();

        // Aseguramos que WhatsApp siga activo (si existe la columna)
        if ($hasWpp) {
            $wpp = $evento->wpp ?? [];
            $wpp['enabled'] = true;
            $evento->wpp = $wpp;
        }

        $evento->save();

        return response()->json(['success' => true]);
    }

    /** ELIMINAR */
    public function destroy($id)
    {
        $evento = Evento::findOrFail($id);
        $evento->delete();
        return response()->json(['success' => true]);
    }

    /** LISTA DE USUARIOS (para el select de invitados) */
    public function usuarios()
    {
        return response()->json(
            User::select('id', 'name', 'email', 'phone')
                ->orderBy('name')
                ->get()
        );
    }

    /* ===== Helpers ===== */

    private function hasWpp(): bool
    {
        return Schema::hasColumn((new Evento)->getTable(), 'wpp');
    }

    private function asIso($value): ?string
    {
        if (! $value) {
            return null;
        }
        try {
            return Carbon::parse($value)->toIso8601String();
        } catch (\Throwable $e) {
            return null;
        }
    }
}
