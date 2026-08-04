<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evento extends Model
{
    use HasFactory;

    protected $table = 'eventos';

    protected $fillable = [
        'title',
        'start',
        'end',
        'location',
        'all_day',
        'repeat',
        'repeat_end',
        'guests',
        'notes',
        'timezone',
        'remind_offset_minutes',
        'next_reminder_at',
        'last_reminded_at',
        'wpp',
    ];

    protected $casts = [
        'start'             => 'datetime',
        'end'               => 'datetime',
        'repeat_end'        => 'datetime',
        'next_reminder_at'  => 'datetime',
        'last_reminded_at'  => 'datetime',
        'all_day'           => 'boolean',
        'guests'            => 'array',
        'wpp'               => 'array',
        'remind_offset_minutes' => 'integer',
    ];

    /**
     * Calcula el próximo recordatorio para este evento,
     * según start, timezone, remind_offset_minutes y repeat.
     */
    public function computeNextReminder(): void
    {
        $tz  = $this->timezone ?: config('app.timezone', 'America/Mexico_City');
        $now = now($tz);

        if (! $this->start) {
            $this->next_reminder_at = null;
            return;
        }

        $start = $this->start->clone()->setTimezone($tz);
        $offset = max(1, (int) ($this->remind_offset_minutes ?? 1));

        // recordatorio base = inicio - offset
        $reminder = $start->clone()->subMinutes($offset);

        if ($this->repeat === 'none' || ! $this->repeat) {
            // evento de una sola vez
            $this->next_reminder_at = $reminder;
            return;
        }

        // eventos recurrentes: mover hacia adelante hasta que quede en el futuro
        $next = $reminder->clone();
        $step = match ($this->repeat) {
            'daily'   => 'day',
            'weekly'  => 'week',
            'monthly' => 'month',
            default   => null,
        };

        if (! $step) {
            $this->next_reminder_at = $reminder;
            return;
        }

        while ($next->lte($now)) {
            $next->add($step, 1);
        }

        $this->next_reminder_at = $next;
    }

    /**
     * Se llama después de enviar un recordatorio; avanza a la siguiente
     * ocurrencia según repeat. Si repeat = none, se deja en null.
     */
    public function advanceAfterSending(): void
    {
        $tz  = $this->timezone ?: config('app.timezone', 'America/Mexico_City');
        $now = now($tz);

        if (! $this->next_reminder_at) {
            $this->computeNextReminder();
            return;
        }

        if ($this->repeat === 'none' || ! $this->repeat) {
            $this->next_reminder_at = null;
            return;
        }

        $next = $this->next_reminder_at->clone()->setTimezone($tz);

        $step = match ($this->repeat) {
            'daily'   => 'day',
            'weekly'  => 'week',
            'monthly' => 'month',
            default   => null,
        };

        if (! $step) {
            $this->next_reminder_at = null;
            return;
        }

        // mover tantas veces como haga falta para dejarlo en el futuro
        while ($next->lte($now)) {
            $next->add($step, 1);
        }

        $this->next_reminder_at = $next;
    }
}
