<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use App\Models\Seguimiento;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cliente extends Model
{
    use HasFactory, Notifiable;

    protected $table = 'clientes';

    protected $fillable = [
        'nombre',
        'apellido',
        'telefono',
        'email',
        'comentarios',
        'asesor',
        'categoria_id',
        'recibe_promocion',
        'congreso_conocido',
        'origen',

        // campos nuevos para rentas médicas
        'nombre_comercial',
        'direccion',
        'activo',
        'tipo_cliente',
    ];

    protected $casts = [
        'recibe_promocion' => 'boolean',
        'activo' => 'boolean',
    ];

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function notas()
    {
        return $this->hasMany(Nota::class);
    }

    public function seguimientos()
    {
        return $this->hasMany(Seguimiento::class);
    }

    public function rentals(): HasMany
    {
        return $this->hasMany(Rental::class, 'cliente_id');
    }

    public function getNombreCompletoAttribute(): string
    {
        return trim(($this->nombre ?? '') . ' ' . ($this->apellido ?? ''));
    }

    public function getNombreRentaAttribute(): string
    {
        if (!empty($this->nombre_comercial)) {
            return $this->nombre_comercial;
        }

        return $this->nombre_completo;
    }
}