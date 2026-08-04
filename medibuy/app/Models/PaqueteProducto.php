<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaqueteProducto extends Pivot
{
    protected $table = 'paquete_producto';

    public $timestamps = false;

    protected $fillable = [
        'paquete_id',
        'producto_id',
        'orden',      // 👈 solo orden
    ];

    protected $casts = [
        'paquete_id'  => 'int',
        'producto_id' => 'int',
        'orden'       => 'int',
    ];

    public function paquete(): BelongsTo
    {
        return $this->belongsTo(Paquete::class);
    }

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }
}
