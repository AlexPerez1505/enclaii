<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class EquipoComponente extends Pivot
{
    protected $table = 'equipo_componentes';
    protected $fillable = ['equipo_id', 'componente_id', 'cantidad_esperada'];
}
