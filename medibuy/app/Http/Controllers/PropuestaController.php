<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\Propuesta;
use App\Models\PagoFinanciamientoPropuesta;
use App\Models\FichaTecnica;
use App\Models\PropuestaTradein;
use PDF;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use setasign\Fpdi\Fpdi;
use Carbon\Carbon;
use App\Services\WhatsAppService;

class PropuestaController extends Controller
{
    /* ========================= CRUD BÁSICO ========================= */

    public function encontrarClientes(Request $request)
    {
        $search = (string) $request->input('search', '');

        $clientes = Cliente::query();

        if ($search !== '') {
            $clientes->where(function ($query) use ($search) {
                $query->where('nombre', 'LIKE', "%{$search}%")
                      ->orWhere('apellido', 'LIKE', "%{$search}%");
            });
        }

        return response()->json(
            $clientes->get(['id', 'nombre', 'apellido', 'telefono', 'email', 'comentarios'])
        );
    }

    public function create()
    {
        $productos = Producto::all();
        $fichas    = FichaTecnica::all();
        $categorias = \App\Models\Categoria::orderBy('nombre')->get();

        return view('propuesta.create', compact('productos', 'fichas', 'categorias'));
    }

    public function store(Request $request)
    {
        \Log::info('Inicio método store - request recibido', $request->all());

        $request->validate([
            'cliente_id'          => 'required|exists:clientes,id',
            'subtotal'            => 'required|numeric',
            'total'               => 'required|numeric',
            'productos_json'      => 'required|json',
            'pagos_json'          => 'nullable|json',
            'equipos_cuenta_json' => 'nullable|json',
            'ficha_tecnica_id'    => 'nullable|exists:fichas_tecnicas,id',
            'lugar'               => 'required|string',
        ]);

        try {
            DB::beginTransaction();

            $propuesta = Propuesta::create([
                'cliente_id'       => $request->cliente_id,
                'lugar'            => $request->lugar,
                'nota'             => $request->nota,
                'user_id'          => auth()->id(),
                'subtotal'         => $request->subtotal,
                'descuento'        => $request->descuento ?? 0,
                'envio'            => $request->envio ?? 0,
                'iva'              => $request->iva ?? 0,
                'total'            => $request->total,
                'plan'             => $request->plan,
                'ficha_tecnica_id' => $request->ficha_tecnica_id,
            ]);

            \Log::info('Propuesta creada', ['id' => $propuesta->id]);

            // ================== PRODUCTOS ==================
            $productos = json_decode($request->productos_json, true) ?: [];
            \Log::info('Productos decodificados', ['productos' => $productos]);

            foreach ($productos as $p) {
                $productoId = (int)($p['producto_id'] ?? 0);
                $cantidad   = max(1, (int)($p['cantidad'] ?? 1));

                $esRegalo = (int)($p['es_regalo'] ?? 0) === 1;

                $precioUnitInput  = (float)($p['precio_unitario'] ?? 0);
                $sobreprecioInput = (float)($p['sobreprecio'] ?? 0);

                $precioUnit  = $esRegalo ? 0.0 : $precioUnitInput;
                $sobreprecio = $esRegalo ? 0.0 : $sobreprecioInput;
                $subtotal    = $esRegalo ? 0.0 : ($cantidad * ($precioUnit + $sobreprecio));

                $propuesta->productos()->create([
                    'producto_id'     => $productoId,
                    'cantidad'        => $cantidad,
                    'precio_unitario' => $precioUnit,
                    'subtotal'        => $subtotal,
                    'sobreprecio'     => $sobreprecio,
                    'es_regalo'       => $esRegalo ? 1 : 0,
                ]);
            }

            // ================== PAGOS FINANCIAMIENTO ==================
            $pf = $request->input('pagos_financiamiento');

            if (is_array($pf)) {
                foreach ($pf as $key => $row) {
                    if (!is_array($row)) {
                        continue;
                    }

                    $eliminar = (string)($row['eliminar'] ?? '0') === '1';
                    if ($eliminar) {
                        continue;
                    }

                    $descripcion = (string)($row['descripcion'] ?? '');
                    $fecha       = (string)($row['fecha_pago'] ?? '');
                    $monto       = (float)($row['monto'] ?? 0);

                    if (!$fecha) {
                        continue;
                    }

                    PagoFinanciamientoPropuesta::create([
                        'propuesta_id' => $propuesta->id,
                        'descripcion'  => $descripcion,
                        'fecha_pago'   => Carbon::parse($fecha),
                        'monto'        => $monto,
                    ]);
                }

                \Log::info('Pagos guardados desde pagos_financiamiento (store)');
            } elseif ($request->filled('pagos_json')) {
                $pagos = json_decode($request->pagos_json, true);
                \Log::info('Pagos decodificados (fallback pagos_json)', ['pagos' => $pagos]);

                if (is_array($pagos)) {
                    foreach ($pagos as $pago) {
                        $fecha = $pago['fecha_pago'] ?? $pago['mes'] ?? null;
                        $monto = $pago['monto'] ?? $pago['cuota'] ?? 0;

                        if (!$fecha) {
                            continue;
                        }

                        PagoFinanciamientoPropuesta::create([
                            'propuesta_id' => $propuesta->id,
                            'descripcion'  => $pago['descripcion'] ?? '',
                            'fecha_pago'   => Carbon::parse($fecha),
                            'monto'        => (float)$monto,
                        ]);
                    }
                }
            }

            // ================== EQUIPOS A CUENTA ==================
            if ($request->filled('equipos_cuenta_json')) {
                $equiposCuenta = json_decode($request->equipos_cuenta_json, true) ?: [];
                \Log::info('Equipos a cuenta decodificados', ['equipos_cuenta' => $equiposCuenta]);

                if (is_array($equiposCuenta)) {
                    foreach ($equiposCuenta as $eq) {
                        $propuesta->tradeins()->create([
                            'tipo_equipo'    => $eq['tipo_equipo']    ?? null,
                            'marca'          => $eq['marca']          ?? null,
                            'modelo'         => $eq['modelo']         ?? null,
                            'numero_serie'   => $eq['numero_serie']   ?? null,
                            'valor_a_cuenta' => $eq['valor_a_cuenta'] ?? 0,
                        ]);
                    }
                }
            }

            DB::commit();

            \Log::info('Propuesta guardada', ['propuesta_id' => $propuesta->id]);

            return redirect()
                ->route('propuestas.show', $propuesta->id)
                ->with('success', 'Propuesta guardada exitosamente.');
        } catch (\Throwable $e) {
            DB::rollBack();

            \Log::error('Error guardando propuesta', [
                'error'   => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
                'request' => $request->all(),
            ]);

            return back()->withErrors('Ocurrió un error al guardar la propuesta.');
        }
    }

