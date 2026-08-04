<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ModuloUso extends Model
{
    protected $fillable = ['user_id', 'nombre', 'ruta', 'icono', 'usos'];

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }
}
