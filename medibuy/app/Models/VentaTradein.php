<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Venta;

class VentaTradein extends Model
{
    protected $table = 'venta_tradeins';

    protected $fillable = [
        'venta_id',
        'tipo_equipo',
        'descripcion',
        'marca',
        'modelo',
        'numero_serie',
        'valor_a_cuenta',
        'observaciones',
    ];

    protected $casts = [
        'valor_a_cuenta' => 'float',
    ];

    public function venta()
    {
        return $this->belongsTo(Venta::class, 'venta_id');
    }
}