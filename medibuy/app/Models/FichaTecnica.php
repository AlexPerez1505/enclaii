<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FichaTecnica extends Model
{
    use HasFactory;

    protected $table = 'fichas_tecnicas'; // Asegurar que usa la tabla correcta

    protected $fillable = ['nombre', 'archivo'];
    // En el modelo FichaTecnica
public function procesos()
{
    return $this->hasMany(ProcesoEquipo::class); // Si quieres obtener todos los procesos relacionados con una ficha
}
    // Relación inversa con cotizaciones
    public function cotizaciones()
    {
        return $this->hasMany(Cotizacion::class);
    }
}