    public function index()
    {
        $propuestas = Propuesta::with(['cliente', 'usuario'])
                        ->latest('created_at')
                        ->get();

        return view('propuesta.index', compact('propuestas'));
    }

    public function pdf(Propuesta $propuesta)
    {
        $propuesta->load([
            'cliente',
            'usuario',
            'productos.producto',
            'fichaTecnica',
            'tradeins',
        ]);

        $url = route('propuestas.show', $propuesta->id);

        $qr = base64_encode(
            \QrCode::format('svg')
                ->size(120)
                ->generate($url)
        );

        $pdfPropuesta = PDF::loadView('propuesta.pdf', compact('propuesta', 'qr'))
            ->setPaper('a4', 'portrait');

        $dirTemp = storage_path("app/public/temp");
        if (!is_dir($dirTemp)) {
            @mkdir($dirTemp, 0775, true);
        }

        $rutaPropuesta = "{$dirTemp}/propuesta_{$propuesta->id}.pdf";
        file_put_contents($rutaPropuesta, $pdfPropuesta->output());

        $rutaFicha = $propuesta->fichaTecnica?->archivo
            ? storage_path("app/public/" . $propuesta->fichaTecnica->archivo)
            : null;

        $pdf = new Fpdi();
        $archivos = [$rutaPropuesta];

        if ($rutaFicha && file_exists($rutaFicha)) {
            $archivos[] = $rutaFicha;
        }

        foreach ($archivos as $archivo) {
            $pageCount = $pdf->setSourceFile($archivo);

            for ($i = 1; $i <= $pageCount; $i++) {
                $tpl  = $pdf->importPage($i);
                $size = $pdf->getTemplateSize($tpl);

                $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                $pdf->useTemplate($tpl);
            }
        }

        $rutaFinal = "{$dirTemp}/final_propuesta_{$propuesta->id}.pdf";
        $pdf->Output($rutaFinal, 'F');

        $clienteNombre = preg_replace('/[^A-Za-z0-9 _-]/', '', $propuesta->cliente->nombre);
        $nombreArchivo = "Cotización_{$propuesta->id}_{$clienteNombre}.pdf";

        return response()->download($rutaFinal, $nombreArchivo);
    }

