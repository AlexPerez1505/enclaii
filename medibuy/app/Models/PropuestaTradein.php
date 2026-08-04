<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropuestaTradein extends Model
{
    protected $fillable = [
        'propuesta_id',
        'tipo_equipo',
        'marca',        // opcional
        'modelo',       // opcional
        'numero_serie', // opcional
        'valor_a_cuenta',
    ];

    public function propuesta()
    {
        return $this->belongsTo(Propuesta::class);
    }
}
