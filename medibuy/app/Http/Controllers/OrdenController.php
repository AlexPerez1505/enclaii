<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Orden;
use App\Models\Pago;
use App\Models\Registro;
use App\Models\Servicio;
use App\Models\User;
use App\Services\AiDynamicChecklistService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use PDF;

class OrdenController extends Controller
{
    private function usuarioLimitado(): bool
    {
        return (int) Auth::id() === 19;
    }

    private function validarAccesoOrdenUsuario19(Orden $orden): void
    {
        if ($this->usuarioLimitado() && (int) ($orden->user_id ?? 0) !== (int) Auth::id()) {
            abort(403, 'No tienes permiso para ver esta orden.');
        }
    }

    private function aplicarFiltroOrdenUsuario19($query)
    {
        if ($this->usuarioLimitado()) {
            $query->where('user_id', Auth::id());
        }

        return $query;
    }

    private function validarAccesoServicioUsuario19(Servicio $servicio): void
    {
        if (!$this->usuarioLimitado()) {
            return;
        }

        if (Schema::hasColumn($servicio->getTable(), 'user_id') && (int) ($servicio->user_id ?? 0) !== (int) Auth::id()) {
            abort(403, 'No tienes permiso para usar este servicio.');
        }
    }

    public function index()
    {
        $ordenes = $this->aplicarFiltroOrdenUsuario19(Orden::with('cliente', 'tecnico'))
            ->latest('id')
            ->paginate(1500)
            ->through(function ($o) {
                $path = $o->foto_equipo ? ltrim($o->foto_equipo, '/') : null;
                $o->foto_url = $path ? Storage::disk('public')->url($path) : null;

                $path2 = $o->foto_equipo_2 ? ltrim($o->foto_equipo_2, '/') : null;
                $o->foto_url_2 = $path2 ? Storage::disk('public')->url($path2) : null;

                $path3 = $o->foto_equipo_3 ? ltrim($o->foto_equipo_3, '/') : null;
                $o->foto_url_3 = $path3 ? Storage::disk('public')->url($path3) : null;

                return $o;
            });

        return view('ordenes.index', compact('ordenes'));
    }

    public function create()
    {
        return view('ordenes.create', [
            'clientes'         => Cliente::orderBy('nombre')->get(),
            'usuariosServicio' => User::orderBy('name')->get(['id', 'name']),
            'servicio_id'      => request('servicio_id'),
        ]);
    }

    public function show($id)
    {
        $orden = Orden::with('cliente', 'user', 'tecnico')
            ->findOrFail((int) $id);

        $this->validarAccesoOrdenUsuario19($orden);

        if (empty($orden->codigo_validacion_servicio)) {
            $orden->codigo_validacion_servicio = $this->generarCodigoValidacionServicio();
            $orden->save();
        }

        $foto_url   = $this->buildFotoUrl($orden->foto_equipo);
        $foto_url_2 = $this->buildFotoUrl($orden->foto_equipo_2);
        $foto_url_3 = $this->buildFotoUrl($orden->foto_equipo_3);

        $servicioVinculado = Servicio::where('orden_id', $orden->id)
            ->orderByDesc('id')
            ->first();

        return view('ordenes.show', compact(
            'orden',
            'foto_url',
            'foto_url_2',
            'foto_url_3',
            'servicioVinculado'
        ));
    }

    public function edit($id)
    {
        $orden = Orden::with('cliente', 'tecnico')->findOrFail((int) $id);

        $this->validarAccesoOrdenUsuario19($orden);

        $clientes = Cliente::orderBy('nombre')->orderBy('apellido')->get();
        $usuariosServicio = User::orderBy('name')->get(['id', 'name']);

        if (empty($orden->codigo_validacion_servicio)) {
            $orden->codigo_validacion_servicio = $this->generarCodigoValidacionServicio();
            $orden->save();
        }

        $foto_url   = $this->buildFotoUrl($orden->foto_equipo);
        $foto_url_2 = $this->buildFotoUrl($orden->foto_equipo_2);
        $foto_url_3 = $this->buildFotoUrl($orden->foto_equipo_3);

        return view('ordenes.edit', compact(
            'orden',
            'clientes',
            'usuariosServicio',
            'foto_url',
            'foto_url_2',
            'foto_url_3'
        ));
    }

