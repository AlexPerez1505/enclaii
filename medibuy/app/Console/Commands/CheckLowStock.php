<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\InventoryItem;
use App\Models\User;
use App\Notifications\LowStockNotification;

class CheckLowStock extends Command
{
  protected $signature = 'inventory:check-low-stock';
  protected $description = 'Notifica cuando hay artículos en stock bajo';

  public function handle()
  {
    $low = InventoryItem::whereColumn('stock', '<=', 'stock_min')->get();

    if ($low->isEmpty()) {
      $this->info('OK: sin stock bajo.');
      return 0;
    }

    $items = $low->map(fn($i)=>[
      'id'=>$i->id,
      'name'=>$i->name,
      'stock'=>$i->stock,
      'min'=>$i->stock_min,
      'category'=>$i->category?->name
    ])->values()->all();

    // Admins: ajusta esta lógica a tu sistema (rol, email, etc.)
    $admins = User::where('is_admin', 1)->get();
    if ($admins->isEmpty()) {
      $this->warn('No hay admins (is_admin=1) para notificar.');
      return 0;
    }

    foreach ($admins as $admin) {
      $admin->notify(new LowStockNotification($items));
    }

    $this->info('Notificaciones enviadas: '.count($admins));
    return 0;
  }
}
