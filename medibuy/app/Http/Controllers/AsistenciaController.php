<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;

use App\Models\Asistencia;
use App\Models\User;

use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Services\AsistenciaAiService;

class AsistenciaController extends Controller
{
    // Reglas de tiempo (HH:MM)
    private string $ENTRADA_OK_HASTA      = '09:05';
    private string $ENTRADA_RETARDO_HASTA = '11:15';

    private string $ALMUERZO_INICIO = '11:15';
    private string $ALMUERZO_FIN    = '11:30';

    private string $COMIDA_INICIO = '14:30';
    private string $COMIDA_FIN    = '15:30';

    private string $SALIDA_OFICIAL = '18:00';

    public function index()
    {
        $usuarios = User::all();
        return view('asistencias', compact('usuarios'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'fecha'   => 'required|date',
            'hora'    => 'required_unless:estado,falta,permiso,vacaciones|date_format:H:i',
            'estado'  => 'required|in:asistencia,falta,permiso,vacaciones,retardo,salida',
        ]);

        $fecha  = Carbon::parse($request->fecha);
        $userId = (int) $request->user_id;
        $estado = (string) $request->estado;

        $estadosUnicos = ['asistencia', 'falta', 'permiso', 'vacaciones', 'retardo'];

        if (in_array($estado, $estadosUnicos, true)) {
            $registroExistente = Asistencia::where('user_id', $userId)
                ->whereDate('fecha', $fecha->toDateString())
                ->whereIn('estado', $estadosUnicos)
                ->exists();

            if ($registroExistente) {
                session()->flash(
                    'error_asistencia',
                    'Ya existe un registro de asistencia, falta, permiso, vacaciones o retardo para este usuario en la fecha ' . $fecha->format('d-m-Y') . '.'
                );
                return redirect()->back()->withInput();
            }
        }

        if ($estado === 'salida') {
            $asistenciaEntrada = Asistencia::where('user_id', $userId)
                ->whereDate('fecha', $fecha->toDateString())
                ->whereIn('estado', ['asistencia', 'retardo'])
                ->first();

            if (!$asistenciaEntrada) {
                session()->flash('error_asistencia', 'No se puede registrar salida porque no existe asistencia o retardo ese día para el usuario.');
                return redirect()->back()->withInput();
            }

            if (!empty($asistenciaEntrada->hora_salida)) {
                session()->flash('error_asistencia', 'Ya existe una hora de salida registrada para este usuario en esta fecha.');
                return redirect()->back()->withInput();
            }

            $asistenciaEntrada->hora_salida = $request->hora;
            $asistenciaEntrada->save();

            session()->flash('success', 'Hora de salida registrada correctamente.');
            return redirect()->back();
        }

        $usuario = User::findOrFail($userId);

        if ($estado === 'permiso' && (int) $usuario->permisos <= 0) {
            session()->flash('error_permiso', 'El usuario no tiene permisos disponibles.');
            return redirect()->back()->withInput();
        }

        if ($estado === 'vacaciones' && (int) $usuario->vacaciones_disponibles <= 0) {
            session()->flash('error_vacaciones', 'El usuario no tiene vacaciones disponibles.');
            return redirect()->back()->withInput();
        }

        switch ($estado) {
            case 'asistencia':
                $usuario->increment('asistencias');
                break;

            case 'falta':
                $usuario->increment('faltas');
                break;

            case 'permiso':
                $usuario->increment('permisos_utilizados');
                $usuario->decrement('permisos');
                break;

            case 'vacaciones':
                $usuario->increment('vacaciones_utilizadas');
                $usuario->decrement('vacaciones_disponibles');
                break;

            case 'retardo':
                $retardosActuales = (int) $usuario->retardos;
                $usuario->increment('retardos');

                if ((($retardosActuales + 1) % 3) === 0) {
                    $usuario->increment('faltas');
                } else {
                    $usuario->increment('asistencias');
                }
                break;
        }

        $hora = $request->hora ?: '00:00';

        Asistencia::create([
            'user_id' => $userId,
            'fecha'   => $fecha->toDateString(),
            'hora'    => $hora,
            'estado'  => $estado,
        ]);

        session()->flash('success', 'Registro de ' . $estado . ' guardado correctamente.');
        return redirect()->back();
    }

