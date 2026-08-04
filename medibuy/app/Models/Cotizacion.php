<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon; // Importar Carbon para trabajar con fechas

class Cotizacion extends Model
{
    use HasFactory;

    protected $table = 'cotizaciones'; // Asegura que use la tabla correcta

    protected $fillable = [
        'cliente',
        'productos',
        'subtotal',
        'descuento',
        'iva',
        'envio',  // Nuevo campo agregado
        'total',
        'tipo_pago',
        'plan_pagos',
        'nota',
        'valido_hasta',
        'lugar_cotizacion',
        'registrado_por', // Campo agregado
        'ficha_tecnica_id',
    ];

    protected $casts = [
        'productos' => 'array',
        'plan_pagos' => 'array',
    ];

    // Asegurarnos de que la fecha 'valido_hasta' se establezca al crear el registro
    protected static function booted()
    {
        static::creating(function ($cotizacion) {
            // Si 'valido_hasta' no tiene un valor, se le asigna 10 días a partir de la fecha actual
            if (!$cotizacion->valido_hasta) {
                $cotizacion->valido_hasta = Carbon::now()->addDays(11); // Fecha 10 días después
            }
        });
    }
    // App\Models\Cotizacion.php

// App\Models\Cotizacion.php

    // Relación con la ficha técnica
    public function fichaTecnica()
    {
        return $this->belongsTo(FichaTecnica::class);
    }


}
