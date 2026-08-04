<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\Venta;
use App\Models\VentaProducto;
use App\Models\PagoFinanciamiento;
use App\Models\Paquete;
use App\Models\Pago;
use App\Models\CartaGarantia;
use App\Models\DocumentoPago;
use App\Models\Registro;
use App\Models\Checklist;
use App\Services\WhatsAppService;
use App\Models\VentaTradein; // ✅ Equipos a cuenta
use PDF;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\File; // ✅ para buildRemisionPdf
use Illuminate\Support\Str;
use setasign\Fpdi\Fpdi;
use Carbon\Carbon;
use App\Mail\PagoPendienteHoyMail;
use App\Mail\PagoPendienteHoyAdminMail;
use App\Models\User;
use App\Notifications\PagoFinanciamientoAlertNotification;
use Illuminate\Support\Facades\Route as RouteFacade;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use App\Models\Orden;
use Symfony\Component\HttpFoundation\StreamedResponse;

class VentaController extends Controller
{
    // Método para devolver clientes con filtro (para fetch AJAX)
    public function buscarClientes(Request $request)
    {
        $search = $request->input('search', '');

        $clientes = Cliente::query()
            ->where('nombre', 'LIKE', "%{$search}%")
            ->orWhere('apellido', 'LIKE', "%{$search}%")
            ->get(['id', 'nombre', 'apellido', 'telefono', 'email', 'comentarios']);

        return response()->json($clientes);
    }

    public function create()
    {
        $productos = Producto::all();
        $paquetes  = Paquete::all();
        $cartas    = CartaGarantia::all();
        $registros = Registro::where('estado_actual', 'stock')->get();
        $categorias = \App\Models\Categoria::orderBy('nombre')->get();

        return view('venta.create', compact('productos', 'paquetes', 'cartas', 'registros','categorias'));
    }

