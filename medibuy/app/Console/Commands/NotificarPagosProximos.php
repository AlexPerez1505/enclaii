<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Venta;
use App\Models\User;
use App\Notifications\ProximoPagoNotification;
use Carbon\Carbon;

class NotificarPagosProximos extends Command
{
    protected $signature = 'notificar:pagos-proximos';
    protected $description = 'Enviar notificaciones de pagos próximos a clientes y administradores';

    public function handle()
    {
        $this->info('Buscando ventas con pagos próximos...');

        $ventas = Venta::with('cliente', 'pagos')
            ->where('estado', 'pendiente')
            ->get();

        $hoy = Carbon::now();
        $limite = $hoy->copy()->addDays(7);

        foreach ($ventas as $venta) {
            // Supongo que $venta->detalle_financiamiento es texto con fechas, 
            // y tienes una función que remueve pago inicial (ajusta según tu app)
            $detalle = $this->removerPagoInicialDelDetalle($venta->detalle_financiamiento);

            preg_match_all('/\b(\d{2}[\/\-]\d{2}[\/\-]\d{4}|\d{4}[\/\-]\d{2}[\/\-]\d{2}|\d{2}\s+de\s+\w+\s+de\s+\d{4})\b/i', $detalle, $matches);
            $fechas = $matches[0];

            foreach ($fechas as $fechaTexto) {
                try {
                    $fecha = Carbon::parse(str_ireplace(' de ', ' ', $fechaTexto));
                } catch (\Exception $e) {
                    continue; // Ignorar fechas no válidas
                }

                if ($fecha->between($hoy, $limite)) {
                    // Notificar cliente
                    $venta->cliente->notify(new ProximoPagoNotification($venta, $fecha));
                    $this->info("Notificado cliente: {$venta->cliente->nombre} para venta #{$venta->id}");

                    // Notificar administradores
                    $admins = User::where('is_admin', true)->get();
                    foreach ($admins as $admin) {
                        $admin->notify(new ProximoPagoNotification($venta, $fecha));
                        $this->info("Notificado admin: {$admin->name} para venta #{$venta->id}");
                    }

                    break; // Solo notificar una vez por venta
                }
            }
        }

        $this->info('Proceso de notificaciones finalizado.');
        return 0;
    }

    // Debes definir esta función según tu lógica para eliminar pago inicial
    protected function removerPagoInicialDelDetalle($detalle)
    {
        // Ejemplo simple, ajusta a tu lógica real
        return preg_replace('/Pago inicial.*$/s', '', $detalle);
    }
}
