<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    protected $fillable = ['fecha_programada', 'creado_por', 'observaciones'];

    public function equipos()
    {
        return $this->hasMany(PedidoEquipo::class);
    }

    public function componentes()
    {
        return $this->hasMany(PedidoComponente::class);
    }
    public function recepciones()
{
    return $this->hasMany(Recepcion::class);
}

}
