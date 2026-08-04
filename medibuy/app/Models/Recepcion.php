<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recepcion extends Model
{
    protected $table = 'recepciones';

    protected $fillable = [
        'equipo_id',
        'pedido_id',
        'fecha',
        'recibido_por',
        'observaciones',
    ];

    public function equipo()
    {
        return $this->belongsTo(Equipo::class);
    }

    public function componentes()
    {
        return $this->hasMany(RecepcionComponente::class, 'recepcion_id');
    }
    public function pedido()
{
    return $this->belongsTo(Pedido::class);
}

}
