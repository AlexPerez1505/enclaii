<?php

namespace App\Http\Controllers;

use App\Models\ItemParte;
use App\Models\Item;
use Illuminate\Http\Request;

class ItemParteController extends Controller
{
    // Listar todas las partes
    public function index()
    {
        $itemPartes = ItemParte::with('item')->get();
        return view('item_partes.index', compact('itemPartes'));
    }

    // Mostrar formulario de creación
    public function create()
    {
        $items = Item::all();
        return view('item_partes.create', compact('items'));
    }

    // Guardar nueva parte
    public function store(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:items,id',
            'nombre_parte' => 'required|string|max:255',
            'codigo_parte' => 'nullable|string|max:255',
            'descripcion' => 'nullable|string',
        ]);
        ItemParte::create($request->all());
        return redirect()->route('item-partes.index')->with('success', 'Parte creada correctamente.');
    }

    // Mostrar detalle de parte
    public function show($id)
    {
        $itemParte = ItemParte::with('item')->findOrFail($id);
        return view('item_partes.show', compact('itemParte'));
    }

    // Mostrar formulario de edición
    public function edit($id)
    {
        $itemParte = ItemParte::findOrFail($id);
        $items = Item::all();
        return view('item_partes.edit', compact('itemParte', 'items'));
    }

    // Actualizar parte
    public function update(Request $request, $id)
    {
        $itemParte = ItemParte::findOrFail($id);
        $request->validate([
            'item_id' => 'required|exists:items,id',
            'nombre_parte' => 'required|string|max:255',
            'codigo_parte' => 'nullable|string|max:255',
            'descripcion' => 'nullable|string',
        ]);
        $itemParte->update($request->all());
        return redirect()->route('item-partes.index')->with('success', 'Parte actualizada correctamente.');
    }

    // Eliminar parte
    public function destroy($id)
    {
        $itemParte = ItemParte::findOrFail($id);
        $itemParte->delete();
        return redirect()->route('item-partes.index')->with('success', 'Parte eliminada correctamente.');
    }
}