    public function show(Propuesta $propuesta)
    {
        $propuesta->load([
            'cliente',
            'productos.producto',
            'usuario',
            'fichaTecnica',
            'tradeins',
        ]);

        $agrupados = $propuesta->productos->groupBy(function ($item) {
            return optional($item->producto)->tipo_equipo ?? 'Sin tipo';
        });

        $tiposEquipo = $agrupados->keys()->values()->all();
        $cantidades  = $agrupados->map->count()->values()->all();

        $productosOrdenados = $propuesta->productos
            ->sortByDesc(function ($item) {
                return (float) ($item->subtotal ?? 0);
            })
            ->map(function ($item) {
                $producto = $item->producto;

                return [
                    'nombre'   => $producto ? ($producto->tipo_equipo . ' ' . $producto->modelo) : 'Producto eliminado',
                    'subtotal' => (float) ($item->subtotal ?? 0),
                ];
            });

        $labels  = $productosOrdenados->pluck('nombre')->values();
        $valores = $productosOrdenados->pluck('subtotal')->values();

        return view('propuesta.show', compact('propuesta', 'tiposEquipo', 'cantidades', 'labels', 'valores'));
    }

    public function edit($id)
    {
        $propuesta = Propuesta::with([
                'productos.producto',
                'cliente',
                'pagosFinanciamiento',
                'fichaTecnica',
                'tradeins',
            ])
            ->findOrFail($id);

        $productos = Producto::all();
        $fichas    = FichaTecnica::all();
        $clientes  = Cliente::all();

        return view('propuesta.edit', compact('propuesta', 'productos', 'fichas', 'clientes'));
    }

