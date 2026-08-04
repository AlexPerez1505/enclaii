<?php
/**
 * reparar_evidencias.php
 *
 * SOLUCIÓN DEFINITIVA:
 *   1) Mueve archivos de storage/app/public/public/* -> storage/app/public/*
 *   2) Corrige en la tabla `registros` las URLs que tienen "/storage/public/"
 *      duplicado, dejándolas como "/storage/..." correcto.
 *   3) Para evidencias que de verdad NO existen en ningún lado, reemplaza
 *      su valor en la BD por una imagen placeholder ("sin-evidencia.jpg")
 *      para que dejen de romper la interfaz, y genera un CSV con esos
 *      casos para que sepan qué hay que volver a capturar.
 *
 * USO:
 *   php reparar_evidencias.php --dry-run   (solo simula, no cambia nada)
 *   php reparar_evidencias.php --apply     (aplica los cambios de verdad)
 *
 * SIEMPRE corre primero con --dry-run para revisar el resumen antes de aplicar.
 */
 
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
 
use Illuminate\Support\Facades\DB;
 
$modo = in_array('--apply', $argv) ? 'apply' : 'dry-run';
echo "MODO: " . strtoupper($modo) . "\n";
echo str_repeat('=', 70) . "\n\n";
 
$storagePublicRoot = storage_path('app/public');
$anidadoRoot = $storagePublicRoot . DIRECTORY_SEPARATOR . 'public';
$placeholderRelativo = 'evidencias/sin-evidencia.jpg'; // ajusta si tu placeholder está en otro lado
$placeholderUrl = 'https://medibuy.grupomedibuy.com/storage/' . $placeholderRelativo;
 
// -----------------------------------------------------------
// PASO 0: asegurar que exista un placeholder
// -----------------------------------------------------------
$placeholderAbsoluto = $storagePublicRoot . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $placeholderRelativo);
if (!file_exists($placeholderAbsoluto)) {
    echo "⚠️  No existe placeholder en: $placeholderAbsoluto\n";
    echo "   Se usará de todas formas la URL, pero sube una imagen ahí (ej. 'Sin evidencia disponible.jpg')\n\n";
}
 
// -----------------------------------------------------------
// PASO 1: mover archivos anidados storage/app/public/public/* -> storage/app/public/*
// -----------------------------------------------------------
echo "PASO 1: Mover archivos de carpeta anidada 'public/public'\n";
echo str_repeat('-', 70) . "\n";
 
$movidos = 0;
$erroresMover = 0;
 
if (is_dir($anidadoRoot)) {
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($anidadoRoot, FilesystemIterator::SKIP_DOTS)
    );
 
    foreach ($iterator as $file) {
        if (!$file->isFile()) continue;
 
        $rutaOrigen = $file->getPathname();
        $rutaRelativaDentroDeAnidado = substr($rutaOrigen, strlen($anidadoRoot) + 1); // ej: evidencias/x.jpg
        $rutaDestino = $storagePublicRoot . DIRECTORY_SEPARATOR . $rutaRelativaDentroDeAnidado;
 
        $carpetaDestino = dirname($rutaDestino);
 
        if ($modo === 'apply') {
            if (!is_dir($carpetaDestino)) {
                mkdir($carpetaDestino, 0755, true);
            }
            if (!file_exists($rutaDestino)) {
                if (rename($rutaOrigen, $rutaDestino)) {
                    $movidos++;
                } else {
                    $erroresMover++;
                }
            } else {
                // Ya existe en destino (raro), no lo pisamos, solo contamos
                $erroresMover++;
            }
        } else {
            $movidos++; // en dry-run solo contamos lo que SE MOVERÍA
        }
    }
}
 
echo "Archivos " . ($modo === 'apply' ? 'movidos' : 'a mover') . ": $movidos\n";
if ($erroresMover > 0) {
    echo "⚠️  Conflictos/errores (ya existían en destino o no se pudieron mover): $erroresMover\n";
}
echo "\n";
 