    public function obtenerAsistenciasQuincena()
    {
        $inicio = Carbon::now()->day <= 15
            ? Carbon::now()->startOfMonth()
            : Carbon::now()->startOfMonth()->addDays(15);

        $fin = $inicio->copy()->addDays(14);

        $asistencias = Asistencia::whereDate('fecha', '>=', $inicio->toDateString())
            ->whereDate('fecha', '<=', $fin->toDateString())
            ->get();

        return view('reporte-asistencias', compact('asistencias'));
    }

    public function miHistorial(Request $request)
    {
        $user = Auth::user();

        $asistencias = Asistencia::where('user_id', $user->id)
            ->when($request->filled('fecha_inicio'), fn($q) => $q->whereDate('fecha', '>=', $request->fecha_inicio))
            ->when($request->filled('fecha_fin'), fn($q) => $q->whereDate('fecha', '<=', $request->fecha_fin))
            ->orderBy('fecha', 'desc')
            ->get();

        return view('mi-historial', compact('asistencias'));
    }

    public function verificarAsistencia(Request $request)
    {
        $userId = (int) $request->user_id;
        $fecha  = Carbon::parse($request->fecha)->toDateString();

        $registro = Asistencia::where('user_id', $userId)
            ->whereDate('fecha', $fecha)
            ->whereIn('estado', ['asistencia', 'retardo'])
            ->exists();

        return response()->json(['tieneEntrada' => $registro]);
    }

    /**
     * ✅ VISTA HORIZONTAL (TIPO EXCEL)
     * resources/views/asistencias/historial.blade.php
     */
    public function historialHorizontal(Request $request)
    {
        $month = $request->get('month', Carbon::now()->format('Y-m'));

        try {
            $start = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        } catch (\Throwable $e) {
            $month = Carbon::now()->format('Y-m');
            $start = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        }

        $end         = $start->copy()->endOfMonth();
        $daysInMonth = $start->daysInMonth;

        $usuarios = User::query()
            ->select('id', 'name', 'puesto', 'cargo')
            ->whereNotIn('name', [
                'Anahí Téllez Ortiz',
                'Gabriela Díaz Garcia',
            ])
            ->orderBy('name')
            ->get();

        $userIds = $usuarios->pluck('id')->all();

        $asistencias = Asistencia::query()
            ->whereIn('user_id', $userIds)
            ->whereDate('fecha', '>=', $start->toDateString())
            ->whereDate('fecha', '<=', $end->toDateString())
            ->select('user_id', 'fecha', 'hora', 'hora_salida', 'estado')
            ->get();

        Log::info('HISTORIAL HORIZONTAL', [
            'month' => $month,
            'start' => $start->toDateString(),
            'end'   => $end->toDateString(),
            'usuarios'    => count($userIds),
            'asistencias' => $asistencias->count(),
        ]);

        $byUserDay = [];
        foreach ($asistencias as $a) {
            $day = Carbon::parse($a->fecha)->day;

            $byUserDay[(int)$a->user_id][(int)$day] = [
                'entrada' => $a->hora ?: null,
                'salida'  => $a->hora_salida ?: null,
                'estado'  => strtolower((string) $a->estado),
            ];
        }

        return view('asistencias.historial', compact(
            'usuarios',
            'asistencias',
            'month',
            'start',
            'end',
            'daysInMonth',
            'byUserDay'
        ));
    }

