<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Maintenance extends Model
{
    protected $fillable = [
        'equipment_id',
        'equipment_name',
        'type',
        'date',
        'next_maintenance_date',
        'technician',
        'description',
        'cost',
        'equipment_status_after',
        'calibration_certificate_url',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'next_maintenance_date' => 'date',
            'cost' => 'decimal:2',
        ];
    }

    public function equipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class);
    }
}