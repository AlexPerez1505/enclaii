<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChecklistEntrega extends Model
{
    use HasFactory;

    protected $table = 'checklist_entrega';

    protected $fillable = [
        'checklist_id',
        'user_id',

        // esquema unificado
        'verificados',
        'no_verificados',
        'componentes',
        'observaciones',

        // payload de entrega (presencial / envío, etc.)
        'datos_entrega',

        // firmas
        'firma_cliente',
        'firma_entrega',

        // evidencias
        'evidencias',
    ];

    protected $casts = [
        'verificados'    => 'array',
        'no_verificados' => 'array',
        'componentes'    => 'array',
        'evidencias'     => 'array',
        'datos_entrega'  => 'array',
    ];

    /* -------- Relaciones -------- */

    public function checklist()
    {
        return $this->belongsTo(Checklist::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
