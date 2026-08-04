<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class LowStockNotification extends Notification
{
  use Queueable;

  public function __construct(public array $items) {}

  public function via($notifiable)
  {
    return ['database', 'mail']; // quita mail si no quieres correo
  }

  public function toMail($notifiable)
  {
    return (new MailMessage)
      ->subject('Alerta: Stock bajo en inventario interno')
      ->line('Se detectaron artículos con stock bajo:')
      ->line(collect($this->items)->take(10)->map(fn($i)=>"- {$i['name']} (Stock: {$i['stock']}, Min: {$i['min']})")->implode("\n"))
      ->line('Revisa el módulo de Inventario.');
  }

  public function toDatabase($notifiable)
  {
    return [
      'type' => 'inventory_low_stock',
      'count' => count($this->items),
      'items' => $this->items,
      'message' => 'Hay artículos con stock bajo.'
    ];
  }
}
