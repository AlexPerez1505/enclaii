<?php

namespace App\Http\Controllers;

use App\Models\Movimiento;
use App\Models\Orden;
use App\Models\Servicio;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ServicioController extends Controller
{
    private function usuarioLimitado(): bool
    {
        return (int) Auth::id() === 19;
    }

    private function tablaServicio(): string
    {
        return (new Servicio())->getTable();
    }

    private function tieneColumnaServicio(string $columna): bool
    {
        try {
            return Schema::hasColumn($this->tablaServicio(), $columna);
        } catch (\Throwable $e) {
            return false;
        }
    }

    private function aplicarFiltroUsuario19($query)
    {
        if ($this->usuarioLimitado() && $this->tieneColumnaServicio('user_id')) {
            $query->where('user_id', Auth::id());
        }

        return $query;
    }

    private function validarAccesoServicioUsuario19(Servicio $servicio): void
    {
        if (!$this->usuarioLimitado()) {
            return;
        }

        if (!$this->tieneColumnaServicio('user_id')) {
            abort(403, 'No tienes permiso para ver este registro. Falta la columna user_id en la tabla de servicios.');
        }

        if ((int) ($servicio->user_id ?? 0) !== (int) Auth::id()) {
            abort(403, 'No tienes permiso para ver este registro.');
        }
    }

    private function validarAccesoOrdenUsuario19(Orden $orden): void
    {
        if ((int) Auth::id() === 19 && (int) ($orden->user_id ?? 0) !== (int) Auth::id()) {
            abort(403, 'No tienes permiso para usar esta Orden de Servicio.');
        }
    }

    public function index()
    {
        $servicios = $this->aplicarFiltroUsuario19(Servicio::query())
            ->orderByDesc('created_at')
            ->get();

        return view('servicios.index', compact('servicios'));
    }

    public function show($id)
    {
        $servicio = Servicio::with(['movimientos' => function ($q) {
            $q->orderByDesc('created_at');
        }])->findOrFail($id);

        $this->validarAccesoServicioUsuario19($servicio);

        return view('servicios.show', compact('servicio'));
    }

    public function create()
    {
        return view('servicios.create');
    }

    public function edit($id)
    {
        $servicio = Servicio::findOrFail($id);

        $this->validarAccesoServicioUsuario19($servicio);

        return view('servicios.edit', compact('servicio'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mantenimiento_tipo' => 'required|in:interno,externo',

            'tipo_equipo'        => 'required|string|max:255',
            'subtipo_equipo'     => 'required|string|max:255',

            'numero_serie'       => 'nullable|string|max:255',
            'marca'              => 'nullable|string|max:255',
            'modelo'             => 'nullable|string|max:255',
            'año'                => 'nullable|integer',
            'descripcion'        => 'nullable|string',
            'fecha_inicial'      => 'required|date',

            'evidencia1'         => 'nullable|image|mimes:jpg,jpeg,png,gif,webp,heic,heif',
            'evidencia2'         => 'nullable|image|mimes:jpg,jpeg,png,gif,webp,heic,heif',
            'evidencia3'         => 'nullable|image|mimes:jpg,jpeg,png,gif,webp,heic,heif',

            'video'              => 'nullable|file|mimetypes:video/mp4,video/avi,video/mpeg,video/webm,video/quicktime,video/x-msvideo,video/x-flv,video/3gpp,video/3gpp2,video/x-matroska,video/x-ms-wmv,video/hevc,video/h265,video/mp2t,video/ogg,video/mkv',

            'firmaDigital'       => 'nullable|string',
            'observaciones'      => 'nullable|string',
            'user_name'          => 'nullable|string|max:255',
            'nombre_doctor'      => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            Log::warning('Validación fallida al guardar servicio.', $validator->errors()->toArray());

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors'  => $validator->errors(),
                ], 422);
            }

            return back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            Log::info('Inicio del proceso de guardar servicio.');

            $evidencias = [null, null, null];

            for ($i = 1; $i <= 3; $i++) {
                $campo = "evidencia{$i}";

                if ($request->hasFile($campo) && $request->file($campo)->isValid()) {
                    $path = $request->file($campo)->store('evidencias', 'public');
                    $evidencias[$i - 1] = Storage::disk('public')->url($path);
                }
            }

            $video = null;

            if ($request->hasFile('video') && $request->file('video')->isValid()) {
                $pathVideo = $request->file('video')->store('videos', 'public');
                $video = Storage::disk('public')->url($pathVideo);
            }

            $firma = null;

            if ($request->filled('firmaDigital')) {
                $firma = $this->guardarFirmaBase64($request->input('firmaDigital'));
            }

            $mantenimientoTipo = $request->input('mantenimiento_tipo', 'interno');

            $payloadServicio = [
                'mantenimiento_tipo' => $mantenimientoTipo,
                'estado_proceso'     => $mantenimientoTipo === 'externo'
                    ? 'pendiente_salida_foraneo'
                    : 'requiere_os',

                'tipo_equipo'        => $request->input('tipo_equipo'),
                'subtipo_equipo'     => $request->input('subtipo_equipo'),
                'numero_serie'       => $request->input('numero_serie'),
                'marca'              => $request->input('marca'),
                'modelo'             => $request->input('modelo'),
                'año'                => $request->input('año'),
                'descripcion'        => $request->input('descripcion'),
                'fecha_adquisicion'  => $request->input('fecha_inicial'),

                'evidencia1'         => $evidencias[0],
                'evidencia2'         => $evidencias[1],
                'evidencia3'         => $evidencias[2],
                'video'              => $video,
                'firma_digital'      => $firma,

                'observaciones'      => $request->input('observaciones'),
                'user_name'          => Auth::user()->name ?? $request->input('user_name'),
                'nombre_doctor'      => $request->input('nombre_doctor'),
            ];

            if ($this->tieneColumnaServicio('user_id')) {
                $payloadServicio['user_id'] = Auth::id();
            }

            $servicio = Servicio::create($payloadServicio);

            $this->sincronizarEstadoProceso($servicio);

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success'     => true,
                    'message'     => 'Servicio guardado. Continúa con el proceso obligatorio del mantenimiento.',
                    'servicio_id' => $servicio->id,
                    'next_url'    => route('servicio.proceso', $servicio->id),
                ]);
            }

            return redirect()
                ->route('servicio.proceso', $servicio->id)
                ->with('ok', 'Servicio guardado correctamente.');
        } catch (\Throwable $e) {
            Log::error('Error al guardar el servicio.', [
                'error' => $e->getMessage(),
                'stack' => $e->getTraceAsString(),
            ]);

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'error'   => 'Hubo un error al guardar el registro de servicio. Revisa logs.',
                ], 500);
            }

            return back()
                ->withInput()
                ->with('error', 'Hubo un error al guardar el registro de servicio. Revisa logs.');
        }
    }

    public function update(Request $request, $id)
    {
        $servicio = Servicio::findOrFail($id);

        $this->validarAccesoServicioUsuario19($servicio);

        $validator = Validator::make($request->all(), [
            'mantenimiento_tipo' => 'nullable|in:interno,externo',

            'tipo_equipo'        => 'required|string|max:255',
            'subtipo_equipo'     => 'required|string|max:255',

            'numero_serie'       => 'nullable|string|max:255',
            'marca'              => 'nullable|string|max:255',
            'modelo'             => 'nullable|string|max:255',
            'año'                => 'nullable|integer',
            'descripcion'        => 'nullable|string',
            'fecha_inicial'      => 'required|date',

            'evidencia1'         => 'nullable|image|mimes:jpg,jpeg,png,gif,webp,heic,heif',
            'evidencia2'         => 'nullable|image|mimes:jpg,jpeg,png,gif,webp,heic,heif',
            'evidencia3'         => 'nullable|image|mimes:jpg,jpeg,png,gif,webp,heic,heif',

            'video'              => 'nullable|file|mimetypes:video/mp4,video/avi,video/mpeg,video/webm,video/quicktime,video/x-msvideo,video/x-flv,video/3gpp,video/3gpp2,video/x-matroska,video/x-ms-wmv,video/hevc,video/h265,video/mp2t,video/ogg,video/mkv',

            'firmaDigital'       => 'nullable|string',
            'observaciones'      => 'nullable|string',
            'user_name'          => 'nullable|string|max:255',
            'nombre_doctor'      => 'nullable|string|max:255',

            'eliminar_evidencia1' => 'nullable|in:0,1',
            'eliminar_evidencia2' => 'nullable|in:0,1',
            'eliminar_evidencia3' => 'nullable|in:0,1',
            'eliminar_video'      => 'nullable|in:0,1',
            'eliminar_firma'      => 'nullable|in:0,1',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            for ($i = 1; $i <= 3; $i++) {
                $campoArchivo = "evidencia{$i}";
                $campoEliminar = "eliminar_evidencia{$i}";

                if ($request->input($campoEliminar) == '1' && !empty($servicio->{$campoArchivo})) {
                    $this->eliminarArchivoPublico($servicio->{$campoArchivo});
                    $servicio->{$campoArchivo} = null;
                }

                if ($request->hasFile($campoArchivo) && $request->file($campoArchivo)->isValid()) {
                    if (!empty($servicio->{$campoArchivo})) {
                        $this->eliminarArchivoPublico($servicio->{$campoArchivo});
                    }

                    $path = $request->file($campoArchivo)->store('evidencias', 'public');
                    $servicio->{$campoArchivo} = Storage::disk('public')->url($path);
                }
            }

            if ($request->input('eliminar_video') == '1' && !empty($servicio->video)) {
                $this->eliminarArchivoPublico($servicio->video);
                $servicio->video = null;
            }

            if ($request->hasFile('video') && $request->file('video')->isValid()) {
                if (!empty($servicio->video)) {
                    $this->eliminarArchivoPublico($servicio->video);
                }

                $pathVideo = $request->file('video')->store('videos', 'public');
                $servicio->video = Storage::disk('public')->url($pathVideo);
            }

            if ($request->input('eliminar_firma') == '1' && !empty($servicio->firma_digital)) {
                $this->eliminarArchivoPublico($servicio->firma_digital);
                $servicio->firma_digital = null;
            }

            if ($request->filled('firmaDigital')) {
                if (!empty($servicio->firma_digital)) {
                    $this->eliminarArchivoPublico($servicio->firma_digital);
                }

                $servicio->firma_digital = $this->guardarFirmaBase64($request->input('firmaDigital'));
            }

            $servicio->mantenimiento_tipo = $request->input(
                'mantenimiento_tipo',
                $servicio->mantenimiento_tipo ?: 'interno'
            );

            $servicio->tipo_equipo       = $request->input('tipo_equipo');
            $servicio->subtipo_equipo    = $request->input('subtipo_equipo');
            $servicio->numero_serie      = $request->input('numero_serie');
            $servicio->marca             = $request->input('marca');
            $servicio->modelo            = $request->input('modelo');
            $servicio->año               = $request->input('año');
            $servicio->descripcion       = $request->input('descripcion');
            $servicio->fecha_adquisicion = $request->input('fecha_inicial');
            $servicio->observaciones     = $request->input('observaciones');
            $servicio->user_name         = Auth::user()->name ?? $request->input('user_name') ?? $servicio->user_name;
            $servicio->nombre_doctor     = $request->input('nombre_doctor');

            $servicio->save();

            $this->sincronizarEstadoProceso($servicio->fresh());

            return redirect()
                ->route('servicios.show', $servicio->id)
                ->with('ok', 'Registro de servicio actualizado correctamente.');
        } catch (\Throwable $e) {
            Log::error('Error al actualizar servicio.', [
                'id'    => $servicio->id,
                'error' => $e->getMessage(),
                'stack' => $e->getTraceAsString(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'Hubo un error al actualizar el servicio.');
        }
    }

    public function destroy($id)
    {
        $servicio = Servicio::findOrFail($id);

        $this->validarAccesoServicioUsuario19($servicio);

        try {
            foreach (['evidencia1', 'evidencia2', 'evidencia3', 'video', 'firma_digital'] as $campo) {
                if (!empty($servicio->{$campo})) {
                    $this->eliminarArchivoPublico($servicio->{$campo});
                }
            }

            $movimientos = Movimiento::where('servicio_id', $servicio->id)->get();

            foreach ($movimientos as $mov) {
                foreach (['evidencia1', 'evidencia2', 'evidencia3', 'video'] as $campo) {
                    if (!empty($mov->{$campo})) {
                        $this->eliminarArchivoPublico($mov->{$campo});
                    }
                }

                $mov->delete();
            }

            $servicio->delete();

            return redirect()
                ->route('servicios.index')
                ->with('ok', 'Registro eliminado correctamente.');
        } catch (\Throwable $e) {
            Log::error('Error al eliminar servicio.', [
                'id'    => $servicio->id,
                'error' => $e->getMessage(),
                'stack' => $e->getTraceAsString(),
            ]);

            return redirect()
                ->route('servicios.index')
                ->with('error', 'No se pudo eliminar el registro.');
        }
    }

    public function proceso($id)
    {
        $servicio = Servicio::with(['movimientos' => function ($q) {
            $q->orderBy('created_at');
        }])->findOrFail($id);

        $this->validarAccesoServicioUsuario19($servicio);

        $this->sincronizarEstadoProceso($servicio);

        $servicio->refresh()->load(['movimientos' => function ($q) {
            $q->orderByDesc('created_at');
        }]);

        $proceso = $this->resolverProceso($servicio);

        return view('servicios.proceso', compact('servicio', 'proceso'));
    }

    public function avanzarProceso(Request $request, $id)
    {
        $servicio = Servicio::with('movimientos')->findOrFail($id);
        $this->validarAccesoServicioUsuario19($servicio);
        $proceso = $this->resolverProceso($servicio);

        if ($proceso['completado']) {
            return redirect()
                ->route('servicio.proceso', $servicio->id)
                ->with('ok', 'El proceso ya fue completado.');
        }

        $pasoEsperado = $proceso['siguiente']['key'] ?? null;

        if ($pasoEsperado === 'validar_os') {
            return redirect()
                ->route('servicio.os.form', $servicio->id)
                ->with('error', 'Primero debes validar la Orden de Servicio.');
        }

        if ($pasoEsperado === 'salida_foraneo' && ($servicio->mantenimiento_tipo ?? 'interno') === 'externo') {
            return redirect()
                ->route('servicio.externo.salida.qr', $servicio->id)
                ->with('error', 'La salida a mantenimiento externo debe registrarse mediante el formulario QR.');
        }

        $validator = Validator::make($request->all(), [
            'paso'         => 'required|string',
            'descripcion'  => 'nullable|string|max:5000',
            'evidencia1'   => 'nullable|image|mimes:jpg,jpeg,png,gif,webp,heic,heif',
            'evidencia2'   => 'nullable|image|mimes:jpg,jpeg,png,gif,webp,heic,heif',
            'evidencia3'   => 'nullable|image|mimes:jpg,jpeg,png,gif,webp,heic,heif',
            'video'        => 'nullable|file|mimetypes:video/mp4,video/avi,video/mpeg,video/webm,video/quicktime,video/x-msvideo,video/x-flv,video/3gpp,video/3gpp2,video/x-matroska,video/x-ms-wmv,video/hevc,video/h265,video/mp2t,video/ogg,video/mkv',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        if ($request->input('paso') !== $pasoEsperado) {
            return back()->with('error', 'No puedes saltarte pasos del proceso.');
        }

        try {
            [$e1, $e2, $e3, $video] = $this->subirArchivosMovimiento($request);

            $meta = $this->metaPaso($pasoEsperado);

            $mov = new Movimiento();
            $mov->servicio_id = $servicio->id;
            $mov->tipo_movimiento = $pasoEsperado;
            $mov->descripcion = $request->input('descripcion') ?: $meta['descripcion_default'];
            $mov->evidencia1 = $e1;
            $mov->evidencia2 = $e2;
            $mov->evidencia3 = $e3;
            $mov->video = $video;
            $mov->save();

            $this->sincronizarEstadoProceso($servicio->fresh());

            return redirect()
                ->route('servicio.proceso', $servicio->id)
                ->with('ok', $meta['success']);
        } catch (\Throwable $e) {
            Log::error('Error al registrar paso del proceso.', [
                'servicio_id' => $servicio->id,
                'paso'        => $request->input('paso'),
                'error'       => $e->getMessage(),
                'stack'       => $e->getTraceAsString(),
            ]);

            return back()->with('error', 'No se pudo registrar el paso del proceso.');
        }
    }

    public function qrSalidaExterna($id)
    {
        $servicio = Servicio::with('movimientos')->findOrFail($id);
        $this->validarAccesoServicioUsuario19($servicio);
        $proceso = $this->resolverProceso($servicio);

        if (($servicio->mantenimiento_tipo ?? 'interno') !== 'externo') {
            return redirect()
                ->route('servicio.proceso', $servicio->id)
                ->with('error', 'Este QR solo aplica para mantenimientos externos.');
        }

        if (($proceso['siguiente']['key'] ?? null) !== 'salida_foraneo') {
            return redirect()
                ->route('servicio.proceso', $servicio->id)
                ->with('error', 'Ahorita no corresponde registrar la salida foránea.');
        }

        $tokenInfo = $this->obtenerOGenerarTokenSalidaExterna($servicio->id, 12);
        $tokenActual = $tokenInfo['token'];
        $expiraEn = Carbon::parse($tokenInfo['expira_en']);
        $ttlSeconds = max(now()->diffInSeconds($expiraEn, false), 0);
        $accessUrl = route('servicio.externo.salida.access', ['id' => $servicio->id]);

        return view('servicios.salida-externa-qr', compact(
            'servicio',
            'accessUrl',
            'tokenActual',
            'expiraEn',
            'ttlSeconds'
        ));
    }

    public function accesoSalidaExterna($id)
    {
        $servicio = Servicio::with('movimientos')->findOrFail($id);
        $this->validarAccesoServicioUsuario19($servicio);
        $proceso = $this->resolverProceso($servicio);

        if (($servicio->mantenimiento_tipo ?? 'interno') !== 'externo') {
            return response()
                ->view('servicios.salida-externa-status', [
                    'status'  => 'bloqueado',
                    'titulo'  => 'Formulario no disponible',
                    'mensaje' => 'Este formulario solo aplica a mantenimientos externos.',
                ], 409);
        }

        if (($proceso['siguiente']['key'] ?? null) !== 'salida_foraneo') {
            return response()
                ->view('servicios.salida-externa-status', [
                    'status'  => 'bloqueado',
                    'titulo'  => 'Paso ya no disponible',
                    'mensaje' => 'La salida foránea ya fue registrada o el flujo ya avanzó.',
                ], 409);
        }

        $tokenInfo = $this->obtenerOGenerarTokenSalidaExterna($servicio->id, 12);

        return redirect()->route('servicio.externo.salida.form', [
            'token' => $tokenInfo['token'],
        ]);
    }

    public function formSalidaExterna($token)
    {
        $payload = $this->obtenerPayloadQrSalidaExterna($token);

        if (!$payload) {
            return response()
                ->view('servicios.salida-externa-status', [
                    'status'  => 'expirado',
                    'titulo'  => 'QR expirado o inválido',
                    'mensaje' => 'El enlace de formulario caducó. Escanea nuevamente el QR dinámico para obtener uno nuevo.',
                ], 410);
        }

        $servicio = Servicio::with('movimientos')->findOrFail($payload['servicio_id']);
        $proceso = $this->resolverProceso($servicio);

        if (($servicio->mantenimiento_tipo ?? 'interno') !== 'externo') {
            return response()
                ->view('servicios.salida-externa-status', [
                    'status'  => 'bloqueado',
                    'titulo'  => 'Formulario no disponible',
                    'mensaje' => 'Este formulario solo aplica a mantenimientos externos.',
                ], 409);
        }

        if (($proceso['siguiente']['key'] ?? null) !== 'salida_foraneo') {
            return response()
                ->view('servicios.salida-externa-status', [
                    'status'  => 'bloqueado',
                    'titulo'  => 'Paso ya no disponible',
                    'mensaje' => 'La salida foránea ya fue registrada o el flujo ya avanzó.',
                ], 409);
        }

        return view('servicios.salida-externa-form', compact('servicio', 'token'));
    }

    public function storeSalidaExterna(Request $request, $token)
    {
        $payload = $this->obtenerPayloadQrSalidaExterna($token);

        if (!$payload) {
            return back()
                ->withInput()
                ->with('error', 'El código QR expiró. Escanea nuevamente el QR dinámico.');
        }

        $servicio = Servicio::with('movimientos')->findOrFail($payload['servicio_id']);
        $proceso = $this->resolverProceso($servicio);

        if (($servicio->mantenimiento_tipo ?? 'interno') !== 'externo') {
            return back()
                ->withInput()
                ->with('error', 'Este formulario solo aplica a mantenimientos externos.');
        }

        if (($proceso['siguiente']['key'] ?? null) !== 'salida_foraneo') {
            return back()
                ->withInput()
                ->with('error', 'La salida foránea ya fue registrada o el flujo cambió.');
        }

        $data = $request->validate([
            'nombre_salida'                       => 'required|string|max:255',
            'fecha_salida'                        => 'required|date',
            'hora_salida'                         => 'required|date_format:H:i',
            'firma_salida'                        => 'required|string',
            'observaciones_salida'                => 'nullable|string|max:5000',
            'componentes_salida'                  => 'required|array|min:1',
            'componentes_salida.*.nombre'         => 'required|string|max:255',
            'componentes_salida.*.cantidad'       => 'required|numeric|min:0.01',
            'componentes_salida.*.tipo'           => 'required|string|max:100',
            'fecha_regreso_estimada'              => 'nullable|date',
            'hora_regreso_estimada'               => 'nullable|date_format:H:i',
        ], [
            'nombre_salida.required'                 => 'El nombre de quien realiza la salida es obligatorio.',
            'fecha_salida.required'                  => 'La fecha de salida es obligatoria.',
            'hora_salida.required'                   => 'La hora de salida es obligatoria.',
            'firma_salida.required'                  => 'La firma es obligatoria.',
            'componentes_salida.required'            => 'Debes agregar al menos un componente.',
            'componentes_salida.array'               => 'Los componentes deben enviarse correctamente.',
            'componentes_salida.min'                 => 'Debes agregar al menos un componente.',
            'componentes_salida.*.nombre.required'   => 'Cada componente debe tener nombre.',
            'componentes_salida.*.cantidad.required' => 'Cada componente debe tener cantidad.',
            'componentes_salida.*.cantidad.numeric'  => 'La cantidad de cada componente debe ser numérica.',
            'componentes_salida.*.cantidad.min'      => 'La cantidad de cada componente debe ser mayor a 0.',
            'componentes_salida.*.tipo.required'     => 'Cada componente debe tener tipo.',
        ]);

        try {
            $componentes = collect($data['componentes_salida'])
                ->map(function ($item) {
                    return [
                        'nombre'   => trim((string) ($item['nombre'] ?? '')),
                        'cantidad' => (string) ($item['cantidad'] ?? ''),
                        'tipo'     => trim((string) ($item['tipo'] ?? '')),
                    ];
                })
                ->filter(function ($item) {
                    return $item['nombre'] !== '' && $item['cantidad'] !== '' && $item['tipo'] !== '';
                })
                ->values()
                ->all();

            if (empty($componentes)) {
                return back()
                    ->withInput()
                    ->with('error', 'Debes agregar al menos un componente válido.');
            }

            $resumenComponentes = collect($componentes)
                ->map(function ($item) {
                    return $item['cantidad'] . ' ' . $item['tipo'] . ' de ' . $item['nombre'];
                })
                ->implode(', ');

            $descripcion = 'Salida a mantenimiento foráneo registrada por '
                . $data['nombre_salida']
                . ' el ' . $data['fecha_salida']
                . ' a las ' . $data['hora_salida']
                . '. Componentes / salida: ' . $resumenComponentes;

            if (!empty($data['fecha_regreso_estimada'])) {
                $descripcion .= '. Regreso estimado: ' . $data['fecha_regreso_estimada'];

                if (!empty($data['hora_regreso_estimada'])) {
                    $descripcion .= ' ' . $data['hora_regreso_estimada'];
                }
            }

            if (!empty($data['observaciones_salida'])) {
                $descripcion .= '. Observaciones: ' . $data['observaciones_salida'];
            }

            $mov = new Movimiento();
            $mov->servicio_id = $servicio->id;
            $mov->tipo_movimiento = 'salida_foraneo';
            $mov->descripcion = $descripcion;
            $mov->checklist = json_encode([
                'origen'                 => 'qr_salida_externa',
                'nombre_salida'          => $data['nombre_salida'],
                'fecha_salida'           => $data['fecha_salida'],
                'hora_salida'            => $data['hora_salida'],
                'firma_salida'           => $data['firma_salida'],
                'observaciones_salida'   => $data['observaciones_salida'] ?? null,
                'componentes_salida'     => $resumenComponentes,
                'componentes_detalle'    => $componentes,
                'fecha_regreso_estimada' => $data['fecha_regreso_estimada'] ?? null,
                'hora_regreso_estimada'  => $data['hora_regreso_estimada'] ?? null,
                'capturado_desde'        => 'qr_publico',
                'capturado_at'           => now()->toDateTimeString(),
            ], JSON_UNESCAPED_UNICODE);

            $mov->save();

            $this->invalidarTokenSalidaExterna($token, $servicio->id);
            $this->sincronizarEstadoProceso($servicio->fresh());

            return view('servicios.salida-externa-status', [
                'status'  => 'ok',
                'titulo'  => 'Salida registrada correctamente',
                'mensaje' => 'El formulario fue enviado y el proceso del mantenimiento externo ya avanzó al siguiente paso.',
            ]);
        } catch (\Throwable $e) {
            Log::error('Error al registrar salida foránea con QR.', [
                'servicio_id' => $servicio->id,
                'error'       => $e->getMessage(),
                'stack'       => $e->getTraceAsString(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'No se pudo registrar la salida foránea.');
        }
    }

    public function ordenServicioForm($id)
    {
        $servicio = Servicio::with('movimientos')->findOrFail($id);
        $this->validarAccesoServicioUsuario19($servicio);
        $proceso = $this->resolverProceso($servicio);

        if (!empty($servicio->orden_validada_at) && !empty($servicio->orden_id)) {
            return redirect()
                ->route('servicio.proceso', $servicio->id)
                ->with('ok', 'La Orden de Servicio ya está validada.');
        }

        if (($proceso['siguiente']['key'] ?? null) !== 'validar_os') {
            return redirect()
                ->route('servicio.proceso', $servicio->id)
                ->with('error', 'Aún no corresponde validar la OS. Completa primero el paso anterior.');
        }

        return view('servicios.orden-servicio', compact('servicio'));
    }

    public function ordenServicioValidar(Request $request, $id)
    {
        $servicio = Servicio::with('movimientos')->findOrFail($id);
        $this->validarAccesoServicioUsuario19($servicio);
        $proceso = $this->resolverProceso($servicio);

        if (($proceso['siguiente']['key'] ?? null) !== 'validar_os') {
            return redirect()
                ->route('servicio.proceso', $servicio->id)
                ->with('error', 'Aún no puedes validar la OS porque falta un paso previo.');
        }

        $data = $request->validate([
            'orden_id' => ['required', 'string', 'max:50'],
        ], [
            'orden_id.required' => 'Debes capturar el ID o código de validación de la Orden de Servicio.',
        ]);

        $valor = trim((string) $data['orden_id']);

        $orden = null;

        if (ctype_digit($valor)) {
            $orden = Orden::where('id', (int) $valor)->first();
        }

        if (!$orden) {
            $orden = Orden::where('codigo_validacion_servicio', $valor)->first();
        }

        if (!$orden) {
            return back()
                ->withInput()
                ->with('error', 'No existe una Orden de Servicio con ese ID o código de validación.');
        }

        $this->validarAccesoOrdenUsuario19($orden);

        $servicio->orden_id = $orden->id;
        $servicio->orden_validada_at = Carbon::now();
        $servicio->save();

        $this->sincronizarEstadoProceso($servicio->fresh());

        return redirect()
            ->route('servicio.proceso', $servicio->id)
            ->with('ok', "Orden #{$orden->id} validada correctamente con el código {$orden->codigo_validacion_servicio}.");
    }

    public function detalles($id)
    {
        $servicio = Servicio::with('movimientos')->find($id);

        if (!$servicio) {
            return response()->json(['error' => 'Servicio no encontrado.'], 404);
        }

        $this->validarAccesoServicioUsuario19($servicio);

        $this->sincronizarEstadoProceso($servicio);

        $servicio->refresh()->load('movimientos');

        $clean = function ($path) {
            if (!$path) {
                return null;
            }

            if (preg_match('/^https?:\/\//', $path)) {
                return $path;
            }

            $rel = ltrim(preg_replace('#^/?storage/#', '', $path), '/');

            return Storage::disk('public')->url($rel);
        };

        $movimientos = $servicio->movimientos->map(function ($mov) use ($clean) {
            return [
                'id'              => $mov->id,
                'descripcion'     => $mov->descripcion,
                'evidencia1'      => $clean($mov->evidencia1),
                'evidencia2'      => $clean($mov->evidencia2),
                'evidencia3'      => $clean($mov->evidencia3),
                'video'           => $clean($mov->video),
                'tipo_movimiento' => $mov->tipo_movimiento,
                'fecha'           => optional($mov->created_at)->format('Y-m-d H:i:s'),
                'checklist'       => $this->normalizarChecklist($mov->checklist),
            ];
        });

        $firmaUrl = $servicio->firma_digital ? $clean($servicio->firma_digital) : null;

        $ordenId = $servicio->orden_id ? (int) $servicio->orden_id : null;
        $ordenExiste = $ordenId ? Orden::where('id', $ordenId)->exists() : false;
        $puedeMovimientos = !empty($servicio->orden_validada_at);

        $proceso = $this->resolverProceso($servicio);

        return response()->json([
            'tipo_equipo'        => $servicio->tipo_equipo,
            'descripcion'        => $servicio->descripcion,
            'subtipo_equipo'     => $servicio->subtipo_equipo,
            'modelo'             => $servicio->modelo,
            'marca'              => $servicio->marca,
            'observaciones'      => $servicio->observaciones,
            'numero_serie'       => $servicio->numero_serie,
            'fecha_adquisicion'  => $servicio->fecha_adquisicion,
            'estado_proceso'     => $servicio->estado_proceso,
            'anio'               => $servicio->año,

            'mantenimiento_tipo' => $servicio->mantenimiento_tipo,
            'orden_id'           => $ordenId,
            'orden_existe'       => $ordenExiste ? 1 : 0,
            'orden_validada_at'  => $servicio->orden_validada_at,

            'evidencia1'         => $clean($servicio->evidencia1),
            'evidencia2'         => $clean($servicio->evidencia2),
            'evidencia3'         => $clean($servicio->evidencia3),
            'firma_digital'      => $firmaUrl,
            'user_name'          => $servicio->user_name,
            'nombre_doctor'      => $servicio->nombre_doctor,
            'video'              => $clean($servicio->video),

            'movimientos'        => $movimientos,
            'puede_movimientos'  => $puedeMovimientos ? 1 : 0,

            'proceso_completado' => $proceso['completado'] ? 1 : 0,
            'siguiente_paso'     => $proceso['siguiente']['key'] ?? null,
            'siguiente_label'    => $proceso['siguiente']['label'] ?? null,
        ]);
    }

    public function qrSalidaExternaStatus(Servicio $servicio)
    {
        $this->validarAccesoServicioUsuario19($servicio);

        $ultimoMovimiento = $servicio->movimientos()
            ->where('tipo_movimiento', 'salida_foraneo')
            ->latest('id')
            ->first();

        $check = [];

        if ($ultimoMovimiento) {
            $check = is_array($ultimoMovimiento->checklist)
                ? $ultimoMovimiento->checklist
                : (json_decode($ultimoMovimiento->checklist ?? '', true) ?: []);
        }

        $completadoPorQr = $ultimoMovimiento
            && (($check['origen'] ?? null) === 'qr_salida_externa');

        $estadoAvanzado = in_array($servicio->estado_proceso, [
            'pendiente_regreso_foraneo',
            'pendiente_salida_cliente',
            'completado',
        ], true);

        $completed = $completadoPorQr || $estadoAvanzado;

        return response()->json([
            'ok'                => true,
            'completed'         => $completed,
            'message'           => $completed
                ? 'Formulario completado correctamente'
                : 'Esperando envío del formulario',
            'redirect_url'      => route('servicio.proceso', $servicio->id),
            'estado_proceso'    => $servicio->estado_proceso,
            'ultimo_movimiento' => $ultimoMovimiento?->tipo_movimiento,
            'completed_at'      => optional($ultimoMovimiento?->created_at)->format('Y-m-d H:i:s'),
        ]);
    }

    private function resolverProceso(Servicio $servicio): array
    {
        $movimientos = $servicio->relationLoaded('movimientos')
            ? $servicio->movimientos
            : $servicio->movimientos()->get();

        $tiene = function (string $tipo) use ($movimientos) {
            return $movimientos->contains(function ($mov) use ($tipo) {
                return $mov->tipo_movimiento === $tipo;
            });
        };

        $tipo = $servicio->mantenimiento_tipo ?: 'interno';

        $osValidada = !empty($servicio->orden_validada_at) && !empty($servicio->orden_id);

        $tieneEntrega = $tiene('entrega');
        $tieneSalidaForaneo = $tiene('salida_foraneo');
        $tieneRegresoForaneo = $tiene('regreso_foraneo');
        $tieneSalidaCliente = $tiene('salida_cliente');

        if ($tipo === 'externo') {
            $pasosBase = [
                ['key' => 'salida_foraneo',  'label' => 'Salida a mantenimiento foráneo', 'done' => $tieneSalidaForaneo],
                ['key' => 'regreso_foraneo', 'label' => 'Regreso de mantenimiento foráneo', 'done' => $tieneRegresoForaneo],
                ['key' => 'validar_os',      'label' => 'Validar Orden de Servicio', 'done' => $osValidada],
                ['key' => 'salida_cliente',  'label' => 'Salida para cliente', 'done' => $tieneSalidaCliente],
            ];

            if (!$tieneSalidaForaneo) {
                $estado = 'pendiente_salida_foraneo';
                $siguiente = $this->metaPaso('salida_foraneo');
            } elseif (!$tieneRegresoForaneo) {
                $estado = 'pendiente_regreso_foraneo';
                $siguiente = $this->metaPaso('regreso_foraneo');
            } elseif (!$osValidada) {
                $estado = 'requiere_os';
                $siguiente = $this->metaPaso('validar_os');
            } elseif (!$tieneSalidaCliente) {
                $estado = 'pendiente_salida_cliente';
                $siguiente = $this->metaPaso('salida_cliente');
            } else {
                $estado = 'completado';
                $siguiente = null;
            }
        } else {
            $pasosBase = [
                ['key' => 'validar_os', 'label' => 'Validar Orden de Servicio', 'done' => $osValidada],
                ['key' => 'entrega',    'label' => 'Salida a entrega', 'done' => $tieneEntrega],
            ];

            if (!$osValidada) {
                $estado = 'requiere_os';
                $siguiente = $this->metaPaso('validar_os');
            } elseif (!$tieneEntrega) {
                $estado = 'pendiente_entrega';
                $siguiente = $this->metaPaso('entrega');
            } else {
                $estado = 'completado';
                $siguiente = null;
            }
        }

        $pasos = [];
        $marcarCurrent = true;

        foreach ($pasosBase as $paso) {
            $status = 'pending';

            if ($paso['done']) {
                $status = 'done';
            } elseif ($marcarCurrent) {
                $status = 'current';
                $marcarCurrent = false;
            }

            $pasos[] = [
                'key'    => $paso['key'],
                'label'  => $paso['label'],
                'done'   => $paso['done'],
                'status' => $status,
            ];
        }

        return [
            'tipo'           => $tipo,
            'estado_proceso' => $estado,
            'completado'     => $estado === 'completado',
            'siguiente'      => $siguiente,
            'pasos'          => $pasos,
        ];
    }

    private function metaPaso(string $paso): array
    {
        return match ($paso) {
            'entrega' => [
                'key'                 => 'entrega',
                'label'               => 'Registrar salida a entrega',
                'button'              => 'Registrar salida a entrega',
                'descripcion_default' => 'Salida a entrega registrada desde el proceso de mantenimiento interno.',
                'success'             => 'Salida a entrega registrada correctamente.',
                'icon'                => 'bi-box-arrow-up-right',
            ],

            'salida_foraneo' => [
                'key'                 => 'salida_foraneo',
                'label'               => 'Registrar salida a mantenimiento foráneo',
                'button'              => 'Registrar salida a mantenimiento foráneo',
                'descripcion_default' => 'Salida registrada hacia mantenimiento foráneo.',
                'success'             => 'Salida a mantenimiento foráneo registrada correctamente.',
                'icon'                => 'bi-truck',
            ],

            'regreso_foraneo' => [
                'key'                 => 'regreso_foraneo',
                'label'               => 'Registrar regreso de mantenimiento foráneo',
                'button'              => 'Registrar regreso',
                'descripcion_default' => 'Regreso registrado desde mantenimiento foráneo.',
                'success'             => 'Regreso de mantenimiento foráneo registrado correctamente.',
                'icon'                => 'bi-arrow-repeat',
            ],

            'salida_cliente' => [
                'key'                 => 'salida_cliente',
                'label'               => 'Registrar salida para cliente',
                'button'              => 'Registrar salida para cliente',
                'descripcion_default' => 'Salida para cliente registrada.',
                'success'             => 'Salida para cliente registrada correctamente.',
                'icon'                => 'bi-person-check',
            ],

            'validar_os' => [
                'key'                 => 'validar_os',
                'label'               => 'Validar Orden de Servicio',
                'button'              => 'Ir a validar OS',
                'descripcion_default' => 'Validación de Orden de Servicio.',
                'success'             => 'Orden de Servicio validada correctamente.',
                'icon'                => 'bi-patch-check',
            ],

            default => [
                'key'                 => $paso,
                'label'               => ucfirst(str_replace('_', ' ', $paso)),
                'button'              => 'Continuar',
                'descripcion_default' => ucfirst(str_replace('_', ' ', $paso)),
                'success'             => 'Paso registrado correctamente.',
                'icon'                => 'bi-check2-circle',
            ],
        };
    }

    private function sincronizarEstadoProceso(Servicio $servicio): void
    {
        $proceso = $this->resolverProceso($servicio);

        if ($servicio->estado_proceso !== $proceso['estado_proceso']) {
            $servicio->estado_proceso = $proceso['estado_proceso'];
            $servicio->save();
        }
    }

    private function subirArchivosMovimiento(Request $request): array
    {
        $evidencias = [null, null, null];

        for ($i = 1; $i <= 3; $i++) {
            $campo = "evidencia{$i}";

            if ($request->hasFile($campo) && $request->file($campo)->isValid()) {
                $path = $request->file($campo)->store('movimientos/evidencias', 'public');
                $evidencias[$i - 1] = Storage::disk('public')->url($path);
            }
        }

        $video = null;

        if ($request->hasFile('video') && $request->file('video')->isValid()) {
            $pathVideo = $request->file('video')->store('movimientos/videos', 'public');
            $video = Storage::disk('public')->url($pathVideo);
        }

        return [$evidencias[0], $evidencias[1], $evidencias[2], $video];
    }

    private function guardarFirmaBase64(?string $firmaBase64): ?string
    {
        if (!$firmaBase64) {
            return null;
        }

        $base64Image = preg_replace('#^data:image/\w+;base64,#i', '', $firmaBase64);
        $decoded = base64_decode($base64Image);

        if ($decoded === false) {
            return null;
        }

        $nombreFirma = 'firma_' . time() . '_' . uniqid() . '.png';
        $ruta = 'firmas/' . $nombreFirma;

        Storage::disk('public')->put($ruta, $decoded);

        return $ruta;
    }

    private function eliminarArchivoPublico(?string $path): void
    {
        if (!$path) {
            return;
        }

        try {
            $parsedPath = parse_url($path, PHP_URL_PATH);

            if ($parsedPath) {
                $relative = ltrim(preg_replace('#^/?storage/#', '', $parsedPath), '/');
            } else {
                $relative = ltrim(preg_replace('#^/?storage/#', '', $path), '/');
            }

            if ($relative && Storage::disk('public')->exists($relative)) {
                Storage::disk('public')->delete($relative);
            }
        } catch (\Throwable $e) {
            Log::warning('No se pudo eliminar archivo del storage.', [
                'path'  => $path,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function qrSalidaExternaCacheKey(string $token): string
    {
        return 'servicio_qr_salida_externa:' . $token;
    }

    private function qrSalidaExternaCurrentKey(int $servicioId): string
    {
        return 'servicio_qr_salida_externa_actual:' . $servicioId;
    }

    private function obtenerPayloadQrSalidaExterna(string $token): ?array
    {
        $payload = Cache::get($this->qrSalidaExternaCacheKey($token));

        return is_array($payload) ? $payload : null;
    }

    private function obtenerOGenerarTokenSalidaExterna(int $servicioId, int $horas = 12): array
    {
        $currentToken = Cache::get($this->qrSalidaExternaCurrentKey($servicioId));

        if (is_string($currentToken) && $currentToken !== '') {
            $payload = $this->obtenerPayloadQrSalidaExterna($currentToken);

            if (is_array($payload) && (int) ($payload['servicio_id'] ?? 0) === $servicioId) {
                return [
                    'token'       => $currentToken,
                    'servicio_id' => $payload['servicio_id'],
                    'expira_en'   => $payload['expira_en'],
                ];
            }
        }

        $token = Str::random(64);
        $expiraEn = now()->addHours($horas);

        Cache::put(
            $this->qrSalidaExternaCacheKey($token),
            [
                'servicio_id' => $servicioId,
                'expira_en'   => $expiraEn->toDateTimeString(),
            ],
            $expiraEn
        );

        Cache::put(
            $this->qrSalidaExternaCurrentKey($servicioId),
            $token,
            $expiraEn
        );

        return [
            'token'       => $token,
            'servicio_id' => $servicioId,
            'expira_en'   => $expiraEn->toDateTimeString(),
        ];
    }

    private function invalidarTokenSalidaExterna(string $token, ?int $servicioId = null): void
    {
        Cache::forget($this->qrSalidaExternaCacheKey($token));

        if ($servicioId) {
            $actual = Cache::get($this->qrSalidaExternaCurrentKey($servicioId));

            if ($actual === $token) {
                Cache::forget($this->qrSalidaExternaCurrentKey($servicioId));
            }
        }
    }

    private function normalizarChecklist($checklist)
    {
        if (is_array($checklist)) {
            return $checklist;
        }

        if (is_string($checklist) && trim($checklist) !== '') {
            $decoded = json_decode($checklist, true);

            return json_last_error() === JSON_ERROR_NONE ? $decoded : $checklist;
        }

        return $checklist;
    }
}