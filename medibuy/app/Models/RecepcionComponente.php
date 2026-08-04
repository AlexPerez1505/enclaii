<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecepcionComponente extends Model
{
    protected $table = 'recepcion_componentes';

    protected $fillable = [
        'recepcion_id',
        'componente_id',
        'equipo_id',
        'nombre_equipo',
        'nombre_componente',
        'cantidad_recibida',
        'observaciones',
    ];

    public function recepcion()
    {
        return $this->belongsTo(Recepcion::class, 'recepcion_id');
    }

    // Opcional si tienes modelo Componente
    public function componente()
    {
        return $this->belongsTo(Componente::class, 'componente_id');
    }

    // Opcional si tienes modelo Equipo
    public function equipo()
    {
        return $this->belongsTo(Equipo::class, 'equipo_id');
    }
    public function matchPedidoComponente()
{
    return \App\Models\PedidoComponente::where('nombre', $this->nombre_componente)
        ->where('equipo_id', $this->equipo_id)
        ->first();
}


}
