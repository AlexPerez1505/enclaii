<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use ZipArchive;

class WooCommerceProductExportService
{
    /**
     * Encabezados EXACTOS de tu plantilla (hoja Catalogo).
     */
    private array $columns = [
        'ID',
        'Tipo',
        'SKU',
        'Nombre',
        'Publicado',
        '¿Está destacado?',
        'Visibilidad en el catálogo',
        'Descripción corta',
        'Descripción',
        'Día en que empieza el precio rebajado',
        'Día en que termina el precio rebajado',
        'Estado del impuesto',
        'Clase de impuesto',
        '¿En inventario?',
        'Inventario',
        'Cantidad de bajo inventario',
        '¿Permitir reservas de productos agotados?',
        '¿Vendido individualmente?',
        'Peso (kg)',
        'Longitud (cm)',
        'Anchura (cm)',
        'Altura (cm)',
        '¿Permitir valoraciones de clientes?',
        'Nota de compra',
        'Precio rebajado',
        'Precio normal',
        'Categorías',
        'Etiquetas',
        'Clase de envío',
        'Imágenes',
        'Límite de descargas',
        'Días de caducidad de la descarga',
        'Superior',
        'Productos agrupados',
        'Ventas dirigidas',
        'Ventas cruzadas',
        'URL externa',
        'Texto del botón',
        'Posición',
        'Nombre del atributo 1',
        'Valor(es) del atributo 1',
        'Atributo visible 1',
        'Atributo global 1',
        'Nombre de la descarga 1',
        'URL de la descarga 1',
    ];

    /**
     * Ajustes para no saturar.
     */
    private int $maxProductsPerFile = 200; // si pasa esto, genera varios xlsx en zip
    private int $aiMaxItems = 20;          // máximo productos a enriquecer con IA por archivo
    private int $aiBatchSize = 5;          // tamaño de lote IA
    private int $aiHardTimeout = 8;        // timeout máximo por llamada IA

    /**
     * Descarga 1 XLSX o un ZIP con varios XLSX si se supera el límite.
     */
    public function download(Collection $productos, string $fileName, string $baseUrl, bool $useAi = false)
    {
        @set_time_limit(180);
        @ini_set('max_execution_time', '180');
        @ini_set('memory_limit', '1024M');

        $productos = $productos->values();

        if ($productos->isEmpty()) {
            abort(404, 'No hay productos para exportar.');
        }

        if ($productos->count() <= $this->maxProductsPerFile) {
            $tmpXlsx = $this->generateSingleXlsxTempFile($productos, $baseUrl, $useAi);

            return response()->download(
                $tmpXlsx,
                $fileName,
                ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
            )->deleteFileAfterSend(true);
        }

        $chunks = $productos->chunk($this->maxProductsPerFile)->values();
        $zipPath = $this->generateZipWithChunks($chunks, $baseUrl, $useAi);

        $zipName = Str::replaceLast('.xlsx', '', $fileName) . '.zip';

        return response()->download(
            $zipPath,
            $zipName,
            ['Content-Type' => 'application/zip']
        )->deleteFileAfterSend(true);
    }

    /**
     * Genera un ZIP con múltiples archivos XLSX.
     */
    private function generateZipWithChunks(Collection $chunks, string $baseUrl, bool $useAi): string
    {
        $zipPath = storage_path('app/temp/' . 'woocommerce_export_' . now()->format('Ymd_His') . '_' . Str::random(8) . '.zip');

        if (!is_dir(dirname($zipPath))) {
            @mkdir(dirname($zipPath), 0775, true);
        }

        $zip = new ZipArchive();

        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException('No se pudo crear el archivo ZIP.');
        }

        $tempFiles = [];

