<?php

namespace App\Http\Controllers;

use App\Models\PagoFinanciamiento;
use App\Models\User;
use App\Notifications\PagoFinanciamientoAlertNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route as RouteFacade;

class CronPublicController extends Controller
{
    public function financiamientosNotificar(Request $request)
    {
        // ✅ Validación por token (para cron-job.org)
        $token     = (string) $request->query('token', '');
        $expected  = (string) config('services.cron.financiamientos_token', '');

        if ($expected === '' || !hash_equals($expected, $token)) {
            return response()->json(['ok' => false, 'error' => 'Unauthorized'], 401);
        }

        $tz    = config('app.timezone', 'America/Mexico_City');
        $today = Carbon::today($tz);

        // ✅ Admins (users.role = admin)
        $admins = User::query()->where('role', 'admin')->get();

        if ($admins->isEmpty()) {
            return response()->json([
                'ok' => true,
                'admins' => 0,
                'pagos_detectados' => 0,
                'notificaciones_enviadas' => 0,
                'msg' => 'No hay admins (users.role=admin)',
            ]);
        }

        // ✅ Pagos NO pagados: HOY o ATRASADOS
        $pagos = PagoFinanciamiento::with(['venta.cliente'])
            ->whereDate('fecha_pago', '<=', $today->toDateString())
            ->where(function ($q) {
                $q->whereNull('pagado')->orWhere('pagado', false);
            })
            ->where(function ($q) {
                // Si existe monto_pendiente, evitamos notificar pagos ya en 0
                $q->whereNull('monto_pendiente')->orWhere('monto_pendiente', '>', 0);
            })
            ->orderBy('fecha_pago', 'asc')
            ->get();

        $sent = 0;

        foreach ($pagos as $pago) {
            $venta   = $pago->venta;
            $cliente = $venta?->cliente;

            $clienteNombre = trim(($cliente->nombre ?? '') . ' ' . ($cliente->apellido ?? ''));
            if ($clienteNombre === '') $clienteNombre = 'Cliente';

            $fechaPago = $pago->fecha_pago
                ? Carbon::parse($pago->fecha_pago, $tz)
                : null;

            $isToday = $fechaPago && $fechaPago->isSameDay($today);
            $daysLate = ($fechaPago && $fechaPago->lt($today))
                ? $fechaPago->diffInDays($today)
                : 0;

            // ✅ URL click
            if ($venta && RouteFacade::has('ventas.pagos.index')) {
                $url = route('ventas.pagos.index', $venta->id);
            } elseif ($venta && RouteFacade::has('ventas.show')) {
                $url = route('ventas.show', $venta->id);
            } else {
                $url = url('/ventas/deudores');
            }

            $fechaTxt = $fechaPago ? $fechaPago->format('d/m/Y') : 'sin fecha';

            $title = $isToday
                ? 'Pago de HOY en financiamiento'
                : 'Pago ATRASADO en financiamiento';

            $extra = $isToday
                ? 'vence hoy'
                : ($daysLate > 0 ? "{$daysLate} día(s) de atraso" : 'atrasado');

            $message = "Pago {$extra} ({$fechaTxt}) · {$clienteNombre}" . ($venta ? " · Remisión 2025-{$venta->id}" : "");

            // ✅ Anti-spam: 1 notificación por pago por día
            $dedupeKey = 'pf_notif:' . $pago->id . ':' . $today->toDateString();
            if (Cache::has($dedupeKey)) {
                continue;
            }

            foreach ($admins as $admin) {
                $admin->notify(new PagoFinanciamientoAlertNotification(
                    title: $title,
                    message: $message,
                    url: $url,
                    type: 'financiamiento'
                ));
            }

            Cache::put($dedupeKey, true, now()->addHours(20));
            $sent++;
        }

        return response()->json([
            'ok' => true,
            'admins' => $admins->count(),
            'pagos_detectados' => $pagos->count(),
            'notificaciones_enviadas' => $sent,
            'today' => $today->toDateString(),
            'tz' => $tz,
        ]);
    }
}
