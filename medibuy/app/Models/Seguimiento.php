<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Seguimiento extends Model
{
    protected $fillable = [
        'cliente_id',
        'titulo',
        'fecha_seguimiento',
        'descripcion',
        'completado'
    ];
protected $casts = [
    'fecha_seguimiento' => 'date',
];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }
}
