<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Prestamo extends Model
{
    use HasFactory;

    protected $table = 'prestamos';

    // Estados permitidos (útil para scopes/validaciones)
    public const ESTADO_ACTIVO     = 'activo';
    public const ESTADO_DEVUELTO   = 'devuelto';
    public const ESTADO_RETRASADO  = 'retrasado';
    public const ESTADO_CANCELADO  = 'cancelado';
    public const ESTADO_VENDIDO    = 'vendido';

    protected $fillable = [
        'cliente_id',
        'estado',
        'fecha_prestamo',
        'fecha_devolucion_estimada',
        'fecha_devolucion_real',
        'condiciones_prestamo',
        'observaciones',
        'user_name',
        'firmaDigital',
    ];

    protected $casts = [
        'fecha_prestamo'            => 'date',
        'fecha_devolucion_estimada' => 'date',
        'fecha_devolucion_real'     => 'date',
        'created_at'                => 'datetime',
        'updated_at'                => 'datetime',
    ];

    /* =========================
     | Relaciones
     ==========================*/

    /** Registros (equipos) incluidos en el paquete */
    public function registros(): BelongsToMany
    {
        // Exponemos columnas de la pivote usadas en salida/devolución/vendido
        return $this->belongsToMany(Registro::class, 'prestamo_registro')
            ->withTimestamps()
            ->withPivot([
                'salida_scanned_at',
                'salida_scanned_by',
                'devolucion_scanned_at',
                'devolucion_scanned_by',
                'vendido_scanned_at',
                'vendido_scanned_by',
                'estado_item', // si la tienes en DB
            ]);
    }

    /** Cliente destinatario del paquete */
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    /* =========================
     | Scopes útiles
     ==========================*/

    /** Filtra por estado exacto */
    public function scopeEstado($query, string $estado)
    {
        return $query->where('estado', $estado);
    }

    /** Solo activos */
    public function scopeActivos($query)
    {
        return $query->where('estado', self::ESTADO_ACTIVO);
    }

    /** Solo retrasados */
    public function scopeRetrasados($query)
    {
        return $query->where('estado', self::ESTADO_RETRASADO);
    }

    /** Devueltos (cerrados) */
    public function scopeDevueltos($query)
    {
        return $query->where('estado', self::ESTADO_DEVUELTO);
    }

    /** Vendidos (cerrados por venta de al menos un ítem) */
    public function scopeVendidos($query)
    {
        return $query->where('estado', self::ESTADO_VENDIDO);
    }

    /* =========================
     | Helpers / Accessors
     ==========================*/

    /** Cantidad de equipos del paquete (usa relación cargada si existe) */
    public function getEquiposCountAttribute(): int
    {
        return $this->relationLoaded('registros')
            ? $this->registros->count()
            : $this->registros()->count();
    }

    public function getEsActivoAttribute(): bool
    {
        return $this->estado === self::ESTADO_ACTIVO;
    }

    public function getEsDevueltoAttribute(): bool
    {
        return $this->estado === self::ESTADO_DEVUELTO;
    }

    public function getEsVendidoAttribute(): bool
    {
        return $this->estado === self::ESTADO_VENDIDO;
    }

    public function getEsRetrasadoAttribute(): bool
    {
        return $this->estado === self::ESTADO_RETRASADO;
    }

    public function getEsCanceladoAttribute(): bool
    {
        return $this->estado === self::ESTADO_CANCELADO;
    }
}
