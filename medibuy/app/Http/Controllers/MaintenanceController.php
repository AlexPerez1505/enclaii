<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\Maintenance;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MaintenanceController extends Controller
{
    public function index()
    {
        $maintenances = Maintenance::with('equipment')->latest()->paginate(15);
        return view('maintenances.index', compact('maintenances'));
    }

    public function create()
    {
        $equipments = Equipment::orderBy('name')->get();
        return view('maintenances.create', compact('equipments'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->rules());

        $equipment = Equipment::findOrFail($validated['equipment_id']);
        $validated['equipment_name'] = $equipment->name;

        $maintenance = Maintenance::create($validated);

        if (!empty($validated['equipment_status_after'])) {
            $equipment->update(['status' => $validated['equipment_status_after']]);
        }

        return redirect()->route('maintenances.index')
            ->with('success', 'Mantenimiento registrado correctamente.');
    }

    public function show(Maintenance $maintenance)
    {
        $maintenance->load('equipment');
        return view('maintenances.show', compact('maintenance'));
    }

    public function edit(Maintenance $maintenance)
    {
        $equipments = Equipment::orderBy('name')->get();
        return view('maintenances.edit', compact('maintenance', 'equipments'));
    }

    public function update(Request $request, Maintenance $maintenance)
    {
        $validated = $request->validate($this->rules($maintenance->id));

        $equipment = Equipment::findOrFail($validated['equipment_id']);
        $validated['equipment_name'] = $equipment->name;

        $maintenance->update($validated);

        if (!empty($validated['equipment_status_after'])) {
            $equipment->update(['status' => $validated['equipment_status_after']]);
        }

        return redirect()->route('maintenances.index')
            ->with('success', 'Mantenimiento actualizado correctamente.');
    }

    public function destroy(Maintenance $maintenance)
    {
        $maintenance->delete();

        return redirect()->route('maintenances.index')
            ->with('success', 'Mantenimiento eliminado correctamente.');
    }

    private function rules(?int $id = null): array
    {
        return [
            'equipment_id' => ['required', 'exists:equipments,id'],
            'type' => ['required', Rule::in(['Preventivo', 'Correctivo'])],
            'date' => ['required', 'date'],
            'next_maintenance_date' => ['nullable', 'date', 'after_or_equal:date'],
            'technician' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'cost' => ['nullable', 'numeric', 'min:0'],
            'equipment_status_after' => ['nullable', Rule::in(['Disponible', 'Mantenimiento', 'Fuera de servicio'])],
            'calibration_certificate_url' => ['nullable', 'string', 'max:2048'],
        ];
    }
}