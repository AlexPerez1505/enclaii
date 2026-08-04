<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

use App\Models\Venta;
use App\Models\Checklist;
use App\Models\ChecklistIngenieria;
use App\Models\ChecklistEmbalaje;
use App\Models\ChecklistEntrega;

use Barryvdh\DomPDF\Facade\Pdf;

class ChecklistController extends Controller
{
    public function __construct()
    {
        // ✅ Protege todo el controlador con autenticación
        $this->middleware('auth');
    }

    /** PASO 0: Wizard general */
    public function wizard($ventaId)
    {
        $venta = Venta::findOrFail($ventaId);
        $checklist = Checklist::firstOrCreate(['venta_id' => $ventaId]);

        $ingenieria = $checklist->ingenieria ?? null;
        $embalaje   = $checklist->embalaje   ?? null;
        $entrega    = $checklist->entrega    ?? null;

        if (!$ingenieria) {
            $progresoNombre = "Ingeniería";
            $progresoPorc   = 0;
            $rutaSiguiente  = 'checklists.ingenieria';
        } elseif (!$embalaje) {
            $progresoNombre = "Embalaje";
            $progresoPorc   = 50;
            $rutaSiguiente  = 'checklists.embalaje';
        } elseif (!$entrega) {
            $progresoNombre = "Entrega";
            $progresoPorc   = 100;
            $rutaSiguiente  = 'checklists.entrega';
        } else {
            $progresoNombre = "Checklist Finalizado";
            $progresoPorc   = 100;
            $rutaSiguiente  = null;
        }

        return view('checklists.wizard', compact(
            'venta','progresoNombre','progresoPorc','rutaSiguiente','checklist'
        ));
    }

    /** PASO 1: Vista de Ingeniería */
    public function ingenieria($ventaId)
    {
        $venta = Venta::findOrFail($ventaId);

        $productos = DB::table('venta_productos')
            ->join('productos', 'venta_productos.producto_id', '=', 'productos.id')
            ->leftJoin('registros', 'venta_productos.registro_id', '=', 'registros.id')
            ->where('venta_productos.venta_id', $ventaId)
            ->select(
                'venta_productos.*',
                'productos.tipo_equipo',
                'productos.modelo',
                'productos.marca',
                'registros.numero_serie',
                'registros.id as registro_id'
            )
            ->get();

        return view('checklists.ingenieria', compact('venta', 'productos'));
    }

    /** Guardar Ingeniería (flujo genérico) */
    public function guardarIngenieria(Request $request, $ventaId)
    {
        return $this->guardarEtapaGenerica($request, $ventaId, 'ingenieria');
    }

    /** PASO 2: Vista de Embalaje */
    public function embalaje($ventaId)
    {
        $venta = Venta::findOrFail($ventaId);

        $productos = DB::table('venta_productos')
            ->join('productos', 'venta_productos.producto_id', '=', 'productos.id')
            ->leftJoin('registros', 'venta_productos.registro_id', '=', 'registros.id')
            ->where('venta_productos.venta_id', $ventaId)
            ->select(
                'venta_productos.*',
                'productos.tipo_equipo',
                'productos.modelo',
                'productos.marca',
                'registros.numero_serie',
                'registros.id as registro_id'
            )
            ->get();

        $checklist  = Checklist::where('venta_id', $ventaId)->first();
        $ingenieria = $checklist?->ingenieria;
        $embalaje   = $checklist?->embalaje;

        $firmaGuardada         = $embalaje->firma_responsable ?? null;
        $reporteIngeniero      = $ingenieria->observaciones ?? $ingenieria->incidente ?? null;
        $ingenieriaComponentes = $ingenieria?->componentes ?? null;
        $checklistEmbalaje     = $embalaje ?? null;

        return view('checklists.embalaje', compact(
            'venta','productos','firmaGuardada','reporteIngeniero','checklistEmbalaje','ingenieria','ingenieriaComponentes','checklist'
        ));
    }

    /** Guardar Embalaje (flujo genérico) */
    public function guardarEmbalaje(Request $request, $ventaId)
    {
        return $this->guardarEtapaGenerica($request, $ventaId, 'embalaje');
    }