    /**
     * ✅ IMPORTAR 1 o 2 EXCEL EN EL MISMO ENVÍO
     */
    public function importarExcel(Request $request)
    {
        $request->validate([
            'archivo_entrada_salida' => 'nullable|file|mimes:xlsx,xls,csv',
            'archivo_comida'         => 'nullable|file|mimes:xlsx,xls,csv',
        ]);

        if (!$request->hasFile('archivo_entrada_salida') && !$request->hasFile('archivo_comida')) {
            return back()->with('error_asistencia', 'Sube al menos un archivo Excel.');
        }

        $userMap = $this->buildUserNominaMap(); // ✅ checador_id/nomina/id
        $batch   = (string) Str::uuid();

        $entradaSalidaData  = [];
        $entradaSalidaNames = [];
        $comidaData         = [];
        $comidaNames        = [];

        $periodStart = null;
        $periodEnd   = null;

        if ($request->hasFile('archivo_entrada_salida')) {
            $path   = $request->file('archivo_entrada_salida')->store('tmp', 'local');
            $parsed = $this->parseReporteAsistenciaExcel(storage_path('app/' . $path));

            $entradaSalidaData  = $parsed['data'];
            $entradaSalidaNames = $parsed['names'];

            $periodStart = $parsed['period_start'] ?? null;
            $periodEnd   = $parsed['period_end'] ?? null;
        }

        if ($request->hasFile('archivo_comida')) {
            $path2   = $request->file('archivo_comida')->store('tmp', 'local');
            $parsed2 = $this->parseReporteAsistenciaExcel(storage_path('app/' . $path2));

            $comidaData  = $parsed2['data'];
            $comidaNames = $parsed2['names'];

            $periodStart = $periodStart ?: ($parsed2['period_start'] ?? null);
            $periodEnd   = $periodEnd   ?: ($parsed2['period_end'] ?? null);
        }

        $excelNames = $entradaSalidaNames + $comidaNames;

        // keys = lista de IDs del Excel (pueden ser 9, 9.0, 0009, etc)
        $keys = array_values(array_unique(array_merge(
            array_keys($entradaSalidaData),
            array_keys($comidaData)
        )));

        Log::info('IMPORT ASISTENCIAS - Parse', [
            'users_entrada_salida' => count($entradaSalidaData),
            'users_comida'         => count($comidaData),
            'keys_sample'          => array_slice($keys, 0, 10),
            'period'               => [$periodStart, $periodEnd],
        ]);

        $insertados    = 0;
        $actualizados  = 0;
        $saltados      = 0;
        $noEncontrados = 0;
        $faltasAuto    = 0;

        $pendientesIA = [];

        DB::beginTransaction();
        try {
            // 1) Resolver por ID/nomina/checador_id
            $resolvedUserIdByExcelId = []; // excelId(normalizado) => user_id

            foreach ($keys as $excelIdRaw) {
                $excelIdNorm = $this->normalizeNomina($excelIdRaw);

                $uid = $userMap[$excelIdNorm] ?? null;

                Log::info('IMPORT MATCH CHECK', [
                    'excel_id_raw'  => (string)$excelIdRaw,
                    'excel_id_norm' => (string)$excelIdNorm,
                    'uid_found'     => $uid,
                    'excel_name'    => $excelNames[$excelIdRaw] ?? null,
                ]);

                if ($uid) {
                    $resolvedUserIdByExcelId[$excelIdNorm] = (int) $uid;
                } else {
                    $noEncontrados++;
                    $pendientesIA[] = [
                        'excel_id'   => (string) $excelIdRaw,
                        'excel_name' => $excelNames[$excelIdRaw] ?? null,
                    ];
                }
            }

            // 2) Fallback LOCAL por nombre (si es único) ANTES de IA
            if (!empty($pendientesIA)) {
                $usuariosLite = User::select('id', 'name')->get()->map(fn($u) => [
                    'id'   => (int)$u->id,
                    'name' => (string)$u->name,
                ])->values()->all();

                $mapLocal = $this->matchUsuariosPorNombreLocal($usuariosLite, $pendientesIA);

                foreach ($mapLocal as $excelId => $uid) {
                    $resolvedUserIdByExcelId[$this->normalizeNomina($excelId)] = (int)$uid;
                }

                // quitar ya resueltos
                $pendientesIA = array_values(array_filter($pendientesIA, function ($p) use ($resolvedUserIdByExcelId) {
                    $k = $this->normalizeNomina($p['excel_id'] ?? '');
                    return !isset($resolvedUserIdByExcelId[$k]);
                }));
            }

            // 3) IA por nombre (si quedó algo)
            $pendientesIA = array_values(array_filter($pendientesIA, fn($p) => !empty($p['excel_name'])));
            if (!empty($pendientesIA)) {
                try {
                    /** @var AsistenciaAiService $ai */
                    $ai = app(AsistenciaAiService::class);

                    $usuariosAi = User::select('id', 'nomina', 'name')->get()->map(fn($u) => [
                        'id'     => (int) $u->id,
                        'nomina' => (string) $u->nomina,
                        'name'   => (string) $u->name,
                    ])->values()->all();

                    $mapIA = $ai->matchUsuariosPorNombre($usuariosAi, $pendientesIA);

                    foreach ($mapIA as $excelId => $uid) {
                        $resolvedUserIdByExcelId[$this->normalizeNomina($excelId)] = (int) $uid;
                    }

                    Log::info('IMPORT ASISTENCIAS - IA matches', [
                        'pendientes' => count($pendientesIA),
                        'matches'    => count($mapIA),
                    ]);
                } catch (\Throwable $e) {
                    Log::warning('IMPORT ASISTENCIAS - IA FAIL', ['e' => $e->getMessage()]);
                }
            }

            // 4) NO RESUELTOS (debug)
            $noResueltos = [];
            foreach ($keys as $excelIdRaw) {
                $k = $this->normalizeNomina($excelIdRaw);
                if (!isset($resolvedUserIdByExcelId[$k])) {
                    $noResueltos[] = [
                        'excel_id'   => (string)$excelIdRaw,
                        'excel_name' => $excelNames[$excelIdRaw] ?? null,
                    ];
                }
            }
            if (!empty($noResueltos)) {
                Log::warning('IMPORT ASISTENCIAS - NO RESUELTOS', [
                    'count'  => count($noResueltos),
                    'sample' => array_slice($noResueltos, 0, 30),
                ]);
            }

            // 5) Guardar asistencias
            foreach ($keys as $excelIdRaw) {
                $excelIdNorm = $this->normalizeNomina($excelIdRaw);
                $userId = $resolvedUserIdByExcelId[$excelIdNorm] ?? null;

                if (!$userId) continue;

                $fechas = array_unique(array_merge(
                    array_keys($entradaSalidaData[$excelIdRaw] ?? []),
                    array_keys($comidaData[$excelIdRaw] ?? [])
                ));

                foreach ($fechas as $fechaYmd) {
                    $entradaTimes = $entradaSalidaData[$excelIdRaw][$fechaYmd] ?? [];
                    $comidaTimes  = $comidaData[$excelIdRaw][$fechaYmd] ?? [];

                    $horaEntrada = $this->pickHoraEntrada($entradaTimes);
                    $horaSalida  = $this->pickHoraSalida($entradaTimes);

                    $almuerzoSalida  = $this->pickInRange($comidaTimes, $this->ALMUERZO_INICIO, $this->ALMUERZO_FIN);
                    $almuerzoRegreso = $this->pickAfter($comidaTimes, $this->ALMUERZO_FIN, $this->COMIDA_INICIO);

                    $comidaSalida  = $this->pickInRange($comidaTimes, $this->COMIDA_INICIO, $this->COMIDA_FIN);
                    $comidaRegreso = $this->pickAfter($comidaTimes, $this->COMIDA_FIN, $this->SALIDA_OFICIAL);

                    if (!$horaEntrada) {
                        $saltados++;
                        continue;
                    }

                    $estado = $this->calcularEstado($horaEntrada, $almuerzoRegreso, $comidaRegreso);

                    $asistencia = Asistencia::where('user_id', $userId)
                        ->whereDate('fecha', $fechaYmd)
                        ->first();

                    $payload = [
                        'user_id' => $userId,
                        'fecha'   => $fechaYmd,
                        'hora'    => $horaEntrada,
                        'estado'  => $estado,

                        'hora_salida'           => $horaSalida,
                        'hora_almuerzo_salida'  => $almuerzoSalida,
                        'hora_almuerzo_regreso' => $almuerzoRegreso,
                        'hora_comida_salida'    => $comidaSalida,
                        'hora_comida_regreso'   => $comidaRegreso,

                        'import_batch'  => $batch,
                        'import_source' => ($request->hasFile('archivo_entrada_salida') && $request->hasFile('archivo_comida'))
                            ? 'entrada_salida+comida'
                            : ($request->hasFile('archivo_entrada_salida') ? 'entrada_salida' : 'comida'),
                    ];

                    if ($asistencia) {
                        $asistencia->fill($payload);
                        $asistencia->save();
                        $actualizados++;
                    } else {
                        Asistencia::create($payload);
                        $insertados++;
                    }
                }
            }

            // 6) AUTOFALTAS
            if ($periodStart && $periodEnd) {
                $start = Carbon::parse($periodStart)->startOfDay();
                $end   = Carbon::parse($periodEnd)->endOfDay();

                $userIds = array_values(array_unique(array_values($resolvedUserIdByExcelId)));
                $faltasAuto = $this->autogenerarFaltasPeriodo($userIds, $start, $end, $batch);
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error_asistencia', 'Error al importar: ' . $e->getMessage());
        }

        return back()->with(
            'success',
            "Importación lista ✅ | Insertados: {$insertados} | Actualizados: {$actualizados} | Faltas auto: {$faltasAuto} | Saltados(sin entrada): {$saltados} | Usuarios no encontrados: {$noEncontrados}"
        );
    }

    /**
     * ✅ Fallback LOCAL por nombre si es ÚNICO (sirve para Excel con nombre corto: "Adrian")
     */
    private function matchUsuariosPorNombreLocal(array $usuarios, array $pendientes): array
    {
        $out = [];

        foreach ($pendientes as $p) {
            $excelId   = (string)($p['excel_id'] ?? '');
            $excelName = (string)($p['excel_name'] ?? '');

            $excelNameN = $this->normNombre($excelName);
            if ($excelNameN === '') continue;

            $tokens = array_values(array_filter(explode(' ', $excelNameN)));
            if (count($tokens) === 0) continue;

            $candidatos = [];

            foreach ($usuarios as $u) {
                $uname = $this->normNombre($u['name'] ?? '');
                if ($uname === '') continue;

                $ok = true;
                foreach ($tokens as $t) {
                    if (!preg_match('/(^| )' . preg_quote($t, '/') . '/', $uname)) {
                        $ok = false; break;
                    }
                }

                if ($ok) $candidatos[] = (int)$u['id'];
            }

            $candidatos = array_values(array_unique($candidatos));
            if (count($candidatos) === 1) {
                $out[$excelId] = $candidatos[0];
            }
        }

        if (!empty($out)) {
            Log::info('IMPORT ASISTENCIAS - LocalName matches', ['matches' => $out]);
        }

        return $out;
    }

    private function normNombre(string $s): string
    {
        $s = trim(mb_strtolower($s, 'UTF-8'));
        if ($s === '') return '';

        $s = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $s);
        $s = preg_replace('/[^a-z0-9 ]/i', ' ', $s);
        $s = preg_replace('/\s+/', ' ', $s);
        return trim($s);
    }

