<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Equipo extends Model
{
    protected $fillable = ['nombre', 'categoria', 'descripcion'];

    public function componentes()
    {
        return $this->belongsToMany(Componente::class, 'equipo_componentes')
                    ->withPivot('cantidad_esperada')
                    ->withTimestamps();
    }

    public function recepciones()
    {
        return $this->hasMany(Recepcion::class);
    }
}
