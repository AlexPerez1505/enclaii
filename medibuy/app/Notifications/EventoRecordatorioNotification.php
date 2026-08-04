<?php

namespace App\Notifications;

use App\Models\Evento;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class EventoRecordatorioNotification extends Notification
{
    use Queueable;

    public function __construct(public Evento $evento) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        $date = $this->evento->start
            ? $this->evento->start->copy()->setTimezone($this->evento->timezone ?: config('app.timezone', 'America/Mexico_City'))->toDateString()
            : now(config('app.timezone', 'America/Mexico_City'))->toDateString();

        return [
            'type'    => 'agenda_event_reminder',
            'title'   => 'Recordatorio de evento',
            'message' => 'Te recordamos tu evento: '.$this->evento->title,

            // 👇 para el click
            'routeName'   => 'agenda',
            'routeParams' => [
                'date'  => $date,               // YYYY-MM-DD
                'event' => $this->evento->id,   // id del evento
            ],

            // extra por si lo ocupas
            'evento_id' => $this->evento->id,
            'date'      => $date,
        ];
    }
}
