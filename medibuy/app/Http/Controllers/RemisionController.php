<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

use App\Models\Remision;
use App\Models\Cliente;

use NumberFormatter;
use PDF;

class RemisionController extends Controller
{
    public function index()
    {
        $remisiones = Remision::with('cliente', 'user')->latest()->get();
        return view('remisions.index', compact('remisiones'));
    }

    public function create()
    {
        $clientes = Cliente::all();
        return view('remisions.create', compact('clientes'));
    }

    public function store(Request $request)
    {
        // ✅ Normaliza switches (por si no vienen)
        $request->merge([
            'aplicar_iva' => (int)($request->input('aplicar_iva') == 1),
            'tiene_envio' => (int)($request->input('tiene_envio') == 1),
            'envio_costo' => $request->input('envio_costo', 0),
        ]);

        // ✅ Validación
        $request->validate([
            'cliente_id' => 'required|exists:clientes,id',

            'items' => 'required|array|min:1',
            'items.*.unidad' => 'required|string|max:50',
            'items.*.cantidad' => 'required|integer|min:1',
            'items.*.nombre_item' => 'required|string|max:255',
            'items.*.descripcion_item' => 'nullable|string',
            'items.*.importe_unitario' => 'required|numeric|min:0',

            'aplicar_iva' => 'required|boolean',

            // Envío
            'tiene_envio' => 'required|boolean',
            'envio_costo' => 'nullable|numeric|min:0',
            'envio_direccion' => 'nullable|string|max:255',

            // Meses
            'meses_a_pagar' => 'nullable|integer|min:1|max:24',
        ]);

        // ✅ Calcular subtotal items
        $subtotal = 0;
        foreach ($request->items as $item) {
            $subtotal += ((int)$item['cantidad']) * ((float)$item['importe_unitario']);
        }
        $subtotal = round($subtotal, 2);

        // ✅ IVA
        $iva = $request->aplicar_iva ? round($subtotal * 0.16, 2) : 0;

        // ✅ Envío
        $envio = $request->tiene_envio ? round((float)$request->envio_costo, 2) : 0;

        // ✅ Total final
        $total = round($subtotal + $iva + $envio, 2);

        // ✅ Mensualidad
        $meses = $request->filled('meses_a_pagar') ? (int)$request->meses_a_pagar : null;
        $mensualidad = ($meses && $meses > 0) ? round($total / $meses, 2) : null;

        // ✅ Total en letra
        $parteEntera = (int)floor($total);
        $centavos = (int)round(($total - $parteEntera) * 100);

        $formatter = new NumberFormatter('es', NumberFormatter::SPELLOUT);
        $letra = strtoupper($formatter->format($parteEntera)) . ' PESOS';
        $letra .= ' CON ' . str_pad((string)$centavos, 2, '0', STR_PAD_LEFT) . '/100 M.N.';

        try {
            $remision = null;

            DB::transaction(function () use ($request, $subtotal, $iva, $envio, $total, $meses, $mensualidad, $letra, &$remision) {

                // ✅ Crear Remisión
                $remision = Remision::create([
                    'cliente_id' => $request->cliente_id,
                    'user_id' => Auth::id(),

                    'subtotal' => $subtotal,
                    'iva' => $iva,
                    'aplicar_iva' => $request->aplicar_iva,

                    'tiene_envio' => $request->tiene_envio,
                    'envio_costo' => $envio,
                    'envio_direccion' => $request->tiene_envio ? ($request->envio_direccion ?: null) : null,

                    'meses_a_pagar' => $meses,
                    'mensualidad' => $mensualidad,

                    'total' => $total,
                    'importe_letra' => $letra,
                ]);

                // ✅ Compatibilidad: si tu tabla item_remisions todavía trae estas columnas viejas
                $hasACuenta  = Schema::hasColumn('item_remisions', 'a_cuenta');
                $hasRestante = Schema::hasColumn('item_remisions', 'restante');

                // ✅ Crear items
                foreach ($request->items as $item) {
                    $cantidad = (int)$item['cantidad'];
                    $importe_unitario = (float)$item['importe_unitario'];
                    $itemSubtotal = round($cantidad * $importe_unitario, 2);

                    $payload = [
                        'unidad' => $item['unidad'],
                        'cantidad' => $cantidad,
                        'nombre_item' => $item['nombre_item'],
                        'descripcion_item' => $item['descripcion_item'] ?? null,
                        'importe_unitario' => $importe_unitario,
                        'subtotal' => $itemSubtotal,
                    ];

                    // ✅ Si existen en DB, los llenamos para evitar errores NOT NULL
                    if ($hasACuenta)  $payload['a_cuenta'] = 0;
                    if ($hasRestante) $payload['restante'] = $itemSubtotal;

                    $remision->items()->create($payload);
                }
            });

            return redirect()->route('remisions.show', $remision->id)
                ->with('success', 'Remisión creada correctamente.');
        } catch (\Exception $e) {
            return redirect()->route('remisions.index')
                ->with('error', 'Error al crear la remisión: ' . $e->getMessage());
        }
    }

    public function show(Remision $remision)
    {
        $remision->load('cliente', 'items', 'user');
        return view('remisions.show', compact('remision'));
    }

    /**
     * ✅ PDF “normal” (si todavía usas remisions.pdf)
     */
    public function descargarPdf(Remision $remision)
    {
        $remision->load('cliente', 'user', 'items');
        $pdf = PDF::loadView('remisions.pdf', compact('remision'));
        return $pdf->download("remision_{$remision->id}.pdf");
    }

    /**
     * ✅ Vista Ticket Mantenimiento (para ver en navegador)
     * Requiere: resources/views/remisions/ticket_mantenimiento.blade.php
     */
    public function ticketMantenimiento(Remision $remision)
    {
        $remision->load('cliente', 'user', 'items');

        // Si no usas QR todavía, puedes dejarlo null
        $qr_path = null;

        return view('remisions.ticket_mantenimiento', compact('remision', 'qr_path'));
    }

    /**
     * ✅ Descargar Ticket Mantenimiento en PDF (térmico)
     * Mucho mejor para impresión de servicio
     */
    public function ticketMantenimientoPdf(Remision $remision)
    {
        $remision->load('cliente', 'user', 'items');

        $qr_path = null;

        $pdf = PDF::loadView('remisions.ticket_mantenimiento', compact('remision', 'qr_path'));

        // Tip opcional si quieres tamaño ticket 80mm (dependiendo del driver):
        // $pdf->setPaper([0, 0, 226.77, 1000], 'portrait'); // ~80mm ancho

        return $pdf->download("orden_servicio_{$remision->id}.pdf");
    }
}
