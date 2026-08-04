<?php

namespace App\Http\Controllers;

use App\Models\Component;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ComponentController extends Controller
{
    public function index()
    {
        $components = Component::latest()->paginate(15);
        return view('components.index', compact('components'));
    }

    public function create()
    {
        return view('components.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->rules());
        Component::create($validated);

        return redirect()->route('components.index')
            ->with('success', 'Componente creado correctamente.');
    }

    public function show(Component $component)
    {
        $component->load('equipments');
        return view('components.show', compact('component'));
    }

    public function edit(Component $component)
    {
        return view('components.edit', compact('component'));
    }

    public function update(Request $request, Component $component)
    {
        $validated = $request->validate($this->rules($component->id));
        $component->update($validated);

        return redirect()->route('components.index')
            ->with('success', 'Componente actualizado correctamente.');
    }

    public function destroy(Component $component)
    {
        $component->delete();

        return redirect()->route('components.index')
            ->with('success', 'Componente eliminado correctamente.');
    }

    private function rules(?int $id = null): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
            'brand' => ['nullable', 'string', 'max:255'],
            'model' => ['nullable', 'string', 'max:255'],
            'serial_number' => ['nullable', 'string', 'max:255', Rule::unique('components', 'serial_number')->ignore($id)],
            'status' => ['required', Rule::in(['Disponible', 'En uso', 'Dañado', 'Baja'])],
            'unit_cost' => ['nullable', 'numeric', 'min:0'],
            'photo_url' => ['nullable', 'string', 'max:2048'],
            'notes' => ['nullable', 'string'],
        ];
    }
}