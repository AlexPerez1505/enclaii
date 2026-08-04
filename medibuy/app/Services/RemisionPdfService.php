<?php

namespace App\Services;

use App\Models\Remision;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use PDF;

class RemisionPdfService
{
    /**
     * Genera PDF y lo guarda como archivo temporal.
     * Retorna: absolute path del PDF.
     */
    public function buildMaintenancePdfToTemp(Remision $remision): string
    {
        $view = (string) env('WA_REMISION_PDF_VIEW', 'remisions.pdf_mantenimiento');
        $timeout = (int) env('WA_REMISION_PDF_TIMEOUT', 60);

        $remision->loadMissing('cliente','items','user');

        $qr_path = null; // si luego metes QR, aquí

        $pdf = PDF::loadView($view, compact('remision', 'qr_path'));
        $pdf->setOptions(['isRemoteEnabled' => true]);
        // dompdf timeout (depende del wrapper, pero ayuda)
        try { $pdf->setOption('defaultFont', 'Arial'); } catch (\Throwable $e) {}

        $dir = storage_path('app/tmp-wa');
        if (!File::exists($dir)) File::makeDirectory($dir, 0755, true);

        $filename = 'orden_servicio_'.$remision->id.'_'.date('Ymd_His').'.pdf';
        $path = $dir . DIRECTORY_SEPARATOR . $filename;

        file_put_contents($path, $pdf->output());

        Log::info('WA_PDF_BUILT', ['remision_id'=>$remision->id, 'path'=>$path]);

        return $path;
    }
}
