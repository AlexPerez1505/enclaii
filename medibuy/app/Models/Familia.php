<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Familia extends Model
{
    use HasFactory;

    protected $fillable = ['nombre', 'slug', 'descripcion'];

    public function productos(): BelongsToMany
    {
        return $this->belongsToMany(
            Producto::class,
            'familia_producto',
            'familia_id',
            'producto_id'
        );
    }
}
