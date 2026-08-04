<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Invoice;
use App\Models\Rental;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::with(['client', 'rental'])->latest()->paginate(15);
        return view('invoices.index', compact('invoices'));
    }

    public function create()
    {
        $clients = Client::where('active', true)->orderBy('name')->get();
        $rentals = Rental::orderByDesc('id')->get();

        return view('invoices.create', compact('clients', 'rentals'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->rules());

        if (empty($validated['client_name']) && !empty($validated['client_id'])) {
            $client = Client::find($validated['client_id']);
            $validated['client_name'] = $client?->name;
        }

        Invoice::create($validated);

        return redirect()->route('invoices.index')
            ->with('success', 'Factura creada correctamente.');
    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['client', 'rental']);
        return view('invoices.show', compact('invoice'));
    }

    public function edit(Invoice $invoice)
    {
        $clients = Client::where('active', true)->orderBy('name')->get();
        $rentals = Rental::orderByDesc('id')->get();

        return view('invoices.edit', compact('invoice', 'clients', 'rentals'));
    }

    public function update(Request $request, Invoice $invoice)
    {
        $validated = $request->validate($this->rules($invoice->id));

        if (empty($validated['client_name']) && !empty($validated['client_id'])) {
            $client = Client::find($validated['client_id']);
            $validated['client_name'] = $client?->name;
        }

        $invoice->update($validated);

        return redirect()->route('invoices.index')
            ->with('success', 'Factura actualizada correctamente.');
    }

    public function destroy(Invoice $invoice)
    {
        $invoice->delete();

        return redirect()->route('invoices.index')
            ->with('success', 'Factura eliminada correctamente.');
    }

    private function rules(?int $id = null): array
    {
        return [
            'rental_id' => ['required', 'exists:rentals,id'],
            'client_id' => ['required', 'exists:clients,id'],
            'client_name' => ['nullable', 'string', 'max:255'],
            'invoice_number' => ['required', 'string', 'max:255', Rule::unique('invoices', 'invoice_number')->ignore($id)],
            'subtotal' => ['nullable', 'numeric', 'min:0'],
            'iva' => ['nullable', 'numeric', 'min:0'],
            'total' => ['required', 'numeric', 'min:0'],
            'payment_status' => ['required', Rule::in(['Pendiente', 'Pagado', 'Parcial', 'Vencido'])],
            'payment_method' => ['nullable', Rule::in(['Transferencia', 'Efectivo', 'Tarjeta', 'Cheque', 'Otro'])],
            'payment_date' => ['nullable', 'date'],
            'due_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
        ];
    }
}