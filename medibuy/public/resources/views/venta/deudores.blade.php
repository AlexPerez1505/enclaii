@extends('layouts.app')
@section('title', 'Financiamientos')
@section('titulo', 'Financiamientos')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/deudores.css') }}?v={{ time() }}">

@php
use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Models\PagoRecordatorio;

/* ===== Helpers de servidor ===== */
function extraerPagoInicialDePagos($pagos) {
    foreach ($pagos as $pago) {
        if (stripos($pago->descripcion, 'Pago inicial') !== false || stripos($pago->descripcion, 'Enganche') !== false) {
            return $pago->monto;
        }
    }
    return 0;
}
function extraerFechasDePagos($pagos) {
    $fechas = [];
    foreach ($pagos as $pago) {
        if ($pago->fecha_pago) {
            try { $fechas[] = Carbon::parse($pago->fecha_pago); } catch (\Exception $e) {}
        }
    }
    return $fechas;
}
function obtenerPagoVencidoMasAntiguo($pagosPendientes) {
    $masAntiguo = null;

    foreach ($pagosPendientes as $p) {
        if ($p->fecha_pago) {
            try {
                // Normalizamos a inicio de día y SOLO consideramos fechas anteriores a hoy
                $f   = Carbon::parse($p->fecha_pago)->startOfDay();
                $hoy = Carbon::today();

                if ($f->lt($hoy) && !$p->pagado) {
                    if (!$masAntiguo || $f->lt($masAntiguo)) {
                        $masAntiguo = $f;
                    }
                }
            } catch (\Throwable $e) {}
        }
    }

    return $masAntiguo;
}
/** Ya notificado HOY por email o whatsapp para este pago? */
function yaNotificadoHoy(int $pagoId): bool {
    return PagoRecordatorio::where('pago_financiamiento_id', $pagoId)
        ->whereIn('channel', ['email','whatsapp'])
        ->whereDate('sent_at', Carbon::today()->toDateString())
        ->exists();
}

/* ===== Métricas y colecciones para banners (✅ CORREGIDO A GLOBAL) ===== */
$ventasConPagosProximos = [];
$ventasConPagosVencidos = [];
$metrics = ['total'=>0,'pendientes'=>0,'atrasadas'=>0,'liquidadas'=>0,'saldo'=>0.0];

foreach ($ventas as $ventaTmp) {
    $metrics['total']++;

    // Plan
    $pagosPlanTmp       = $ventaTmp->pagosFinanciamiento ?? collect();
    $pagosPendientesTmp = $pagosPlanTmp->where('pagado', false) ?? collect();
    $pagosPagadosTmp    = $pagosPlanTmp->where('pagado', true) ?? collect();
    $totalPagadoPlanTmp = (float) $pagosPagadosTmp->sum('monto');

    // Total original
    $totalOriginalTmp   = (float) ($ventaTmp->total_original ?? $ventaTmp->total ?? 0);

    // Pagos de venta (anticipo + trade-in)
    $pagosVentaTmp      = \App\Models\Pago::where('venta_id', $ventaTmp->id)->get();

    $montoAnticipoTmp   = (float) $pagosVentaTmp->filter(function($p){
        return (bool) ($p->es_anticipo ?? false);
    })->sum('monto');

    $montoTradeInTmp    = (float) $pagosVentaTmp->filter(function($p){
        $metodo = strtolower(trim(
            $p->metodo
            ?? $p->metodo_pago
            ?? $p->forma_pago
            ?? ''
        ));
        return in_array($metodo, ['trade-in','trade in','tradein']);
    })->sum('monto');

    // ✅ Global
    $totalPagadoGlobalTmp = $montoTradeInTmp + $montoAnticipoTmp + $totalPagadoPlanTmp;
    $restanteGlobalTmp    = max(0, $totalOriginalTmp - $totalPagadoGlobalTmp);

    if ($restanteGlobalTmp <= 0.01) {
        $metrics['liquidadas']++;
    } else {
        $vencidoMasAntiguo = obtenerPagoVencidoMasAntiguo($pagosPendientesTmp);
        if ($vencidoMasAntiguo) $metrics['atrasadas']++;
        else $metrics['pendientes']++;
        $metrics['saldo'] += $restanteGlobalTmp;
    }

    // Próximos (hoy / mañana / próximos 7 días)
    $fechas = extraerFechasDePagos($pagosPendientesTmp);
    foreach ($fechas as $fecha) {
        if ($fecha->isToday() || $fecha->isTomorrow() || $fecha->between(Carbon::now(), Carbon::now()->addDays(7), true)) {
            $ventasConPagosProximos[] = ['venta'=>$ventaTmp,'fecha'=>$fecha];
            break;
        }
    }

    // Vencidos (para banner)
    $vencidoMasAntiguo = obtenerPagoVencidoMasAntiguo($pagosPendientesTmp);
    if ($vencidoMasAntiguo) {
        $pagoVencido = $pagosPendientesTmp->first(function($p) use ($vencidoMasAntiguo) {
            try { return $p->fecha_pago && Carbon::parse($p->fecha_pago)->isSameDay($vencidoMasAntiguo); }
            catch (\Throwable $e) { return false; }
        });
        $ventasConPagosVencidos[] = [
            'venta' => $ventaTmp,
            'fecha' => $vencidoMasAntiguo,
            'pago'  => $pagoVencido,
            'dias'  => (int) $vencidoMasAntiguo->diffInDays(Carbon::today()),
            'ya'    => $pagoVencido ? yaNotificadoHoy($pagoVencido->id) : false,
        ];
    }
}
@endphp

