<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AgendaEvent extends Model
{
    protected $fillable = [
        'title',
        'description',
        'start_at',
        'end_at',
        'remind_offset_minutes',
        'repeat_rule',
        'timezone',

        'send_email',
        'send_whatsapp',

        'all_day',
        'completed',
        'color',
        'category',
        'priority',
        'location',
        'notes',

        'next_reminder_at',
        'last_reminder_sent_at',
    ];

    protected $casts = [
        'send_email'            => 'boolean',
        'send_whatsapp'         => 'boolean',
        'all_day'               => 'boolean',
        'completed'             => 'boolean',
        'last_reminder_sent_at' => 'datetime',
    ];

    /**
     * Usuarios asignados al evento mediante tabla pivote agenda_event_user.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'agenda_event_user')
            ->withTimestamps();
    }

    protected static function booted(): void
    {
        static::saving(function (AgendaEvent $event) {
            if (!$event->timezone) {
                $event->timezone = config('app.timezone', 'America/Mexico_City');
            }

            if (!$event->repeat_rule) {
                $event->repeat_rule = 'none';
            }

            if (!$event->remind_offset_minutes) {
                $event->remind_offset_minutes = 15;
            }

            if ($event->completed) {
                $event->attributes['next_reminder_at'] = null;
                return;
            }

            /*
             * Recalcular recordatorio cuando el evento es nuevo o cambian campos clave.
             */
            if (
                !$event->exists ||
                $event->isDirty('start_at') ||
                $event->isDirty('remind_offset_minutes') ||
                $event->isDirty('repeat_rule') ||
                $event->isDirty('timezone') ||
                $event->isDirty('completed')
            ) {
                $event->computeNextReminder();
            }
        });
    }

    public function setStartAtAttribute($value): void
    {
        $this->attributes['start_at'] = $this->normalizeDateTimeValue($value);
    }

    public function getStartAtAttribute($value): ?Carbon
    {
        return $this->castDateTimeFromStorage($value);
    }

    public function setEndAtAttribute($value): void
    {
        $this->attributes['end_at'] = $this->normalizeDateTimeValue($value);
    }

    public function getEndAtAttribute($value): ?Carbon
    {
        return $this->castDateTimeFromStorage($value);
    }

    public function setNextReminderAtAttribute($value): void
    {
        $this->attributes['next_reminder_at'] = $this->normalizeDateTimeValue($value);
    }

    public function getNextReminderAtAttribute($value): ?Carbon
    {
        return $this->castDateTimeFromStorage($value);
    }

    protected function normalizeDateTimeValue($value): ?string
    {
        if (!$value) {
            return null;
        }

        if ($value instanceof Carbon) {
            return $value->format('Y-m-d H:i:s');
        }

        if ($value instanceof \DateTimeInterface) {
            return Carbon::instance($value)->format('Y-m-d H:i:s');
        }

        if (is_string($value)) {
            $value = trim($value);

            if ($value === '') {
                return null;
            }

            /*
             * Formato HTML datetime-local:
             * 2026-06-06T09:00
             */
            if (preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}$/', $value)) {
                return str_replace('T', ' ', $value) . ':00';
            }

            /*
             * Formato ISO:
             * 2026-06-06T09:00:00.000000Z
             * 2026-06-06T09:00:00-06:00
             */
            if (preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}/', $value)) {
                return Carbon::parse($value)
                    ->setTimezone($this->timezone ?: config('app.timezone', 'America/Mexico_City'))
                    ->format('Y-m-d H:i:s');
            }

            /*
             * Formato local:
             * 2026-06-06 09:00
             * 2026-06-06 09:00:00
             */
            if (preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}(:\d{2})?$/', $value)) {
                return Carbon::parse($value)
                    ->format('Y-m-d H:i:s');
            }
        }

        return Carbon::parse($value)
            ->setTimezone($this->timezone ?: config('app.timezone', 'America/Mexico_City'))
            ->format('Y-m-d H:i:s');
    }

    protected function castDateTimeFromStorage($value): ?Carbon
    {
        if (!$value) {
            return null;
        }

        $tz = $this->timezone ?: config('app.timezone', 'America/Mexico_City');

        try {
            return Carbon::createFromFormat('Y-m-d H:i:s', $value, $tz)->setTimezone($tz);
        } catch (\Throwable $e) {
            return Carbon::parse($value, $tz)->setTimezone($tz);
        }
    }

    /**
     * Calcula el próximo recordatorio del evento.
     */
    public function computeNextReminder(): void
    {
        $tz = $this->timezone ?: config('app.timezone', 'America/Mexico_City');

        /** @var Carbon|null $start */
        $start = $this->start_at;

        if (!$start || !$this->remind_offset_minutes) {
            $this->attributes['next_reminder_at'] = null;
            return;
        }

        if ($this->completed) {
            $this->attributes['next_reminder_at'] = null;
            return;
        }

        $start = $start->copy()->setTimezone($tz);
        $offset = (int) $this->remind_offset_minutes;
        $reminder = $start->copy()->subMinutes($offset);

        /*
         * Si el evento no se repite y el recordatorio ya pasó,
         * no dejamos next_reminder_at activo para evitar envíos atrasados.
         */
        if (($this->repeat_rule ?: 'none') === 'none' && $reminder->lt(now($tz)->subMinute())) {
            $this->attributes['next_reminder_at'] = null;
            return;
        }

        /*
         * Si es repetitivo y el recordatorio ya pasó, avanzamos hasta la siguiente ocurrencia futura.
         */
        if (($this->repeat_rule ?: 'none') !== 'none' && $reminder->lt(now($tz)->subMinute())) {
            $this->advanceRepeatedReminderUntilFuture($start, $offset, $tz);
            return;
        }

        $this->attributes['start_at'] = $start->format('Y-m-d H:i:s');
        $this->attributes['next_reminder_at'] = $reminder->format('Y-m-d H:i:s');
    }

    protected function advanceRepeatedReminderUntilFuture(Carbon $start, int $offset, string $tz): void
    {
        $repeatRule = $this->repeat_rule ?: 'none';

        if (!in_array($repeatRule, ['daily', 'weekly', 'monthly'], true)) {
            $this->attributes['next_reminder_at'] = null;
            return;
        }

        /** @var Carbon|null $currentEnd */
        $currentEnd = $this->end_at;
        $currentEnd = $currentEnd ? $currentEnd->copy()->setTimezone($tz) : null;

        $durationMinutes = null;

        if ($currentEnd && $currentEnd->gte($start)) {
            $durationMinutes = $start->diffInMinutes($currentEnd);
        }

        $nextStart = $start->copy();
        $now = now($tz);

        do {
            switch ($repeatRule) {
                case 'daily':
                    $nextStart->addDay();
                    break;

                case 'weekly':
                    $nextStart->addWeek();
                    break;

                case 'monthly':
                    $nextStart->addMonthNoOverflow();
                    break;
            }

            $nextReminder = $nextStart->copy()->subMinutes($offset);
        } while ($nextReminder->lt($now));

        $this->attributes['start_at'] = $nextStart->format('Y-m-d H:i:s');

        if ($durationMinutes !== null && $durationMinutes > 0) {
            $this->attributes['end_at'] = $nextStart->copy()
                ->addMinutes($durationMinutes)
                ->format('Y-m-d H:i:s');
        }

        $this->attributes['next_reminder_at'] = $nextReminder->format('Y-m-d H:i:s');
    }

    /**
     * Después de enviar el recordatorio:
     * - Si no se repite, apaga next_reminder_at.
     * - Si se repite, mueve start_at/end_at y calcula el siguiente recordatorio.
     */
    public function advanceAfterSending(): void
    {
        $tz = $this->timezone ?: config('app.timezone', 'America/Mexico_City');

        /** @var Carbon|null $currentStart */
        $currentStart = $this->start_at;

        /** @var Carbon|null $currentEnd */
        $currentEnd = $this->end_at;

        if (!$currentStart || !$this->remind_offset_minutes) {
            $this->attributes['next_reminder_at'] = null;
            return;
        }

        if ($this->completed) {
            $this->attributes['next_reminder_at'] = null;
            return;
        }

        $repeatRule = $this->repeat_rule ?: 'none';

        if ($repeatRule === 'none') {
            $this->attributes['next_reminder_at'] = null;
            return;
        }

        if (!in_array($repeatRule, ['daily', 'weekly', 'monthly'], true)) {
            $this->attributes['next_reminder_at'] = null;
            return;
        }

        $currentStart = $currentStart->copy()->setTimezone($tz);
        $currentEnd = $currentEnd ? $currentEnd->copy()->setTimezone($tz) : null;

        $durationMinutes = null;

        if ($currentEnd && $currentEnd->gte($currentStart)) {
            $durationMinutes = $currentStart->diffInMinutes($currentEnd);
        }

        $newStart = $currentStart->copy();
        $offset = (int) $this->remind_offset_minutes;
        $now = now($tz);

        do {
            switch ($repeatRule) {
                case 'daily':
                    $newStart->addDay();
                    break;

                case 'weekly':
                    $newStart->addWeek();
                    break;

                case 'monthly':
                    $newStart->addMonthNoOverflow();
                    break;
            }

            $nextReminder = $newStart->copy()->subMinutes($offset);
        } while ($nextReminder->lt($now));

        $this->attributes['start_at'] = $newStart->format('Y-m-d H:i:s');

        if ($durationMinutes !== null && $durationMinutes > 0) {
            $newEnd = $newStart->copy()->addMinutes($durationMinutes);
            $this->attributes['end_at'] = $newEnd->format('Y-m-d H:i:s');
        }

        $this->attributes['next_reminder_at'] = $nextReminder->format('Y-m-d H:i:s');
    }
}