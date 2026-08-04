<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChecklistIngenieria extends Model
{
    use HasFactory;

    // Mantengo tu nombre de tabla en singular
    protected $table = 'checklist_ingenieria';

    protected $fillable = [
        'checklist_id',
        'user_id',

        // esquema unificado
        'verificados',        // array de series
        'no_verificados',     // array de series
        'componentes',        // { serie: { "Comp": bool } }
        'observaciones',      // texto
        'incidente',          // compat: algunas vistas lo usan como nombre del campo

        // firmas
        'firma_responsable',
        'firma_supervisor',

        // evidencias
        'evidencias',         // array de rutas
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
