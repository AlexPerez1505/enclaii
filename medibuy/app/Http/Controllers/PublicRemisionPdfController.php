<?php

namespace App\Http\Controllers;

use App\Models\Remision;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use PDF;

class PublicRemisionPdfController extends Controller
{
    public function ticketMantenimiento(Request $request, Remision $remision)
    {
        if (! $request->hasValidSignature()) {
            abort(401, 'Link inválido o expirado');
        }

        $remision->loadMissing('cliente', 'user', 'items');

        $dir    = trim((string) env('WA_MAINT_REMISION_PDF_DIR', 'remisiones')) ?: 'remisiones';
        $prefix = trim((string) env('WA_MAINT_REMISION_PDF_PREFIX', 'remision_mantenimiento')) ?: 'remision_mantenimiento';

        $path = "{$dir}/{$prefix}_{$remision->id}.pdf";

        if (!Storage::disk('public')->exists($dir)) {
            Storage::disk('public')->makeDirectory($dir);
        }

        if (!Storage::disk('public')->exists($path)) {
            try {
                $qr_path = null;

                $view = $this->pickPdfView();
                $pdf = PDF::loadView($view, compact('remision', 'qr_path'));
                try { $pdf->setOptions(['isRemoteEnabled' => true]); } catch (\Throwable $e) {}

                Storage::disk('public')->put($path, $pdf->output());
            } catch (\Throwable $e) {
                Log::error('PUBLIC_REMISION_PDF_BUILD_FAIL', [
                    'remision_id' => $remision->id,
                    'e' => $e->getMessage(),
                    'line' => $e->getLine(),
                ]);
                abort(500, 'No se pudo generar el PDF');
            }
        }

        if (!Storage::disk('public')->exists($path)) {
            abort(500, 'El PDF no está disponible');
        }

        $full = Storage::disk('public')->path($path);

        return response()->file($full, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$prefix.'_'.$remision->id.'.pdf"',
            'Cache-Control'       => 'no-store, no-cache, must-revalidate, max-age=0',
            'Pragma'              => 'no-cache',
        ]);
    }

    private function pickPdfView(): string
    {
        $envView = trim((string) env('WA_MAINT_REMISION_PDF_VIEW', ''));
        if ($envView !== '' && View::exists($envView)) return $envView;

        foreach ([
            'remisions.remision_pdf',
            'remisions.remision',
            'remisions.pdf',
            'remisions.ticket',
            'remisions.ticket_mantenimiento',
        ] as $v) {
            if (View::exists($v)) return $v;
        }

        return 'remisions.ticket_mantenimiento';
    }
}