    public function update(Request $request, $id)
    {
        \Log::info('Inicio método update - request recibido', $request->all());
        \Log::info('Valor de productos_json', ['productos_json' => $request->productos_json]);

        $request->validate([
            'cliente_id'          => 'required|exists:clientes,id',
            'subtotal'            => 'required|numeric',
            'total'               => 'required|numeric',
            'productos_json'      => 'required|json',
            'pagos_json'          => 'nullable|json',
            'equipos_cuenta_json' => 'nullable|json',
            'ficha_tecnica_id'    => 'nullable|exists:fichas_tecnicas,id',
            'lugar'               => 'required|string',
        ]);

        try {
            DB::beginTransaction();

            $propuesta = Propuesta::findOrFail($id);

            $propuesta->update([
                'cliente_id'       => $request->cliente_id,
                'lugar'            => $request->lugar,
                'nota'             => $request->nota,
                'subtotal'         => $request->subtotal,
                'descuento'        => $request->descuento ?? 0,
                'envio'            => $request->envio ?? 0,
                'iva'              => $request->iva ?? 0,
                'total'            => $request->total,
                'plan'             => $request->plan,
                'ficha_tecnica_id' => $request->ficha_tecnica_id,
            ]);

            \Log::info('Propuesta actualizada', ['id' => $propuesta->id]);

            // ================== PRODUCTOS ==================
            $propuesta->productos()->delete();

            $productos = json_decode($request->productos_json, true) ?: [];
            \Log::info('Productos decodificados', ['productos' => $productos]);

            foreach ($productos as $p) {
                $productoId = (int)($p['producto_id'] ?? 0);
                $cantidad   = max(1, (int)($p['cantidad'] ?? 1));

                $esRegalo = (int)($p['es_regalo'] ?? 0) === 1;

                $precioUnitInput  = (float)($p['precio_unitario'] ?? 0);
                $sobreprecioInput = (float)($p['sobreprecio'] ?? 0);

                $precioUnit  = $esRegalo ? 0.0 : $precioUnitInput;
                $sobreprecio = $esRegalo ? 0.0 : $sobreprecioInput;
                $subtotal    = $esRegalo ? 0.0 : ($cantidad * ($precioUnit + $sobreprecio));

                $propuesta->productos()->create([
                    'producto_id'     => $productoId,
                    'cantidad'        => $cantidad,
                    'precio_unitario' => $precioUnit,
                    'subtotal'        => $subtotal,
                    'sobreprecio'     => $sobreprecio,
                    'es_regalo'       => $esRegalo ? 1 : 0,
                ]);
            }

            // ================== PAGOS FINANCIAMIENTO ==================
            $procesados = false;
            $idsConservados = [];

            $pf = $request->input('pagos_financiamiento', null);

            if (is_array($pf)) {
                $procesados = true;

                foreach ($pf as $key => $row) {
                    if (!is_array($row)) {
                        continue;
                    }

                    $isNew = str_starts_with((string)$key, 'nuevo_');
                    $id    = $isNew ? null : (int)$key;

                    $eliminar    = (string)($row['eliminar'] ?? '0') === '1';
                    $descripcion = (string)($row['descripcion'] ?? '');
                    $fecha       = (string)($row['fecha_pago'] ?? '');
                    $monto       = (float)($row['monto'] ?? 0);

                    if ($eliminar && !$isNew && $id) {
                        PagoFinanciamientoPropuesta::where('propuesta_id', $propuesta->id)
                            ->where('id', $id)
                            ->delete();

                        continue;
                    }

                    if (!$fecha) {
                        continue;
                    }

                    if (!$isNew && $id) {
                        $pagoExistente = PagoFinanciamientoPropuesta::where('propuesta_id', $propuesta->id)
                            ->where('id', $id)
                            ->first();

                        if ($pagoExistente) {
                            $pagoExistente->update([
                                'descripcion' => $descripcion,
                                'fecha_pago'  => Carbon::parse($fecha),
                                'monto'       => $monto,
                            ]);

                            $idsConservados[] = $pagoExistente->id;
                        } else {
                            $nuevo = PagoFinanciamientoPropuesta::create([
                                'propuesta_id' => $propuesta->id,
                                'descripcion'  => $descripcion,
                                'fecha_pago'   => Carbon::parse($fecha),
                                'monto'        => $monto,
                            ]);

                            $idsConservados[] = $nuevo->id;
                        }
                    } else {
                        $nuevo = PagoFinanciamientoPropuesta::create([
                            'propuesta_id' => $propuesta->id,
                            'descripcion'  => $descripcion,
                            'fecha_pago'   => Carbon::parse($fecha),
                            'monto'        => $monto,
                        ]);

                        $idsConservados[] = $nuevo->id;
                    }
                }
            } elseif ($request->filled('pagos_json')) {
                $procesados = true;

                $pagos = json_decode($request->pagos_json, true);
                \Log::info('Pagos decodificados (fallback pagos_json)', ['pagos' => $pagos]);

                if (is_array($pagos)) {
                    foreach ($pagos as $pago) {
                        $fecha = $pago['fecha_pago'] ?? $pago['mes'] ?? null;
                        $monto = $pago['monto'] ?? $pago['cuota'] ?? 0;

                        if (!$fecha) {
                            continue;
                        }

                        if (!empty($pago['id'])) {
                            $pagoExistente = PagoFinanciamientoPropuesta::where('propuesta_id', $propuesta->id)
                                ->where('id', $pago['id'])
                                ->first();

                            if ($pagoExistente) {
                                $pagoExistente->update([
                                    'descripcion' => $pago['descripcion'] ?? '',
                                    'fecha_pago'  => Carbon::parse($fecha),
                                    'monto'       => (float)$monto,
                                ]);

                                $idsConservados[] = $pagoExistente->id;
                            }
                        } else {
                            $nuevoPago = PagoFinanciamientoPropuesta::create([
                                'propuesta_id' => $propuesta->id,
                                'descripcion'  => $pago['descripcion'] ?? '',
                                'fecha_pago'   => Carbon::parse($fecha),
                                'monto'        => (float)$monto,
                            ]);

                            $idsConservados[] = $nuevoPago->id;
                        }
                    }
                }
            } else {
                \Log::info('Sin cambios en pagos (no llegó pagos_financiamiento ni pagos_json)');
            }

            if ($procesados) {
                PagoFinanciamientoPropuesta::where('propuesta_id', $propuesta->id)
                    ->when(!empty($idsConservados), function ($query) use ($idsConservados) {
                        $query->whereNotIn('id', $idsConservados);
                    })
                    ->delete();
            }

            \Log::info('Pagos actualizados correctamente', [
                'procesados'     => $procesados,
                'idsConservados' => $idsConservados,
            ]);

            // ================== EQUIPOS A CUENTA ==================
            $propuesta->tradeins()->delete();

            if ($request->filled('equipos_cuenta_json')) {
                $equiposCuenta = json_decode($request->equipos_cuenta_json, true) ?: [];
                \Log::info('Equipos a cuenta decodificados (update)', ['equipos_cuenta' => $equiposCuenta]);

                if (is_array($equiposCuenta)) {
                    foreach ($equiposCuenta as $eq) {
                        $propuesta->tradeins()->create([
                            'tipo_equipo'    => $eq['tipo_equipo']    ?? null,
                            'marca'          => $eq['marca']          ?? null,
                            'modelo'         => $eq['modelo']         ?? null,
                            'numero_serie'   => $eq['numero_serie']   ?? null,
                            'valor_a_cuenta' => $eq['valor_a_cuenta'] ?? 0,
                        ]);
                    }
                }
            }

            DB::commit();

            return redirect()
                ->route('propuestas.show', $propuesta->id)
                ->with('success', 'Propuesta actualizada exitosamente.');
        } catch (\Throwable $e) {
            DB::rollBack();

            \Log::error('Error actualizando propuesta', [
                'error'   => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
                'request' => $request->all(),
            ]);

            return back()->withErrors('Ocurrió un error al actualizar la propuesta.');
        }
    }

