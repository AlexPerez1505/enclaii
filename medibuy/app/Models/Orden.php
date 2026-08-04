<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Orden extends Model
{
    protected $table = 'ordenes';

    protected $fillable = [
        'cliente_id',
        'fecha_entrada',
        'fecha_mantenimiento',
        'tipo_mantenimiento',

        // Datos del equipo
        'equipo',
        'marca',
        'modelo',
        'numero_serie',
        'observaciones',
        'foto_equipo',
        'foto_equipo_2',
        'foto_equipo_3',
        'proximo_mantenimiento',
        'proximo_mantenimiento_fecha',

        // JSON
        'mto_preventivo',
        'mto_realizado',
        'remision_partidas',

        // Remisión / factura
        'remision_cantidad',
        'remision_precio',
        'remision_subtotal',
        'remision_unidad',
        'remision_descripcion',
        'remision_envio',
        'remision_requiere_iva',
        'remision_iva',
        'remision_total',
        'remision_anticipo',
        'remision_total_pagar',

        // Autor / técnico / validación
        'user_id',
        'tecnico_id',
        'codigo_validacion_servicio',
    ];

    protected $casts = [
        'fecha_entrada'               => 'date',
        'fecha_mantenimiento'         => 'date',
        'proximo_mantenimiento_fecha' => 'date',

        'mto_preventivo'              => 'array',
        'mto_realizado'               => 'array',
        'remision_partidas'           => 'array',

        'proximo_mantenimiento'       => 'integer',

        'remision_cantidad'           => 'integer',
        'remision_precio'             => 'decimal:2',
        'remision_envio'              => 'decimal:2',
        'remision_subtotal'           => 'decimal:2',
        'remision_iva'                => 'decimal:2',
        'remision_total'              => 'decimal:2',
        'remision_anticipo'           => 'decimal:2',
        'remision_total_pagar'        => 'decimal:2',
        'remision_requiere_iva'       => 'boolean',
    ];

    public function cliente()
    {
        return $this->belongsTo(\App\Models\Cliente::class);
    }

    public function usuario()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    public function tecnico()
    {
        return $this->belongsTo(\App\Models\User::class, 'tecnico_id');
    }

    public function pagos()
    {
        return $this->hasMany(\App\Models\Pago::class, 'orden_id');
    }
}