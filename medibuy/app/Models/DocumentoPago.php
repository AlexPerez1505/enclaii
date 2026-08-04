<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentoPago extends Model
{
    use HasFactory;

    protected $table = 'documentos_pago';

    protected $fillable = [
        'pago_id',
        'nombre_original',
        'ruta_archivo',
    ];

    public function pago()
    {
        return $this->belongsTo(Pago::class, 'pago_id');
    }
}