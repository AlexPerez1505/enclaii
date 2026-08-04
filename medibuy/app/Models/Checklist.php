<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Checklist extends Model
{
    use HasFactory;

    protected $table = 'checklists';

    protected $fillable = [
        'venta_id',
         // ... otros campos que ya tengas
    'qr_url',
    'qr_path',
    'label_path',
    ];

    public function venta()
    {
        return $this->belongsTo(Venta::class);
    }

    public function ingenieria()
    {
        return $this->hasOne(ChecklistIngenieria::class, 'checklist_id');
    }

    public function embalaje()
    {
        return $this->hasOne(ChecklistEmbalaje::class, 'checklist_id');
    }

    public function entrega()
    {
        return $this->hasOne(ChecklistEntrega::class, 'checklist_id');
    }

    public function recepcion()
    {
        return $this->hasOne(ChecklistRecepcion::class, 'checklist_id');
    }
}
