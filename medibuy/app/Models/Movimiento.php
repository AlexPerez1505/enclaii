<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movimiento extends Model
{
    use HasFactory;

    protected $fillable = [
        'servicio_id', 
        'tipo_movimiento', 
        'descripcion', 
        'checklist', 
        'evidencia1', 
        'evidencia2', 
        'evidencia3', 
        'video'
    ];

    // Relación con el modelo Servicio
    public function servicio()
    {
        return $this->belongsTo(Servicio::class);
    }
}