    /* ======================= PDF PARA WHATSAPP ======================= */

    private function buildPropuestaPdf(Propuesta $propuesta): array
    {
        $propuesta->load([
            'cliente',
            'usuario',
            'productos.producto',
            'fichaTecnica',
            'tradeins',
        ]);

        $url = route('propuestas.show', $propuesta->id);
        $qr  = base64_encode(\QrCode::format('svg')->size(120)->generate($url));

        $pdfPropuesta = \PDF::loadView('propuesta.pdf', compact('propuesta', 'qr'))
            ->setPaper('a4', 'portrait');

        $dirTemp = storage_path('app/public/temp');
        if (!is_dir($dirTemp)) {
            @mkdir($dirTemp, 0775, true);
        }

        $rutaPropuesta = $dirTemp . "/propuesta_{$propuesta->id}.pdf";
        file_put_contents($rutaPropuesta, $pdfPropuesta->output());

        $rutaFicha = $propuesta->fichaTecnica?->archivo
            ? storage_path('app/public/' . $propuesta->fichaTecnica->archivo)
            : null;

        $pdf = new Fpdi();
        $archivos = [$rutaPropuesta];

        if ($rutaFicha && file_exists($rutaFicha)) {
            $archivos[] = $rutaFicha;
        }

        foreach ($archivos as $archivo) {
            $pageCount = $pdf->setSourceFile($archivo);

            for ($i = 1; $i <= $pageCount; $i++) {
                $tpl  = $pdf->importPage($i);
                $size = $pdf->getTemplateSize($tpl);

                $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                $pdf->useTemplate($tpl);
            }
        }

        $rutaFinal = $dirTemp . "/final_propuesta_{$propuesta->id}.pdf";
        $pdf->Output($rutaFinal, 'F');

        $clienteNombre = preg_replace('/[^A-Za-z0-9 _-]/', '', $propuesta->cliente->nombre);
        $filename = "Cotizacion_{$propuesta->id}_{$clienteNombre}.pdf";

        return [$rutaFinal, $filename];
    }