    /**
     * ✅ AUTOGENERA FALTAS (excepto domingos)
     */
    private function autogenerarFaltasPeriodo(array $userIds, Carbon $start, Carbon $end, string $batch): int
    {
        $totalFaltas = 0;

        foreach ($userIds as $userId) {
            $existentes = Asistencia::where('user_id', $userId)
                ->whereDate('fecha', '>=', $start->toDateString())
                ->whereDate('fecha', '<=', $end->toDateString())
                ->get(['fecha'])
                ->map(fn($a) => Carbon::parse($a->fecha)->toDateString())
                ->unique()
                ->values()
                ->all();

            $existSet = array_flip($existentes);

            $faltasUsuario = 0;
            $cursor = $start->copy()->startOfDay();

            while ($cursor->lte($end)) {
                if ($cursor->isSunday()) {
                    $cursor->addDay();
                    continue;
                }

                $ymd = $cursor->toDateString();

                if (!isset($existSet[$ymd])) {
                    Asistencia::create([
                        'user_id' => $userId,
                        'fecha'   => $ymd,
                        'hora'    => '00:00',
                        'estado'  => 'falta',
                        'import_batch'  => $batch,
                        'import_source' => 'auto_faltas',
                    ]);

                    $faltasUsuario++;
                    $totalFaltas++;
                }

                $cursor->addDay();
            }

            if ($faltasUsuario > 0) {
                User::whereKey($userId)->increment('faltas', $faltasUsuario);
            }
        }

        return $totalFaltas;
    }

