<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class CartaGarantia extends Model
{
    protected $fillable = ['nombre', 'archivo'];

    public function getArchivoUrlAttribute()
    {
        return Storage::url($this->archivo);
    }
}
