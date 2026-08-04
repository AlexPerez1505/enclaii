<?php
// app/Models/InvRegistroComponente.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvRegistroComponente extends Model
{
    protected $table = 'inv_registro_componentes';

    protected $fillable = [
        'registro_id','componente_id','nombre_cache',
        'cantidad','incluido','notas',
    ];

    public function registro(){
        return $this->belongsTo(Registro::class);
    }
}