    /**
     * ✅ Lee Excel “Reporte de Eventos de Asistencia”
     */
    private function parseReporteAsistenciaExcel(string $fullPath): array
    {
        if (!class_exists(\PhpOffice\PhpSpreadsheet\IOFactory::class)) {
            throw new \RuntimeException('No está instalado PhpSpreadsheet. Ejecuta: composer require phpoffice/phpspreadsheet');
        }

        $spreadsheet = IOFactory::load($fullPath);

        foreach ($spreadsheet->getWorksheetIterator() as $sheet) {
            $rows = $sheet->toArray(null, true, true, true);
            $parsed = $this->parseRowsReporte($rows);

            if (!empty($parsed['data'])) return $parsed;
        }

        return ['data' => [], 'names' => [], 'period_start' => null, 'period_end' => null];
    }

    private function parseRowsReporte(array $rows): array
    {
        $periodStart = null;
        $periodEnd   = null;

        foreach ($rows as $r) {
            foreach ($r as $cell) {
                $text = is_string($cell) ? $cell : '';
                if (preg_match('/(\d{4}-\d{2}-\d{2})\s*~\s*(\d{4}-\d{2}-\d{2})/u', $text, $m)) {
                    $periodStart = Carbon::parse($m[1]);
                    $periodEnd   = Carbon::parse($m[2]);
                    break 2;
                }
            }
        }

        if (!$periodStart || !$periodEnd) {
            return ['data' => [], 'names' => [], 'period_start' => null, 'period_end' => null];
        }

        $dayRowIndex = null;
        $dayCols = [];

        foreach ($rows as $idx => $r) {
            $hits = 0;
            $tmp  = [];

            foreach ($r as $col => $cell) {
                $raw = is_null($cell) ? '' : trim((string) $cell);
                if ($raw !== '' && ctype_digit($raw)) {
                    $val = (int) $raw;
                    if ($val >= 1 && $val <= 31) {
                        $hits++;
                        $tmp[$col] = $val;
                    }
                }
            }

            if ($hits >= 10) {
                $dayRowIndex = $idx;
                $dayCols = $tmp;
                break;
            }
        }

        if (!$dayRowIndex || empty($dayCols)) {
            return [
                'data' => [],
                'names' => [],
                'period_start' => $periodStart->toDateString(),
                'period_end'   => $periodEnd->toDateString(),
            ];
        }

        $data = [];
        $names = [];
        $currentEmp = null;

        for ($i = $dayRowIndex + 1; $i <= count($rows); $i++) {
            if (!isset($rows[$i])) continue;
            $row = $rows[$i];

            $emp = $this->extractEmployeeIdFromRow($row);
            if ($emp) {
                $currentEmp = $emp;
                if (!isset($data[$currentEmp])) $data[$currentEmp] = [];

                $name = $this->extractEmployeeNameFromRow($row);
                if ($name) $names[$currentEmp] = $name;

                continue;
            }

            if (!$currentEmp) continue;

            foreach ($dayCols as $colLetter => $dayNumber) {
                $cell = $row[$colLetter] ?? null;
                if ($cell === null || $cell === '') continue;

                $times = $this->extractTimesFromCell($cell);
                if (!$times) continue;

                $date = $periodStart->copy()->day($dayNumber);
                if ($date->lt($periodStart) || $date->gt($periodEnd)) continue;

                $ymd = $date->toDateString();

                if (!isset($data[$currentEmp][$ymd])) $data[$currentEmp][$ymd] = [];
                $data[$currentEmp][$ymd] = array_values(array_unique(array_merge($data[$currentEmp][$ymd], $times)));
                sort($data[$currentEmp][$ymd]);
            }
        }

        return [
            'data'         => $data,
            'names'        => $names,
            'period_start' => $periodStart->toDateString(),
            'period_end'   => $periodEnd->toDateString(),
        ];
    }

