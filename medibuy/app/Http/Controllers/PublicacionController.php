<?php

namespace App\Http\Controllers;

use App\Models\Publicacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PublicacionController extends Controller
{
    /**
     * Muestra todas las publicaciones
     */
    public function index()
    {
        $publicaciones = Publicacion::latest()->get();
        return view('publicaciones.index', compact('publicaciones'));
    }

    /**
     * Muestra el formulario para crear una publicación
     */
    public function create()
    {
        return view('publicaciones.create');
    }

    /**
     * Guarda la publicación
     */
    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'archivo' => 'required|file|max:20480',
        ]);
    
        $archivo = $request->file('archivo');
        $path = $archivo->store('public/publicaciones');
    
        $tipo = 'documento';
        if (str_starts_with($archivo->getMimeType(), 'image/')) {
            $tipo = 'imagen';
        } elseif (str_starts_with($archivo->getMimeType(), 'video/')) {
            $tipo = 'video';
        }
    
        Publicacion::create([
            'titulo' => $request->titulo,
            'descripcion' => $request->descripcion,
            'archivo' => $path,
            'tipo' => $tipo,
        ]);
    
        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Archivo subido exitosamente.']);
        }
    
        return redirect()->route('publicaciones.index')->with('success', 'Archivo subido exitosamente.');
    }
    
    public function fetchPublicaciones()
    {
        $publicaciones = Publicacion::latest()->get();
        return view('partials.publicaciones-list', compact('publicaciones'));
    }
    public function ultimaActualizacion()
{
    $ultima = Publicacion::latest('updated_at')->first()?->updated_at?->toISOString();
    return response()->json(['ultima_actualizacion' => $ultima]);
}
public function show($id)
{
    $publicacion = Publicacion::with('valoraciones')->findOrFail($id);
    $promedio = $publicacion->valoraciones()->avg('valor');
return view('publicaciones.show', compact('publicacion', 'promedio'));
}

public function subirArchivo(Request $request, $id)
{
    $publicacion = Publicacion::findOrFail($id);

    $request->validate([
        'archivo' => 'required|file|mimes:jpg,jpeg,png,webp,mp4,mov|max:51200',
    ]);

    // Elimina el archivo anterior si existe
    if ($publicacion->archivo && Storage::exists($publicacion->archivo)) {
        Storage::delete($publicacion->archivo);
    }

    $path = $request->file('archivo')->store('publicaciones', 'public');
    $publicacion->update(['archivo' => $path]);

    return redirect()->back()->with('success', 'Archivo actualizado correctamente.');
}

public function destroy($id)
{
    $publicacion = Publicacion::findOrFail($id);

    if ($publicacion->archivo && Storage::exists($publicacion->archivo)) {
        Storage::delete($publicacion->archivo);
    }

    $publicacion->delete();

    return response()->json(['success' => true]);
}
    

    
}
