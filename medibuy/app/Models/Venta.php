<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Cliente;
use App\Models\VentaProducto;
use App\Models\User;
use Carbon\Carbon;
use App\Notifications\ProximoPagoNotification;

use App\Models\VentaTradein;
use App\Models\PagoFinanciamiento;
use App\Models\CartaGarantia;
use App\Models\Remision;
use App\Models\Pago;
use App\Models\Checklist;

class Venta extends Model
{
    protected $fillable = [
        'cliente_id',
        'lugar',
        'nota',
        'user_id',
        'subtotal',
        'descuento',
        'envio',
        'iva',

        // ⚠️ OJO: en tu controller dijiste que "total" se guarda como total ORIGINAL.
        'total',

        'plan',
        'detalle_financiamiento',
        'carta_garantia_id',
        'meses_garantia',

        // ===== CAMPOS NUEVOS (trade-in / netos) =====
        'total_original',
        'tradein_total',
        'total_neto',
    ];

    protected $casts = [
        'detalle_financiamiento' => 'array',
    ];

    /* ============================
     * NOTIFICACIONES (igual)
     * ============================ */
    public function notificarSiPagoProximo()
    {
        if (!$this->detalle_financiamiento || !$this->cliente) {
            return;
        }

        // Remover "Pago inicial..." del detalle
        if (function_exists('removerPagoInicialDelDetalle')) {
            $detalle = removerPagoInicialDelDetalle($this->detalle_financiamiento);
        } else {
            $detalle = is_array($this->detalle_financiamiento)
                ? json_encode($this->detalle_financiamiento)
                : (string) $this->detalle_financiamiento;
        }

        preg_match_all(
            '/\b(\d{2}[\/\-]\d{2}[\/\-]\d{4}|\d{4}[\/\-]\d{2}[\/\-]\d{2}|\d{2}\s+de\s+\w+\s+de\s+\d{4})\b/i',
            $detalle,
            $matches
        );

        $fechas  = $matches[0] ?? [];
        $hoy     = Carbon::now();
        $limite  = $hoy->copy()->addDays(7);

        foreach ($fechas as $fechaTexto) {
            try {
                $fecha = Carbon::parse(str_ireplace(' de ', ' ', $fechaTexto));

                if ($fecha->between($hoy, $limite)) {
                    $this->cliente->notify(new ProximoPagoNotification($this, $fecha));
                    break;
                }
            } catch (\Throwable $e) {
                // Ignorar parse fall
            }
        }
    }

    /* ============================
     * RELACIONES
     * ============================ */
    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function productos()
    {
        return $this->hasMany(VentaProducto::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Pagos planeados / plan
    public function pagos()
    {
        return $this->hasMany(PagoFinanciamiento::class, 'venta_id');
    }

    public function pagosFinanciamiento()
    {
        return $this->hasMany(PagoFinanciamiento::class, 'venta_id');
    }

    public function pagosFinanciamientoConfirmados()
    {
        return $this->hasMany(PagoFinanciamiento::class, 'venta_id')->where('pagado', 1);
    }

    // Alias por compat
    public function pagoFinanciamiento()
    {
        return $this->hasMany(PagoFinanciamiento::class, 'venta_id');
    }

    public function cartaGarantia()
    {
        return $this->belongsTo(CartaGarantia::class, 'carta_garantia_id');
    }

    public function remision()
    {
        return $this->belongsTo(Remision::class);
    }

    public function checklist()
    {
        return $this->hasOne(Checklist::class, 'venta_id');
    }

    // Pagos reales (abonos/anticipos/trade-in)
    public function pagosReales()
    {
        return $this->hasMany(Pago::class, 'venta_id');
    }

    // Trade-ins
    public function tradeins()
    {
        return $this->hasMany(VentaTradein::class, 'venta_id');
    }

    /* ============================
     * ACCESORES
     * ============================ */

    /**
     * Total neto real:
     * - si viene total_neto en BD, lo usa
     * - si no, lo calcula con total_original - tradein_total
     */
    public function getTotalNetoAttribute($value)
    {
        if ($value !== null) return (float) $value;

        $orig = (float) ($this->total_original ?? $this->total ?? 0);
        $ti   = (float) ($this->tradein_total ?? 0);

        return max($orig - $ti, 0);
    }

    /**
     * Total pagado aprobado (pagos reales aprobados)
     */
    public function getTotalPagadoAprobadoAttribute()
    {
        return (float) $this->pagosReales()
            ->where('aprobado', true)
            ->sum('monto');
    }

    /**
     * Saldo pendiente (total_original - pagos aprobados)
     * OJO: en tu indexPagos estás usando total_original como base de catálogo.
     */
    public function getSaldoAttribute()
    {
        $base   = (float) ($this->total_original ?? $this->total ?? 0);
        $pagado = (float) $this->total_pagado_aprobado;

        return max($base - $pagado, 0);
    }
}