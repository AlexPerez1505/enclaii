@extends('layouts.app')

@section('title', 'Remisión')
@section('titulo', 'Remisión No' . $venta->id)

@section('content')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@500;600;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/pagos.css') }}?v={{ time() }}">

<style>
  :root {
    --bg: #f9fafb; 
    --card: #ffffff; 
    --ink: #111111; 
    --text-main: #333333;
    --muted: #888888; 
    --line: #ebebeb; 
    --blue: #007aff; 
    --blue-soft: #e6f0ff; 
    --success: #15803d; 
    --success-soft: #e6ffe6; 
    --danger: #ff4a4a; 
    --danger-soft: #ffebeb;
  }

  body {
    font-family: 'Quicksand', sans-serif;
    background-color: var(--bg);
    color: var(--text-main);
  }

  /* =========================================
     ESTRUCTURA GENERAL Y TARJETAS
  ========================================= */
  .page-wrap{
    max-width:1280px;
    margin:0 auto;
  }

  .card-clean{
    border:1px solid var(--line);
    border-radius:16px;
    background:var(--card);
    box-shadow:0 4px 12px rgba(0,0,0,0.02);
    overflow:hidden;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
  }

  .card-clean:hover{
    transform: translateY(-2px);
    box-shadow:0 8px 24px rgba(0,0,0,0.04);
  }

  .card-clean .card-body{
    padding:32px;
  }

  /* =========================================
     TIPOGRAFÍA Y MÉTRICAS
  ========================================= */
  .section-title{
    font-size:20px;
    font-weight:700;
    color:var(--ink);
    margin:0;
  }

  .section-sub{
    color:var(--muted);
    font-size:14px;
    margin-top:4px;
  }

  .metric-grid{
    display:grid;
    grid-template-columns:repeat(2,minmax(0,1fr));
    gap:16px;
    margin-top:24px;
  }

  .metric-box{
    border:1px solid var(--line);
    border-radius:12px;
    padding:16px;
    background:var(--card);
  }

  .metric-k{
    font-size:12px;
    color:var(--muted);
    text-transform:uppercase;
    letter-spacing:.5px;
    font-weight:700;
  }

  .metric-v{
    font-size:22px;
    color:var(--ink);
    font-weight:700;
    margin-top:4px;
  }

  .plan-chip{
    display:inline-flex;
    align-items:center;
    gap:8px;
    padding:8px 16px;
    border-radius:999px;
    font-size:13px;
    font-weight:700;
    background:var(--blue-soft);
    color:var(--blue);
    border:none;
  }

  /* =========================================
     BOTONES SISTEMA DE DISEÑO
  ========================================= */
  .btn-soft{
    border:none;
    border-radius:8px;
    font-weight:600;
    font-family: 'Quicksand', sans-serif;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    gap:8px;
    transition:all .2s ease;
    text-decoration:none;
    padding: 10px 20px;
    cursor: pointer;
  }

  .btn-soft:active{ transform: scale(0.98); }

  .btn-soft-sm{ padding:8px 16px; font-size:13px; }

  .btn-indigo{ background:var(--blue); color:#fff; }
  .btn-indigo:hover{ opacity: 0.9; color:#fff; }

  .btn-teal{ background:var(--success-soft); color:var(--success); }
  .btn-teal:hover{ background:#d1fada; color:var(--success); }

  .btn-slate{ background:transparent; color:#555; }
  .btn-slate:hover{ background:#f3f4f6; color:#333; }

  .btn-amber{ background:#fffbeb; color:#92400e; }

  .btn-rose{ background:var(--danger-soft); color:var(--danger); }
  .btn-rose:hover{ opacity: 0.9; }

  .btn-x{
    width:36px;
    height:36px;
    border-radius:50%;
    border:none;
    background:transparent;
    color:var(--muted);
    display:flex;
    align-items:center;
    justify-content:center;
    transition:.2s ease;
    font-size: 16px;
  }
  .btn-x:hover{ background:var(--line); color:var(--ink); }

  /* =========================================
     BARRA DE PROGRESO Y ELEMENTOS LISTA
  ========================================= */
  .progress-clean{
    display: block;
    width: 100%;
    height:12px;
    border-radius:999px;
    background:var(--line);
    overflow:hidden;
  }
  .progress-clean .progress-bar{ 
    display: block;
    height: 100%;
    border-radius:999px; 
    transition: width 0.4s ease;
  }

  .payment-list{ display:grid; gap:12px; }

  .payment-item{
    border:1px solid var(--line);
    border-radius:12px;
    padding:16px;
    background:var(--card);
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:16px;
    transition: transform 0.2s ease;
  }
  .payment-item:hover { transform: translateY(-1px); border-color: #d1d5db; }

  .payment-date{ font-weight:700; color:var(--ink); }
  .payment-desc{ color:var(--muted); font-size:13px; margin-top:4px; }
  .payment-amount{ font-weight:700; color:var(--ink); text-align:right; font-size:16px; }

  /* =========================================
     MODALES (OVERRIDE BOOTSTRAP)
  ========================================= */
  .modal-backdrop.show { opacity: 0.4; }

  .modal-pro .modal-content {
    border: 0;
    border-radius: 16px; /* <-- Bordes menos redondos como pediste */
    box-shadow: 0 24px 48px rgba(0,0,0,0.12);
    overflow: hidden;
    background: var(--card);
  }

  .modal-pro .modal-header {
    border-bottom: 1px solid var(--line);
    background: var(--card);
    padding: 24px 32px;
  }

  .modal-pro .modal-body {
    background: var(--card);
    padding: 32px;
  }

  .modal-pro .modal-footer {
    border-top: 1px solid var(--line);
    background: var(--bg);
    padding: 24px 32px;
  }

  /* =========================================
     INPUTS Y FORMULARIOS
  ========================================= */
  .pf-input,
  .bank-input,
  .bank-select,
  .form-control{
    border:1px solid var(--line);
    border-radius:8px;
    padding:12px 16px;
    font-size:14px;
    color: var(--text-main);
    font-family: 'Quicksand', sans-serif;
    font-weight: 500;
    transition: all 0.2s ease;
    background: var(--card);
    outline: none;
    box-shadow: none;
  }

  .pf-input:focus,
  .bank-input:focus,
  .bank-select:focus,
  .form-control:focus {
    border-color: var(--blue);
    box-shadow: 0 0 0 3px var(--blue-soft);
  }

  .form-label {
    font-weight: 700;
    font-size: 13px;
    color: var(--ink);
    margin-bottom: 8px;
  }

  /* =========================================
     ESTILOS ESPECÍFICOS DE MODALES DE PAGO
  ========================================= */
  .pf-top{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:12px;
    border:1px solid var(--line);
    border-radius:12px;
    background:var(--bg); 
    padding:20px 24px;
    margin-bottom:24px;
  }
  .pf-top .k{ font-size:12px; color:var(--muted); text-transform:uppercase; letter-spacing:.5px; font-weight:700; }
  .pf-top .v{ color:var(--ink); font-weight:700; font-size:24px; margin-top: 4px;}
  .pf-chip{ border:none; background:var(--blue-soft); color:var(--blue); border-radius:999px; padding:8px 16px; font-size:14px; font-weight:700; }

  .pf-alert{ display:none; border:1px solid #fde68a; background:#fffbeb; color:#78350f; border-radius:12px; padding:16px; margin-bottom:24px; font-size:14px; font-weight: 500;}
  .pf-alert.show{ display:block; }

  .pf-list{ display:grid; gap:16px; }

  .pf-row{
    border:1px solid var(--line);
    border-radius:12px;
    background:var(--card);
    padding:20px;
    transition: border-color 0.2s ease;
  }
  .pf-row:hover { border-color: #d1d5db; }

  .pf-grid{ display:grid; grid-template-columns:1.4fr .8fr .8fr auto; gap:16px; align-items:end; }

  .pf-label{ font-size:13px; color:var(--ink); font-weight:700; margin-bottom:8px; }

  .pf-money{
    display:flex;
    align-items:center;
    border:1px solid var(--line);
    border-radius:8px;
    overflow:hidden;
    background:var(--card);
    transition: 0.2s ease;
  }
  .pf-money:focus-within { border-color: var(--blue); box-shadow: 0 0 0 3px var(--blue-soft); }
  .pf-money span{ padding:0 12px; color:var(--muted); font-weight:700; }
  .pf-money input{ border:0; outline:0; padding:12px 12px 12px 0; width:100%; background: transparent; color: var(--text-main); font-weight: 600; font-family: 'Quicksand';}

  .pf-actions{ display:flex; justify-content:space-between; align-items:center; gap:12px; margin-top:24px; }

  .bank-card{
    border:1px solid var(--line);
    border-radius:12px;
    background:var(--bg);
    padding:24px;
    margin-bottom:24px;
  }
  .bank-k{ font-size:12px; color:var(--muted); text-transform:uppercase; letter-spacing:.5px; font-weight:700; }
  .bank-v{ color:var(--ink); font-weight:700; margin-top:4px; font-size: 15px;}
  .bank-amount{ color:var(--blue); font-weight:700; font-size:28px; margin-top: 4px; }

  .match-wrap{ margin-top:16px; }
  .match-bar{ height:8px; border-radius:999px; background:var(--line); overflow:hidden; position:relative; }
  .match-fill{ position:absolute; inset:0 100% 0 0; background:var(--blue); border-radius:999px; transition:.3s ease; }
  .match-label{ font-size:13px; color:var(--muted); margin-top:8px; font-weight: 600; }

  .inline-warning{
    display:none; margin-top:12px; border:1px solid #fde68a; background:#fffbeb; color:#78350f; border-radius:8px; padding:12px; font-size:13px; font-weight: 500;
  }

  /* =========================================
     SUBIDA DE ARCHIVOS
  ========================================= */
  .doc-inputs-wrap{ display:grid; gap:16px; margin-top:16px; }
  .doc-upload-row{ border:1px dashed #d1d5db; border-radius:12px; background:var(--bg); padding:20px; text-align: center;}
  
  .upload-preview{ display:flex; flex-wrap:wrap; gap:12px; margin-top:16px; justify-content: center; }
  .upload-item{ width:110px; border:1px solid var(--line); border-radius:8px; background:var(--card); padding:8px; overflow:hidden; text-align: left; box-shadow: 0 2px 8px rgba(0,0,0,0.02);}
  .upload-thumb{ width:100%; height:70px; border-radius:6px; overflow:hidden; background:var(--bg); display:flex; align-items:center; justify-content:center; border:1px solid var(--line); }
  .upload-thumb img{ width:100%; height:100%; object-fit:cover; }
  .upload-name{ margin-top:8px; font-size:11px; line-height:1.3; color:var(--ink); word-break:break-word; font-weight: 600; }

  /* Elementos Visuales Extras */
  .doc-stack{ display:flex; flex-wrap:wrap; gap:8px; align-items:center; }
  .doc-thumb{ width:48px; height:48px; border-radius:8px; overflow:hidden; border:1px solid var(--line); display:inline-block; background:var(--card); }
  .doc-thumb img{ width:100%; height:100%; object-fit:cover; display:block; }
  .doc-file{ display:inline-flex; align-items:center; gap:6px; max-width:220px; border: 1px solid var(--line); border-radius: 8px; padding: 6px 12px; font-size: 12px; font-weight: 600; color: #333; background: var(--card); text-decoration: none; }
  .doc-file:hover { background: var(--bg); }

  /* =========================================
     MODAL PIN (SEGURIDAD)
  ========================================= */
  .pin-box{
    width:56px;
    height:64px;
    border:1px solid var(--line);
    border-radius:12px;
    text-align:center;
    font-weight:700;
    font-size:28px;
    color: var(--ink);
    background: var(--bg);
    outline: none;
    transition: 0.2s ease;
  }
  .pin-box:focus { background: var(--card); border-color: var(--blue); box-shadow: 0 0 0 4px var(--blue-soft); }
  .pin-error{ display:none; color:var(--danger); font-size:14px; text-align:center; margin-top:16px; font-weight:700; }

  /* =========================================
     TOASTS & TABLES
  ========================================= */
  .toast-clean{ border:1px solid var(--line); border-radius:16px; box-shadow:0 12px 36px rgba(0,0,0,0.06); background: var(--card); }
  .toast-dot{ width:10px; height:10px; border-radius:999px; display:inline-block; }
  .toast-dot.ok{ background:var(--success); }
  .toast-dot.bad{ background:var(--danger); }

  .table-hover tbody tr:hover { background-color: var(--bg); }
  .table-light th { background: transparent; color: var(--muted); border-bottom: 1px solid var(--line); font-weight: 700; text-transform: uppercase; font-size: 12px; letter-spacing: 0.5px; padding-bottom: 12px; }
  td { border-bottom: 1px solid var(--line); color: var(--ink); padding: 16px 8px; }

  .badge { padding: 6px 12px; border-radius: 8px; font-weight: 700; font-size: 12px; border: none; }
  .bg-success { background: var(--success-soft) !important; color: var(--success) !important; }
  .bg-warning { background: var(--danger-soft) !important; color: var(--danger) !important; }

  @media(max-width:768px){
    .metric-grid{ grid-template-columns:1fr; }
    .pf-grid{ grid-template-columns:1fr; }
    .payment-item{ align-items:flex-start; flex-direction:column; }
    .payment-amount{ text-align:left; }
    .card-clean .card-body{ padding: 24px; }
    .modal-pro .modal-body, .modal-pro .modal-header, .modal-pro .modal-footer { padding: 24px; }
  }
</style>

@php
  if (!function_exists('ordinalES')) {
    function ordinalES($n){
      $ord = [
        'Primer','Segundo','Tercer','Cuarto','Quinto','Sexto','Séptimo','Octavo','Noveno','Décimo',
        'Onceavo','Doceavo','Treceavo','Catorceavo','Quinceavo','Dieciseisavo','Diecisieteavo','Dieciochoavo','Diecinueveavo','Veinteavo'
      ];
      return ($n >= 1 && $n <= count($ord)) ? $ord[$n - 1] : "Pago {$n}";
    }
  }

  if (!function_exists('esDescAutoContraEsperado')) {
    function esDescAutoContraEsperado($desc, $esperado){
      $desc = trim((string) $desc);
      $esperado = trim((string) $esperado);
      if ($desc === '') return true;
      if (preg_match('/^pago$/iu', $desc)) return true;
      if (preg_match('/^pago\s*\d+$/iu', $desc)) return true;
      return mb_strtolower($desc, 'UTF-8') === mb_strtolower($esperado, 'UTF-8');
    }
  }

  if (!function_exists('normalizeDetalleMetodos')) {
    function normalizeDetalleMetodos($detalle){
      if (empty($detalle)) return [];
      if (is_string($detalle)) {
        $tmp = json_decode($detalle, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($tmp)) {
          return $tmp;
        }
        return [];
      }
      return is_array($detalle) ? $detalle : [];
    }
  }

  if (!function_exists('buildPagoDocUrl')) {
    function buildPagoDocUrl($path){
      if (!$path) return null;
      $path = (string) $path;
      if (preg_match('#^https?://#i', $path)) {
        return $path;
      }
      $path = str_replace('\\', '/', $path);
      $path = preg_replace('#^public/#', '', $path);
      if (str_starts_with($path, 'storage/')) {
        return asset($path);
      }
      return asset('storage/' . ltrim($path, '/'));
    }
  }

  if (!function_exists('fileExtLike')) {
    function fileExtLike($pathOrName){
      $raw = (string) $pathOrName;
      $parsed = parse_url($raw, PHP_URL_PATH);
      $base = $parsed ?: $raw;
      return strtolower(pathinfo($base, PATHINFO_EXTENSION));
    }
  }

  if (!function_exists('isImageLikeFile')) {
    function isImageLikeFile($pathOrName){
      $ext = fileExtLike($pathOrName);
      return in_array($ext, [
        'jpg','jpeg','png','webp','gif','bmp','svg','avif','heic','heif','tif','tiff'
      ], true);
    }
  }

  if (!function_exists('isPdfLikeFile')) {
    function isPdfLikeFile($pathOrName){
      return fileExtLike($pathOrName) === 'pdf';
    }
  }

  if (!function_exists('resolvePagoDocUrlLegacy')) {
    function resolvePagoDocUrlLegacy($pago){
      $candidatos = [
        'documento_adicional',
        'documento_adicional_path',
        'documento',
        'documento_path',
        'recibo',
        'recibo_path',
        'archivo',
        'archivo_path',
        'path',
        'url',
      ];
      foreach ($candidatos as $k) {
        if (!empty($pago->{$k})) {
          return buildPagoDocUrl($pago->{$k});
        }
      }
      return null;
    }
  }

  if (!function_exists('resolvePagoDocLabelLegacy')) {
    function resolvePagoDocLabelLegacy($pago){
      $candidatos = [
        'documento_adicional_nombre',
        'documento_nombre',
        'recibo_nombre',
        'nombre_documento',
      ];
      foreach ($candidatos as $k) {
        if (!empty($pago->{$k})) {
          return (string) $pago->{$k};
        }
      }
      return 'Documento';
    }
  }

  if (!function_exists('resolvePagoDocs')) {
    function resolvePagoDocs($pago){
      $items = [];
      try {
        $docs = $pago->documentos ?? null;
        if ($docs && $docs->count()) {
          foreach ($docs as $i => $doc) {
            $path = (string) ($doc->ruta_archivo ?? '');
            if ($path === '') {
              continue;
            }
            $label = (string) ($doc->nombre_original ?? ('Archivo ' . ($i + 1)));
            $url = buildPagoDocUrl($path);
            if ($url) {
              $items[] = [
                'url' => $url,
                'label' => $label ?: ('Archivo ' . ($i + 1)),
                'is_image' => isImageLikeFile($label ?: $path),
                'is_pdf' => isPdfLikeFile($label ?: $path),
              ];
            }
          }
        }
      } catch (\Throwable $e) {
      }

      if (empty($items)) {
        $legacyUrl = resolvePagoDocUrlLegacy($pago);
        if ($legacyUrl) {
          $legacyLabel = resolvePagoDocLabelLegacy($pago);
          $items[] = [
            'url' => $legacyUrl,
            'label' => $legacyLabel,
            'is_image' => isImageLikeFile($legacyLabel ?: $legacyUrl),
            'is_pdf' => isPdfLikeFile($legacyLabel ?: $legacyUrl),
          ];
        }
      }
      return $items;
    }
  }

  $pagos = $pagos ?? collect();
  $totalVenta = $totalVenta ?? 0;
  $totalPagado = $totalPagado ?? 0;
  $saldoRestante = $saldoRestante ?? max((float) $totalVenta - (float) $totalPagado, 0);
  $progreso = $progreso ?? (((float) $totalVenta > 0) ? round(((float) $totalPagado / (float) $totalVenta) * 100, 2) : 0);

  $finIdsPagados = collect($pagos)
      ->where('aprobado', true)
      ->pluck('financiamiento_id')
      ->filter()
      ->unique()
      ->values();

  $paidInstallments = $finIdsPagados->count();

  $pagosFinanciamientoCollection = $venta->pagosFinanciamiento ?? collect();

  $pagosPendientes = collect($pagosFinanciamientoCollection)
      ->filter(function ($pf) use ($finIdsPagados) {
          return !((bool)($pf->cancelado ?? false))
              && (float)($pf->monto ?? 0) > 0
              && !$finIdsPagados->contains($pf->id);
      })
      ->sortBy('fecha_pago')
      ->values();

  $pagosEditable = $pagosPendientes;

  $planActualNormalizado = strtr(
      mb_strtolower(trim((string)($venta->plan ?? '')), 'UTF-8'),
      [
          'á' => 'a',
          'é' => 'e',
          'í' => 'i',
          'ó' => 'o',
          'ú' => 'u',
          'ü' => 'u',
          'ñ' => 'n',
      ]
  );

  $totalVentaView = (float) $totalVenta;
  $totalPagadoView = (float) $totalPagado;
  $saldoRestanteView = (float) $saldoRestante;
  $porcentajePagado = (float) $progreso;
  $tooltipText = "Has pagado $" . number_format($totalPagadoView, 2) . " de $" . number_format($totalVentaView, 2);
@endphp

<div class="toast-container position-fixed top-0 end-0 p-4" style="z-index:9999;">
  @php
    $toastType = null;
    $toastMsg = null;

    if (session('error')) {
      $toastType = 'bad';
      $toastMsg = session('error');
    } elseif (session('success')) {
      $toastType = 'ok';
      $toastMsg = session('success');
    } elseif (session('ok')) {
      $toastType = 'ok';
      $toastMsg = session('ok');
    }
  @endphp

  @if($toastType && $toastMsg)
    <div class="toast toast-clean align-items-center"
         role="alert"
         aria-live="assertive"
         aria-atomic="true"
         data-bs-delay="4000"
         id="oneToast">
      <div class="toast-header border-0 pb-0 bg-transparent">
        <span class="toast-dot {{ $toastType }} me-2"></span>
        <div class="me-auto text-truncate" style="max-width:320px; font-weight: 700; color: var(--ink);">
          Notificación
        </div>
        <button type="button" class="btn-close ms-2" data-bs-dismiss="toast" aria-label="Close"></button>
      </div>
      <div class="toast-body pt-1" style="color: var(--text-main); font-weight: 500; font-size: 14px;">
        {{ $toastMsg }}
      </div>
    </div>
  @endif
</div>

<div class="container mt-5 mb-0 page-wrap">
  <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
       {{--Agrupa los dos botones--}}
       <div class="d-flex gap-2">
           <button type="button" onclick="history.back()" class="btn btn-soft btn-slate btn-soft-sm">
               <i class="bi bi-arrow-left"></i>
               Regresar
            </button>
            <a href="{{ url('/ventas') }}" class="btn btn-soft btn-slate btn-soft-sm">
                <i class="bi bi-arrow-left"></i>
                Historial Remisiones
            </a>
        </div>
    <span class="plan-chip">
      <i class="bi bi-credit-card-2-front"></i>
      Plan actual: {{ $venta->plan ?? 'Sin plan' }}
    </span>
  </div>
</div>

<div class="container my-4 page-wrap">
  <div class="row g-4">

    <div class="col-lg-5">
      <div class="card-clean">
        <div class="card-body">
          <div>
            <h5 class="section-title">
              {{ $venta->cliente->nombre ?? 'Cliente' }} {{ $venta->cliente->apellido ?? '' }}
            </h5>
            <div class="section-sub">
              Resumen financiero de la remisión.
            </div>
          </div>

          <div class="metric-grid">
            <div class="metric-box">
              <div class="metric-k">Plan</div>
              <div class="metric-v">{{ $venta->plan ?? '—' }}</div>
            </div>

            <div class="metric-box">
              <div class="metric-k">Total</div>
              <div class="metric-v">${{ number_format($totalVentaView, 2) }}</div>
            </div>

            <div class="metric-box">
              <div class="metric-k">Total pagado</div>
              <div class="metric-v">${{ number_format($totalPagadoView, 2) }}</div>
            </div>

            <div class="metric-box">
              <div class="metric-k">Saldo restante</div>
              <div class="metric-v text-primary">${{ number_format($saldoRestanteView, 2) }}</div>
            </div>
          </div>

          <div class="mt-4 pt-2">
            <div class="d-flex align-items-center justify-content-between mb-2">
              <label class="form-label mb-0 fw-bold">Progreso de liquidación</label>
              <span class="fw-bold">{{ number_format((float)$porcentajePagado, 2, '.', '') }}%</span>
            </div>

            <div class="progress-clean"
                 data-bs-toggle="tooltip"
                 data-bs-placement="top"
                 title="{{ $tooltipText }}">
              <div class="progress-bar
                @if($porcentajePagado < 50) bg-danger
                @elseif($porcentajePagado < 100) bg-warning
                @else bg-success @endif"
                role="progressbar"
                style="width: {{ number_format(min(100, max(0, $porcentajePagado)), 2, '.', '') }}%; background-color: var(--blue);">
              </div>
            </div>
          </div>

          <div class="mt-4 pt-2 d-flex flex-wrap gap-2">
            <a href="{{ route('venta.pagos-global.pdf', $venta->id) }}"
               target="_blank"
               class="btn btn-soft w-100" style="border: 1px solid var(--blue); color: var(--blue); background: var(--card);">
              <i class="bi bi-file-earmark-pdf"></i>
              Descargar plan global PDF
            </a>
          </div>
        </div>
      </div>

      <div class="card-clean mt-4">
        <div class="card-body">
          <div class="d-flex align-items-center justify-content-between gap-2 mb-4">
            <div>
              <h5 class="section-title">Calendario</h5>
              <div class="section-sub mb-0">Próximos cobros pendientes.</div>
            </div>

            <button type="button"
                    class="btn btn-soft btn-slate btn-soft-sm px-3"
                    data-bs-toggle="modal"
                    data-bs-target="#modalEditarPagos">
              <i class="bi bi-pencil-square"></i>
            </button>
          </div>

          @if($pagosPendientes->isEmpty())
            <div class="text-center text-muted py-4 small fw-bold" style="background: var(--bg); border-radius: 12px;">
              No hay cobros pendientes.
            </div>
          @else
            <div class="payment-list">
              @foreach($pagosPendientes as $p)
                @php
                  $n = $paidInstallments + $loop->iteration;
                  $labelAuto = ordinalES($n) . ' pago';
                  $isAuto = esDescAutoContraEsperado($p->descripcion, $labelAuto);
                  $descMostrada = $isAuto ? $labelAuto : $p->descripcion;
                  $fechaISO = \Carbon\Carbon::parse($p->fecha_pago)->format('Y-m-d');
                @endphp

                <div class="payment-item">
                  <div>
                    <div class="payment-date">
                      {{ \Carbon\Carbon::parse($p->fecha_pago)->format('d M, Y') }}
                    </div>
                    <div class="payment-desc">{{ $descMostrada }}</div>
                  </div>

                  <div>
                    <div class="payment-amount">${{ number_format((float)$p->monto, 2) }}</div>

                    <button type="button"
                            class="btn btn-soft btn-indigo btn-soft-sm mt-2 w-100"
                            data-bs-toggle="modal"
                            data-bs-target="#pagoModal"
                            data-fin-id="{{ $p->id }}"
                            data-fecha="{{ $fechaISO }}"
                            data-monto="{{ number_format((float)$p->monto, 2, '.', '') }}"
                            data-descripcion="{{ $descMostrada }}"
                            data-action="{{ route('ventas.pagos.store', $p->venta_id) }}">
                      Cobrar
                    </button>
                  </div>
                </div>
              @endforeach
            </div>
          @endif
        </div>
      </div>
    </div>

    <div class="col-lg-7">
      <div class="card-clean">
        <div class="card-body">
          <div class="mb-4">
            <h5 class="section-title">Historial de Transacciones</h5>
            <div class="section-sub">Pagos registrados y por aprobar.</div>
          </div>

          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead class="table-light">
                <tr>
                  <th>Fecha</th>
                  <th>Monto</th>
                  <th>Método</th>
                  <th>Comprobante</th>
                  <th>Estado</th>
                  <th class="text-end">Acción</th>
                </tr>
              </thead>

              <tbody>
                @forelse($pagos as $pago)
                  @php
                    $detalleRows = normalizeDetalleMetodos($pago->detalle_metodos ?? null);
                    $docs = resolvePagoDocs($pago);
                  @endphp

                  <tr>
                    <td class="fw-bold">{{ \Carbon\Carbon::parse($pago->fecha_pago)->format('d/m/Y') }}</td>

                    <td class="fw-bold">
                      ${{ number_format((float)$pago->monto, 2) }}
                    </td>

                    <td>
                      <div class="fw-semibold">{{ $pago->metodo_pago }}</div>

                      @if(!empty($detalleRows) && is_array($detalleRows))
                        <div class="small text-muted mt-1" style="line-height:1.25;">
                          @foreach($detalleRows as $d)
                            <div>
                              • {{ $d['metodo'] ?? '—' }}:
                              ${{ number_format((float)($d['monto'] ?? 0), 2) }}
                            </div>
                          @endforeach
                        </div>
                      @endif
                    </td>

                    <td>
                      @if(count($docs))
                        <div class="doc-stack">
                          @foreach($docs as $doc)
                            @if($doc['is_image'])
                              <a href="{{ $doc['url'] }}"
                                 class="doc-thumb"
                                 target="_blank"
                                 title="{{ $doc['label'] }}">
                                <img src="{{ $doc['url'] }}" alt="{{ $doc['label'] }}">
                              </a>
                            @else
                              <a href="{{ $doc['url'] }}"
                                 class="doc-file"
                                 target="_blank"
                                 title="{{ $doc['label'] }}">
                                <i class="bi {{ $doc['is_pdf'] ? 'bi-file-earmark-pdf' : 'bi-paperclip' }}"></i>
                                <span>Doc</span>
                              </a>
                            @endif
                          @endforeach
                        </div>
                      @elseif($pago->aprobado)
                        <a href="{{ route('pagos.recibo', $pago->id) }}"
                           class="btn btn-soft btn-slate btn-soft-sm" style="border: 1px solid var(--line);"
                           target="_blank">
                          Recibo
                        </a>
                      @else
                        <span class="text-muted small">—</span>
                      @endif
                    </td>

                    <td>
                      @if($pago->aprobado)
                        <span class="badge bg-success">Aprobado</span>
                      @else
                        <span class="badge bg-warning">Pendiente</span>
                      @endif
                    </td>

                    <td class="text-end">
                      <div class="d-flex gap-2 justify-content-end">
                        @if(!$pago->aprobado)
                          @if($pago->financiamiento_id)
                            <button type="button"
                                    class="btn btn-soft btn-teal btn-soft-sm px-2 aprobar-pago-btn"
                                    data-form-id="form-{{ $pago->id }}">
                              <i class="bi bi-check2"></i>
                            </button>

                            <form id="form-{{ $pago->id }}"
                                  action="{{ route('pagos.marcarPagado', $pago->financiamiento_id) }}"
                                  method="POST"
                                  class="d-none">
                              @csrf
                              @method('PUT')
                              <input type="hidden" name="pin" class="pin-input">
                            </form>
                          @endif

                          <button type="button"
                                  class="btn btn-soft btn-rose btn-soft-sm px-2 eliminar-pago-btn"
                                  data-form-id="del-{{ $pago->id }}">
                            <i class="bi bi-trash"></i>
                          </button>

                          <form id="del-{{ $pago->id }}"
                                action="{{ route('pagos.destroy', $pago->id) }}"
                                method="POST"
                                class="d-none">
                            @csrf
                            @method('DELETE')
                          </form>
                        @else
                          <button type="button"
                                  class="btn btn-soft btn-slate btn-soft-sm px-2 revertir-pago-btn" style="border: 1px solid var(--line);"
                                  data-form-id="rev-{{ $pago->id }}">
                            <i class="bi bi-arrow-counterclockwise"></i>
                          </button>

                          <form id="rev-{{ $pago->id }}"
                                action="{{ route('pagos.revertir', $pago->id) }}"
                                method="POST"
                                class="d-none">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="pin" class="pin-input">
                          </form>
                        @endif
                      </div>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="6" class="text-center text-muted py-5">
                      No hay pagos registrados.
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>

        </div>
      </div>
    </div>

  </div>
</div>

<div class="modal fade modal-pro" id="modalEditarPagos" tabindex="-1" aria-labelledby="modalEditarPagosLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg modal-fullscreen-sm-down">
    <form id="formEditarPagos"
          action="{{ route('ventas.pagosFinanciamiento.update', $venta->id) }}"
          method="POST"
          data-plan-actual="{{ $planActualNormalizado }}"
          onsubmit="return validarTotalPagos(event)">
      @csrf
      @method('PUT')

      <div class="modal-content">
        <div class="modal-header">
          <div class="w-100 d-flex align-items-center justify-content-between">
            <div>
              <h6 class="modal-title section-title" id="modalEditarPagosLabel">Editar Calendario de Pagos</h6>
              <div class="section-sub mb-0 mt-1">Configura las fechas y montos.</div>
            </div>

            <button type="button" class="btn-x" data-bs-dismiss="modal" aria-label="Cerrar">
              <i class="bi bi-x-lg"></i>
            </button>
          </div>
        </div>

        <div class="modal-body">
          @if($planActualNormalizado === 'contado')
            <div class="alert mb-4" style="border-radius:12px; border:1px solid #fde68a; background:#fffbeb;">
              <div class="fw-bold mb-1" style="color:#92400e;">
                <i class="bi bi-exclamation-triangle me-1"></i>
                Venta en plan de contado
              </div>
              <div class="small" style="color:#78350f; line-height:1.5;">
                Si agregas un pago adicional, el sistema cambiará el plan a
                <strong>personalizado</strong> automáticamente.
              </div>
            </div>
          @endif

          <div class="pf-top">
            <div>
              <div class="k">Saldo por programar</div>
              <div class="v">${{ number_format((float)$saldoRestante, 2) }}</div>
            </div>

            <div class="pf-chip">
              Total Programado: <strong>$<span id="pfTotal">0.00</span></strong>
            </div>
          </div>

          <div class="pf-alert" id="pfAlert"></div>

          <div class="pf-list" id="pfList" data-paid-offset="{{ $paidInstallments }}">
            @forelse($pagosEditable as $p)
              @php
                $fechaISO = \Carbon\Carbon::parse($p->fecha_pago)->format('Y-m-d');
                $n = $paidInstallments + $loop->iteration;
                $labelAuto = ordinalES($n) . ' pago';
                $isAuto = esDescAutoContraEsperado($p->descripcion, $labelAuto);
                $descMostrada = $isAuto ? $labelAuto : $p->descripcion;
              @endphp

              <div class="pf-row" data-row>
                <div class="pf-grid">
                  <div class="col-desc">
                    <div class="pf-label">Descripción</div>
                    <input type="text"
                           name="pagos_financiamiento[{{ $p->id }}][descripcion]"
                           class="pf-input w-100"
                           value="{{ $descMostrada }}"
                           data-auto="{{ $isAuto ? 1 : 0 }}"
                           data-manual="0"
                           oninput="this.dataset.manual='1'; this.dataset.auto='0';"
                           required>
                  </div>

                  <div class="col-fecha">
                    <div class="pf-label">Fecha Límite</div>
                    <input type="date"
                           name="pagos_financiamiento[{{ $p->id }}][fecha_pago]"
                           class="pf-input w-100"
                           value="{{ $fechaISO }}"
                           required>
                  </div>

                  <div class="col-monto">
                    <div class="pf-label">Monto a Cobrar</div>
                    <div class="pf-money">
                      <span>$</span>
                      <input type="number"
                             name="pagos_financiamiento[{{ $p->id }}][monto]"
                             class="monto-pago"
                             min="0"
                             step="0.01"
                             value="{{ number_format((float)$p->monto, 2, '.', '') }}"
                             required>
                    </div>
                  </div>

                  <div class="pf-trash col-trash pb-2">
                    <button type="button"
                            class="btn btn-soft btn-rose btn-soft-sm"
                            title="Eliminar"
                            onclick="eliminarPagoExistente(this)">
                      <i class="bi bi-trash"></i>
                    </button>

                    <input type="hidden"
                           name="pagos_financiamiento[{{ $p->id }}][eliminar]"
                           value="0"
                           class="campo-eliminar">
                  </div>
                </div>
              </div>
            @empty
              <div class="text-center text-muted small py-4 fw-bold" style="background: var(--bg); border-radius: 12px;">
                No hay cobros en el calendario actual.
              </div>
            @endforelse
          </div>

          <div class="pf-actions">
            <button type="button"
                    class="btn btn-soft btn-slate" style="border: 1px solid var(--line); background: var(--card);"
                    onclick="agregarFilaPago()">
              <i class="bi bi-plus-lg"></i>
              Añadir nuevo cobro
            </button>

            <div class="text-muted small fw-bold">
              El botón de guardar se habilitará cuando cuadren los montos.
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-soft btn-slate me-2" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit"
                  class="btn btn-soft btn-indigo px-4"
                  id="btnGuardarPagos"
                  disabled>
            Guardar Cambios
          </button>
        </div>
      </div>
    </form>
  </div>
</div>

<div class="modal fade modal-pro" id="modalConfirmarCambioPlan" tabindex="-1" aria-labelledby="modalConfirmarCambioPlanLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header border-0 pb-0 pt-4 px-4">
        <div class="w-100 d-flex align-items-center justify-content-between">
          <div>
            <h6 class="modal-title section-title" id="modalConfirmarCambioPlanLabel">Cambiar a Personalizado</h6>
          </div>
          <button type="button" class="btn-x" data-bs-dismiss="modal" aria-label="Cerrar">
            <i class="bi bi-x-lg"></i>
          </button>
        </div>
      </div>

      <div class="modal-body px-4">
        <div style="border:1px solid #fde68a; background:#fffbeb; border-radius:12px; padding:20px;">
          <div class="fw-bold mb-2" style="color:#92400e; font-size: 15px;">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            Esta venta es de contado
          </div>

          <div class="small" style="color:#78350f; line-height:1.6; font-weight: 500;">
            Al agregar un nuevo pago, el sistema cambiará automáticamente el tipo de plan de esta remisión a <strong>"Personalizado"</strong>. <br><br>No se afectarán otros datos, solo cambiará la etiqueta del plan.
          </div>
        </div>

        <div class="mt-4 text-center fw-bold text-dark">
          ¿Estás seguro de continuar con el cambio?
        </div>
      </div>

      <div class="modal-footer border-0 justify-content-center pb-4 pt-2 bg-transparent">
        <button type="button"
                class="btn btn-soft btn-slate" style="border: 1px solid var(--line); background: var(--card);"
                data-bs-dismiss="modal">
          Cancelar
        </button>

        <button type="button"
                class="btn btn-soft btn-indigo"
                id="btnConfirmarCambioPlan">
          Sí, cambiar plan
        </button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade modal-pro" id="pagoModal" tabindex="-1" aria-labelledby="pagoModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form id="formPagoModal" method="POST" enctype="multipart/form-data" class="w-100">
      @csrf
      <input type="hidden" name="financiamiento_id" id="pm-fin-id">

      <div class="modal-content">
        <div class="modal-header">
          <div class="w-100 d-flex align-items-center justify-content-between">
            <h6 class="modal-title section-title" id="pagoModalLabel">Registrar Cobro</h6>
            <button type="button" class="btn-x" data-bs-dismiss="modal" aria-label="Cerrar">
              <i class="bi bi-x-lg"></i>
            </button>
          </div>
        </div>

        <div class="modal-body">
          @if($planActualNormalizado === 'contado')
            <div class="alert mb-4" style="border-radius:12px; border:1px solid #fde68a; background:#fffbeb;">
              <div class="fw-bold mb-1" style="color:#92400e;">
                <i class="bi bi-exclamation-triangle me-1"></i>
                Venta en plan de contado
              </div>
              <div class="small fw-medium" style="color:#78350f; line-height:1.5;">
                Al registrar este cobro, el plan pasará automáticamente a <strong>personalizado</strong>.
              </div>
            </div>
          @endif

          <div class="bank-card">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <div class="bank-k">Vencimiento</div>
                <div class="bank-v" id="pm-fecha-text">—</div>
                <div class="bank-k mt-3" id="pm-desc-text"></div>
              </div>
              <div class="text-end">
                <div class="bank-k">Por Cobrar</div>
                <div class="bank-amount text-primary" id="pm-esperado-text">$0.00</div>
              </div>
            </div>
          </div>

          <input type="hidden" id="pm-expected-amount" value="0">

          <div class="mb-4">
            <label class="form-label">Monto recibido</label>
            <div class="input-group">
              <span class="input-group-text" style="background: var(--card); border-color: var(--line); border-right: none; font-weight: 700; color: var(--muted);">$</span>
              <input type="number"
                     step="0.01"
                     min="0"
                     class="form-control bank-input"
                     style="border-left: none; padding-left: 0; font-size: 18px; font-weight: 700;"
                     name="monto"
                     id="modalMonto"
                     required>
            </div>

            <div class="match-wrap">
              <div class="match-bar">
                <div class="match-fill" id="matchFill"></div>
              </div>
              <div class="match-label">
                <span id="matchText" class="fw-bold">0% coincide con el esperado</span>
              </div>
            </div>

            <div class="inline-warning" id="pm-mismatch">
              El monto ingresado no cuadra. Edita el plan primero si necesitas ajustar montos permanentemente.
            </div>
          </div>

          <div class="row g-3 mb-4">
            <div class="col-md-6">
              <label class="form-label">Fecha Real</label>
              <input type="date"
                     class="form-control bank-input"
                     name="fecha_pago"
                     id="modalFecha"
                     required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Método</label>
              <select class="form-select bank-select" name="metodo_pago" id="modalMetodo" required>
                <option value="">Selecciona…</option>
                <option value="Efectivo">Efectivo</option>
                <option value="Transferencia bancaria">Transferencia</option>
                <option value="Depósito">Depósito</option>
                <option value="Tarjeta">Tarjeta</option>
                <option value="Cheque">Cheque</option>
                <option value="Bien mueble o inmueble">Bienes</option>
              </select>
            </div>
          </div>

          <div class="mt-4 pt-4 border-top" style="border-color: var(--line) !important;">
            <div class="d-flex align-items-center justify-content-between mb-2">
              <label class="form-label mb-0">División de Pago (Opcional)</label>
              <button type="button"
                      class="btn btn-soft btn-slate btn-soft-sm" style="border: 1px solid var(--line); background: var(--card);"
                      id="btnAddParte">
                <i class="bi bi-plus-lg"></i> Dividir
              </button>
            </div>
            <div class="small text-muted fw-medium mt-1">
              Para cobros en múltiples métodos (Ej: Efectivo + Tarjeta).
            </div>
            <div id="partesWrap" class="mt-3"></div>
            <div class="inline-warning mt-2" id="partesMismatch">
              La suma de las divisiones debe dar el monto total recibido.
            </div>
          </div>

          <div class="mt-4 pt-4 border-top" style="border-color: var(--line) !important;">
            <div class="d-flex align-items-center justify-content-between mb-2">
              <label class="form-label mb-0">Comprobantes (Opcional)</label>
              <button type="button"
                      class="btn btn-soft btn-slate btn-soft-sm" style="border: 1px solid var(--line); background: var(--card);"
                      id="btnAddDocumento">
                <i class="bi bi-paperclip"></i> Adjuntar
              </button>
            </div>
            <div class="small text-muted fw-medium">
              Fotografías o PDF de la transferencia / recibo.
            </div>
            <div id="docsInputsWrap" class="doc-inputs-wrap mt-3"></div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-soft btn-indigo w-100 justify-content-center py-3" style="font-size: 15px;">
            Confirmar y Registrar
          </button>
        </div>
      </div>
    </form>
  </div>
</div>

<div class="modal fade modal-pro" id="pinModal" tabindex="-1" aria-labelledby="pinModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" style="max-width:400px;">
    <div class="modal-content">
      <div class="modal-header border-0 pb-0 pt-4 px-4 justify-content-center position-relative">
        <h6 class="modal-title section-title" id="pinModalLabel">Acción Protegida</h6>
        <button type="button" class="btn-x position-absolute" style="right: 24px; top: 20px;" data-bs-dismiss="modal" aria-label="Cerrar">
            <i class="bi bi-x-lg"></i>
        </button>
      </div>

      <div class="modal-body text-center py-4 px-4">
        <div class="text-muted small fw-bold mb-4">Ingresa tu PIN maestro para autorizar.</div>

        <div class="d-flex gap-2 justify-content-center" aria-label="PIN">
          @for($i = 0; $i < 6; $i++)
            <input class="pin-box" type="password" inputmode="numeric" maxlength="1" autocomplete="one-time-code">
          @endfor
        </div>

        <div class="pin-error mt-4 p-2" id="pinError" style="background: var(--danger-soft); border-radius: 8px;">PIN incorrecto. Intenta de nuevo.</div>
        <input type="hidden" id="pinTargetFormId">
      </div>
      
      <div class="modal-footer border-0 pt-0 justify-content-center pb-4 bg-transparent">
          <div class="small text-muted fw-bold"><i class="bi bi-shield-lock-fill me-1 text-success"></i> Verificación segura</div>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/locale/es.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function(){
  document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => new bootstrap.Tooltip(el));

  const oneToast = document.getElementById('oneToast');

  if (oneToast) {
    bootstrap.Toast.getOrCreateInstance(oneToast).show();
    oneToast.addEventListener('hidden.bs.toast', () => oneToast.remove());
  }

  const pagoModalEl = document.getElementById('pagoModal');
  const formPago = document.getElementById('formPagoModal');

  const pmFinId = document.getElementById('pm-fin-id');
  const montoInput = document.getElementById('modalMonto');
  const fechaInput = document.getElementById('modalFecha');
  const metodoInput = document.getElementById('modalMetodo');

  const expectedEl = document.getElementById('pm-expected-amount');
  const esperadoTxt = document.getElementById('pm-esperado-text');
  const fechaTxt = document.getElementById('pm-fecha-text');
  const descTxt = document.getElementById('pm-desc-text');

  const mismatch = document.getElementById('pm-mismatch');
  const matchFill = document.getElementById('matchFill');
  const matchText = document.getElementById('matchText');

  const partesWrap = document.getElementById('partesWrap');
  const btnAddParte = document.getElementById('btnAddParte');
  const partesMismatch = document.getElementById('partesMismatch');

  const docsInputsWrap = document.getElementById('docsInputsWrap');
  const btnAddDocumento = document.getElementById('btnAddDocumento');

  const fmtMoney = (n) => Number(n || 0).toLocaleString('es-MX', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  });

  const formatDateES = (iso) => {
    if (!iso) return '—';
    const d = new Date(String(iso).slice(0, 10) + 'T00:00:00');
    return d.toLocaleDateString('es-MX', {day: '2-digit', month: 'short', year: 'numeric'});
  };

  const toISODate = (v) => v ? String(v).slice(0, 10) : '';

  function prettySize(bytes){
    const n = Number(bytes || 0);
    if (n < 1024) return n + ' B';
    if (n < 1024 * 1024) return (n / 1024).toFixed(1) + ' KB';
    return (n / (1024 * 1024)).toFixed(1) + ' MB';
  }

  function updateMatch(){
    if (!expectedEl || !montoInput || !matchFill || !matchText || !mismatch) return;

    const esperado = parseFloat(expectedEl.value || 0);
    const ingresado = parseFloat(montoInput.value || 0);

    let pct = 0;

    if (esperado > 0) {
      pct = Math.max(0, Math.min(100, (ingresado / esperado) * 100));
    }

    matchFill.style.inset = `0 ${Math.max(0, 100 - pct)}% 0 0`;
    matchText.textContent = `${Math.round(pct)}% coincide con el esperado`;

    const iguales = Math.abs(esperado - ingresado) < 0.005;
    mismatch.style.display = iguales ? 'none' : 'block';
  }

  let partesIndex = 0;
  let documentoIndex = 0;

  function parteRowHTML(idx){
    return `
      <div class="d-flex gap-2 align-items-center mb-2 parte-row" data-parte>
        <select class="form-select bank-select w-50" name="partes[${idx}][metodo]" required>
          <option value="">Método…</option>
          <option value="Efectivo">Efectivo</option>
          <option value="Transferencia bancaria">Transferencia bancaria</option>
          <option value="Depósito">Depósito</option>
          <option value="Tarjeta">Tarjeta</option>
          <option value="Cheque">Cheque</option>
          <option value="Bien mueble o inmueble">Bien mueble o inmueble</option>
        </select>

        <div class="input-group w-50">
          <span class="input-group-text" style="background: var(--card); border-color: var(--line); border-right:none; color: var(--muted); font-weight:700;">$</span>
          <input type="number"
                 step="0.01"
                 min="0"
                 class="form-control bank-input parte-monto"
                 style="border-left: none; padding-left:0; font-weight: 600;"
                 name="partes[${idx}][monto]"
                 required>
        </div>

        <button type="button" class="btn btn-soft btn-rose btn-soft-sm btn-remove-parte px-3">
          <i class="bi bi-trash"></i>
        </button>
      </div>
    `;
  }

  function sumPartes(){
    let total = 0;
    document.querySelectorAll('.parte-monto').forEach(inp => {
      total += parseFloat(inp.value || 0);
    });
    return total;
  }

  function updatePartes(){
    if (!partesMismatch) return;

    const rows = document.querySelectorAll('[data-parte]');
    const monto = parseFloat(montoInput?.value || 0);

    if (!rows.length) {
      partesMismatch.style.display = 'none';
      return;
    }

    const total = sumPartes();
    partesMismatch.style.display = Math.abs(total - monto) < 0.005 ? 'none' : 'block';
  }

  btnAddParte?.addEventListener('click', function(){
    if (!partesWrap) return;
    partesWrap.insertAdjacentHTML('beforeend', parteRowHTML(partesIndex++));
    updatePartes();
  });

  partesWrap?.addEventListener('input', function(e){
    if (e.target.classList.contains('parte-monto')) {
      updatePartes();
    }
  });

  partesWrap?.addEventListener('click', function(e){
    const btn = e.target.closest('.btn-remove-parte');
    if (!btn) return;
    const row = btn.closest('[data-parte]');
    if (row) row.remove();
    updatePartes();
  });

  montoInput?.addEventListener('input', function(){
    updateMatch();
    updatePartes();
  });

  function docRowHTML(idx){
    return `
      <div class="doc-upload-row" data-doc-row>
        <div class="d-flex align-items-center justify-content-between gap-2 mb-3">
          <div>
            <div class="fw-bold small text-dark"><i class="bi bi-cloud-arrow-up text-primary me-1"></i> Archivo ${idx + 1}</div>
          </div>

          <button type="button" class="btn btn-soft btn-rose btn-soft-sm btn-remove-doc">
            <i class="bi bi-trash"></i>
          </button>
        </div>

        <input type="file"
               name="documentos[]"
               class="form-control bank-input doc-file-input w-100"
               accept=".pdf,.jpg,.jpeg,.png,.webp,.gif,.bmp,.svg,.avif,.heic,.heif,.tif,.tiff,image/*,application/pdf">

        <div class="upload-preview"></div>
      </div>
    `;
  }

  btnAddDocumento?.addEventListener('click', function(){
    if (!docsInputsWrap) return;
    docsInputsWrap.insertAdjacentHTML('beforeend', docRowHTML(documentoIndex++));
  });

  docsInputsWrap?.addEventListener('click', function(e){
    const btn = e.target.closest('.btn-remove-doc');
    if (!btn) return;
    const row = btn.closest('[data-doc-row]');
    if (row) row.remove();
  });

  docsInputsWrap?.addEventListener('change', function(e){
    const input = e.target.closest('.doc-file-input');
    if (!input) return;

    const row = input.closest('[data-doc-row]');
    const preview = row.querySelector('.upload-preview');

    preview.innerHTML = '';

    Array.from(input.files || []).forEach(file => {
      const item = document.createElement('div');
      item.className = 'upload-item';

      const thumb = document.createElement('div');
      thumb.className = 'upload-thumb';

      if (file.type && file.type.startsWith('image/')) {
        const img = document.createElement('img');
        img.src = URL.createObjectURL(file);
        thumb.appendChild(img);
      } else {
        thumb.innerHTML = '<i class="bi bi-file-earmark-pdf" style="font-size:28px; color:var(--danger);"></i>';
      }

      const name = document.createElement('div');
      name.className = 'upload-name text-truncate';
      name.textContent = file.name;

      const size = document.createElement('div');
      size.className = 'text-muted small mt-1';
      size.textContent = prettySize(file.size);

      item.appendChild(thumb);
      item.appendChild(name);
      item.appendChild(size);
      preview.appendChild(item);
    });
  });

  pagoModalEl?.addEventListener('show.bs.modal', function(event){
    const btn = event.relatedTarget;
    if (!btn) return;

    const finId = btn.getAttribute('data-fin-id') || '';
    const fecha = btn.getAttribute('data-fecha') || '';
    const monto = btn.getAttribute('data-monto') || '0.00';
    const descripcion = btn.getAttribute('data-descripcion') || '';
    const action = btn.getAttribute('data-action') || '';

    if (formPago) formPago.action = action;
    if (pmFinId) pmFinId.value = finId;
    if (montoInput) montoInput.value = monto;
    if (fechaInput) fechaInput.value = toISODate(fecha);
    if (expectedEl) expectedEl.value = monto;
    if (esperadoTxt) esperadoTxt.textContent = '$' + fmtMoney(monto);
    if (fechaTxt) fechaTxt.textContent = formatDateES(fecha);
    if (descTxt) descTxt.textContent = descripcion;

    if (metodoInput) metodoInput.value = '';
    if (partesWrap) partesWrap.innerHTML = '';
    if (docsInputsWrap) docsInputsWrap.innerHTML = '';

    partesIndex = 0;
    documentoIndex = 0;

    updateMatch();
    updatePartes();
  });

  formPago?.addEventListener('submit', function(e){
    const rows = document.querySelectorAll('[data-parte]');
    const monto = parseFloat(montoInput?.value || 0);

    if (rows.length) {
      const total = sumPartes();
      if (Math.abs(total - monto) >= 0.005) {
        e.preventDefault();
        updatePartes();
        return false;
      }
    }
    return true;
  });
});
</script>

<script>
(function(){
  const form = document.getElementById('formEditarPagos');
  if (!form) return;

  let contadorNuevoPago = 1;
  let cambioPlanConfirmado = false;
  let pendienteAgregarPagoDespuesDeConfirmar = false;
  let submitPagoModalPendiente = false;

  const planActual = (form.dataset.planActual || '').trim().toLowerCase();
  const ventaEsContado = planActual === 'contado';

  const modalCambioPlanEl = document.getElementById('modalConfirmarCambioPlan');
  const modalCambioPlan = modalCambioPlanEl ? bootstrap.Modal.getOrCreateInstance(modalCambioPlanEl) : null;
  const btnConfirmarCambioPlan = document.getElementById('btnConfirmarCambioPlan');
  const formPagoModal = document.getElementById('formPagoModal');

  const objetivoCents = Math.round(Number(@json((float) $saldoRestante)) * 100);

  const $list = () => document.getElementById('pfList');
  const $alert = () => document.getElementById('pfAlert');
  const $total = () => document.getElementById('pfTotal');
  const $btn = () => document.getElementById('btnGuardarPagos');

  function ordinalESjs(n){
    const ord = [
      'Primer','Segundo','Tercer','Cuarto','Quinto','Sexto','Séptimo','Octavo','Noveno','Décimo',
      'Onceavo','Doceavo','Treceavo','Catorceavo','Quinceavo','Dieciseisavo','Diecisieteavo','Dieciochoavo','Diecinueveavo','Veinteavo'
    ];
    return n >= 1 && n <= ord.length ? ord[n - 1] : `Pago ${n}`;
  }

  function fmtMoney(n){
    return Number(n || 0).toLocaleString('es-MX', {
      minimumFractionDigits: 2,
      maximumFractionDigits: 2
    });
  }

  function toCents(v){
    return Math.round(Number(v || 0) * 100);
  }

  function rowsVisible(){
    const list = $list();
    if (!list) return [];
    return Array.from(list.querySelectorAll('[data-row]')).filter(row => row.style.display !== 'none');
  }

  function sumCents(){
    return rowsVisible().reduce((acc, row) => {
      const inp = row.querySelector('.monto-pago');
      return acc + toCents(inp ? inp.value : 0);
    }, 0);
  }

  function setAlert(msg){
    const a = $alert();
    if (!a) return;

    if (!msg) {
      a.classList.remove('show');
      a.innerHTML = '';
      return;
    }

    a.classList.add('show');
    a.innerHTML = msg;
  }

  function getDescInput(row){
    return row.querySelector('input[name*="[descripcion]"]');
  }

  function esAutoGenerico(v){
    const s = (v || '').trim();
    if (!s) return true;
    if (/^pago$/iu.test(s)) return true;
    if (/^pago\s*\d+$/iu.test(s)) return true;
    return false;
  }

  function marcarCambioPlanPersonalizado(){
    cambioPlanConfirmado = true;
  }

  function requiereConfirmarCambioPlan(){
    return ventaEsContado && !cambioPlanConfirmado;
  }

  function renumerarSoloAuto(){
    const list = $list();
    const paidOffset = Number(list?.dataset?.paidOffset || 0);
    const visibles = rowsVisible();

    visibles.forEach((row, idx) => {
      const inp = getDescInput(row);
      if (!inp) return;
      if (inp.dataset.manual === '1') return;

      const n = paidOffset + idx + 1;
      const esperado = `${ordinalESjs(n)} pago`;
      const val = (inp.value || '').trim();

      if (!inp.dataset.auto) {
        inp.dataset.auto = (esAutoGenerico(val) || val === '' || val.toLowerCase() === esperado.toLowerCase()) ? '1' : '0';
      }

      if (val === '' || esAutoGenerico(val) || val.toLowerCase() === esperado.toLowerCase()) {
        inp.value = esperado;
        inp.dataset.auto = '1';
      } else {
        inp.dataset.auto = '0';
      }
    });
  }

  function actualizarTotalPagos(){
    const total = sumCents();
    const diff = objetivoCents - total;

    if ($total()) {
      $total().textContent = fmtMoney(total / 100);
    }

    if (rowsVisible().length === 0) {
      setAlert('No hay pagos para guardar en el calendario.');
      if ($btn()) $btn().disabled = true;
      return;
    }

    for (const row of rowsVisible()) {
      const desc = getDescInput(row);
      const date = row.querySelector('input[type="date"]');
      const mon = row.querySelector('.monto-pago');

      if (desc && !desc.value.trim()) {
        setAlert('Atención: Falta una descripción en uno de los pagos.');
        if ($btn()) $btn().disabled = true;
        return;
      }

      if (date && !date.value) {
        setAlert('Atención: Falta la fecha en uno de los pagos.');
        if ($btn()) $btn().disabled = true;
        return;
      }

      if (mon && parseFloat(mon.value || 0) <= 0) {
        setAlert('Atención: Hay un monto en $0.00.');
        if ($btn()) $btn().disabled = true;
        return;
      }
    }

    if (diff !== 0) {
      setAlert(diff > 0
        ? `Aún faltan asignar <b>$${fmtMoney(diff / 100)}</b> para completar el saldo.`
        : `Te excediste por <b>$${fmtMoney(Math.abs(diff) / 100)}</b>.`
      );
      if ($btn()) $btn().disabled = true;
      return;
    }

    if (ventaEsContado && cambioPlanConfirmado) {
      setAlert(`
        <div>
          <i class="bi bi-info-circle me-1"></i> <b>Plan contado cambiado a personalizado.</b><br>
          Al guardar, esta remisión quedará con calendario de pagos en plazos.
        </div>
      `);
    } else {
      setAlert('');
    }

    if ($btn()) {
      $btn().disabled = false;
    }
  }

  window.validarTotalPagos = function(e){
    actualizarTotalPagos();

    if ($btn() && $btn().disabled) {
      e.preventDefault();
      return false;
    }

    if (ventaEsContado && rowsVisible().length > 0 && !cambioPlanConfirmado) {
      e.preventDefault();
      pendienteAgregarPagoDespuesDeConfirmar = false;
      submitPagoModalPendiente = false;

      if (modalCambioPlan) {
        modalCambioPlan.show();
      }
      return false;
    }

    if (ventaEsContado && cambioPlanConfirmado) {
      marcarCambioPlanPersonalizado();
    }

    return true;
  };

  function bindRow(row){
    const mon = row.querySelector('.monto-pago');
    if (mon) {
      mon.addEventListener('input', actualizarTotalPagos);
      mon.addEventListener('blur', () => {
        if (mon.value) mon.value = (parseFloat(mon.value) || 0).toFixed(2);
        actualizarTotalPagos();
      });
    }

    const date = row.querySelector('input[type="date"]');
    if (date) {
      date.addEventListener('change', actualizarTotalPagos);
    }

    const desc = getDescInput(row);
    if (desc) {
      desc.addEventListener('input', () => {
        desc.dataset.manual = '1';
        desc.dataset.auto = '0';
      });
      desc.addEventListener('change', () => {
        desc.dataset.manual = '1';
        desc.dataset.auto = '0';
      });
    }
  }

  function redistribuirEquitativo(){
    const filas = rowsVisible();
    const n = filas.length;

    if (n === 0) {
      actualizarTotalPagos();
      return;
    }

    const base = Math.floor(objetivoCents / n);
    let acum = 0;

    filas.forEach((row, i) => {
      const cents = i === n - 1 ? objetivoCents - acum : base;
      const inp = row.querySelector('.monto-pago');
      if (inp) inp.value = (cents / 100).toFixed(2);
      acum += cents;
    });

    renumerarSoloAuto();
    actualizarTotalPagos();
  }

  function agregarFilaPagoReal(){
    const idx = 'nuevo_' + (contadorNuevoPago++);
    const visibles = rowsVisible();
    let lastDate = '';

    visibles.forEach(r => {
      const d = r.querySelector('input[type="date"]');
      if (d && d.value) lastDate = d.value;
    });

    const fechaNueva = lastDate
      ? moment(lastDate).add(1, 'months').format('YYYY-MM-DD')
      : moment().format('YYYY-MM-DD');

    const div = document.createElement('div');
    div.className = 'pf-row';
    div.setAttribute('data-row', '1');

    div.innerHTML = `
      <div class="pf-grid">
        <div class="col-desc">
          <div class="pf-label">Descripción</div>
          <input type="text"
                 name="pagos_financiamiento[${idx}][descripcion]"
                 class="pf-input w-100"
                 placeholder="Ej. Anticipo"
                 data-auto="1"
                 data-manual="0"
                 oninput="this.dataset.manual='1'; this.dataset.auto='0';"
                 required>
        </div>
        <div class="col-fecha">
          <div class="pf-label">Fecha Límite</div>
          <input type="date"
                 name="pagos_financiamiento[${idx}][fecha_pago]"
                 class="pf-input w-100"
                 value="${fechaNueva}"
                 required>
        </div>
        <div class="col-monto">
          <div class="pf-label">Monto a Cobrar</div>
          <div class="pf-money">
            <span>$</span>
            <input type="number"
                   name="pagos_financiamiento[${idx}][monto]"
                   class="monto-pago"
                   min="0"
                   step="0.01"
                   placeholder="0.00"
                   required>
          </div>
        </div>
        <div class="pf-trash col-trash pb-2">
          <button type="button"
                  class="btn btn-soft btn-rose btn-soft-sm"
                  title="Eliminar"
                  onclick="eliminarPagoNuevo(this)">
            <i class="bi bi-trash"></i>
          </button>
        </div>
      </div>
    `;

    const list = $list();
    if (!list) return;

    const emptyMessage = list.querySelector('.text-center.text-muted.small.py-4');
    if (emptyMessage) emptyMessage.remove();

    list.appendChild(div);
    bindRow(div);
    renumerarSoloAuto();
    redistribuirEquitativo();
  }

  window.agregarFilaPago = function(){
    if (requiereConfirmarCambioPlan()) {
      pendienteAgregarPagoDespuesDeConfirmar = true;
      submitPagoModalPendiente = false;

      if (modalCambioPlan) {
        modalCambioPlan.show();
      }
      return;
    }
    agregarFilaPagoReal();
  };

  btnConfirmarCambioPlan?.addEventListener('click', function(){
    marcarCambioPlanPersonalizado();

    if (modalCambioPlan) {
      modalCambioPlan.hide();
    }

    if (pendienteAgregarPagoDespuesDeConfirmar) {
      pendienteAgregarPagoDespuesDeConfirmar = false;
      agregarFilaPagoReal();
      return;
    }

    if (submitPagoModalPendiente && formPagoModal) {
      submitPagoModalPendiente = false;

      setTimeout(() => {
        if (formPagoModal.requestSubmit) {
          formPagoModal.requestSubmit();
        } else {
          formPagoModal.submit();
        }
      }, 150);
      return;
    }

    actualizarTotalPagos();
  });

  window.eliminarPagoNuevo = function(btn){
    const row = btn.closest('[data-row]');
    if (row) row.remove();
    redistribuirEquitativo();
  };

  window.eliminarPagoExistente = function(btn){
    const row = btn.closest('[data-row]');
    if (!row) return;

    const hidden = row.querySelector('.campo-eliminar');
    if (hidden) hidden.value = '1';

    row.style.display = 'none';
    redistribuirEquitativo();
  };

  document.addEventListener('DOMContentLoaded', function(){
    rowsVisible().forEach(bindRow);
    renumerarSoloAuto();

    setTimeout(actualizarTotalPagos, 120);

    const modalEl = document.getElementById('modalEditarPagos');
    if (modalEl) {
      modalEl.addEventListener('show.bs.modal', function(){
        setTimeout(() => {
          renumerarSoloAuto();
          actualizarTotalPagos();
        }, 80);
      });

      modalEl.addEventListener('hidden.bs.modal', function(){
        pendienteAgregarPagoDespuesDeConfirmar = false;
      });
    }

    if (formPagoModal) {
      formPagoModal.addEventListener('submit', function(e){
        if (ventaEsContado && !cambioPlanConfirmado) {
          e.preventDefault();
          submitPagoModalPendiente = true;
          pendienteAgregarPagoDespuesDeConfirmar = false;

          if (modalCambioPlan) {
            modalCambioPlan.show();
          }
          return false;
        }

        if (ventaEsContado && cambioPlanConfirmado) {
          marcarCambioPlanPersonalizado();
        }

        return true;
      });
    }
  });
})();
</script>

<script>
document.addEventListener('DOMContentLoaded', function(){
  const pinModalEl = document.getElementById('pinModal');
  if (!pinModalEl) return;

  const pinModal = bootstrap.Modal.getOrCreateInstance(pinModalEl);
  const pinBoxes = Array.from(document.querySelectorAll('.pin-box'));
  const pinTargetFormId = document.getElementById('pinTargetFormId');
  const pinError = document.getElementById('pinError');

  let targetFormId = null;

  function resetPin(){
    pinBoxes.forEach(i => i.value = '');
    if (pinError) pinError.style.display = 'none';

    setTimeout(() => {
      if (pinBoxes[0]) pinBoxes[0].focus();
    }, 180);
  }

  function getPin(){
    return pinBoxes.map(i => i.value).join('');
  }

  function submitPinForm(){
    const pin = getPin();

    if (!/^\d{6}$/.test(pin)) {
      if (pinError) pinError.style.display = 'block';
      return;
    }

    const form = document.getElementById(targetFormId);
    if (!form) return;

    const pinInput = form.querySelector('.pin-input');
    if (pinInput) pinInput.value = pin;

    form.submit();
  }

  document.querySelectorAll('.aprobar-pago-btn, .revertir-pago-btn').forEach(btn => {
    btn.addEventListener('click', function(){
      targetFormId = this.dataset.formId;
      if (pinTargetFormId) pinTargetFormId.value = targetFormId;

      pinModal.show();
      resetPin();
    });
  });

  pinBoxes.forEach((box, idx) => {
    box.addEventListener('input', function(){
      this.value = this.value.replace(/\D/g, '').slice(0, 1);

      if (this.value && pinBoxes[idx + 1]) {
        pinBoxes[idx + 1].focus();
      }

      if (getPin().length === 6) {
        submitPinForm();
      }
    });

    box.addEventListener('keydown', function(e){
      if (e.key === 'Backspace' && !this.value && pinBoxes[idx - 1]) {
        pinBoxes[idx - 1].focus();
      }
    });
  });

  pinModalEl.addEventListener('shown.bs.modal', resetPin);

  document.querySelectorAll('.eliminar-pago-btn').forEach(btn => {
    btn.addEventListener('click', function(){
      const formId = this.dataset.formId;
      const form = document.getElementById(formId);
      if (!form) return;

      if (confirm('¿Estás totalmente seguro que deseas eliminar permanentemente este pago?')) {
        form.submit();
      }
    });
  });
});
</script>

@endsection