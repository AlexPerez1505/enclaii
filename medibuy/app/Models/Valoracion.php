<?php

// app/Models/Valoracion.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Valoracion extends Model
{
    // Especificar el nombre correcto de la tabla
    protected $table = 'valoraciones';

    // Especificar los campos que se pueden rellenar
    protected $fillable = [
        'publicacion_id', 'valor', 'user_id',
    ];

    // Relación con la tabla Publicaciones
    public function publicacion()
    {
        return $this->belongsTo(Publicacion::class);
    }

    // Relación con la tabla Users
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
