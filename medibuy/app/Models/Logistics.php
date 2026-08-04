<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Logistics extends Model
{
    protected $fillable = [
        'rental_id',
        'rental_client',
        'delivery_date',
        'pickup_date',
        'driver',
        'status',
        'delivery_address',
        'delivery_photo_url',
        'pickup_photo_url',
        'signature_url',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'delivery_date' => 'datetime',
            'pickup_date' => 'datetime',
        ];
    }

    public function rental(): BelongsTo
    {
        return $this->belongsTo(Rental::class);
    }
}