    private function extractEmployeeIdFromRow(array $row): ?string
    {
        $cols  = array_keys($row);
        $count = count($cols);

        for ($i = 0; $i < $count; $i++) {
            $cell = $row[$cols[$i]] ?? null;
            $text = is_null($cell) ? '' : trim((string) $cell);
            if ($text === '') continue;

            if (preg_match('/^ID\s*:?\s*$/iu', $text)) {
                for ($j = $i + 1; $j < $count; $j++) {
                    $next = $row[$cols[$j]] ?? null;
                    $nextText = is_null($next) ? '' : trim((string) $next);
                    if ($nextText === '') continue;

                    if (preg_match('/^\d+(\.0+)?$/', $nextText)) {
                        return (string) ((int) ((float) $nextText));
                    }
                }
            }

            if (preg_match('/\bID\s*:\s*([0-9]{1,20}(?:\.0+)?)\b/iu', $text, $m)) {
                return (string) ((int) ((float) trim($m[1])));
            }
        }

        return null;
    }

    private function extractEmployeeNameFromRow(array $row): ?string
    {
        $cols  = array_keys($row);
        $count = count($cols);

        for ($i = 0; $i < $count; $i++) {
            $cell = $row[$cols[$i]] ?? null;
            $text = is_null($cell) ? '' : trim((string) $cell);
            if ($text === '') continue;

            if (preg_match('/^Nombre\s*:?\s*$/iu', $text)) {
                for ($j = $i + 1; $j < $count; $j++) {
                    $next = $row[$cols[$j]] ?? null;
                    $nextText = is_null($next) ? '' : trim((string) $next);
                    if ($nextText === '') continue;

                    $nextText = preg_replace('/\s+/', ' ', $nextText);
                    return trim($nextText);
                }
            }

            if (preg_match('/\bNombre\s*:\s*(.+)$/iu', $text, $m)) {
                $name = preg_replace('/\s+/', ' ', $m[1]);
                return trim($name);
            }
        }

        return null;
    }

