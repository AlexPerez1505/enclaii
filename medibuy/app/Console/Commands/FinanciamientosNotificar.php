<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\User;
use App\Models\PagoFinanciamiento;
use App\Notifications\PagoFinanciamientoAlertNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route as RouteFacade;

class FinanciamientosNotificar extends Command
{
    protected $signature = 'financiamientos:notificar {--dry-run : No guarda notificaciones}';
    protected $description = 'Notifica a admins sobre pagos HOY y ATRASADOS (sin duplicar)';

    public function handle(): int
    {
        $today  = Carbon::today();
        $admins = User::where('role', 'admin')->get();

        if ($admins->isEmpty()) {
            $this->warn('No hay admins (users.role=admin).');
            return self::SUCCESS;
        }

        // ✅ HOY o ATRASADOS, NO pagados, con pendiente > 0 (si existe monto_pendiente)
        $pagos = PagoFinanciamiento::with(['venta.cliente'])
            ->whereDate('fecha_pago', '<=', $today)
            ->where(function ($q) {
                $q->whereNull('pagado')->orWhere('pagado', false);
            })
            ->where(function ($q) {
                $q->whereNull('monto_pendiente')->orWhere('monto_pendiente', '>', 0);
            })
            ->orderBy('fecha_pago', 'asc')
            ->get();

        $dry = (bool) $this->option('dry-run');

        $created = 0;

        foreach ($pagos as $pago) {
            $venta   = $pago->venta;
            $cliente = $venta?->cliente;

            $clienteNombre = trim(($cliente->nombre ?? '') . ' ' . ($cliente->apellido ?? ''));
            if ($clienteNombre === '') $clienteNombre = 'Cliente';

            $fechaPago = $pago->fecha_pago ? Carbon::parse($pago->fecha_pago) : null;
            $fechaTxt  = $fechaPago ? $fechaPago->format('d/m/Y') : 'sin fecha';

            $esAtrasado = $fechaPago ? $fechaPago->lt($today) : false;
            $esHoy      = $fechaPago ? $fechaPago->isSameDay($today) : true;

            $type  = $esHoy ? 'financiamiento_hoy' : 'financiamiento_atrasado';
            $title = $esHoy ? 'Pago programado para HOY' : 'Pago ATRASADO';

            // ✅ clave única: 1 notif por pago y tipo (hoy/atrasado)
            $uniqueKey = "pf:{$pago->id}:{$type}";

            // ✅ URL a donde quieres que vaya al dar click
            if ($venta && RouteFacade::has('ventas.pagos.index')) {
                $url = route('ventas.pagos.index', $venta->id);
            } elseif ($venta && RouteFacade::has('ventas.show')) {
                $url = route('ventas.show', $venta->id);
            } else {
                $url = url('/ventas/deudores');
            }

            $message = ($esHoy ? "Hoy vence un pago ({$fechaTxt})" : "Pago atrasado ({$fechaTxt})")
                ." · {$clienteNombre} · Remisión 2025-".($venta->id ?? 'N/A');

            foreach ($admins as $admin) {
                // ✅ evita duplicar
                $exists = $admin->unreadNotifications()
                    ->where('data->uniqueKey', $uniqueKey)
                    ->exists();

                if ($exists) continue;

                if (!$dry) {
                    $admin->notify(new PagoFinanciamientoAlertNotification(
                        title: $title,
                        message: $message,
                        url: $url,
                        type: $type,
                        uniqueKey: $uniqueKey
                    ));
                }

                $created++;
            }
        }

        $this->info("Admins: {$admins->count()} | Pagos(hoy+atrasados): {$pagos->count()} | Notifs creadas: {$created}".($dry ? " (dry-run)" : ""));
        return self::SUCCESS;
    }
}
