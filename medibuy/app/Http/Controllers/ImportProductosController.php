<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ImportProductosController extends Controller
{
    /**
     * Verifica que el usuario sea administrador
     */
    private function checkAdmin()
    {
        if (!auth()->check()) {
            abort(403, 'Debes iniciar sesión para acceder a esta página.');
        }
        
        if (auth()->user()->role !== 'admin') {
            abort(403, 'No tienes permisos de administrador para realizar esta acción.');
        }
    }

    /**
     * Muestra el formulario de importación
     */
    public function showImportForm()
    {
        $this->checkAdmin();
        return view('import-productos');
    }

    /**
     * Importa productos desde CSV o Excel
     */
    public function import(Request $request)
    {
        $this->checkAdmin();

        $request->validate([
            'archivo' => 'required|file|mimes:csv,txt,xls,xlsx|max:10240',
            'modo' => 'required|in:replace,update',
        ]);

        try {
            $archivo = $request->file('archivo');
            $modo = $request->input('modo', 'replace');
            $extension = $archivo->getClientOriginalExtension();

            $stats = [
                'creados' => 0,
                'actualizados' => 0,
                'eliminados' => 0,
                'mantenidos' => 0,
                'errores' => 0,
                'total_procesados' => 0,
                'detalles_errores' => [],
            ];

            // ============================================
            // MODO REPLACE - USANDO ID DEL EXCEL
            // ============================================
            
            if ($modo === 'replace') {
                DB::beginTransaction();
                try {
                    // 1. Obtener IDs de productos que tienen imagen
                    $productosConImagen = Producto::whereNotNull('imagen')
                        ->where('imagen', '!=', '')
                        ->pluck('id')
                        ->toArray();
                    
                    $stats['mantenidos'] = count($productosConImagen);
                    
                    if ($stats['mantenidos'] > 0) {
                        Log::info("Se mantendrán {$stats['mantenidos']} productos con imagen");
                    }
                    
                    // 2. ELIMINAR productos SIN imagen (excepto los que tienen imagen)
                    $eliminados = Producto::whereNotIn('id', $productosConImagen)->delete();
                    
                    $stats['eliminados'] = $eliminados;
                    Log::info("Se eliminaron {$eliminados} productos sin imagen");
                    
                    // 3. Guardar IDs con imagen para preservarlos
                    session(['ids_con_imagen' => $productosConImagen]);
                    
                    DB::commit();
                    Log::info('Limpieza completada, productos con imagen conservados');
                    
                } catch (\Exception $e) {
                    DB::rollBack();
                    throw new \Exception('Error al limpiar los datos: ' . $e->getMessage());
                }
            }

            // ============================================
            // PROCESAR ARCHIVO
            // ============================================
            
            if (in_array($extension, ['xls', 'xlsx'])) {
                $resultados = $this->procesarExcel($archivo, $modo);
            } else {
                $resultados = $this->procesarCSV($archivo, $modo);
            }
            
            $stats = array_merge($stats, $resultados);

            // Generar mensaje de éxito
            $mensaje = "✅ Importación completada exitosamente.\n\n";
            $mensaje .= "📊 Resumen:\n";
            $mensaje .= "• Productos creados: {$stats['creados']}\n";
            $mensaje .= "• Productos actualizados: {$stats['actualizados']}\n";
            $mensaje .= "• Productos mantenidos (con imagen): {$stats['mantenidos']}\n";
            $mensaje .= "• Productos eliminados (sin imagen): {$stats['eliminados']}\n";
            $mensaje .= "• Errores: {$stats['errores']}\n";
            $mensaje .= "• Total procesados: {$stats['total_procesados']}\n";

            if ($stats['errores'] > 0 && !empty($stats['detalles_errores'])) {
                $mensaje .= "\n⚠️ Detalles de errores (primeros 10):\n";
                $limit = 0;
                foreach ($stats['detalles_errores'] as $error) {
                    if ($limit >= 10) break;
                    $mensaje .= "• Fila {$error['fila']}: {$error['mensaje']}\n";
                    $limit++;
                }
                if (count($stats['detalles_errores']) > 10) {
                    $mensaje .= "• ... y " . (count($stats['detalles_errores']) - 10) . " errores más\n";
                }
            }

            // Limpiar sesión
            session()->forget(['ids_con_imagen']);

            Log::info('Importación completada', $stats);

            return back()->with('success', $mensaje);

        } catch (\Exception $e) {
            Log::error('Error en importación: ' . $e->getMessage());
            return back()->with('error', '❌ Error al importar: ' . $e->getMessage());
        }
    }

    /**
     * Procesa archivo Excel (XLSX)
     */
    private function procesarExcel($archivo, $modo)
    {
        $stats = [
            'creados' => 0,
            'actualizados' => 0,
            'errores' => 0,
            'total_procesados' => 0,
            'detalles_errores' => [],
        ];

        $path = $archivo->getRealPath();
        
        if ($archivo->getClientOriginalExtension() === 'xlsx') {
            try {
                $rows = $this->leerXLSX($path);
            } catch (\Exception $e) {
                throw new \Exception('Error al leer el archivo XLSX: ' . $e->getMessage());
            }
        } else {
            throw new \Exception('El formato XLS no es soportado. Convierte a XLSX o CSV.');
        }

        if (empty($rows)) {
            throw new \Exception('No se encontraron datos en el archivo');
        }

        return $this->procesarDatosDesdeArray($rows, $modo);
    }

    /**
     * Lee un archivo XLSX usando PHP puro
     */
    private function leerXLSX($path)
    {
        $rows = [];
        
        $zip = new \ZipArchive();
        if ($zip->open($path) !== true) {
            throw new \Exception('No se pudo abrir el archivo XLSX');
        }

        $sharedStrings = [];
        $xml = $zip->getFromName('xl/sharedStrings.xml');
        if ($xml) {
            $dom = new \DOMDocument();
            $dom->loadXML($xml);
            $elements = $dom->getElementsByTagName('t');
            foreach ($elements as $element) {
                $sharedStrings[] = $element->textContent;
            }
        }

        $xml = $zip->getFromName('xl/worksheets/sheet1.xml');
        $zip->close();

        if (!$xml) {
            throw new \Exception('No se pudo leer el contenido del XLSX');
        }

        $dom = new \DOMDocument();
        $dom->loadXML($xml);
        
        $rowElements = $dom->getElementsByTagName('row');
        
        foreach ($rowElements as $rowElement) {
            $rowData = [];
            $cells = $rowElement->getElementsByTagName('c');
            
            foreach ($cells as $cell) {
                $value = '';
                $type = $cell->getAttribute('t');
                $valueElement = $cell->getElementsByTagName('v')->item(0);
                
                if ($valueElement) {
                    $value = $valueElement->textContent;
                    if ($type === 's' && isset($sharedStrings[(int)$value])) {
                        $value = $sharedStrings[(int)$value];
                    }
                }
                $rowData[] = $value;
            }
            
            if (!empty($rowData)) {
                $rows[] = $rowData;
            }
        }

        return $rows;
    }

    /**
     * Procesa datos desde un array (para Excel)
     * AHORA USA EL ID DEL EXCEL
     */
    private function procesarDatosDesdeArray($rows, $modo)
    {
        $stats = [
            'creados' => 0,
            'actualizados' => 0,
            'errores' => 0,
            'total_procesados' => 0,
            'detalles_errores' => [],
        ];

        // Obtener IDs con imagen de la sesión
        $idsConImagen = session('ids_con_imagen', []);

        // Tomar encabezados de la primera fila
        $headers = array_map('trim', $rows[0]);
        
        // Mapeo de columnas - AHORA INCLUYE 'id'
        $map = [
            'id' => 'id',
            'categoria' => 'tipo_equipo',
            'tipo_equipo' => 'tipo_equipo',
            'subcategoria' => 'subtipo_equipo',
            'subtipo_equipo' => 'subtipo_equipo',
            'modelo' => 'modelo',
            'marca' => 'marca',
            'stock' => 'stock',
            'precio' => 'precio',
            'nombre' => 'tipo_equipo',
        ];
        
        foreach ($headers as $i => $h) {
            $h = strtolower(trim($h));
            $headers[$i] = $map[$h] ?? $h;
        }

        // Encontrar índices
        $idxId = array_search('id', $headers);
        $idxTipoEquipo = array_search('tipo_equipo', $headers);
        $idxSubtipo = array_search('subtipo_equipo', $headers);
        $idxModelo = array_search('modelo', $headers);
        $idxMarca = array_search('marca', $headers);
        $idxStock = array_search('stock', $headers);
        $idxPrecio = array_search('precio', $headers);

        if ($idxId === false) {
            throw new \Exception("⚠️ No se encontró la columna 'id' en el archivo. La columna 'id' es OBLIGATORIA para identificar productos únicos.");
        }

        if ($idxTipoEquipo === false) {
            throw new \Exception("⚠️ No se encontró la columna 'categoria' o 'tipo_equipo'");
        }

        if ($idxPrecio === false) {
            throw new \Exception("⚠️ No se encontró la columna 'precio'");
        }

        // Procesar filas
        for ($i = 1; $i < count($rows); $i++) {
            $row = $rows[$i];
            $stats['total_procesados']++;
            $filaNumero = $i + 1;

            try {
                if (count(array_filter($row)) < 2) {
                    continue;
                }

                while (count($row) < count($headers)) {
                    $row[] = '';
                }

                $data = array_combine($headers, $row);
                $data = array_map('trim', $data);

                // ============================================
                // OBTENER DATOS - INCLUYENDO ID
                // ============================================
                
                $idExcel = isset($data['id']) ? (int) $data['id'] : null;
                
                if (!$idExcel || $idExcel <= 0) {
                    throw new \Exception("El ID '{$data['id']}' no es válido");
                }

                $tipoEquipo = $data['tipo_equipo'] ?? $data['modelo'] ?? $data['marca'] ?? 'Producto';
                $subtipoEquipo = $data['subtipo_equipo'] ?? null;
                $modelo = $data['modelo'] ?? $tipoEquipo;
                $marca = $data['marca'] ?? 'Sin marca';
                $stock = isset($data['stock']) ? (int) $data['stock'] : 1;
                $precio = isset($data['precio']) ? (float) str_replace(',', '.', str_replace('.', '', $data['precio'])) : 0;

                // ============================================
                // BUSCAR PRODUCTO POR ID
                // ============================================
                
                $producto = Producto::find($idExcel);

                // ============================================
                // PREPARAR DATOS PARA GUARDAR
                // ============================================
                
                $datos = [
                    'tipo_equipo' => $tipoEquipo,
                    'subtipo_equipo' => $subtipoEquipo,
                    'modelo' => $modelo,
                    'marca' => $marca,
                    'stock' => $stock,
                    'precio' => $precio,
                ];

                // ============================================
                // GUARDAR O ACTUALIZAR (USANDO ID)
                // ============================================
                
                if ($producto) {
                    // El producto existe, actualizar pero CONSERVAR la imagen
                    // Si el producto está en la lista de IDs con imagen, conservar su imagen
                    if (in_array($idExcel, $idsConImagen)) {
                        // No incluir 'imagen' en los datos para conservarla
                        $producto->update($datos);
                        $stats['actualizados']++;
                        Log::info("Producto ID {$idExcel} actualizado (imagen conservada)");
                    } else {
                        // Si no tiene imagen, actualizar normalmente
                        $producto->update($datos);
                        $stats['actualizados']++;
                    }
                } else {
                    // El producto NO existe, CREAR con el ID del Excel
                    $datos['id'] = $idExcel; // Forzar el ID del Excel
                    Producto::create($datos);
                    $stats['creados']++;
                    Log::info("Producto ID {$idExcel} creado");
                }

            } catch (\Exception $e) {
                $stats['errores']++;
                $stats['detalles_errores'][] = [
                    'fila' => $filaNumero,
                    'mensaje' => $e->getMessage(),
                ];
                Log::warning("Error en fila {$filaNumero}: " . $e->getMessage());
            }
        }

        return $stats;
    }

    /**
     * Procesa archivo CSV (similar al Excel)
     */
    private function procesarCSV($archivo, $modo)
    {
        $stats = [
            'creados' => 0,
            'actualizados' => 0,
            'errores' => 0,
            'total_procesados' => 0,
            'detalles_errores' => [],
        ];

        $handle = fopen($archivo->getRealPath(), 'r');
        if (!$handle) {
            throw new \Exception('No se pudo abrir el archivo');
        }

        $headers = fgetcsv($handle, 0, ';', '"');
        if ($headers) {
            $headers = array_map('trim', $headers);
            
            $map = [
                'id' => 'id',
                'categoria' => 'tipo_equipo',
                'tipo_equipo' => 'tipo_equipo',
                'subcategoria' => 'subtipo_equipo',
                'subtipo_equipo' => 'subtipo_equipo',
                'modelo' => 'modelo',
                'marca' => 'marca',
                'stock' => 'stock',
                'precio' => 'precio',
            ];
            
            foreach ($headers as $i => $h) {
                $h = strtolower(trim($h));
                $headers[$i] = $map[$h] ?? $h;
            }
        }

        // Verificar que existe la columna id
        if (!in_array('id', $headers)) {
            throw new \Exception("⚠️ No se encontró la columna 'id' en el archivo CSV. La columna 'id' es OBLIGATORIA.");
        }

        $filaNumero = 1;

        while (($row = fgetcsv($handle, 0, ';', '"')) !== false) {
            $filaNumero++;
            $stats['total_procesados']++;

            try {
                if (count(array_filter($row)) < 2) {
                    continue;
                }

                while (count($row) < count($headers)) {
                    $row[] = '';
                }

                $data = array_combine($headers, $row);
                $data = array_map('trim', $data);

                // Obtener datos incluyendo ID
                $idExcel = isset($data['id']) ? (int) $data['id'] : null;
                
                if (!$idExcel || $idExcel <= 0) {
                    throw new \Exception("El ID '{$data['id']}' no es válido");
                }

                $tipoEquipo = $data['tipo_equipo'] ?? $data['modelo'] ?? $data['marca'] ?? 'Producto';
                $subtipoEquipo = $data['subtipo_equipo'] ?? null;
                $modelo = $data['modelo'] ?? $tipoEquipo;
                $marca = $data['marca'] ?? 'Sin marca';
                $stock = isset($data['stock']) ? (int) $data['stock'] : 1;
                $precio = isset($data['precio']) ? (float) str_replace(',', '.', str_replace('.', '', $data['precio'])) : 0;

                // Buscar por ID
                $producto = Producto::find($idExcel);

                $datos = [
                    'tipo_equipo' => $tipoEquipo,
                    'subtipo_equipo' => $subtipoEquipo,
                    'modelo' => $modelo,
                    'marca' => $marca,
                    'stock' => $stock,
                    'precio' => $precio,
                ];

                if ($producto) {
                    $producto->update($datos);
                    $stats['actualizados']++;
                } else {
                    $datos['id'] = $idExcel;
                    Producto::create($datos);
                    $stats['creados']++;
                }

            } catch (\Exception $e) {
                $stats['errores']++;
                $stats['detalles_errores'][] = [
                    'fila' => $filaNumero,
                    'mensaje' => $e->getMessage(),
                ];
            }
        }

        fclose($handle);
        return $stats;
    }

    /**
     * Descarga plantilla CSV
     */
    public function downloadTemplate()
    {
        $this->checkAdmin();

        $headers = [
            'id',
            'categoria',
            'subcategoria',
            'modelo',
            'marca',
            'stock',
            'precio'
        ];

        $callback = function() use ($headers) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            fputcsv($file, $headers, ';');
            
            fputcsv($file, [
                '1',
                'Celular',
                'Smartphone',
                'iPhone 15 Pro',
                'Apple',
                '10',
                '1999.99'
            ], ';');
            
            fputcsv($file, [
                '2',
                'Computadora',
                'Laptop',
                'ROG Strix',
                'Asus',
                '5',
                '2500.00'
            ], ';');
            
            fputcsv($file, [
                '3',
                'Celular',
                'Smartphone',
                'iPhone 15 Pro',
                'Apple',
                '8',
                '1299.99'
            ], ';');
            
            fclose($file);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="plantilla_productos.csv"',
        ]);
    }
}