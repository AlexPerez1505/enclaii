<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PedidoComponente extends Model
{
    protected $fillable = ['pedido_id', 'nombre', 'equipo_id', 'cantidad_esperada']; // Agrega 'nombre', elimina 'componente_id' si no usas

    // Si no tienes modelo Componente, puedes quitar o ajustar esta relación
    // public function componente()
    // {
    //     return $this->belongsTo(Componente::class);
    // }
    public function pedido()
{
    return $this->belongsTo(Pedido::class);
}

// Relación con los registros en la tabla intermedia de recepciones
public function recepcionesComponentes()
{
    return $this->hasMany(\App\Models\RecepcionComponente::class, 'pedido_componente_id');
}

}