// -----------------------------------------------------------
// PASO 2: corregir URLs en BD que tengan "/storage/public/" duplicado
// -----------------------------------------------------------
echo "PASO 2: Corregir URLs con '/storage/public/' duplicado en la BD\n";
echo str_repeat('-', 70) . "\n";
 
$campos = ['evidencia1', 'evidencia2', 'evidencia3'];
$corregidos = 0;
 
$registros = DB::table('registros')->select(array_merge(['id'], $campos))->get();
 
foreach ($registros as $registro) {
    $updates = [];
    foreach ($campos as $campo) {
        $valor = $registro->$campo;
        if (!empty($valor) && strpos($valor, '/storage/public/') !== false) {
            $nuevoValor = str_replace('/storage/public/', '/storage/', $valor);
            $updates[$campo] = $nuevoValor;
        }
    }
    if (!empty($updates)) {
        $corregidos++;
        if ($modo === 'apply') {
            DB::table('registros')->where('id', $registro->id)->update($updates);
        }
    }
}
 
echo "Registros con URL " . ($modo === 'apply' ? 'corregida' : 'a corregir') . ": $corregidos\n\n";
 
// -----------------------------------------------------------
// PASO 3: detectar los que YA NO se pueden recuperar y marcarlos con placeholder
// -----------------------------------------------------------
echo "PASO 3: Detectar evidencias irrecuperables y asignar placeholder\n";
echo str_repeat('-', 70) . "\n";
 
// Releemos (por si acabamos de mover archivos) para verificar existencia real
$registros = DB::table('registros')->select(array_merge(['id', 'numero_serie'], $campos))->get();
 
$irrecuperables = [];
$totalConPlaceholder = 0;
 
foreach ($registros as $registro) {
    $updates = [];
    foreach ($campos as $campo) {
        $valor = $registro->$campo;
        if (empty($valor)) continue;
 
        // Ya corregido arriba, así que ahora asumimos formato correcto
        $path = parse_url($valor, PHP_URL_PATH);
        $relativo = preg_replace('#^/storage/#', '', $path);
        $rutaCompleta = $storagePublicRoot . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relativo);
 
        // Si en modo dry-run aún no se movió nada, probamos también la ruta anidada
        $rutaAnidada = $anidadoRoot . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relativo);
 
        $existe = file_exists($rutaCompleta) || file_exists($rutaAnidada);
 
        if (!$existe) {
            $irrecuperables[] = [
                'id' => $registro->id,
                'numero_serie' => $registro->numero_serie,
                'campo' => $campo,
                'archivo' => basename($relativo),
                'url_original' => $valor,
            ];
            $updates[$campo] = $placeholderUrl;
        }
    }
    if (!empty($updates)) {
        $totalConPlaceholder++;
        if ($modo === 'apply') {
            DB::table('registros')->where('id', $registro->id)->update($updates);
        }
    }
}
 
echo "Registros con evidencia irrecuperable (" . ($modo === 'apply' ? 'YA marcados' : 'a marcar') . " con placeholder): $totalConPlaceholder\n";
echo "Total de campos de evidencia irrecuperables: " . count($irrecuperables) . "\n\n";
 
// Guardar listado para seguimiento manual (re-captura de fotos)
if (!empty($irrecuperables)) {
    $csvPath = __DIR__ . '/evidencias_para_recapturar.csv';
    $fp = fopen($csvPath, 'w');
    fputcsv($fp, ['id_registro', 'numero_serie', 'campo', 'archivo_faltante', 'url_original']);
    foreach ($irrecuperables as $r) {
        fputcsv($fp, $r);
    }
    fclose($fp);
    echo "📄 Lista para re-capturar evidencias guardada en: $csvPath\n\n";
}
 
echo str_repeat('=', 70) . "\n";
if ($modo === 'dry-run') {
    echo "Esto fue una SIMULACIÓN. No se cambió nada todavía.\n";
    echo "Si el resumen se ve bien, corre:\n";
    echo "   php reparar_evidencias.php --apply\n";
} else {
    echo "✅ Cambios aplicados.\n";
}
 