<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Servicio extends Model
{
    protected $table = 'servicio';

    protected $fillable = [
        'estado_id',
        'tipo_equipo',
        'subtipo_equipo',
        'numero_serie',
        'marca',
        'modelo',
        'año',
        'descripcion',
        'fecha_adquisicion',
        'evidencia1',
        'evidencia2',
        'evidencia3',
        'video',
        'firma_digital',
        'observaciones',
        'user_name',
        'nombre_doctor',
        'estado_proceso',
        'mantenimiento_tipo',
        'orden_id',
        'orden_validada_at',
    ];

    protected $casts = [
        'orden_validada_at' => 'datetime',
        'fecha_adquisicion' => 'date',
    ];

    public function movimientos()
    {
        return $this->hasMany(Movimiento::class, 'servicio_id');
    }

    public function orden()
    {
        return $this->belongsTo(Orden::class, 'orden_id');
    }
}