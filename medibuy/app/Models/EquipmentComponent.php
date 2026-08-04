<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class EquipmentComponent extends Pivot
{
    protected $table = 'equipment_components';

    protected $fillable = [
        'equipment_id',
        'component_id',
        'quantity',
        'condition',
        'notes',
    ];
}