    public function sendWhatsappTemplateRemision(Propuesta $propuesta, Request $request, WhatsAppService $wa)
    {
        $request->validate([
            'frase'         => ['nullable', 'string', 'max:200'],
            'site_suffix'   => ['nullable', 'string', 'max:200'],
            'template_name' => ['required', 'string', 'max:128'],
            'template_lang' => ['required', 'string', 'max:12'],
        ]);

        $propuesta->load('cliente');

        $to = $wa::normalizeMsisdn((string) ($propuesta->cliente->telefono ?? ''));
        if (!$to) {
            return back()->with('wa_info', 'El cliente no tiene teléfono válido.');
        }

        try {
            [$path, $filename] = $this->buildPropuestaPdf($propuesta);
        } catch (\Throwable $e) {
            \Log::error('PDF build fail', ['e' => $e->getMessage()]);

            return back()->with('wa_info', 'No se pudo generar el PDF.');
        }

        $upload  = $wa->uploadMediaPath($path, $filename, 'application/pdf');
        $uJson   = $upload->json();
        $mediaId = data_get($uJson, 'id');

        if (!$upload->successful() || !$mediaId) {
            \Log::warning('WA_MEDIA_UPLOAD_FAIL', ['resp' => $uJson]);

            return back()
                ->with('wa_info', 'No se pudo subir el PDF a WhatsApp.')
                ->with('wa_fail', [$uJson]);
        }

        $templateName = (string) $request->input('template_name');
        $langCode     = (string) $request->input('template_lang');

        $clienteNombre = trim((string) $propuesta->cliente->nombre . ' ' . (string) ($propuesta->cliente->apellido ?? ''));

        $fraseInput = trim((string) $request->input('frase', ''));
        $frase      = ($fraseInput === '') ? null : $fraseInput;

        $siteInput  = trim((string) $request->input('site_suffix', ''));
        $siteSuffix = ($siteInput === '') ? null : $siteInput;

        $resp = $wa->sendTemplateWithDocument(
            to: $to,
            templateName: $templateName,
            langCode: $langCode,
            mediaId: $mediaId,
            filename: $filename,
            clienteNombre: $clienteNombre,
            frase: $frase,
            btn0UrlSuffix: $siteSuffix,
            btn1UrlSuffix: null
        );

        $json  = $resp->json();
        $wamid = data_get($json, 'messages.0.id');

        if ($resp->successful() && $wamid) {
            \Log::info('WA_OK_TEMPLATE_DOC', [
                'to'    => $to,
                'wamid' => $wamid,
                'tpl'   => $templateName,
                'lang'  => $langCode,
            ]);

            try {
                if (method_exists($propuesta, 'seguimientos')) {
                    $propuesta->seguimientos()->create([
                        'descripcion' => "WA enviado (tpl: {$templateName}, lang: {$langCode}, wamid: {$wamid})",
                        'user_id'     => auth()->id(),
                    ]);
                }
            } catch (\Throwable $e) {
                \Log::warning('WA_SEGUIMIENTO_SAVE_FAIL', ['e' => $e->getMessage()]);
            }

            return back()->with('wa_success', "Plantilla enviada por WhatsApp ✅ ({$templateName} · {$langCode})");
        }

        $code   = data_get($json, 'error.code');
        $detail = data_get($json, 'error.message') ?: data_get($json, 'error.error_data.details');

        $suggestions = $wa->groupTemplatesByName($wa->fetchTemplatesSmart(200));

        \Log::warning('WA_FAIL_TEMPLATE_DOC', [
            'to'   => $to,
            'resp' => $json,
            'tpl'  => $templateName,
            'lang' => $langCode,
        ]);

        return back()
            ->with('wa_info', 'No se pudo enviar la plantilla. Verifica nombre/idioma tal cual existen en tu WABA.')
            ->with('wa_fail', [[
                'n'              => $to,
                'code'           => $code,
                'detail'         => $detail,
                'raw'            => $json,
                'template_tried' => $templateName,
                'lang_tried'     => $langCode,
            ]])
            ->with('wa_templates_grouped', $suggestions);
    }

