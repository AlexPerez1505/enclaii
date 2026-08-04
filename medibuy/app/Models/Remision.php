<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Remision extends Model
{
    use HasFactory;

    protected $table = 'remisions';

    protected $fillable = [
        'cliente_id',
        'user_id',
        'fecha',
        'iva',
        'subtotal',
        'aplicar_iva',
        'total',
        'importe_letra',

        'tiene_envio',
        'envio_costo',
        'envio_direccion',
        'meses_a_pagar',
        'mensualidad',

        // NOTA: 'meta' eliminado porque tu DB no tiene esa columna
    ];

    protected $casts = [
        'aplicar_iva' => 'boolean',
        'tiene_envio' => 'boolean',
        // 'meta' eliminado
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function items()
    {
        return $this->hasMany(ItemRemision::class, 'remision_id');
    }
}
