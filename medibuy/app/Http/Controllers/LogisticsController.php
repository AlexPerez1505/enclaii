<?php

namespace App\Http\Controllers;

use App\Models\Logistics;
use App\Models\Rental;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LogisticsController extends Controller
{
    public function index()
    {
        $logistics = Logistics::with('rental')->latest()->paginate(15);
        return view('logistics.index', compact('logistics'));
    }

    public function create()
    {
        $rentals = Rental::orderByDesc('id')->get();
        return view('logistics.create', compact('rentals'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->rules());

        if (empty($validated['rental_client']) && !empty($validated['rental_id'])) {
            $rental = Rental::find($validated['rental_id']);
            $validated['rental_client'] = $rental?->client_name;
        }

        Logistics::create($validated);

        return redirect()->route('logistics.index')
            ->with('success', 'Logística registrada correctamente.');
    }

    public function show(Logistics $logistic)
    {
        $logistic->load('rental');
        return view('logistics.show', ['logistics' => $logistic]);
    }

    public function edit(Logistics $logistic)
    {
        $rentals = Rental::orderByDesc('id')->get();
        return view('logistics.edit', ['logistics' => $logistic, 'rentals' => $rentals]);
    }

    public function update(Request $request, Logistics $logistic)
    {
        $validated = $request->validate($this->rules($logistic->id));

        if (empty($validated['rental_client']) && !empty($validated['rental_id'])) {
            $rental = Rental::find($validated['rental_id']);
            $validated['rental_client'] = $rental?->client_name;
        }

        $logistic->update($validated);

        return redirect()->route('logistics.index')
            ->with('success', 'Logística actualizada correctamente.');
    }

    public function destroy(Logistics $logistic)
    {
        $logistic->delete();

        return redirect()->route('logistics.index')
            ->with('success', 'Logística eliminada correctamente.');
    }

    private function rules(?int $id = null): array
    {
        return [
            'rental_id' => ['required', 'exists:rentals,id', Rule::unique('logistics', 'rental_id')->ignore($id)],
            'rental_client' => ['nullable', 'string', 'max:255'],
            'delivery_date' => ['nullable', 'date'],
            'pickup_date' => ['nullable', 'date', 'after_or_equal:delivery_date'],
            'driver' => ['nullable', 'string', 'max:255'],
            'status' => ['required', Rule::in(['Pendiente', 'En camino', 'Entregado', 'Recogido'])],
            'delivery_address' => ['nullable', 'string'],
            'delivery_photo_url' => ['nullable', 'string', 'max:2048'],
            'pickup_photo_url' => ['nullable', 'string', 'max:2048'],
            'signature_url' => ['nullable', 'string', 'max:2048'],
            'notes' => ['nullable', 'string'],
        ];
    }
}