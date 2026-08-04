<?php


namespace App\Http\Controllers;

use App\Models\Valoracion;
use App\Models\Publicacion;
use Illuminate\Http\Request;

class ValoracionController extends Controller
{
    // Función para guardar la valoración
    public function guardarValoracion(Request $request)
    {
        // Validar que la valoración esté en el rango de 1 a 5
        $request->validate([
            'valor' => 'required|integer|min:1|max:5',
            'publicacion_id' => 'required|exists:publicaciones,id'
        ]);

        // Buscar la publicación
        $publicacion = Publicacion::findOrFail($request->publicacion_id);

        // Verificar si el usuario ya ha valorado esta publicación
        $valoracion = Valoracion::where('publicacion_id', $publicacion->id)
                                ->where('user_id', auth()->id()) // Suponiendo que tienes autenticación
                                ->first();
        
        if ($valoracion) {
            // Si ya existe una valoración, actualizarla
            $valoracion->valor = $request->valor;
            $valoracion->save();
        } else {
            // Si no existe, crear una nueva valoración
            Valoracion::create([
                'publicacion_id' => $publicacion->id,
                'valor' => $request->valor,
                'user_id' => auth()->id(), // Si estás usando autenticación
            ]);
        }

        // Calcular el nuevo promedio de valoraciones
        $promedio = $publicacion->valoraciones()->avg('valor');

        return response()->json([
            'success' => true,
            'promedio' => $promedio,
        ]);
    }
}
