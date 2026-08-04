<?php

namespace App\Http\Controllers;

use App\Models\Movimiento;
use App\Models\Servicio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MovimientoController extends Controller
{
    public function guardar(Request $request, $id)
    {
        try {
            $servicio = Servicio::findOrFail($id);

            $movimientoExistente = Movimiento::where('servicio_id', $id)
                ->where('tipo_movimiento', $request->tipo_movimiento)
                ->exists();

            if ($movimientoExistente) {
                $tipoLegible = ucwords(str_replace('_', ' ', $request->tipo_movimiento));
                $mensaje = 'Ya existe un registro para el movimiento "' . $tipoLegible . '" de este equipo.';

                return $request->ajax()
                    ? response()->json(['message' => $mensaje], 422)
                    : redirect()->back()->with('error', $mensaje);
            }
    // Si aún no tiene un estado de proceso, establecerlo como "registro"
    if (empty($servicio->estado_proceso)) {
        $servicio->estado_proceso = 'registro';
        $servicio->save();
    }
            $validator = Validator::make($request->all(), [
                'descripcion' => 'required|string',
  'evidencia1' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp,heic,heif',
                'evidencia2' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp,heic,heif',
                'evidencia3' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp,heic,heif',
                'video-evidencia' => 'nullable|file|mimetypes:video/mp4,video/avi,video/mpeg,video/webm,video/quicktime,video/x-msvideo,video/x-flv,video/3gpp,video/3gpp2,video/x-matroska,video/x-ms-wmv,video/hevc,video/h265,video/mp2t,video/ogg,video/x-matroska,video/mkv',
                'checklist' => 'nullable|array',
                'checklist.*' => 'string',
            ]);

            if ($validator->fails()) {
                return $request->ajax()
                    ? response()->json(['message' => 'Errores de validación.', 'errors' => $validator->errors()], 422)
                    : back()->withErrors($validator)->withInput();
            }

            $evidencia1 = $request->file('evidencia1')?->store('movimientos', 'public');
            $evidencia2 = $request->file('evidencia2')?->store('movimientos', 'public');
            $evidencia3 = $request->file('evidencia3')?->store('movimientos', 'public');
            $video = $request->file('video')?->store('movimientos', 'public');

            $movimiento = new Movimiento([
                'servicio_id' => $servicio->id,
                'tipo_movimiento' => $request->tipo_movimiento,
                'descripcion' => $request->descripcion,
                'checklist' => json_encode($request->checklist),
                'evidencia1' => $evidencia1,
                'evidencia2' => $evidencia2,
                'evidencia3' => $evidencia3,
                'video' => $video,
            ]);

            $movimiento->save();

            // Actualizar estado del servicio
            $this->actualizarEstado($servicio, $request->tipo_movimiento);

            return $request->ajax()
                ? response()->json(['message' => 'Movimiento guardado con éxito.'])
                : redirect()->back()->with('success', 'Movimiento guardado con éxito');
        } catch (\Exception $e) {
            return $request->ajax()
                ? response()->json(['message' => 'Error inesperado.', 'error' => $e->getMessage()], 500)
                : redirect()->back()->with('error', 'Error al guardar: ' . $e->getMessage());
        }
    }

    private function actualizarEstado($servicio, $tipo)
    {
        switch ($tipo) {
            case 'salida_mantenimiento':
                $servicio->estado_proceso = 'salida';
                break;
            case 'entrada_mantenimiento':
                $servicio->estado_proceso = 'regreso';
                break;
            case 'salida_dueno':
                $servicio->estado_proceso = 'entregado';
                break;
            case 'entrada_dueno':
                $servicio->estado_proceso = 'recibido';
                break;
        }

        $servicio->save();
    }

    public function vista($tipo, $id)
    {
        $servicio = Servicio::findOrFail($id);
        return view("movimientos.$tipo", compact('servicio', 'id'));
    }

    public function salidaMantenimiento($id)
    {
        $servicio = Servicio::findOrFail($id);
        return view('movimientos.salida_mantenimiento', compact('servicio', 'id'));
    }
    public function entradaMantenimiento($id)
{
    $servicio = Servicio::findOrFail($id);
    return view('movimientos.entrada_mantenimiento', compact('servicio', 'id'));
}
public function salidaDueno($id)
{
    $servicio = Servicio::findOrFail($id);
    return view('movimientos.salida_dueno', compact('servicio', 'id'));
}


}
