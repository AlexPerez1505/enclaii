<?php

namespace App\Http\Controllers;

use App\Models\Cotizacion;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use setasign\Fpdi\Fpdi;
use App\Models\FichaTecnica;
use App\Models\Paquete; // Asegúrate de importar el modelo correcto
use App\Models\Producto; // Asegúrate de importar el modelo correcto


class CotizacionController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'cliente' => 'nullable|array',
            'productos' => 'nullable|array',
            'subtotal' => 'nullable|numeric',
            'descuento' => 'nullable|numeric',
            'envio' => 'nullable|numeric',
            'iva' => 'nullable|numeric',
            'total' => 'nullable|numeric',
            'tipo_pago' => 'nullable|string',
            'plan_pagos' => 'nullable|array',
            'nota' => 'nullable|string',
            'valido_hasta' => 'nullable|date',
            'lugar_cotizacion' => 'nullable|string',
            'ficha_tecnica_id' => 'nullable|exists:fichas_tecnicas,id',
            'registrado_por' => 'nullable|string'
        ]);

        $cotizacion = Cotizacion::create([
            'cliente' => json_encode($request->cliente), // Guardamos como JSON
            'productos' => json_encode($request->productos),
            'subtotal' => $request->subtotal,
            'descuento' => $request->descuento,
            'envio' => $request->envio,
            'iva' => $request->iva,
            'total' => $request->total,
            'tipo_pago' => $request->tipo_pago,
            'plan_pagos' => json_encode($request->plan_pagos),
            'nota' => $request->nota,
            'valido_hasta' => $request->valido_hasta,
            'lugar_cotizacion' => $request->lugar_cotizacion,
            'registrado_por' => Auth::user()->name,
            'ficha_tecnica_id' => $request->ficha_tecnica_id,
        ]);

        return response()->json(['mensaje' => 'Cotización guardada', 'id' => $cotizacion->id]);
    }

    public function descargarPDF($id)
    {
        // Buscar la cotización con su ficha técnica
        $cotizacion = Cotizacion::with('fichaTecnica')->findOrFail($id);
    
        // Decodificar productos y cliente
        $productos = json_decode($cotizacion->productos, true) ?? [];
        $cliente = json_decode($cotizacion->cliente, true) ?? [];
    
        // Generar nombre para el archivo
        $nombreCliente = isset($cliente['nombre']) ? strtoupper(Str::slug($cliente['nombre'], '_')) : 'SIN_NOMBRE';
        $nombreArchivo = 'COTIZACION_' . $nombreCliente . '_' . now()->format('Ymd_His') . '.pdf';
        $rutaFinalPDF = storage_path('app/public/cotizaciones/' . $nombreArchivo);
    
        // Calcular vigencia
        $createdAt = Carbon::parse($cotizacion->created_at);
        $vigencia = $cotizacion->valido_hasta ? Carbon::parse($cotizacion->valido_hasta) : null;
        $diasRestantes = $vigencia ? $createdAt->diffInDays($vigencia) : 'Sin vigencia';
        $diasRestantes = $diasRestantes < 0 ? 'Vencido' : round($diasRestantes);
    
        // Generar PDF de cotización
        $pdfCotizacion = PDF::loadView('cotizacion.pdf', [
            'cotizacion' => $cotizacion,
            'productos' => $productos,
            'cliente' => $cliente,
            'diasRestantes' => $diasRestantes
        ]);
    
        // Guardar PDF temporal de la cotización
        $rutaTemporalCotizacion = storage_path('app/temp_cotizacion.pdf');
        file_put_contents($rutaTemporalCotizacion, $pdfCotizacion->output());
    
        // Crear PDF combinado
        $pdfFinal = new FPDI();
    
        // Páginas de la cotización
        $pageCount = $pdfFinal->setSourceFile($rutaTemporalCotizacion);
        for ($i = 1; $i <= $pageCount; $i++) {
            $tplId = $pdfFinal->importPage($i);
            $pdfFinal->AddPage();
            $pdfFinal->useTemplate($tplId);
        }
    
        // Ruta ficha técnica
        $rutaFichaTecnica = null;
        if ($cotizacion->ficha_tecnica_id && $cotizacion->fichaTecnica && $cotizacion->fichaTecnica->archivo) {
            $rutaFichaTecnica = storage_path('app/public/' . $cotizacion->fichaTecnica->archivo);
    
            // Si existe, agregar al PDF
            if (file_exists($rutaFichaTecnica)) {
                $pageCountFicha = $pdfFinal->setSourceFile($rutaFichaTecnica);
                for ($i = 1; $i <= $pageCountFicha; $i++) {
                    $tplId = $pdfFinal->importPage($i);
                    $pdfFinal->AddPage();
                    $pdfFinal->useTemplate($tplId);
                }
            }
        }
    
        // Guardar el PDF combinado en almacenamiento
        file_put_contents($rutaFinalPDF, $pdfFinal->Output('S'));
    
        // Guardar el nombre del archivo en la base de datos de la cotización (si lo necesitas)

        $cotizacion->save();
    
        // Descargar el archivo final
        return response()->download($rutaFinalPDF, $nombreArchivo)->deleteFileAfterSend(true);
    }
    
    
    
    public function mostrarFormulario()
    {
        $fichas = FichaTecnica::all();
        $paquetes = Paquete::all(); // Agrega esta línea para obtener los paquetes
        $productos = Producto::all(); // o el query que tú necesitas
    
        return view('cotizaciones', compact('fichas', 'paquetes','productos'));
    }
  
    
    // En tu controlador
    public function index()
    {
        // Obtener todas las cotizaciones
        $cotizaciones = Cotizacion::all();
    
        // Pasar las cotizaciones a la vista
        return view('historial', compact('cotizaciones'));
    }
    // app/Http/Controllers/CotizacionController.php

public function duplicar($id)
{
    $cotizacion = Cotizacion::findOrFail($id);

    // Decodifica los campos JSON para que sean usables en los formularios
    $cotizacion->cliente = json_decode($cotizacion->cliente, true);
    $cotizacion->productos = json_decode($cotizacion->productos, true);
    $cotizacion->plan_pagos = json_decode($cotizacion->plan_pagos, true);

    return view('cotizaciones', ['cotizacion' => $cotizacion, 'modo' => 'duplicar']);
}


}
