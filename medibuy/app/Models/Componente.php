<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Componente extends Model
{
    protected $fillable = ['nombre', 'descripcion'];

    public function equipos()
    {
        return $this->belongsToMany(Equipo::class, 'equipo_componentes')
                    ->withPivot('cantidad_esperada')
                    ->withTimestamps();
    }
    public function pedido()
{
    return $this->belongsTo(Pedido::class);
}

}