    /** PASO 3: Vista de Entrega (sin QR/escáner) */
    public function entrega($ventaId)
    {
        $venta = Venta::findOrFail($ventaId);

        $checklist  = Checklist::where('venta_id', $ventaId)->first();
        $ingenieria = $checklist?->ingenieria;
        $embalaje   = $checklist?->embalaje;

        $productos = DB::table('venta_productos')
            ->join('productos', 'venta_productos.producto_id', '=', 'productos.id')
            ->leftJoin('registros', 'venta_productos.registro_id', '=', 'registros.id')
            ->where('venta_productos.venta_id', $ventaId)
            ->select(
                'venta_productos.*',
                'productos.tipo_equipo',
                'productos.modelo',
                'productos.marca',
                'registros.numero_serie',
                'registros.id as registro_id'
            )
            ->get();

        return view('checklists.entrega', compact(
            'venta','productos','checklist','ingenieria','embalaje'
        ));
    }

    /** Guardar Entrega (flujo genérico) */
    public function guardarEntrega(Request $request, $ventaId)
    {
        return $this->guardarEtapaGenerica($request, $ventaId, 'entrega');
    }

    /** PDF (sin recepción) */
    public function descargarPdf(Request $request, $checklistId)
    {
        $checklist = Checklist::with([
            'venta',
            'ingenieria.usuario',
            'embalaje.usuario',
            'entrega.usuario',
        ])->findOrFail($checklistId);

        $venta      = $checklist->venta;
        $ingenieria = $checklist->ingenieria;
        $embalaje   = $checklist->embalaje;
        $entrega    = $checklist->entrega;

        // Productos con serie desde registros
        $productos = collect();
        if ($venta) {
            $productos = DB::table('venta_productos')
                ->join('productos', 'venta_productos.producto_id', '=', 'productos.id')
                ->leftJoin('registros', 'venta_productos.registro_id', '=', 'registros.id')
                ->where('venta_productos.venta_id', $venta->id)
                ->select(
                    'venta_productos.*',
                    'productos.tipo_equipo',
                    'productos.modelo',
                    'productos.marca',
                    'productos.id',
                    'registros.numero_serie'
                )
                ->get();
        }

        $pdf = Pdf::loadView(
            'checklists.pdf-reporte',
            compact('checklist','venta','ingenieria','embalaje','entrega','productos')
        )->setPaper('a4', 'portrait');

        $nombre = 'Checklist_Reporte_' . ($venta->folio ?? $checklist->id) . '.pdf';

        return $request->boolean('inline')
            ? $pdf->stream($nombre)
            : $pdf->download($nombre);
    }

    /* ==========================
     *      FLUJO GENÉRICO
     * ========================== */

    private const STAGES = [
        'ingenieria' => [
            'model'          => ChecklistIngenieria::class,
            'sig_fields'     => ['firma_responsable','firma_supervisor'],
            'obs_candidates' => ['ingenieria_incidente','observaciones','comentario_final','incidente'],
            'folder'         => 'ingenieria',
        ],
        'embalaje' => [
            'model'          => ChecklistEmbalaje::class,
            'sig_fields'     => ['firma_responsable','firma_supervisor'],
            'obs_candidates' => ['embalaje_observacion','observaciones','comentario_final'],
            'folder'         => 'embalaje',
        ],
        'entrega' => [
            'model'          => ChecklistEntrega::class,
            'sig_fields'     => ['firma_cliente','firma_entrega'], // en Envío puede faltar firma_cliente (OK)
            'obs_candidates' => ['comentario_final','observaciones'],
            'folder'         => 'entrega',
        ],
    ];

