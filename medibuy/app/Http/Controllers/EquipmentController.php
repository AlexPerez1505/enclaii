<?php

namespace App\Http\Controllers;

use App\Models\Component;
use App\Models\Equipment;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EquipmentController extends Controller
{
    public function index()
    {
        $equipments = Equipment::with(['components' => function ($query) {
                $query->orderBy('name');
            }])
            ->latest()
            ->get();

        $components = Component::orderBy('name')->get();

        return view('equipments.index', compact('equipments', 'components'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->rules());

        $components = $validated['components'] ?? [];
        $isPackage = (bool) ($validated['is_package'] ?? false);

        unset($validated['components']);

        $equipment = Equipment::create($validated);

        $this->syncComponents($equipment, $components, $isPackage);

        return redirect()
            ->route('equipments.index')
            ->with('success', 'Equipo creado correctamente.');
    }

    public function update(Request $request, Equipment $equipment)
    {
        $validated = $request->validate($this->rules($equipment->id));

        $components = $validated['components'] ?? [];
        $isPackage = (bool) ($validated['is_package'] ?? false);

        unset($validated['components']);

        $equipment->update($validated);

        $this->syncComponents($equipment, $components, $isPackage);

        return redirect()
            ->route('equipments.index')
            ->with('success', 'Equipo actualizado correctamente.');
    }

    public function destroy(Equipment $equipment)
    {
        $equipment->components()->detach();
        $equipment->delete();

        return redirect()
            ->route('equipments.index')
            ->with('success', 'Equipo eliminado correctamente.');
    }

    private function rules(?int $id = null): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:255'],
            'brand' => ['nullable', 'string', 'max:255'],
            'model' => ['nullable', 'string', 'max:255'],
            'serial_number' => ['required', 'string', 'max:255', Rule::unique('equipments', 'serial_number')->ignore($id)],
            'is_package' => ['nullable', 'boolean'],
            'year_of_manufacture' => ['nullable', 'integer', 'min:1900', 'max:' . (date('Y') + 1)],
            'status' => ['required', Rule::in(['Disponible', 'Rentado', 'Mantenimiento', 'Fuera de servicio'])],
            'current_location' => ['nullable', 'string', 'max:255'],
            'equipment_cost' => ['nullable', 'numeric', 'min:0'],
            'rental_price_day' => ['nullable', 'numeric', 'min:0'],
            'rental_price_event' => ['nullable', 'numeric', 'min:0'],
            'useful_life_years' => ['nullable', 'integer', 'min:0'],
            'photo_url' => ['nullable', 'string', 'max:2048'],
            'notes' => ['nullable', 'string'],

            'components' => ['nullable', 'array'],
            'components.*.component_id' => ['nullable', 'exists:components,id'],
            'components.*.quantity' => ['nullable', 'integer', 'min:1'],
            'components.*.notes' => ['nullable', 'string'],
        ];
    }

private function syncComponents(Equipment $equipment, array $components, bool $isPackage): void
{
    if (!$isPackage) {
        $equipment->components()->detach();
        return;
    }

    $syncData = [];

    foreach ($components as $row) {
        if (empty($row['component_id']) && empty($row['name'])) {
            continue;
        }

        $component = null;

        if (!empty($row['component_id'])) {
            $component = Component::find($row['component_id']);

            if ($component) {
                $component->update([
                    'name' => $row['name'] ?? $component->name,
                    'category' => $equipment->category,
                    'brand' => $row['brand'] ?? $component->brand,
                    'model' => $row['model'] ?? $component->model,
                    'serial_number' => $row['serial_number'] ?? $component->serial_number,
                ]);
            }
        } else {
            $component = Component::create([
                'name' => $row['name'],
                'category' => $equipment->category,
                'brand' => $row['brand'] ?? null,
                'model' => $row['model'] ?? null,
                'serial_number' => $row['serial_number'] ?? null,
                'status' => 'Disponible',
                'notes' => $row['notes'] ?? null,
            ]);
        }

        if (!$component) {
            continue;
        }

        $syncData[$component->id] = [
            'quantity' => $row['quantity'] ?? 1,
            'condition' => $row['condition'] ?? 'Buenas condiciones',
            'notes' => $row['notes'] ?? null,
        ];
    }

    $equipment->components()->sync($syncData);
}
}