<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Producto extends Model
{
    use HasFactory;

    protected $fillable = [
        'tipo_equipo',
        'subtipo_equipo',
        'modelo',
        'marca',
        'stock',
        'precio',
        'imagen',
    ];

    public function cotizaciones(): BelongsToMany
    {
        return $this->belongsToMany(Cotizacion::class, 'cotizacion_producto')
                    ->withPivot('cantidad', 'precio', 'subtotal')
                    ->withTimestamps();
    }

    public function paquetes(): BelongsToMany
    {
        return $this->belongsToMany(
                Paquete::class,
                'paquete_producto',
                'producto_id',
                'paquete_id'
            )
            ->using(PaqueteProducto::class)
            ->withPivot('orden');
    }
}