    public function aiChecklist(Request $request, AiDynamicChecklistService $ai)
    {
        $data = $request->validate([
            'equipo'        => ['nullable', 'string', 'max:180'],
            'nombre_equipo' => ['nullable', 'string', 'max:180'],
            'marca'         => ['nullable', 'string', 'max:120'],
            'modelo'        => ['nullable', 'string', 'max:120'],
            'numero_serie'  => ['nullable', 'string', 'max:140'],
            'observaciones' => ['nullable', 'string', 'max:5000'],
            'sintomas'      => ['nullable', 'string', 'max:5000'],
            'servicio'      => ['nullable', 'string', 'max:30'],
            'locale'        => ['nullable', 'string', 'max:5'],
        ]);

        $equipoTexto = trim((string)($data['equipo'] ?? $data['nombre_equipo'] ?? ''));
        if ($equipoTexto === '') {
            return response()->json([
                'ok' => false,
                'message' => 'Debes indicar un equipo o nombre de equipo.'
            ], 422);
        }

        $producto = null;

        if (!empty($data['numero_serie'])) {
            $producto = Registro::query()
                ->where('numero_serie', 'LIKE', '%' . $data['numero_serie'] . '%')
                ->first();
        }

        if (!$producto) {
            $query = Registro::query();

            if (!empty($equipoTexto)) {
                $query->where(function ($q) use ($equipoTexto) {
                    $q->where('tipo_equipo', 'LIKE', '%' . $equipoTexto . '%')
                      ->orWhere('descripcion', 'LIKE', '%' . $equipoTexto . '%');
                });
            }

            if (!empty($data['marca'])) {
                $query->where('marca', 'LIKE', '%' . $data['marca'] . '%');
            }

            if (!empty($data['modelo'])) {
                $query->where('modelo', 'LIKE', '%' . $data['modelo'] . '%');
            }

            $producto = $query->first();
        }

        $productoContext = null;
        $taxonomia = null;

        if ($producto) {
            $productoContext = [
                'id'             => $producto->id,
                'tipo_equipo'    => $producto->tipo_equipo ?? null,
                'subtipo_equipo' => $producto->subtipo_equipo ?? null,
                'marca'          => $producto->marca ?? null,
                'modelo'         => $producto->modelo ?? null,
                'numero_serie'   => $producto->numero_serie ?? null,
                'descripcion'    => $producto->descripcion ?? null,
                'anio'           => $producto->anio ?? null,
            ];

            $taxonomia = [
                'tipo_equipo'    => $producto->tipo_equipo ?? null,
                'subtipo_equipo' => $producto->subtipo_equipo ?? null,
            ];
        }

        $servicio = mb_strtolower(trim((string)($data['servicio'] ?? 'preventivo')), 'UTF-8');
        if (!in_array($servicio, ['preventivo', 'correctivo', 'mixto'], true)) {
            $servicio = 'preventivo';
        }

        $freeText = trim(implode(' ', array_filter([
            $equipoTexto,
            $data['marca'] ?? null,
            $data['modelo'] ?? null,
            $data['numero_serie'] ?? null,
        ])));

        $result = $ai->generateChecklist($freeText ?: 'equipo médico', [
            'locale'        => $data['locale'] ?? 'es',
            'servicio'      => $servicio,
            'equipo'        => $equipoTexto,
            'marca'         => $data['marca'] ?? null,
            'modelo'        => $data['modelo'] ?? null,
            'numero_serie'  => $data['numero_serie'] ?? null,
            'observaciones' => $data['observaciones'] ?? null,
            'sintomas'      => $data['sintomas'] ?? ($data['observaciones'] ?? null),
            'producto'      => $productoContext,
            'taxonomia'     => $taxonomia,
        ], [
            'return_meta' => true,
        ]);

        return response()->json($result);
    }

