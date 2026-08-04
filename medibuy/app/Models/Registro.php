<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Registro extends Model
{
    use HasFactory;

    protected $table = 'registros';

    protected $fillable = [
        'tipo_equipo',
        'subtipo_equipo',
        'subtipo_equipo_otro',
        'numero_serie',
        'marca',
        'modelo',
        'anio',
        'descripcion',

        'estado_actual',

        // ✅ IMPORTANTE: para que se guarde y el inventario lo muestre bien
        'estado_proceso',
        'proceso_id',

        'fecha_adquisicion',
        'ultimo_mantenimiento',
        'observaciones',
        'evidencia1',
        'evidencia2',
        'evidencia3',
        'video',
        'documentoPDF',
        'firma_digital',
        'user_name',
    ];

    // ✅ Default interno (por si en algún flujo no mandas el campo)
    protected $attributes = [
        'estado_proceso' => 'registro',
    ];

    protected $casts = [
        'fecha_adquisicion'    => 'date',
        'ultimo_mantenimiento' => 'date',
        'proximo_mantenimiento'=> 'date',
    ];

    // Relación correcta con ProcesoEquipo
    public function procesos()
    {
        return $this->hasMany(ProcesoEquipo::class, 'registro_id', 'id');
    }

    public function fichaTecnica()
    {
        return $this->belongsTo(FichaTecnica::class, 'ficha_tecnica_id');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    public function prestamos()
    {
        return $this->belongsToMany(Prestamo::class, 'prestamo_registro')->withTimestamps();
    }

    public function componentesInv()
    {
        return $this->belongsToMany(\App\Models\InvComponente::class, 'inv_registro_componentes', 'registro_id', 'componente_id')
            ->withPivot(['nombre_cache','cantidad','incluido','notas'])
            ->withTimestamps();
    }

    public function componentes()
    {
        return $this->hasMany(\App\Models\InvRegistroComponente::class, 'registro_id');
    }
}
