<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemRemision extends Model
{
    use HasFactory;

    protected $fillable = [
        'remision_id',
        'unidad',
        'cantidad',
        'nombre_item',
        'descripcion_item',
        'importe_unitario',
        'a_cuenta',
        'subtotal',
        'restante',
    ];

    public function remision()
    {
        return $this->belongsTo(Remision::class);
    }
}
