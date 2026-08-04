<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PedidoEquipo extends Model
{
    protected $fillable = ['pedido_id', 'nombre', 'cantidad']; // Agrega 'nombre' y quita 'equipo_id'

    // Si no tienes tabla o modelo 'Equipo' relacionado, quita esta función o déjala si la tienes y usas
    // public function equipo()
    // {
    //     return $this->belongsTo(Equipo::class);
    // }
}