    /**
     * ✅ FIX: detecta horas pegadas "08:5718:02" y repetidas "09:0309:0318:01"
     */
    private function extractTimesFromCell($cell): array
    {
        $text = '';

        if (is_string($cell)) $text = $cell;
        elseif (is_numeric($cell)) $text = (string) $cell;

        $text = trim($text);
        if ($text === '') return [];

        $text = preg_replace('/(\d{2}:\d{2})(?=\d{2}:\d{2})/u', '$1 ', $text);
        preg_match_all('/([01]\d|2[0-3]):[0-5]\d/u', $text, $m);

        $times = $m[0] ?? [];
        $times = array_values(array_unique($times));
        sort($times);

        return $times;
    }

    private function pickHoraEntrada(array $times): ?string
    {
        if (empty($times)) return null;
        sort($times);

        foreach ($times as $t) {
            if ($t >= '05:00' && $t <= '12:00') return $t;
        }

        return $times[0] ?? null;
    }

    private function pickHoraSalida(array $times): ?string
    {
        if (empty($times)) return null;
        sort($times);

        if (count($times) === 1) return null;

        $candidate = null;
        foreach ($times as $t) {
            if ($t >= '15:00') $candidate = $t;
        }

        return $candidate ?: $times[count($times) - 1];
    }

