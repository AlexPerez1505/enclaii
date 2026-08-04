<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Equipment;
use App\Models\Invoice;
use App\Models\Logistics;
use App\Models\Rental;
use App\Models\RentalItem;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class RentalController extends Controller
{
    public function index()
    {
        $rentals = Rental::with([
            'cliente',
            'items.equipment.components',
            'logistics',
            'invoices',
        ])->latest()->get();

        $clients = Cliente::orderBy('nombre')->get();

        $equipments = Equipment::with('components')
            ->orderBy('name')
            ->get();

        return view('rentals.index', compact('rentals', 'clients', 'equipments'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->rules());

        $cliente = Cliente::findOrFail($validated['cliente_id']);
        $validated['cliente_nombre'] = $cliente->nombre_renta ?? trim(($cliente->nombre ?? '') . ' ' . ($cliente->apellido ?? ''));

        $validated['subtotal'] = (float) ($validated['subtotal'] ?? 0);
        $validated['iva'] = (float) ($validated['iva'] ?? 0);
        $validated['total'] = (float) ($validated['total'] ?? 0);

        Rental::create($validated);

        return redirect()
            ->route('rentals.index')
            ->with('success', 'Renta creada correctamente.');
    }

    public function update(Request $request, Rental $rental)
    {
        $validated = $request->validate($this->rules($rental->id));

        $cliente = Cliente::findOrFail($validated['cliente_id']);
        $validated['cliente_nombre'] = $cliente->nombre_renta ?? trim(($cliente->nombre ?? '') . ' ' . ($cliente->apellido ?? ''));

        $validated['subtotal'] = (float) ($validated['subtotal'] ?? 0);
        $validated['iva'] = (float) ($validated['iva'] ?? 0);
        $validated['total'] = (float) ($validated['total'] ?? 0);

        $rental->update($validated);

        return redirect()
            ->route('rentals.index')
            ->with('success', 'Renta actualizada correctamente.');
    }

    public function destroy(Rental $rental)
    {
        $equipmentIds = $rental->items()->pluck('equipment_id')->all();

        if (!empty($equipmentIds)) {
            Equipment::whereIn('id', $equipmentIds)
                ->whereNotIn('status', ['Mantenimiento', 'Fuera de servicio'])
                ->update(['status' => 'Disponible']);
        }

        $rental->delete();

        return redirect()
            ->route('rentals.index')
            ->with('success', 'Renta eliminada correctamente.');
    }

    public function changeStatus(Request $request, Rental $rental): JsonResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['Programada', 'En curso', 'Finalizada', 'Cancelada'])],
        ]);

        $newStatus = $validated['status'];

        $rental->loadMissing(['items', 'logistics', 'invoices']);

        if ($newStatus === 'En curso') {
            if ($rental->items->count() === 0) {
                return response()->json([
                    'message' => 'Debes agregar al menos un equipo antes de iniciar la renta.',
                ], 422);
            }

            if (!$rental->logistics) {
                return response()->json([
                    'message' => 'Debes crear un registro de logística antes de iniciar la renta.',
                ], 422);
            }

            if ($rental->logistics->status !== 'Entregado') {
                return response()->json([
                    'message' => 'La logística debe estar en "Entregado" para iniciar la renta.',
                ], 422);
            }
        }

        if ($newStatus === 'Finalizada') {
            $invoice = $rental->invoices()->latest()->first();

            if (!$invoice) {
                return response()->json([
                    'message' => 'Debes generar una factura antes de finalizar la renta.',
                ], 422);
            }
        }

        $rental->update(['status' => $newStatus]);

        if (in_array($newStatus, ['Finalizada', 'Cancelada'], true)) {
            $equipmentIds = $rental->items()->pluck('equipment_id')->all();

            if (!empty($equipmentIds)) {
                Equipment::whereIn('id', $equipmentIds)
                    ->whereNotIn('status', ['Mantenimiento', 'Fuera de servicio'])
                    ->update(['status' => 'Disponible']);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Estado actualizado correctamente.',
            'rental' => $this->rentalPayload($rental->fresh([
                'cliente',
                'items.equipment.components',
                'logistics',
                'invoices',
            ])),
        ]);
    }

    public function storeItem(Request $request, Rental $rental): JsonResponse
    {
        if ($rental->status !== 'Programada') {
            return response()->json([
                'message' => 'Solo puedes agregar equipos cuando la renta está en estado Programada.',
            ], 422);
        }

        $validated = $request->validate([
            'equipment_id' => ['required', 'exists:equipments,id'],
            'applied_price' => ['nullable', 'numeric', 'min:0'],
            'quantity' => ['nullable', 'integer', 'min:1'],
        ]);

        $equipment = Equipment::findOrFail($validated['equipment_id']);

        if ($equipment->status !== 'Disponible') {
            return response()->json([
                'message' => 'Ese equipo no está disponible.',
            ], 422);
        }

        $rental->items()->create([
            'equipment_id' => $equipment->id,
            'equipment_name' => $equipment->name,
            'serial_number' => $equipment->serial_number,
            'applied_price' => $validated['applied_price'] ?? $equipment->rental_price_day ?? 0,
            'quantity' => $validated['quantity'] ?? 1,
            'hours_used' => null,
            'observations' => null,
        ]);

        if (!in_array($equipment->status, ['Mantenimiento', 'Fuera de servicio'], true)) {
            $equipment->update(['status' => 'Rentado']);
        }

        $this->recalculateTotals($rental);

        return response()->json([
            'success' => true,
            'message' => 'Equipo agregado correctamente.',
            'rental' => $this->rentalPayload($rental->fresh([
                'cliente',
                'items.equipment.components',
                'logistics',
                'invoices',
            ])),
        ]);
    }

    public function destroyItem(Rental $rental, RentalItem $item): JsonResponse
    {
        if ($item->rental_id !== $rental->id) {
            abort(404);
        }

        if ($rental->status !== 'Programada') {
            return response()->json([
                'message' => 'Solo puedes quitar equipos cuando la renta está en estado Programada.',
            ], 422);
        }

        $equipment = Equipment::find($item->equipment_id);

        $item->delete();

        if ($equipment && !in_array($equipment->status, ['Mantenimiento', 'Fuera de servicio'], true)) {
            $equipment->update(['status' => 'Disponible']);
        }

        $this->recalculateTotals($rental);

        return response()->json([
            'success' => true,
            'message' => 'Equipo removido correctamente.',
            'rental' => $this->rentalPayload($rental->fresh([
                'cliente',
                'items.equipment.components',
                'logistics',
                'invoices',
            ])),
        ]);
    }

    public function markInvoicePaid(Rental $rental): JsonResponse
    {
        /** @var Invoice|null $invoice */
        $invoice = $rental->invoices()->latest()->first();

        if (!$invoice) {
            return response()->json([
                'message' => 'No existe factura para esta renta.',
            ], 422);
        }

        $invoice->update([
            'payment_status' => 'Pagado',
            'payment_date' => now()->toDateString(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Factura marcada como pagada.',
            'rental' => $this->rentalPayload($rental->fresh([
                'cliente',
                'items.equipment.components',
                'logistics',
                'invoices',
            ])),
        ]);
    }

    public function updateLogisticsStatus(Request $request, Logistics $logistic): JsonResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['Pendiente', 'En camino', 'Entregado', 'Recogido'])],
        ]);

        $logistic->update([
            'status' => $validated['status'],
        ]);

        $rental = $logistic->rental()->with([
            'cliente',
            'items.equipment.components',
            'logistics',
            'invoices',
        ])->firstOrFail();

        return response()->json([
            'success' => true,
            'message' => 'Logística actualizada correctamente.',
            'rental' => $this->rentalPayload($rental),
        ]);
    }

    private function rules(?int $id = null): array
    {
        return [
            'cliente_id' => ['required', 'exists:clientes,id'],
            'cliente_nombre' => ['nullable', 'string', 'max:255'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'service_type' => ['required', Rule::in(['Cirugía', 'Evento programado', 'Urgencia', 'Renta prolongada'])],
            'service_location' => ['nullable', 'string', 'max:255'],
            'responsible' => ['nullable', 'string', 'max:255'],
            'status' => ['required', Rule::in(['Programada', 'En curso', 'Finalizada', 'Cancelada'])],
            'subtotal' => ['nullable', 'numeric', 'min:0'],
            'iva' => ['nullable', 'numeric', 'min:0'],
            'total' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ];
    }

    private function recalculateTotals(Rental $rental): void
    {
        $rental->loadMissing('items');

        $subtotal = (float) $rental->items->sum(function ($item) {
            return ((float) $item->applied_price) * ((int) $item->quantity);
        });

        $iva = round($subtotal * 0.16, 2);
        $total = round($subtotal + $iva, 2);

        $rental->update([
            'subtotal' => $subtotal,
            'iva' => $iva,
            'total' => $total,
        ]);
    }

    private function rentalPayload(Rental $rental): array
    {
        $rental->loadMissing([
            'cliente',
            'items.equipment.components',
            'logistics',
            'invoices',
        ]);

        $invoice = $rental->invoices->sortByDesc('id')->first();

        return [
            'id' => $rental->id,
            'cliente_id' => (string) $rental->cliente_id,
            'cliente_nombre' => $rental->cliente_nombre,
            'start_date' => optional($rental->start_date)->format('Y-m-d'),
            'end_date' => optional($rental->end_date)->format('Y-m-d'),
            'service_type' => $rental->service_type,
            'service_location' => $rental->service_location,
            'responsible' => $rental->responsible,
            'status' => $rental->status,
            'subtotal' => (float) $rental->subtotal,
            'iva' => (float) $rental->iva,
            'total' => (float) $rental->total,
            'notes' => $rental->notes,
            'items' => $rental->items->map(function ($item) {
                $equipment = $item->equipment;

                return [
                    'id' => $item->id,
                    'equipment_id' => (string) $item->equipment_id,
                    'equipment_name' => $item->equipment_name,
                    'serial_number' => $item->serial_number,
                    'applied_price' => (float) $item->applied_price,
                    'quantity' => (int) $item->quantity,
                    'hours_used' => $item->hours_used,
                    'observations' => $item->observations,
                    'is_package' => (bool) ($equipment?->is_package),
                    'components' => $equipment
                        ? $equipment->components->map(function ($component) {
                            return [
                                'id' => $component->id,
                                'name' => $component->name,
                                'brand' => $component->brand,
                                'model' => $component->model,
                                'serial_number' => $component->serial_number,
                                'quantity' => $component->pivot->quantity ?? 1,
                                'condition' => $component->pivot->condition ?? 'Buenas condiciones',
                                'notes' => $component->pivot->notes ?? '',
                            ];
                        })->values()->toArray()
                        : [],
                ];
            })->values()->toArray(),
            'logistics' => $rental->logistics ? [
                'id' => $rental->logistics->id,
                'rental_id' => $rental->logistics->rental_id,
                'status' => $rental->logistics->status,
                'driver' => $rental->logistics->driver,
                'delivery_date' => $rental->logistics->delivery_date,
                'pickup_date' => $rental->logistics->pickup_date,
                'delivery_address' => $rental->logistics->delivery_address,
                'notes' => $rental->logistics->notes,
            ] : null,
            'invoice' => $invoice ? [
                'id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'subtotal' => (float) $invoice->subtotal,
                'iva' => (float) $invoice->iva,
                'total' => (float) $invoice->total,
                'payment_status' => $invoice->payment_status,
                'payment_method' => $invoice->payment_method,
                'payment_date' => optional($invoice->payment_date)->format('Y-m-d'),
                'due_date' => optional($invoice->due_date)->format('Y-m-d'),
                'notes' => $invoice->notes,
            ] : null,
        ];
    }
}