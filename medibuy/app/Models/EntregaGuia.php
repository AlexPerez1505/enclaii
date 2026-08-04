<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EntregaGuia extends Model
{
    protected $fillable = [
        'guia_id','entregado_por','fecha_entrega','contenido','numero_serie',
        'observaciones','destinatario','firmaDigital','imagen'
    ];

    protected $casts = [
        'fecha_entrega' => 'datetime',
    ];

    public function guia() {
        return $this->belongsTo(Guia::class, 'guia_id');
    }
}
