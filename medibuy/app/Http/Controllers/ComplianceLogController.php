<?php

namespace App\Http\Controllers;

use App\Models\ComplianceLog;
use App\Models\Equipment;
use App\Models\Rental;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ComplianceLogController extends Controller
{
    public function index()
    {
        $complianceLogs = ComplianceLog::with(['equipment', 'rental'])->latest()->paginate(15);
        return view('compliance_logs.index', compact('complianceLogs'));
    }

    public function create()
    {
        $equipments = Equipment::orderBy('name')->get();
        $rentals = Rental::orderByDesc('id')->get();

        return view('compliance_logs.create', compact('equipments', 'rentals'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->rules());

        $equipment = Equipment::findOrFail($validated['equipment_id']);
        $validated['equipment_name'] = $equipment->name;
        $validated['serial_number'] = $equipment->serial_number;

        if (!empty($validated['rental_id']) && empty($validated['client_name'])) {
            $rental = Rental::find($validated['rental_id']);
            $validated['client_name'] = $rental?->client_name;
        }

        $validated['maintenance_valid'] = $validated['maintenance_valid'] ?? true;

        ComplianceLog::create($validated);

        return redirect()->route('compliance_logs.index')
            ->with('success', 'Bitácora de compliance creada correctamente.');
    }

    public function show(ComplianceLog $complianceLog)
    {
        $complianceLog->load(['equipment', 'rental']);
        return view('compliance_logs.show', compact('complianceLog'));
    }

    public function edit(ComplianceLog $complianceLog)
    {
        $equipments = Equipment::orderBy('name')->get();
        $rentals = Rental::orderByDesc('id')->get();

        return view('compliance_logs.edit', compact('complianceLog', 'equipments', 'rentals'));
    }

    public function update(Request $request, ComplianceLog $complianceLog)
    {
        $validated = $request->validate($this->rules($complianceLog->id));

        $equipment = Equipment::findOrFail($validated['equipment_id']);
        $validated['equipment_name'] = $equipment->name;
        $validated['serial_number'] = $equipment->serial_number;

        if (!empty($validated['rental_id']) && empty($validated['client_name'])) {
            $rental = Rental::find($validated['rental_id']);
            $validated['client_name'] = $rental?->client_name;
        }

        $validated['maintenance_valid'] = $validated['maintenance_valid'] ?? true;

        $complianceLog->update($validated);

        return redirect()->route('compliance_logs.index')
            ->with('success', 'Bitácora de compliance actualizada correctamente.');
    }

    public function destroy(ComplianceLog $complianceLog)
    {
        $complianceLog->delete();

        return redirect()->route('compliance_logs.index')
            ->with('success', 'Bitácora de compliance eliminada correctamente.');
    }

    private function rules(?int $id = null): array
    {
        return [
            'equipment_id' => ['required', 'exists:equipments,id'],
            'rental_id' => ['nullable', 'exists:rentals,id'],
            'client_name' => ['nullable', 'string', 'max:255'],
            'event_type' => ['required', Rule::in([
                'Uso en cirugía',
                'Entrega',
                'Recolección',
                'Mantenimiento',
                'Calibración',
                'Falla',
                'Incidente',
            ])],
            'date' => ['required', 'date'],
            'responsible' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'maintenance_valid' => ['nullable', 'boolean'],
            'document_url' => ['nullable', 'string', 'max:2048'],
        ];
    }
}