<style>
:root{
  --bg:#f5f7fb;
  --ink:#0f172a;
  --muted:#6b7280;
  --line:#e2e8f0;
  --brand:#e0edff;
  --brand-soft:#eff4ff;
  --brand-ink:#1d4ed8;
  --ok:#dcfce7;
  --ok-ink:#166534;
  --warn:#fef3c7;
  --warn-ink:#92400e;
  --danger:#fee2e2;
  --danger-ink:#b91c1c;
  --card:#ffffff;
  --radius:18px;
  --shadow:0 14px 32px rgba(15,23,42,.06);
  --ease:cubic-bezier(.22,1,.36,1);
}

/* FONDO degradado tipo Base44 */
body{
  background:
    linear-gradient(
      180deg,
      #ffffff 0%,
      #ffffff 28%,
      #f6ffe9 70%,
      #f0ffe0 100%
    );
  color:var(--ink);
  font-family: "Söhne", "Circular Std", "Poppins", system-ui, -apple-system,
               "Segoe UI", "Helvetica Neue", Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
}

input,
button,
select,
textarea,
.badge,
.alert,
.btn{
  font-family: "Söhne", "Circular Std", "Poppins", system-ui, -apple-system,
               "Segoe UI", "Helvetica Neue", Arial, sans-serif;
}

/* Header */
.titulo-principal{
  margin-top:88px;
  margin-bottom:4px;
  color:var(--ink);
  font-weight:700;
  letter-spacing:.01em;
  font-size:clamp(1.6rem, 2vw + 1rem, 2.1rem);
}
.subtitulo{
  color:var(--muted);
  font-size:.95rem;
}

