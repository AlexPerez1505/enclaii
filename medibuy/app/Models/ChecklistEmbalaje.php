<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChecklistEmbalaje extends Model
{
    use HasFactory;

    protected $table = 'checklist_embalaje';

    protected $fillable = [
        'checklist_id',
        'user_id',

        // esquema unificado
        'verificados',
        'no_verificados',
        'componentes',
        'observaciones',

        // firmas
        'firma_responsable',
        'firma_supervisor',

        // evidencias
        'evidencias',
    ];

    protected $casts = [
        'verificados'    => 'array',
        'no_verificados' => 'array',
        'componentes'    => 'array',
        'evidencias'     => 'array',
    ];

    public function checklist()
    {
        return $this->belongsTo(Checklist::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
