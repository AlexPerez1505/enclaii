<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EnvioGasto extends Model
{
    protected $table = 'envio_gastos';

    protected $fillable = [
        'referencia','sucursal','destino','transportista',
        'alto_cm','largo_cm','ancho_cm','peso_kg',
        'peso_volumetrico_kg','peso_facturable_kg',
        'costo_mxn','fecha_envio','notas'
    ];

    protected $casts = [
        'fecha_envio' => 'date',
        'alto_cm' => 'decimal:2',
        'largo_cm' => 'decimal:2',
        'ancho_cm' => 'decimal:2',
        'peso_kg' => 'decimal:2',
        'peso_volumetrico_kg' => 'decimal:2',
        'peso_facturable_kg' => 'decimal:2',
        'costo_mxn' => 'decimal:2',
    ];
}
