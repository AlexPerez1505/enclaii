<?php
use App\Models\ModuloUso;
use Illuminate\Support\Facades\Auth;

function registrarModuloUso($nombre, $ruta, $icono) {
    if (!Auth::check()) return;

    $registro = ModuloUso::firstOrCreate(
        [ 'user_id' => Auth::id(), 'ruta' => $ruta ],
        [ 'nombre' => $nombre, 'icono' => $icono ]
    );

    $registro->increment('usos');
}
