<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CashTransaction extends Model
{
    use HasFactory;

    protected $table = 'cash_transactions';

    // Tipos permitidos
    public const TYPE_ALLOCATION    = 'allocation';    // entrada de jefas
    public const TYPE_DISBURSEMENT  = 'disbursement';  // entrega a usuario
    public const TYPE_RETURN        = 'return';        // devolución / cambio

    /**
     * Atributos asignables en masa.
     * Nota: incluimos created_at/updated_at porque en algunos flujos
     * se fija manualmente la fecha/hora (performed_at).
     */
    protected $fillable = [
        'type',
        'manager_id',
        'counterparty_id',
        'amount',
        'purpose',
        'evidence_paths',
        'manager_signature_path',
        'counterparty_signature_path',
        'nip_approved_by',
        'nip_approved_at',
        'pdf_receipt_path',
        'qr_token',
        'qr_expires_at',
        'acknowledged_at',
        'created_by',
        'created_at',
        'updated_at',
    ];

    /**
     * Casts para tipos fuertes.
     * - evidence_paths se guarda/lee como JSON (array).
     * - amount con 2 decimales.
     */
    protected $casts = [
        'amount'            => 'decimal:2',
        'evidence_paths'    => 'array',
        'nip_approved_at'   => 'datetime',
        'qr_expires_at'     => 'datetime',
        'acknowledged_at'   => 'datetime',
        'created_at'        => 'datetime',
        'updated_at'        => 'datetime',
    ];

    /* ---------------- Relaciones ---------------- */

    // Encargado (admin que recibe/entrega)
    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    // Contraparte (jefa en allocation, usuario en disbursement/return)
    public function counterparty()
    {
        return $this->belongsTo(User::class, 'counterparty_id');
    }

    // Quien autorizó con NIP
    public function approver()
    {
        return $this->belongsTo(User::class, 'nip_approved_by');
    }

    // Quien creó el registro en el sistema
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /* ---------------- Scopes útiles ---------------- */

    // Por encargado
    public function scopeForManager($query, int $managerId)
    {
        return $query->where('manager_id', $managerId);
    }

    // Entre fechas (solo fecha, sin horas)
    public function scopeBetween($query, ?string $from, ?string $to)
    {
        if ($from) $query->whereDate('created_at', '>=', $from);
        if ($to)   $query->whereDate('created_at', '<=', $to);
        return $query;
    }

    /* ---------------- Accessors/Helpers ---------------- */

    // URL pública del PDF (si existe)
    public function getPdfUrlAttribute(): ?string
    {
        return $this->pdf_receipt_path ? asset('storage/'.$this->pdf_receipt_path) : null;
    }

    // ¿Sigue pendiente de aceptación por QR?
    public function getIsPendingAckAttribute(): bool
    {
        return (bool) ($this->qr_token && !$this->acknowledged_at
            && (is_null($this->qr_expires_at) || now()->lt($this->qr_expires_at)));
    }

    // Etiqueta legible del tipo
    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            self::TYPE_ALLOCATION   => 'Entrada',
            self::TYPE_DISBURSEMENT => 'Entrega',
            self::TYPE_RETURN       => 'Devolución',
            default                 => ucfirst((string)$this->type),
        };
    }
}
