<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Equipment extends Model
{
    protected $table = 'equipments';

    protected $fillable = [
        'name',
        'category',
        'brand',
        'model',
        'serial_number',
        'is_package',
        'year_of_manufacture',
        'status',
        'current_location',
        'equipment_cost',
        'rental_price_day',
        'rental_price_event',
        'useful_life_years',
        'photo_url',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'is_package' => 'boolean',
            'equipment_cost' => 'decimal:2',
            'rental_price_day' => 'decimal:2',
            'rental_price_event' => 'decimal:2',
        ];
    }

    public function components(): BelongsToMany
    {
        return $this->belongsToMany(Component::class, 'equipment_components')
            ->using(EquipmentComponent::class)
            ->withPivot(['quantity', 'condition', 'notes'])
            ->withTimestamps();
    }

    public function rentalItems(): HasMany
    {
        return $this->hasMany(RentalItem::class);
    }

    public function maintenances(): HasMany
    {
        return $this->hasMany(Maintenance::class);
    }

    public function complianceLogs(): HasMany
    {
        return $this->hasMany(ComplianceLog::class);
    }
}