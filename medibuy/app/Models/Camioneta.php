<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Camioneta extends Model
{
    use HasFactory;

    // Especifica la tabla si el nombre no sigue la convención plural en Laravel
    protected $table = 'camionetas';

    // Campos que se pueden asignar masivamente
    protected $fillable = [
        'placa',
        'vin',
        'marca',
        'modelo',
        'anio',
        'color',
        'tipo_motor',
        'capacidad_carga',
        'tipo_combustible',
        'fecha_adquisicion',
        'ultimo_mantenimiento',
        'proximo_mantenimiento',
        'ultima_verificacion',
        'proxima_verificacion',
        'kilometraje',
        'rendimiento_litro',
        'costo_llenado',
        'fotos',  // Asumimos que se guardan los nombres de los archivos de las fotos
        'tarjeta_circulacion',
        'verificacion',
        'tenencia',
        'seguro',
    ];

    // Si se usa un tipo diferente para la fecha (ejemplo, de tipo timestamp)
    protected $dates = [
        'fecha_adquisicion',
        'ultimo_mantenimiento',
        'proximo_mantenimiento',
        'ultima_verificacion',
        'proxima_verificacion',
    ];

    // Si se manejan imágenes o archivos y deseas almacenamiento en disco
    public function saveFotos($fotos)
    {
        $path = $fotos->store('public/fotos'); // Guarda las fotos en el directorio "storage/app/public/fotos"
        return $path;
    }
}
