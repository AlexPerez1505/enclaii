<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    use HasFactory;

    protected $table = 'pagos';

    protected $fillable = [
        'venta_id',
        'orden_id',
        'item_remision_id',
        'financiamiento_id',
        'monto',
        'fecha_pago',
        'metodo_pago',
        'detalle_metodos',
        'aprobado',
        'es_anticipo',
    ];

    protected $casts = [
        'fecha_pago'      => 'date',
        'monto'           => 'decimal:2',
        'detalle_metodos' => 'array',
        'aprobado'        => 'boolean',
        'es_anticipo'     => 'boolean',
    ];

    public function venta()
    {
        return $this->belongsTo(Venta::class);
    }

    public function orden()
    {
        return $this->belongsTo(\App\Models\Orden::class, 'orden_id');
    }

    public function item()
    {
        return $this->belongsTo(ItemRemision::class, 'item_remision_id');
    }

    public function detalleFinanciamiento()
    {
        return $this->belongsTo(PagoFinanciamiento::class, 'financiamiento_id');
    }

    public function documentos()
    {
        return $this->hasMany(DocumentoPago::class, 'pago_id');
    }
}