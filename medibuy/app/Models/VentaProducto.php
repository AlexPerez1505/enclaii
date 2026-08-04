<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Producto;
use App\Models\Venta;
use App\Models\Registro;

class VentaProducto extends Model
{
    protected $fillable = [
        'venta_id',
        'producto_id',
        'cantidad',
        'precio_unitario',
        'subtotal',
        'sobreprecio',
        'registro_id',

        // ✅ NUEVO (para regalos por renglón)
        'is_regalo',
    ];

    protected $casts = [
        'is_regalo' => 'boolean',
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    public function venta()
    {
        return $this->belongsTo(Venta::class);
    }

    public function registro()
    {
        return $this->belongsTo(Registro::class, 'registro_id');
    }
}