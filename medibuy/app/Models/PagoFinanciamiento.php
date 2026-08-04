<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PagoFinanciamiento extends Model
{
    use HasFactory;

    protected $table = 'pagos_financiamiento';

    protected $fillable = [
        'venta_id',
        'descripcion',
        'fecha_pago',
        'monto',
        'monto_pendiente', // ✅ nuevo: para ir descontando anticipos/abonos
        'pagado',
        'metodo_pago',
        'notificado',
    ];

    protected $casts = [
        'fecha_pago'       => 'date',
        'monto'            => 'decimal:2',
        'monto_pendiente'  => 'decimal:2',
        'pagado'           => 'boolean',
        'notificado'       => 'boolean',
    ];

    // =========================
    // Relaciones
    // =========================

    public function venta()
    {
        return $this->belongsTo(Venta::class, 'venta_id');
    }

    /**
     * Todos los pagos reales ligados a esta cuota
     */
    public function pagos()
    {
        return $this->hasMany(Pago::class, 'financiamiento_id');
    }

    /**
     * Primer pago real (si quieres 1:1 para UI)
     */
    public function pago()
    {
        return $this->hasOne(Pago::class, 'financiamiento_id');
    }

    /**
     * Documentos asociados a los pagos reales de esta cuota.
     * Esto permite: PagoFinanciamiento::with(['pago','documentos'])
     */
    public function documentos()
    {
        return $this->hasManyThrough(
            DocumentoPago::class, // modelo final
            Pago::class,          // modelo intermedio
            'financiamiento_id',  // FK en pagos que apunta a pagos_financiamiento.id
            'pago_id',            // FK en documento_pagos que apunta a pagos.id
            'id',                 // PK local de pagos_financiamiento
            'id'                  // PK local de pagos
        );
    }
}
