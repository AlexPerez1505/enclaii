<?php

namespace App\Notifications;

use App\Models\SolicitudMaterial;
use App\Mail\SolicitudMaterialMarkdown;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class NuevaSolicitudRecibida extends Notification
{
    use Queueable;

    protected $solicitud;

    public function __construct(SolicitudMaterial $solicitud)
    {
        $this->solicitud = $solicitud;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        // Retorna el Mailable en lugar del MailMessage
        return (new SolicitudMaterialMarkdown($this->solicitud))->to($notifiable->email);
    }
}
