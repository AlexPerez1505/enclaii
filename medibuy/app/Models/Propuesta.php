<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Propuesta extends Model
{
    protected $fillable = [
        'cliente_id',
        'lugar',
        'nota',
        'user_id',
        'subtotal',
        'descuento',
        'envio',
        'iva',
        'total',
        'plan',
        'ficha_tecnica_id',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function productos()
    {
        return $this->hasMany(PropuestaProducto::class);
    }

    public function fichaTecnica()
    {
        return $this->belongsTo(FichaTecnica::class);
    }

    public function pagos()
    {
        return $this->hasMany(PagoFinanciamientoPropuesta::class);
    }

    public function pagosFinanciamiento()
    {
        return $this->hasMany(PagoFinanciamientoPropuesta::class, 'propuesta_id');
    }

    public function whatsappLogs()
    {
        return $this->hasMany(WhatsAppLog::class)->latest();
    }

    public function tradeins()
    {
        return $this->hasMany(PropuestaTradein::class, 'propuesta_id');
    }
}