/* Top progress */
.top-progress{
  position:sticky;
  top:56px;
  height:3px;
  width:100%;
  z-index:20;
}
.top-progress .bar{
  width:0%;
  height:100%;
  background:linear-gradient(90deg,var(--brand-ink),#93c5fd);
  transition:width .28s var(--ease);
}

/* Toolbar */
.filter-toolbar{
  position: sticky;
  top:60px;
  z-index: 11;

  display:flex;
  align-items:center;
  gap:12px;
  flex-wrap:wrap;

  padding:10px 14px;
  border-radius:999px;
  overflow:hidden;

  backdrop-filter: blur(4px) saturate(174%);
  -webkit-backdrop-filter: blur(4px) saturate(174%);
  background-color: rgba(255, 255, 255, 0.47);
  border: 1px solid rgba(209, 213, 219, 0.3);
  box-shadow: 0 18px 40px rgba(15,23,42,0.25);
}
.filter-toolbar::before{
  content:"";
  position:absolute;
  inset:0;
  pointer-events:none;
  border-radius:inherit;
  opacity:0.18;
  background-image:
    radial-gradient(circle at 0 0, rgba(255,255,255,0.6) 0, transparent 55%),
    radial-gradient(circle at 100% 0, rgba(15,23,42,0.35) 0, transparent 55%),
    radial-gradient(circle at 0 100%, rgba(15,23,42,0.28) 0, transparent 55%),
    radial-gradient(circle at 100% 100%, rgba(255,255,255,0.5) 0, transparent 55%);
  mix-blend-mode: soft-light;
}
.filter-toolbar > *{ position:relative; z-index:1; }

.segmented{
  position:relative;
  display:inline-flex;
  gap:0;
  background:#f9fafb;
  border:1px solid rgba(148,163,184,0.5);
  border-radius:999px;
  padding:3px;
  align-items:stretch;
}
.segmented .indicator{
  position:absolute;
  top:3px;
  bottom:3px;
  left:3px;
  width:0;
  background:#ffffff;
  border-radius:999px;
  box-shadow:0 8px 20px rgba(15,23,42,.12);
  transition: transform .22s var(--ease), width .22s var(--ease);
  z-index:0;
  pointer-events:none;
}
.segmented button{
  position:relative;
  z-index:1;
  border:0;
  background:transparent;
  padding:7px 14px;
  font-weight:600;
  color:#64748b;
  border-radius:999px;
  cursor:pointer;
  transition: color .15s ease;
  font-size:.85rem;
}
.segmented button.active{ color:var(--brand-ink); }

.search-chip{
  display:flex;
  align-items:center;
  gap:8px;
  background:#f9fafb;
  border:1px solid rgba(148,163,184,0.5);
  border-radius:999px;
  padding:6px 10px;
  min-width:240px;
  flex:1;
}
.search-chip input{
  border:0;
  outline:0;
  width:100%;
  font-weight:500;
  font-size:.9rem;
  color:var(--ink);
  background:transparent;
}
.search-chip input::placeholder{ color:#9ca3af; }
.search-chip .clear-btn{
  border:0;
  background:transparent;
  border-radius:999px;
  padding:4px 8px;
  font-weight:700;
  line-height:1;
  color:#9ca3af;
  font-size:.9rem;
}

.select-campo{
  background:#f9fafb;
  border:1px solid rgba(148,163,184,0.5);
  border-radius:999px;
  padding:6px 12px;
  font-weight:600;
  color:var(--ink);
  font-size:.85rem;
}

.summary-badges .badge{
  font-weight:600;
  padding:.35rem .7rem;
  border-radius:999px;
  font-size:.75rem;
}

.btn-filter{
  position: sticky;
  top: 70px;
  z-index: 12;
  width:100%;
  border-radius:999px;
  border:1px solid rgba(148,163,184,0.4);
  background:#ffffff;
  color:var(--ink);
  font-weight:600;
  padding:10px 14px;
  box-shadow:0 14px 36px rgba(15,23,42,.10);
  font-size:.9rem;
}

.sheet-overlay{
  position:fixed;
  inset:0;
  background:rgba(15,23,42,.35);
  backdrop-filter:blur(4px);
  opacity:0;
  pointer-events:none;
  transition:.22s var(--ease);
}
.filter-sheet{
  position:fixed;
  left:0;
  right:0;
  bottom:-100%;
  background:#ffffff;
  border-top-left-radius:22px;
  border-top-right-radius:22px;
  box-shadow:0 -30px 60px rgba(15,23,42,.22);
  padding:18px 16px 20px;
  z-index:1050;
  transition:bottom .34s var(--ease);
}
.filter-sheet.open{ bottom:0; }
.sheet-overlay.open{ opacity:1; pointer-events:all; }
.sheet-handle{
  width:52px;
  height:4px;
  background:#e5e7eb;
  border-radius:999px;
  margin:6px auto 10px;
}
.sheet-title{ font-weight:600; color:var(--ink); }

.pill{
  border:1px solid #e5e7eb;
  padding:7px 11px;
  border-radius:999px;
  background:#f9fafb;
  font-weight:500;
  cursor:pointer;
  font-size:.85rem;
}
.pill input{ display:none; }
.pill.active{
  background:#eef2ff;
  color:var(--brand-ink);
  border-color:#c7d2fe;
}

.input-chip{
  display:flex;
  align-items:center;
  gap:8px;
  background:#f9fafb;
  border:1px solid #e5e7eb;
  border-radius:999px;
  padding:9px 12px;
}
.input-chip input{
  border:0;
  outline:0;
  width:100%;
  font-weight:500;
  color:var(--ink);
  background:transparent;
  font-size:.9rem;
}
.input-chip input::placeholder{ color:#9ca3af; }

.btn-ghost{
  background:#ffffff;
  border:1px solid #e5e7eb;
  color:var(--ink);
  font-weight:500;
  border-radius:999px;
  padding:9px 13px;
  font-size:.9rem;
}
.btn-apply{
  background:#1d4ed8;
  color:#ffffff;
  border:1px solid #1d4ed8;
  font-weight:600;
  border-radius:999px;
  padding:9px 13px;
  font-size:.9rem;
  box-shadow:0 10px 24px rgba(37,99,235,.25);
  transition: transform .08s ease, box-shadow .18s ease;
}
.btn-apply:active{
  transform: scale(.98);
  box-shadow:0 7px 16px rgba(37,99,235,.30);
}

.fin-banner{
  border-radius:18px;
  padding:10px 14px;
  display:flex;
  gap:10px;
  align-items:flex-start;
  border:1px solid var(--line);
  background:#ffffff;
}
.fin-banner--proximos{
  margin-top:84px;
  border-color:#fbbf24;
  background:#fffbeb;
}
.fin-banner--vencidos{
  margin-top:10px;
  border-color:#fecaca;
  background:#fef2f2;
}
.fin-banner-title{ font-weight:600; color:var(--ink); }
.fin-banner-text{ font-size:.9rem; color:var(--muted); }

.card-venta{
  background:var(--card);
  border:1px solid transparent;
  border-radius:var(--radius);
  box-shadow: var(--shadow);
  padding:14px 14px 12px;
  transform: translateY(8px);
  opacity:0;
  transition:
    transform .4s var(--ease),
    opacity .4s var(--ease),
    box-shadow .18s ease,
    background-color .18s ease;
}
.card-venta.revealed{ transform: translateY(0); opacity:1; }
.card-venta:hover{
  box-shadow: 0 24px 60px rgba(15,23,42,.18);
  transform: translateY(-2px);
}
.card-venta-header{
  border-bottom:1px dashed #e5e7eb;
  padding-bottom:8px;
  display:flex;
  justify-content:space-between;
  align-items:center;
}
.cliente-nombre{
  font-weight:600;
  color:var(--ink);
  font-size:.98rem;
}
.venta-id{
  font-size:.8rem;
  color:var(--muted);
}
.estado-liquidada,
.estado-pendiente,
.estado-atrasada{
  border-radius:999px;
  padding:.25rem .6rem;
  font-size:.75rem;
  border:1px solid transparent;
}
.estado-liquidada{ background:var(--ok); color:var(--ok-ink); border-color:#bbf7d0; }
.estado-pendiente{ background:var(--warn); color:var(--warn-ink); border-color:#fde68a; }
.estado-atrasada{ background:var(--danger); color:var(--danger-ink); border-color:#fecaca; }

.detalle-venta p{
  margin-bottom:4px;
  font-size:.88rem;
  color:var(--muted);
}
.detalle-venta strong{ color:var(--ink); font-weight:500; }
.restante{ color:var(--danger-ink); font-weight:600; }

.pill-atraso{
  display:inline-flex;
  align-items:center;
  gap:6px;
  font-weight:600;
  padding:4px 10px;
  border-radius:999px;
  background:#fef2f2;
  color:#b91c1c;
  border:1px solid #fecaca;
  font-size:.8rem;
}
.detalle-venta ul{ padding-left:1rem; }
.detalle-venta li{ font-size:.83rem; color:#6b7280; }

.no-ventas{
  background:#ffffff;
  border:1px dashed #e2e8f0;
  color:var(--muted);
  padding:16px;
  border-radius:14px;
  text-align:center;
  font-size:.9rem;
}
.badge{ font-weight:600; }
.hidden{ display:none !important; }
.hl{
  background:linear-gradient(180deg,transparent 60%,rgba(37,99,235,.18) 0);
  border-radius:3px;
}
:focus-visible{
  outline:2px solid rgba(37,99,235,.45);
  outline-offset:2px;
  border-radius:10px;
}
@media (prefers-reduced-motion:reduce){
  .card-venta, .filter-sheet, .segmented .indicator{ transition:none !important; }
}
.is-filtering #ventasGrid{ opacity:.88; transition:opacity .2s; }
.toast-results{
  position:fixed;
  left:50%;
  transform:translateX(-50%);
  bottom:20px;
  z-index:9999;
  background:#ffffff;
  color:var(--ink);
  border:1px solid #e5e7eb;
  border-radius:999px;
  padding:7px 13px;
  box-shadow:var(--shadow);
  font-weight:500;
  display:none;
  font-size:.8rem;
}

/* Botones suaves */
.btn-soft-info{
  background:#eff6ff;
  color:#1d4ed8;
  border-radius:999px;
  border:1px solid #dbeafe;
  padding:7px 13px;
  font-weight:500;
  font-size:.85rem;
}
.btn-soft-info:hover{ background:#dbeafe; color:#1d4ed8; }

.btn-soft-success{
  background:#ecfdf3;
  color:#15803d;
  border-radius:999px;
  border:1px solid #bbf7d0;
  padding:7px 13px;
  font-weight:500;
  font-size:.85rem;
}
.btn-soft-success:hover{ background:#bbf7d0; }

.btn-send{
  position: relative;
  overflow: hidden;
  border: 1px solid #e5e7eb;
  border-radius: 999px;
  padding: 7px 13px;
  font-weight: 600;
  background: #ffffff;
  color: var(--brand-ink);
  box-shadow: 0 8px 18px rgba(15,23,42,.10);
  display: inline-flex;
  align-items: center;
  gap: 7px;
  font-size:.83rem;
}
.btn-send .spinner{
  width:16px;
  height:16px;
  border:3px solid #e5e7eb;
  border-top-color:#3b82f6;
  border-radius:50%;
  animation: spin .8s linear infinite;
  display:none;
}
.btn-send.sending .spinner{ display:inline-block; }
.btn-send .check{ width:18px; height:18px; display:none; }
.btn-send.sent .check{ display:inline-block; animation: pop .28s var(--ease); }
.btn-send.sent{
  background:#ecfdf3;
  color:#15803d;
  border-color:#bbf7d0;
  box-shadow:0 10px 24px rgba(22,163,74,.18);
}
@keyframes spin{ to { transform: rotate(360deg);} }
@keyframes pop{ 0%{ transform: scale(.6);} 100%{ transform: scale(1);} }

/* Acciones */
.card-actions{
  display:flex;
  gap:10px;
  flex-wrap:wrap;
}
.card-actions .btn,
.card-actions .btn-send{
  flex: 1 1 140px;
  justify-content:center;
  text-align:center;
  white-space:nowrap;
}
.card-actions .badge{
  flex: 0 0 auto;
  white-space:nowrap;
}

@media (max-width: 767.98px){
  .filter-toolbar{ border-radius:16px; }
  .titulo-principal{ margin-top:80px; }
}
</style>

<div class="top-progress"><div class="bar" id="topBar" ></div></div>

<div class="container py-3" >
  {{-- ===== Toolbar Desktop ===== --}}
  <div class="filter-toolbar d-none d-md-flex mb-3" >
    <div class="segmented" role="tablist" aria-label="Filtro de estado" id="segmented" style="margin-top:10px;">
      <div class="indicator" aria-hidden="true"></div>
      <button type="button" class="seg-btn active" data-filter="all" aria-pressed="true">Todas</button>
      <button type="button" class="seg-btn" data-filter="pendiente">Pendientes ({{ $metrics['pendientes'] }})</button>
      <button type="button" class="seg-btn" data-filter="atrasada">Atrasadas ({{ $metrics['atrasadas'] }})</button>
      <button type="button" class="seg-btn" data-filter="liquidada">Liquidadas ({{ $metrics['liquidadas'] }})</button>
    </div>

    <div class="search-chip" title="Buscar por nombre, #venta o cantidad">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
        <path d="M21 21l-4.35-4.35M10 18a8 8 0 1 1 0-16 8 8 0 0 1 0 16Z" stroke="#64748b" stroke-width="2" stroke-linecap="round"/>
      </svg>
      <input id="txtSearchDesktop" type="text">
      <button class="clear-btn" id="btnClearSearchDesktop" title="Limpiar">×</button>
    </div>

    <select class="select-campo" id="selCampoDesktop" aria-label="Campo de cantidad">
      <option value="restante" selected>Restante</option>
      <option value="total">Total</option>
      <option value="pagado">Pagado</option>
    </select>

    <div class="ms-auto d-flex gap-2 align-items-center summary-badges">
      <span class="badge" style="background:#eff6ff;color:#1d4ed8;">Ventas: {{ $metrics['total'] }}</span>
      <span class="badge" style="background:#dcfce7;color:#166534;">Liquidadas: {{ $metrics['liquidadas'] }}</span>
      <span class="badge" style="background:#fef3c7;color:#92400e;">Pendientes: {{ $metrics['pendientes'] }}</span>
      <span class="badge" style="background:#fee2e2;color:#b91c1c;">Atrasadas: {{ $metrics['atrasadas'] }}</span>
      <span class="badge" style="background:#e5e7eb;color:#111827;">Saldo: ${{ number_format($metrics['saldo'],2) }}</span>
    </div>
  </div>

  {{-- ===== Botón filtros: Móvil ===== --}}
  <button class="btn-filter d-md-none mb-3" id="openFilterSheet" aria-haspopup="dialog" aria-controls="filterSheet">
    Filtros y búsqueda
  </button>

  <div class="sheet-overlay" id="sheetOverlay" aria-hidden="true"></div>
  <div class="filter-sheet d-md-none" id="filterSheet" role="dialog" aria-modal="true" aria-labelledby="sheetTitle">
    <div class="sheet-handle"></div>
    <h6 class="sheet-title mb-3" id="sheetTitle">Filtrar financiamientos</h6>

    {{-- Estado (radios) --}}
    <div class="mb-3">
      <label class="form-label small text-muted">Estado</label>
      <div class="d-flex gap-2 pill-group" id="estadoMob">
        <label class="pill active"><input type="radio" name="estadoMob" value="all" checked> Todas</label>
        <label class="pill"><input type="radio" name="estadoMob" value="pendiente"> Pendientes</label>
        <label class="pill"><input type="radio" name="estadoMob" value="atrasada"> Atrasadas</label>
        <label class="pill"><input type="radio" name="estadoMob" value="liquidada"> Liquidadas</label>
      </div>
    </div>

    <div class="mb-3">
      <label class="form-label small text-muted">Buscar</label>
      <div class="input-chip">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
          <path d="M21 21l-4.35-4.35M10 18a8 8 0 1 1 0-16 8 8 0 0 1 0 16Z" stroke="#64748b" stroke-width="2" stroke-linecap="round"/>
        </svg>
        <input type="text" id="txtSearchMobile" placeholder="Nombre, #venta o cantidad… (>, <, 800-1200)">
      </div>
    </div>

    <div class="mb-3">
      <label class="form-label small text-muted">Buscar cantidad en</label>
      <div class="d-flex gap-2 pill-group">
        <label class="pill active"><input type="radio" name="campoMob" value="restante" checked> Restante</label>
        <label class="pill"><input type="radio" name="campoMob" value="total"> Total</label>
        <label class="pill"><input type="radio" name="campoMob" value="pagado"> Pagado</label>
      </div>
    </div>

    <div class="d-flex gap-2">
      <button class="btn-ghost w-50" id="btnClear">Limpiar</button>
      <button class="btn-apply w-50" id="btnApply">Aplicar</button>
    </div>
  </div>

  <h1 class="titulo-principal">Financiamientos</h1>
  <div class="subtitulo mb-2">
    Control de pagos, atrasos y saldo global.
  </div>

  <div id="noResults" class="no-ventas d-none mt-2">No hay resultados con los filtros aplicados.</div>
  <div id="resultToast" class="toast-results"></div>

  @if($ventas->isEmpty())
    <div class="no-ventas mt-3">No hay ventas registradas.</div>
  @else
    <div id="ventasGrid" class="row g-4 mt-1">
      @foreach($ventas as $venta)
        @php
          // ---------- PLAN DE PAGOS ----------
          $pagosPlan       = $venta->pagosFinanciamiento ?? collect();
          $pagosPendientes = $pagosPlan->where('pagado', false) ?? collect();
          $pagosPagados    = $pagosPlan->where('pagado', true) ?? collect();

          $pagoInicial     = extraerPagoInicialDePagos($pagosPagados);
          $totalPagadoPlan = (float) $pagosPagados->sum('monto');

          // ---------- TOTALES ORIGINAL / NETO ----------
          $totalOriginal   = (float) ($venta->total_original ?? $venta->total ?? 0);
          $totalNeto       = (float) ($venta->total_neto ?? $totalOriginal);

          // ---------- PAGOS (ANTICIPO / TRADE-IN) ----------
          $pagosVenta      = \App\Models\Pago::where('venta_id', $venta->id)->get();

          // Anticipo por flag es_anticipo
          $montoAnticipo   = (float) $pagosVenta->filter(function($p){
                                return (bool) ($p->es_anticipo ?? false);
                             })->sum('monto');

          // Trade-in por método de pago
          $montoTradeIn    = (float) $pagosVenta->filter(function($p){
                                $metodo = strtolower(trim(
                                    $p->metodo
                                    ?? $p->metodo_pago
                                    ?? $p->forma_pago
                                    ?? ''
                                ));
                                return in_array($metodo, ['trade-in','trade in','tradein']);
                             })->sum('monto');

          // ---------- PAGADO / RESTANTE (GLOBAL) ----------
          $totalPagadoGlobal = $montoTradeIn + $montoAnticipo + $totalPagadoPlan;
          $restanteGlobal    = max(0, $totalOriginal - $totalPagadoGlobal);

          // (plan) solo informativo
          $restantePlan      = max(0, $totalNeto - $totalPagadoPlan);

          $fechasPago        = extraerFechasDePagos($pagosPendientes);
          $pagoHoy           = collect($fechasPago)->contains(fn($f) => $f->isToday());

          $vencidoMasAntiguo = obtenerPagoVencidoMasAntiguo($pagosPendientes);
          $diasAtraso        = $vencidoMasAntiguo ? (int) $vencidoMasAntiguo->diffInDays(Carbon::today()) : 0;

          // ✅ CORREGIDO: ESTADO DEPENDE DEL RESTANTE GLOBAL
          if ($restanteGlobal <= 0.01)        $estado = 'liquidada';
          elseif ($vencidoMasAntiguo)         $estado = 'atrasada';
          else                                $estado = 'pendiente';

          $clienteNombre   = trim((optional($venta->cliente)->nombre.' '.optional($venta->cliente)->apellido) ?? '');

          // El pago objetivo (vencido más antiguo o el de hoy)
          $pagoObjetivo = $pagosPendientes->first(function($p) use($vencidoMasAntiguo, $pagoHoy) {
              try {
                  if ($vencidoMasAntiguo) return $p->fecha_pago && Carbon::parse($p->fecha_pago)->isSameDay($vencidoMasAntiguo);
                  if ($pagoHoy)           return $p->fecha_pago && Carbon::parse($p->fecha_pago)->isToday();
              } catch (\Throwable $e) {}
              return false;
          });

          $yaNotificado = $pagoObjetivo ? yaNotificadoHoy($pagoObjetivo->id) : false;
        @endphp

        <div class="col-md-6 col-lg-4 venta-item"
             data-estado="{{ $estado }}"
             data-id="{{ $venta->id }}"
             data-cliente="{{ Str::of($clienteNombre)->lower() }}"
             data-total="{{ (float)$totalOriginal }}"
             data-pagado="{{ (float)$totalPagadoGlobal }}"
             data-restante="{{ (float)$restanteGlobal }}"
             data-atraso="{{ (int)$diasAtraso }}">
          <div class="card-venta h-100">
            <div class="card-venta-header">
              <div>
                <div class="cliente-nombre js-name">{{ $clienteNombre }}</div>
                <div class="venta-id js-id">Remisión: 2025-{{ $venta->id }}</div>
              </div>
              @if($estado === 'liquidada')
                <span class="estado-liquidada badge">Liquidada</span>
              @elseif($estado === 'atrasada')
                <span class="estado-atrasada badge">Atrasada</span>
              @else
                <span class="estado-pendiente badge">Pendiente</span>
              @endif
            </div>

            <div class="detalle-venta mt-3">
              @if($estado === 'atrasada')
                <div class="d-flex align-items-center mb-2">
                  <span class="pill-atraso" title="Días de atraso">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#b91c1c" viewBox="0 0 24 24">
                      <path d="M1 21h22L12 2 1 21zM13 17h-2v-2h2v2zm0-4h-2V9h2v4z"/>
                    </svg>
                    @if($diasAtraso === 1)
                      1 día de atraso
                    @else
                      {{ $diasAtraso }} días de atraso
                    @endif
                  </span>
                </div>
              @elseif($pagoHoy && $restanteGlobal > 0.01)
                <div class="alert d-flex align-items-center mb-3 p-2 px-3" style="border-radius:12px;border:1px solid #fecaca;background:#fef2f2;color:var(--danger-ink);font-size:.8rem;">
                  <svg xmlns="http://www.w3.org/2000/svg" class="me-2" width="18" height="18" fill="#dc2626" viewBox="0 0 24 24">
                    <path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z"/>
                  </svg>
                  <strong class="me-1">Hoy hay un pago de este plan.</strong>
                </div>
              @endif

              <p><strong>Emitido:</strong> {{ Carbon::parse($venta->created_at)->format('d/m/Y') }}</p>
              <p><strong>Total venta:</strong> ${{ number_format($totalOriginal, 2) }}</p>

              @if($montoTradeIn > 0)
                <p><strong>Trade-in aplicado:</strong> -${{ number_format($montoTradeIn, 2) }}</p>
              @endif

              @if($montoAnticipo > 0)
                <p><strong>Anticipo:</strong> -${{ number_format($montoAnticipo, 2) }}</p>
              @endif

              @if(abs($totalNeto - $totalOriginal) > 0.01)
                <p><strong>Total neto (a financiar):</strong> ${{ number_format($totalNeto, 2) }}</p>
              @endif

              @if($pagoInicial > 0)
                <p><strong>Pago inicial del plan:</strong> ${{ number_format($pagoInicial, 2) }}</p>
              @endif

              <p>
                <strong>Total pagado (trade-in + anticipo + plan):</strong>
                ${{ number_format($totalPagadoGlobal, 2) }}
              </p>

              <p>
                <strong>Restante por pagar:</strong>
                <span class="restante js-restante">${{ number_format($restanteGlobal, 2) }}</span>
              </p>

              @if(count($fechasPago))
                <p class="mt-2 mb-1"><strong>Fechas de pagos pendientes del plan:</strong></p>
                <ul class="mb-0">
                  @foreach($fechasPago as $f)
                    <li>{{ $f->isoFormat('DD [de] MMMM [de] YYYY') }}</li>
                  @endforeach
                </ul>
              @endif
            </div>

            {{-- Acciones --}}
            <div class="card-actions mt-3 px-1">
              <a href="{{ route('ventas.pagos.index', $venta->id) }}" class="btn btn-soft-info">
                Ver pagos
              </a>

              <a href="{{ route('ventas.show', $venta->id) }}" class="btn btn-soft-success">
                Ver remisión
              </a>

              @if($pagoObjetivo && !$yaNotificado)
                <button class="btn-send reenviar-btn" data-pago-id="{{ $pagoObjetivo->id }}">
                  <span class="spinner" aria-hidden="true"></span>
                  <svg class="check" viewBox="0 0 24 24" fill="none">
                    <path d="M20 6L9 17l-5-5" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                  </svg>
                  <span class="label">{{ $estado === 'atrasada' ? 'Avisar por WhatsApp' : 'Notificar' }}</span>
                </button>
              @elseif($pagoObjetivo && $yaNotificado)
                <span class="badge rounded-pill bg-secondary align-self-center" style="font-size:.78rem;">
                  Notificado hoy
                </span>
              @endif
            </div>
          </div>
        </div>
      @endforeach
    </div>
  @endif
</div>

<script>
(function(){
  const $$=(s,c=document)=>Array.from(c.querySelectorAll(s));
  const $ =(s,c=document)=>c.querySelector(s);
  const grid=$('#ventasGrid'), noResults=$('#noResults'), toast=$('#resultToast'), topBar=$('#topBar');
  const segmented=$('#segmented'); const indicator=segmented?.querySelector('.indicator');

  // === Select de vencidos (si existe en tu layout)
  const selVencidosVis = document.getElementById('selVencidosVis');
  const vencidosList   = document.getElementById('vencidosList');
  const vencidosRes    = document.getElementById('vencidosResumen');

  let estadoMode='all', debounceTimer;

  /* ===== Helpers ===== */
  const norm = s => (s||'').toString().toLowerCase().normalize('NFD').replace(/\p{Diacritic}/gu,'');
  function parseNumericQuery(q){
    if(!q) return null; const s=q.replace(/\s+/g,'').replace(',','.');
    if(/^(\d+(\.\d+)?)\-(\d+(\.\d+)?)$/.test(s)){
      const [a,b]=s.split('-').map(parseFloat);
      return {type:'range',min:Math.min(a,b),max:Math.max(a,b)};
    }
    const m=s.match(/^([<>]=?|=)(\d+(\.\d+)?)$/);
    if(m){ return {type:m[1],value:parseFloat(m[2])}; }
    if(/^\d+(\.\d+)?$/.test(s)){ return {type:'eq-soft',value:parseFloat(s)}; }
    return null;
  }
  function matchNumeric(val,nq){
    if(nq==null) return true;
    const v=Number(val)||0;
    switch(nq.type){
      case 'range':return v>=nq.min&&v<=nq.max;
      case '>':return v>nq.value;
      case '>=':return v>=nq.value;
      case '<':return v<nq.value;
      case '<=':return v<=nq.value;
      case '=':return v===nq.value;
      case 'eq-soft':return (''+v).includes((''+nq.value));
      default:return true;
    }
  }
  function getCampo(){
    const d=$('#selCampoDesktop');
    if (window.matchMedia('(min-width:768px)').matches && d) return d.value||'restante';
    const r=document.querySelector('input[name="campoMob"]:checked');
    return r? r.value : 'restante';
  }
  function showToast(msg){
    if(!toast) return;
    toast.textContent=msg;
    toast.style.display='block';
    clearTimeout(toast._tmr);
    toast._tmr=setTimeout(()=>toast.style.display='none',1400);
  }
  function setFiltering(on){
    document.body.classList.toggle('is-filtering',on);
    if(topBar){ topBar.style.width=on?'70%':'0%'; }
  }

  function clearHighlights(card){
    const n=card.querySelector('.js-name'), i=card.querySelector('.js-id');
    if(n) n.innerHTML=n.textContent;
    if(i) i.innerHTML=i.textContent;
  }
  function applyHighlights(card,q){
    if(!q) return;
    const qn=norm(q);
    const n=card.querySelector('.js-name'), i=card.querySelector('.js-id');
    function paint(el){
      const t=el.textContent;
      const idx=norm(t).indexOf(qn);
      if(idx>=0){
        el.innerHTML=t.slice(0,idx)+'<mark class="hl">'+t.slice(idx,idx+q.length)+'</mark>'+t.slice(idx+q.length);
      }
    }
    if(n) paint(n);
    if(i) paint(i);
  }

  function updateIndicator(){
    if(!segmented || !indicator) return;
    const active=segmented.querySelector('button.active') || segmented.querySelector('button');
    if(!active) return;
    const {offsetLeft,offsetWidth}=active;
    indicator.style.width = (offsetWidth - 8) + 'px';
    indicator.style.transform = `translateX(${offsetLeft - 4}px)`;
  }
  window.addEventListener('resize', updateIndicator);

  function applyAllFilters(){
    if(!grid) return;
    setFiltering(true);
    const items = $$('.venta-item', grid);
    const qDesktop = $('#txtSearchDesktop')?.value || '';
    const qMobile  = $('#txtSearchMobile')?.value || '';
    const q = (window.matchMedia('(min-width:768px)').matches ? qDesktop : qMobile) || '';
    const campo = getCampo();
    const nq = parseNumericQuery(q);
    let visible=0;

    items.forEach(it=>{
      clearHighlights(it);
      const st=it.dataset.estado;
      const cid=(it.dataset.id||'').toString();
      const cli=it.dataset.cliente||'';
      const total=it.dataset.total;
      const pagado=it.dataset.pagado;
      const restante=it.dataset.restante;

      const passEstado=(estadoMode==='all')||(estadoMode===st);
      const passTexto = !q ? true : ( cli.includes(norm(q)) || cid.includes(q) );
      let passNumero=true;

      if(nq){
        const val = campo==='total'?total:(campo==='pagado'?pagado:restante);
        passNumero=matchNumeric(val,nq);
      } else if(q && /\d/.test(q)){
        const direct=[total,pagado,restante].some(v=>(''+Number(v||0)).includes(q.replace(/\s+/g,'')));
        passNumero=direct||passTexto;
      }

      const show=passEstado && passTexto && passNumero;
      it.classList.toggle('hidden',!show);
      if(show){ visible++; applyHighlights(it,q); }
    });

    if(noResults) noResults.classList.toggle('d-none', visible>0);
    showToast(visible + ' resultado' + (visible===1?'':'s'));
    if(topBar){
      topBar.style.width='100%';
      setTimeout(()=> setFiltering(false),220);
    }
  }
  function debouncedFilter(){
    clearTimeout(debounceTimer);
    setFiltering(true);
    if(topBar) topBar.style.width='45%';
    debounceTimer=setTimeout(applyAllFilters,200);
  }

  $$('.seg-btn').forEach(btn=>{
    btn.addEventListener('click', ()=>{
      $$('.seg-btn').forEach(b=>b.classList.remove('active'));
      btn.classList.add('active');
      estadoMode=btn.dataset.filter;
      updateIndicator();
      debouncedFilter();
    });
  });
  updateIndicator();

  $('#txtSearchDesktop')?.addEventListener('input', debouncedFilter);
  $('#btnClearSearchDesktop')?.addEventListener('click', ()=>{
    const t=$('#txtSearchDesktop');
    if(!t) return;
    t.value='';
    debouncedFilter();
  });
  $('#selCampoDesktop')?.addEventListener('change', debouncedFilter);

  const sheet=$('#filterSheet'), overlay=$('#sheetOverlay');
  const openBtn=$('#openFilterSheet'); const txtMob=$('#txtSearchMobile');

  function openSheet(){ sheet?.classList.add('open'); overlay?.classList.add('open'); }
  function closeSheet(){ sheet?.classList.remove('open'); overlay?.classList.remove('open'); }

  openBtn?.addEventListener('click', openSheet);
  overlay?.addEventListener('click', closeSheet);

  $$('#estadoMob .pill').forEach(p=>{
    p.addEventListener('click', ()=>{
      $$('#estadoMob .pill').forEach(x=>x.classList.remove('active'));
      p.classList.add('active');
      p.querySelector('input').checked=true;
    });
  });

  $('#btnClear')?.addEventListener('click', ()=>{
    const all=$('#estadoMob .pill input[value="all"]')?.closest('.pill');
    $$('#estadoMob .pill').forEach(x=>x.classList.remove('active'));
    if (all){
      all.classList.add('active');
      all.querySelector('input').checked=true;
    }

    const rest=document.querySelector('input[name="campoMob"][value="restante"]');
    if(rest){ rest.checked=true; }
    if(txtMob) txtMob.value='';
    document.querySelectorAll('.pill-group input[name="campoMob"]').forEach(inp=>{
      const lab = inp.closest('.pill');
      if (lab) lab.classList.toggle('active', inp.value==='restante');
    });
  });

  $('#btnApply')?.addEventListener('click', ()=>{
    const estadoSel = document.querySelector('input[name="estadoMob"]:checked')?.value || 'all';
    estadoMode = estadoSel;

    const segBtn=document.querySelector(`.seg-btn[data-filter="${estadoMode}"]`);
    if(segBtn){
      $$('.seg-btn').forEach(b=>b.classList.remove('active'));
      segBtn.classList.add('active');
      updateIndicator();
    }

    const checkedMob=document.querySelector('input[name="campoMob"]:checked')?.value||'restante';
    const selDesk=$('#selCampoDesktop'); if(selDesk) selDesk.value=checkedMob;

    const txtDesk=$('#txtSearchDesktop'); if(txtDesk && txtMob) txtDesk.value=txtMob.value;

    debouncedFilter();
    closeSheet();
  });

  const io=new IntersectionObserver((entries)=>{
    entries.forEach(e=>{
      if(e.isIntersecting){
        e.target.classList.add('revealed');
        io.unobserve(e.target);
      }
    });
  },{rootMargin:'0px 0px -10% 0px',threshold:.15});
  $$('.card-venta').forEach(c=>io.observe(c));

  function sendNotify(btn){
    const pagoId = btn.dataset.pagoId;
    const csrf   = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if(!pagoId || !csrf) return;

    btn.disabled = true;
    btn.classList.add('sending');
    btn.querySelector('.label').textContent = 'Enviando...';

    fetch(`/financiamientos/notificar/${pagoId}`,{
      method:'POST',
      headers:{'X-CSRF-TOKEN':csrf,'Accept':'application/json'}
    })
    .then(r=>{
      if(r.ok){
        btn.classList.remove('sending');
        btn.classList.add('sent');
        btn.querySelector('.label').textContent = 'Enviado';
        setTimeout(()=>{
          btn.replaceWith(Object.assign(document.createElement('span'), {
            className:'badge rounded-pill bg-secondary',
            textContent:'Notificado hoy'
          }));
        }, 450);
      }else{
        throw new Error('Solicitud fallida');
      }
    })
    .catch(()=>{
      btn.classList.remove('sending');
      btn.disabled = false;
      btn.querySelector('.label').textContent = 'Error, reintentar';
    });
  }
  document.querySelectorAll('.reenviar-btn').forEach(button=>{
    button.addEventListener('click', function(){ sendNotify(this); });
  });

  debouncedFilter();

  (function initVencidosSelect(){
    if (!selVencidosVis || !vencidosList) return;
    const KEY = 'fin_vencidos_vis';
    const saved = localStorage.getItem(KEY) || 'resumen';
    selVencidosVis.value = saved;

    function applyView(v){
      const isLista = (v === 'lista');
      if (vencidosList) vencidosList.style.display = isLista ? '' : 'none';
      if (vencidosRes)  vencidosRes.style.display  = isLista ? 'none' : '';
      localStorage.setItem(KEY, v);
    }
    selVencidosVis.addEventListener('change', e => applyView(e.target.value));
    applyView(saved);
  })();
})();
</script>
@endsection
