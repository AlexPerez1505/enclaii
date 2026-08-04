<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Paquete extends Model
{
    use HasFactory;

    // Solo nombre, porque ya quitamos descripción e imagen de la BD
    protected $fillable = ['nombre'];

    public function productos(): BelongsToMany
    {
        return $this->belongsToMany(
                Producto::class,
                'paquete_producto',
                'paquete_id',
                'producto_id'
            )
            ->using(PaqueteProducto::class)       // usa el pivot custom
            ->withPivot('orden')                 // 👈 solo ORDEN
            ->orderBy('paquete_producto.orden'); // se muestran ordenados
    }
}
