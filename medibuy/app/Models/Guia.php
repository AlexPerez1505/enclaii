<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Guia extends Model
{
    protected $fillable = [
        'numero_rastreo', 'peso', 'fecha_recepcion',
    ];

    // app/Models/Guia.php
public function entrega()
{
    return $this->hasOne(\App\Models\EntregaGuia::class, 'guia_id');
}

}
