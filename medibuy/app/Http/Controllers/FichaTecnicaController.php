<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FichaTecnica;
use Illuminate\Support\Facades\Storage;

class FichaTecnicaController extends Controller
{
    public function index()
    {
        // Ordenar alfabéticamente por nombre
        $fichas = FichaTecnica::orderBy('nombre')->get();
        return view('fichas.index', compact('fichas'));
    }

    public function create()
    {
        return view('fichas.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'archivo' => 'required|mimes:pdf', // Solo archivos PDF
        ]);

        $archivoPath = $request->file('archivo')->store('fichas_tecnicas', 'public');

        FichaTecnica::create([
            'nombre' => $request->nombre,
            'archivo' => $archivoPath,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Ficha técnica subida correctamente.'
        ]);
    }

    public function download(FichaTecnica $ficha)
    {
        $rutaArchivo = storage_path("app/public/" . $ficha->archivo);
        $nombreOriginal = $ficha->nombre . '.pdf'; // Agrega la extensión si es PDF

        return response()->download($rutaArchivo, $nombreOriginal);
    }

    public function destroy(FichaTecnica $ficha)
    {
        Storage::delete("public/" . $ficha->archivo);
        $ficha->delete();

        return redirect()->route('fichas.index')->with('success', 'Ficha eliminada correctamente.');
    }
}
