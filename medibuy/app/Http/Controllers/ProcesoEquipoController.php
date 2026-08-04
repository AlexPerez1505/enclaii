<?php

namespace App\Http\Controllers;

use App\Models\ProcesoEquipo;
use App\Models\Registro;
use App\Models\FichaTecnica;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ProcesoEquipoController extends Controller
{

    /**
     * Guarda un proceso (hojalatería, mantenimiento, stock, etc.)
     * y opcionalmente lo clona a un lote de registros.
     */
    public function guardarProceso(Request $request, $id)
    {
        try {
            $registro = Registro::findOrFail($id);

            // Validación
            $validator = Validator::make($request->all(), [
                'tipo_proceso'         => 'required|string|max:100',
                'descripcion_proceso'  => 'required|string',

                'evidencia1'           => 'nullable|file|mimes:jpg,jpeg,png,gif,webp,heic,heif|max:20480',
                'evidencia2'           => 'nullable|file|mimes:jpg,jpeg,png,gif,webp,heic,heif|max:20480',
                'evidencia3'           => 'nullable|file|mimes:jpg,jpeg,png,gif,webp,heic,heif|max:20480',
                'video'                => 'nullable|file|mimetypes:video/mp4,video/quicktime,video/webm,video/mpeg|max:512000',
                'documentoPDF'         => 'nullable|file|mimes:pdf|max:102400',

                'ficha_tecnica_id'     => 'nullable|exists:fichas_tecnicas,id',
                'defectos'             => 'nullable|array',
                'defectos.*'           => 'string',

                'checklist_json'       => 'nullable|string',

                // 🔹 lote ids JSON: "[15,16,17]"
                'lote_ids'             => 'nullable|string',

                // 🔹 checkbox aplicar a lote
                'aplicar_lote'         => 'nullable|boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Errores de validación.',
                    'errors'  => $validator->errors(),
                ], 422);
            }

            $aplicarLote = $request->boolean('aplicar_lote');

            // Evitar duplicar mismo tipo de proceso en ESTE registro
            $procesoExistente = ProcesoEquipo::where('registro_id', $id)
                ->where('tipo_proceso', $request->tipo_proceso)
                ->exists();

            if ($procesoExistente) {
                $tipoLegible = ucwords(str_replace('_', ' ', $request->tipo_proceso));
                return response()->json([
                    'message' => 'Ya se ha realizado el proceso de tipo "' . $tipoLegible . '" para este equipo.',
                ], 422);
            }

            DB::beginTransaction();

            // ==== Archivos ====
            $evidencia1 = $request->file('evidencia1')
                ? $request->file('evidencia1')->store('procesos', 'public')
                : null;

            $evidencia2 = $request->file('evidencia2')
                ? $request->file('evidencia2')->store('procesos', 'public')
                : null;

            $evidencia3 = $request->file('evidencia3')
                ? $request->file('evidencia3')->store('procesos', 'public')
                : null;

            $video = $request->file('video')
                ? $request->file('video')->store('procesos', 'public')
                : null;

            $documentoPDF = $request->file('documentoPDF')
                ? $request->file('documentoPDF')->store('documentos', 'public')
                : null;

            // ==== Checklist ====
            $checklistArray = null;
            if ($request->filled('checklist_json')) {
                $decoded = json_decode($request->input('checklist_json'), true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $checklistArray = $decoded;
                }
            }

            // ==== Crear Proceso principal (para este equipo) ====
            $proceso = new ProcesoEquipo([
                'registro_id'         => $registro->id,
                'tipo_proceso'        => $request->tipo_proceso,
                'descripcion_proceso' => $request->descripcion_proceso,
                'evidencia1'          => $evidencia1,
                'evidencia2'          => $evidencia2,
                'evidencia3'          => $evidencia3,
                'video'               => $video,
                'documento_pdf'       => $documentoPDF,
                'ficha_tecnica_id'    => $request->ficha_tecnica_id,
                'defectos'            => $request->input('defectos', null),
                'checklist'           => $checklistArray,
            ]);
            $proceso->save();

            // Apuntar el último proceso y actualizar estado de ESTE registro
            $registro->proceso_id = $proceso->id;

            if ($request->tipo_proceso === 'defectuoso') {
                $registro->estado_proceso = 'defectuoso';
                $registro->save();
            } elseif ($request->tipo_proceso === 'vendido') {
                // Vendido no se clona a lote
                $registro->estado_proceso = 'vendido';
                $registro->save();
            } else {
                $this->actualizarEstado($registro);
            }

            // ==== Lote: clonar a otros registros SOLO si marcaron checkbox y no es "vendido" ====
            if ($aplicarLote && $request->tipo_proceso !== 'vendido') {
                $loteRaw = $request->input('lote_ids', '[]');
                $loteIds = collect();

                if ($loteRaw !== null && $loteRaw !== '') {
                    $decoded = json_decode($loteRaw, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                        $loteIds = collect($decoded)
                            ->filter(fn($v) => is_numeric($v))
                            ->map(fn($v) => (int) $v)
                            ->unique()
                            ->values();
                    }
                }

                // quitamos el ID principal del lote
                $loteIds = $loteIds->reject(fn($lid) => $lid === $registro->id);

                if ($loteIds->isNotEmpty()) {
                    foreach ($loteIds as $otroId) {
                        $otro = Registro::find($otroId);
                        if (!$otro) continue;

                        $yaExiste = ProcesoEquipo::where('registro_id', $otro->id)
                            ->where('tipo_proceso', $request->tipo_proceso)
                            ->exists();

                        if ($yaExiste) continue;

                        $clon = $proceso->replicate();
                        $clon->registro_id = $otro->id;
                        $clon->created_at  = now();
                        $clon->updated_at  = now();
                        $clon->save();

                        $otro->proceso_id = $clon->id;

                        if ($request->tipo_proceso === 'defectuoso') {
                            $otro->estado_proceso = 'defectuoso';
                            $otro->save();
                        } else {
                            $this->actualizarEstado($otro);
                        }
                    }
                }
            }

            DB::commit();

            return response()->json([
                'message' => 'Proceso guardado con éxito.',
                'proceso' => [
                    'id'           => $proceso->id,
                    'tipo_proceso' => $proceso->tipo_proceso,
                    'created_at'   => optional($proceso->created_at)->toDateTimeString(),
                ],
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error al guardar proceso de equipo', [
                'registro_id' => $id,
                'error'       => $e->getMessage(),
                'trace'       => $e->getTraceAsString(),
            ]);

            return response()->json([
                'message' => 'Ocurrió un error inesperado.',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * ✅ ELIMINAR SOLO 1 PROCESO (sin borrar posteriores)
     * Ajusta proceso_id y estado_proceso para que no queden apuntando a un proceso que ya no existe.
     */
    public function eliminarProceso(Request $request, $registroId, $procesoId)
    {
        try {
            $registro = Registro::findOrFail($registroId);

            // Proceso debe pertenecer a este registro
            $proceso = ProcesoEquipo::where('registro_id', $registroId)->findOrFail($procesoId);

            DB::beginTransaction();

            $eraUltimo = ((int)($registro->proceso_id ?? 0) === (int)$proceso->id);

            // ⚠️ Borramos SOLO ese proceso
            $proceso->delete();

            // Recalcular estado/proceso_id si era el apuntado o si ya no existe el apuntado
            $this->recalcularEstadoDesdeProcesos($registro);

            DB::commit();

            return response()->json([
                'message'    => 'Proceso eliminado.',
                'estado'     => $registro->estado_proceso,
                'proceso_id' => $registro->proceso_id,
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('ELIMINAR_PROCESO_FAIL', [
                'registro_id' => $registroId,
                'proceso_id'  => $procesoId,
                'error'       => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Error al eliminar proceso.',
                'error'   => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Vista de proceso "stock".
     */
    public function stock($id)
    {
        $registro = Registro::findOrFail($id);

        $fichas = collect(); // o FichaTecnica::all()

        return view('procesos.stock', compact('registro', 'fichas', 'id'));
    }

    /**
     * Muestra formulario de Hojalatería para un registro.
     */
    public function mostrarProceso($id)
    {
        $registro       = Registro::findOrFail($id);
        $subtipo_equipo = $registro->subtipo_equipo;
        $success        = session('success');

        return view('procesos.hojalateria', [
            'registro'       => $registro,
            'subtipo_equipo' => $subtipo_equipo,
            'success'        => $success,
            'id'             => $id,
        ]);
    }

    /**
     * Avanza el estado_proceso según el flujo:
     * registro → hojalateria → mantenimiento → stock → vendido
     * (No toca 'defectuoso' ni 'vendido')
     */
    private function actualizarEstado(Registro $registro): void
    {
        if (in_array($registro->estado_proceso, ['defectuoso', 'vendido'], true)) {
            return;
        }

        $estado = $registro->estado_proceso ?: 'registro';

        switch ($estado) {
            case 'registro':
                $registro->estado_proceso = 'hojalateria';
                break;
            case 'hojalateria':
                $registro->estado_proceso = 'mantenimiento';
                break;
            case 'mantenimiento':
                $registro->estado_proceso = 'stock';
                break;
            case 'stock':
                $registro->estado_proceso = 'vendido';
                break;
            default:
                return;
        }

        $registro->save();
    }

    /**
     * ✅ Recalcula estado_proceso y proceso_id SOLO con lo que exista en DB.
     * - Si existe "defectuoso" => prioridad
     * - Si no, toma el proceso más alto del flujo (vendido > stock > mantenimiento > hojalateria)
     * - Si no hay procesos => registro
     */
    private function recalcularEstadoDesdeProcesos(Registro $registro): void
    {
        $orden = ['registro','hojalateria','mantenimiento','stock','vendido'];

        // 1) Si hay defectuoso, manda defectuoso
        $def = ProcesoEquipo::where('registro_id', $registro->id)
            ->where('tipo_proceso', 'defectuoso')
            ->latest('created_at')
            ->first();

        if ($def) {
            $registro->estado_proceso = 'defectuoso';
            $registro->proceso_id     = $def->id;
            $registro->save();
            return;
        }

        // 2) Hallar el mejor (más alto) dentro del flujo
        $procesos = ProcesoEquipo::where('registro_id', $registro->id)->get();

        $mejor = null;
        $mejorIdx = -1;

        foreach ($procesos as $p) {
            $idx = array_search($p->tipo_proceso, $orden, true);
            if ($idx !== false && $idx > $mejorIdx) {
                $mejorIdx = $idx;
                $mejor = $p;
            }
        }

        if ($mejor) {
            $registro->estado_proceso = $mejor->tipo_proceso;
            $registro->proceso_id     = $mejor->id;
        } else {
            $registro->estado_proceso = 'registro';
            $registro->proceso_id     = null;
        }

        $registro->save();
    }
         public function modificarPasos(Request $request, Registro $registro)
{
    $request->validate([
        'pasos'   => 'required|array|min:1',
        'pasos.*' => 'in:hojalateria,mantenimiento,stock,vendido',
    ]);

    $registro->tipo_pasos = $request->input('pasos'); // el cast 'array' lo serializa solo
    $registro->save();

    return response()->json([
        'message' => 'Procesos actualizados correctamente.'
    ]);
}
}