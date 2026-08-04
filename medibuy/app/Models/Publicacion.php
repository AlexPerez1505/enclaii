<?php

// app/Models/Publicacion.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Publicacion extends Model
{
    // 👉 Esto le dice a Laravel que use 'publicaciones' en lugar de 'publicacions'
    protected $table = 'publicaciones';

    protected $fillable = [
        'titulo',
        'descripcion',
        'archivo',
        'tipo',
    ];
    public function valoraciones()
    {
        return $this->hasMany(Valoracion::class, 'publicacion_id');
    }
    

}
