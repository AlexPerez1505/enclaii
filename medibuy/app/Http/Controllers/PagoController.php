<?php

namespace App\Http\Controllers;

use App\Models\Pago;
use App\Models\DocumentoPago;
use App\Models\ItemRemision;
use App\Models\PagoFinanciamiento;
use App\Models\Venta;
use App\Models\Orden;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class PagoController extends Controller
{
    // ============================================================
    // Helper: guardar UN archivo en documentos_pago
    // ============================================================
    private function guardarDocumentoPago(Pago $pago, UploadedFile $file): void
    {
        $folder = 'pagos';

        if (!empty($pago->venta_id)) {
            $folder .= '/venta_' . $pago->venta_id;
        } elseif (!empty($pago->orden_id)) {
            $folder .= '/orden_' . $pago->orden_id;
        } else {
            $folder .= '/general';
        }

        $path = $file->store($folder, 'public');

        DocumentoPago::create([
            'pago_id'         => $pago->id,
            'nombre_original' => $file->getClientOriginalName(),
            'ruta_archivo'    => $path,
        ]);
    }

    // ============================================================
    // Helper: guardar MUCHOS archivos o uno solo
    // ============================================================
    private function guardarEvidenciasPago(Request $request, Pago $pago): void
    {
        if ($request->hasFile('documentos')) {
            $documentos = $request->file('documentos');

            if (is_array($documentos)) {
                foreach ($documentos as $archivo) {
                    if ($archivo instanceof UploadedFile) {
                        $this->guardarDocumentoPago($pago, $archivo);
                    }
                }
            } elseif ($documentos instanceof UploadedFile) {
                $this->guardarDocumentoPago($pago, $documentos);
            }
        }

        if ($request->hasFile('recibo')) {
            $recibo = $request->file('recibo');

            if ($recibo instanceof UploadedFile) {
                $this->guardarDocumentoPago($pago, $recibo);
            }
        }
    }

    // ============================================================
    // Helper: borrar evidencias
    // ============================================================
    private function borrarDocumentosPago(Pago $pago): void
    {
        $docs = $pago->relationLoaded('documentos')
            ? $pago->documentos
            : DocumentoPago::where('pago_id', $pago->id)->get();

        foreach ($docs as $doc) {
            if (!empty($doc->ruta_archivo)) {
                Storage::disk('public')->delete($doc->ruta_archivo);
            }

            $doc->delete();
        }
    }

    // ============================================================
    // Helper: normalizar texto del plan porque en BD se guarda libre
    // ============================================================
    private function normalizarPlan(?string $plan): string
    {
        $plan = trim(mb_strtolower((string) $plan, 'UTF-8'));

        $reemplazos = [
            'á' => 'a',
            'é' => 'e',
            'í' => 'i',
            'ó' => 'o',
            'ú' => 'u',
            'ü' => 'u',
            'ñ' => 'n',
        ];

        return strtr($plan, $reemplazos);
    }

    // ============================================================
    // Helper: cambiar plan contado a personalizado
    // No agrega campos nuevos.
    // Edita directamente el campo plan en la tabla ventas.
    // ============================================================
    private function cambiarPlanContadoAPersonalizado(Venta $venta): void
    {
        $planActual = $this->normalizarPlan($venta->plan ?? '');

        if ($planActual !== 'contado') {
            return;
        }

        DB::table('ventas')
            ->where('id', $venta->id)
            ->update([
                'plan' => 'personalizado',
                'updated_at' => now(),
            ]);

        $venta->plan = 'personalizado';
    }

    // ============================================================
    // Helper: sincronizar cuota de financiamiento con tabla pagos
    // ============================================================
    private function sincronizarFinanciamientoDesdePagos(?int $financiamientoId): void
    {
        if (empty($financiamientoId)) {
            return;
        }

        $fin = PagoFinanciamiento::lockForUpdate()->find($financiamientoId);

        if (!$fin) {
            return;
        }

        $existePagoAprobado = Pago::where('financiamiento_id', $fin->id)
            ->where('aprobado', true)
            ->exists();

        if ($existePagoAprobado) {
            $fin->pagado = true;
            $fin->monto_pendiente = 0;
        } else {
            $fin->pagado = false;

            if (isset($fin->monto) && !is_null($fin->monto)) {
                $fin->monto_pendiente = (float) $fin->monto;
            } else {
                $fin->monto_pendiente = max((float) ($fin->monto_pendiente ?? 0), 0);
            }
        }

        $fin->save();
    }

    // ============================================================
    // PAGOS POR ITEM REMISION
    // ============================================================
    public function store(Request $request)
    {
        $data = $request->validate([
            'item_remision_id'  => ['required', 'exists:item_remisions,id'],
            'monto'             => ['required', 'numeric', 'min:0.01'],
            'fecha_pago'        => ['required', 'date'],
            'metodo_pago'       => ['required', 'string', 'max:255'],
            'es_anticipo'       => ['sometimes', 'boolean'],
            'financiamiento_id' => ['nullable', 'exists:pagos_financiamiento,id'],

            'recibo'            => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,webp,gif,bmp,svg,avif,heic,heif,tif,tiff', 'max:20480'],

            'documentos'        => ['nullable', 'array'],
            'documentos.*'      => ['file', 'mimes:pdf,jpg,jpeg,png,webp,gif,bmp,svg,avif,heic,heif,tif,tiff', 'max:20480'],
        ]);

        $item = ItemRemision::with('remision.venta')->findOrFail($data['item_remision_id']);

        try {
            DB::transaction(function () use ($request, $item, $data) {
                $pago = new Pago();

                $pago->item_remision_id  = $item->id;
                $pago->venta_id          = $item->remision->venta->id ?? null;
                $pago->financiamiento_id = $data['financiamiento_id'] ?? null;
                $pago->orden_id          = null;

                $pago->monto       = $data['monto'];
                $pago->fecha_pago  = $data['fecha_pago'];
                $pago->metodo_pago = $data['metodo_pago'];
                $pago->aprobado    = true;
                $pago->es_anticipo = $request->boolean('es_anticipo', false);

                $pago->save();

                $this->guardarEvidenciasPago($request, $pago);

                $item->a_cuenta = (float) $item->a_cuenta + (float) $pago->monto;
                $item->restante = max(((float) ($item->subtotal ?? 0)) - (float) $item->a_cuenta, 0);
                $item->save();

                if ($pago->financiamiento_id) {
                    $this->sincronizarFinanciamientoDesdePagos((int) $pago->financiamiento_id);
                }
            });
        } catch (\Throwable $e) {
            Log::error('ITEM_PAGO_STORE_FAIL', [
                'item' => $item->id ?? null,
                'e'    => $e->getMessage(),
            ]);

            return back()->with('error', 'No se pudo registrar el pago. Revisa logs.');
        }

        return redirect()->back()->with('success', 'Pago registrado correctamente.');
    }

    // ============================================================
    // PAGOS DE VENTA / FINANCIAMIENTO
    // ============================================================
    public function storePorVenta(Request $request, $ventaId)
    {
        $venta = Venta::findOrFail((int) $ventaId);

        $data = $request->validate([
            'financiamiento_id' => ['nullable', 'exists:pagos_financiamiento,id'],
            'monto'             => ['required', 'numeric', 'min:0.01'],
            'fecha_pago'        => ['required', 'date'],
            'metodo_pago'       => ['required', 'string', 'max:60'],

            'partes'            => ['nullable', 'array'],
            'partes.*.metodo'   => ['required_with:partes', 'string', 'max:60'],
            'partes.*.monto'    => ['required_with:partes', 'numeric', 'min:0.01'],

            'recibo'            => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,webp,gif,bmp,svg,avif,heic,heif,tif,tiff', 'max:20480'],

            'documentos'        => ['nullable', 'array'],
            'documentos.*'      => ['file', 'mimes:pdf,jpg,jpeg,png,webp,gif,bmp,svg,avif,heic,heif,tif,tiff', 'max:20480'],

            'es_anticipo'       => ['nullable', 'boolean'],
        ]);

        try {
            DB::transaction(function () use ($request, $venta, $data) {
                /*
                 * Regla importante:
                 * Si la venta está en plan contado y se registra un pago,
                 * se cambia automáticamente el campo plan a personalizado.
                 * No se usa input hidden.
                 * No se agrega ningún campo nuevo.
                 */
                $this->cambiarPlanContadoAPersonalizado($venta);

                $detalle = null;

                if (!empty($data['partes']) && is_array($data['partes'])) {
                    $detalle = array_values($data['partes']);
                }

                $pago = Pago::create([
                    'venta_id'          => $venta->id,
                    'financiamiento_id' => $data['financiamiento_id'] ?? null,
                    'item_remision_id'  => null,
                    'orden_id'          => null,

                    'monto'             => $data['monto'],
                    'fecha_pago'        => $data['fecha_pago'],
                    'metodo_pago'       => !empty($detalle) ? 'Mixto' : $data['metodo_pago'],
                    'detalle_metodos'   => $detalle,

                    'aprobado'          => false,
                    'es_anticipo'       => (bool) ($data['es_anticipo'] ?? false),
                ]);

                $this->guardarEvidenciasPago($request, $pago);

                if ($pago->financiamiento_id) {
                    $this->sincronizarFinanciamientoDesdePagos((int) $pago->financiamiento_id);
                }
            });
        } catch (\Throwable $e) {
            Log::error('VENTA_PAGO_STORE_FAIL', [
                'venta' => $venta->id,
                'e'     => $e->getMessage(),
            ]);

            return back()->with('error', 'No se pudo registrar el pago. Revisa logs.');
        }

        return back()->with('success', 'Pago registrado correctamente. El plan se actualizó a personalizado si era de contado.');
    }

    // ============================================================
    // ACTUALIZAR CALENDARIO DE PAGOS DE FINANCIAMIENTO
    // ============================================================
    public function updatePagosFinanciamiento(Request $request, $ventaId)
    {
        $venta = Venta::with('pagosFinanciamiento')->findOrFail((int) $ventaId);

        $data = $request->validate([
            'pagos_financiamiento' => ['required', 'array'],
            'pagos_financiamiento.*.descripcion' => ['nullable', 'string', 'max:255'],
            'pagos_financiamiento.*.fecha_pago' => ['nullable', 'date'],
            'pagos_financiamiento.*.monto' => ['nullable', 'numeric', 'min:0'],
            'pagos_financiamiento.*.eliminar' => ['nullable', 'boolean'],
        ]);

        try {
            DB::transaction(function () use ($venta, $data) {
                $seAgregoNuevoPago = false;

                foreach (($data['pagos_financiamiento'] ?? []) as $key => $row) {
                    $eliminar = !empty($row['eliminar']);
                    $esNuevo = !is_numeric($key);

                    if ($esNuevo) {
                        if ($eliminar) {
                            continue;
                        }

                        $descripcion = trim((string) ($row['descripcion'] ?? ''));
                        $fechaPago   = $row['fecha_pago'] ?? null;
                        $monto       = (float) ($row['monto'] ?? 0);

                        if ($descripcion === '' || empty($fechaPago) || $monto <= 0) {
                            continue;
                        }

                        PagoFinanciamiento::create([
                            'venta_id'        => $venta->id,
                            'descripcion'     => $descripcion,
                            'fecha_pago'      => $fechaPago,
                            'monto'           => $monto,
                            'monto_pendiente' => $monto,
                            'pagado'          => false,
                        ]);

                        $seAgregoNuevoPago = true;

                        continue;
                    }

                    $pagoFin = PagoFinanciamiento::lockForUpdate()
                        ->where('venta_id', $venta->id)
                        ->where('id', (int) $key)
                        ->first();

                    if (!$pagoFin) {
                        continue;
                    }

                    if ($eliminar) {
                        $tienePagoAprobado = Pago::where('financiamiento_id', $pagoFin->id)
                            ->where('aprobado', true)
                            ->exists();

                        if (!$tienePagoAprobado) {
                            Pago::where('financiamiento_id', $pagoFin->id)
                                ->where('aprobado', false)
                                ->delete();

                            $pagoFin->delete();
                        }

                        continue;
                    }

                    $descripcion = trim((string) ($row['descripcion'] ?? $pagoFin->descripcion));
                    $fechaPago   = $row['fecha_pago'] ?? $pagoFin->fecha_pago;
                    $monto       = array_key_exists('monto', $row)
                        ? (float) $row['monto']
                        : (float) $pagoFin->monto;

                    $pagoFin->descripcion = $descripcion !== '' ? $descripcion : $pagoFin->descripcion;
                    $pagoFin->fecha_pago = $fechaPago;
                    $pagoFin->monto = $monto;

                    $tienePagoAprobado = Pago::where('financiamiento_id', $pagoFin->id)
                        ->where('aprobado', true)
                        ->exists();

                    if (!$tienePagoAprobado) {
                        $pagoFin->pagado = false;
                        $pagoFin->monto_pendiente = $monto;
                    }

                    $pagoFin->save();

                    $this->sincronizarFinanciamientoDesdePagos((int) $pagoFin->id);
                }

                /*
                 * Regla importante:
                 * Si agregaste un pago nuevo y la venta era contado,
                 * se cambia directamente el campo ventas.plan a personalizado.
                 */
                if ($seAgregoNuevoPago) {
                    $this->cambiarPlanContadoAPersonalizado($venta);
                }
            });
        } catch (\Throwable $e) {
            Log::error('VENTA_PAGOS_FIN_UPDATE_FAIL', [
                'venta' => $venta->id ?? $ventaId,
                'e'     => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->with('error', 'No se pudo actualizar el calendario de pagos. Revisa logs.');
        }

        return back()->with('success', 'Calendario de pagos actualizado correctamente. El plan se actualizó a personalizado si era de contado.');
    }

    // ============================================================
    // PAGOS POR ORDEN DE SERVICIO
    // ============================================================
    public function storeOrden(Request $request, $ordenId)
    {
        $data = $request->validate([
            'monto'        => ['required', 'numeric', 'min:0.01'],
            'fecha_pago'   => ['required', 'date'],
            'metodo_pago'  => ['required', 'string', 'max:255'],
            'aprobado'     => ['sometimes', 'boolean'],

            'recibo'       => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,webp,gif,bmp,svg,avif,heic,heif,tif,tiff', 'max:20480'],

            'documentos'   => ['nullable', 'array'],
            'documentos.*' => ['file', 'mimes:pdf,jpg,jpeg,png,webp,gif,bmp,svg,avif,heic,heif,tif,tiff', 'max:20480'],
        ]);

        $orden = Orden::with('cliente')->findOrFail($ordenId);

        $pago = new Pago();

        $pago->orden_id          = $orden->id;
        $pago->venta_id          = null;
        $pago->financiamiento_id = null;
        $pago->item_remision_id  = null;

        $pago->monto       = $data['monto'];
        $pago->fecha_pago  = $data['fecha_pago'];
        $pago->metodo_pago = $data['metodo_pago'];
        $pago->aprobado    = array_key_exists('aprobado', $data) ? (bool) $data['aprobado'] : true;
        $pago->es_anticipo = false;

        $pago->save();

        $this->guardarEvidenciasPago($request, $pago);

        return redirect()->back()->with('success', 'Pago de Orden registrado correctamente.');
    }

    // ============================================================
    // HISTORIAL DE PAGOS POR ITEM
    // ============================================================
    public function index($item_id)
    {
        $item = ItemRemision::with(['remision.cliente', 'pagos.documentos'])->findOrFail($item_id);

        return view('pagos.index', compact('item'));
    }

    // ============================================================
    // VER PAGOS POR VENTA COMPLETA
    // ============================================================
    public function indexPorVenta($venta_id)
    {
        $venta = Venta::with(['cliente'])->findOrFail($venta_id);

        $pagosVenta = Pago::with('documentos')
            ->where('venta_id', $venta->id)
            ->orderByDesc('id')
            ->get();

        $pagosPlan = method_exists($venta, 'pagosFinanciamiento')
            ? $venta->pagosFinanciamiento()->orderBy('fecha_pago')->get()
            : collect();

        return view('ventas.pagos.index', compact('venta', 'pagosVenta', 'pagosPlan'));
    }

    // ============================================================
    // PDF RECIBO POR ITEM
    // ============================================================
    public function generarPDF($item_id)
    {
        $item = ItemRemision::with(['remision.cliente', 'pagos.documentos'])->findOrFail($item_id);

        $pdf = Pdf::loadView('pagos.recibo', compact('item'));

        return $pdf->stream('recibo_pago_' . $item->id . '.pdf');
    }

    // ============================================================
    // VISTA INTELIGENTE POR ITEM
    // ============================================================
    public function seguimientoInteligente($item_id)
    {
        $item = ItemRemision::with(['remision.venta', 'remision.cliente', 'pagos.documentos'])->findOrFail($item_id);

        return view('pagos.inteligente', compact('item'));
    }

    // ============================================================
    // VISTA INTELIGENTE POR VENTA COMPLETA
    // ============================================================
    public function seguimientoVenta($venta_id)
    {
        $venta = Venta::with(['cliente', 'pagos.documentos'])->findOrFail($venta_id);

        $detalleFinanciamiento = $venta->detalle_financiamiento;

        return view('pagos.inteligente_venta', compact('venta', 'detalleFinanciamiento'));
    }

    // ============================================================
    // APROBAR PAGO DE FINANCIAMIENTO
    // ============================================================
    public function marcarPagado(Request $request, $financiamientoId)
    {
        $request->validate([
            'pin' => ['required', 'digits:6'],
        ]);

        $pinReal = env('PAGOS_PIN');

        if (!is_null($pinReal) && (string) $request->pin !== (string) $pinReal) {
            return back()->with('error', 'PIN incorrecto.');
        }

        try {
            DB::transaction(function () use ($financiamientoId) {
                $fin = PagoFinanciamiento::lockForUpdate()->findOrFail($financiamientoId);

                Pago::where('financiamiento_id', $fin->id)
                    ->where('aprobado', false)
                    ->update(['aprobado' => true]);

                $this->sincronizarFinanciamientoDesdePagos((int) $fin->id);
            });
        } catch (\Throwable $e) {
            Log::error('FIN_APROBAR_FAIL', [
                'fin' => $financiamientoId,
                'e'   => $e->getMessage(),
            ]);

            return back()->with('error', 'No se pudo aprobar el pago. Revisa logs.');
        }

        return back()->with('success', 'Pago aprobado correctamente.');
    }

    // ============================================================
    // REVERTIR PAGO A PENDIENTE
    // ============================================================
    public function revertirAPendiente(Request $request, $id)
    {
        $request->validate([
            'pin' => ['required', 'digits:6'],
        ]);

        $pinReal = env('APROBACION_PIN');

        if (!is_null($pinReal) && (string) $request->pin !== (string) $pinReal) {
            return back()->with('error', 'PIN incorrecto.');
        }

        $pago = Pago::findOrFail($id);

        if (!$pago->aprobado) {
            return back()->with('error', 'Este pago ya está en estado pendiente.');
        }

        try {
            DB::transaction(function () use ($pago) {
                if ($pago->item_remision_id) {
                    $item = ItemRemision::lockForUpdate()->find($pago->item_remision_id);

                    if ($item) {
                        $item->a_cuenta = max(((float) $item->a_cuenta) - (float) $pago->monto, 0);

                        $subtotal = (float) ($item->subtotal ?? 0);

                        $item->restante = max($subtotal - (float) $item->a_cuenta, 0);
                        $item->save();
                    }
                }

                $financiamientoId = $pago->financiamiento_id ? (int) $pago->financiamiento_id : null;

                $pago->aprobado = false;
                $pago->save();

                if ($financiamientoId) {
                    $this->sincronizarFinanciamientoDesdePagos($financiamientoId);
                }
            });
        } catch (\Throwable $e) {
            Log::error('PAGO_REVERT_FAIL', [
                'pago' => $pago->id,
                'e'    => $e->getMessage(),
            ]);

            return back()->with('error', 'No se pudo revertir el pago. Revisa logs.');
        }

        return back()->with('success', 'Pago revertido a pendiente.');
    }

    // ============================================================
    // ELIMINAR PAGO + BORRAR EVIDENCIAS
    // ============================================================
    public function destroy(Request $request, $id)
    {
        $pago = Pago::with('documentos')->findOrFail($id);

        try {
            DB::transaction(function () use ($pago) {
                $financiamientoId = $pago->financiamiento_id ? (int) $pago->financiamiento_id : null;

                if ($pago->item_remision_id) {
                    $item = ItemRemision::lockForUpdate()->find($pago->item_remision_id);

                    if ($item) {
                        $item->a_cuenta = max(((float) $item->a_cuenta) - (float) $pago->monto, 0);

                        $subtotal = (float) ($item->subtotal ?? 0);

                        $item->restante = max($subtotal - (float) $item->a_cuenta, 0);
                        $item->save();
                    }
                }

                $this->borrarDocumentosPago($pago);

                $pago->delete();

                if ($financiamientoId) {
                    $this->sincronizarFinanciamientoDesdePagos($financiamientoId);
                }
            });
        } catch (\Throwable $e) {
            Log::error('PAGO_DELETE_FAIL', [
                'pago' => $pago->id,
                'e'    => $e->getMessage(),
            ]);

            return back()->with('error', 'No se pudo eliminar el pago. Revisa logs.');
        }

        return redirect()->back()->with('success', 'Pago eliminado correctamente. El financiamiento se actualizó en deudores.');
    }

    // ============================================================
    // PAGOS POR ORDEN INDEX
    // ============================================================
    public function indexPorOrden(Orden $orden)
    {
        $orden->load('cliente');

        $pagos = Pago::with('documentos')
            ->where('orden_id', $orden->id)
            ->orderBy('fecha_pago', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        $total = (float) ($orden->remision_total_pagar ?? 0);

        if ($total <= 0) {
            $total = (float) ($orden->remision_total ?? 0);
        }

        if ($total <= 0) {
            $total = (float) ($orden->remision_subtotal ?? 0);
        }

        $pagado = (float) $pagos->where('aprobado', true)->sum('monto');
        $pendientePorAprobar = (float) $pagos->where('aprobado', false)->sum('monto');

        $restante = max(0, $total - $pagado);
        $progreso = $total > 0 ? round(($pagado / $total) * 100, 2) : 0;

        return view('ordenes.pagos.index', compact(
            'orden',
            'pagos',
            'total',
            'pagado',
            'pendientePorAprobar',
            'restante',
            'progreso'
        ));
    }

    // ============================================================
    // REGISTRAR PAGO POR ORDEN
    // ============================================================
    public function storePorOrden(Request $request, Orden $orden)
    {
        $data = $request->validate([
            'monto'        => ['required', 'numeric', 'min:0.01'],
            'fecha_pago'   => ['required', 'date'],
            'metodo_pago'  => ['required', 'string', 'max:60'],
            'es_anticipo'  => ['nullable', 'boolean'],

            'recibo'       => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,webp,gif,bmp,svg,avif,heic,heif,tif,tiff', 'max:20480'],

            'documentos'   => ['nullable', 'array'],
            'documentos.*' => ['file', 'mimes:pdf,jpg,jpeg,png,webp,gif,bmp,svg,avif,heic,heif,tif,tiff', 'max:20480'],
        ]);

        try {
            DB::transaction(function () use ($request, $data, $orden) {
                $pago = Pago::create([
                    'orden_id'          => $orden->id,
                    'venta_id'          => null,
                    'financiamiento_id' => null,
                    'item_remision_id'  => null,

                    'monto'             => $data['monto'],
                    'fecha_pago'        => $data['fecha_pago'],
                    'metodo_pago'       => $data['metodo_pago'],
                    'es_anticipo'       => (bool) ($data['es_anticipo'] ?? false),
                    'aprobado'          => false,
                ]);

                $this->guardarEvidenciasPago($request, $pago);
            });
        } catch (\Throwable $e) {
            Log::error('ORDEN_PAGO_STORE_FAIL', [
                'orden' => $orden->id,
                'e'     => $e->getMessage(),
            ]);

            return back()->with('error', 'No se pudo registrar el pago de orden. Revisa logs.');
        }

        return redirect()->route('ordenes.pagos.index', $orden->id)
            ->with('ok', 'Pago registrado pendiente de aprobación.');
    }

    // ============================================================
    // APROBAR PAGO DE ORDEN
    // ============================================================
    public function aprobarPagoOrden(Pago $pago)
    {
        abort_if(!$pago->orden_id, 404);

        $pago->aprobado = true;
        $pago->save();

        return back()->with('ok', 'Pago aprobado.');
    }

    // ============================================================
    // REVERTIR PAGO DE ORDEN
    // ============================================================
    public function revertirPagoOrden(Pago $pago)
    {
        abort_if(!$pago->orden_id, 404);

        $pago->aprobado = false;
        $pago->save();

        return back()->with('ok', 'Pago revertido a pendiente.');
    }

    // ============================================================
    // ELIMINAR PAGO DE ORDEN
    // ============================================================
    public function destroyPagoOrden(Pago $pago)
    {
        abort_if(!$pago->orden_id, 404);

        $pago->load('documentos');

        try {
            DB::transaction(function () use ($pago) {
                $this->borrarDocumentosPago($pago);
                $pago->delete();
            });
        } catch (\Throwable $e) {
            Log::error('ORDEN_PAGO_DELETE_FAIL', [
                'pago' => $pago->id,
                'e'    => $e->getMessage(),
            ]);

            return back()->with('error', 'No se pudo eliminar el pago. Revisa logs.');
        }

        return back()->with('ok', 'Pago eliminado.');
    }
}