    /**
     * STORE con:
     * 1) Equipos a cuenta (trade-ins): tradeins_json
     * 2) Anticipo real: anticipo_monto / anticipo_fecha / anticipo_metodo
     * Aplica a TODOS los planes.
     *
     * Además:
     * - Trade-in + Anticipo se aplican como "crédito inicial" al calendario de pagos.
     * - Anticipo se guarda como Pago real APROBADO (es_anticipo = true).
     * - Trade-in también se guarda como Pago real APROBADO.
     *
     * ✅ NUEVO: Soporta "is_regalo" dentro de productos_json
     */
    public function store(Request $request)
    { 
        $request->validate([
            'cliente_id'        => 'required|exists:clientes,id',
            'subtotal'          => 'required|numeric',
            'total'             => 'required|numeric',  // total ORIGINAL del front (sin trade-in)
            'productos_json'    => 'required|json',
            'pagos_json'        => 'nullable|json',
            'carta_garantia_id' => 'nullable|exists:carta_garantias,id',
            'meses_garantia'    => 'required|integer|in:6,9,12,15,18',

            // Trade-ins + anticipo
            'tradeins_json'     => 'nullable|json',
            'anticipo_monto'    => 'nullable|numeric|min:0',
            'anticipo_fecha'    => 'nullable|date',
            'anticipo_metodo'   => 'nullable|string|max:255',
        ]);

        Log::info('Iniciando creación de venta...');
        Log::info('Request completo:', $request->all());

        // Normalizar para que nunca vayan null al DB
        $descuento = $request->descuento;
        if ($descuento === null || $descuento === '') {
            $descuento = 0;
        }

        $envio = $request->envio;
        if ($envio === null || $envio === '') {
            $envio = 0;
        }

        $iva = $request->iva;
        if ($iva === null || $iva === '') {
            $iva = 0;
        }

        DB::beginTransaction();

        try {
            // =============================
            // 0) Calcular trade-ins
            // =============================
            $tradeins     = [];
            $tradeinTotal = 0;

            if ($request->filled('tradeins_json')) {
                $tradeins = json_decode($request->tradeins_json, true) ?: [];
                foreach ($tradeins as $t) {
                    // Compat: aceptamos valor_a_cuenta o valor
                    $valorAcuenta   = (float) ($t['valor_a_cuenta'] ?? $t['valor'] ?? 0);
                    $tradeinTotal  += max($valorAcuenta, 0);
                }
            }

            $totalOriginal = (float) $request->total;
            $totalNeto     = max($totalOriginal - $tradeinTotal, 0); // sólo informativo

            // =============================
            // 1) Crear la venta
            // =============================
            $venta = Venta::create([
                'cliente_id'             => $request->cliente_id,
                'lugar'                  => $request->lugar,
                'nota'                   => $request->nota,
                'user_id'                => auth()->id(),
                'subtotal'               => $request->subtotal,
                'descuento'              => $descuento,
                'envio'                  => $envio,
                'iva'                    => $iva,

                'total_original'         => $totalOriginal,
                'tradein_total'          => $tradeinTotal,
                'total_neto'             => $totalNeto,
                'total'                  => $totalOriginal, // 👉 siempre total original

                'plan'                   => $request->plan,
                'detalle_financiamiento' => null,
                'carta_garantia_id'      => $request->carta_garantia_id,
                'meses_garantia'         => $request->meses_garantia,
            ]);

            Log::info('Venta creada:', ['venta_id' => $venta->id]);

            // =============================
            // 1.1) Guardar TRADE-INS
            // =============================
            if (!empty($tradeins)) {
                foreach ($tradeins as $t) {
                    $valorAcuenta = (float) ($t['valor_a_cuenta'] ?? $t['valor'] ?? 0);

                    VentaTradein::create([
                        'venta_id'       => $venta->id,
                        'tipo_equipo'    => $t['tipo_equipo'] ?? null,
                        'descripcion'    => $t['descripcion']   ?? null,
                        'marca'          => $t['marca']         ?? null,
                        'modelo'         => $t['modelo']        ?? null,
                        'numero_serie'   => $t['numero_serie']  ?? null,
                        'valor_a_cuenta' => max($valorAcuenta, 0),
                        'observaciones'  => $t['observaciones'] ?? null,
                    ]);
                }
            }

            // =============================
            // 1.2) Registrar trade-in como PAGO APROBADO
            // =============================
            if ($tradeinTotal > 0) {
                Pago::create([
                    'venta_id'     => $venta->id,
                    'monto'        => $tradeinTotal,
                    'fecha_pago'   => Carbon::today(),
                    'metodo_pago'  => 'trade-in',
                    'aprobado'     => true,   // ✅ ya entra como autorizado
                    'es_anticipo'  => false,  // es trade-in, no anticipo
                ]);
            }

            // =============================
            // 2) Procesar productos  ✅ is_regalo
            // =============================
            $productos = json_decode($request->productos_json, true);
            Log::info('Productos recibidos:', $productos);

            foreach ($productos as $p) {
                Log::info('Procesando producto:', $p);

                $producto = Producto::find($p['producto_id']);
                if (!$producto) {
                    Log::warning('Producto no encontrado:', ['id' => $p['producto_id']]);
                    continue;
                }

                $series = $p['registro_id'] ?? null;

                // ✅ NUEVO: bandera regalo por renglón
                $isRegalo = (bool) ($p['is_regalo'] ?? false);

                if (is_array($series) && count($series) > 0) {
                    foreach ($series as $serieId) {
                        VentaProducto::create([
                            'venta_id'        => $venta->id,
                            'producto_id'     => $producto->id,
                            'cantidad'        => 1,
                            'precio_unitario' => $p['precio_unitario'],
                            'subtotal'        => $p['subtotal'] / max(count($series), 1),
                            'sobreprecio'     => $p['sobreprecio'] / max(count($series), 1),
                            'registro_id'     => $serieId,

                            // ✅ NUEVO
                            'is_regalo'       => $isRegalo,
                        ]);

                        Log::info("VentaProducto creado para serie {$serieId}.");

                        $registro = Registro::find($serieId);
                        if ($registro) {
                            $registro->estado_proceso = 'vendido';
                            $registro->save();

                            try {
                                \App\Models\ProcesoEquipo::create([
                                    'registro_id'         => $serieId,
                                    'tipo_proceso'        => 'vendido',
                                    'descripcion_proceso' => "Equipo vendido (venta #{$venta->id})",
                                ]);
                            } catch (\Exception $e) {
                                Log::error("Error al crear ProcesoEquipo para registro {$serieId}: {$e->getMessage()}");
                            }
                        }
                    }
                } else {
                    VentaProducto::create([
                        'venta_id'        => $venta->id,
                        'producto_id'     => $producto->id,
                        'cantidad'        => $p['cantidad'],
                        'precio_unitario' => $p['precio_unitario'],
                        'subtotal'        => $p['subtotal'],
                        'sobreprecio'     => $p['sobreprecio'],
                        'registro_id'     => null,

                        // ✅ NUEVO
                        'is_regalo'       => $isRegalo,
                    ]);
                }
            }

            // =============================
            // 3) Procesar pagos planeados (plan)
            // =============================
            if ($request->filled('pagos_json')) {
                $pagos = json_decode($request->input('pagos_json'), true);
                Log::info('Pagos recibidos:', $pagos);

                foreach ($pagos as $pago) {
                    PagoFinanciamiento::create([
                        'venta_id'        => $venta->id,
                        'descripcion'     => $pago['descripcion'] ?? '',
                        'fecha_pago'      => Carbon::parse($pago['mes']),
                        'monto'           => $pago['cuota'] ?? 0,
                        'monto_pendiente' => $pago['cuota'] ?? 0,
                        'pagado'          => false,
                    ]);
                }
            }

            // =============================
            // 4) Anticipo REAL
            // =============================
            $anticipoMonto  = (float) ($request->anticipo_monto ?? 0);
            $anticipoFecha  = $request->anticipo_fecha
                ? Carbon::parse($request->anticipo_fecha)
                : Carbon::today();
            $anticipoMetodo = $request->anticipo_metodo ?: ($request->plan === 'contado' ? 'anticipo_contado' : 'anticipo');

            if ($anticipoMonto > 0) {
                Pago::create([
                    'venta_id'     => $venta->id,
                    'monto'        => $anticipoMonto,
                    'fecha_pago'   => $anticipoFecha,
                    'metodo_pago'  => $anticipoMetodo,
                    'aprobado'     => true,  // ✅ autorizado desde el inicio
                    'es_anticipo'  => true,  // ✅ identificamos que ESTE es el anticipo
                ]);
            }

            // =============================
            // 5) Aplicar crédito inicial a calendario
            //    (Trade-in + Anticipo)
            // =============================
            $this->aplicarCreditoInicial($venta, $anticipoMonto, $tradeinTotal);

            DB::commit();

            return redirect()
                ->route('ventas.show', $venta->id)
                ->with('success', 'Venta guardada exitosamente.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('VENTA_STORE_FAIL', ['e' => $e->getMessage()]);
            return back()->with('error', 'No se pudo guardar la venta: ' . $e->getMessage());
        }
    }

    public function index()
    {
        $ventas = Venta::with(['cliente', 'usuario'])
            ->latest('created_at')
            ->get();

        return view('venta.index', compact('ventas'));
    }

   public function update(Request $request, $id)
{
    $request->validate([
        'cliente_id'        => 'required|exists:clientes,id',
        'lugar'             => 'required|string|max:255',
        'nota'              => 'nullable|string',
        'subtotal'          => 'required|numeric',
        'descuento'         => 'nullable|numeric',
        'envio'             => 'nullable|numeric',
        'iva'               => 'nullable|numeric',
        'total'             => 'required|numeric', // total original
        'plan'              => 'nullable|string|max:255',
        'productos_json'    => 'required|json',
        'tradeins_json'     => 'nullable|json',
        'pagos_json'        => 'nullable|json',
        'carta_garantia_id' => 'nullable|exists:carta_garantias,id',
        'meses_garantia'    => 'required|integer|in:6,9,12,15,18',

        // ✅ NUEVO: anticipo en EDIT también
        'anticipo_monto'    => 'nullable|numeric|min:0',
        'anticipo_fecha'    => 'nullable|date',
        'anticipo_metodo'   => 'nullable|string|max:255',
    ]);

    $venta = Venta::findOrFail($id);

    // Normalizar 0s
    $descuento = ($request->descuento === null || $request->descuento === '') ? 0 : (float) $request->descuento;
    $envio     = ($request->envio === null || $request->envio === '') ? 0 : (float) $request->envio;
    $iva       = ($request->iva === null || $request->iva === '') ? 0 : (float) $request->iva;

    DB::beginTransaction();

    try {
        // =============================
        // 1) Recalcular trade-ins
        // =============================
        $tradeins     = [];
        $tradeinTotal = 0;

        if ($request->filled('tradeins_json')) {
            $tradeins = json_decode($request->tradeins_json, true) ?: [];
            foreach ($tradeins as $t) {
                $valorAcuenta  = (float) ($t['valor_a_cuenta'] ?? $t['valor'] ?? 0);
                $tradeinTotal += max($valorAcuenta, 0);
            }
        } else {
            $tradeinTotal = (float) ($venta->tradein_total ?? 0);
        }

        $totalOriginal = (float) $request->total;
        $totalNeto     = max($totalOriginal - $tradeinTotal, 0);

        // =============================
        // 2) UPDATE VENTA
        // =============================
        $venta->update([
            'cliente_id'        => $request->cliente_id,
            'lugar'             => $request->lugar,
            'nota'              => $request->nota,
            'subtotal'          => (float) $request->subtotal,
            'descuento'         => $descuento,
            'envio'             => $envio,
            'iva'               => $iva,

            'total_original'    => $totalOriginal,
            'tradein_total'     => $tradeinTotal,
            'total_neto'        => $totalNeto,
            'total'             => $totalOriginal,

            'plan'              => $request->plan,

            'carta_garantia_id' => $request->carta_garantia_id,
            'meses_garantia'    => $request->meses_garantia,
        ]);

        // =============================
        // 3) Reemplazar TRADE-INS (si vienen)
        // =============================
        if ($request->filled('tradeins_json')) {
            $venta->tradeins()->delete();

            foreach ($tradeins as $t) {
                $valorAcuenta = (float) ($t['valor_a_cuenta'] ?? $t['valor'] ?? 0);

                VentaTradein::create([
                    'venta_id'       => $venta->id,
                    'tipo_equipo'    => $t['tipo_equipo'] ?? null,
                    'descripcion'    => $t['descripcion'] ?? null,
                    'marca'          => $t['marca'] ?? null,
                    'modelo'         => $t['modelo'] ?? null,
                    'numero_serie'   => $t['numero_serie'] ?? null,
                    'valor_a_cuenta' => max($valorAcuenta, 0),
                    'observaciones'  => $t['observaciones'] ?? null,
                ]);
            }
        }

        // =============================
        // 3.1) ✅ SINCRONIZAR PAGO "TRADE-IN" (tabla pagos)
        // =============================
        // Si antes había trade-in y ahora no, lo elimina.
        // Si hay trade-in, lo crea o actualiza.
        $pagoTrade = Pago::where('venta_id', $venta->id)
            ->where(function($q){
                $q->where('metodo_pago', 'trade-in')
                  ->orWhere('metodo_pago', 'trade in')
                  ->orWhere('metodo_pago', 'tradein');
            })
            ->orderByDesc('fecha_pago')
            ->first();

        if ($tradeinTotal > 0) {
            if ($pagoTrade) {
                $pagoTrade->update([
                    'monto'       => $tradeinTotal,
                    'aprobado'    => true,
                    'es_anticipo' => false,
                ]);
            } else {
                Pago::create([
                    'venta_id'     => $venta->id,
                    'monto'        => $tradeinTotal,
                    'fecha_pago'   => Carbon::today(),
                    'metodo_pago'  => 'trade-in',
                    'aprobado'     => true,
                    'es_anticipo'  => false,
                ]);
            }
        } else {
            if ($pagoTrade) $pagoTrade->delete();
        }

        // =============================
        // 4) ✅ ANTICIPO REAL (tabla pagos) — UPSERT
        // =============================
        $anticipoMonto  = (float) ($request->anticipo_monto ?? 0);
        $anticipoFecha  = $request->anticipo_fecha ? Carbon::parse($request->anticipo_fecha) : Carbon::today();
        $anticipoMetodo = $request->anticipo_metodo ?: (($request->plan === 'contado') ? 'anticipo_contado' : 'anticipo');

        $pagoAnticipo = Pago::where('venta_id', $venta->id)
            ->where('es_anticipo', true)
            ->orderByDesc('fecha_pago')
            ->first();

        if ($anticipoMonto > 0) {
            if ($pagoAnticipo) {
                $pagoAnticipo->update([
                    'monto'        => $anticipoMonto,
                    'fecha_pago'   => $anticipoFecha,
                    'metodo_pago'  => $anticipoMetodo,
                    'aprobado'     => true,
                    'es_anticipo'  => true,
                ]);
            } else {
                Pago::create([
                    'venta_id'     => $venta->id,
                    'monto'        => $anticipoMonto,
                    'fecha_pago'   => $anticipoFecha,
                    'metodo_pago'  => $anticipoMetodo,
                    'aprobado'     => true,
                    'es_anticipo'  => true,
                ]);
            }
        } else {
            // si en edit lo pusieron en 0, lo quitamos
            if ($pagoAnticipo) $pagoAnticipo->delete();
        }

        // =============================
        // 5) Regresar series antiguas a stock (igual que tú)
        // =============================
        $oldSerieIds = VentaProducto::where('venta_id', $venta->id)
            ->whereNotNull('registro_id')
            ->pluck('registro_id')
            ->unique()
            ->values()
            ->all();

        foreach ($oldSerieIds as $rid) {
            $usadaEnOtra = VentaProducto::where('registro_id', $rid)
                ->where('venta_id', '!=', $venta->id)
                ->exists();
            if ($usadaEnOtra) continue;

            $registro = Registro::find($rid);
            if ($registro) {
                if (Schema::hasColumn('registros', 'estado_actual')) $registro->estado_actual = 1;
                if (Schema::hasColumn('registros', 'estado_proceso')) $registro->estado_proceso = 'stock';
                $registro->save();
            }
        }

        // =============================
        // 6) Eliminar productos anteriores
        // =============================
        VentaProducto::where('venta_id', $venta->id)->delete();

        // =============================
        // 7) Guardar productos nuevos ✅ is_regalo (igual que tú)
        // =============================
        $productos = json_decode($request->productos_json, true) ?: [];

        foreach ($productos as $p) {
            $productoId = (int) ($p['producto_id'] ?? 0);
            if ($productoId <= 0) throw new \RuntimeException('Falta producto_id en uno de los productos.');

            $producto = Producto::find($productoId);
            if (!$producto) throw new \RuntimeException('Producto no encontrado: ' . $productoId);

            $cantidad = max(1, (int) ($p['cantidad'] ?? 1));
            $pu       = (float) ($p['precio_unitario'] ?? 0);
            $sp       = (float) ($p['sobreprecio'] ?? 0);
            $subRow   = (float) ($p['subtotal'] ?? (($pu + $sp) * $cantidad));

            $series   = $p['registro_id'] ?? null;
            $isRegalo = (bool) ($p['is_regalo'] ?? false);

            if (is_string($series)) {
                $tmp = json_decode($series, true);
                if (json_last_error() === JSON_ERROR_NONE) $series = $tmp;
            }

            if (is_array($series)) {
                $series = array_values(array_filter($series, fn ($v) => !is_null($v) && $v !== ''));
            } else {
                $series = [];
            }

            if (count($series) > 0) {
                foreach ($series as $serieId) {
                    VentaProducto::create([
                        'venta_id'        => $venta->id,
                        'producto_id'     => $producto->id,
                        'cantidad'        => 1,
                        'precio_unitario' => $pu,
                        'sobreprecio'     => $sp,
                        'subtotal'        => ($pu + $sp),
                        'registro_id'     => (int) $serieId,
                        'is_regalo'       => $isRegalo,
                    ]);

                    $registro = Registro::find($serieId);
                    if ($registro) {
                        if (Schema::hasColumn('registros', 'estado_actual')) $registro->estado_actual = 2;
                        if (Schema::hasColumn('registros', 'estado_proceso')) $registro->estado_proceso = 'vendido';
                        $registro->save();

                        try {
                            \App\Models\ProcesoEquipo::create([
                                'registro_id'         => (int) $serieId,
                                'tipo_proceso'        => 'vendido',
                                'descripcion_proceso' => "Equipo vendido (venta #{$venta->id})",
                            ]);
                        } catch (\Throwable $e) {
                            Log::error("Error ProcesoEquipo registro {$serieId}: " . $e->getMessage());
                        }
                    }
                }
            } else {
                VentaProducto::create([
                    'venta_id'        => $venta->id,
                    'producto_id'     => $producto->id,
                    'cantidad'        => $cantidad,
                    'precio_unitario' => $pu,
                    'sobreprecio'     => $sp,
                    'subtotal'        => $subRow,
                    'registro_id'     => null,
                    'is_regalo'       => $isRegalo,
                ]);
            }
        }

        // =============================
        // 8) Pagos planeados (pagos_json) (igual que tú)
        // =============================
        if ($request->has('pagos_json')) {
            $pagosArr = json_decode($request->pagos_json, true);

            if (is_array($pagosArr)) {
                PagoFinanciamiento::where('venta_id', $venta->id)
                    ->where(function ($q) {
                        $q->whereNull('pagado')->orWhere('pagado', false);
                    })
                    ->delete();

                foreach ($pagosArr as $pago) {
                    $cuota = (float) ($pago['cuota'] ?? 0);
                    if ($cuota <= 0) continue;

                    try {
                        $fecha = !empty($pago['mes']) ? Carbon::parse($pago['mes']) : $venta->created_at;
                    } catch (\Throwable $e) {
                        $fecha = $venta->created_at;
                    }

                    PagoFinanciamiento::create([
                        'venta_id'        => $venta->id,
                        'descripcion'     => $pago['descripcion'] ?? '',
                        'fecha_pago'      => $fecha,
                        'monto'           => $cuota,
                        'monto_pendiente' => $cuota,
                        'pagado'          => false,
                    ]);
                }
            }
        }

        // =============================
        // 9) ✅ Re-aplicar crédito inicial al calendario
        // =============================
        $this->aplicarCreditoInicial($venta->fresh(), $anticipoMonto, $tradeinTotal);

        DB::commit();

        return redirect()
            ->route('ventas.show', $venta->id)
            ->with('success', 'Venta actualizada correctamente.');
    } catch (\Throwable $e) {
        DB::rollBack();
        Log::error('VENTA_UPDATE_FAIL', ['venta' => $id, 'e' => $e->getMessage()]);
        return back()->with('error', 'No se pudo actualizar: ' . $e->getMessage());
    }
}
    /** PDF principal (remisión + carta, si existe) */
    public function pdf(Venta $venta)
    {
        try {
            $venta->load([
                'cliente',
                'usuario',
                'productos.producto',
                'pagos',
                'pagosReales',
                'tradeins',
                'cartaGarantia'
            ]);

            $pagos = $venta->pagos ?? collect();

            $pagosRealesAprobados = ($venta->pagosReales ?? collect())->filter(fn ($p) => $p->aprobado);
            $totalPagado = (float) $pagosRealesAprobados->sum('monto');

            $url = route('ventas.show', $venta->id);
            $qr = base64_encode(
                QrCode::format('svg')->size(120)->generate($url)
            );

            $plan = $venta->plan;
            $anticipo = $venta->pagosReales
             ?->filter(fn($p) => $p->aprobado && ($p->es_anticipo ?? false))
             ->sum('monto') ?? 0;

            $pdfVenta = PDF::loadView('venta.pdf', compact('venta', 'qr', 'pagos', 'totalPagado', 'plan','anticipo'))
                ->setPaper('a4', 'portrait');

            $dirTemp = storage_path("app/public/temp");
            if (!File::isDirectory($dirTemp)) {
                File::makeDirectory($dirTemp, 0775, true);
            }

            $rutaVenta = "{$dirTemp}/venta_{$venta->id}.pdf";
            file_put_contents($rutaVenta, $pdfVenta->output());

            if (!file_exists($rutaVenta)) {
                throw new \RuntimeException("No se generó el PDF base de la remisión.");
            }

            $rutaCarta = $venta->cartaGarantia?->archivo
                ? storage_path("app/public/" . $venta->cartaGarantia->archivo)
                : null;

            if (!$rutaCarta || !file_exists($rutaCarta)) {
                $clienteNombre = preg_replace('/[^A-Za-z0-9 _-]/', '', (string) ($venta->cliente->nombre ?? 'Cliente'));
                $nombreArchivo = "Remision_{$venta->id}_{$clienteNombre}.pdf";

                return response()->download($rutaVenta, $nombreArchivo, [
                    'Content-Type'           => 'application/pdf',
                    'X-Content-Type-Options' => 'nosniff',
                ]);
            }

            $pdf = new Fpdi();
            $archivos = [$rutaVenta, $rutaCarta];

            foreach ($archivos as $archivo) {
                $pageCount = $pdf->setSourceFile($archivo);
                for ($i = 1; $i <= $pageCount; $i++) {
                    $tpl = $pdf->importPage($i);
                    $size = $pdf->getTemplateSize($tpl);
                    $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                    $pdf->useTemplate($tpl);
                }
            }

            $rutaFinal = "{$dirTemp}/final_venta_{$venta->id}.pdf";
            $pdf->Output($rutaFinal, 'F');

            if (!file_exists($rutaFinal)) {
                throw new \RuntimeException("El archivo PDF final NO se generó");
            }

            $clienteNombre = preg_replace('/[^A-Za-z0-9 _-]/', '', (string) ($venta->cliente->nombre ?? 'Cliente'));
            $nombreArchivo = "Remision_{$venta->id}_{$clienteNombre}.pdf";

            return response()->download($rutaFinal, $nombreArchivo, [
                'Content-Type'           => 'application/pdf',
                'X-Content-Type-Options' => 'nosniff',
            ]);
        } catch (\Throwable $e) {
            Log::error('VENTA_PDF_FAIL', ['venta' => $venta->id, 'e' => $e->getMessage()]);
            return back()->with('error', 'No se pudo generar el PDF: ' . $e->getMessage());
        }
    }

public function edit($id)
{
    $venta = Venta::with([
        'productos.producto',
        'cliente',
        'pagoFinanciamiento',
        'tradeins',
        'pagosReales', // ✅ para poder leer anticipo
    ])->findOrFail($id);

    // ✅ Anticipo real (tabla pagos) para precargar en el edit
    $anticipo = Pago::where('venta_id', $venta->id)
        ->where('es_anticipo', true)
        ->orderByDesc('fecha_pago')
        ->first();

    $clientes  = Cliente::all();
    $productos = Producto::all();
    $paquetes  = Paquete::all();
    $cartas    = CartaGarantia::all();
    $registros = Registro::where('estado_actual', 'stock')->get();

    return view('venta.edit', compact(
        'venta', 'clientes', 'productos', 'paquetes', 'cartas', 'registros', 'anticipo'
    ));
}

    public function search(Request $request)
    {
        $query = $request->input('search');

        $paquetes = Paquete::with('productos')
            ->when($query, function ($q) use ($query) {
                $q->where('nombre', 'like', '%' . $query . '%');
            })
            ->get();

        return response()->json($paquetes);
    }

    /** Construye remisión (vista alternativa) + carta (opcional) */
    private function buildRemisionPdf(Venta $venta, string $bladeView, string $prefix = 'final_alt_'): string
    {
        $venta->load([
            'cliente',
            'usuario',
            'productos.producto',
            'pagos',
            'pagosReales',
            'tradeins',
            'cartaGarantia'
        ]);

        $pagos = $venta->pagos ?? collect();

        $pagosRealesAprobados = ($venta->pagosReales ?? collect())->filter(fn ($p) => $p->aprobado);
        $totalPagado = (float) $pagosRealesAprobados->sum('monto');

        $plan = $venta->plan;

        $url = route('ventas.show', $venta->id);
        $qr  = base64_encode(QrCode::format('svg')->size(120)->generate($url));

        $dirTemp = storage_path('app/public/temp');
        if (!File::isDirectory($dirTemp)) {
            File::makeDirectory($dirTemp, 0775, true);
        }

        $rutaVenta = "{$dirTemp}/venta_alt_{$venta->id}.pdf";
        $pdfVenta  = PDF::loadView($bladeView, compact('venta', 'qr', 'pagos', 'totalPagado', 'plan'))
            ->setPaper('a4', 'portrait');
        file_put_contents($rutaVenta, $pdfVenta->output());

        if (!is_file($rutaVenta)) {
            throw new \RuntimeException("No se pudo generar el PDF base de la remisión ({$bladeView}).");
        }

        $rutaCarta = $venta->cartaGarantia?->archivo
            ? storage_path('app/public/' . $venta->cartaGarantia->archivo)
            : null;

        $pdf = new Fpdi();
        $archivos = [$rutaVenta];
        if ($rutaCarta && is_file($rutaCarta)) $archivos[] = $rutaCarta;

        foreach ($archivos as $archivo) {
            $pageCount = $pdf->setSourceFile($archivo);
            for ($i = 1; $i <= $pageCount; $i++) {
                $tpl  = $pdf->importPage($i);
                $size = $pdf->getTemplateSize($tpl);
                $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                $pdf->useTemplate($tpl);
            }
        }

        $rutaFinal = "{$dirTemp}/{$prefix}venta_{$venta->id}.pdf";
        $pdf->Output($rutaFinal, 'F');

        if (!is_file($rutaFinal)) {
            throw new \RuntimeException("No se pudo generar el PDF final concatenado.");
        }

        return $rutaFinal;
    }

    public function pdfAlt(Venta $venta)
    {
        try {
            $venta->load([
                'cliente',
                'usuario',
                'productos.producto',
                'pagos',
                'pagosReales',
                'tradeins'
            ]);

            $pagos = $venta->pagos ?? collect();

            $pagosRealesAprobados = ($venta->pagosReales ?? collect())->filter(fn ($p) => $p->aprobado);
            $totalPagado = (float) $pagosRealesAprobados->sum('monto');

            $plan = $venta->plan;

            $urlShow = route('ventas.show', $venta->id);
            $qr      = base64_encode(QrCode::format('svg')->size(120)->generate($urlShow));

            $docCode = 'RM-' . $venta->id . '-' . now()->format('Ymd') . '-' . Str::upper(Str::random(4));
            $ackUrl  = $urlShow . '?ack=' . $docCode;
            $qrAck   = base64_encode(QrCode::format('svg')->size(100)->generate($ackUrl));

            $pdf = PDF::loadView('venta.pdf_alt', compact(
                'venta', 'qr', 'pagos', 'totalPagado', 'plan', 'docCode', 'qrAck', 'ackUrl'
            ))->setPaper('a4', 'portrait');

            $raw = $pdf->output();
            if (strncmp($raw, '%PDF-', 5) !== 0) {
                throw new \RuntimeException('La salida generada no es un PDF válido.');
            }

            $clienteNombre = preg_replace('/[^A-Za-z0-9 _-]/', '', (string) ($venta->cliente->nombre ?? 'Cliente'));
            $filename = "Remision_Alt_{$venta->id}_{$clienteNombre}.pdf";

            return response($raw, 200, [
                'Content-Type'           => 'application/pdf',
                'Content-Disposition'    => 'attachment; filename="' . $filename . '"',
                'X-Content-Type-Options' => 'nosniff',
                'Cache-Control'          => 'no-store, no-cache, must-revalidate, max-age=0',
                'Pragma'                 => 'no-cache',
            ]);
        } catch (\Throwable $e) {
            Log::error('VENTA_PDF_ALT_FAIL', ['venta' => $venta->id, 'e' => $e->getMessage()]);
            return back()->with('error', 'No se pudo generar el PDF alterno: ' . $e->getMessage());
        }
    }

    public function previewPdfAlt(Venta $venta)
    {
        try {
            $rutaFinal = $this->buildRemisionPdf($venta, 'venta.pdf_alt', 'final_alt_');

            return response()->file($rutaFinal, [
                'Content-Type'        => 'application/pdf',
                'Content-Disposition' => 'inline; filename="preview_remision_alt_' . $venta->id . '.pdf"',
            ]);
        } catch (\Throwable $e) {
            Log::error('VENTA_PDF_ALT_PREVIEW_FAIL', ['venta' => $venta->id, 'e' => $e->getMessage()]);
            return back()->with('error', 'No se pudo previsualizar el PDF alterno. Revisa el log.');
        }
    }

public function storePago(Request $request, $ventaId)
{
    $request->validate([
        'monto'             => ['required','numeric','min:0.01'],
        'fecha_pago'        => ['required','date'],
        'metodo_pago'       => ['required','string','max:255'],
        'financiamiento_id' => ['nullable','integer'],
        'orden_id'          => ['nullable','integer'],
        'partes'            => ['nullable','array'],
        'partes.*.metodo'   => ['required_with:partes','string','max:255'],
        'partes.*.monto'    => ['required_with:partes','numeric','min:0.01'],

        // ✅ compatibilidad vieja
        'recibo'            => ['nullable','file','mimes:pdf,jpg,jpeg,png,webp,gif,bmp,svg,avif,heic,heif,tif,tiff','max:20480'],

        // ✅ nuevo: múltiples archivos
        'documentos'        => ['nullable','array'],
        'documentos.*'      => ['file','mimes:pdf,jpg,jpeg,png,webp,gif,bmp,svg,avif,heic,heif,tif,tiff','max:20480'],
    ]);

    $venta = Venta::findOrFail($ventaId);

    // ✅ 1) Buscar cuota por ID
    $financiamiento = null;

    if ($request->filled('financiamiento_id')) {
        $financiamiento = PagoFinanciamiento::where('id', (int)$request->financiamiento_id)
            ->where('venta_id', (int)$ventaId)
            ->first();
    }

    // ✅ 2) Fallback por monto + fecha
    if (!$financiamiento) {
        $financiamiento = PagoFinanciamiento::where('venta_id', (int)$ventaId)
            ->where('monto', $request->monto)
            ->whereDate('fecha_pago', $request->fecha_pago)
            ->first();
    }

    // ✅ 3) Si hay cuota: actualizar fecha programada a la fecha real capturada
    if ($financiamiento) {
        $financiamiento->fecha_pago = $request->fecha_pago;
        $financiamiento->save();
    }

    // =========================
    // ✅ PAGO MIXTO (opcional)
    // =========================
    $detalleMetodos = null;

    if (is_array($request->partes) && count($request->partes) > 0) {
        $partesLimpias = collect($request->partes)
            ->map(function ($p) {
                return [
                    'metodo' => trim((string)($p['metodo'] ?? '')),
                    'monto'  => (float)($p['monto'] ?? 0),
                ];
            })
            ->filter(fn($p) => $p['metodo'] !== '' && $p['monto'] > 0)
            ->values();

        $sumaPartes = (float) $partesLimpias->sum('monto');
        $montoTotal = (float) $request->monto;

        if (abs($sumaPartes - $montoTotal) > 0.01) {
            return back()
                ->withInput()
                ->with('error', 'La suma del pago mixto no coincide con el monto recibido.');
        }

        $detalleMetodos = $partesLimpias->all();

        // ✅ Si viene mixto, forzamos metodo_pago = Mixto
        $request->merge(['metodo_pago' => 'Mixto']);
    }

    try {
        DB::beginTransaction();
         $this->cambiarPlanContadoAPersonalizadoVenta($venta);

        $pago = new Pago();
        $pago->venta_id    = $venta->id;
        $pago->orden_id    = $request->filled('orden_id') ? (int)$request->orden_id : null;
        $pago->monto       = (float)$request->monto;
        $pago->fecha_pago  = $request->fecha_pago;
        $pago->metodo_pago = $request->metodo_pago;

        if ($detalleMetodos) {
            $pago->detalle_metodos = $detalleMetodos;
        }

        // ✅ Auto-aprobados
        $metodo = Str::lower((string)$request->metodo_pago);
        $autoAprobados = ['trade-in', 'trade in', 'anticipo', 'anticipo_contado', 'anticipo contado'];

        $pago->aprobado    = in_array($metodo, $autoAprobados, true);
        $pago->es_anticipo = in_array($metodo, ['anticipo', 'anticipo_contado', 'anticipo contado'], true);

        // ✅ Si es mixto
        if ($metodo === 'mixto' && $detalleMetodos) {
            $auto = collect($detalleMetodos)->every(function ($p) {
                $m = Str::lower((string)($p['metodo'] ?? ''));
                return in_array($m, ['trade-in', 'trade in', 'anticipo', 'anticipo_contado', 'anticipo contado'], true);
            });

            $pago->aprobado = $auto;

            $pago->es_anticipo = collect($detalleMetodos)->contains(function ($p) {
                $m = Str::lower((string)($p['metodo'] ?? ''));
                return in_array($m, ['anticipo', 'anticipo_contado', 'anticipo contado'], true);
            });
        }

        if ($financiamiento) {
            $pago->financiamiento_id = $financiamiento->id;
        }

        $pago->save();

        // ✅ Guardar múltiples evidencias
        $this->guardarEvidenciasPago($request, $pago);

        // ✅ Si coincidió y está aprobado, marcar cuota pagada automáticamente
        if ($financiamiento && $pago->aprobado) {
            $financiamiento->monto_pendiente = 0;
            $financiamiento->pagado = true;
            $financiamiento->save();
        }

        DB::commit();

        return redirect()->route('ventas.pagos.index', $venta->id)
            ->with('success', 'Pago registrado correctamente.');
    } catch (\Throwable $e) {
        DB::rollBack();

        Log::error('VENTA_STORE_PAGO_FAIL', [
            'venta_id' => $ventaId,
            'error'    => $e->getMessage(),
        ]);

        return back()->withInput()->with('error', 'No se pudo registrar el pago.');
    }
}
    public function show(Venta $venta)
    {
       $venta->load([
    'cliente',
    'productos.producto',
    'usuario',
    'cartaGarantia',
    'remision',
    'tradeins',
    'pagosReales.documentos',
]);

$pagos = PagoFinanciamiento::with(['pago.documentos'])
    ->where('venta_id', $venta->id)
    ->orderBy('fecha_pago')
    ->get();

        return view('venta.show', compact('venta', 'pagos'));
    }

    public function indexPagos($ventaId)
    {
        $venta = Venta::with('cliente')->findOrFail($ventaId);

$pagos = Pago::with('documentos')
    ->where('venta_id', $ventaId)
    ->orderBy('fecha_pago')
    ->get();

        $financiamientos = PagoFinanciamiento::where('venta_id', $ventaId)->get();

        // Totales calculados con pagos APROBADOS
        $totalVenta    = (float) ($venta->total_original ?? $venta->total ?? 0); // 👉 total de catálogo
        $totalPagado   = (float) $pagos->where('aprobado', true)->sum('monto');
        $saldoRestante = max(0, $totalVenta - $totalPagado);
        $progreso      = $totalVenta > 0
            ? round(($totalPagado / $totalVenta) * 100, 2)
            : 0;

        return view('venta.pagos', compact(
            'venta',
            'pagos',
            'financiamientos',
            'totalVenta',
            'totalPagado',
            'saldoRestante',
            'progreso'
        ));
    }

    public function reciboPago($pagoId)
    {
        $pago = Pago::with('venta.cliente')->findOrFail($pagoId);

        $pdf = PDF::loadView('venta.recibo', compact('pago'))
            ->setPaper('a6', 'portrait');

        return $pdf->stream("recibo_pago_{$pago->id}.pdf");
    }

    public function reciboFinal(Venta $venta)
    {
        $pendientes = PagoFinanciamiento::where('venta_id', $venta->id)
            ->where('pagado', false)
            ->count();

        if ($pendientes > 0) {
            return redirect()->back()->with('error', 'Aún hay pagos pendientes.');
        }

        $venta->load(['cliente', 'productos.producto']);

        $totalPagado = Pago::where('venta_id', $venta->id)->sum('monto');

        $pdf = PDF::loadView('venta.recibo_final', [
            'venta'       => $venta,
            'totalPagado' => $totalPagado,
        ])->setPaper('a5', 'portrait');

        return $pdf->stream("recibo_final_venta_{$venta->id}.pdf");
    }

    public function marcarPagado(Request $request, $pagoFinanciamientoId)
    {
        // Esta ruta sigue existiendo por si quieres un flujo manual con PIN.

        $pinIngresado = $request->input('pin');
        $pinCorrecto  = env('APROBACION_PIN');

        if ($pinIngresado !== $pinCorrecto) {
            return redirect()->back()->with('error', 'PIN inválido. Intenta nuevamente.');
        }

        $financiamiento = PagoFinanciamiento::findOrFail($pagoFinanciamientoId);
        $financiamiento->pagado = true;
        $financiamiento->monto_pendiente = 0;
        $financiamiento->save();

        $pago = Pago::where('financiamiento_id', $financiamiento->id)->first();
        if ($pago) {
            $pago->aprobado = true;
            $pago->save();
        }

        return redirect()->back()->with('success', 'Pago aprobado correctamente.');
    }

    public function deudores()
    {
        setlocale(LC_TIME, 'es_ES.UTF-8', 'es_ES', 'spanish');
        Carbon::setLocale('es');

        function parsearFechaEnEsp($fechaTexto) {
            $fmt = new \IntlDateFormatter('es_ES', \IntlDateFormatter::FULL, \IntlDateFormatter::NONE);
            $fmt->setPattern("d 'de' MMMM 'de' y");
            $timestamp = $fmt->parse($fechaTexto);
            return $timestamp ? Carbon::createFromTimestamp($timestamp) : null;
        }

        // ============================
        // ✅ VENTAS (igual que tú)
        // ============================
        $ventas = Venta::with([
                'cliente',
                'pagos',
                'productos.producto',
                'productos.registro',
                'pagosFinanciamiento',
                'tradeins',
                'pagosReales',
            ])
            ->orderBy('created_at', 'desc')
            ->get();

        foreach ($ventas as $v) {
            if (!empty($v->detalle_financiamiento['fecha']) && is_string($v->detalle_financiamiento['fecha'])) {
                try {
                    $fechaTexto = $v->detalle_financiamiento['fecha'];
                    $fechaCarbon = parsearFechaEnEsp($fechaTexto);
                    $v->detalle_financiamiento['fecha_carbon'] = $fechaCarbon;
                } catch (\Exception $e) {
                    logger()->error("Error al parsear fecha: $fechaTexto", ['error' => $e->getMessage()]);
                    $v->detalle_financiamiento['fecha_carbon'] = null;
                }
            }
        }

        // ============================
        // ✅ ÓRDENES DE SERVICIO (OS)
        // ============================
        $ordenes = \App\Models\Orden::with([
                'cliente',
            ])
            ->orderBy('created_at', 'desc')
            ->get();

        $clientes = Cliente::orderBy('nombre')->get();

        // ✅ MUY IMPORTANTE: mandar $ordenes a la vista
        return view('venta.deudores', compact('ventas', 'clientes', 'ordenes'));
    }

    public function pendientes()
    {
        $ventas = DB::table('ventas')
            ->leftJoin('checklists', function ($join) {
                $join->on('ventas.id', '=', 'checklists.venta_id')
                    ->where('checklists.finalizado', 1);
            })
            ->join('clientes', 'ventas.cliente_id', '=', 'clientes.id')
            ->select(
                'ventas.id',
                'clientes.nombre as cliente',
                'ventas.created_at as fecha'
            )
            ->whereNull('checklists.id')
            ->groupBy('ventas.id', 'clientes.nombre', 'ventas.created_at')
            ->orderBy('ventas.created_at', 'desc')
            ->get();

        return view('venta.pendientes', compact('ventas'));
    }

    public function productos($ventaId)
    {
        $productos = DB::table('venta_productos')
            ->join('productos', 'venta_productos.producto_id', '=', 'productos.id')
            ->where('venta_productos.venta_id', $ventaId)
            ->select(
                'venta_productos.*',
                DB::raw("CONCAT(productos.tipo_equipo, ' ', productos.marca, ' ', productos.modelo) AS nombre_producto")
            )
            ->get();

        $venta = DB::table('ventas')->where('id', $ventaId)->first();

        return view('venta.productos', compact('venta', 'productos'));
    }
public function updatePagosFinanciamiento(Request $request, Venta $venta)
{
    $request->validate([
        'pagos_financiamiento' => ['nullable', 'array'],
        'pagos_financiamiento.*.descripcion' => ['nullable', 'string', 'max:255'],
        'pagos_financiamiento.*.fecha_pago' => ['nullable', 'date'],
        'pagos_financiamiento.*.monto' => ['nullable', 'numeric', 'min:0'],
        'pagos_financiamiento.*.monto_pendiente' => ['nullable', 'numeric', 'min:0'],
        'pagos_financiamiento.*.eliminar' => ['nullable'],
    ]);

    DB::beginTransaction();

    try {
        $seAgregoNuevoPago = false;

        if ($request->has('pagos_financiamiento')) {
            foreach ($request->pagos_financiamiento as $pagoId => $datos) {
                $eliminar = !empty($datos['eliminar']);

                if (Str::startsWith((string) $pagoId, 'nuevo_')) {
                    if ($eliminar) {
                        continue;
                    }

                    $descripcion = trim((string) ($datos['descripcion'] ?? ''));
                    $fechaPago = $datos['fecha_pago'] ?? null;
                    $monto = (float) ($datos['monto'] ?? 0);

                    if ($descripcion === '') {
                        $descripcion = 'Pago planeado';
                    }

                    if (empty($fechaPago) || $monto <= 0) {
                        continue;
                    }

                    PagoFinanciamiento::create([
                        'venta_id'        => $venta->id,
                        'fecha_pago'      => $fechaPago,
                        'monto'           => $monto,
                        'monto_pendiente' => $monto,
                        'descripcion'     => $descripcion,
                        'pagado'          => false,
                    ]);

                    $seAgregoNuevoPago = true;

                    continue;
                }

                $pago = PagoFinanciamiento::where('venta_id', $venta->id)
                    ->where('id', (int) $pagoId)
                    ->first();

                if (!$pago) {
                    continue;
                }

                if ($eliminar) {
                    $tienePagoAprobado = Pago::where('financiamiento_id', $pago->id)
                        ->where('aprobado', true)
                        ->exists();

                    if (!$tienePagoAprobado) {
                        Pago::where('financiamiento_id', $pago->id)
                            ->where('aprobado', false)
                            ->delete();

                        $pago->delete();
                    }

                    continue;
                }

                $monto = (float) ($datos['monto'] ?? $pago->monto);

                $tienePagoAprobado = Pago::where('financiamiento_id', $pago->id)
                    ->where('aprobado', true)
                    ->exists();

                $updateData = [
                    'fecha_pago'  => $datos['fecha_pago'] ?? $pago->fecha_pago,
                    'monto'       => $monto,
                    'descripcion' => $datos['descripcion'] ?? $pago->descripcion,
                ];

                if (!$tienePagoAprobado) {
                    $updateData['monto_pendiente'] = $monto;
                    $updateData['pagado'] = false;
                }

                $pago->update($updateData);
            }
        }

        if ($seAgregoNuevoPago) {
            $this->cambiarPlanContadoAPersonalizadoVenta($venta);
        }

        DB::commit();

        return redirect()
            ->back()
            ->with('success', 'Pagos actualizados correctamente. Si la venta era de contado, el plan cambió a personalizado.');
    } catch (\Throwable $e) {
        DB::rollBack();

        Log::error('VENTA_UPDATE_PAGOS_FINANCIAMIENTO_FAIL', [
            'venta_id' => $venta->id,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);

        return redirect()
            ->back()
            ->with('error', 'No se pudieron actualizar los pagos: ' . $e->getMessage());
    }
}

    /** Ver/Imprimir la etiqueta (stream del PDF) */
public function etiqueta(Venta $venta)
{
    // ✅ SIEMPRE regenerar la etiqueta para reflejar edición (productos eliminados, etc.)
    $checklist = $this->ensureChecklistAssets($venta, true);

    // label_path usualmente viene como: "storage/checklists/venta_1/label_4x8.pdf"
    $labelPath = (string)($checklist->label_path ?? '');

    if ($labelPath === '') {
        abort(404, 'No se encontró label_path en el checklist.');
    }

    // Normaliza slashes
    $labelPath = str_replace('\\', '/', $labelPath);

    // ✅ Caso típico: "storage/....pdf" o "/storage/....pdf"
    // Eso NO es una ruta de archivo real; es una URL pública.
    // La ruta real está en storage/app/public/...
    $abs = null;

    if (Str::startsWith($labelPath, '/')) {
        $labelPath = ltrim($labelPath, '/');
    }

    if (Str::startsWith($labelPath, 'storage/')) {
        // "storage/xxxxx.pdf" -> en disco public es "xxxxx.pdf"
        $relativeOnDisk = Str::after($labelPath, 'storage/');
        $abs = Storage::disk('public')->path($relativeOnDisk);
    } else {
        // Si alguien guardó ya una ruta relativa tipo "checklists/venta_1/label_4x8.pdf"
        // también la resolvemos en disco public.
        $abs = Storage::disk('public')->path($labelPath);
    }

    // ✅ Si aún no existe, hacemos fallback a public_path por compatibilidad
    if (!is_file($abs)) {
        $fallbackPublic = public_path($labelPath); // por si label_path ya venía como "storage/..."
        if (is_file($fallbackPublic)) {
            $abs = $fallbackPublic;
        }
    }

    // ✅ Si no existe, ya es error real: ensureChecklistAssets no lo generó o guardó en otro lado
    if (!is_file($abs)) {
        abort(404, "La etiqueta no existe. Esperado: {$abs} | label_path: {$checklist->label_path}");
    }

    // ✅ Evitar cache del navegador / proxy
    return response()->file($abs, [
        'Content-Type'              => 'application/pdf',
        'X-Content-Type-Options'    => 'nosniff',
        'Cache-Control'             => 'no-store, no-cache, must-revalidate, max-age=0',
        'Pragma'                    => 'no-cache',
        'Expires'                   => '0',
    ]);
}

    /**
     * Genera QR + etiqueta y guarda rutas en Checklist
     * @param bool $forceLabel  si true, regenera el PDF aunque ya exista
     */
    private function ensureChecklistAssets(Venta $venta, bool $forceLabel = false): Checklist
    {
        $checklist = Checklist::firstOrCreate(['venta_id' => $venta->id]);

        $url = route('checklists.wizard', $venta->id);

        // =============== QR (solo si falta) ===============
        $qrExists = !empty($checklist->qr_path) && file_exists(public_path($checklist->qr_path));
        if (!$qrExists) {
            $ext = 'png';
            try {
                $qrBinary = \QrCode::format('png')->size(380)->errorCorrection('H')->margin(1)->generate($url);
            } catch (\Throwable $e) {
                $qrBinary = \QrCode::format('svg')->size(380)->errorCorrection('H')->margin(1)->generate($url);
                $ext = 'svg';
            }

            $qrPath = "checklists/venta_{$venta->id}/qr.$ext";
            Storage::disk('public')->put($qrPath, $qrBinary);

            $checklist->qr_url  = $url;
            $checklist->qr_path = 'storage/' . $qrPath;
            $checklist->save();
        } else {
            // asegurar url actualizada
            if (empty($checklist->qr_url)) {
                $checklist->qr_url = $url;
                $checklist->save();
            }
        }

        // =============== Productos (SIEMPRE leer de DB) ===============
        $productos = DB::table('venta_productos')
            ->leftJoin('productos', 'venta_productos.producto_id', '=', 'productos.id')
            ->leftJoin('registros', 'venta_productos.registro_id', '=', 'registros.id')
            ->where('venta_productos.venta_id', $venta->id)
            ->select([
                'productos.tipo_equipo',
                'productos.marca',
                'productos.modelo',
                'registros.numero_serie',
                'venta_productos.accesorios',
                'venta_productos.notas',
            ])
            ->get();

        // =============== Etiqueta PDF (regenerar si force o si no existe) ===============
        $labelExists = !empty($checklist->label_path) && file_exists(public_path($checklist->label_path));
        if ($forceLabel || !$labelExists) {

            $pdf = PDF::loadView('checklists.label-4x8', [
                    'venta'     => $venta->loadMissing('cliente'), // por si acaso
                    'checklist' => $checklist,
                    'qr_path'   => $checklist->qr_path,
                    'qr_url'    => $checklist->qr_url,
                    'productos' => $productos, // ✅ la vista usa $productos
                ])
                ->setPaper([0, 0, 288, 576], 'portrait');

            // ✅ Nombre fijo (se sobreescribe) para que siempre sea la misma ruta
            $labelPath = "checklists/venta_{$venta->id}/label_4x8.pdf";
            Storage::disk('public')->put($labelPath, $pdf->output());

            $checklist->label_path = 'storage/' . $labelPath;
            $checklist->save();
        }

        return $checklist->fresh();
    }

    public function sendWhatsappTemplateRemision(Venta $venta, Request $request, WhatsAppService $wa)
    {
        $request->validate([
            'template_name' => ['nullable', 'string', 'max:128'],
            'template_lang' => ['nullable', 'string', 'max:12'],
        ]);

        $venta->load([
            'cliente',
            'usuario',
            'productos.producto',
            'cartaGarantia',
            'pagos',
            'pagosReales',
            'tradeins'
        ]);

        $to = WhatsAppService::normalizeMsisdn($venta->cliente->telefono ?? '');
        if (!$to) {
            return back()->with('wa_info', 'El cliente no tiene teléfono válido.');
        }

        try {
            $url = route('ventas.show', $venta->id);
            $qr  = base64_encode(QrCode::format('svg')->size(120)->generate($url));

            $pagos = $venta->pagos ?? collect();

            $pagosRealesAprobados = ($venta->pagosReales ?? collect())->filter(fn ($p) => $p->aprobado);
            $totalPagado = (float) $pagosRealesAprobados->sum('monto');

            $plan = $venta->plan;

            $dirTemp = storage_path('app/public/temp');
            if (!is_dir($dirTemp)) @mkdir($dirTemp, 0775, true);

            $rutaVenta = "{$dirTemp}/venta_{$venta->id}.pdf";
            $pdfVenta  = PDF::loadView('venta.pdf', compact('venta', 'qr', 'pagos', 'totalPagado', 'plan'))
                ->setPaper('a4', 'portrait');
            file_put_contents($rutaVenta, $pdfVenta->output());

            $rutaCarta = $venta->cartaGarantia?->archivo
                ? storage_path('app/public/' . $venta->cartaGarantia->archivo)
                : null;

            $pdf = new Fpdi();
            $archivos = [$rutaVenta];
            if ($rutaCarta && is_file($rutaCarta)) $archivos[] = $rutaCarta;

            foreach ($archivos as $archivo) {
                $pageCount = $pdf->setSourceFile($archivo);
                for ($i = 1; $i <= $pageCount; $i++) {
                    $tpl  = $pdf->importPage($i);
                    $size = $pdf->getTemplateSize($tpl);
                    $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                    $pdf->useTemplate($tpl);
                }
            }

            $rutaFinal = "{$dirTemp}/final_venta_{$venta->id}.pdf";
            $pdf->Output($rutaFinal, 'F');

            if (!is_file($rutaFinal)) {
                return back()->with('wa_info', 'No se pudo generar el PDF final de la remisión.');
            }

            $clienteNombre = preg_replace('/[^A-Za-z0-9 _-]/', '', (string) $venta->cliente->nombre);
            $filename = "Remision_{$venta->id}_{$clienteNombre}.pdf";
        } catch (\Throwable $e) {
            Log::error('VENTA_PDF_BUILD_FAIL', ['venta' => $venta->id, 'e' => $e->getMessage()]);
            return back()->with('wa_info', 'No se pudo generar el PDF de la remisión.');
        }

        $upload  = $wa->uploadMediaPath($rutaFinal, $filename, 'application/pdf');
        $uJson   = $upload->json();
        $mediaId = data_get($uJson, 'id');

        if (!$upload->successful() || !$mediaId) {
            Log::warning('WA_MEDIA_UPLOAD_FAIL', ['resp' => $uJson]);
            return back()->with('wa_info', 'No se pudo subir el PDF a WhatsApp.')
                ->with('wa_fail', [$uJson]);
        }

        $templateName  = (string) ($request->input('template_name') ?: 'doc_pdf_utility_v2');
        $langCode      = (string) ($request->input('template_lang') ?: ($wa->pickTemplateLanguage($templateName) ?? 'es_MX'));
        $clienteNombre = trim(($venta->cliente->nombre ?? '') . ' ' . ($venta->cliente->apellido ?? ''));

        $resp = $wa->sendTemplateWithDocument(
            to: $to,
            templateName: $templateName,
            langCode: $langCode,
            mediaId: $mediaId,
            filename: $filename,
            clienteNombre: $clienteNombre,
            frase: null,
            btn0UrlSuffix: null,
            btn1UrlSuffix: null
        );

        $json  = $resp->json();
        $wamid = data_get($json, 'messages.0.id');

        if ($resp->successful() && $wamid) {
            Log::info('WA_OK_TPL_REMISION', [
                'venta' => $venta->id, 'to' => $to, 'tpl' => $templateName, 'lang' => $langCode, 'wamid' => $wamid
            ]);
            return back()->with('wa_success', "Remisión enviada por WhatsApp ✅ ({$templateName} · {$langCode})");
        }

        $code   = data_get($json, 'error.code');
        $detail = data_get($json, 'error.message') ?: data_get($json, 'error.error_data.details');
        $suggestions = $wa->groupTemplatesByName($wa->fetchTemplatesSmart(200));

        Log::warning('WA_FAIL_TPL_REMISION', [
            'venta' => $venta->id, 'to' => $to, 'resp' => $json, 'tpl' => $templateName, 'lang' => $langCode
        ]);

        return back()
            ->with('wa_info', 'No se pudo enviar la remisión por WhatsApp. Verifica nombre/idioma tal cual existen en tu WABA.')
            ->with('wa_fail', [[
                'n' => $to, 'code' => $code, 'detail' => $detail, 'raw' => $json,
                'template_tried' => $templateName, 'lang_tried' => $langCode,
            ]])
            ->with('wa_templates_grouped', $suggestions);
    }

    // =====================================================
    // LÓGICA NUEVA: aplicar crédito inicial (trade-in + anticipo)
    // de forma proporcional a TODAS las cuotas de financiamiento
    // =====================================================
    private function aplicarCreditoInicial(Venta $venta, float $anticipoMonto, float $tradeinTotal): void
    {
        $credito = max(0, $anticipoMonto + $tradeinTotal);

        if ($credito <= 0) {
            return;
        }

        // Cuotas ordenadas
        $cuotas = PagoFinanciamiento::where('venta_id', $venta->id)
            ->orderBy('fecha_pago')
            ->get();

        if ($cuotas->isEmpty()) {
            return;
        }

        // Total base = total_original (si existe) o total
        $totalBase = (float) ($venta->total_original ?? $venta->total ?? 0);

        // Suma original de cuotas
        $sumaOriginal = (float) $cuotas->sum('monto');
        if ($totalBase <= 0) {
            $totalBase = $sumaOriginal;
        }

        if ($sumaOriginal <= 0) {
            foreach ($cuotas as $c) {
                $c->monto = 0;
                $c->monto_pendiente = 0;
                $c->pagado = false;
                $c->save();
            }
            return;
        }

        // Lo que debe quedar por pagar después de crédito inicial
        $targetRest = max(0.0, $totalBase - $credito);
        // No puede ser mayor a la suma de cuotas
        $targetRest = min($targetRest, $sumaOriginal);

        // Si el crédito cubre todo, marcamos todo pagado
        if ($targetRest <= 0.009) {
            foreach ($cuotas as $c) {
                $c->monto = 0;
                $c->monto_pendiente = 0;
                $c->pagado = true;
                $c->save();
            }
            return;
        }

        // Factor de ajuste proporcional
        $factor = $targetRest / $sumaOriginal;

        $acumulado = 0.0;
        $lastIndex = $cuotas->count() - 1;

        foreach ($cuotas as $index => $cuota) {
            if ($index === $lastIndex) {
                // La última cuota absorbe diferencia de redondeo
                $nuevoMonto = round($targetRest - $acumulado, 2);
            } else {
                $nuevoMonto = round($cuota->monto * $factor, 2);
                $acumulado += $nuevoMonto;
            }

            $nuevoMonto = max(0, $nuevoMonto);

            // 🔥 IMPORTANTE: actualizar tanto monto como monto_pendiente
            $cuota->monto = $nuevoMonto;
            $cuota->monto_pendiente = $nuevoMonto;
            $cuota->pagado = false; // siguen siendo pagos futuros
            $cuota->save();
        }
    }

    public function pagosGlobalPdf(Venta $venta)
    {
        // Cargar relaciones necesarias
        $venta->load(['cliente']);

        // === PLAN DE FINANCIAMIENTO (pagos programados) ===
        $pagosPlan        = PagoFinanciamiento::where('venta_id', $venta->id)
            ->orderBy('fecha_pago')
            ->get();
        $totalCuotas      = $pagosPlan->count();
        $totalProgramado  = (float) $pagosPlan->sum('monto');

        // === TOTALES ORIGINAL Y NETO ===
        $totalOriginal = (float) ($venta->total_original ?? $venta->total ?? 0);
        $totalNeto     = (float) ($venta->total_neto ?? $totalOriginal);

        // === PAGOS REALIZADOS (de la tabla pagos) ===
        $pagosRealizados = Pago::where('venta_id', $venta->id)->get();

        // ANTICIPOS: por flag es_anticipo
        $pagosAnticipo = $pagosRealizados->filter(function ($pago) {
            return (bool) ($pago->es_anticipo ?? false);
        })->values();

        // TRADE-IN: por método de pago (metodo_pago / metodo / forma_pago)
        $pagosTradeIn = $pagosRealizados->filter(function ($pago) {
            $metodo = strtolower(trim(
                $pago->metodo_pago
                ?? $pago->metodo
                ?? $pago->forma_pago
                ?? ''
            ));
            return in_array($metodo, ['trade-in', 'trade in', 'tradein']);
        })->values();

        // Generar PDF con TODOS los datos necesarios
        $pdf = PDF::loadView('venta.pagos_global_pdf', [
            'venta'           => $venta,
            'pagosPlan'       => $pagosPlan,
            'totalCuotas'     => $totalCuotas,
            'totalProgramado' => $totalProgramado,
            'totalOriginal'   => $totalOriginal,
            'totalNeto'       => $totalNeto,
            'pagosAnticipo'   => $pagosAnticipo,
            'pagosTradeIn'    => $pagosTradeIn,
        ])->setPaper('letter', 'portrait');

        $filename = 'PlanPagos_Remision_2025-' . $venta->id . '.pdf';

        return $pdf->download($filename);
    }

    public function notificarPagoFinanciamiento(Request $request, int $pagoId)
    {
        $tz = config('app.timezone', 'America/Mexico_City');
        $now = Carbon::now($tz);

        $pago = PagoFinanciamiento::with(['venta.cliente'])->findOrFail($pagoId);

        // Si ya está pagado o no tiene fecha, no notificamos
        if (($pago->pagado ?? false) === true) {
            return response()->json([
                'ok' => true,
                'skipped' => true,
                'reason' => 'Pago ya está marcado como pagado.',
            ]);
        }

        $venta   = $pago->venta;
        $cliente = $venta?->cliente;

        $clienteNombre = trim(($cliente->nombre ?? '') . ' ' . ($cliente->apellido ?? ''));
        if ($clienteNombre === '') $clienteNombre = 'Cliente';

        $fechaPago = $pago->fecha_pago ? Carbon::parse($pago->fecha_pago, $tz)->startOfDay() : null;

        $hoy = $now->copy()->startOfDay();
        $estado = $fechaPago
            ? ($fechaPago->lt($hoy) ? 'atrasado' : ($fechaPago->equalTo($hoy) ? 'hoy' : 'futuro'))
            : 'sin_fecha';

        // Solo avisamos atrasado/hoy
        if (!in_array($estado, ['atrasado','hoy'], true)) {
            return response()->json([
                'ok' => true,
                'skipped' => true,
                'reason' => 'No es pago de hoy ni atrasado.',
                'estado' => $estado,
            ]);
        }

        $fechaTxt = $fechaPago ? $fechaPago->format('d/m/Y') : 'sin fecha';

        // ✅ URL click segura
        if ($venta && RouteFacade::has('ventas.pagos.index')) {
            $url = route('ventas.pagos.index', $venta->id);
        } elseif ($venta && RouteFacade::has('ventas.show')) {
            $url = route('ventas.show', $venta->id);
        } else {
            $url = url('/financiamientos');
        }

        $title = $estado === 'atrasado'
            ? 'Pago atrasado en financiamiento'
            : 'Pago vence hoy (financiamiento)';

        $remisionId = $venta?->id ?? '—';
        $monto = isset($pago->monto_pendiente) ? (float)$pago->monto_pendiente : (float)($pago->monto ?? 0);

        $message = ($estado === 'atrasado')
            ? "Atrasado ({$fechaTxt}) · $clienteNombre · Remisión 2025-{$remisionId} · $".number_format($monto,2)
            : "Vence HOY ({$fechaTxt}) · $clienteNombre · Remisión 2025-{$remisionId} · $".number_format($monto,2);

        // ✅ Dedup key (para no spamear)
        $key = 'financiamiento:' . $pago->id . ':' . ($fechaPago?->toDateString() ?? 'nofecha');

        // ✅ SOLO ADMINS (users.role = 'admin')
        $admins = User::query()->where('role', 'admin')->get();

        $sent = 0;
        $skippedDup = 0;

        foreach ($admins as $admin) {
            $yaExiste = $admin->notifications()
                ->where('data->key', $key)
                ->exists();

            if ($yaExiste) {
                $skippedDup++;
                continue;
            }

            $admin->notify(new PagoFinanciamientoAlertNotification(
                title: $title,
                message: $message,
                url: $url,
                type: 'financiamiento'
            ));

            $n = $admin->unreadNotifications()->latest()->first();
            if ($n) {
                $data = $n->data ?? [];
                $data['key'] = $key;
                $data['pago_financiamiento_id'] = $pago->id;
                $data['venta_id'] = $venta?->id;
                $data['fecha_pago'] = $fechaPago?->toDateString();
                $data['estado'] = $estado;
                $n->update(['data' => $data]);
            }

            $sent++;
        }

        return response()->json([
            'ok' => true,
            'pago_id' => $pago->id,
            'estado' => $estado,
            'admins' => $admins->count(),
            'sent' => $sent,
            'skipped_duplicates' => $skippedDup,
        ]);
    }

    /**
     * ✅ NUEVO: Notifica TODOS los pagos "atrasados" y "de hoy".
     * - pagado = false
     * - fecha_pago <= hoy
     * - dedup por pago+fecha (key) para no duplicar
     */
    public function notificarPagosPendientesHoyYAtrasados(Request $request)
    {
        $tz = config('app.timezone', 'America/Mexico_City');
        $now = Carbon::now($tz);
        $hoy = $now->copy()->startOfDay();

        // Solo admins reciben
        $admins = User::query()->where('role', 'admin')->get();
        if ($admins->isEmpty()) {
            return response()->json(['ok' => true, 'admins' => 0, 'sent_total' => 0]);
        }

        // Traer pagos pendientes de hoy y atrasados
        $pagos = PagoFinanciamiento::with(['venta.cliente'])
            ->where(function ($q) {
                $q->whereNull('pagado')->orWhere('pagado', false);
            })
            ->whereNotNull('fecha_pago')
            ->whereDate('fecha_pago', '<=', $hoy->toDateString())
            ->orderBy('fecha_pago', 'asc')
            ->get();

        $sentTotal = 0;
        $skippedDupTotal = 0;

        foreach ($pagos as $pago) {
            $venta   = $pago->venta;
            $cliente = $venta?->cliente;

            $clienteNombre = trim(($cliente->nombre ?? '') . ' ' . ($cliente->apellido ?? ''));
            if ($clienteNombre === '') $clienteNombre = 'Cliente';

            $fechaPago = Carbon::parse($pago->fecha_pago, $tz)->startOfDay();
            $estado = $fechaPago->lt($hoy) ? 'atrasado' : 'hoy';

            $fechaTxt = $fechaPago->format('d/m/Y');

            // URL click
            if ($venta && RouteFacade::has('ventas.pagos.index')) {
                $url = route('ventas.pagos.index', $venta->id);
            } elseif ($venta && RouteFacade::has('ventas.show')) {
                $url = route('ventas.show', $venta->id);
            } else {
                $url = url('/financiamientos');
            }

            $title = $estado === 'atrasado'
                ? 'Pago atrasado en financiamiento'
                : 'Pago vence hoy (financiamiento)';

            $remisionId = $venta?->id ?? '—';
            $monto = isset($pago->monto_pendiente) ? (float)$pago->monto_pendiente : (float)($pago->monto ?? 0);

            $message = ($estado === 'atrasado')
                ? "Atrasado ({$fechaTxt}) · $clienteNombre · Remisión 2025-{$remisionId} · $".number_format($monto,2)
                : "Vence HOY ({$fechaTxt}) · $clienteNombre · Remisión 2025-{$remisionId} · $".number_format($monto,2);

            // Dedup
            $key = 'financiamiento:' . $pago->id . ':' . $fechaPago->toDateString();

            foreach ($admins as $admin) {
                $yaExiste = $admin->notifications()
                    ->where('data->key', $key)
                    ->exists();

                if ($yaExiste) {
                    $skippedDupTotal++;
                    continue;
                }

                $admin->notify(new PagoFinanciamientoAlertNotification(
                    title: $title,
                    message: $message,
                    url: $url,
                    type: 'financiamiento'
                ));

                // Inyectar key + extras a la notificación recién creada
                $n = $admin->unreadNotifications()->latest()->first();
                if ($n) {
                    $data = $n->data ?? [];
                    $data['key'] = $key;
                    $data['pago_financiamiento_id'] = $pago->id;
                    $data['venta_id'] = $venta?->id;
                    $data['fecha_pago'] = $fechaPago->toDateString();
                    $data['estado'] = $estado;
                    $n->update(['data' => $data]);
                }

                $sentTotal++;
            }
        }

        return response()->json([
            'ok' => true,
            'admins' => $admins->count(),
            'pagos_detectados' => $pagos->count(),
            'sent_total' => $sentTotal,
            'skipped_duplicates_total' => $skippedDupTotal,
            'hoy' => $hoy->toDateString(),
        ]);
    }

    public function destroy(Request $request, $id)
    {
        // ✅ Validar que venga el PIN
        $request->validate([
            'aprobacion_pin' => ['required','string'],
        ]);

        // ✅ PIN esperado desde .env (mejor vía config)
        $expected = (string) config('app.aprobacion_pin', env('APROBACION_PIN'));
        $pin      = (string) $request->input('aprobacion_pin');

        // ✅ Comparación segura
        if ($expected === '' || !hash_equals($expected, $pin)) {
            return back()->with('error', 'PIN incorrecto. No se eliminó la venta.');
        }

        // ✅ Buscar y borrar (ajusta el modelo si el tuyo se llama distinto)
        $venta = \App\Models\Venta::findOrFail($id);
        $venta->delete();

        return back()->with('success', 'Venta eliminada correctamente.');
    }
    // ============================================================
// ✅ Helper: guardar UN archivo en documentos_pago
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
// ✅ Helper: guardar MUCHOS archivos o uno solo
// ============================================================
private function guardarEvidenciasPago(Request $request, Pago $pago): void
{
    // ✅ Nuevo flujo: múltiples archivos documentos[]
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

    // ✅ Compatibilidad con formularios viejos: recibo
    if ($request->hasFile('recibo')) {
        $recibo = $request->file('recibo');

        if ($recibo instanceof UploadedFile) {
            $this->guardarDocumentoPago($pago, $recibo);
        }
    }
}
private function buildAuditoriaSnapshotForAI(): array
{
    $hoy = Carbon::today();

    $ventas = Venta::with([
            'cliente',
            'productos.producto',
            'pagosFinanciamiento',
            'pagosReales',
            'tradeins',
        ])
        ->orderByDesc('created_at')
        ->take(180)
        ->get();

    $ordenes = Orden::with(['cliente'])
        ->orderByDesc('created_at')
        ->take(120)
        ->get();

    $rows = collect();

    foreach ($ventas as $venta) {
        $clienteNombre = trim(
            ((optional($venta->cliente)->nombre ?? '') . ' ' . (optional($venta->cliente)->apellido ?? ''))
        );
        $clienteNombre = $clienteNombre !== '' ? $clienteNombre : 'SIN CLIENTE';

        $pagosPlan = collect($venta->pagosFinanciamiento ?? []);
        $pagosPendientes = $pagosPlan->filter(fn($p) => !(bool)($p->pagado ?? false))->values();
        $pagosPagadosPlan = $pagosPlan->filter(fn($p) => (bool)($p->pagado ?? false))->values();

        $pagosRealesAprobados = collect($venta->pagosReales ?? [])
            ->filter(fn($p) => (bool)($p->aprobado ?? false))
            ->values();

        $montoAnticipo = (float) $pagosRealesAprobados
            ->filter(fn($p) => (bool)($p->es_anticipo ?? false))
            ->sum('monto');

        $montoTradeIn = (float) $pagosRealesAprobados->filter(function ($p) {
            $metodo = strtolower(trim($p->metodo_pago ?? $p->metodo ?? $p->forma_pago ?? ''));
            return in_array($metodo, ['trade-in', 'trade in', 'tradein']);
        })->sum('monto');

        $montoPlanPagado = (float) $pagosPagadosPlan->sum('monto');

        $totalOriginal = (float) ($venta->total_original ?? $venta->total ?? 0);
        $totalPagado = $montoAnticipo + $montoTradeIn + $montoPlanPagado;
        $saldo = max(0, $totalOriginal - $totalPagado);

        $pagosVencidos = $pagosPendientes->filter(function ($p) use ($hoy) {
            try {
                return $p->fecha_pago && Carbon::parse($p->fecha_pago)->startOfDay()->lt($hoy);
            } catch (\Throwable $e) {
                return false;
            }
        })->sortBy('fecha_pago')->values();

        $mesesAdeudados = $pagosVencidos->count();

        $diasAtraso = 0;
        if ($pagosVencidos->count() > 0) {
            try {
                $primeroVencido = Carbon::parse($pagosVencidos->first()->fecha_pago)->startOfDay();
                $diasAtraso = $primeroVencido->diffInDays($hoy);
            } catch (\Throwable $e) {
                $diasAtraso = 0;
            }
        }

        if ($saldo <= 0.01) {
            $estado = 'liquidada';
        } elseif ($mesesAdeudados > 0) {
            $estado = 'atrasada';
        } else {
            $estado = 'pendiente';
        }

        if ($estado === 'liquidada') {
            $riesgo = 'bajo';
        } elseif ($mesesAdeudados >= 3 || $diasAtraso >= 60) {
            $riesgo = 'alto';
        } elseif ($mesesAdeudados >= 1 || $diasAtraso >= 1) {
            $riesgo = 'medio';
        } else {
            $riesgo = 'bajo';
        }

        $primerProducto = optional(optional($venta->productos)->first())->producto;
        $equipo = trim(collect([
            $primerProducto->tipo_equipo ?? null,
            $primerProducto->marca ?? null,
            $primerProducto->modelo ?? null,
        ])->filter()->implode(' '));

        $proximoPago = $pagosPendientes
            ->filter(function ($p) {
                try {
                    return !empty($p->fecha_pago);
                } catch (\Throwable $e) {
                    return false;
                }
            })
            ->sortBy('fecha_pago')
            ->first();

        $ultimoPagoReal = $pagosRealesAprobados
            ->filter(fn($p) => !empty($p->fecha_pago))
            ->sortByDesc('fecha_pago')
            ->first();

        $ultimoPagoPlan = $pagosPagadosPlan
            ->filter(fn($p) => !empty($p->fecha_pago))
            ->sortByDesc('fecha_pago')
            ->first();

        $ultimaFechaPago = null;
        if ($ultimoPagoReal && $ultimoPagoPlan) {
            $fechaReal = Carbon::parse($ultimoPagoReal->fecha_pago);
            $fechaPlan = Carbon::parse($ultimoPagoPlan->fecha_pago);
            $ultimaFechaPago = $fechaReal->gte($fechaPlan) ? $fechaReal : $fechaPlan;
        } elseif ($ultimoPagoReal) {
            $ultimaFechaPago = Carbon::parse($ultimoPagoReal->fecha_pago);
        } elseif ($ultimoPagoPlan) {
            $ultimaFechaPago = Carbon::parse($ultimoPagoPlan->fecha_pago);
        }

        $rows->push([
            'tipo' => 'venta',
            'id' => $venta->id,
            'folio' => 'REM-2025-' . $venta->id,
            'cliente' => $clienteNombre,
            'equipo' => $equipo ?: 'Sin equipo especificado',
            'estado' => $estado,
            'riesgo' => $riesgo,
            'meses_adeudados' => $mesesAdeudados,
            'dias_atraso' => $diasAtraso,
            'saldo' => round($saldo, 2),
            'monto_vencido' => round((float) $pagosVencidos->sum('monto'), 2),
            'total' => round($totalOriginal, 2),
            'pagado' => round($totalPagado, 2),
            'telefono' => optional($venta->cliente)->telefono,
            'correo' => optional($venta->cliente)->email,
            'ultimo_pago' => $ultimaFechaPago,
            'proximo_pago' => $proximoPago?->fecha_pago ? Carbon::parse($proximoPago->fecha_pago) : null,
            'url_pagos' => route('ventas.pagos.index', $venta->id),
            'url_ver' => route('ventas.show', $venta->id),
        ]);
    }

    foreach ($ordenes as $orden) {
        $clienteNombre = trim(
            ((optional($orden->cliente)->nombre ?? '') . ' ' . (optional($orden->cliente)->apellido ?? ''))
        );
        $clienteNombre = $clienteNombre !== '' ? $clienteNombre : 'SIN CLIENTE';

        $cantidad = (float) ($orden->remision_cantidad ?? 0);
        $precio = (float) ($orden->remision_precio ?? 0);
        $total = (float) ($orden->remision_subtotal ?? 0);

        if ($total <= 0 && $cantidad > 0 && $precio > 0) {
            $total = $cantidad * $precio;
        }

        $pagado = (float) Pago::where('orden_id', $orden->id)
            ->where('aprobado', true)
            ->sum('monto');

        $saldo = max(0, $total - $pagado);
        $estado = $saldo <= 0.01 ? 'liquidada' : 'pendiente';
        $riesgo = $saldo > 0 ? 'medio' : 'bajo';

        $rows->push([
            'tipo' => 'orden',
            'id' => $orden->id,
            'folio' => 'OS-2025-' . $orden->id,
            'cliente' => $clienteNombre,
            'equipo' => $orden->equipo ?? 'Servicio',
            'estado' => $estado,
            'riesgo' => $riesgo,
            'meses_adeudados' => 0,
            'dias_atraso' => 0,
            'saldo' => round($saldo, 2),
            'monto_vencido' => 0,
            'total' => round($total, 2),
            'pagado' => round($pagado, 2),
            'telefono' => optional($orden->cliente)->telefono,
            'correo' => optional($orden->cliente)->email,
            'ultimo_pago' => null,
            'proximo_pago' => null,
            'url_pagos' => \Route::has('ordenes.pagos.index')
                ? route('ordenes.pagos.index', $orden->id)
                : url('/ordenes/' . $orden->id . '/pagos'),
            'url_ver' => \Route::has('ordenes.remision.pdf')
                ? route('ordenes.remision.pdf', $orden->id)
                : url('/ordenes/' . $orden->id . '/remision-pdf'),
        ]);
    }

    $rows = $rows->sortByDesc(function ($item) {
        $pesoEstado = $item['estado'] === 'atrasada' ? 3 : ($item['estado'] === 'pendiente' ? 2 : 1);
        return ($pesoEstado * 1000000000) + ((int) $item['meses_adeudados'] * 1000000) + (int) round($item['saldo']);
    })->values();

    $resumen = [
        'total_registros'     => $rows->count(),
        'total_deudores'      => $rows->filter(fn($x) => $x['saldo'] > 0.01)->count(),
        'atrasados'           => $rows->filter(fn($x) => $x['estado'] === 'atrasada')->count(),
        'pendientes'          => $rows->filter(fn($x) => $x['estado'] === 'pendiente')->count(),
        'liquidadas'          => $rows->filter(fn($x) => $x['estado'] === 'liquidada')->count(),
        'saldo_total'         => round((float) $rows->sum('saldo'), 2),
        'monto_vencido_total' => round((float) $rows->sum('monto_vencido'), 2),
        'riesgo_alto'         => $rows->filter(fn($x) => $x['riesgo'] === 'alto')->count(),
        'riesgo_medio'        => $rows->filter(fn($x) => $x['riesgo'] === 'medio')->count(),
        'riesgo_bajo'         => $rows->filter(fn($x) => $x['riesgo'] === 'bajo')->count(),
    ];

    $topDeudores = $rows->where('saldo', '>', 0)->sortByDesc('saldo')->take(8)->values()->map(function ($x) {
        return [
            'folio' => $x['folio'],
            'cliente' => $x['cliente'],
            'estado' => ucfirst($x['estado']),
            'riesgo' => ucfirst($x['riesgo']),
            'saldo' => $x['saldo'],
        ];
    })->values()->all();

    $topAtrasos = $rows->where('meses_adeudados', '>', 0)->sortByDesc('meses_adeudados')->take(8)->values()->map(function ($x) {
        return [
            'folio' => $x['folio'],
            'cliente' => $x['cliente'],
            'meses_adeudados' => $x['meses_adeudados'],
            'dias_atraso' => $x['dias_atraso'],
            'monto_vencido' => $x['monto_vencido'],
        ];
    })->values()->all();

    $chartEstado = [
        'title' => 'Distribución por estado',
        'type' => 'doughnut',
        'labels' => ['Pendiente', 'Atrasada', 'Liquidada'],
        'values' => [
            $resumen['pendientes'],
            $resumen['atrasados'],
            $resumen['liquidadas'],
        ],
    ];

    $chartRiesgo = [
        'title' => 'Distribución por riesgo',
        'type' => 'bar',
        'labels' => ['Alto', 'Medio', 'Bajo'],
        'values' => [
            $resumen['riesgo_alto'],
            $resumen['riesgo_medio'],
            $resumen['riesgo_bajo'],
        ],
    ];

    $chartTopSaldo = [
        'title' => 'Top saldo pendiente',
        'type' => 'bar',
        'labels' => collect($topDeudores)->pluck('cliente')->map(fn($x) => Str::limit($x, 18))->all(),
        'values' => collect($topDeudores)->pluck('saldo')->all(),
    ];

    return [
        'rows' => $rows,
        'resumen' => $resumen,
        'top_deudores' => $topDeudores,
        'top_atrasos' => $topAtrasos,
        'charts' => [$chartEstado, $chartRiesgo, $chartTopSaldo],
    ];
}

private function resolveAuditoriaAdminUser()
{
    abort_unless(auth()->check(), 403);

    $user = auth()->user();

    $isAdmin =
        (($user->is_admin ?? false) === true)
        || (($user->role ?? null) === 'admin')
        || (method_exists($user, 'hasRole') && $user->hasRole('admin'));

    abort_unless($isAdmin, 403);

    return $user;
}

private function auditoriaPersonas(): array
{
    return [
        'director_financiero' => 'Director financiero',
        'contador' => 'Contador senior',
        'administrador' => 'Administrador operativo',
        'cobranza' => 'Gerente de cobranza',
    ];
}

private function auditoriaSchema(): array
{
    return [
        'name' => 'auditoria_financiera_chat',
        'strict' => true,
        'schema' => [
            'type' => 'object',
            'additionalProperties' => false,
            'properties' => [
                'titulo' => ['type' => 'string'],
                'resumen_corto' => ['type' => 'string'],
                'respuesta_corta' => ['type' => 'string'],
                'resumen_ejecutivo' => ['type' => 'string'],
                'respuesta_detallada' => ['type' => 'string'],
                'hallazgos' => [
                    'type' => 'array',
                    'items' => ['type' => 'string'],
                ],
                'cuellos_botella' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'object',
                        'additionalProperties' => false,
                        'properties' => [
                            'titulo' => ['type' => 'string'],
                            'impacto' => ['type' => 'string'],
                            'detalle' => ['type' => 'string'],
                        ],
                        'required' => ['titulo', 'impacto', 'detalle'],
                    ],
                ],
                'recomendaciones' => [
                    'type' => 'array',
                    'items' => ['type' => 'string'],
                ],
                'alertas' => [
                    'type' => 'array',
                    'items' => ['type' => 'string'],
                ],
            ],
            'required' => [
                'titulo',
                'resumen_corto',
                'respuesta_corta',
                'resumen_ejecutivo',
                'respuesta_detallada',
                'hallazgos',
                'cuellos_botella',
                'recomendaciones',
                'alertas',
            ],
        ],
    ];
}

private function prepareAuditoriaChatData(Request $request): array
{
    $user = $this->resolveAuditoriaAdminUser();
    $debugId = (string) Str::uuid();

    $data = $request->validate([
        'question' => ['required', 'string', 'max:4000'],
        'persona' => ['nullable', 'string', 'in:director_financiero,contador,administrador,cobranza'],
        'conversation' => ['nullable', 'array'],
    ]);

    $persona = $data['persona'] ?? 'director_financiero';
    $personas = $this->auditoriaPersonas();
    $snapshot = $this->buildAuditoriaSnapshotForAI();

    $contexto = [
        'resumen' => $snapshot['resumen'],
        'top_deudores' => $snapshot['top_deudores'],
        'top_atrasos' => $snapshot['top_atrasos'],
        'casos_criticos' => $snapshot['rows']
            ->where('saldo', '>', 0)
            ->sortByDesc(fn($x) => ($x['meses_adeudados'] * 1000000) + $x['saldo'])
            ->take(15)
            ->values()
            ->all(),
    ];

    $history = collect($data['conversation'] ?? [])
        ->take(-20)
        ->map(function ($m) {
            return [
                'role' => in_array(($m['role'] ?? 'user'), ['user', 'assistant']) ? $m['role'] : 'user',
                'content' => (string) ($m['content'] ?? ''),
            ];
        })
        ->filter(fn($m) => trim($m['content']) !== '')
        ->values()
        ->all();

    $messages = [
        [
            'role' => 'system',
            'content' =>
                'Eres un ' . $personas[$persona] . ' experto en cobranza, análisis financiero, métricas, cuellos de botella y reportes ejecutivos para ERP en México. ' .
                'Responde solo con JSON estructurado. ' .
                'No inventes cifras; usa únicamente el contexto suministrado. ' .
                'Mantén coherencia con la conversación previa. ' .
                'Si el usuario pide algo puntual, responde exactamente eso y no des más de lo necesario. ' .
                'resumen_corto debe ser de 1 sola frase clara. ' .
                'respuesta_corta debe ser breve, natural, profesional y máximo 2 frases. ' .
                'resumen_ejecutivo debe ser conciso, útil y máximo 2 frases. ' .
                'El detalle amplio déjalo en respuesta_detallada, hallazgos, cuellos_botella, recomendaciones, alertas, tablas y gráficas.',
        ],
    ];

    foreach ($history as $msg) {
        $messages[] = $msg;
    }

    $messages[] = [
        'role' => 'user',
        'content' =>
            "Pregunta actual del usuario:\n" .
            $data['question'] .
            "\n\nContexto financiero:\n" .
            json_encode($contexto, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
    ];

    return [
        'user' => $user,
        'debug_id' => $debugId,
        'data' => $data,
        'persona' => $persona,
        'personas' => $personas,
        'snapshot' => $snapshot,
        'contexto' => $contexto,
        'history' => $history,
        'messages' => $messages,
        'schema' => $this->auditoriaSchema(),
        'api_key' => config('services.openai.key'),
        'model' => config('services.openai.primary', 'gpt-4o'),
        'timeout' => (int) config('services.openai.timeout', 60),
    ];
}

private function buildAuditoriaPayload(array $snapshot, array $decoded, string $personaLabel, string $question): array
{
    return [
        'persona' => $personaLabel,
        'question' => $question,
        'generated_at' => now()->format('d/m/Y H:i'),
        'narrative' => $decoded,
        'kpis' => [
            ['label' => 'Saldo total', 'value' => '$' . number_format($snapshot['resumen']['saldo_total'], 2), 'detail' => 'Pendiente acumulado'],
            ['label' => 'Vencido acumulado', 'value' => '$' . number_format($snapshot['resumen']['monto_vencido_total'], 2), 'detail' => 'Cuotas vencidas'],
            ['label' => 'Deudores activos', 'value' => number_format($snapshot['resumen']['total_deudores']), 'detail' => 'Con saldo > 0'],
            ['label' => 'Riesgo alto', 'value' => number_format($snapshot['resumen']['riesgo_alto']), 'detail' => 'Casos críticos'],
        ],
        'tables' => [
            [
                'title' => 'Top deudores',
                'columns' => ['Folio', 'Cliente', 'Estado', 'Riesgo', 'Saldo'],
                'rows' => collect($snapshot['top_deudores'])->map(function ($x) {
                    return [
                        $x['folio'],
                        $x['cliente'],
                        $x['estado'],
                        $x['riesgo'],
                        '$' . number_format($x['saldo'], 2),
                    ];
                })->all(),
            ],
            [
                'title' => 'Casos con más atraso',
                'columns' => ['Folio', 'Cliente', 'Meses adeudados', 'Días atraso', 'Monto vencido'],
                'rows' => collect($snapshot['top_atrasos'])->map(function ($x) {
                    return [
                        $x['folio'],
                        $x['cliente'],
                        (string) $x['meses_adeudados'],
                        (string) $x['dias_atraso'],
                        '$' . number_format($x['monto_vencido'], 2),
                    ];
                })->all(),
            ],
        ],
        'charts' => $snapshot['charts'],
    ];
}

private function extractFirstJsonObject(string $content): string
{
    $content = trim($content);

    if ($content === '') {
        return '';
    }

    $decoded = json_decode($content, true);
    if (is_array($decoded)) {
        return $content;
    }

    $start = strpos($content, '{');
    $end = strrpos($content, '}');

    if ($start !== false && $end !== false && $end > $start) {
        return substr($content, $start, ($end - $start + 1));
    }

    return $content;
}

private function flushStream(): void
{
    if (function_exists('apache_setenv')) {
        @apache_setenv('no-gzip', '1');
    }

    @ini_set('zlib.output_compression', '0');
    @ini_set('output_buffering', 'off');
    @ini_set('implicit_flush', '1');

    while (ob_get_level() > 0) {
        @ob_end_flush();
    }

    @ob_implicit_flush(true);
}

private function streamNdjson(array $payload): void
{
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "\n";

    if (function_exists('ob_flush')) {
        @ob_flush();
    }

    flush();
}

public function auditoriaFinanciamientosChat(Request $request)
{
    $prepared = $this->prepareAuditoriaChatData();

    if (!$prepared['api_key']) {
        Log::error('Auditoría IA: falta OPENAI_API_KEY', [
            'debug_id' => $prepared['debug_id'],
            'user_id' => $prepared['user']?->id,
            'persona' => $prepared['persona'],
            'question' => $prepared['data']['question'],
        ]);

        return response()->json([
            'ok' => false,
            'message' => 'Falta OPENAI_API_KEY en el .env',
            'request_id' => $prepared['debug_id'],
        ], 422);
    }

    try {
        Log::info('Auditoría IA: enviando solicitud a OpenAI', [
            'debug_id' => $prepared['debug_id'],
            'user_id' => $prepared['user']?->id,
            'persona' => $prepared['persona'],
            'question' => $prepared['data']['question'],
            'model' => $prepared['model'],
        ]);

        $response = Http::timeout($prepared['timeout'])
            ->withToken($prepared['api_key'])
            ->post('https://api.openai.com/v1/chat/completions', [
                'model' => $prepared['model'],
                'temperature' => 0.2,
                'store' => false,
                'messages' => $prepared['messages'],
                'response_format' => [
                    'type' => 'json_schema',
                    'json_schema' => $prepared['schema'],
                ],
            ]);

        if (!$response->successful()) {
            $responseJson = null;

            try {
                $responseJson = $response->json();
            } catch (\Throwable $e) {
                $responseJson = null;
            }

            Log::error('Auditoría IA: OpenAI devolvió error', [
                'debug_id' => $prepared['debug_id'],
                'user_id' => $prepared['user']?->id,
                'status' => $response->status(),
                'reason' => $response->reason(),
                'body' => $response->body(),
                'json' => $responseJson,
                'persona' => $prepared['persona'],
                'question' => $prepared['data']['question'],
            ]);

            return response()->json([
                'ok' => false,
                'message' => 'OpenAI devolvió error.',
                'status' => $response->status(),
                'request_id' => $prepared['debug_id'],
                'error' => $responseJson,
                'raw' => $response->body(),
            ], 422);
        }

        $content = data_get($response->json(), 'choices.0.message.content', '');
        $content = $this->extractFirstJsonObject((string) $content);
        $decoded = json_decode($content, true);

        if (!is_array($decoded)) {
            Log::warning('Auditoría IA: respuesta no llegó en JSON válido', [
                'debug_id' => $prepared['debug_id'],
                'user_id' => $prepared['user']?->id,
                'content' => $content,
                'persona' => $prepared['persona'],
                'question' => $prepared['data']['question'],
            ]);

            return response()->json([
                'ok' => false,
                'message' => 'La respuesta no llegó en JSON válido.',
                'request_id' => $prepared['debug_id'],
                'raw' => $content,
            ], 422);
        }

        $payload = $this->buildAuditoriaPayload(
            $prepared['snapshot'],
            $decoded,
            $prepared['personas'][$prepared['persona']],
            $prepared['data']['question']
        );

        Log::info('Auditoría IA: respuesta exitosa', [
            'debug_id' => $prepared['debug_id'],
            'user_id' => $prepared['user']?->id,
            'persona' => $prepared['persona'],
            'question' => $prepared['data']['question'],
        ]);

        return response()->json([
            'ok' => true,
            'request_id' => $prepared['debug_id'],
            'data' => $payload,
        ]);
    } catch (\Throwable $e) {
        Log::error('Auditoría IA: excepción al consultar OpenAI', [
            'debug_id' => $prepared['debug_id'],
            'user_id' => $prepared['user']?->id,
            'persona' => $prepared['persona'],
            'question' => $prepared['data']['question'],
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString(),
        ]);

        return response()->json([
            'ok' => false,
            'message' => 'No se pudo generar el análisis conversacional.',
            'request_id' => $prepared['debug_id'],
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
        ], 500);
    }
}

public function auditoriaFinanciamientosChatStream(Request $request): StreamedResponse
{
    $prepared = $this->prepareAuditoriaChatData($request);

    return response()->stream(function () use ($prepared) {
        $this->flushStream();
        ignore_user_abort(true);
        @set_time_limit(0);

        $this->streamNdjson([
            'type' => 'start',
            'label' => 'Preparando análisis',
        ]);

        if (!$prepared['api_key']) {
            Log::error('Auditoría IA stream: falta OPENAI_API_KEY', [
                'debug_id' => $prepared['debug_id'],
                'user_id' => $prepared['user']?->id,
                'persona' => $prepared['persona'],
                'question' => $prepared['data']['question'],
            ]);

            $this->streamNdjson([
                'type' => 'error',
                'message' => 'Falta OPENAI_API_KEY en el .env',
            ]);

            return;
        }

        $this->streamNdjson([
            'type' => 'phase',
            'label' => 'Consultando OpenAI',
        ]);

        $rawOpenAiResponse = '';
        $sseBuffer = '';
        $assembledJson = '';
        $streamedRefusal = '';
        $receivedDone = false;
        $didEmitWritingPhase = false;

        try {
            $postData = json_encode([
                'model' => $prepared['model'],
                'temperature' => 0.2,
                'store' => false,
                'stream' => true,
                'messages' => $prepared['messages'],
                'response_format' => [
                    'type' => 'json_schema',
                    'json_schema' => $prepared['schema'],
                ],
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

            if ($postData === false) {
                throw new \RuntimeException('No se pudo serializar la solicitud a OpenAI.');
            }

            $ch = curl_init('https://api.openai.com/v1/chat/completions');

            curl_setopt_array($ch, [
                CURLOPT_POST => true,
                CURLOPT_HTTPHEADER => [
                    'Authorization: Bearer ' . $prepared['api_key'],
                    'Content-Type: application/json',
                    'Accept: text/event-stream',
                ],
                CURLOPT_POSTFIELDS => $postData,
                CURLOPT_RETURNTRANSFER => false,
                CURLOPT_HEADER => false,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_CONNECTTIMEOUT => 20,
                CURLOPT_TIMEOUT => max($prepared['timeout'], 120),
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_WRITEFUNCTION => function ($ch, $chunk) use (&$rawOpenAiResponse, &$sseBuffer, &$assembledJson, &$streamedRefusal, &$receivedDone, &$didEmitWritingPhase) {
                    $rawOpenAiResponse .= $chunk;
                    $sseBuffer .= $chunk;

                    while (($pos = strpos($sseBuffer, "\n")) !== false) {
                        $line = substr($sseBuffer, 0, $pos);
                        $sseBuffer = substr($sseBuffer, $pos + 1);
                        $line = trim($line);

                        if ($line === '' || !Str::startsWith($line, 'data:')) {
                            continue;
                        }

                        $data = trim(substr($line, 5));

                        if ($data === '[DONE]') {
                            $receivedDone = true;
                            continue;
                        }

                        $event = json_decode($data, true);
                        if (!is_array($event)) {
                            continue;
                        }

                        $deltaContent = data_get($event, 'choices.0.delta.content');
                        if (is_string($deltaContent) && $deltaContent !== '') {
                            $assembledJson .= $deltaContent;

                            if (!$didEmitWritingPhase) {
                                $didEmitWritingPhase = true;

                                $this->streamNdjson([
                                    'type' => 'phase',
                                    'label' => 'Redactando respuesta',
                                ]);
                            }
                        }

                        $deltaRefusal = data_get($event, 'choices.0.delta.refusal');
                        if (is_string($deltaRefusal) && $deltaRefusal !== '') {
                            $streamedRefusal .= $deltaRefusal;
                        }

                        $finishReason = data_get($event, 'choices.0.finish_reason');
                        if ($finishReason === 'stop') {
                            $receivedDone = true;
                        }
                    }

                    return strlen($chunk);
                },
            ]);

            $result = curl_exec($ch);
            $curlError = curl_error($ch);
            $curlErrno = curl_errno($ch);
            $status = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
            curl_close($ch);

            if ($result === false || $curlErrno) {
                throw new \RuntimeException('Error CURL/OpenAI: ' . ($curlError ?: 'desconocido'));
            }

            if ($status < 200 || $status >= 300) {
                $responseJson = json_decode($rawOpenAiResponse, true);

                Log::error('Auditoría IA stream: OpenAI devolvió error', [
                    'debug_id' => $prepared['debug_id'],
                    'user_id' => $prepared['user']?->id,
                    'status' => $status,
                    'body' => $rawOpenAiResponse,
                    'json' => $responseJson,
                    'persona' => $prepared['persona'],
                    'question' => $prepared['data']['question'],
                ]);

                $this->streamNdjson([
                    'type' => 'error',
                    'message' => 'OpenAI devolvió error.',
                ]);

                return;
            }

            $this->streamNdjson([
                'type' => 'phase',
                'label' => 'Procesando estructura final',
            ]);

            if (trim($assembledJson) === '' && trim($streamedRefusal) !== '') {
                Log::warning('Auditoría IA stream: respuesta en refusal', [
                    'debug_id' => $prepared['debug_id'],
                    'user_id' => $prepared['user']?->id,
                    'persona' => $prepared['persona'],
                    'question' => $prepared['data']['question'],
                    'refusal' => $streamedRefusal,
                ]);

                $this->streamNdjson([
                    'type' => 'error',
                    'message' => $streamedRefusal,
                ]);

                return;
            }

            $jsonText = $this->extractFirstJsonObject($assembledJson);
            $decoded = json_decode($jsonText, true);

            if (!is_array($decoded)) {
                Log::warning('Auditoría IA stream: respuesta no llegó en JSON válido', [
                    'debug_id' => $prepared['debug_id'],
                    'user_id' => $prepared['user']?->id,
                    'persona' => $prepared['persona'],
                    'question' => $prepared['data']['question'],
                    'received_done' => $receivedDone,
                    'assembled_json' => $assembledJson,
                ]);

                $this->streamNdjson([
                    'type' => 'error',
                    'message' => 'La respuesta no llegó en JSON válido.',
                ]);

                return;
            }

            $payload = $this->buildAuditoriaPayload(
                $prepared['snapshot'],
                $decoded,
                $prepared['personas'][$prepared['persona']],
                $prepared['data']['question']
            );

            Log::info('Auditoría IA stream: respuesta exitosa', [
                'debug_id' => $prepared['debug_id'],
                'user_id' => $prepared['user']?->id,
                'persona' => $prepared['persona'],
                'question' => $prepared['data']['question'],
            ]);

            $this->streamNdjson([
                'type' => 'analysis',
                'data' => $payload,
            ]);

            usleep(220000);

            $this->streamNdjson([
                'type' => 'done',
            ]);
        } catch (\Throwable $e) {
            Log::error('Auditoría IA stream: excepción al consultar OpenAI', [
                'debug_id' => $prepared['debug_id'],
                'user_id' => $prepared['user']?->id,
                'persona' => $prepared['persona'],
                'question' => $prepared['data']['question'],
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            $this->streamNdjson([
                'type' => 'error',
                'message' => 'No se pudo generar el análisis conversacional.',
            ]);
        }
    }, 200, [
        'Content-Type' => 'application/x-ndjson; charset=UTF-8',
        'Cache-Control' => 'no-cache, no-transform',
        'X-Accel-Buffering' => 'no',
        'Connection' => 'keep-alive',
    ]);
}
public function auditoriaFinanciamientosPdf(Request $request)
{
    $this->resolveAuditoriaAdminUser();

    $analysisRaw = $request->input('analysis');
    $chartImagesRaw = $request->input('chart_images');

    $analysis = is_array($analysisRaw)
        ? $analysisRaw
        : json_decode((string) $analysisRaw, true);

    $chartImages = is_array($chartImagesRaw)
        ? $chartImagesRaw
        : json_decode((string) $chartImagesRaw, true);

    if (!is_array($analysis) || empty($analysis)) {
        return back()->with('error', 'No se recibió el análisis para generar el PDF.');
    }

    if (!is_array($chartImages)) {
        $chartImages = [];
    }

    $chartImages = collect($chartImages)
        ->filter(fn($img) => is_string($img) && trim($img) !== '')
        ->values()
        ->all();

    try {
        $pdf = Pdf::loadView('venta.pdf.auditoria_financiamientos_ia', [
            'analysis' => $analysis,
            'chartImages' => $chartImages,
        ])->setPaper('a4', 'portrait');

        return $pdf->download('auditoria-financiamientos-ia-' . now()->format('Ymd_His') . '.pdf');
    } catch (\Throwable $e) {
        Log::error('AUDITORIA_FINANCIAMIENTOS_PDF_FAIL', [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString(),
        ]);

        return back()->with('error', 'No se pudo generar el PDF: ' . $e->getMessage());
    }
}
public function auditoriaFinanciamientos()
{
    setlocale(LC_TIME, 'es_ES.UTF-8', 'es_ES', 'spanish');
    Carbon::setLocale('es');

    $user = auth()->user();
    $isAdmin =
        auth()->check() && (
            (($user->is_admin ?? false) === true)
            || (($user->role ?? null) === 'admin')
            || (method_exists($user, 'hasRole') && $user->hasRole('admin'))
        );

    $snapshot = $this->buildAuditoriaSnapshotForAI();

    $auditoria = $snapshot['rows'] ?? collect();
    $resumen   = $snapshot['resumen'] ?? [];

    $resumen['top_deudores'] = $snapshot['top_deudores'] ?? [];
    $resumen['top_meses']    = $snapshot['top_atrasos'] ?? [];

    return view('venta.auditoria_financiamientos', compact('auditoria', 'resumen', 'isAdmin'));
}
private function normalizarPlanVenta(?string $plan): string
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

private function cambiarPlanContadoAPersonalizadoVenta(Venta $venta): void
{
    $planActual = $this->normalizarPlanVenta($venta->plan ?? '');

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
}