<?php

namespace App\Services;

use App\Models\Cliente;
use App\Models\Remision;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use NumberFormatter;
use PDF;

class RemisionAiTools
{
    public function createMaintenanceRemision(string $msisdn, array $args): array
    {
        // ✅ 1) Allowlist
        if (!$this->isAllowedMaintenanceMsisdn($msisdn)) {
            return [
                'ok' => false,
                'error' => 'not_allowed',
                'missing' => [],
                'message' => 'Este número no tiene permiso para generar remisión de mantenimiento.',
            ];
        }

        // ✅ 2) Campos mínimos
        $missing = [];
        foreach (['items', 'aplicar_iva', 'tiene_envio'] as $k) {
            if (!array_key_exists($k, $args)) $missing[] = $k;
        }

        $items = is_array($args['items'] ?? null) ? $args['items'] : [];
        if (count($items) < 1) {
            if (!in_array('items', $missing, true)) $missing[] = 'items';
        }

        // Reglas envío
        $tieneEnvio = (bool)($args['tiene_envio'] ?? false);
        if ($tieneEnvio) {
            $envioCosto = (float)($args['envio_costo'] ?? -1);
            $envioDir   = trim((string)($args['envio_direccion'] ?? ''));
            if ($envioCosto < 0) $missing[] = 'envio_costo';
            if ($envioDir === '') $missing[] = 'envio_direccion';
        }

        if (!empty($missing)) {
            return [
                'ok' => false,
                'error' => 'missing_fields',
                'missing' => array_values(array_unique($missing)),
            ];
        }

        // ✅ 3) Cliente por teléfono (últimos 10)
        $digits = preg_replace('/\D+/', '', (string)$msisdn);
        $last10 = substr($digits, -10);

        $cliente = null;
        if ($last10) {
            $cliente = Cliente::where('telefono', 'like', "%{$last10}%")->first();
        }

        // Si no existe, pedimos datos para crearlo
        if (!$cliente) {
            $cliName = trim((string)($args['cliente_nombre'] ?? ''));
            $cliLast = trim((string)($args['cliente_apellido'] ?? ''));
            $cliTel  = trim((string)($args['cliente_telefono'] ?? $last10));
            $cliMail = trim((string)($args['cliente_email'] ?? ''));
            $cliAddr = trim((string)($args['cliente_direccion'] ?? ''));

            $need = [];
            if ($cliName === '') $need[] = 'cliente_nombre';
            if ($cliLast === '') $need[] = 'cliente_apellido';
            if ($cliTel === '')  $need[] = 'cliente_telefono';
            if ($cliMail === '') $need[] = 'cliente_email';

            if (!empty($need)) {
                return [
                    'ok' => false,
                    'error' => 'cliente_not_found_need_data',
                    'missing' => $need,
                ];
            }

            $cliente = Cliente::create([
                'nombre'      => mb_strtoupper($cliName),
                'apellido'    => mb_strtoupper($cliLast),
                'telefono'    => $cliTel,
                'email'       => $cliMail,
                'comentarios' => $cliAddr !== '' ? mb_strtoupper($cliAddr) : 'CREADO DESDE WHATSAPP',
            ]);
        }

        // ✅ 4) Normaliza + limpia items (para que NO salga "No No ...")
        $items = $this->sanitizeItems($items);

        if (count($items) < 1) {
            return [
                'ok' => false,
                'error' => 'items_required',
                'missing' => ['items'],
            ];
        }

        $aplicarIva = (bool)($args['aplicar_iva'] ?? false);

        $envioCosto = $tieneEnvio ? round((float)($args['envio_costo'] ?? 0), 2) : 0;
        $envioDir   = $tieneEnvio ? trim((string)($args['envio_direccion'] ?? '')) : null;

        // ✅ 5) meta_pairs opcional -> lo metemos a descripción (pero NO lo exigimos)
        $metaPairs = is_array($args['meta_pairs'] ?? null) ? $args['meta_pairs'] : [];
        $metaText  = $this->metaPairsToText($metaPairs);

        try {
            $remision = null;

            DB::transaction(function () use (
                $cliente, $items, $aplicarIva, $tieneEnvio, $envioCosto, $envioDir, $metaText, &$remision
            ) {
                $subtotal = 0;
                foreach ($items as $it) {
                    $subtotal += ((int)$it['cantidad'] * (float)$it['importe_unitario']);
                }
                $subtotal = round($subtotal, 2);

                $iva   = $aplicarIva ? round($subtotal * 0.16, 2) : 0;
                $envio = $tieneEnvio ? round((float)$envioCosto, 2) : 0;
                $total = round($subtotal + $iva + $envio, 2);

                // Total en letra
                $parteEntera = (int)floor($total);
                $centavos    = (int)round(($total - $parteEntera) * 100);
                $formatter   = new NumberFormatter('es', NumberFormatter::SPELLOUT);

                $letra  = strtoupper($formatter->format($parteEntera)) . ' PESOS';
                $letra .= ' CON ' . str_pad((string)$centavos, 2, '0', STR_PAD_LEFT) . '/100 M.N.';

                $userId = Auth::id() ?: (int)env('WA_SYSTEM_USER_ID', 1);

                // ✅ IMPORTANTE: NO insertamos 'meta' (porque tu tabla NO tiene esa columna)
                $remisionData = [
                    'cliente_id'      => $cliente->id,
                    'user_id'         => $userId,
                    'subtotal'        => $subtotal,
                    'iva'             => $iva,
                    'aplicar_iva'     => $aplicarIva ? 1 : 0,
                    'tiene_envio'     => $tieneEnvio ? 1 : 0,
                    'envio_costo'     => $envio,
                    'envio_direccion' => $tieneEnvio ? ($envioDir ?: null) : null,
                    'total'           => $total,
                    'importe_letra'   => $letra,
                ];

                $remision = Remision::create($remisionData);

                $hasACuenta  = Schema::hasColumn('item_remisions', 'a_cuenta');
                $hasRestante = Schema::hasColumn('item_remisions', 'restante');

                foreach ($items as $it) {
                    $cantidad = (int)$it['cantidad'];
                    $precio   = (float)$it['importe_unitario'];
                    $itemSubtotal = round($cantidad * $precio, 2);

                    $desc = trim((string)($it['descripcion_item'] ?? ''));
                    // ✅ Meta solo como texto extra al final (opcional)
                    if ($metaText !== '') {
                        $desc = trim($desc . "\n\n" . $metaText);
                    }

                    $payload = [
                        'unidad'           => (string)($it['unidad'] ?? 'servicio'),
                        'cantidad'         => max(1, $cantidad),
                        'nombre_item'      => (string)($it['nombre_item'] ?? 'Servicio'),
                        'descripcion_item' => $desc !== '' ? $desc : null,
                        'importe_unitario' => $precio,
                        'subtotal'         => $itemSubtotal,
                    ];

                    if ($hasACuenta)  $payload['a_cuenta'] = 0;
                    if ($hasRestante) $payload['restante'] = $itemSubtotal;

                    $remision->items()->create($payload);
                }
            });

            // ✅ 6) Generar PDF NORMAL
            $remision->load('cliente', 'user', 'items');

            $viewName = $this->pickPdfView();

            $dir    = trim((string)env('WA_MAINT_REMISION_PDF_DIR', 'remisiones'));
            $prefix = trim((string)env('WA_MAINT_REMISION_PDF_PREFIX', 'remision_mantenimiento'));

            if ($dir === '') $dir = 'remisiones';
            if ($prefix === '') $prefix = 'remision_mantenimiento';

            $pdfPath = "{$dir}/{$prefix}_{$remision->id}.pdf";

            $qr_path = null;
            $pdf = PDF::loadView($viewName, compact('remision', 'qr_path'));
            try { $pdf->setOptions(['isRemoteEnabled' => true]); } catch (\Throwable $e) {}

            Storage::disk('public')->put($pdfPath, $pdf->output());

            $absPath  = Storage::disk('public')->path($pdfPath);
            $filename = "{$prefix}_{$remision->id}.pdf";

            // ✅ Signed route configurable (para fallback)
            $routeName = trim((string)env('WA_MAINT_REMISION_SIGNED_ROUTE', 'public.remision.mantenimiento'));
            if ($routeName === '') $routeName = 'public.remision.mantenimiento';

            $signedUrl = '';
            try {
                if (\Illuminate\Support\Facades\Route::has($routeName)) {
                    $signedUrl = URL::temporarySignedRoute(
                        $routeName,
                        now()->addHours(24),
                        ['remision' => $remision->id]
                    );
                } elseif (\Illuminate\Support\Facades\Route::has('public.remision.ticket_mantenimiento')) {
                    // fallback por si aún no creas la nueva ruta
                    $signedUrl = URL::temporarySignedRoute(
                        'public.remision.ticket_mantenimiento',
                        now()->addHours(24),
                        ['remision' => $remision->id]
                    );
                }
            } catch (\Throwable $e) {
                $signedUrl = '';
            }

            return [
                'ok' => true,
                'remision_id' => $remision->id,
                'pdf_signed_url' => $signedUrl,
                'pdf_storage_path' => $pdfPath,
                'pdf_abs_path'     => $absPath,
                'pdf_filename'     => $filename,
                'total' => (float)$remision->total,
                'importe_letra' => (string)$remision->importe_letra,
            ];
        } catch (\Throwable $e) {
            Log::error('AI_CREATE_REMISION_FAIL', [
                'msisdn' => $msisdn,
                'err' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);

            return [
                'ok' => false,
                'error' => 'exception',
                'missing' => [],
                'message' => $e->getMessage(),
            ];
        }
    }

    private function isAllowedMaintenanceMsisdn(string $msisdn): bool
    {
        // Ejemplo .env:
        // WA_MAINT_REMISION_ALLOWLIST=7224485191,2205381046,7224407379
        $allow = trim((string)env('WA_MAINT_REMISION_ALLOWLIST', ''));
        if ($allow === '') return false;

        $digits = preg_replace('/\D+/', '', $msisdn);
        $last10 = substr($digits, -10);

        $list = array_filter(array_map(function ($x) {
            return substr(preg_replace('/\D+/', '', trim($x)), -10);
        }, explode(',', $allow)));

        return in_array($last10, $list, true);
    }

    private function pickPdfView(): string
    {
        // ✅ Primero intenta tu PDF NORMAL:
        // resources/views/remisions/remision_mantenimiento.blade.php
        $envView = trim((string)env('WA_MAINT_REMISION_PDF_VIEW', ''));
        if ($envView !== '' && View::exists($envView)) return $envView;

        foreach ([
            'remisions.remision_mantenimiento', // ✅ TU PDF NORMAL
            'remisions.remision_pdf',
            'remisions.remision',
            'remisions.pdf',
            'remisions.ticket',
            'remisions.ticket_mantenimiento',
        ] as $v) {
            if (View::exists($v)) return $v;
        }

        return 'remisions.remision_mantenimiento';
    }

    private function metaPairsToText(array $pairs): string
    {
        $out = [];
        foreach ($pairs as $p) {
            if (!is_array($p)) continue;
            $k = trim((string)($p['key'] ?? ''));
            $v = trim((string)($p['value'] ?? ''));
            if ($k === '' || $v === '') continue;
            $out[] = "{$k}: {$v}";
        }
        return $out ? ("DATOS DEL EQUIPO\n" . implode("\n", $out)) : '';
    }

    /**
     * Limpia items:
     * - evita "No No"
     * - extrae dinero si venía pegado
     * - unidad default "servicio"
     */
    private function sanitizeItems(array $items): array
    {
        $clean = [];

        foreach ($items as $it) {
            if (!is_array($it)) continue;

            $cantidad = (int)($it['cantidad'] ?? 1);
            if ($cantidad < 1) $cantidad = 1;

            $unidad = trim((string)($it['unidad'] ?? ''));
            if ($unidad === '') $unidad = 'servicio';

            $nombre = trim((string)($it['nombre_item'] ?? ''));
            $desc   = trim((string)($it['descripcion_item'] ?? ''));

            $precio = (float)($it['importe_unitario'] ?? 0);

            // Quitar "No No" repetidos
            $nombre = preg_replace('/\b(no)\s+\1\b/i', 'no', $nombre);
            $desc   = preg_replace('/\b(no)\s+\1\b/i', 'no', $desc);

            // Si el nombre trae precio "$3000" y precio es 0, extraer
            if ($precio <= 0) {
                $found = $this->extractMoney($nombre . ' ' . $desc);
                if ($found !== null) $precio = $found;
            }

            // Si nombre quedó vacío, usa genérico
            if ($nombre === '') {
                $nombre = 'Servicio de mantenimiento';
            }

            $clean[] = [
                'cantidad' => $cantidad,
                'unidad' => $unidad,
                'nombre_item' => mb_substr($nombre, 0, 180),
                'descripcion_item' => mb_substr($desc, 0, 1200),
                'importe_unitario' => max(0, $precio),
            ];
        }

        // Quita items inválidos (sin precio)
        $clean = array_values(array_filter($clean, function ($it) {
            return is_array($it) && trim((string)($it['nombre_item'] ?? '')) !== '' && (float)($it['importe_unitario'] ?? 0) > 0;
        }));

        return $clean;
    }

    private function extractMoney(string $s): ?float
    {
        // 2500, 2,500, 2500.00, $2,500.00
        if (!preg_match('/\$?\s*([0-9]{1,3}(?:[, ]?[0-9]{3})*(?:\.[0-9]{1,2})?)/u', $s, $m)) {
            return null;
        }
        $raw = str_replace([' ', ','], '', $m[1]);
        $val = is_numeric($raw) ? (float)$raw : null;
        return ($val !== null && $val >= 0) ? $val : null;
    }
}