        try {
            foreach ($chunks as $index => $chunk) {
                $part = $index + 1;
                $tmpXlsx = $this->generateSingleXlsxTempFile($chunk->values(), $baseUrl, $useAi);
                $tempFiles[] = $tmpXlsx;

                $nameInsideZip = 'woocommerce_productos_parte_' . $part . '.xlsx';
                $zip->addFile($tmpXlsx, $nameInsideZip);
            }

            $zip->close();

            foreach ($tempFiles as $file) {
                if (is_file($file)) {
                    @unlink($file);
                }
            }

            return $zipPath;
        } catch (\Throwable $e) {
            $zip->close();

            foreach ($tempFiles as $file) {
                if (is_file($file)) {
                    @unlink($file);
                }
            }

            if (is_file($zipPath)) {
                @unlink($zipPath);
            }

            throw $e;
        }
    }

    /**
     * Genera un XLSX temporal para un chunk.
     */
    private function generateSingleXlsxTempFile(Collection $productos, string $baseUrl, bool $useAi): string
    {
        $spreadsheet = $this->buildSpreadsheet($productos, $baseUrl, $useAi);

        $tmpPath = storage_path('app/temp/' . 'woo_' . Str::random(20) . '.xlsx');

        if (!is_dir(dirname($tmpPath))) {
            @mkdir(dirname($tmpPath), 0775, true);
        }

        $writer = new Xlsx($spreadsheet);
        $writer->save($tmpPath);

        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);

        return $tmpPath;
    }

    /**
     * Construye el spreadsheet para un conjunto de productos.
     */
    private function buildSpreadsheet(Collection $productos, string $baseUrl, bool $useAi): Spreadsheet
    {
        $spreadsheet = $this->loadTemplate();
        $sheet = $this->getCatalogSheet($spreadsheet);

        $this->prepareSheet($sheet);

        $enrichMap = $this->buildEnrichmentMap($productos, $useAi);

        $usedSkus = [];
        $row = 2;

        foreach ($productos->values() as $index => $producto) {
            $sig = $this->signature($producto);
            $suggested = $enrichMap[$sig] ?? $this->fallbackEnrichment($producto);

            $payload = $this->buildWooRow($producto, $index + 1, $baseUrl, $suggested, $usedSkus);
            $sheet->fromArray($payload, null, 'A' . $row);
            $row++;
        }

        return $spreadsheet;
    }

    private function loadTemplate(): Spreadsheet
    {
        $paths = [
            storage_path('app/templates/Plantilla-Productos-Woocommerce.xlsx'),
            storage_path('app/Plantilla-Productos-Woocommerce.xlsx'),
            public_path('templates/Plantilla-Productos-Woocommerce.xlsx'),
            base_path('Plantilla-Productos-Woocommerce.xlsx'),
        ];

        foreach ($paths as $path) {
            if (is_file($path)) {
                return IOFactory::load($path);
            }
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Catalogo');
        $sheet->fromArray($this->columns, null, 'A1');
        $spreadsheet->createSheet()->setTitle('Valores');

        return $spreadsheet;
    }

    private function getCatalogSheet(Spreadsheet $spreadsheet): Worksheet
    {
        $sheet = $spreadsheet->getSheetByName('Catalogo');
        if ($sheet) {
            return $sheet;
        }

        $sheet = $spreadsheet->getSheetByName('Catálogo');
        if ($sheet) {
            return $sheet;
        }

        return $spreadsheet->getActiveSheet();
    }

    /**
     * Limpieza más rápida.
     */
    private function prepareSheet(Worksheet $sheet): void
    {
        $highestColumnIndex = count($this->columns);

        $headerMap = [];
        for ($col = 1; $col <= $highestColumnIndex; $col++) {
            $coord = Coordinate::stringFromColumnIndex($col) . '1';
            $header = trim((string)($sheet->getCell($coord)->getValue() ?? ''));
            if ($header !== '') {
                $headerMap[$header] = $col;
            }
        }

        if (count(array_intersect($this->columns, array_keys($headerMap))) < 10) {
            $sheet->fromArray($this->columns, null, 'A1');
        }

        $highestRow = (int) $sheet->getHighestRow();

        if ($highestRow >= 2) {
            $sheet->removeRow(2, $highestRow - 1);
        }
    }

    private function buildWooRow($producto, int $position, string $baseUrl, array $suggested, array &$usedSkus): array
    {
        $stock  = (int) ($producto->stock ?? 0);
        $precio = (float) ($producto->precio ?? 0);

        $skuBase = $suggested['sku'] ?: $this->defaultSku($producto);
        $sku = $this->makeUniqueSku($skuBase, $producto, $usedSkus);

        $imagen = $this->buildImageUrl($producto->imagen ?? null, $baseUrl);

        return [
            '',
            'simple',
            $sku,
            $suggested['name'],
            1,
            !empty($suggested['featured']) ? 1 : 0,
            'visible',
            $suggested['short_description'],
            $suggested['description'],
            '',
            '',
            'taxable',
            '',
            $stock > 0 ? 1 : 0,
            $stock,
            $stock > 0 ? 1 : '',
            0,
            !empty($suggested['sold_individually']) ? 1 : 0,
            $suggested['weight_kg'],
            $suggested['length_cm'],
            $suggested['width_cm'],
            $suggested['height_cm'],
            1,
            $suggested['purchase_note'],
            '',
            $precio,
            $suggested['categories'],
            $suggested['tags'],
            '',
            $imagen,
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            $position,
            'Marca',
            (string) ($producto->marca ?? ''),
            1,
            0,
            '',
            '',
        ];
    }

    /**
     * IA por lotes + cache.
     */
    private function buildEnrichmentMap(Collection $productos, bool $useAi): array
    {
        $map = [];

        if (!$useAi) {
            return $map;
        }

        $key = trim((string) config('services.openai.key', ''));
        if ($key === '') {
            return $map;
        }

        $models = $this->modelCandidates();
        if (empty($models)) {
            return $map;
        }

        $unique = [];
        foreach ($productos as $p) {
            $sig = $this->signature($p);
            if (!isset($unique[$sig])) {
                $unique[$sig] = $p;
            }
        }

        $toAsk = [];
        foreach ($unique as $sig => $p) {
            $cacheKey = $this->cacheKeyForSig($sig);
            $cached = Cache::get($cacheKey);

            if (is_array($cached) && !empty($cached['name'])) {
                $map[$sig] = $cached;
            } else {
                $toAsk[$sig] = $p;
            }
        }

        $toAsk = array_slice($toAsk, 0, $this->aiMaxItems, true);

        if (empty($toAsk)) {
            return $map;
        }

        $chunks = array_chunk($toAsk, $this->aiBatchSize, true);

        foreach ($chunks as $chunkAssoc) {
            $chunk = [];

            foreach ($chunkAssoc as $sig => $p) {
                $chunk[] = [
                    'sig' => $sig,
                    'tipo_equipo' => (string) ($p->tipo_equipo ?? ''),
                    'subtipo_equipo' => (string) ($p->subtipo_equipo ?? ''),
                    'marca' => (string) ($p->marca ?? ''),
                    'modelo' => (string) ($p->modelo ?? ''),
                    'descripcion_actual' => (string) ($p->descripcion ?? ''),
                    'precio' => (float) ($p->precio ?? 0),
                    'stock' => (int) ($p->stock ?? 0),
                ];
            }

            $aiResult = $this->askAiBatch($chunk, $models, $key);

            if (!is_array($aiResult) || empty($aiResult['items']) || !is_array($aiResult['items'])) {
                continue;
            }

            foreach ($aiResult['items'] as $item) {
                if (!is_array($item)) {
                    continue;
                }

                $sig = (string)($item['sig'] ?? '');
                if ($sig === '' || !isset($unique[$sig])) {
                    continue;
                }

                $fallback = $this->fallbackEnrichment($unique[$sig]);

                $merged = array_merge($fallback, [
                    'sku' => $this->truncate((string)($item['sku'] ?? $fallback['sku']), 80),
                    'name' => $this->truncate((string)($item['name'] ?? $fallback['name']), 120),
                    'short_description' => $this->truncate((string)($item['short_description'] ?? $fallback['short_description']), 160),
                    'description' => (string)($item['description'] ?? $fallback['description']),
                    'categories' => $this->truncate((string)($item['categories'] ?? $fallback['categories']), 255),
                    'tags' => $this->truncate((string)($item['tags'] ?? $fallback['tags']), 255),
                    'purchase_note' => $this->truncate((string)($item['purchase_note'] ?? ''), 255),
                    'featured' => (bool)($item['featured'] ?? false),
                    'sold_individually' => (bool)($item['sold_individually'] ?? false),
                    'weight_kg' => $this->normalizeNumericString($item['weight_kg'] ?? null),
                    'length_cm' => $this->normalizeNumericString($item['length_cm'] ?? null),
                    'width_cm' => $this->normalizeNumericString($item['width_cm'] ?? null),
                    'height_cm' => $this->normalizeNumericString($item['height_cm'] ?? null),
                ]);

                if (trim((string)$merged['name']) === '') {
                    continue;
                }

                $map[$sig] = $merged;
                Cache::put($this->cacheKeyForSig($sig), $merged, now()->addDays(60));
            }
        }

        return $map;
    }

    private function askAiBatch(array $chunk, array $models, string $key): ?array
    {
        $timeoutCfg = (int) config('services.openai.timeout', 20);
        $timeout = max(5, min($timeoutCfg, $this->aiHardTimeout));

        foreach ($models as $model) {
            try {
                $response = Http::connectTimeout(4)
                    ->timeout($timeout)
                    ->retry(0, 0)
                    ->withToken($key)
                    ->post('https://api.openai.com/v1/chat/completions', [
                        'model' => $model,
                        'temperature' => 0.3,
                        'response_format' => ['type' => 'json_object'],
                        'messages' => [
                            [
                                'role' => 'system',
                                'content' =>
                                    "Eres un especialista en catálogos WooCommerce.\n" .
                                    "Devuelve SOLO JSON válido.\n" .
                                    "No inventes peso/dimensiones: si no se infieren, null.\n" .
                                    "Categorías con > y etiquetas con comas.\n" .
                                    "Sin emojis.\n" .
                                    "Respeta sig para mapear cada producto."
                            ],
                            [
                                'role' => 'user',
                                'content' => json_encode([
                                    'items' => $chunk,
                                    'output' => [
                                        'items' => [
                                            [
                                                'sig' => 'string',
                                                'sku' => 'string',
                                                'name' => 'string',
                                                'short_description' => 'string max 160',
                                                'description' => 'string',
                                                'categories' => 'string con >',
                                                'tags' => 'string comas',
                                                'purchase_note' => 'string',
                                                'featured' => 'boolean',
                                                'sold_individually' => 'boolean',
                                                'weight_kg' => 'string|null',
                                                'length_cm' => 'string|null',
                                                'width_cm' => 'string|null',
                                                'height_cm' => 'string|null',
                                            ],
                                        ],
                                    ],
                                ], JSON_UNESCAPED_UNICODE),
                            ],
                        ],
                    ]);

                if (!$response->successful()) {
                    continue;
                }

                $content = data_get($response->json(), 'choices.0.message.content');

                if (!is_string($content) || trim($content) === '') {
                    continue;
                }

                $decoded = json_decode($content, true);

                if (!is_array($decoded)) {
                    continue;
                }

                return $decoded;
            } catch (\Throwable $e) {
                Log::warning('IA batch WooCommerce falló', [
                    'model' => $model,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return null;
    }

    private function modelCandidates(): array
    {
        $primary = trim((string) config('services.openai.primary', ''));
        $fallbacks = config('services.openai.fallback_models', []);
        if (!is_array($fallbacks)) {
            $fallbacks = [];
        }

        $models = array_merge($primary !== '' ? [$primary] : [], $fallbacks);
        $models = array_values(array_filter(array_map('trim', $models)));

        return array_values(array_unique($models));
    }

    private function signature($producto): string
    {
        $tipo = strtolower(trim((string)($producto->tipo_equipo ?? '')));
        $sub  = strtolower(trim((string)($producto->subtipo_equipo ?? '')));
        $mar  = strtolower(trim((string)($producto->marca ?? '')));
        $mod  = strtolower(trim((string)($producto->modelo ?? '')));

        return md5($tipo . '|' . $sub . '|' . $mar . '|' . $mod);
    }

    private function cacheKeyForSig(string $sig): string
    {
        return 'woo_ai_sig_' . $sig;
    }

    private function fallbackEnrichment($producto): array
    {
        $tipo = trim((string) ($producto->tipo_equipo ?? 'Producto'));
        $subtipo = trim((string) ($producto->subtipo_equipo ?? ''));
        $marca = trim((string) ($producto->marca ?? ''));
        $modelo = trim((string) ($producto->modelo ?? ''));
        $descripcionActual = trim((string) ($producto->descripcion ?? ''));

        $parts = array_filter([$tipo, $subtipo, $marca, $modelo]);
        $name = $this->truncate(implode(' ', $parts), 120);

        $short = $descripcionActual !== ''
            ? $this->truncate($descripcionActual, 160)
            : $this->truncate(trim("{$tipo} {$subtipo} {$marca} {$modelo}."), 160);

        $description = $descripcionActual !== ''
            ? $descripcionActual
            : trim(implode("\n", array_filter([
                $name,
                $subtipo !== '' ? 'Subtipo: ' . $subtipo : null,
                $marca !== '' ? 'Marca: ' . $marca : null,
                $modelo !== '' ? 'Modelo: ' . $modelo : null,
                'Producto listo para catálogo y publicación.',
            ])));

        $categories = $tipo;
        if ($subtipo !== '') {
            $categories .= ' > ' . $subtipo;
        }

        $tags = collect([$tipo, $subtipo, $marca, $modelo])
            ->filter()
            ->map(fn ($item) => trim((string) $item))
            ->unique()
            ->implode(', ');

        return [
            'sku' => $this->defaultSku($producto),
            'name' => $name !== '' ? $name : 'Producto',
            'short_description' => $short,
            'description' => $description,
            'categories' => $this->truncate($categories, 255),
            'tags' => $this->truncate($tags, 255),
            'purchase_note' => '',
            'featured' => false,
            'sold_individually' => false,
            'weight_kg' => $this->normalizeNumericString($producto->peso ?? null),
            'length_cm' => $this->normalizeNumericString($producto->longitud ?? null),
            'width_cm' => $this->normalizeNumericString($producto->anchura ?? null),
            'height_cm' => $this->normalizeNumericString($producto->altura ?? null),
        ];
    }

    private function defaultSku($producto): string
    {
        $base = collect([
            Str::upper(Str::slug((string) ($producto->tipo_equipo ?? ''), '')),
            Str::upper(Str::slug((string) ($producto->marca ?? ''), '')),
            Str::upper(Str::slug((string) ($producto->modelo ?? ''), '')),
            (string) ($producto->id ?? ''),
        ])->filter()->implode('-');

        return $this->truncate($base !== '' ? $base : 'PRODUCTO-' . uniqid(), 80);
    }

    private function makeUniqueSku(string $sku, $producto, array &$usedSkus): string
    {
        $sku = trim($sku) !== '' ? trim($sku) : $this->defaultSku($producto);
        $base = $this->truncate($sku, 80);

        if (!isset($usedSkus[$base])) {
            $usedSkus[$base] = 1;
            return $base;
        }

        $id = (string)($producto->id ?? '');
        $suffix = $id !== '' ? '-' . $id : '-' . (++$usedSkus[$base]);

        $final = $this->truncate($base . $suffix, 80);

        if (!isset($usedSkus[$final])) {
            $usedSkus[$final] = 1;
            return $final;
        }

        $i = 2;
        do {
            $final2 = $this->truncate($base . $suffix . '-' . $i, 80);
            $i++;
        } while (isset($usedSkus[$final2]));

        $usedSkus[$final2] = 1;

        return $final2;
    }

    private function buildImageUrl(?string $path, string $baseUrl): string
    {
        if (!$path) {
            return '';
        }

        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        $relativeUrl = Storage::disk('public')->url($path);

        return rtrim($baseUrl, '/') . '/' . ltrim($relativeUrl, '/');
    }

    private function normalizeNumericString($value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        $value = str_replace(',', '.', trim((string) $value));

        return is_numeric($value) ? (string) $value : null;
    }

    private function truncate(string $value, int $limit): string
    {
        return trim(Str::limit(trim($value), $limit, ''));
    }
}