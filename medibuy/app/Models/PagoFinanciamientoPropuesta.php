<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PagoFinanciamientoPropuesta extends Model
{
    protected $fillable = [
        'propuesta_id',
        'descripcion',
        'fecha_pago',
        'monto'
    ];

    public function propuesta()
    {
        return $this->belongsTo(Propuesta::class);
    }
}