    public function whatsappTemplates(WhatsAppService $wa)
    {
        $tpls  = $wa->fetchTemplatesSmart(200);
        $group = $wa->groupTemplatesByName($tpls);

        return response()->json([
            'ok'        => true,
            'count'     => count($tpls),
            'grouped'   => $group,
            'templates' => $tpls,
        ]);
    }

    public function whatsappDebug(WhatsAppService $wa)
    {
        return response()->json([
            'phone_meta'       => $wa->getPhoneNumberMeta(),
            'templates_sample' => array_slice($wa->fetchTemplatesSmart(50), 0, 10),
            'note'             => 'Confirma que whatsapp_business_account.id == WHATSAPP_BUSINESS_ACCOUNT_ID en tu .env',
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $request->validate([
            'aprobacion_pin' => ['required', 'string'],
        ]);

        $expected = trim((string) config('app.aprobacion_pin'));
        $pin      = trim((string) $request->input('aprobacion_pin'));

        \Log::info('Intento de eliminación de propuesta', [
            'propuesta_id'            => $id,
            'pin_recibido_length'     => strlen($pin),
            'pin_configurado_length'  => strlen($expected),
            'pin_configurado_existe'  => $expected !== '',
        ]);

        if ($expected === '' || !hash_equals($expected, $pin)) {
            return redirect()
                ->route('propuestas.index')
                ->with('error', 'PIN incorrecto. No se eliminó la cotización.');
        }

        try {
            DB::beginTransaction();

            $propuesta = Propuesta::with([
                'productos',
                'pagos',
                'pagosFinanciamiento',
                'tradeins',
            ])->findOrFail($id);

            if (method_exists($propuesta, 'productos')) {
                $propuesta->productos()->delete();
            }

            if (method_exists($propuesta, 'pagos')) {
                $propuesta->pagos()->delete();
            }

            if (method_exists($propuesta, 'pagosFinanciamiento')) {
                $propuesta->pagosFinanciamiento()->delete();
            }

            if (method_exists($propuesta, 'tradeins')) {
                $propuesta->tradeins()->delete();
            }

            $propuesta->delete();

            DB::commit();

            \Log::info('Propuesta eliminada correctamente', [
                'id' => $id,
            ]);

            return redirect()
                ->route('propuestas.index')
                ->with('success', 'La cotización se eliminó correctamente.');
        } catch (\Throwable $e) {
            DB::rollBack();

            \Log::error('Error al eliminar propuesta', [
                'id'    => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()
                ->route('propuestas.index')
                ->with('error', 'No se pudo eliminar la cotización.');
        }
    }
}