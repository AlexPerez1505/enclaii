<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropuestaProducto extends Model
{
    protected $fillable = [
        'propuesta_id',
        'producto_id',
        'cantidad',
        'precio_unitario',
        'subtotal',
        'sobreprecio',
        'es_regalo',
    ];

    protected $casts = [
        'es_regalo' => 'boolean',
    ];

    public function propuesta()
    {
        return $this->belongsTo(Propuesta::class);
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }
}