    private function guardarEtapaGenerica(Request $request, $ventaId, string $stage)
    {
        $userId = auth()->id();
        abort_unless($userId, 403, 'No autenticado');

        $venta = Venta::findOrFail($ventaId);
        $cfg   = self::STAGES[$stage] ?? null;
        abort_unless($cfg, 500, 'Etapa inválida');

        $checklist = Checklist::firstOrCreate(['venta_id' => $venta->id]);

        $request->validate([
            'verificados'  => ['nullable','string'], // JSON array
            'componentes'  => ['nullable','string'], // JSON object
            'evidencias.*' => ['nullable','file','mimes:jpg,jpeg,png,webp,pdf','max:10240'],
        ]);

        $verificados = $this->decodeJsonArray($request->input('verificados'));
        $componentes = $this->decodeJsonObject($request->input('componentes'));

        // Series esperadas SIEMPRE desde registros.numero_serie
        $esperados = $this->seriesEsperadas($venta);
        if ($esperados->isEmpty() && $request->filled('productosEsperados')) {
            $esperados = collect($request->input('productosEsperados'))->filter()->values();
        }
        $noVerificados = $esperados->diff($verificados)->values()->all();

        $obs = $this->firstFilled($request, $cfg['obs_candidates']);

        /** @var \Illuminate\Database\Eloquent\Model $model */
        $model = ($cfg['model'])::firstOrNew(['checklist_id' => $checklist->id]);
        $model->user_id        = $userId;
        $model->verificados    = $verificados;
        $model->no_verificados = $noVerificados;
        $model->componentes    = $componentes;
        $model->observaciones  = $obs;

        // En Ingeniería guardamos también "incidente" por compatibilidad
        if ($stage === 'ingenieria') {
            $model->incidente = $obs;
        }

        // En ENTREGA guardamos el payload útil (sin firmas/evidencias/_token)
        if ($stage === 'entrega') {
            $payload = $request->except(['_token','firma_cliente','firma_entrega','evidencias','verificados','componentes']);
            $model->datos_entrega = json_encode($payload);
        }

        // Firmas (en Envío puede no venir firma_cliente; OK)
        foreach ($cfg['sig_fields'] as $campo) {
            if ($request->filled($campo)) {
                $model->$campo = $this->saveDataUrl(
                    $request->input($campo),
                    "checklists/{$cfg['folder']}/venta_{$venta->id}/firmas",
                    $campo
                );
            }
        }

        $model->save();

        // Evidencias
        $model->evidencias = $this->storeEvidences(
            $request,
            "checklists/{$cfg['folder']}/venta_{$venta->id}/evidencias/{$model->id}"
        );
        $model->save();

        Log::info('Checklist etapa guardada', [
            'venta_id'       => $venta->id,
            'etapa'          => $stage,
            'verificados'    => $model->verificados,
            'no_verificados' => $model->no_verificados,
        ]);

        return redirect()->route('checklists.wizard', $venta->id)
            ->with('success', ucfirst($stage).' guardado correctamente.');
    }

    /* ===== Helpers ===== */

    private function decodeJsonArray(?string $json): array
    {
        if (!$json) return [];
        $arr = json_decode($json, true);
        if (!is_array($arr)) return [];
        return collect($arr)->filter(fn($s) => filled($s))->unique()->values()->all();
    }

    private function decodeJsonObject(?string $json): array
    {
        if (!$json) return [];
        $obj = json_decode($json, true);
        return is_array($obj) ? $obj : [];
    }

    private function firstFilled(Request $request, array $candidates): ?string
    {
        foreach ($candidates as $key) {
            if ($request->filled($key)) return (string) $request->input($key);
        }
        return null;
    }

    /**
     * Series esperadas desde registros.numero_serie vía registro_id
     */
    private function seriesEsperadas(Venta $venta)
    {
        return DB::table('venta_productos')
            ->leftJoin('registros', 'venta_productos.registro_id', '=', 'registros.id')
            ->where('venta_productos.venta_id', $venta->id)
            ->pluck('registros.numero_serie')
            ->filter()
            ->unique()
            ->values();
    }

    /** Guarda dataURL en /storage y devuelve 'storage/...' */
    private function saveDataUrl(?string $dataUrl, string $dir, string $prefix): ?string
    {
        if (!$dataUrl || !str_starts_with($dataUrl, 'data:image')) return null;

        [$meta, $content] = explode(',', $dataUrl, 2);
        $ext = 'png';
        if (preg_match('/image\/([a-zA-Z0-9]+)/', $meta, $m)) {
            $ext = strtolower($m[1] ?? 'png');
        }
        $binary = base64_decode($content);
        if ($binary === false) return null;

        $filename = $prefix.'_'.now()->format('Ymd_His').'_'.Str::random(6).'.'.$ext;
        $path = $dir.'/'.$filename;

        Storage::disk('public')->put($path, $binary);
        return 'storage/'.$path;
    }

    /** Guarda evidencias y devuelve rutas tipo 'storage/...' */
    private function storeEvidences(Request $request, string $dir): array
    {
        $paths = [];
        if ($request->hasFile('evidencias')) {
            foreach ($request->file('evidencias') as $file) {
                if (!$file) continue;
                $paths[] = 'storage/'.$file->store($dir, 'public');
            }
        }
        return $paths;
    }
}
