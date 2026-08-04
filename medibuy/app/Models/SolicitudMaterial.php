<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SolicitudMaterial extends Model
{
    use HasFactory;

    protected $table = 'solicitudes_materiales'; // Especifica la tabla en la base de datos

    protected $fillable = [
        'user_id', 'categoria', 'material', 'cantidad',
        'justificacion', 'estado', 'fecha_entrega', 'entregado_por', 'motivo_rechazo' // Agregado motivo_rechazo
    ];

    /**
     * Relación inversa con el modelo User.
     * Un solicitud material pertenece a un usuario.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación con el modelo User para el usuario que entregó el material.
     */
    public function entregadoPor()
    {
        return $this->belongsTo(User::class, 'entregado_por');
    }
}
