<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Schema;

class BentoController extends Controller
{
    public function index()
    {
        $cotizacionRentaModel = class_exists(\App\Models\CotizacionRenta::class)
            ? \App\Models\CotizacionRenta::class
            : null;

        $propuestaModel = class_exists(\App\Models\Propuesta::class)
            ? \App\Models\Propuesta::class
            : null;

        $ordenModel = class_exists(\App\Models\Orden::class)
            ? \App\Models\Orden::class
            : null;

        $ventaModel = class_exists(\App\Models\Venta::class)
            ? \App\Models\Venta::class
            : null;

        $clienteModel = class_exists(\App\Models\Cliente::class)
            ? \App\Models\Cliente::class
            : null;

        $cotizacionesRentasCount = $cotizacionRentaModel ? $cotizacionRentaModel::count() : 0;
        $cotizacionesVentasCount = $propuestaModel ? $propuestaModel::count() : 0;
        $ordenesServicioCount    = $ordenModel ? $ordenModel::count() : 0;
        $ventasRealizadasCount   = $ventaModel ? $ventaModel::count() : 0;
        $clientesCount           = $clienteModel ? $clienteModel::count() : 0;

        $ventasTotal = 0;
        $deudaTotal = 0;
        $deudoresCount = 0;

        if ($ventaModel) {
            $ventasTotal = $this->safeSum($ventaModel, [
                'total',
                'monto_total',
                'importe',
                'precio_total',
            ]);

            $deudaColumn = $this->firstExistingColumn($ventaModel, [
                'saldo_pendiente',
                'saldo',
                'deuda',
                'monto_pendiente',
                'restante',
            ]);

            if ($deudaColumn) {
                $deudoresCount = $ventaModel::where($deudaColumn, '>', 0)->count();
                $deudaTotal = $ventaModel::where($deudaColumn, '>', 0)->sum($deudaColumn);
            }
        }

        $ordenesPendientesCount = 0;
        $ordenesProcesoCount = 0;
        $ordenesTerminadasCount = 0;

        if ($ordenModel) {
            $statusColumn = $this->firstExistingColumn($ordenModel, [
                'estatus',
                'status',
                'estado',
            ]);

            if ($statusColumn) {
                $ordenesPendientesCount = $ordenModel::whereIn($statusColumn, [
                    'pendiente',
                    'Pendiente',
                    'PENDIENTE',
                ])->count();

                $ordenesProcesoCount = $ordenModel::whereIn($statusColumn, [
                    'proceso',
                    'en proceso',
                    'En proceso',
                    'EN PROCESO',
                    'procesando',
                ])->count();

                $ordenesTerminadasCount = $ordenModel::whereIn($statusColumn, [
                    'terminada',
                    'terminado',
                    'finalizada',
                    'finalizado',
                    'completada',
                    'completado',
                    'Terminada',
                    'Finalizada',
                    'Completada',
                ])->count();
            }
        }

        $actividadHoyCount = 0;

        if ($ventaModel) {
            $actividadHoyCount += $ventaModel::whereDate('created_at', today())->count();
        }

        if ($ordenModel) {
            $actividadHoyCount += $ordenModel::whereDate('created_at', today())->count();
        }

        if ($propuestaModel) {
            $actividadHoyCount += $propuestaModel::whereDate('created_at', today())->count();
        }

        if ($cotizacionRentaModel) {
            $actividadHoyCount += $cotizacionRentaModel::whereDate('created_at', today())->count();
        }

        $conversionRate = $this->calculateConversionRate(
            $cotizacionesVentasCount,
            $ventasRealizadasCount
        );

        return view('bento', compact(
            'cotizacionesRentasCount',
            'cotizacionesVentasCount',
            'ordenesServicioCount',
            'ventasRealizadasCount',
            'deudoresCount',
            'clientesCount',
            'ventasTotal',
            'deudaTotal',
            'ordenesPendientesCount',
            'ordenesProcesoCount',
            'ordenesTerminadasCount',
            'actividadHoyCount',
            'conversionRate'
        ));
    }

    private function calculateConversionRate(int $cotizaciones, int $ventas): int
    {
        if ($cotizaciones <= 0) {
            return 0;
        }

        return min(100, (int) round(($ventas / $cotizaciones) * 100));
    }

    private function safeSum(?string $model, array $columns): float
    {
        if (!$model) {
            return 0;
        }

        $column = $this->firstExistingColumn($model, $columns);

        if (!$column) {
            return 0;
        }

        return (float) $model::sum($column);
    }

    private function firstExistingColumn(string $model, array $columns): ?string
    {
        try {
            $instance = new $model;
            $table = $instance->getTable();

            foreach ($columns as $column) {
                if (Schema::hasColumn($table, $column)) {
                    return $column;
                }
            }
        } catch (\Throwable $e) {
            return null;
        }

        return null;
    }
}