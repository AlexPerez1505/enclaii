<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ProximoPagoNotification extends Notification
{
    public $venta;
    public $fechaPago;

    public function __construct($venta, $fechaPago)
    {
        $this->venta = $venta;
        $this->fechaPago = $fechaPago;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

public function toMail($notifiable)
{
    $esAdmin = method_exists($notifiable, 'is_admin') && $notifiable->is_admin;

    $mail = (new MailMessage)
        ->subject('🔔 Recordatorio de pago próximo')
        ->line('Venta #: ' . $this->venta->id)
        ->line('Pago programado para el día ' . $this->fechaPago->format('d/m/Y'))
        ->line('Total restante: $' . number_format($this->venta->total - $this->venta->pagos->sum('monto'), 2))
        ->action('Ver detalle', route('ventas.pagos.index', $this->venta->id));

    if ($esAdmin) {
        $mail->greeting('Hola administrador,');
    } else {
        $mail->greeting('Hola ' . $notifiable->nombre . ',');
        $mail->line('Gracias por tu preferencia.');
    }

    return $mail;
}

}
