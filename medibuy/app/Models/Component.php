<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Component extends Model
{
    protected $fillable = [
        'name',
        'category',
        'brand',
        'model',
        'serial_number',
        'status',
        'unit_cost',
        'photo_url',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'unit_cost' => 'decimal:2',
        ];
    }

public function equipments(): BelongsToMany
{
    return $this->belongsToMany(Equipment::class, 'equipment_component')
        ->using(EquipmentComponent::class)
        ->withPivot(['quantity', 'condition', 'notes'])
        ->withTimestamps();
}
}