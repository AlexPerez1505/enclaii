<?php

namespace App\Http\Controllers;

use App\Models\Paquete;
use App\Models\Producto;
use App\Services\WooCommerceProductExportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ProductoController extends Controller
{
    public function index()
    {
        $productos = Producto::orderBy('tipo_equipo', 'asc')->get();
        return view('cotizaciones', compact('productos'));
    }

    public function create()
    {
        return view('productos.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tipo_equipo'    => 'required|string|max:255',
            'subtipo_equipo' => 'required|string|max:255',
            'marca'          => 'required|string|max:255',
            'modelo'         => 'required|string|max:255',
            'precio'         => 'required|numeric|min:0',
            'imagen'         => 'nullable|mimes:jpeg,png,jpg,gif,webp,heic|max:4096',
        ]);

        $stock = $request->input('stock', 1);

        $imagenPath = $request->hasFile('imagen')
            ? $request->file('imagen')->store('productos', 'public')
            : null;

        $producto = Producto::create([
            'tipo_equipo'    => $validated['tipo_equipo'],
            'subtipo_equipo' => $validated['subtipo_equipo'],
            'marca'          => $validated['marca'],
            'modelo'         => $validated['modelo'],
            'stock'          => $stock,
            'precio'         => $validated['precio'],
            'imagen'         => $imagenPath,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'message'  => 'Producto creado exitosamente',
                'producto' => $producto,
            ]);
        }

        return redirect()->route('productos.cards')->with('success', 'Producto creado exitosamente');
    }

    public function search(Request $request)
    {
        $search = $request->input('search');

        $productos = Producto::where('tipo_equipo', 'like', "%{$search}%")
            ->orWhere('subtipo_equipo', 'like', "%{$search}%")
            ->orWhere('modelo', 'like', "%{$search}%")
            ->orWhere('marca', 'like', "%{$search}%")
            ->orderBy('tipo_equipo', 'asc')
            ->get()
            ->map(function ($producto) {
                return [
                    'id'             => $producto->id,
                    'tipo_equipo'    => strtoupper((string) $producto->tipo_equipo),
                    'subtipo_equipo' => strtoupper((string) $producto->subtipo_equipo),
                    'modelo'         => strtoupper((string) $producto->modelo),
                    'marca'          => strtoupper((string) $producto->marca),
                    'precio'         => $producto->precio,
                    'imagen'         => $producto->imagen,
                    'stock'          => $producto->stock,
                ];
            });

        return response()->json($productos);
    }

    public function buscar(Request $request)
    {
        $request->validate([
            'termino' => 'required|string|max:255',
        ]);

        $productos = Producto::where('tipo_equipo', 'like', '%' . $request->termino . '%')
            ->orWhere('subtipo_equipo', 'like', '%' . $request->termino . '%')
            ->orWhere('modelo', 'like', '%' . $request->termino . '%')
            ->orWhere('marca', 'like', '%' . $request->termino . '%')
            ->get();

        return response()->json($productos);
    }

    public function cardsVista()
    {
        $productos = Producto::orderBy('tipo_equipo')->get();

        $paquetes = Paquete::withCount('productos')
            ->with(['productos' => fn ($q) => $q->select('productos.*')])
            ->latest()
            ->get();

        return view('productos-cards', compact('productos', 'paquetes'));
    }

    public function edit($id)
    {
        $producto = Producto::findOrFail($id);
        return view('productos.edit', compact('producto'));
    }

    public function update(Request $request, $id)
    {
        $producto = Producto::findOrFail($id);

        $validated = $request->validate([
            'tipo_equipo'    => 'required|string|max:255',
            'subtipo_equipo' => 'required|string|max:255',
            'marca'          => 'required|string|max:255',
            'modelo'         => 'required|string|max:255',
            'precio'         => 'required|numeric|min:0',
            'imagen'         => 'nullable|image|max:4096',
        ]);

        $producto->fill([
            'tipo_equipo'    => $validated['tipo_equipo'],
            'subtipo_equipo' => $validated['subtipo_equipo'],
            'marca'          => $validated['marca'],
            'modelo'         => $validated['modelo'],
            'precio'         => $validated['precio'],
        ]);

        if ($request->hasFile('imagen')) {
            if ($producto->imagen && Storage::disk('public')->exists($producto->imagen)) {
                Storage::disk('public')->delete($producto->imagen);
            }

            $producto->imagen = $request->file('imagen')->store('productos', 'public');
        }

        $producto->save();

        return redirect()->route('productos.cards')->with('success', 'Producto actualizado correctamente');
    }

    public function destroy($id)
    {
        $producto = Producto::findOrFail($id);

        if ($producto->imagen && Storage::disk('public')->exists($producto->imagen)) {
            Storage::disk('public')->delete($producto->imagen);
        }

        $producto->delete();

        return redirect()->route('productos.cards')->with('success', 'Producto eliminado correctamente');
    }

    /**
     * Lee filtros de query/body.
     */
    private function extractProductFilters(Request $request): array
    {
        $q       = trim((string) $request->input('q', $request->query('q', '')));
        $tipo    = trim((string) $request->input('tipo', $request->query('tipo', '')));
        $subtipo = trim((string) $request->input('subtipo', $request->query('subtipo', '')));
        $marca   = trim((string) $request->input('marca', $request->query('marca', '')));
        $scope   = trim((string) $request->input('scope', $request->query('scope', 'all')));

        $stockRaw = trim((string) $request->input('stock', $request->query('stock', 'all')));
        $stock = in_array($stockRaw, ['all', 'with_stock', 'without_stock', 'in', 'out'], true) ? $stockRaw : 'all';

        if ($stock === 'in') {
            $stock = 'with_stock';
        }

        if ($stock === 'out') {
            $stock = 'without_stock';
        }

        // IA apagada por default
        $ai = $request->input('ai', $request->query('ai', 0));
        $ai = (int) (filter_var($ai, FILTER_VALIDATE_BOOLEAN) ? 1 : 0);

        return [
            'q'       => $q,
            'tipo'    => $tipo,
            'subtipo' => $subtipo,
            'marca'   => $marca,
            'stock'   => $stock,
            'scope'   => $scope,
            'ai'      => $ai,
        ];
    }

    private function applySearchToProductos($query, string $q)
    {
        $q = trim($q);

        if ($q === '') {
            return $query;
        }

        return $query->where(function ($w) use ($q) {
            $w->where('tipo_equipo', 'like', "%{$q}%")
                ->orWhere('subtipo_equipo', 'like', "%{$q}%")
                ->orWhere('modelo', 'like', "%{$q}%")
                ->orWhere('marca', 'like', "%{$q}%")
                ->orWhere('precio', 'like', "%{$q}%")
                ->orWhere('descripcion', 'like', "%{$q}%");
        });
    }

    private function applyFilterProductos($query, array $filters)
    {
        $query = $this->applySearchToProductos($query, $filters['q'] ?? '');

        if (!empty($filters['tipo'])) {
            $query->where('tipo_equipo', $filters['tipo']);
        }

        if (!empty($filters['subtipo'])) {
            $query->where('subtipo_equipo', $filters['subtipo']);
        }

        if (!empty($filters['marca'])) {
            $query->where('marca', $filters['marca']);
        }

        $stock = $filters['stock'] ?? 'all';

        if ($stock === 'with_stock') {
            $query->where('stock', '>', 0);
        } elseif ($stock === 'without_stock') {
            $query->where(function ($w) {
                $w->whereNull('stock')->orWhere('stock', '<=', 0);
            });
        }

        return $query;
    }

    private function applyFilterPaquetes($query, array $filters)
    {
        $q = trim((string) ($filters['q'] ?? ''));

        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->where('nombre', 'like', "%{$q}%")
                    ->orWhere('descripcion', 'like', "%{$q}%")
                    ->orWhereHas('productos', function ($p) use ($q) {
                        $this->applySearchToProductos($p, $q);
                    });
            });
        }

        if (
            !empty($filters['tipo']) ||
            !empty($filters['subtipo']) ||
            !empty($filters['marca']) ||
            (($filters['stock'] ?? 'all') !== 'all')
        ) {
            $query->whereHas('productos', function ($p) use ($filters) {
                $this->applyFilterProductos($p, array_merge($filters, ['q' => '']));
            });
        }

        return $query;
    }

    public function exportPdf(Request $request)
    {
        $filters = $this->extractProductFilters($request);
        $scope   = $filters['scope'] ?: 'all';

        $productos = collect();
        $paquetes  = collect();

        if ($scope === 'all' || $scope === 'productos') {
            $qp = Producto::orderBy('tipo_equipo');
            $qp = $this->applyFilterProductos($qp, $filters);
            $productos = $qp->get();
        }

        if ($scope === 'all' || $scope === 'paquetes') {
            $qk = Paquete::withCount('productos')
                ->with(['productos' => fn ($qq) => $qq->select('productos.*')])
                ->latest();

            $qk = $this->applyFilterPaquetes($qk, $filters);
            $paquetes = $qk->get();
        }

        $pdf = Pdf::loadView('exports.catalogo_pdf', [
            'productos'   => $productos,
            'paquetes'    => $paquetes,
            'q'           => $filters['q'],
            'scope'       => $scope,
            'generatedAt' => now(),
        ])->setPaper('a4', 'portrait');

        $name = 'catalogo_' . now()->format('Y-m-d_His') . '.pdf';

        return $pdf->download($name);
    }

    public function exportXlsx(Request $request)
    {
        $filters = $this->extractProductFilters($request);
        $scope   = $filters['scope'] ?: 'all';

        $productos = collect();
        $paquetes  = collect();

        if ($scope === 'all' || $scope === 'productos') {
            $qp = Producto::orderBy('tipo_equipo');
            $qp = $this->applyFilterProductos($qp, $filters);
            $productos = $qp->get();
        }

        if ($scope === 'all' || $scope === 'paquetes') {
            $qk = Paquete::withCount('productos')
                ->with(['productos' => fn ($qq) => $qq->select('productos.*')])
                ->latest();

            $qk = $this->applyFilterPaquetes($qk, $filters);
            $paquetes = $qk->get();
        }

        $spreadsheet = new Spreadsheet();

        $sheet0 = $spreadsheet->getActiveSheet();
        $sheet0->setTitle('Resumen');

        $sheet0->setCellValue('A1', 'CATÁLOGO DE PRODUCTOS - GRUPO MEDIBUY');
        $sheet0->mergeCells('A1:F1');
        $sheet0->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet0->getStyle('A1')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet0->setCellValue('A2', 'Generado:');
        $sheet0->setCellValue('B2', now()->format('Y-m-d H:i:s'));
        $sheet0->setCellValue('A3', 'Filtro (q):');
        $sheet0->setCellValue('B3', $filters['q'] !== '' ? $filters['q'] : '—');
        $sheet0->setCellValue('A4', 'Tipo:');
        $sheet0->setCellValue('B4', $filters['tipo'] !== '' ? $filters['tipo'] : '—');
        $sheet0->setCellValue('A5', 'Subtipo:');
        $sheet0->setCellValue('B5', $filters['subtipo'] !== '' ? $filters['subtipo'] : '—');
        $sheet0->setCellValue('A6', 'Marca:');
        $sheet0->setCellValue('B6', $filters['marca'] !== '' ? $filters['marca'] : '—');
        $sheet0->setCellValue('A7', 'Stock:');
        $sheet0->setCellValue('B7', $filters['stock']);
        $sheet0->setCellValue('A8', 'Scope:');
        $sheet0->setCellValue('B8', $scope);

        $sheet0->setCellValue('A10', 'Totales');
        $sheet0->getStyle('A10')->getFont()->setBold(true);

        $sheet0->setCellValue('A11', 'Productos');
        $sheet0->setCellValue('B11', (int) $productos->count());
        $sheet0->setCellValue('A12', 'Paquetes');
        $sheet0->setCellValue('B12', (int) $paquetes->count());

        foreach (range('A', 'F') as $col) {
            $sheet0->getColumnDimension($col)->setAutoSize(true);
        }

        if ($scope === 'all' || $scope === 'productos') {
            $sheetP = $spreadsheet->createSheet();
            $sheetP->setTitle('Productos');

            $sheetP->setCellValue('A1', 'PRODUCTOS');
            $sheetP->mergeCells('A1:G1');
            $sheetP->getStyle('A1')->getFont()->setBold(true)->setSize(13);
            $sheetP->getStyle('A1')->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER);

            $headers = ['ID', 'TIPO', 'SUBTIPO', 'MARCA', 'MODELO', 'PRECIO', 'STOCK'];
            $sheetP->fromArray($headers, null, 'A2');
            $sheetP->getStyle('A2:G2')->getFont()->setBold(true);

            $rowNum = 3;
            foreach ($productos as $p) {
                $sheetP->setCellValue('A' . $rowNum, (int) ($p->id ?? 0));
                $sheetP->setCellValue('B' . $rowNum, (string) ($p->tipo_equipo ?? ''));
                $sheetP->setCellValue('C' . $rowNum, (string) ($p->subtipo_equipo ?? ''));
                $sheetP->setCellValue('D' . $rowNum, (string) ($p->marca ?? ''));
                $sheetP->setCellValue('E' . $rowNum, (string) ($p->modelo ?? ''));
                $sheetP->setCellValue('F' . $rowNum, (float) ($p->precio ?? 0));
                $sheetP->setCellValue('G' . $rowNum, (int) ($p->stock ?? 0));
                $rowNum++;
            }

            foreach (range('A', 'G') as $col) {
                $sheetP->getColumnDimension($col)->setAutoSize(true);
            }
        }

        if ($scope === 'all' || $scope === 'paquetes') {
            $sheetK = $spreadsheet->createSheet();
            $sheetK->setTitle('Paquetes');

            $sheetK->setCellValue('A1', 'PAQUETES');
            $sheetK->mergeCells('A1:F1');
            $sheetK->getStyle('A1')->getFont()->setBold(true)->setSize(13);
            $sheetK->getStyle('A1')->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER);

            $headersK = ['ID', 'NOMBRE', 'TOTAL', '# PRODUCTOS', 'PRODUCTOS', 'DESCRIPCIÓN'];
            $sheetK->fromArray($headersK, null, 'A2');
            $sheetK->getStyle('A2:F2')->getFont()->setBold(true);

            $rowNum = 3;
            foreach ($paquetes as $pkg) {
                $count = property_exists($pkg, 'productos_count')
                    ? $pkg->productos_count
                    : (isset($pkg->productos) ? $pkg->productos->count() : 0);

                $pkgTotal = isset($pkg->productos)
                    ? $pkg->productos->sum(function ($pp) {
                        $precio = (float) ($pp->precio ?? 0);
                        $cant   = (int) ($pp->pivot->cantidad ?? 1);
                        return $precio * max(1, $cant);
                    })
                    : 0;

                $namesIn = '';
                if (isset($pkg->productos)) {
                    $namesIn = collect($pkg->productos)->map(function ($pp) {
                        return trim(($pp->tipo_equipo ?? '') . ' ' . ($pp->subtipo_equipo ?? '') . ' ' . ($pp->marca ?? '') . ' ' . ($pp->modelo ?? ''));
                    })->join(', ');
                }

                $sheetK->setCellValue('A' . $rowNum, (int) ($pkg->id ?? 0));
                $sheetK->setCellValue('B' . $rowNum, (string) ($pkg->nombre ?? 'Paquete'));
                $sheetK->setCellValue('C' . $rowNum, (float) $pkgTotal);
                $sheetK->setCellValue('D' . $rowNum, (int) $count);
                $sheetK->setCellValue('E' . $rowNum, (string) $namesIn);
                $sheetK->setCellValue('F' . $rowNum, (string) ($pkg->descripcion ?? ''));
                $rowNum++;
            }

            foreach (range('A', 'F') as $col) {
                $sheetK->getColumnDimension($col)->setAutoSize(true);
            }
        }

        $fileName = 'catalogo_' . now()->format('Y-m-d_His') . '.xlsx';
        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /**
     * Export WooCommerce:
     * - pocos productos => 1 XLSX
     * - muchos productos => ZIP con varios XLSX
     */
    public function exportWooCommerceXlsx(Request $request, WooCommerceProductExportService $wooCommerceProductExportService)
    {
        @set_time_limit(180);
        @ini_set('max_execution_time', '180');
        @ini_set('memory_limit', '1024M');

        $filters = $this->extractProductFilters($request);

        $productos = Producto::orderBy('tipo_equipo');
        $productos = $this->applyFilterProductos($productos, $filters);
        $productos = $productos->get();

        if ($productos->isEmpty()) {
            return back()->with('error', 'No hay productos para exportar con esos filtros.');
        }

        $fileName = 'woocommerce_productos_' . now()->format('Y-m-d_His') . '.xlsx';
        $useAi    = (int) ($filters['ai'] ?? 0) === 1;
        $baseUrl  = $request->getSchemeAndHttpHost();

        return $wooCommerceProductExportService->download($productos, $fileName, $baseUrl, $useAi);
    }

    // ==========================
    // API
    // ==========================

    public function apiIndex()
    {
        $productos = Producto::select('id', 'tipo_equipo', 'subtipo_equipo', 'modelo', 'marca', 'stock', 'precio', 'imagen')
            ->orderBy('tipo_equipo', 'asc')
            ->get();

        $base = request()->getSchemeAndHttpHost();

        $productos->transform(function ($producto) use ($base) {
            $producto->imagen = $producto->imagen
                ? $base . '/storage/' . ltrim($producto->imagen, '/')
                : null;

            return $producto;
        });

        return response()->json($productos, 200);
    }

    public function apiStore(Request $request)
    {
        try {
            $validated = $request->validate([
                'tipo_equipo'    => 'required|string|max:255',
                'subtipo_equipo' => 'required|string|max:255',
                'modelo'         => 'required|string|max:255',
                'marca'          => 'required|string|max:255',
                'stock'          => 'required|integer|min:0',
                'precio'         => 'required|numeric|min:0',
                'imagen'         => 'nullable|mimes:jpeg,png,jpg,gif,webp,heic|max:4096',
            ]);

            $imagenPath = $request->hasFile('imagen')
                ? $request->file('imagen')->store('productos', 'public')
                : null;

            $producto = Producto::create([
                'tipo_equipo'    => $validated['tipo_equipo'],
                'subtipo_equipo' => $validated['subtipo_equipo'],
                'modelo'         => $validated['modelo'],
                'marca'          => $validated['marca'],
                'stock'          => $validated['stock'],
                'precio'         => $validated['precio'],
                'imagen'         => $imagenPath,
            ]);

            return response()->json([
                'message'  => 'Producto creado correctamente',
                'producto' => $producto,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al crear producto',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function apiShow($id)
    {
        try {
            $producto = Producto::select(
                'id',
                'tipo_equipo',
                'subtipo_equipo',
                'modelo',
                'marca',
                'stock',
                'precio',
                'imagen'
            )->find($id);

            if (!$producto) {
                return response()->json(['message' => 'Producto no encontrado'], 404);
            }

            $base = request()->getSchemeAndHttpHost();

            $producto->imagen = (!empty($producto->imagen) && is_string($producto->imagen))
                ? $base . '/storage/' . ltrim($producto->imagen, '/')
                : null;

            return response()->json($producto, 200);
        } catch (\Throwable $e) {
            \Log::error('Error en apiShow producto: ' . $e->getMessage(), ['id' => $id]);

            return response()->json([
                'message' => 'Error interno al obtener producto',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function apiUpdate(Request $request, $id)
    {
        try {
            $producto = Producto::findOrFail($id);

            $validated = $request->validate([
                'tipo_equipo'    => 'required|string|max:255',
                'subtipo_equipo' => 'required|string|max:255',
                'modelo'         => 'required|string|max:255',
                'marca'          => 'required|string|max:255',
                'stock'          => 'required|integer|min:0',
                'precio'         => 'required|numeric|min:0',
                'imagen'         => 'nullable|mimes:jpeg,png,jpg,gif,webp,heic|max:4096',
            ]);

            $producto->tipo_equipo    = $validated['tipo_equipo'];
            $producto->subtipo_equipo = $validated['subtipo_equipo'];
            $producto->modelo         = $validated['modelo'];
            $producto->marca          = $validated['marca'];
            $producto->stock          = $validated['stock'];
            $producto->precio         = $validated['precio'];

            if ($request->hasFile('imagen')) {
                if ($producto->imagen && \Storage::disk('public')->exists($producto->imagen)) {
                    \Storage::disk('public')->delete($producto->imagen);
                }

                $producto->imagen = $request->file('imagen')->store('productos', 'public');
            }

            $producto->save();

            $base = $request->getSchemeAndHttpHost();
            $producto->imagen = $producto->imagen ? $base . '/storage/' . ltrim($producto->imagen, '/') : null;

            return response()->json([
                'message'  => 'Producto actualizado correctamente',
                'producto' => $producto,
            ], 200);
        } catch (\Throwable $e) {
            \Log::error('Error en apiUpdate producto: ' . $e->getMessage(), ['id' => $id]);

            return response()->json([
                'message' => 'Error al actualizar producto',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}