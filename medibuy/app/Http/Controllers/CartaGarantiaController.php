<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CartaGarantia;
use Illuminate\Support\Facades\Storage;

class CartaGarantiaController extends Controller
{
    public function index()
    {
        // Ordenar por nombre alfabéticamente
        $cartas = CartaGarantia::orderBy('nombre')->get();
        return view('carta.index', compact('cartas'));
    }

    public function create()
    {
        return view('carta.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string',
            'archivo' => 'required|file|mimes:pdf'
        ]);

        // Guarda el archivo en storage/app/public/cartas
        $archivoPath = $request->file('archivo')->store('cartas', 'public');

        CartaGarantia::create([
            'nombre' => $request->nombre,
            'archivo' => $archivoPath
        ]);

        return redirect()->route('carta.index')->with('success', 'Carta subida exitosamente.');
    }

    public function descargar($id)
    {
        $carta = CartaGarantia::findOrFail($id);
        $filePath = storage_path('app/public/' . $carta->archivo);

        if (!file_exists($filePath)) {
            return back()->with('error', 'El archivo no existe.');
        }

        return response()->download($filePath, strtoupper($carta->nombre) . '.pdf');
    }

    public function destroy($id)
    {
        $carta = CartaGarantia::findOrFail($id);
        Storage::disk('public')->delete($carta->archivo);
        $carta->delete();

        return back()->with('success', 'Carta eliminada correctamente.');
    }
}
