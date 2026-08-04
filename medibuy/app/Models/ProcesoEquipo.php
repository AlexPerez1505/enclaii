<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProcesoEquipo extends Model
{
    use HasFactory;

    protected $table = 'procesos_equipos';

    protected $fillable = [
        'registro_id',
        'tipo_proceso',
        'descripcion_proceso',
        'evidencia1',
        'evidencia2',
        'evidencia3',
        'video',
        'documento_pdf',
        'ficha_tecnica_id',
        'defectos',
        'checklist',         // <- nuevo: guardamos el checklist aquí (como array/JSON)
    ];

    protected $casts = [
        'defectos'  => 'array',   // se devolverá como array
        'checklist' => 'array',   // se devolverá como array
    ];

    public function registro()
    {
        return $this->belongsTo(Registro::class, 'registro_id', 'id');
    }

    public function fichaTecnica()
    {
        return $this->belongsTo(FichaTecnica::class, 'ficha_tecnica_id');
    }
}