    private function pickInRange(array $times, string $inicio, string $fin): ?string
    {
        if (empty($times)) return null;
        sort($times);
        foreach ($times as $t) if ($t >= $inicio && $t <= $fin) return $t;
        return null;
    }

    private function pickAfter(array $times, string $desde, string $hasta): ?string
    {
        if (empty($times)) return null;
        sort($times);
        foreach ($times as $t) if ($t > $desde && $t <= $hasta) return $t;
        return null;
    }

    private function calcularEstado(?string $entrada, ?string $almuerzoRegreso, ?string $comidaRegreso): string
    {
        $retardo = false;

        if ($entrada && $entrada > $this->ENTRADA_OK_HASTA && $entrada <= $this->ENTRADA_RETARDO_HASTA) $retardo = true;
        if ($almuerzoRegreso && $almuerzoRegreso > $this->ALMUERZO_FIN && $almuerzoRegreso <= $this->COMIDA_INICIO) $retardo = true;
        if ($comidaRegreso && $comidaRegreso > $this->COMIDA_FIN && $comidaRegreso < $this->SALIDA_OFICIAL) $retardo = true;

        return $retardo ? 'retardo' : 'asistencia';
    }

    /**
     * ✅ Normaliza ID/Nómina/checador:
     * - Soporta "9", "0009", 9.0, "ID: 9"
     */
    private function normalizeNomina($value): string
    {
        if ($value === null) return '';

        if (is_int($value) || is_float($value)) {
            $n = (int) round((float) $value);
            return (string) $n;
        }

        $s = trim((string) $value);
        $s = preg_replace('/\s+/', '', $s);
        if ($s === '') return '';

        if (preg_match('/^\d+(\.0+)?$/', $s)) {
            return (string) ((int) ((float) $s));
        }

        if (preg_match('/(\d+)/', $s, $m)) {
            $digits = ltrim($m[1], '0');
            return $digits === '' ? '0' : $digits;
        }

        $s2 = ltrim($s, '0');
        return $s2 === '' ? '0' : $s2;
    }

    /**
     * ✅ Mapa [clave_normalizada => user_id]
     * - checador_id (si existe)
     * - nomina
     * - id (fallback)
     */
    private function buildUserNominaMap(): array
    {
        $map = [];

        $cols = ['id', 'nomina', 'name'];
        if (Schema::hasColumn('users', 'checador_id')) {
            $cols[] = 'checador_id';
        }

        $users = User::select($cols)->get();

        foreach ($users as $u) {
            // 1) checador_id
            if (isset($u->checador_id) && $u->checador_id !== null && $u->checador_id !== '') {
                $k = $this->normalizeNomina($u->checador_id);
                if ($k !== '' && !isset($map[$k])) $map[$k] = (int) $u->id;
            }

            // 2) nomina
            $k2 = $this->normalizeNomina($u->nomina);
            if ($k2 !== '' && !isset($map[$k2])) $map[$k2] = (int) $u->id;

            // 3) id
            $k3 = $this->normalizeNomina($u->id);
            if ($k3 !== '' && !isset($map[$k3])) $map[$k3] = (int) $u->id;
        }

        return $map;
    }
}
