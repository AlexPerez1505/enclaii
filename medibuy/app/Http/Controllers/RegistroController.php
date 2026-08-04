<?php

namespace App\Http\Controllers;

use App\Models\Registro;
use App\Models\ProcesoEquipo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class RegistroController extends Controller
{
    /**
     * Muestra formulario de creación.
     */
    public function create()
    {
        return view('registros.create');
    }

    /**
     * Listado (alias web).
     */
    public function index()
    {
        return $this->mostrarProductos();
    }

    public function edit($id)
    {
        $registro = Registro::findOrFail($id);

        Log::info('REGISTRO EDIT - VALORES SELECT', [
            'id'             => $registro->id,
            'tipo_equipo'    => $registro->tipo_equipo,
            'subtipo_equipo' => $registro->subtipo_equipo,
            'marca'          => $registro->marca,
            'modelo'         => $registro->modelo,
        ]);

        $componentes = DB::table('inv_registro_componentes')
            ->where('registro_id', $registro->id)
            ->select('nombre_cache as nombre', 'cantidad', 'incluido')
            ->orderBy('nombre_cache')
            ->get();

        return view('registros.edit', compact('registro', 'componentes'));
    }

    /**
     * AJAX: Checar duplicados de series (uno por línea).
     */
    public function checkSeries(Request $request)
    {
        $seriesRaw = (string) $request->input('series', '');

        $series = collect(preg_split('/\r\n|\r|\n/', $seriesRaw))
            ->map(fn($v) => trim($v))
            ->filter(fn($v) => $v !== '')
            ->unique()
            ->values();

        if ($series->isEmpty()) {
            return response()->json([
                'ok' => true,
                'duplicates' => [],
            ]);
        }

        $existentes = Registro::whereIn('numero_serie', $series->all())
            ->pluck('numero_serie')
            ->map(fn($v) => (string) $v)
            ->values()
            ->all();

        $existentesOrden = collect($series->all())
            ->filter(fn($s) => in_array($s, $existentes, true))
            ->values()
            ->all();

        return response()->json([
            'ok' => true,
            'duplicates' => $existentesOrden,
        ]);
    }

    /**
     * PDF: Exportar inventario
     */
    public function exportPdf(Request $request)
    {
        $estado  = trim((string) ($request->query('estado_proceso', $request->query('estado', ''))));
        $tipo    = trim((string) $request->query('tipo_equipo', ''));
        $subtipo = trim((string) $request->query('subtipo_equipo', ''));

        $estadoActual = $request->query('estado_actual');
        if ($estado === '' && $estadoActual !== null && $estadoActual !== '') {
            $map = [1 => 'stock', 2 => 'vendido', 3 => 'mantenimiento', 4 => 'defectuoso'];
            $estado = $map[(int) $estadoActual] ?? '';
        }

        $q = Registro::query()->latest('created_at');

        if ($estado !== '') {
            if ($estado === 'registro') {
                $q->where(function ($qq) {
                    $qq->whereNull('estado_proceso')
                        ->orWhere('estado_proceso', 'registro');
                });
            } else {
                $q->where('estado_proceso', $estado);
            }
        }

        if ($tipo !== '') {
            $q->where('tipo_equipo', $tipo);
        }

        if ($subtipo !== '') {
            $q->where('subtipo_equipo', $subtipo);
        }

        $rows = $q->get();

        $estados = [
            'registro'      => 'REGISTRO',
            'hojalateria'   => 'HOJALATERÍA',
            'mantenimiento' => 'MANTENIMIENTO',
            'stock'         => 'STOCK',
            'vendido'       => 'VENDIDO',
            'defectuoso'    => 'DEFECTUOSO',
            1 => 'STOCK',
            2 => 'VENDIDO',
            3 => 'MANTENIMIENTO',
            4 => 'DEFECTUOSO',
        ];

        $filtrosTxt = [];
        $filtrosTxt[] = 'ESTADO: ' . ($estado !== '' ? ($estados[$estado] ?? strtoupper($estado)) : 'TODOS');
        $filtrosTxt[] = 'TIPO: ' . ($tipo !== '' ? strtoupper($tipo) : 'TODOS');
        $filtrosTxt[] = 'SUBTIPO: ' . ($subtipo !== '' ? strtoupper($subtipo) : 'TODOS');
        $filtrosTxt[] = 'AGRUPADO: CATEGORÍA (TIPO DE EQUIPO)';

        $groups = $rows->groupBy(function ($r) {
            $tipo = $r->tipo_equipo ?? 'SIN CATEGORÍA';
            return strtoupper(trim($tipo));
        });

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('registros.reporte-pdf', [
    'titulo'     => 'REPORTE DE INVENTARIO',
    'rows'       => $rows,
    'groups'     => $groups,
    'estados'    => $estados,
    'filtrosTxt' => $filtrosTxt,
    'total'      => $rows->count(),
])
->setPaper('a4', 'portrait')
->setOptions([
    'isHtml5ParserEnabled' => true,
    'isRemoteEnabled'      => false,
    'defaultFont'          => 'DejaVu Sans',
    'dpi'                  => 96,
]);

return $pdf->stream('INVENTARIO.pdf');
    }

    /**
     * EXCEL: Exportar inventario
     */
    public function exportExcel(Request $request)
    {
        $estado  = trim((string) ($request->query('estado_proceso', $request->query('estado', ''))));
        $tipo    = trim((string) $request->query('tipo_equipo', ''));
        $subtipo = trim((string) $request->query('subtipo_equipo', ''));

        $estadoActual = $request->query('estado_actual');
        if ($estado === '' && $estadoActual !== null && $estadoActual !== '') {
            $map = [1 => 'stock', 2 => 'vendido', 3 => 'mantenimiento', 4 => 'defectuoso'];
            $estado = $map[(int) $estadoActual] ?? '';
        }

        $q = Registro::query()->latest('created_at');

        if ($estado !== '') {
            if ($estado === 'registro') {
                $q->where(function ($qq) {
                    $qq->whereNull('estado_proceso')
                        ->orWhere('estado_proceso', 'registro');
                });
            } else {
                $q->where('estado_proceso', $estado);
            }
        }

        if ($tipo !== '') {
            $q->where('tipo_equipo', $tipo);
        }

        if ($subtipo !== '') {
            $q->where('subtipo_equipo', $subtipo);
        }

        $rows = $q->get();

        $estados = [
            'registro'      => 'REGISTRO',
            'hojalateria'   => 'HOJALATERÍA',
            'mantenimiento' => 'MANTENIMIENTO',
            'stock'         => 'STOCK',
            'vendido'       => 'VENDIDO',
            'defectuoso'    => 'DEFECTUOSO',
            1 => 'STOCK',
            2 => 'VENDIDO',
            3 => 'MANTENIMIENTO',
            4 => 'DEFECTUOSO',
        ];

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Inventario');

        $sheet->setCellValue('A1', 'INVENTARIO DE GRUPO MEDIBUY');
        $sheet->mergeCells('A1:H1');
        $sheet->getStyle('A1')->getFont()
            ->setBold(true)
            ->setSize(14);
        $sheet->getStyle('A1')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $headers = ['SERIE', 'MARCA', 'MODELO', 'TIPO', 'SUBTIPO', 'ESTADO', 'FECHA ADQUISICIÓN', 'USUARIO'];
        $sheet->fromArray($headers, null, 'A2');
        $sheet->getStyle('A2:H2')->getFont()->setBold(true);

        $rowNum = 3;
        foreach ($rows as $r) {
            $estadoKey = $r->estado_proceso ?? $r->estado_actual ?? 'registro';
            $estadoTxt = $estados[$estadoKey]
                ?? ($estados[(int) $estadoKey] ?? $estadoKey ?? '');

            $sheet->setCellValue('A' . $rowNum, (string) ($r->numero_serie ?? ''));
            $sheet->setCellValue('B' . $rowNum, (string) ($r->marca ?? ''));
            $sheet->setCellValue('C' . $rowNum, (string) ($r->modelo ?? ''));
            $sheet->setCellValue('D' . $rowNum, (string) ($r->tipo_equipo ?? ''));
            $sheet->setCellValue('E' . $rowNum, (string) ($r->subtipo_equipo ?? ''));
            $sheet->setCellValue('F' . $rowNum, (string) $estadoTxt);
            $sheet->setCellValue('G' . $rowNum, optional($r->fecha_adquisicion)->format('Y-m-d'));
            $sheet->setCellValue('H' . $rowNum, (string) ($r->user_name ?? ''));

            $rowNum++;
        }

        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $lastRow = $rowNum - 1;
        $sheet->getStyle('A1:H' . $lastRow)
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $fileName = 'inventario_' . now()->format('Ymd_His') . '.xlsx';
        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /**
     * API: Crear registro(s)
     */
    public function guardarRegistro(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'Tipo_de_Equipo'         => 'required|string|max:255',
            'Subtipo_de_Equipo'      => 'nullable|string|max:255',
            'Subtipo_de_Equipo_Otro' => 'nullable|string|max:255',

            'Numero_de_Serie'        => 'required|string|max:2000',

            'Marca'                  => 'required|string|max:255',
            'Modelo'                 => 'required|string|max:255',
            'Año'                    => 'nullable|string|max:4',
            'descripcion'            => 'required|string',
            'estado_actual'          => 'nullable|in:1,2,3,4',

            'estado_proceso'         => 'nullable|in:registro,hojalateria,mantenimiento,stock,vendido,defectuoso',

            'fecha_inicial'          => 'required|date',
            'fecha_mantenimiento'    => 'nullable|date',

            'evidencia1'             => 'nullable|image|mimes:jpg,jpeg,png,gif,webp,heic,heif',
            'evidencia2'             => 'nullable|image|mimes:jpg,jpeg,png,gif,webp,heic,heif',
            'evidencia3'             => 'nullable|image|mimes:jpg,jpeg,png,gif,webp,heic,heif',

            'video-evidencia'        => 'nullable|file|mimetypes:video/mp4,video/avi,video/mpeg,video/webm,video/quicktime,video/x-msvideo,video/x-flv,video/3gpp,video/3gpp2,video/x-matroska,video/x-ms-wmv,video/hevc,video/h265,video/mp2t,video/ogg,video/mkv',

            'documentoPDF'           => 'nullable|file|mimes:pdf|max:10240',

            'observaciones'          => 'nullable|string',
            'firmaDigital'           => 'required|string',

            'componentes'               => 'nullable|array',
            'componentes.*.id'          => 'nullable|integer|exists:inv_componentes_cat,id',
            'componentes.*.nombre'      => 'nullable|string|max:255',
            'componentes.*.cantidad'    => 'nullable|integer|min:0',
            'componentes.*.incluido'    => 'nullable',
            'componentes.*.notas'       => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            Log::warning('Validación fallida.', $validator->errors()->toArray());
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        $estadoProceso = $this->normalizaEstadoProceso($request->input('estado_proceso', 'registro'));

        $seriesRaw = (string) $request->input('Numero_de_Serie', '');
        $series = collect(preg_split('/\r\n|\r|\n/', $seriesRaw))
            ->map(fn($v) => trim($v))
            ->filter(fn($v) => $v !== '')
            ->unique()
            ->values();

        if ($series->isEmpty()) {
            return response()->json([
                'success' => false,
                'error'   => 'Debes capturar al menos un número de serie válido.'
            ], 422);
        }

        $maxSeries = 500;
        if ($series->count() > $maxSeries) {
            $series = $series->take($maxSeries);
        }

        $existentes = Registro::whereIn('numero_serie', $series->all())
            ->pluck('numero_serie')
            ->map(fn($v) => (string) $v)
            ->values()
            ->all();

        if (!empty($existentes)) {
            $existentesOrden = collect($series->all())
                ->filter(fn($s) => in_array($s, $existentes, true))
                ->values()
                ->all();

            return response()->json([
                'success'    => false,
                'error'      => 'No se puede registrar: uno o más números de serie ya están registrados.',
                'duplicates' => $existentesOrden,
            ], 422);
        }

        try {
            Log::info('STORE_START', [
                'user'           => Auth::id(),
                'series'         => $series->all(),
                'estado_proceso' => $estadoProceso,
            ]);

            DB::beginTransaction();

            $evidencias = [null, null, null];
            for ($i = 1; $i <= 3; $i++) {
                $campo = "evidencia{$i}";
                if ($request->hasFile($campo) && $request->file($campo)->isValid()) {
                    $ruta = $request->file($campo)->store('evidencias', 'public');
                    $evidencias[$i - 1] = Storage::url($ruta);
                }
            }

            $video = null;
            if ($request->hasFile('video-evidencia') && $request->file('video-evidencia')->isValid()) {
                $rutaVideo = $request->file('video-evidencia')->store('videos', 'public');
                $video = Storage::url($rutaVideo);
            }

            $documentoPDF = null;
            if ($request->hasFile('documentoPDF') && $request->file('documentoPDF')->isValid()) {
                $rutaPDF = $request->file('documentoPDF')->store('documentos/pdf', 'public');
                $documentoPDF = Storage::url($rutaPDF);
            }

            $firma = null;
            if ($request->filled('firmaDigital')) {
                $firma = $this->storeBase64Png($request->firmaDigital, 'firmas');
            }

            $registrosIds = [];

            foreach ($series as $serie) {
                /** @var \App\Models\Registro $registro */
                $registro = Registro::create([
                    'tipo_equipo'           => $request->Tipo_de_Equipo,
                    'subtipo_equipo'        => $request->Subtipo_de_Equipo,
                    'subtipo_equipo_otro'   => $request->Subtipo_de_Equipo_Otro,
                    'numero_serie'          => $serie,
                    'marca'                 => $request->Marca,
                    'modelo'                => $request->Modelo,
                    'anio'                  => $request->Año,
                    'descripcion'           => $request->descripcion,
                    'estado_actual'         => $request->estado_actual,
                    'estado_proceso'        => $estadoProceso,
                    'fecha_adquisicion'     => $request->fecha_inicial,
                    'ultimo_mantenimiento'  => $request->fecha_mantenimiento,
                    'evidencia1'            => $evidencias[0],
                    'evidencia2'            => $evidencias[1],
                    'evidencia3'            => $evidencias[2],
                    'video'                 => $video,
                    'documentoPDF'          => $documentoPDF,
                    'observaciones'         => $request->observaciones,
                    'firma_digital'         => $firma,
                    'user_name'             => Auth::user()->name ?? null,
                ]);

                $this->syncComponentes($registro->id, $request->componentes);

                if ($estadoProceso !== 'registro') {
                    $procesoAuto = ProcesoEquipo::create([
                        'registro_id'         => $registro->id,
                        'tipo_proceso'        => $estadoProceso,
                        'descripcion_proceso' => 'Creado automáticamente desde registro (directo a estado)',
                    ]);

                    $registro->proceso_id = $procesoAuto->id;
                    $registro->save();
                }

                $registrosIds[] = $registro->id;
            }

            DB::commit();

            $primeroId = $registrosIds[0];

            return response()->json([
                'success'                => true,
                'message'                => $series->count() > 1
                    ? "Se guardaron {$series->count()} registros con sus números de serie."
                    : 'Registro guardado exitosamente.',
                'registro_id'            => $primeroId,
                'registros_ids'          => $registrosIds,
                'imprimir_barcode_url'   => route('registros.imprimir-barcode', $primeroId),
                'imprimir_barcodes_urls' => collect($registrosIds)
                    ->map(fn($id) => route('registros.imprimir-barcode', $id))
                    ->all(),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error al guardar el/los registro(s)', [
                'error' => $e->getMessage(),
                'stack' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'error'   => 'Hubo un error al guardar el registro.'
            ], 500);
        }
    }

    /**
     * UPDATE (edit)
     */
    public function update(Request $request, $id)
    {
        $registro = Registro::findOrFail($id);

        $map = fn($upper, $lower = null) => $request->input($upper, $lower ? $request->input($lower) : null);

        $validator = Validator::make($request->all(), [
            'Tipo_de_Equipo'          => 'nullable|string|max:255',
            'tipo_equipo'             => 'nullable|string|max:255',
            'Subtipo_de_Equipo'       => 'nullable|string|max:255',
            'subtipo_equipo'          => 'nullable|string|max:255',
            'Subtipo_de_Equipo_Otro'  => 'nullable|string|max:255',
            'subtipo_equipo_otro'     => 'nullable|string|max:255',

            'Numero_de_Serie'         => 'nullable|string|max:2000',
            'numero_serie'            => 'nullable|string|max:255',

            'Marca'                   => 'nullable|string|max:255',
            'marca'                   => 'nullable|string|max:255',
            'Modelo'                  => 'nullable|string|max:255',
            'modelo'                  => 'nullable|string|max:255',
            'Año'                     => 'nullable|string|max:4',
            'anio'                    => 'nullable|string|max:4',

            'descripcion'             => 'required|string',
            'estado_actual'           => 'nullable|in:1,2,3,4',

            'fecha_inicial'           => 'nullable|date',
            'fecha_adquisicion'       => 'nullable|date',
            'fecha_mantenimiento'     => 'nullable|date',
            'ultimo_mantenimiento'    => 'nullable|date',

            'observaciones'           => 'nullable|string',

            'evidencia1'              => 'nullable|image|mimes:jpg,jpeg,png,gif,webp,heic,heif',
            'evidencia2'              => 'nullable|image|mimes:jpg,jpeg,png,gif,webp,heic,heif',
            'evidencia3'              => 'nullable|image|mimes:jpg,jpeg,png,gif,webp,heic,heif',
            'video-evidencia'         => 'nullable|file|mimetypes:video/mp4,video/avi,video/mpeg,video/webm,video/quicktime,video/x-msvideo,video/x-flv,video/3gpp,video/3gpp2,video/x-matroska,video/x-ms-wmv,video/hevc,video/h265,video/mp2t,video/ogg,video/mkv',
            'documentoPDF'            => 'nullable|file|mimes:pdf|max:10240',

            'firmaDigital'            => 'nullable|string',

            'componentes'             => 'nullable|array',
            'componentes.*.nombre'    => 'nullable|string|max:255',
            'componentes.*.cantidad'  => 'nullable|integer|min:0',
            'componentes.*.incluido'  => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();

            $numeroSerieEdit = $map('Numero_de_Serie', 'numero_serie');
            if ($numeroSerieEdit !== null) {
                $numeroSerieEdit = trim(preg_split('/\r\n|\r|\n/', (string) $numeroSerieEdit)[0] ?? '');
                if ($numeroSerieEdit === '') {
                    $numeroSerieEdit = null;
                }
            }

            if ($numeroSerieEdit !== null && $numeroSerieEdit !== $registro->numero_serie) {
                $existe = Registro::where('numero_serie', $numeroSerieEdit)
                    ->where('id', '!=', $registro->id)
                    ->exists();

                if ($existe) {
                    DB::rollBack();
                    return response()->json([
                        'success'    => false,
                        'error'      => "No se puede actualizar: el número de serie {$numeroSerieEdit} ya está registrado.",
                        'duplicates' => [$numeroSerieEdit],
                    ], 422);
                }
            }

            $payload = [
                'tipo_equipo'          => $map('Tipo_de_Equipo', 'tipo_equipo') ?? $registro->tipo_equipo,
                'subtipo_equipo'       => $map('Subtipo_de_Equipo', 'subtipo_equipo') ?? $registro->subtipo_equipo,
                'subtipo_equipo_otro'  => $map('Subtipo_de_Equipo_Otro', 'subtipo_equipo_otro') ?? $registro->subtipo_equipo_otro,
                'numero_serie'         => $numeroSerieEdit ?? $registro->numero_serie,
                'marca'                => $map('Marca', 'marca') ?? $registro->marca,
                'modelo'               => $map('Modelo', 'modelo') ?? $registro->modelo,
                'anio'                 => $map('Año', 'anio') ?? $registro->anio,
                'descripcion'          => $request->input('descripcion', $registro->descripcion),
                'estado_actual'        => $request->input('estado_actual', $registro->estado_actual),
                'fecha_adquisicion'    => $map('fecha_inicial', 'fecha_adquisicion') ?? $registro->fecha_adquisicion,
                'ultimo_mantenimiento' => $map('fecha_mantenimiento', 'ultimo_mantenimiento') ?? $registro->ultimo_mantenimiento,
                'observaciones'        => $request->input('observaciones', $registro->observaciones),
            ];

            for ($i = 1; $i <= 3; $i++) {
                $campo = "evidencia{$i}";
                if ($request->hasFile($campo) && $request->file($campo)->isValid()) {
                    $this->deletePublicUrl($registro->{$campo});
                    $ruta = $request->file($campo)->store('evidencias', 'public');
                    $payload[$campo] = Storage::url($ruta);
                }
            }

            if ($request->hasFile('video-evidencia') && $request->file('video-evidencia')->isValid()) {
                $this->deletePublicUrl($registro->video);
                $rutaVideo = $request->file('video-evidencia')->store('videos', 'public');
                $payload['video'] = Storage::url($rutaVideo);
            }

            if ($request->hasFile('documentoPDF') && $request->file('documentoPDF')->isValid()) {
                $this->deletePublicUrl($registro->documentoPDF);
                $rutaPDF = $request->file('documentoPDF')->store('documentos/pdf', 'public');
                $payload['documentoPDF'] = Storage::url($rutaPDF);
            }

            if ($request->filled('firmaDigital')) {
                $this->deletePublicUrl($registro->firma_digital);
                $payload['firma_digital'] = $this->storeBase64Png($request->firmaDigital, 'firmas');
            }

            $registro->update($payload);

            if ($request->has('componentes')) {
                $this->syncComponentes($registro->id, $request->componentes);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Registro actualizado correctamente.',
                'imprimir_barcode_url' => route('registros.imprimir-barcode', $registro->id),
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('UPDATE_ERROR', ['id' => $id, 'msg' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'error'   => 'Hubo un error al actualizar el registro.'
            ], 500);
        }
    }

    public function actualizarRegistro(Request $request, $id)
    {
        return $this->update($request, $id);
    }

    public function mostrarProductos()
    {
        $productos = Registro::with(['procesos:id,registro_id,tipo_proceso,created_at'])
            ->latest('created_at')
            ->get();

        $procesos = collect();

        return view('inventario', compact('productos', 'procesos'));
    }

    public function obtenerDetalles($id)
    {
        $registro = Registro::with('procesos.fichaTecnica')->findOrFail($id);

        $estados = [
            1 => 'En Stock',
            2 => 'Vendido',
            3 => 'En Mantenimiento',
            4 => 'Defectuoso',
        ];

        $estadoActual = $estados[$registro->estado_actual] ?? 'Estado desconocido';

        $procesos = $registro->procesos->map(function ($proceso) {
            $fichaArchivo = $proceso->fichaTecnica ? $proceso->fichaTecnica->archivo : null;

            $defectos = is_array($proceso->defectos)
                ? implode("\n", array_filter($proceso->defectos))
                : (trim((string) $proceso->defectos) !== '' && strtolower((string) $proceso->defectos) !== 'null'
                    ? $proceso->defectos
                    : null);

            if ($fichaArchivo && !str_contains($fichaArchivo, 'fichas_tecnicas/')) {
                $fichaArchivo = 'fichas_tecnicas/' . $fichaArchivo;
            }

            return [
                'id'                    => $proceso->id,
                'descripcion_proceso'   => $proceso->descripcion_proceso ?? 'No disponible',
                'evidencia1'            => $proceso->evidencia1 ? asset('storage/' . $proceso->evidencia1) : null,
                'evidencia2'            => $proceso->evidencia2 ? asset('storage/' . $proceso->evidencia2) : null,
                'evidencia3'            => $proceso->evidencia3 ? asset('storage/' . $proceso->evidencia3) : null,
                'video'                 => $proceso->video ? asset('storage/' . $proceso->video) : null,
                'ficha_tecnica_archivo' => $fichaArchivo,
                'ficha_tecnica_nombre'  => $proceso->fichaTecnica ? $proceso->fichaTecnica->nombre : 'No disponible',
                'defectos'              => $defectos,
                'created_at'            => $proceso->created_at ? $proceso->created_at->format('Y-m-d H:i:s') : null,
            ];
        });

        $firmaPath = $registro->firma_digital;
        if ($firmaPath && str_contains($firmaPath, 'storage/')) {
            $firmaUrl = asset($firmaPath);
        } elseif ($firmaPath) {
            $firmaUrl = asset('storage/' . ltrim($firmaPath, '/'));
        } else {
            $firmaUrl = null;
        }

        $componentes = DB::table('inv_registro_componentes')
            ->leftJoin('inv_componentes_cat', 'inv_componentes_cat.id', '=', 'inv_registro_componentes.componente_id')
            ->where('inv_registro_componentes.registro_id', $registro->id)
            ->select([
                'inv_registro_componentes.id',
                'inv_componentes_cat.id as componente_id',
                'inv_registro_componentes.nombre_cache as nombre',
                'inv_registro_componentes.cantidad',
                'inv_registro_componentes.incluido',
                'inv_registro_componentes.notas',
            ])
            ->orderBy('inv_registro_componentes.nombre_cache')
            ->get();

        $barcodeUrl = route('registros.imprimir-barcode', $registro->id);

        return response()->json([
            'tipo_equipo'           => $registro->tipo_equipo,
            'subtipo_equipo'        => $registro->subtipo_equipo,
            'numero_serie'          => $registro->numero_serie,
            'marca'                 => $registro->marca,
            'modelo'                => $registro->modelo,
            'anio'                  => $registro->anio,
            'estado_actual'         => $estadoActual,
            'fecha_adquisicion'     => optional($registro->fecha_adquisicion)->format('Y-m-d'),
            'ultimo_mantenimiento'  => optional($registro->ultimo_mantenimiento)->format('Y-m-d'),
            'descripcion'           => $registro->descripcion,
            'observaciones'         => $registro->observaciones,
            'evidencia1'            => $registro->evidencia1,
            'evidencia2'            => $registro->evidencia2,
            'evidencia3'            => $registro->evidencia3,
            'documentoPDF'          => $registro->documentoPDF,
            'video'                 => $registro->video,
            'firma_digital'         => $firmaUrl,
            'user_name'             => $registro->user_name,
            'procesos'              => $procesos,
            'componentes'           => $componentes,
            'imprimir_barcode_url'  => $barcodeUrl,
        ]);
    }

    public function mostrarRegistro($id)
    {
        $registro = Registro::findOrFail($id);
        return response()->json($registro);
    }

    /**
     * VALIDAR NIP PARA EDITAR
     */
    public function validarPinEdicion(Request $request, $registro)
    {
        $validator = Validator::make($request->all(), [
            'aprobacion_pin' => ['required', 'digits:6'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Debes ingresar un NIP válido de 6 dígitos.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $expectedPin = (string) env('APROBACION_PIN', '');
        if ($expectedPin === '') {
            return response()->json([
                'success' => false,
                'message' => 'NIP no configurado en el servidor.',
            ], 403);
        }

        $pin = (string) $request->input('aprobacion_pin', '');

        if (!hash_equals($expectedPin, $pin)) {
            return response()->json([
                'success' => false,
                'message' => 'NIP incorrecto.',
            ], 403);
        }

        $exists = Registro::where('id', $registro)->exists();
        if (!$exists) {
            return response()->json([
                'success' => false,
                'message' => 'Registro no encontrado.',
            ], 404);
        }

        return response()->json([
    'success' => true,
    'message' => 'NIP correcto.',
    'redirect' => route('registros.edit', $registro),
]);
        
    }

    /**
     * ELIMINAR REGISTRO CON NIP
     */
    public function eliminarRegistro(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'aprobacion_pin' => ['required', 'digits:6'],
            ]);

            if ($validator->fails()) {
                if ($request->expectsJson() || $request->wantsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Debes ingresar un NIP válido de 6 dígitos.',
                        'error'   => 'Debes ingresar un NIP válido de 6 dígitos.',
                        'errors'  => $validator->errors(),
                    ], 422);
                }

                return redirect()->back()->with('error', 'Debes ingresar un NIP válido de 6 dígitos.');
            }

            $expectedPin = (string) env('APROBACION_PIN', '');
            if ($expectedPin === '') {
                if ($request->expectsJson() || $request->wantsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'NIP no configurado en el servidor.',
                        'error'   => 'NIP no configurado en el servidor.',
                    ], 403);
                }

                return redirect()->back()->with('error', 'NIP no configurado en el servidor.');
            }

            $pin = (string) $request->input('aprobacion_pin', '');

            if (!hash_equals($expectedPin, $pin)) {
                if ($request->expectsJson() || $request->wantsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'NIP incorrecto.',
                        'error'   => 'NIP incorrecto.',
                    ], 403);
                }

                return redirect()->back()->with('error', 'NIP incorrecto.');
            }

            $registro = Registro::findOrFail($id);
            $registro->delete();

            if ($request->expectsJson() || $request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Registro eliminado correctamente.',
                ]);
            }

            return redirect()
                ->route('registros.index')
                ->with('success', 'Registro eliminado correctamente.');
        } catch (\Throwable $e) {
            Log::error('Error al eliminar el registro.', [
                'error' => $e->getMessage(),
                'registro_id' => $id,
            ]);

            if ($request->expectsJson() || $request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Hubo un error al eliminar el registro.',
                    'error'   => 'Hubo un error al eliminar el registro.',
                ], 500);
            }

            return redirect()
                ->back()
                ->with('error', 'Hubo un error al eliminar el registro.');
        }
    }

    public function obtenerProcesosPendientes($id)
    {
        $registro = Registro::with('procesos')->findOrFail($id);

        $todosLosProcesos    = ['hojalateria', 'mantenimiento', 'stock', 'finalizado'];
        $procesosCompletados = $registro->procesos->pluck('descripcion_proceso')->toArray();
        $procesosPendientes  = array_diff($todosLosProcesos, $procesosCompletados);

        return response()->json($procesosPendientes);
    }

    public function registrosStock()
    {
        return response()->json(
            Registro::where('estado_proceso', 'stock')->get(['id', 'numero_serie'])
        );
    }

    public function info($id)
    {
        $registro = Registro::findOrFail($id);

        return response()->json([
            'numero_serie'   => $registro->numero_serie,
            'estado_proceso' => $registro->estado_proceso,
            'tipo_equipo'    => $registro->tipo_equipo,
            'subtipo_equipo' => $registro->subtipo_equipo,
            'modelo'         => $registro->modelo,
            'marca'          => $registro->marca,
            'evidencia1'     => $registro->evidencia1,
        ]);
    }

    public function imprimirBarcode($id)
    {
        $registro = Registro::findOrFail($id);
        $barcodeData = $registro->numero_serie;

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('registros.ticket-barcode', compact('registro', 'barcodeData'));
        return $pdf->stream('Etiqueta_' . $registro->numero_serie . '.pdf');
    }

    public function vistaBuscar()
    {
        return view('inventario.buscar');
    }

    public function buscarSubmit(Request $request)
    {
        $serie = $request->input('serie');
        $query = $request->input('query');

        if ($serie) {
            $registro = Registro::where('numero_serie', $serie)->first();
            if (!$registro) {
                return back()->with('error', 'No se encontró el equipo con ese número de serie.');
            }
            return redirect()->route('inventario.detalle', $registro->id);
        }

        if ($query) {
            $resultados = Registro::where('tipo_equipo', 'like', "%$query%")
                ->orWhere('marca', 'like', "%$query%")
                ->orWhere('modelo', 'like', "%$query%")
                ->get();

            return view('inventario.resultados', compact('resultados', 'query'));
        }

        return back()->with('error', 'Debes ingresar algún dato de búsqueda.');
    }

    public function mostrarProductoDetalle($id)
    {
        $registro = Registro::with('procesos.fichaTecnica')->findOrFail($id);

        $pasos = [
            'hojalateria',
            'mantenimiento',
            'stock',
            'vendido',
        ];

        $procesos = [];
        for ($i = 0; $i < count($pasos); $i++) {
            $paso = $pasos[$i];
            $procesos[$paso] = $registro->procesos->firstWhere('tipo_proceso', $paso);
        }

        return view('inventario.detalle', compact('registro', 'procesos', 'pasos'));
    }

    /* =======================
       ======= API RN ========
       ======================= */

    public function apiRecientes(Request $req)
    {
        $limit = (int) ($req->input('limit', 12));
        $rows = Registro::with(['procesos:id,registro_id,tipo_proceso,created_at'])
            ->latest('updated_at')
            ->limit($limit)
            ->get();

        $map = $this->mapListadoBasico($rows);

        return response()->json([
            'data'  => $map,
            'count' => count($map),
        ]);
    }

    public function apiInventario(Request $req)
    {
        $perPage = (int) ($req->input('per_page', 50));
        $q = trim((string) $req->input('q', ''));

        $query = Registro::with(['procesos:id,registro_id,tipo_proceso,created_at'])
            ->latest('updated_at');

        if ($q !== '') {
            $query->where(function ($qq) use ($q) {
                $qq->where('tipo_equipo', 'like', "%$q%")
                    ->orWhere('subtipo_equipo', 'like', "%$q%")
                    ->orWhere('marca', 'like', "%$q%")
                    ->orWhere('modelo', 'like', "%$q%")
                    ->orWhere('numero_serie', 'like', "%$q%")
                    ->orWhere('user_name', 'like', "%$q%");
            });
        }

        if ($perPage > 0) {
            $page = $query->paginate($perPage);
            $map = $this->mapListadoBasico($page->items());

            return response()->json([
                'data'         => $map,
                'current_page' => $page->currentPage(),
                'last_page'    => $page->lastPage(),
                'per_page'     => $page->perPage(),
                'total'        => $page->total(),
            ]);
        }

        $rows = $query->get();
        $map = $this->mapListadoBasico($rows);

        return response()->json(['data' => $map]);
    }

    public function apiIndex(Request $req)
    {
        $req->merge(['per_page' => 0]);
        return $this->apiInventario($req);
    }

    /**
     * Estado actual = último proceso real
     */
    private function mapListadoBasico($collection)
    {
        $asUrl = fn($v) => $v
            ? (Str::startsWith($v, ['http://', 'https://']) ? $v : asset('storage/' . $v))
            : null;

        return collect($collection)->map(function ($r) use ($asUrl) {
            $procesos = collect($r->procesos ?? []);

            $ultimo = $procesos
                ->sortByDesc('created_at')
                ->sortByDesc('id')
                ->first();

            $estado = $r->estado_proceso ?: ($ultimo->tipo_proceso ?? 'registro');

            $fechaEstado = $ultimo && $ultimo->created_at
                ? optional($ultimo->created_at)->toDateTimeString()
                : optional($r->updated_at ?? $r->created_at)->toDateTimeString();

            return [
                'id'                => $r->id,
                'tipo_equipo'       => $r->tipo_equipo,
                'subtipo_equipo'    => $r->subtipo_equipo,
                'marca'             => $r->marca,
                'modelo'            => $r->modelo,
                'numero_serie'      => $r->numero_serie,
                'estado_proceso'    => $estado,
                'user_name'         => $r->user_name,
                'evidencia1'        => $asUrl($r->evidencia1),
                'fecha_adquisicion' => optional($r->fecha_adquisicion)->toDateString(),
                'updated_at'        => optional($r->updated_at)->toDateTimeString(),
                'created_at'        => optional($r->created_at)->toDateTimeString(),
                'fecha_estado'      => $fechaEstado,
            ];
        })->values()->all();
    }

    /* =======================
       ===== Helpers ========
       ======================= */

    private function normalizaEstadoProceso($raw): string
    {
        $estado = strtolower(trim((string) $raw));
        $allowed = ['registro', 'hojalateria', 'mantenimiento', 'stock', 'vendido', 'defectuoso'];
        return in_array($estado, $allowed, true) ? $estado : 'registro';
    }

    private function storeBase64Png(?string $dataUri, string $dir): ?string
    {
        if (!$dataUri) {
            return null;
        }

        $base64Image  = preg_replace('#^data:image/\w+;base64,#i', '', $dataUri);
        $decodedImage = base64_decode($base64Image, true);

        if ($decodedImage === false) {
            Log::error('Firma base64 inválida');
            return null;
        }

        $file = $dir . '/firma_' . time() . '_' . mt_rand(1000, 9999) . '.png';
        Storage::disk('public')->put($file, $decodedImage);

        return Storage::url($file);
    }

    private function deletePublicUrl(?string $url): void
    {
        if (!$url) {
            return;
        }

        $path = ltrim(str_replace('/storage/', '', parse_url($url, PHP_URL_PATH) ?? ''), '/');

        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    private function syncComponentes(int $registroId, $componentes): void
    {
        DB::table('inv_registro_componentes')->where('registro_id', $registroId)->delete();

        if (!is_array($componentes) || empty($componentes)) {
            return;
        }

        $batch = [];
        foreach ($componentes as $row) {
            $nombre   = $row['nombre'] ?? null;
            $cantidad = isset($row['cantidad']) ? (int) $row['cantidad'] : 0;
            $incluido = filter_var($row['incluido'] ?? false, FILTER_VALIDATE_BOOLEAN) ? 1 : 0;

            if (!$nombre && $cantidad <= 0) {
                continue;
            }

            $batch[] = [
                'registro_id'   => $registroId,
                'componente_id' => $row['id'] ?? null,
                'nombre_cache'  => $nombre ?: 'Componente',
                'cantidad'      => max(0, $cantidad),
                'incluido'      => $incluido,
                'notas'         => $row['notas'] ?? null,
                'created_at'    => now(),
                'updated_at'    => now(),
            ];
        }

        if (!empty($batch)) {
            DB::table('inv_registro_componentes')->insert($batch);
        }
    }

    public function cambiarEstado(Request $request, $registro)
{
    $validator = Validator::make($request->all(), [
        'estado_proceso' => 'required|in:registro,hojalateria,mantenimiento,stock,vendido,defectuoso',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'message' => 'Estado inválido.',
            'errors'  => $validator->errors(),
        ], 422);
    }

    try {
        DB::beginTransaction();

        $equipo = Registro::findOrFail($registro);

        $estadoAnterior = $equipo->estado_proceso ?? 'registro';
        $nuevoEstado = $request->input('estado_proceso');

        $equipo->estado_proceso = $nuevoEstado;

        /*
         * Si lo mandas manualmente a "registro", limpiamos proceso_id.
         * Si lo mandas a otro estado, NO creamos proceso automático aquí.
         * Esto solo mueve el equipo de etapa.
         */
        if ($nuevoEstado === 'registro') {
            $equipo->proceso_id = null;
        }

        $equipo->save();

        DB::commit();

        $nombres = [
            'registro'      => 'Registro',
            'hojalateria'   => 'Hojalatería',
            'mantenimiento' => 'Mantenimiento',
            'stock'         => 'Stock',
            'vendido'       => 'Vendido',
            'defectuoso'    => 'Defectuoso',
        ];

        return response()->json([
            'success'          => true,
            'message'          => 'El equipo fue movido a '.$nombres[$nuevoEstado].'.',
            'estado_anterior'  => $estadoAnterior,
            'estado_proceso'   => $equipo->estado_proceso,
            'registro_id'      => $equipo->id,
        ]);

    } catch (\Throwable $e) {
        DB::rollBack();

        Log::error('CAMBIAR_ESTADO_ERROR', [
            'registro_id' => $registro,
            'error'       => $e->getMessage(),
        ]);

        return response()->json([
            'success' => false,
            'message' => 'No se pudo cambiar el estado del equipo.',
            'error'   => config('app.debug') ? $e->getMessage() : null,
        ], 500);
    }
}
public function buscarSerie(Request $request)
{
    $q = trim((string) $request->input('q', ''));

    if ($q === '') {
        return response()->json([]);
    }

    $registros = Registro::where('numero_serie', 'like', "%{$q}%")
        ->orWhere('tipo_equipo',    'like', "%{$q}%")
        ->orWhere('subtipo_equipo', 'like', "%{$q}%")
        ->orWhere('marca',          'like', "%{$q}%")
        ->orWhere('modelo',         'like', "%{$q}%")
        ->orderBy('numero_serie')
        ->limit(15)
        ->get(['id', 'numero_serie', 'tipo_equipo', 'subtipo_equipo', 'marca', 'modelo'])
        ->map(fn($r) => [
            'id'             => $r->id,
            'numero_serie'   => strtoupper((string) $r->numero_serie),
            'tipo_equipo'    => strtoupper((string) $r->tipo_equipo),
            'subtipo_equipo' => strtoupper((string) $r->subtipo_equipo),
            'marca'          => strtoupper((string) $r->marca),
            'modelo'         => strtoupper((string) $r->modelo),
            // Contenido = lo que se autorellenará en el campo
            'contenido'      => trim(implode(' ', array_filter([
                $r->tipo_equipo,
                $r->subtipo_equipo,
                $r->marca,
                $r->modelo,
            ]))),
        ]);

    return response()->json($registros);
}
}