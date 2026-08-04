<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PagoFinanciamientoAlertNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $title,
        public string $message,
        public string $url,
        public ?string $type = 'financiamiento',
        public ?string $key = null, // ✅
        public ?int $pago_financiamiento_id = null, // ✅
        public ?int $venta_id = null // ✅
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'title'   => $this->title,
            'message' => $this->message,
            'url'     => $this->url,
            'type'    => $this->type,
            'key'     => $this->key,
            'pago_financiamiento_id' => $this->pago_financiamiento_id,
            'venta_id' => $this->venta_id,
        ];
    }
}