    public function store(Request $request, AiDynamicChecklistService $ai)
    {
        Log::info('==== ORDEN STORE INICIO ====', [
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'user_id' => Auth::id(),
            'content_type' => $request->header('Content-Type'),
            'accept' => $request->header('Accept'),
            'ip' => $request->ip(),
            'payload_except_files' => $request->except(['foto_equipo', 'foto_equipo_2', 'foto_equipo_3']),
            'has_foto_equipo' => $request->hasFile('foto_equipo'),
            'has_foto_equipo_2' => $request->hasFile('foto_equipo_2'),
            'has_foto_equipo_3' => $request->hasFile('foto_equipo_3'),
        ]);

        $data = $request->validate([
            'servicio_id'               => ['nullable', 'integer', 'exists:servicio,id'],
            'cliente_id'                => ['required', 'integer', 'exists:clientes,id'],
            'fecha_entrada'             => ['required', 'date'],
            'fecha_mantenimiento'       => ['required', 'date'],
            'tecnico_id'                => ['required', 'integer', 'exists:users,id'],
            'tipo_mantenimiento'        => ['required', Rule::in(['preventivo', 'correctivo', 'mixto'])],
            'equipo'                    => ['required', 'string', 'max:180'],
            'marca'                     => ['nullable', 'string', 'max:120'],
            'modelo'                    => ['nullable', 'string', 'max:120'],
            'numero_serie'              => ['nullable', 'string', 'max:140'],
            'observaciones'             => ['nullable', 'string', 'max:5000'],
            'proximo_mantenimiento'     => ['required', Rule::in([3, 6, 12])],
            'mto_preventivo'            => ['nullable', 'array'],
            'mto_preventivo.*.seccion'  => ['nullable', 'string', 'max:200'],
            'mto_preventivo.*.item'     => ['nullable', 'string', 'max:300'],
            'mto_preventivo.*.estatus'  => ['nullable', 'string', 'max:100'],
            'mto_realizado'             => ['nullable', 'array'],
            'mto_realizado.*'           => ['nullable', 'string', 'max:300'],

            'remision_cantidad'         => ['nullable', 'integer', 'min:1'],
            'remision_precio'           => ['nullable', 'numeric', 'min:0'],
            'remision_envio'            => ['nullable', 'numeric', 'min:0'],
            'remision_requiere_iva'     => ['nullable', 'boolean'],
            'remision_anticipo'         => ['nullable', 'numeric', 'min:0'],
            'remision_subtotal'         => ['nullable', 'numeric', 'min:0'],
            'remision_iva'              => ['nullable', 'numeric', 'min:0'],
            'remision_total'            => ['nullable', 'numeric', 'min:0'],
            'remision_total_pagar'      => ['nullable', 'numeric', 'min:0'],
            'remision_unidad'           => ['nullable', 'string', 'max:50'],
            'remision_descripcion'      => ['nullable', 'string', 'max:3000'],
            'remision_partidas'         => ['nullable'],

            'usar_ia'                   => ['nullable', 'boolean'],
            'foto_equipo'               => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'foto_equipo_2'             => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'foto_equipo_3'             => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $fotoPath1 = $this->storeUploadedPhoto($request, 'foto_equipo');
        $fotoPath2 = $this->storeUploadedPhoto($request, 'foto_equipo_2');
        $fotoPath3 = $this->storeUploadedPhoto($request, 'foto_equipo_3');

        $producto = $this->buscarProductoRelacionado($data);
        $productoContext = null;
        $taxonomia = null;

        if ($producto) {
            $productoContext = [
                'id'             => $producto->id,
                'tipo_equipo'    => $producto->tipo_equipo ?? null,
                'subtipo_equipo' => $producto->subtipo_equipo ?? null,
                'marca'          => $producto->marca ?? null,
                'modelo'         => $producto->modelo ?? null,
                'numero_serie'   => $producto->numero_serie ?? null,
                'descripcion'    => $producto->descripcion ?? null,
                'anio'           => $producto->anio ?? null,
            ];

            $taxonomia = [
                'tipo_equipo'    => $producto->tipo_equipo ?? null,
                'subtipo_equipo' => $producto->subtipo_equipo ?? null,
            ];
        }

        DB::beginTransaction();

        try {
            $orden = new Orden();

            $orden->cliente_id          = $data['cliente_id'];
            $orden->fecha_entrada       = $data['fecha_entrada'];
            $orden->fecha_mantenimiento = $data['fecha_mantenimiento'];
            $orden->tecnico_id          = (int) $data['tecnico_id'];

            if (Schema::hasColumn($orden->getTable(), 'tipo_mantenimiento')) {
                $orden->tipo_mantenimiento = $data['tipo_mantenimiento'];
            } elseif (Schema::hasColumn($orden->getTable(), 'tipo_servicio')) {
                $orden->tipo_servicio = $data['tipo_mantenimiento'];
            } elseif (Schema::hasColumn($orden->getTable(), 'servicio')) {
                $orden->servicio = $data['tipo_mantenimiento'];
            }

            $orden->equipo        = $data['equipo'];
            $orden->marca         = $data['marca'] ?? null;
            $orden->modelo        = $data['modelo'] ?? null;
            $orden->numero_serie  = $data['numero_serie'] ?? null;
            $orden->observaciones = $data['observaciones'] ?? null;

            $orden->foto_equipo   = $fotoPath1;
            $orden->foto_equipo_2 = $fotoPath2;
            $orden->foto_equipo_3 = $fotoPath3;

            $months   = (int) $data['proximo_mantenimiento'];
            $baseDate = Carbon::parse($data['fecha_mantenimiento']);
            $calcDate = (clone $baseDate)->addMonths($months);

            if (Schema::hasColumn($orden->getTable(), 'proximo_mantenimiento_fecha')) {
                $orden->proximo_mantenimiento_fecha = $calcDate;
            }

            if (Schema::hasColumn($orden->getTable(), 'proximo_mantenimiento')) {
                $col  = DB::selectOne("SHOW COLUMNS FROM `{$orden->getTable()}` LIKE 'proximo_mantenimiento'");
                $type = is_object($col) && isset($col->Type) ? strtolower((string) $col->Type) : '';

                if (preg_match('/int/', $type)) {
                    $orden->proximo_mantenimiento = $months;
                } else {
                    $orden->proximo_mantenimiento = $calcDate;
                }
            }

            $usarIa = (bool) ($data['usar_ia'] ?? false);
            $hayManualPreventivo = !empty($data['mto_preventivo']) && is_array($data['mto_preventivo']);
            $hayManualRealizado  = !empty($data['mto_realizado']) && is_array($data['mto_realizado']);

            $aiResult = null;

            if ($usarIa || (!$hayManualPreventivo && !$hayManualRealizado)) {
                try {
                    $freeText = trim(implode(' ', array_filter([
                        $data['equipo'] ?? null,
                        $data['marca'] ?? null,
                        $data['modelo'] ?? null,
                        $data['numero_serie'] ?? null,
                    ])));

                    $aiResult = $ai->generateChecklist($freeText ?: ($data['equipo'] ?? 'equipo médico'), [
                        'locale'        => 'es',
                        'servicio'      => $data['tipo_mantenimiento'],
                        'equipo'        => $data['equipo'],
                        'marca'         => $data['marca'] ?? null,
                        'modelo'        => $data['modelo'] ?? null,
                        'numero_serie'  => $data['numero_serie'] ?? null,
                        'observaciones' => $data['observaciones'] ?? null,
                        'sintomas'      => $data['observaciones'] ?? null,
                        'producto'      => $productoContext,
                        'taxonomia'     => $taxonomia,
                    ]);
                } catch (\Throwable $e) {
                    Log::warning('ORDEN STORE IA checklist fallo', [
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            if ($aiResult && !empty($aiResult['secciones'])) {
                $flat = [];
                foreach (($aiResult['secciones'] ?? []) as $sec) {
                    $secTitle = (string) ($sec['titulo'] ?? 'Sección');

                    foreach (($sec['items'] ?? []) as $it) {
                        $flat[] = [
                            'seccion' => $secTitle,
                            'item'    => (string) ($it['nombre'] ?? ''),
                            'estatus' => (string) ($it['resultado_sugerido'] ?? 'Revisado'),
                        ];
                    }
                }

                $orden->mto_preventivo = $flat;
                $orden->mto_realizado  = array_values(array_filter($aiResult['acciones_sugeridas'] ?? []));
            } else {
                $orden->mto_preventivo = $hayManualPreventivo ? array_values($data['mto_preventivo']) : [];
                $orden->mto_realizado  = $hayManualRealizado ? array_values($data['mto_realizado']) : [];
            }

            $partidasManual = $this->normalizePartidas($data['remision_partidas'] ?? null);
            $partidasIA = $this->normalizePartidas($aiResult['remision_partidas'] ?? null);

            $partidas = !empty($partidasManual) ? $partidasManual : $partidasIA;

            if (empty($partidas)) {
                $partidas = $this->fallbackPartidaUnica($data, $orden->mto_realizado ?? []);
            }

            $totales = $this->calcularTotalesRemision(
                $partidas,
                (float) ($data['remision_envio'] ?? 0),
                (float) ($data['remision_anticipo'] ?? 0),
                (bool) ($data['remision_requiere_iva'] ?? false)
            );

            $orden->remision_partidas     = $partidas;
            $orden->remision_cantidad     = $totales['cantidad'];
            $orden->remision_precio       = $totales['precio_base'];
            $orden->remision_envio        = $totales['envio'];
            $orden->remision_requiere_iva = $totales['requiere_iva'];
            $orden->remision_anticipo     = $totales['anticipo'];
            $orden->remision_subtotal     = $totales['subtotal'];
            $orden->remision_iva          = $totales['iva'];
            $orden->remision_total        = $totales['total'];
            $orden->remision_total_pagar  = $totales['pagar'];
            $orden->remision_unidad       = $data['remision_unidad'] ?? 'SERVICIO';

            if (!empty($data['remision_descripcion'])) {
                $orden->remision_descripcion = $data['remision_descripcion'];
            } else {
                $orden->remision_descripcion = $this->buildDescripcionRemision(
                    $data['tipo_mantenimiento'],
                    $data['equipo'],
                    $data['marca'] ?? '',
                    $data['modelo'] ?? '',
                    $data['numero_serie'] ?? '',
                    $orden->mto_realizado ?? []
                );
            }

            $orden->user_id = Auth::id();
            $orden->codigo_validacion_servicio = $this->generarCodigoValidacionServicio();
            $orden->save();

            $fechaPagoBase = $orden->fecha_entrada ?? now();

            $montoPendiente = (float) ($orden->remision_total_pagar ?? 0);
            if ($montoPendiente > 0) {
                Pago::create([
                    'orden_id'    => $orden->id,
                    'monto'       => $montoPendiente,
                    'fecha_pago'  => $fechaPagoBase,
                    'metodo_pago' => 'pendiente',
                    'aprobado'    => false,
                    'es_anticipo' => false,
                ]);
            }

            $montoAnticipo = (float) ($orden->remision_anticipo ?? 0);
            if ($montoAnticipo > 0) {
                Pago::create([
                    'orden_id'    => $orden->id,
                    'monto'       => $montoAnticipo,
                    'fecha_pago'  => $fechaPagoBase,
                    'metodo_pago' => 'anticipo',
                    'aprobado'    => true,
                    'es_anticipo' => true,
                ]);
            }

            if (!empty($data['servicio_id'])) {
                $sid = (int) $data['servicio_id'];
                $serv = Servicio::find($sid);

                if ($serv) {
                    $this->validarAccesoServicioUsuario19($serv);
                    if (Schema::hasColumn('servicio', 'orden_id')) {
                        $serv->orden_id = $orden->id;
                    }

                    if (Schema::hasColumn('servicio', 'orden_validada_at')) {
                        $serv->orden_validada_at = now();
                    }

                    if (Schema::hasColumn('servicio', 'estado_proceso')) {
                        $serv->estado_proceso = 'os_validada';
                    }

                    $serv->save();
                }
            }

            DB::commit();

            return redirect()
                ->route('ordenes.show', $orden->id)
                ->with('ok', "Orden #{$orden->id} creada correctamente. Código de validación: {$orden->codigo_validacion_servicio}");
        } catch (\Throwable $e) {
            DB::rollBack();

            foreach ([$fotoPath1, $fotoPath2, $fotoPath3] as $path) {
                if (!empty($path)) {
                    try {
                        Storage::disk('public')->delete($path);
                    } catch (\Throwable $x) {
                    }
                }
            }

            Log::error('==== ORDEN STORE FAIL ====', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'No se pudo crear la orden. Revisa logs.');
        }
    }

    public function update(Request $request, AiDynamicChecklistService $ai, $id)
    {
        $orden = Orden::findOrFail((int) $id);
        $this->validarAccesoOrdenUsuario19($orden);

        $tecnicoActual = $orden->tecnico_id ?: Auth::id();

        $data = $request->validate([
            'cliente_id'                => ['required', 'integer', 'exists:clientes,id'],
            'fecha_entrada'             => ['required', 'date'],
            'fecha_mantenimiento'       => ['required', 'date'],
            'tecnico_id'                => ['nullable', 'integer', 'exists:users,id'],
            'tipo_mantenimiento'        => ['required', Rule::in(['preventivo', 'correctivo', 'mixto'])],
            'equipo'                    => ['required', 'string', 'max:180'],
            'marca'                     => ['nullable', 'string', 'max:120'],
            'modelo'                    => ['nullable', 'string', 'max:120'],
            'numero_serie'              => ['nullable', 'string', 'max:140'],
            'observaciones'             => ['nullable', 'string', 'max:5000'],
            'proximo_mantenimiento'     => ['required', Rule::in([3, 6, 12])],

            'mto_preventivo'            => ['nullable', 'array'],
            'mto_realizado'             => ['nullable', 'array'],

            'remision_envio'            => ['nullable', 'numeric', 'min:0'],
            'remision_anticipo'         => ['nullable', 'numeric', 'min:0'],
            'remision_requiere_iva'     => ['nullable', 'boolean'],
            'remision_unidad'           => ['nullable', 'string', 'max:50'],
            'remision_descripcion'      => ['nullable', 'string', 'max:3000'],
            'remision_partidas'         => ['nullable'],

            'usar_ia'                   => ['nullable', 'boolean'],

            'foto_equipo'               => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'foto_equipo_2'             => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'foto_equipo_3'             => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],

            'quitar_foto'               => ['nullable', 'boolean'],
            'quitar_foto_2'             => ['nullable', 'boolean'],
            'quitar_foto_3'             => ['nullable', 'boolean'],
        ]);

        DB::beginTransaction();

        try {
            $this->handlePhotoUpdate($request, $orden, 'foto_equipo', 'quitar_foto');
            $this->handlePhotoUpdate($request, $orden, 'foto_equipo_2', 'quitar_foto_2');
            $this->handlePhotoUpdate($request, $orden, 'foto_equipo_3', 'quitar_foto_3');

            $orden->cliente_id          = (int) $data['cliente_id'];
            $orden->fecha_entrada       = $data['fecha_entrada'];
            $orden->fecha_mantenimiento = $data['fecha_mantenimiento'];
            $orden->tecnico_id          = (int) ($data['tecnico_id'] ?? $tecnicoActual);

            if (Schema::hasColumn($orden->getTable(), 'tipo_mantenimiento')) {
                $orden->tipo_mantenimiento = $data['tipo_mantenimiento'];
            } elseif (Schema::hasColumn($orden->getTable(), 'tipo_servicio')) {
                $orden->tipo_servicio = $data['tipo_mantenimiento'];
            } elseif (Schema::hasColumn($orden->getTable(), 'servicio')) {
                $orden->servicio = $data['tipo_mantenimiento'];
            }

            $orden->equipo        = $data['equipo'];
            $orden->marca         = $data['marca'] ?? null;
            $orden->modelo        = $data['modelo'] ?? null;
            $orden->numero_serie  = $data['numero_serie'] ?? null;
            $orden->observaciones = $data['observaciones'] ?? null;

            if (empty($orden->codigo_validacion_servicio)) {
                $orden->codigo_validacion_servicio = $this->generarCodigoValidacionServicio();
            }

            $months   = (int) $data['proximo_mantenimiento'];
            $baseDate = Carbon::parse($data['fecha_mantenimiento']);
            $calcDate = (clone $baseDate)->addMonths($months);

            if (Schema::hasColumn($orden->getTable(), 'proximo_mantenimiento_fecha')) {
                $orden->proximo_mantenimiento_fecha = $calcDate;
            }

            if (Schema::hasColumn($orden->getTable(), 'proximo_mantenimiento')) {
                $col  = DB::selectOne("SHOW COLUMNS FROM `{$orden->getTable()}` LIKE 'proximo_mantenimiento'");
                $type = is_object($col) && isset($col->Type) ? strtolower((string) $col->Type) : '';

                if (str_contains($type, 'int')) {
                    $orden->proximo_mantenimiento = $months;
                } else {
                    $orden->proximo_mantenimiento = $calcDate->format('Y-m-d H:i:s');
                }
            }

            $partidas = $this->normalizePartidas($data['remision_partidas'] ?? null);
            if (empty($partidas)) {
                $partidas = $this->normalizePartidas($orden->remision_partidas ?? []);
            }
            if (empty($partidas)) {
                $partidas = $this->fallbackPartidaUnica($data, $data['mto_realizado'] ?? $orden->mto_realizado ?? []);
            }

            $totales = $this->calcularTotalesRemision(
                $partidas,
                (float) ($data['remision_envio'] ?? $orden->remision_envio ?? 0),
                (float) ($data['remision_anticipo'] ?? $orden->remision_anticipo ?? 0),
                (bool) ($request->boolean('remision_requiere_iva') || $orden->remision_requiere_iva)
            );

            $orden->remision_partidas     = $partidas;
            $orden->remision_cantidad     = $totales['cantidad'];
            $orden->remision_precio       = $totales['precio_base'];
            $orden->remision_envio        = $totales['envio'];
            $orden->remision_requiere_iva = $totales['requiere_iva'];
            $orden->remision_anticipo     = $totales['anticipo'];
            $orden->remision_subtotal     = $totales['subtotal'];
            $orden->remision_iva          = $totales['iva'];
            $orden->remision_total        = $totales['total'];
            $orden->remision_total_pagar  = $totales['pagar'];
            $orden->remision_unidad       = $data['remision_unidad'] ?? $orden->remision_unidad ?? 'SERVICIO';

            if (!empty($data['remision_descripcion'])) {
                $orden->remision_descripcion = $data['remision_descripcion'];
            } elseif (empty($orden->remision_descripcion)) {
                $orden->remision_descripcion = $this->buildDescripcionRemision(
                    $data['tipo_mantenimiento'],
                    $data['equipo'],
                    $data['marca'] ?? '',
                    $data['modelo'] ?? '',
                    $data['numero_serie'] ?? '',
                    $data['mto_realizado'] ?? $orden->mto_realizado ?? []
                );
            }

            if (!empty($data['mto_preventivo'])) {
                $orden->mto_preventivo = array_values($data['mto_preventivo']);
            }

            if (!empty($data['mto_realizado'])) {
                $orden->mto_realizado = array_values($data['mto_realizado']);
            }

            $orden->user_id = $orden->user_id ?: Auth::id();
            $orden->save();

            $montoPendiente = (float) ($orden->remision_total_pagar ?? 0);

            $p = Pago::where('orden_id', $orden->id)
                ->where('metodo_pago', 'pendiente')
                ->where('aprobado', false)
                ->orderByDesc('id')
                ->first();

            if ($p) {
                $p->monto = $montoPendiente;
                $p->fecha_pago = $orden->fecha_entrada ?? $p->fecha_pago ?? now();
                $p->save();
            } elseif ($montoPendiente > 0) {
                Pago::create([
                    'orden_id'    => $orden->id,
                    'monto'       => $montoPendiente,
                    'fecha_pago'  => $orden->fecha_entrada ?? now(),
                    'metodo_pago' => 'pendiente',
                    'aprobado'    => false,
                    'es_anticipo' => false,
                ]);
            }

            DB::commit();

            return redirect()
                ->route('ordenes.show', $orden->id)
                ->with('ok', "Orden #{$orden->id} actualizada correctamente. Código de validación: {$orden->codigo_validacion_servicio}");
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('ORDEN_UPDATE_FAIL', [
                'orden_id' => $orden->id ?? null,
                'error'    => $e->getMessage(),
                'trace'    => $e->getTraceAsString(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'No se pudo actualizar la orden. Revisa logs.');
        }
    }

    public function pdf(Orden $orden)
    {
        $this->validarAccesoOrdenUsuario19($orden);

        $orden->load('cliente', 'user', 'tecnico');

        $fotoDataUri1 = $this->buildFotoDataUri($orden->foto_equipo);
        $fotoDataUri2 = $this->buildFotoDataUri($orden->foto_equipo_2);
        $fotoDataUri3 = $this->buildFotoDataUri($orden->foto_equipo_3);

        $tecnicoName = optional($orden->tecnico)->name ?? optional($orden->user)->name ?? 'N/A';

        $data = [
            'orden'          => $orden,
            'mto_preventivo' => $orden->mto_preventivo ?? [],
            'mto_realizado'  => $orden->mto_realizado ?? [],
            'fotoDataUri'    => $fotoDataUri1,
            'fotoDataUri2'   => $fotoDataUri2,
            'fotoDataUri3'   => $fotoDataUri3,
            'tecnicoName'    => $tecnicoName,
        ];

        $pdf = PDF::setOptions(['isRemoteEnabled' => true])
            ->loadView('ordenes.pdf', $data)
            ->setPaper('letter', 'portrait');

        return $pdf->download("OS_{$orden->id}.pdf");
    }

    public function remisionPdf(Orden $orden)
    {
        $this->validarAccesoOrdenUsuario19($orden);

        $orden->load('cliente', 'user', 'tecnico');

        $remision             = new \stdClass();
        $remision->id         = $orden->id;
        $remision->cliente    = $orden->cliente;
        $remision->user       = $orden->user;
        $remision->usuario    = $orden->user;
        $remision->tecnico    = $orden->tecnico;
        $remision->created_at = $orden->created_at;

        $tipoMtto = mb_strtolower(trim((string)(
            $orden->tipo_mantenimiento
            ?? $orden->tipo_servicio
            ?? $orden->servicio
            ?? 'preventivo'
        )), 'UTF-8');

        $labelMtto = match ($tipoMtto) {
            'correctivo' => 'MANTENIMIENTO CORRECTIVO',
            'mixto'      => 'MANTENIMIENTO PREVENTIVO / CORRECTIVO',
            default      => 'MANTENIMIENTO PREVENTIVO',
        };

        $remision->tipo_servicio = $labelMtto . ' DE ' . mb_strtoupper($orden->equipo ?? 'EQUIPO MÉDICO', 'UTF-8');
        $remision->equipo        = $orden->equipo;
        $remision->marca         = $orden->marca;
        $remision->modelo        = $orden->modelo;
        $remision->numero_serie  = $orden->numero_serie;
        $remision->serie         = $orden->numero_serie;
        $remision->lugar         = optional($orden->cliente)->direccion ?? null;

        $partidas = $this->normalizePartidas($orden->remision_partidas ?? []);
        if (empty($partidas)) {
            $partidas = $this->fallbackPartidaUnica([
                'tipo_mantenimiento' => $tipoMtto,
                'equipo'             => $orden->equipo,
                'marca'              => $orden->marca,
                'modelo'             => $orden->modelo,
                'numero_serie'       => $orden->numero_serie,
            ], $orden->mto_realizado ?? []);
        }

        $envio     = (float) ($orden->remision_envio ?: 0);
        $anticipo  = (float) ($orden->remision_anticipo ?: 0);
        $aplicaIva = (bool) ($orden->remision_requiere_iva ?? false);

        $totales = $this->calcularTotalesRemision($partidas, $envio, $anticipo, $aplicaIva);

        $remision->envio       = $totales['envio'];
        $remision->subtotal    = $totales['subtotal'];
        $remision->iva         = $totales['iva'];
        $remision->total       = $totales['total'];
        $remision->anticipo    = $totales['anticipo'];
        $remision->total_pagar = $totales['pagar'];
        $remision->aplicar_iva = $aplicaIva;
        $remision->nota        = $orden->observaciones;
        $remision->codigo_validacion_servicio = $orden->codigo_validacion_servicio;

        // Descripción completa guardada en la orden
        $descripcionCompleta = $orden->remision_descripcion ?? null;

        $items = collect();

        foreach ($partidas as $partida) {
            $item = new \stdClass();
            $item->cantidad         = (float) ($partida['cantidad'] ?? 1);
            $item->unidad           = $partida['unidad'] ?? 'SERVICIO';
            $item->nombre_item      = $partida['item'] ?? 'PARTIDA';
            // FIX: usar remision_descripcion completa si la partida no tiene descripción propia
            $item->descripcion_item = !empty(trim((string) ($partida['descripcion'] ?? '')))
                ? $partida['descripcion']
                : ($descripcionCompleta ?? '');
            $item->importe_unitario = (float) ($partida['precio_unitario'] ?? 0);
            $item->subtotal         = (float) ($partida['importe'] ?? 0);
            $items->push($item);
        }

        if ($envio > 0) {
            $envioItem = new \stdClass();
            $envioItem->cantidad         = 1;
            $envioItem->unidad           = 'SERVICIO';
            $envioItem->nombre_item      = 'ENVÍO';
            $envioItem->descripcion_item = 'Cargo por envío';
            $envioItem->importe_unitario = $envio;
            $envioItem->subtotal         = $envio;
            $items->push($envioItem);
        }

        $remision->items = $items;

        $pdf = PDF::setOptions(['isRemoteEnabled' => true])
            ->loadView('ordenes.remision-pdf', compact('remision'))
            ->setPaper('letter', 'portrait');

        return $pdf->download("Remision_Mantenimiento_OS_{$orden->id}.pdf");
    }

    public function vincularServicio(Request $request, Orden $orden)
    {
        $this->validarAccesoOrdenUsuario19($orden);

        $data = $request->validate([
            'servicio_id' => ['required', 'integer', 'exists:servicio,id'],
        ], [
            'servicio_id.required' => 'Debes capturar el ID del servicio.',
            'servicio_id.exists'   => 'El servicio no existe.',
        ]);

        if (empty($orden->codigo_validacion_servicio)) {
            $orden->codigo_validacion_servicio = $this->generarCodigoValidacionServicio();
            $orden->save();
        }

        $servicio = Servicio::findOrFail((int) $data['servicio_id']);

        $this->validarAccesoServicioUsuario19($servicio);

        if (Schema::hasColumn('servicio', 'orden_id')) {
            $servicio->orden_id = $orden->id;
        }

        if (Schema::hasColumn('servicio', 'orden_validada_at')) {
            $servicio->orden_validada_at = now();
        }

        if (Schema::hasColumn('servicio', 'estado_proceso')) {
            $servicio->estado_proceso = 'os_validada';
        }

        $servicio->save();

        return redirect()
            ->route('ordenes.show', $orden->id)
            ->with('ok', "OS #{$orden->id} vinculada y validada en Servicio #{$servicio->id}. Código: {$orden->codigo_validacion_servicio}");
    }

    public function destroy(Request $request, $id)
    {
        $data = $request->validate([
            'pin' => ['required', 'regex:/^[0-9]{6}$/'],
        ], [
            'pin.required' => 'Debes capturar el PIN de aprobación.',
            'pin.regex'    => 'El PIN debe tener exactamente 6 dígitos numéricos.',
        ]);

        $entered  = trim((string) $data['pin']);
        $expected = (string) config('seguridad.aprobacion_pin');

        if ($expected === '') {
            return redirect()
                ->route('ordenes.index')
                ->with('error', 'No hay PIN configurado (APROBACION_PIN). No se puede eliminar.');
        }

        if (!hash_equals($expected, $entered)) {
            return redirect()
                ->route('ordenes.index')
                ->with('error', 'PIN incorrecto. No se pudo eliminar la orden.');
        }

        $ordenId = (int) $id;

        DB::beginTransaction();

        try {
            $orden = Orden::where('id', $ordenId)->lockForUpdate()->first();

            if ($orden) {
                $this->validarAccesoOrdenUsuario19($orden);
            }

            if (!$orden) {
                DB::rollBack();

                return redirect()
                    ->route('ordenes.index')
                    ->with('error', "La orden #{$ordenId} ya no existe o no se pudo localizar.");
            }

            foreach (['foto_equipo', 'foto_equipo_2', 'foto_equipo_3'] as $fotoField) {
                if (!empty($orden->{$fotoField})) {
                    try {
                        Storage::disk('public')->delete($orden->{$fotoField});
                    } catch (\Throwable $e) {
                    }
                }
            }

            $pagoSoft = in_array('Illuminate\\Database\\Eloquent\\SoftDeletes', class_uses(Pago::class));

            if ($pagoSoft) {
                Pago::withTrashed()->where('orden_id', $ordenId)->forceDelete();
            } else {
                Pago::where('orden_id', $ordenId)->delete();
            }

            Orden::where('id', $ordenId)->delete();

            DB::commit();

            return redirect()
                ->route('ordenes.index')
                ->with('ok', "La orden #{$ordenId} fue eliminada correctamente.");
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('ORDEN_DELETE_FAIL', [
                'orden_id' => $ordenId,
                'error' => $e->getMessage(),
            ]);

            return redirect()
                ->route('ordenes.index')
                ->with('error', 'No se pudo eliminar la orden.');
        }
    }

    private function buildFotoUrl(?string $path): ?string
    {
        if (empty($path)) {
            return null;
        }

        $rel = ltrim((string) $path, '/');

        try {
            if (Storage::disk('public')->exists($rel)) {
                return Storage::disk('public')->url($rel);
            }

            if (is_file(public_path('storage/' . $rel))) {
                return asset('storage/' . $rel);
            }
        } catch (\Throwable $e) {
        }

        return null;
    }

    private function buildFotoDataUri(?string $path): ?string
    {
        if (empty($path)) {
            return null;
        }

        $rel = ltrim((string) $path, '/');
        $candidatePublic  = public_path('storage/' . $rel);
        $candidateStorage = storage_path('app/public/' . $rel);

        $pathForRead = null;

        if (is_readable($candidatePublic)) {
            $pathForRead = $candidatePublic;
        } elseif (is_readable($candidateStorage)) {
            $pathForRead = $candidateStorage;
        }

        if (!$pathForRead) {
            return null;
        }

        try {
            $mime = function_exists('mime_content_type') ? mime_content_type($pathForRead) : null;

            if (!$mime) {
                $ext = strtolower(pathinfo($pathForRead, PATHINFO_EXTENSION));
                $mime = in_array($ext, ['jpg', 'jpeg']) ? 'image/jpeg'
                    : ($ext === 'png' ? 'image/png'
                    : ($ext === 'webp' ? 'image/webp' : 'application/octet-stream'));
            }

            $bytes = file_get_contents($pathForRead);

            if ($bytes === false) {
                return null;
            }

            return 'data:' . $mime . ';base64,' . base64_encode($bytes);
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function storeUploadedPhoto(Request $request, string $field): ?string
    {
        if (!$request->hasFile($field)) {
            return null;
        }

        return $request->file($field)->store('ordenes/fotos', 'public');
    }

    private function handlePhotoUpdate(Request $request, Orden $orden, string $fotoField, string $quitarField): void
    {
        $oldFoto = $orden->{$fotoField};

        if ($request->boolean($quitarField) && !empty($oldFoto)) {
            try {
                Storage::disk('public')->delete($oldFoto);
            } catch (\Throwable $e) {
            }

            $orden->{$fotoField} = null;
        }

        if ($request->hasFile($fotoField)) {
            $newPath = $request->file($fotoField)->store('ordenes/fotos', 'public');

            if (!empty($oldFoto)) {
                try {
                    Storage::disk('public')->delete($oldFoto);
                } catch (\Throwable $e) {
                }
            }

            $orden->{$fotoField} = $newPath;
        }
    }

    private function buscarProductoRelacionado(array $data)
    {
        $producto = null;

        if (!empty($data['numero_serie'])) {
            $producto = Registro::query()
                ->where('numero_serie', 'LIKE', '%' . $data['numero_serie'] . '%')
                ->first();
        }

        if (!$producto) {
            $query = Registro::query();

            if (!empty($data['equipo'])) {
                $query->where(function ($q) use ($data) {
                    $q->where('tipo_equipo', 'LIKE', '%' . $data['equipo'] . '%')
                      ->orWhere('descripcion', 'LIKE', '%' . $data['equipo'] . '%');
                });
            }

            if (!empty($data['marca'])) {
                $query->where('marca', 'LIKE', '%' . $data['marca'] . '%');
            }

            if (!empty($data['modelo'])) {
                $query->where('modelo', 'LIKE', '%' . $data['modelo'] . '%');
            }

            $producto = $query->first();
        }

        return $producto;
    }

    private function normalizePartidas($partidas): array
    {
        if (is_string($partidas)) {
            $decoded = json_decode($partidas, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $partidas = $decoded;
            }
        }

        if (!is_array($partidas)) {
            return [];
        }

        $clean = [];

        foreach ($partidas as $i => $row) {
            if (!is_array($row)) {
                continue;
            }

            $item        = trim((string) ($row['item'] ?? $row['nombre_item'] ?? ('Partida ' . ($i + 1))));
            $descripcion = trim((string) ($row['descripcion'] ?? $row['descripcion_item'] ?? ''));
            $unidad      = trim((string) ($row['unidad'] ?? 'SERVICIO'));
            $cantidad    = (float) ($row['cantidad'] ?? 1);
            $precio      = (float) ($row['precio_unitario'] ?? $row['importe_unitario'] ?? $row['precio'] ?? 0);

            if ($cantidad <= 0) {
                $cantidad = 1;
            }

            if ($precio < 0) {
                $precio = 0;
            }

            $importe = round($cantidad * $precio, 2);

            $clean[] = [
                'item'            => $item !== '' ? $item : ('Partida ' . ($i + 1)),
                'descripcion'     => $descripcion,
                'unidad'          => $unidad !== '' ? $unidad : 'SERVICIO',
                'cantidad'        => $cantidad,
                'precio_unitario' => round($precio, 2),
                'importe'         => $importe,
            ];
        }

        return array_values($clean);
    }

    private function fallbackPartidaUnica(array $data, array $acciones = []): array
    {
        $desc = $this->buildDescripcionRemision(
            $data['tipo_mantenimiento'] ?? 'preventivo',
            $data['equipo'] ?? 'Equipo médico',
            $data['marca'] ?? '',
            $data['modelo'] ?? '',
            $data['numero_serie'] ?? '',
            $acciones
        );

        $precio   = (float) ($data['remision_precio'] ?? 0);
        $cantidad = max(1, (int) ($data['remision_cantidad'] ?? 1));

        return [[
            'item'            => 'Partida 1',
            'descripcion'     => $desc,
            'unidad'          => $data['remision_unidad'] ?? 'SERVICIO',
            'cantidad'        => $cantidad,
            'precio_unitario' => round($precio, 2),
            'importe'         => round($cantidad * $precio, 2),
        ]];
    }

    private function buildDescripcionRemision(
        string $tipoMantenimiento,
        string $equipo,
        ?string $marca = null,
        ?string $modelo = null,
        ?string $serie = null,
        array $acciones = []
    ): string {
        $baseServicio = match (mb_strtolower(trim($tipoMantenimiento), 'UTF-8')) {
            'correctivo' => 'MANTENIMIENTO CORRECTIVO',
            'mixto'      => 'MANTENIMIENTO PREVENTIVO / CORRECTIVO',
            default      => 'MANTENIMIENTO PREVENTIVO',
        };

        $frase = $baseServicio;

        if (trim($equipo) !== '') {
            $frase .= ' A ' . mb_strtoupper(trim($equipo), 'UTF-8');
        }

        $mm = trim(
            (trim((string) $marca) ? mb_strtoupper(trim((string) $marca), 'UTF-8') : '') . ' ' .
            (trim((string) $modelo) ? mb_strtoupper(trim((string) $modelo), 'UTF-8') : '')
        );

        if ($mm !== '') {
            $frase .= ' ' . $mm;
        }

        if (trim((string) $serie) !== '') {
            $frase .= ' CON NÚMERO DE SERIE ' . mb_strtoupper(trim((string) $serie), 'UTF-8');
        }

        $acciones = array_values(array_filter(array_map(function ($a) {
            $a = trim((string) $a);
            $a = preg_replace('/^[\-\•\*]+/u', '', $a);
            return trim($a);
        }, $acciones)));

        if (!empty($acciones)) {
            $frase .= '. Acciones realizadas: ' . implode(', ', $acciones) . '.';
        } else {
            $frase .= '.';
        }

        return $frase;
    }

    private function calcularTotalesRemision(array $partidas, float $envio, float $anticipo, bool $requiereIva): array
    {
        $subtotalPartidas = 0;
        $cantidad = 0;

        foreach ($partidas as &$p) {
            $p['cantidad']        = (float) ($p['cantidad'] ?? 1);
            $p['precio_unitario'] = (float) ($p['precio_unitario'] ?? 0);
            $p['importe']         = round($p['cantidad'] * $p['precio_unitario'], 2);

            $subtotalPartidas += $p['importe'];
            $cantidad         += (int) $p['cantidad'];
        }
        unset($p);

        $subtotal = round($subtotalPartidas + $envio, 2);
        $iva      = $requiereIva ? round($subtotal * 0.16, 2) : 0.00;
        $total    = round($subtotal + $iva, 2);
        $pagar    = round(max(0, $total - $anticipo), 2);

        return [
            'cantidad'     => max(1, $cantidad),
            'precio_base'  => round($subtotalPartidas, 2),
            'envio'        => round($envio, 2),
            'anticipo'     => round($anticipo, 2),
            'requiere_iva' => $requiereIva ? 1 : 0,
            'subtotal'     => $subtotal,
            'iva'          => $iva,
            'total'        => $total,
            'pagar'        => $pagar,
        ];
    }

    private function generarCodigoValidacionServicio(): string
    {
        return strtoupper(Str::random(10));
    }
}