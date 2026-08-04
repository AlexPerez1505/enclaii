@extends('layouts.app')
@section('title', 'Financiamientos')
@section('titulo', 'Financiamientos')

@section('content')
@include('partials.submenu-cotizaciones')
<div class="submenu-page-spacer" aria-hidden="true"></div>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/deudores.css') }}?v={{ time() }}">

<style>
/* ─── RESET DE MÁRGENES DEL LAYOUT ─────────────────────────── */
@media (min-width: 768px) {
  .container.py-3 {
    margin-left: calc(88px + 24px) !important;
    max-width: calc(100% - 88px - 32px) !important;
    overflow: visible !important;
  }
}

/* ─── TOOLBAR STICKY FIJO ───────────────────────────────────── */
/* ─────────────────────────────────────────────
   TOOLBAR / HERO / FILTROS STICKY
───────────────────────────────────────────── */

.filter-toolbar-sticky{
  position: sticky;
  top: 72px; /* altura del navbar superior */
  z-index: 1035;

  background: rgba(255,255,255,.88);
  backdrop-filter: blur(18px);

  border: 1px solid rgba(226,232,240,.9);
  border-radius: 20px;

  padding: 14px 18px;
  margin-bottom: 18px;

  box-shadow:
    0 10px 30px rgba(15,23,42,.08),
    0 2px 8px rgba(15,23,42,.05);
}

/* Contenedor interno */
.filter-toolbar-wrap{
  width: 100%;
  overflow: visible !important;
}

/* Toolbar */
.filter-toolbar{
  display: flex;
  align-items: center;
  gap: 12px;
  flex-wrap: nowrap;

  overflow-x: auto;
  scrollbar-width: none;
}

.filter-toolbar::-webkit-scrollbar{
  display: none;
}

/* Buscador fijo visualmente */
.search-chip{
  position: relative;
  flex-shrink: 0;
}

/* Evita que dropdowns se corten */
.filter-toolbar-sticky,
.filter-toolbar-wrap,
.filter-toolbar,
.dropdown,
.dropdown-menu{
  overflow: visible !important;
}

/* Espacio para que el contenido no se pegue */
.fin-content-spacer{
  height: 8px;
}

/* MOBILE */
@media (max-width: 767px){

  .filter-toolbar-sticky{
    top: 64px;
    border-radius: 0;
    margin-left: -16px;
    margin-right: -16px;

    padding: 12px 16px;

    border-left: 0;
    border-right: 0;
  }

}


/* Cápsula interior del toolbar */


/* Espaciado del contenido bajo el toolbar sticky */


.filter-toolbar-title {
  font-size: 1.15rem;
  font-weight: 600;
  color: #0f172a;
  letter-spacing: -.3px;
  line-height: 1;
  white-space: nowrap;
  flex-shrink: 0;
}
.filter-toolbar-divider {
  width: 1px;
  height: 26px;
  background: rgba(148,163,184,.35);
  flex-shrink: 0;
}
.filter-toolbar {
  display: flex;
  flex-wrap: nowrap;   /* sin salto de línea dentro de la cápsula */
  gap: 8px;
  align-items: center;
  overflow: visible !important;
  min-width: 0;
}
.filter-toolbar, .dropdown-filter-wrap, .dropdown { overflow: visible !important; }

/* Dropdown estado */
.dropdown-filter-wrap { position: relative; }
.dropdown-filter-btn {
  border: 1px solid rgba(148,163,184,.28);
  border-radius: 14px;
  padding: 9px 16px;
  font-weight: 400;
  font-size: .82rem;
  color: #1e293b;
  background: rgba(255,255,255,.8);
  backdrop-filter: blur(14px);
  display: flex;
  align-items: center;
  gap: 8px;
  transition: all .2s;
  cursor: pointer;
  white-space: nowrap;
}
.dropdown-filter-btn:hover { background: #fff; border-color: rgba(148,163,184,.5); }
.dropdown-filter-menu {
  position: absolute;
  top: calc(100% + 6px);
  left: 0;
  z-index: 10500;   /* por encima del toolbar sticky (z:1030) y cualquier card */
  border-radius: 16px;
  border: 1px solid rgba(148,163,184,.25);
  box-shadow: 0 12px 32px rgba(15,23,42,.14);
  padding: 8px;
  min-width: 240px;
  background: rgba(255,255,255,.98);
  backdrop-filter: blur(16px);
}
.dropdown-filter-item {
  display: flex !important;
  align-items: center !important;
  gap: 10px !important;
  padding: 9px 12px !important;
  border-radius: 10px;
  transition: background .15s;
  cursor: pointer;
}
.dropdown-filter-item:hover { background: rgba(15,23,42,.06); }
.dropdown-filter-item .form-check-input {
  width: 1.05em; height: 1.05em;
  margin: 0; flex-shrink: 0; cursor: pointer;
}
.dropdown-filter-item .form-check-input:checked { background-color: #10b981; border-color: #10b981; }
.dropdown-filter-item .form-check-label {
  font-size: .84rem; font-weight: 500; color: #1e293b;
  cursor: pointer; line-height: 1.4;
}
.dropdown-divider { margin: .4rem 0; border-top-color: rgba(148,163,184,.3); }

/* Filtro tipo (Todo / Ventas / OS) */
.type-filter-wrap {
  display: flex; gap: 4px; align-items: center;
  background: rgba(255,255,255,.72);
  border: 1px solid rgba(148,163,184,.22);
  border-radius: 14px;
  padding: 4px;
  box-shadow: 0 4px 14px rgba(15,23,42,.06);
  backdrop-filter: blur(14px);
}
.type-btn {
  border: 0; border-radius: 10px; padding: 7px 12px;
  font-weight: 400; font-size: .8rem; color: #475569;
  background: transparent; transition: .18s ease; white-space: nowrap; cursor: pointer;
}
.type-btn:hover { background: rgba(15,23,42,.06); color: #0f172a; }
.type-btn.active {
  color: #0f172a; background: rgba(16,185,129,.18);
  border: 1px solid rgba(16,185,129,.35);
  font-weight: 500; 
}
.type-btn i { margin-right: 4px; }

/* Buscador */
.search-chip {
  flex: 1; min-width: 200px; max-width: 360px;
  display: flex; align-items: center; gap: 8px;
  background: rgba(255,255,255,.8);
  border: 1px solid rgba(148,163,184,.28);
  border-radius: 14px; padding: 8px 14px;
  backdrop-filter: blur(14px);
  transition: border-color .2s;
}
.search-chip:focus-within { border-color: rgba(16,185,129,.5); background: #fff; }
.search-chip input {
  border: 0; outline: 0; background: transparent;
  font-size: .83rem; color: #1e293b; flex: 1; min-width: 0;
}
.search-chip input::placeholder { color: #94a3b8; }
.clear-btn {
  border: 0; background: transparent; color: #94a3b8;
  font-size: 1.1rem; cursor: pointer; padding: 0; line-height: 1;
}
.clear-btn:hover { color: #475569; }

/* Badge resumen */
.summary-badges .badge {
  font-size: .78rem; font-weight: 600;
  border-radius: 10px; padding: 6px 12px;
}

/* ─── GRID ──────────────────────────────────────────────────── */
#ventasGrid { --bs-gutter-x: 1.25rem; --bs-gutter-y: 1.25rem; }

/* ─── CARD ──────────────────────────────────────────────────── */
.card-venta {
  background: #fff;
  border-radius: 18px;
  border: 1px solid rgba(226,232,240,.8);
  box-shadow: 0 2px 12px rgba(15,23,42,.06);
  transition: transform .22s ease, box-shadow .22s ease;
  display: flex; flex-direction: column;
  overflow: hidden;
  opacity: 0; transform: translateY(12px);
  transition: opacity .35s ease, transform .35s ease, box-shadow .22s ease;
}
.card-venta.revealed { opacity: 1; transform: translateY(0); }
.card-venta:hover {
  transform: translateY(-3px);
  box-shadow: 0 8px 28px rgba(15,23,42,.1);
}

.card-venta-header {
  display: flex; align-items: flex-start;
  justify-content: space-between; gap: 10px;
  padding: 16px 18px 0;
}
.cliente-nombre {
  font-size: .95rem; font-weight: 700;
  color: #0f172a; line-height: 1.3;
}
.venta-id {
  font-size: .74rem; color: #64748b; margin-top: 2px;
}

/* ─── PRODUCTO — RECUADRO GRANDE ────────────────────────────── */
.producto-banner {
  width: 100%;
  height: 140px;
  border-radius: 14px;
  overflow: hidden;
  background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
  border: 1px solid rgba(226,232,240,.9);
  margin-bottom: 14px;
  position: relative;
  flex-shrink: 0;
}
.producto-banner img {
  width: 100%; height: 100%; object-fit: cover;
  display: block;
}
.producto-banner-fallback {
  width: 100%; height: 100%;
  display: flex; flex-direction: column;
  align-items: center; justify-content: center;
  gap: 6px;
}
.producto-banner-fallback .letra {
  font-size: 2.8rem; font-weight: 900;
  color: #cbd5e1; line-height: 1;
}
.prod-meta {
  position: absolute; bottom: 0; left: 0; right: 0;
  padding: 8px 12px;
  background: linear-gradient(to top, rgba(15,23,42,.55) 0%, transparent 100%);
  border-radius: 0 0 14px 14px;
}
.prod-name {
  font-size: .82rem; font-weight: 700;
  color: #fff; line-height: 1.3;
  text-shadow: 0 1px 3px rgba(0,0,0,.4);
}
.prod-sub {
  font-size: .72rem; color: rgba(255,255,255,.8); margin-top: 2px;
}

/* ─── DETALLE ───────────────────────────────────────────────── */
.detalle-venta {
  padding: 14px 18px;
  flex: 1;
}
.detalle-venta p {
  font-size: .82rem; color: #475569;
  margin: 0 0 6px; line-height: 1.5;
}
.detalle-venta p strong { color: #1e293b; }
.detalle-venta ul {
  padding-left: 16px; margin: 0;
}
.detalle-venta ul li {
  font-size: .8rem; color: #64748b; margin-bottom: 3px;
}
.restante {
  color: #dc2626; font-weight: 700;
}

/* ─── BADGES ESTADO ─────────────────────────────────────────── */
.badge { font-size: .72rem; font-weight: 700; padding: 4px 10px; border-radius: 8px; white-space: nowrap; }
.estado-liquidada { background: #dcfce7; color: #166534; }
.estado-atrasada  { background: #fee2e2; color: #991b1b; }
.estado-pendiente { background: #fef9c3; color: #854d0e; }

/* ─── PILL ATRASO ───────────────────────────────────────────── */
.pill-atraso {
  display: inline-flex; align-items: center; gap: 5px;
  background: #fef2f2; border: 1px solid #fecaca;
  border-radius: 8px; padding: 5px 10px;
  font-size: .79rem; font-weight: 600; color: #b91c1c;
  margin-bottom: 10px;
}

/* ─── ACCIONES ──────────────────────────────────────────────── */
.card-actions{
  margin-top: auto;

  display: flex;
  flex-wrap: wrap;
  gap: 8px;

  padding: 14px 18px 16px;

  border-top: 1px solid rgba(226,232,240,.7);
}.btn-soft-info {
  font-size: .78rem; font-weight: 600;
  background: rgba(59,130,246,.1); color: #1d4ed8;
  border: 1px solid rgba(59,130,246,.2);
  border-radius: 10px; padding: 6px 14px;
  text-decoration: none; transition: background .2s;
}
.btn-soft-info:hover { background: rgba(59,130,246,.18); color: #1d4ed8; }
.btn-soft-success {
  font-size: .78rem; font-weight: 600;
  background: rgba(16,185,129,.1); color: #065f46;
  border: 1px solid rgba(16,185,129,.2);
  border-radius: 10px; padding: 6px 14px;
  text-decoration: none; transition: background .2s;
}
.btn-soft-success:hover { background: rgba(16,185,129,.18); color: #065f46; }

/* Botón notificar */
.btn-send {
  display: inline-flex; align-items: center; gap: 6px;
  background: #fef2f2; color: #dc2626;
  border: 1px solid #fecaca;
  border-radius: 10px; padding: 6px 14px;
  font-size: .78rem; font-weight: 600; cursor: pointer;
  transition: background .2s;
}
.btn-send:hover { background: #fee2e2; }
.btn-send.sending .label { opacity: .6; }
.btn-send.sent { background: #f0fdf4; color: #166534; border-color: #bbf7d0; }

/* ─── TOAST ─────────────────────────────────────────────────── */
.toast-results {
  position: fixed; bottom: 20px; right: 20px;
  background: #0f172a; color: #fff;
  border-radius: 12px; padding: 10px 18px;
  font-size: .8rem; font-weight: 600;
  display: none; z-index: 9999;
  box-shadow: 0 4px 20px rgba(0,0,0,.2);
}

/* ─── PROGRESS BAR ──────────────────────────────────────────── */
.top-progress {
  position: fixed; top: 0; left: 0; right: 0; height: 3px; z-index: 9999;
  background: transparent; pointer-events: none;
}
.top-progress .bar {
  height: 100%; width: 0;
  background: linear-gradient(90deg, #10b981, #06b6d4);
  transition: width .3s ease;
}

/* ─── MOBILE SHEET ──────────────────────────────────────────── */
.btn-filter {
  width: 100%; background: #fff; border: 1px solid rgba(148,163,184,.3);
  border-radius: 14px; padding: 12px; font-weight: 600; font-size: .85rem;
  color: #1e293b; cursor: pointer;
}
.sheet-overlay {
  display: none; position: fixed; inset: 0;
  background: rgba(15,23,42,.4); z-index: 1040;
}
.sheet-overlay.open { display: block; }
.filter-sheet {
  position: fixed; bottom: -100%; left: 0; right: 0;
  background: #fff; border-radius: 20px 20px 0 0;
  padding: 20px; z-index: 1050;
  box-shadow: 0 -8px 32px rgba(15,23,42,.12);
  transition: bottom .3s ease;
}
.filter-sheet.open { bottom: 0; }
.sheet-handle {
  width: 36px; height: 4px; background: #e2e8f0;
  border-radius: 4px; margin: 0 auto 16px;
}
.sheet-title { font-weight: 700; color: #0f172a; }
.pill-group { flex-wrap: wrap; }
.pill {
  display: flex; align-items: center; gap: 6px;
  background: #f8fafc; border: 1px solid rgba(148,163,184,.3);
  border-radius: 10px; padding: 7px 14px;
  font-size: .82rem; font-weight: 600; color: #475569; cursor: pointer;
}
.pill input { display: none; }
.pill.active { background: rgba(16,185,129,.12); border-color: rgba(16,185,129,.35); color: #065f46; }
.input-chip {
  display: flex; align-items: center; gap: 8px;
  background: #f8fafc; border: 1px solid rgba(148,163,184,.3);
  border-radius: 12px; padding: 10px 14px;
}
.input-chip input { border: 0; outline: 0; background: transparent; font-size: .83rem; width: 100%; }
.btn-ghost {
  background: #f1f5f9; border: 0; border-radius: 12px;
  padding: 10px; font-weight: 600; font-size: .83rem; cursor: pointer;
}
.btn-apply {
  background: #0f172a; color: #fff; border: 0; border-radius: 12px;
  padding: 10px; font-weight: 600; font-size: .83rem; cursor: pointer;
}

/* ─── NO RESULTADOS ─────────────────────────────────────────── */
.no-ventas {
  text-align: center; padding: 48px 24px;
  color: #94a3b8; font-size: .9rem; font-weight: 600;
}

/* ─── HIGHLIGHT ─────────────────────────────────────────────── */
mark.hl {
  background: #fef08a; color: #713f12;
  border-radius: 3px; padding: 0 2px;
}

/* ─── MODAL FINANCIERO ──────────────────────────────────────── */
.modal-financial .modal-content {
  border-radius: 20px; border: 0;
  box-shadow: 0 20px 60px rgba(15,23,42,.15);
}
.chip-card {
  background: #f8fafc; border: 1px solid rgba(226,232,240,.8);
  border-radius: 14px; padding: 16px;
}
.chip-title { font-size: .75rem; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: .05em; }
.chip-value { font-size: 1.35rem; font-weight: 800; color: #0f172a; margin: 4px 0; }
.chip-tag   { font-size: .73rem; color: #94a3b8; }
.small-muted { font-size: .8rem; color: #64748b; }

/* Ocultar ítems filtrados */
.venta-item.hidden { display: none !important; }


.card-venta{
  height: 100%;
  display: flex;
  flex-direction: column;
}

.detalle-venta{
  flex: 1;
  display: flex;
  flex-direction: column;
}

.cliente-nombre{
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  min-height: 44px;
}

.prod-name{
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  min-height: 38px;
}

.prod-sub{
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.detalle-venta p{
  display: flex;
  justify-content: space-between;
  align-items: flex-start;

  gap: 10px;

  min-height: 24px;

  margin-bottom: 8px;
}
.detalle-venta ul{
  max-height: 90px;
  overflow-y: auto;
  padding-right: 4px;
}

</style>

@php
use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Models\PagoRecordatorio;

$ordenes = $ordenes ?? ($os ?? ($ordenesServicio ?? ($ordenes_servicio ?? collect())));

$user = auth()->user();
$isAdmin =
    auth()->check() && (
        (($user->is_admin ?? false) === true)
        || (($user->role ?? null) === 'admin')
        || (method_exists($user, 'hasRole') && $user->hasRole('admin'))
    );

if (!function_exists('extraerPagoInicialDePagos')) {
    function extraerPagoInicialDePagos($pagos) {
        foreach ($pagos as $pago) {
            if (stripos((string)$pago->descripcion, 'Pago inicial') !== false || stripos((string)$pago->descripcion, 'Enganche') !== false) {
                return (float) $pago->monto;
            }
        }
        return 0;
    }
}

if (!function_exists('extraerFechasDePagos')) {
    function extraerFechasDePagos($pagos) {
        $fechas = [];
        foreach ($pagos as $pago) {
            if ($pago->fecha_pago) {
                try { $fechas[] = Carbon::parse($pago->fecha_pago); } catch (\Exception $e) {}
            }
        }
        return $fechas;
    }
}

if (!function_exists('obtenerPagoVencidoMasAntiguo')) {
    function obtenerPagoVencidoMasAntiguo($pagosPendientes) {
        $masAntiguo = null;
        foreach ($pagosPendientes as $p) {
            if ($p->fecha_pago) {
                try {
                    $f = Carbon::parse($p->fecha_pago)->startOfDay();
                    $hoy = Carbon::today();
                    if ($f->lt($hoy)) {
                        if (!$masAntiguo || $f->lt($masAntiguo)) { $masAntiguo = $f; }
                    }
                } catch (\Throwable $e) {}
            }
        }
        return $masAntiguo;
    }
}

if (!function_exists('yaNotificadoHoy')) {
    function yaNotificadoHoy(int $pagoId): bool {
        return PagoRecordatorio::where('pago_financiamiento_id', $pagoId)
            ->whereIn('channel', ['email','whatsapp'])
            ->whereDate('sent_at', Carbon::today()->toDateString())
            ->exists();
    }
}

if (!function_exists('route_or_url')) {
    function route_or_url($name, $params, $fallbackUrl) {
        try {
            $router = app('router');
            if ($router && $router->has($name)) { return route($name, $params); }
        } catch (\Throwable $e) {}
        return $fallbackUrl;
    }
}

if (!function_exists('resolverEstadoFinanciamientoVenta')) {
    function resolverEstadoFinanciamientoVenta($venta): array {
        $pagosVenta = \App\Models\Pago::where('venta_id', $venta->id)->get();
        $pagosVentaAprobados = $pagosVenta->filter(fn($p) => (bool)($p->aprobado ?? false))->values();
        $finIdsPagados = $pagosVentaAprobados->pluck('financiamiento_id')->filter()->unique()->values();
        $pagosPlan = collect($venta->pagosFinanciamiento ?? [])->filter(fn($p) => !((bool)($p->cancelado ?? false)))->values();
        $pagosPagados = $pagosPlan->filter(fn($p) => $finIdsPagados->contains($p->id) && (float)($p->monto ?? 0) > 0)->values();
        $pagosPendientes = $pagosPlan->filter(fn($p) => !$finIdsPagados->contains($p->id) && (float)($p->monto ?? 0) > 0)->sortBy('fecha_pago')->values();
        $pagoInicial = extraerPagoInicialDePagos($pagosVentaAprobados);
        $totalPagadoPlan = (float) $pagosPagados->sum(fn($p) => (float)($p->monto ?? 0));
        $totalPendientePlan = (float) $pagosPendientes->sum(fn($p) => (float)($p->monto ?? 0));
        $totalOriginal = (float)($venta->total_original ?? $venta->total ?? 0);
        $totalNeto = (float)($venta->total_contrato ?? $venta->total_neto ?? $venta->total ?? $totalOriginal);
        $totalPagadoGlobal = (float) $pagosVentaAprobados->sum(fn($p) => (float)($p->monto ?? 0));
        $montoAnticipo = (float) $pagosVentaAprobados->filter(fn($p) => (bool)($p->es_anticipo ?? false))->sum('monto');
        $montoTradeIn = (float) $pagosVentaAprobados->filter(function($p) {
            $m = strtolower(trim($p->metodo ?? $p->metodo_pago ?? $p->forma_pago ?? ''));
            return in_array($m, ['trade-in','trade in','tradein'], true);
        })->sum('monto');
        $restanteFinanciamiento = max(0, $totalNeto - $totalPagadoGlobal);
        $restantePorCuotas = ($restanteFinanciamiento <= 0.01) ? 0 : max(0, $totalPendientePlan);
        $restanteGlobal = max($restanteFinanciamiento, $restantePorCuotas);
        $fechasPago = ($restanteGlobal <= 0.01) ? [] : extraerFechasDePagos($pagosPendientes);
        $pagoHoy = collect($fechasPago)->contains(fn($f) => $f->isToday());
        $vencidoMasAntiguo = ($restanteGlobal <= 0.01) ? null : obtenerPagoVencidoMasAntiguo($pagosPendientes);
        $diasAtraso = $vencidoMasAntiguo ? (int) $vencidoMasAntiguo->diffInDays(Carbon::today()) : 0;
        $tienePagosPendientesReales = $restanteGlobal > 0.01 && $pagosPendientes->count() > 0 && $totalPendientePlan > 0.01;
        if ($restanteGlobal <= 0.01) $estado = 'liquidada';
        elseif ($vencidoMasAntiguo) $estado = 'atrasada';
        else $estado = 'pendiente';
        $pagoObjetivo = $pagosPendientes->first(function($p) use ($vencidoMasAntiguo, $pagoHoy, $restanteGlobal) {
            if ($restanteGlobal <= 0.01) return false;
            try {
                if ($vencidoMasAntiguo) return $p->fecha_pago && Carbon::parse($p->fecha_pago)->isSameDay($vencidoMasAntiguo);
                if ($pagoHoy) return $p->fecha_pago && Carbon::parse($p->fecha_pago)->isToday();
            } catch (\Throwable $e) {}
            return false;
        });
        return compact('pagosVenta','pagosVentaAprobados','finIdsPagados','pagosPlan','pagosPendientes','pagosPagados','pagoInicial','totalPagadoPlan','totalPendientePlan','totalOriginal','totalNeto','montoAnticipo','montoTradeIn','totalPagadoGlobal','restanteFinanciamiento','restantePorCuotas','restanteGlobal','fechasPago','pagoHoy','vencidoMasAntiguo','diasAtraso','tienePagosPendientesReales','estado','pagoObjetivo') + ['tiene_pagos_pendientes' => $tienePagosPendientesReales, 'pago_objetivo' => $pagoObjetivo, 'pagos_venta' => $pagosVenta, 'pagos_venta_aprobados' => $pagosVentaAprobados, 'fin_ids_pagados' => $finIdsPagados, 'pagos_plan' => $pagosPlan, 'pagos_pendientes' => $pagosPendientes, 'pagos_pagados' => $pagosPagados, 'pago_inicial' => $pagoInicial, 'total_pagado_plan' => $totalPagadoPlan, 'total_pendiente_plan' => $totalPendientePlan, 'total_original' => $totalOriginal, 'total_neto' => $totalNeto, 'monto_anticipo' => $montoAnticipo, 'monto_trade_in' => $montoTradeIn, 'total_pagado_global' => $totalPagadoGlobal, 'restante_financiamiento' => $restanteFinanciamiento, 'restante_por_cuotas' => $restantePorCuotas, 'restante_global' => $restanteGlobal, 'fechas_pago' => $fechasPago, 'pago_hoy' => $pagoHoy, 'vencido_mas_antiguo' => $vencidoMasAntiguo, 'dias_atraso' => $diasAtraso, 'vencido_mas_antiguo' => $vencidoMasAntiguo];
    }
}

$ventasConPagosProximos = [];
$ventasConPagosVencidos = [];
$metrics = ['total'=>0,'ventas'=>0,'ordenes'=>0,'pendientes'=>0,'atrasadas'=>0,'liquidadas'=>0,'saldo'=>0.0];
$moneyGlobal = ['original'=>0.0,'pagado'=>0.0,'restante'=>0.0];
$moneyByStatus = ['pendiente'=>['original'=>0.0,'pagado'=>0.0,'restante'=>0.0],'atrasada'=>['original'=>0.0,'pagado'=>0.0,'restante'=>0.0],'liquidada'=>['original'=>0.0,'pagado'=>0.0,'restante'=>0.0]];
$summaryByYear = []; $summaryByMonth = [];

foreach ($ventas as $ventaTmp) {
    $metrics['total']++; $metrics['ventas']++;
    $calcVentaTmp = resolverEstadoFinanciamientoVenta($ventaTmp);
    $totalOriginalTmp = (float) $calcVentaTmp['total_original'];
    $totalPagadoGlobalTmp = (float) $calcVentaTmp['total_pagado_global'];
    $restanteGlobalTmp = (float) $calcVentaTmp['restante_global'];
    $estadoTmp = $calcVentaTmp['estado'];
    $pagosPendientesTmp = $calcVentaTmp['pagos_pendientes'];
    $vencidoMasAntiguoTmp = $calcVentaTmp['vencido_mas_antiguo'];
    $moneyGlobal['original'] += $totalOriginalTmp;
    $moneyGlobal['pagado'] += $totalPagadoGlobalTmp;
    $moneyGlobal['restante'] += $restanteGlobalTmp;
    $fechaVentaTmp = $ventaTmp->created_at ? Carbon::parse($ventaTmp->created_at) : null;
    if ($fechaVentaTmp) {
        $yearKey = $fechaVentaTmp->format('Y'); $monthKey = $fechaVentaTmp->format('Y-m');
        if (!isset($summaryByYear[$yearKey])) $summaryByYear[$yearKey] = ['original'=>0.0,'pagado'=>0.0,'restante'=>0.0];
        $summaryByYear[$yearKey]['original'] += $totalOriginalTmp; $summaryByYear[$yearKey]['pagado'] += $totalPagadoGlobalTmp; $summaryByYear[$yearKey]['restante'] += $restanteGlobalTmp;
        if (!isset($summaryByMonth[$monthKey])) $summaryByMonth[$monthKey] = ['label'=>$fechaVentaTmp->translatedFormat('F Y'),'original'=>0.0,'pagado'=>0.0,'restante'=>0.0];
        $summaryByMonth[$monthKey]['original'] += $totalOriginalTmp; $summaryByMonth[$monthKey]['pagado'] += $totalPagadoGlobalTmp; $summaryByMonth[$monthKey]['restante'] += $restanteGlobalTmp;
    }
    if ($estadoTmp === 'liquidada') $metrics['liquidadas']++;
    elseif ($estadoTmp === 'atrasada') { $metrics['atrasadas']++; $metrics['saldo'] += $restanteGlobalTmp; }
    else { $metrics['pendientes']++; $metrics['saldo'] += $restanteGlobalTmp; }
    if (isset($moneyByStatus[$estadoTmp])) { $moneyByStatus[$estadoTmp]['original'] += $totalOriginalTmp; $moneyByStatus[$estadoTmp]['pagado'] += $totalPagadoGlobalTmp; $moneyByStatus[$estadoTmp]['restante'] += $restanteGlobalTmp; }
    $fechas = $calcVentaTmp['fechas_pago'];
    foreach ($fechas as $fecha) {
        if ($fecha->isToday() || $fecha->isTomorrow() || $fecha->between(Carbon::now(), Carbon::now()->addDays(7), true)) { $ventasConPagosProximos[] = ['venta'=>$ventaTmp,'fecha'=>$fecha]; break; }
    }
    if ($vencidoMasAntiguoTmp) {
        $pagoVencido = $pagosPendientesTmp->first(function($p) use ($vencidoMasAntiguoTmp) {
            try { return $p->fecha_pago && Carbon::parse($p->fecha_pago)->isSameDay($vencidoMasAntiguoTmp); } catch (\Throwable $e) { return false; }
        });
        $ventasConPagosVencidos[] = ['venta'=>$ventaTmp,'fecha'=>$vencidoMasAntiguoTmp,'pago'=>$pagoVencido,'dias'=>(int)$vencidoMasAntiguoTmp->diffInDays(Carbon::today()),'ya'=>$pagoVencido ? yaNotificadoHoy($pagoVencido->id) : false];
    }
}

foreach ($ordenes as $ordenTmp) {
    $metrics['total']++; $metrics['ordenes']++;
    $cant = (float)($ordenTmp->remision_cantidad ?? 0); $prec = (float)($ordenTmp->remision_precio ?? 0);
    $totalOriginalTmp = (float)($ordenTmp->remision_subtotal ?? 0);
    if ($totalOriginalTmp <= 0 && $cant > 0 && $prec > 0) $totalOriginalTmp = $cant * $prec;
    $pagosOrdenTmp = \App\Models\Pago::where('orden_id', $ordenTmp->id)->where('aprobado', true)->get();
    $totalPagadoGlobalTmp = (float) $pagosOrdenTmp->sum('monto');
    $restanteGlobalTmp = max(0, $totalOriginalTmp - $totalPagadoGlobalTmp);
    $moneyGlobal['original'] += $totalOriginalTmp; $moneyGlobal['pagado'] += $totalPagadoGlobalTmp; $moneyGlobal['restante'] += $restanteGlobalTmp;
    $fechaTmp = $ordenTmp->created_at ? Carbon::parse($ordenTmp->created_at) : null;
    if ($fechaTmp) {
        $yearKey = $fechaTmp->format('Y'); $monthKey = $fechaTmp->format('Y-m');
        if (!isset($summaryByYear[$yearKey])) $summaryByYear[$yearKey] = ['original'=>0.0,'pagado'=>0.0,'restante'=>0.0];
        $summaryByYear[$yearKey]['original'] += $totalOriginalTmp; $summaryByYear[$yearKey]['pagado'] += $totalPagadoGlobalTmp; $summaryByYear[$yearKey]['restante'] += $restanteGlobalTmp;
        if (!isset($summaryByMonth[$monthKey])) $summaryByMonth[$monthKey] = ['label'=>$fechaTmp->translatedFormat('F Y'),'original'=>0.0,'pagado'=>0.0,'restante'=>0.0];
        $summaryByMonth[$monthKey]['original'] += $totalOriginalTmp; $summaryByMonth[$monthKey]['pagado'] += $totalPagadoGlobalTmp; $summaryByMonth[$monthKey]['restante'] += $restanteGlobalTmp;
    }
    if ($restanteGlobalTmp <= 0.01) { $estadoTmp = 'liquidada'; $metrics['liquidadas']++; }
    else { $estadoTmp = 'pendiente'; $metrics['pendientes']++; $metrics['saldo'] += $restanteGlobalTmp; }
    if (isset($moneyByStatus[$estadoTmp])) { $moneyByStatus[$estadoTmp]['original'] += $totalOriginalTmp; $moneyByStatus[$estadoTmp]['pagado'] += $totalPagadoGlobalTmp; $moneyByStatus[$estadoTmp]['restante'] += $restanteGlobalTmp; }
}

ksort($summaryByYear); ksort($summaryByMonth);
@endphp

<div class="top-progress"><div class="bar" id="topBar"></div></div>

<div class="container py-3">

  {{-- ===== Toolbar Desktop sticky (con título integrado) ===== --}}
  
  <div class="filter-toolbar-sticky d-none d-md-block">
    <div class="filter-toolbar-wrap">
      <div class="filter-toolbar">

  {{-- Botón regresar --}}
  <button onclick="window.history.back();"
          class="btn btn-link p-0 me-2 d-inline-flex align-items-center justify-content-center text-dark"
          style="text-decoration:none;transition:.2s;"
          onmouseover="this.style.transform='scale(1.15)'"
          onmouseout="this.style.transform='scale(1)'"
          title="Regresar">
      <i class="bi bi-arrow-left-short fs-2"></i>
  </button>

  {{-- Botón inicio --}}
  <a href="{{ url('/home') }}"
     class="btn btn-link p-0 me-3 d-inline-flex align-items-center justify-content-center text-dark"
     style="text-decoration:none;transition:.2s;"
     onmouseover="this.style.transform='scale(1.15)'"
     onmouseout="this.style.transform='scale(1)'"
     title="Inicio">
      <i class="bi bi-house-door-fill fs-6"></i>
  </a>

  {{-- Título integrado --}}
  <span class="filter-toolbar-title">Financiamientos</span>
        <div class="filter-toolbar-divider"></div>

        {{-- Dropdown: Estado --}}
    <div class="dropdown dropdown-filter-wrap">
      <button class="btn dropdown-filter-btn dropdown-toggle" type="button" id="dropdownStatusFilter"
              data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
        <i class="bi bi-filter-square"></i> <span id="dropdownLabel">Todas</span>
      </button>
      <ul class="dropdown-menu dropdown-filter-menu" aria-labelledby="dropdownStatusFilter">
        <li>
          <div class="dropdown-item dropdown-filter-item form-check">
            <input class="form-check-input chk-status-all" type="checkbox" value="all" id="chkAll" checked>
            <label class="form-check-label" for="chkAll">Todas</label>
          </div>
        </li>
        <li><hr class="dropdown-divider"></li>
        <li>
          <div class="dropdown-item dropdown-filter-item form-check">
            <input class="form-check-input chk-status" type="checkbox" value="pendiente" id="chkPendiente">
            <label class="form-check-label" for="chkPendiente">Pendientes ({{ $metrics['pendientes'] }})</label>
          </div>
        </li>
        <li>
          <div class="dropdown-item dropdown-filter-item form-check">
            <input class="form-check-input chk-status" type="checkbox" value="atrasada" id="chkAtrasada">
            <label class="form-check-label" for="chkAtrasada">Atrasados ({{ $metrics['atrasadas'] }})</label>
          </div>
        </li>
        <li>
          <div class="dropdown-item dropdown-filter-item form-check">
            <input class="form-check-input chk-status" type="checkbox" value="liquidada" id="chkLiquidada">
            <label class="form-check-label" for="chkLiquidada">Liquidadas ({{ $metrics['liquidadas'] }})</label>
          </div>
        </li>
      </ul>
    </div>

    {{-- Filtro tipo --}}
    <div class="type-filter-wrap" role="tablist">
      <button type="button" class="type-btn active" data-type="all" aria-pressed="true">
        <i class="bi bi-grid-3x3-gap"></i>Todo
      </button>
      <button type="button" class="type-btn" data-type="venta" aria-pressed="false">
        <i class="bi bi-receipt"></i>Ventas ({{ $metrics['ventas'] }})
      </button>
      <button type="button" class="type-btn" data-type="os" aria-pressed="false">
        <i class="bi bi-tools"></i>OS ({{ $metrics['ordenes'] }})
      </button>
    </div>

    {{-- Buscador --}}
    <div class="search-chip">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
        <path d="M21 21l-4.35-4.35M10 18a8 8 0 1 1 0-16 8 8 0 0 1 0 16Z" stroke="#64748b" stroke-width="2" stroke-linecap="round"/>
      </svg>
      <input id="txtSearchDesktop" type="text" placeholder="Buscar por nombre, #remisión / OS…">
      <button class="clear-btn" id="btnClearSearchDesktop" title="Limpiar">×</button>
    </div>

        {{-- Botón auditoría (siempre visible para admin) --}}
        @if($isAdmin ?? false)
          <a href="{{ route('financiamientos.auditoria') }}" class="btn dropdown-filter-btn" style="text-decoration:none;">
            <i class="bi bi-shield-check"></i> Auditoría
          </a>
        @endif

        {{-- Botón resumen financiero --}}
        @if($isAdmin)
          <button class="btn dropdown-filter-btn ms-auto" data-bs-toggle="modal" data-bs-target="#resumenFinancieroModal">
            <i class="bi bi-bar-chart-line"></i> Resumen
          </button>
        @endif

      </div>{{-- .filter-toolbar --}}
    </div>{{-- .filter-toolbar-wrap --}}
  </div>{{-- .filter-toolbar-sticky --}}

  <div class="fin-content-spacer"></div>

  {{-- ===== Botón filtros: Móvil ===== --}}
  <button class="btn-filter d-md-none mb-3" id="openFilterSheet">
    <i class="bi bi-sliders"></i> Filtros y búsqueda
  </button>

  <div class="sheet-overlay" id="sheetOverlay" aria-hidden="true"></div>
  <div class="filter-sheet d-md-none" id="filterSheet" role="dialog" aria-modal="true" aria-labelledby="sheetTitle">
    <div class="sheet-handle"></div>
    <h6 class="sheet-title mb-3" id="sheetTitle">Filtrar financiamientos</h6>
    <div class="mb-3">
      <label class="form-label small text-muted">Tipo</label>
      <div class="d-flex gap-2 pill-group" id="tipoMob">
        <label class="pill active"><input type="radio" name="tipoMob" value="all" checked> Todo</label>
        <label class="pill"><input type="radio" name="tipoMob" value="venta"> Ventas</label>
        <label class="pill"><input type="radio" name="tipoMob" value="os"> OS</label>
      </div>
    </div>
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
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
          <path d="M21 21l-4.35-4.35M10 18a8 8 0 1 1 0-16 8 8 0 0 1 0 16Z" stroke="#64748b" stroke-width="2" stroke-linecap="round"/>
        </svg>
        <input type="text" id="txtSearchMobile" placeholder="Nombre, #remisión / OS…">
      </div>
    </div>
    <div class="d-flex gap-2">
      <button class="btn-ghost w-50" id="btnClear">Limpiar</button>
      <button class="btn-apply w-50" id="btnApply">Aplicar</button>
    </div>
  </div>

  {{-- ===== Sin resultados ===== --}}
  <div id="noResults" class="no-ventas d-none mt-2">No hay resultados con los filtros aplicados.</div>
  <div id="resultToast" class="toast-results"></div>

  @php
    $hayVentas  = isset($ventas) && $ventas->count();
    $hayOrdenes = isset($ordenes) && $ordenes->count();
  @endphp

  @if(!$hayVentas && !$hayOrdenes)
    <div class="no-ventas mt-3">No hay registros.</div>
  @else
    <div id="ventasGrid" class="row g-4 mt-1">

      {{-- =========================================================
           VENTAS
           ========================================================= --}}
      @if($hayVentas)
        @foreach($ventas as $venta)
          @php
            $calcVenta = resolverEstadoFinanciamientoVenta($venta);
            $pagosPlan         = $calcVenta['pagos_plan'];
            $pagosPendientes   = $calcVenta['pagos_pendientes'];
            $pagosPagados      = $calcVenta['pagos_pagados'];
            $pagoInicial       = (float) $calcVenta['pago_inicial'];
            $totalOriginal     = (float) $calcVenta['total_original'];
            $totalNeto         = (float) $calcVenta['total_neto'];
            $montoAnticipo     = (float) $calcVenta['monto_anticipo'];
            $montoTradeIn      = (float) $calcVenta['monto_trade_in'];
            $totalPagadoGlobal = (float) $calcVenta['total_pagado_global'];
            $restanteGlobal    = (float) $calcVenta['restante_global'];
            $fechasPago        = $calcVenta['fechas_pago'];
            $pagoHoy           = $calcVenta['pago_hoy'];
            $vencidoMasAntiguo = $calcVenta['vencido_mas_antiguo'];
            $diasAtraso        = (int) $calcVenta['dias_atraso'];
            $estado            = $calcVenta['estado'];
            $pagoObjetivo      = $calcVenta['pago_objetivo'];

            $clienteNombre = trim((optional($venta->cliente)->nombre.' '.optional($venta->cliente)->apellido) ?? '');
            $yaNotificado = $pagoObjetivo ? yaNotificadoHoy($pagoObjetivo->id) : false;

            $primerProductoNombre = null;
            $primerProductoSub    = null;
            $primerProductoSrc    = null;
            $primerItem = $venta->productos->first();
            if ($primerItem && $primerItem->producto) {
                $p = $primerItem->producto;
                if (!empty($p->tipo_equipo)) $primerProductoNombre = mb_strtoupper($p->tipo_equipo, 'UTF-8');
                elseif (!empty($p->nombre))  $primerProductoNombre = mb_strtoupper($p->nombre, 'UTF-8');
                $subPartes = [];
                if (!empty($p->modelo)) $subPartes[] = mb_strtoupper($p->modelo, 'UTF-8');
                if (!empty($p->marca))  $subPartes[] = mb_strtoupper($p->marca, 'UTF-8');
                $primerProductoSub = count($subPartes) ? implode(' | ', $subPartes) : null;
                $primerProductoImg = $p->imagen_url ?? $p->imagen ?? null;
                if ($primerProductoImg) {
                    if (Str::startsWith($primerProductoImg, ['http://','https://'])) $primerProductoSrc = $primerProductoImg;
                    elseif (Str::startsWith($primerProductoImg, ['storage/','images/'])) $primerProductoSrc = asset(ltrim($primerProductoImg, '/'));
                    else $primerProductoSrc = asset('storage/'.ltrim($primerProductoImg, '/'));
                }
            }
          @endphp

          <div class="col-md-6 col-lg-4 venta-item"
               data-tipo="venta"
               data-estado="{{ $estado }}"
               data-id="{{ $venta->id }}"
               data-cliente="{{ Str::of($clienteNombre)->lower() }}"
               @if($isAdmin)
                 data-total="{{ (float)$totalOriginal }}"
                 data-pagado="{{ (float)$totalPagadoGlobal }}"
                 data-restante="{{ (float)$restanteGlobal }}"
               @else
                 data-total="0" data-pagado="0" data-restante="0"
               @endif
               data-atraso="{{ (int)$diasAtraso }}">
            <div class="card-venta h-100">

              <div class="card-venta-header">
                <div style="min-width:0;flex:1;">
                  <div class="cliente-nombre js-name">{{ $clienteNombre ?: 'SIN CLIENTE' }}</div>
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

              <div class="detalle-venta">

                {{-- Producto — recuadro grande --}}
                @if($primerProductoNombre)
                  <div class="producto-banner">
                    @if($primerProductoSrc)
                      <img src="{{ $primerProductoSrc }}" alt="{{ $primerProductoNombre }}">
                    @else
                      <div class="producto-banner-fallback">
                        <span class="letra">{{ mb_substr($primerProductoNombre,0,1,'UTF-8') }}</span>
                      </div>
                    @endif
                    <div class="prod-meta">
                      <div class="prod-name">{{ $primerProductoNombre }}</div>
                      @if($primerProductoSub)
                        <div class="prod-sub">{{ $primerProductoSub }}</div>
                      @endif
                    </div>
                  </div>
                @endif

                {{-- Alertas --}}
                @if($estado === 'atrasada')
                  <div class="pill-atraso mb-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="#b91c1c" viewBox="0 0 24 24"><path d="M1 21h22L12 2 1 21zM13 17h-2v-2h2v2zm0-4h-2V9h2v4z"/></svg>
                    {{ $diasAtraso === 1 ? '1 día de atraso' : $diasAtraso.' días de atraso' }}
                  </div>
                @elseif($pagoHoy && $restanteGlobal > 0.01)
                  <div class="pill-atraso mb-2" style="background:#fffbeb;border-color:#fde68a;color:#92400e;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="#92400e" viewBox="0 0 24 24"><path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2V9h2v4z"/></svg>
                    Hoy hay un pago de este plan
                  </div>
                @endif

                <p><strong>Emitido:</strong> {{ Carbon::parse($venta->created_at)->format('d/m/Y') }}</p>

                @if($isAdmin)
                  <p><strong>Total venta:</strong> ${{ number_format($totalOriginal, 2) }}</p>
                  @if($montoTradeIn > 0)
                    <p><strong>Trade-in:</strong> -${{ number_format($montoTradeIn, 2) }}</p>
                  @endif
                  @if($montoAnticipo > 0)
                    <p><strong>Anticipo:</strong> -${{ number_format($montoAnticipo, 2) }}</p>
                  @endif
                  @if(abs($totalNeto - $totalOriginal) > 0.01)
                    <p><strong>Total a financiar:</strong> ${{ number_format($totalNeto, 2) }}</p>
                  @endif
                  @if($pagoInicial > 0)
                    <p><strong>Pago inicial plan:</strong> ${{ number_format($pagoInicial, 2) }}</p>
                  @endif
                  <p><strong>Total pagado:</strong> ${{ number_format($totalPagadoGlobal, 2) }}</p>
                  <p><strong>Restante:</strong> <span class="restante js-restante">${{ number_format($restanteGlobal, 2) }}</span></p>
                @endif

                @if(count($fechasPago))
                  <p class="mt-2 mb-1"><strong>Fechas pendientes del plan:</strong></p>
                  <ul class="mb-0">
                    @foreach($fechasPago as $f)
                      <li>{{ $f->isoFormat('DD [de] MMMM [de] YYYY') }}</li>
                    @endforeach
                  </ul>
                @endif
              </div>

              <div class="card-actions">
                <a href="{{ route('ventas.pagos.index', $venta->id) }}" class="btn-soft-info">Ver pagos</a>
                <a href="{{ route('ventas.show', $venta->id) }}" class="btn-soft-success">Ver remisión</a>
                @if($pagoObjetivo && !$yaNotificado)
                  <button class="btn-send reenviar-btn" data-pago-id="{{ $pagoObjetivo->id }}">
                    <span class="label">{{ $estado === 'atrasada' ? 'Avisar por WhatsApp' : 'Notificar' }}</span>
                  </button>
                @elseif($pagoObjetivo && $yaNotificado)
                  <span class="badge rounded-pill bg-secondary" style="font-size:.73rem;">Notificado hoy</span>
                @endif
              </div>

            </div>
          </div>
        @endforeach
      @endif

      {{-- =========================================================
           ÓRDENES DE SERVICIO
           ========================================================= --}}
      @if($hayOrdenes)
        @foreach($ordenes as $orden)
          @php
            $clienteOrden = trim((optional($orden->cliente)->nombre.' '.optional($orden->cliente)->apellido) ?? '');
            if(!$clienteOrden) $clienteOrden = 'SIN CLIENTE';
            $cant = (float)($orden->remision_cantidad ?? 0); $prec = (float)($orden->remision_precio ?? 0);
            $totalOS = (float)($orden->remision_subtotal ?? 0);
            if ($totalOS <= 0 && $cant > 0 && $prec > 0) $totalOS = $cant * $prec;
            $pagosOS = \App\Models\Pago::where('orden_id', $orden->id)->where('aprobado', true)->get();
            $pagadoOS = (float)$pagosOS->sum('monto');
            $restanteOS = max(0, $totalOS - $pagadoOS);
            $estadoOS = $restanteOS <= 0.01 ? 'liquidada' : 'pendiente';
            $descOS = $orden->remision_descripcion ?? ('MANTENIMIENTO DE '.mb_strtoupper(($orden->equipo ?? 'EQUIPO'), 'UTF-8'));
            $osIdText = 'OS: 2025-'.$orden->id;
            $urlPagosOS = route_or_url('ordenes.pagos.index', $orden->id, url('/ordenes/'.$orden->id.'/pagos'));
            $urlPdfOS   = route_or_url('ordenes.remision.pdf', $orden->id, url('/ordenes/'.$orden->id.'/remision-pdf'));
          @endphp

          <div class="col-md-6 col-lg-4 venta-item"
               data-tipo="os"
               data-estado="{{ $estadoOS }}"
               data-id="OS-{{ $orden->id }}"
               data-cliente="{{ Str::of($clienteOrden)->lower() }}"
               @if($isAdmin)
                 data-total="{{ (float)$totalOS }}"
                 data-pagado="{{ (float)$pagadoOS }}"
                 data-restante="{{ (float)$restanteOS }}"
               @else
                 data-total="0" data-pagado="0" data-restante="0"
               @endif
               data-atraso="0">
            <div class="card-venta h-100">

              <div class="card-venta-header">
                <div style="min-width:0;flex:1;">
                  <div class="cliente-nombre js-name">{{ $clienteOrden }}</div>
                  <div class="venta-id js-id">{{ $osIdText }}</div>
                </div>
                @if($estadoOS === 'liquidada')
                  <span class="estado-liquidada badge">Liquidada</span>
                @else
                  <span class="estado-pendiente badge">Pendiente</span>
                @endif
              </div>

              <div class="detalle-venta">
                <p><strong>Emitido:</strong> {{ $orden->created_at ? Carbon::parse($orden->created_at)->format('d/m/Y') : '—' }}</p>
                <p><strong>Servicio:</strong> {{ mb_strtoupper(($orden->equipo ?? 'MANTENIMIENTO'), 'UTF-8') }}</p>
                <p class="mb-2">
                  <strong>Descripción:</strong><br>
                  <span class="small text-muted">{!! nl2br(e($descOS)) !!}</span>
                </p>
                @if($isAdmin)
                  <p><strong>Total OS:</strong> ${{ number_format($totalOS, 2) }}</p>
                  <p><strong>Total pagado:</strong> ${{ number_format($pagadoOS, 2) }}</p>
                  <p><strong>Restante:</strong> <span class="restante js-restante">${{ number_format($restanteOS, 2) }}</span></p>
                @endif
              </div>

              <div class="card-actions">
                <a href="{{ $urlPagosOS }}" class="btn-soft-info">Ver pagos</a>
                <a href="{{ $urlPdfOS }}" class="btn-soft-success">Ver remisión</a>
              </div>

            </div>
          </div>
        @endforeach
      @endif

    </div>
  @endif

  {{-- ===== Modal: Resumen financiero (SOLO ADMIN) ===== --}}
  @if($isAdmin)
    <div class="modal fade" id="resumenFinancieroModal" tabindex="-1" aria-labelledby="resumenFinancieroLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered modal-financial">
        <div class="modal-content">
          <div class="modal-header border-0 pb-0">
            <div>
              <h5 class="modal-title" id="resumenFinancieroLabel">Resumen financiero</h5>
              <div class="small-muted">Vista rápida por estado, año y mes. Cifras en MXN.</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
          </div>
          <div class="modal-body pt-3">
            <h6>Global</h6>
            <div class="row g-2 mb-3">
              <div class="col-md-4"><div class="chip-card"><div class="chip-title">Total vendido</div><div class="chip-value">${{ number_format($moneyGlobal['original'], 2) }}</div><div class="chip-tag text-muted">Remisiones + órdenes</div></div></div>
              <div class="col-md-4"><div class="chip-card"><div class="chip-title">Total cobrado</div><div class="chip-value">${{ number_format($moneyGlobal['pagado'], 2) }}</div><div class="chip-tag text-success">Ventas + órdenes</div></div></div>
              <div class="col-md-4"><div class="chip-card"><div class="chip-title">Saldo pendiente</div><div class="chip-value">${{ number_format($moneyGlobal['restante'], 2) }}</div><div class="chip-tag text-danger">Aún por cobrar</div></div></div>
            </div>
            <h6 class="mt-2">Por estado</h6>
            <div class="row g-2 mb-3">
              <div class="col-md-4"><div class="chip-card"><div class="chip-title">Pendientes ({{ $metrics['pendientes'] }})</div><div class="chip-value">${{ number_format($moneyByStatus['pendiente']['restante'] ?? 0, 2) }}</div><div class="chip-tag">Sin atraso</div></div></div>
              <div class="col-md-4"><div class="chip-card"><div class="chip-title">Atrasadas ({{ $metrics['atrasadas'] }})</div><div class="chip-value">${{ number_format($moneyByStatus['atrasada']['restante'] ?? 0, 2) }}</div><div class="chip-tag text-danger">Saldo vencido</div></div></div>
              <div class="col-md-4"><div class="chip-card"><div class="chip-title">Liquidadas ({{ $metrics['liquidadas'] }})</div><div class="chip-value">${{ number_format($moneyByStatus['liquidada']['pagado'] ?? 0, 2) }}</div><div class="chip-tag text-success">Cobrado</div></div></div>
            </div>
            <h6 class="mt-3">Por año</h6>
            <div class="table-responsive mb-3">
              <table class="table table-sm align-middle mb-0">
                <thead><tr><th>Año</th><th class="text-end">Vendido</th><th class="text-end">Cobrado</th><th class="text-end">Saldo</th></tr></thead>
                <tbody>
                @forelse($summaryByYear as $year => $row)
                  <tr><td>{{ $year }}</td><td class="text-end">${{ number_format($row['original'],2) }}</td><td class="text-end">${{ number_format($row['pagado'],2) }}</td><td class="text-end">${{ number_format($row['restante'],2) }}</td></tr>
                @empty
                  <tr><td colspan="4" class="text-center text-muted">Sin información aún.</td></tr>
                @endforelse
                </tbody>
              </table>
            </div>
            <h6 class="mt-3">Por mes</h6>
            <div class="table-responsive">
              <table class="table table-sm align-middle mb-0">
                <thead><tr><th>Mes</th><th class="text-end">Vendido</th><th class="text-end">Cobrado</th><th class="text-end">Saldo</th></tr></thead>
                <tbody>
                @forelse($summaryByMonth as $key => $row)
                  <tr><td>{{ $row['label'] }}</td><td class="text-end">${{ number_format($row['original'],2) }}</td><td class="text-end">${{ number_format($row['pagado'],2) }}</td><td class="text-end">${{ number_format($row['restante'],2) }}</td></tr>
                @empty
                  <tr><td colspan="4" class="text-center text-muted">Sin información aún.</td></tr>
                @endforelse
                </tbody>
              </table>
            </div>
          </div>
          <div class="modal-footer border-0 pt-0">
            <button type="button" class="btn-ghost" data-bs-dismiss="modal">Cerrar</button>
          </div>
        </div>
      </div>
    </div>
  @endif

</div>

<script>
(function(){
  const $$ = (s, c=document) => Array.from(c.querySelectorAll(s));
  const $  = (s, c=document) => c.querySelector(s);
  const grid = $('#ventasGrid'), noResults = $('#noResults'), toast = $('#resultToast'), topBar = $('#topBar');

  let estadoMode = 'all', tipoMode = 'all', debounceTimer;

  const norm = s => (s||'').toString().toLowerCase().normalize('NFD').replace(/\p{Diacritic}/gu,'');

  function showToast(msg){
    if(!toast) return;
    toast.textContent = msg; toast.style.display = 'block';
    clearTimeout(toast._tmr);
    toast._tmr = setTimeout(()=> toast.style.display = 'none', 1400);
  }

  function setFiltering(on){
    if(topBar) topBar.style.width = on ? '70%' : '0%';
  }

  function clearHighlights(card){
    const n=card.querySelector('.js-name'), i=card.querySelector('.js-id');
    if(n) n.innerHTML=n.textContent; if(i) i.innerHTML=i.textContent;
  }

  function applyHighlights(card, q){
    if(!q) return;
    const qn = norm(q);
    ['.js-name','.js-id'].forEach(sel=>{
      const el = card.querySelector(sel);
      if(!el) return;
      const t = el.textContent;
      const idx = norm(t).indexOf(qn);
      if(idx >= 0) el.innerHTML = t.slice(0,idx)+'<mark class="hl">'+t.slice(idx,idx+q.length)+'</mark>'+t.slice(idx+q.length);
    });
  }

  function applyAllFilters(){
    if(!grid) return;
    setFiltering(true);
    const isMobile = window.matchMedia('(max-width:767px)').matches;
    const q = norm((isMobile ? $('#txtSearchMobile')?.value : $('#txtSearchDesktop')?.value) || '');
    const items = $$('.venta-item', grid);
    let visible = 0;

    items.forEach(it => {
      clearHighlights(it);
      const st = it.dataset.estado;
      const tp = it.dataset.tipo || 'venta';
      const cli = it.dataset.cliente || '';
      const cid = (it.dataset.id || '').toString().toLowerCase();

      const passEstado = estadoMode === 'all' || estadoMode.split('|').includes(st);
      const passTipo   = tipoMode === 'all' || tipoMode === tp;
      const passTexto  = !q || cli.includes(q) || cid.includes(q);

      const show = passEstado && passTipo && passTexto;
      it.classList.toggle('hidden', !show);
      if(show){ visible++; applyHighlights(it, q); }
    });

    if(noResults) noResults.classList.toggle('d-none', visible > 0);
    showToast(visible + ' resultado' + (visible === 1 ? '' : 's'));
    topBar.style.width = '100%';
    setTimeout(()=> setFiltering(false), 220);
  }

  function debouncedFilter(){
    clearTimeout(debounceTimer);
    setFiltering(true);
    if(topBar) topBar.style.width = '45%';
    debounceTimer = setTimeout(applyAllFilters, 200);
  }

  // ─── Tipo (desktop) ──────────────────────────────────────────
  $$('.type-btn').forEach(btn => {
    btn.addEventListener('click', ()=>{
      $$('.type-btn').forEach(b=>{ b.classList.remove('active'); b.setAttribute('aria-pressed','false'); });
      btn.classList.add('active'); btn.setAttribute('aria-pressed','true');
      tipoMode = btn.dataset.type || 'all';
      debouncedFilter();
    });
  });

  // ─── Búsqueda desktop ────────────────────────────────────────
  $('#txtSearchDesktop')?.addEventListener('input', debouncedFilter);
  $('#btnClearSearchDesktop')?.addEventListener('click', ()=>{ const t=$('#txtSearchDesktop'); if(t){ t.value=''; debouncedFilter(); } });

  // ─── Dropdown estado ─────────────────────────────────────────
  document.addEventListener('DOMContentLoaded', function(){
    const chkAll = document.getElementById('chkAll');
    const chkStatuses = document.querySelectorAll('.chk-status');
    const dropdownLabel = document.getElementById('dropdownLabel');

    function syncEstadoMode(){
      if(chkAll && chkAll.checked){
        estadoMode = 'all';
        if(dropdownLabel) dropdownLabel.innerText = 'Todas';
      } else {
        const vals = Array.from(chkStatuses).filter(c=>c.checked).map(c=>c.value);
        estadoMode = vals.length ? vals.join('|') : 'all';
        if(dropdownLabel){
          const labels = Array.from(chkStatuses).filter(c=>c.checked).map(c=>c.nextElementSibling.innerText.split(' ')[0]);
          dropdownLabel.innerText = labels.length ? labels.join(', ') : 'Todas';
          if(!vals.length && chkAll) chkAll.checked = true;
        }
      }
      debouncedFilter();
    }

    if(chkAll) chkAll.addEventListener('change', function(){ if(this.checked) chkStatuses.forEach(c=>c.checked=false); syncEstadoMode(); });
    chkStatuses.forEach(chk => chk.addEventListener('change', function(){ if(this.checked && chkAll) chkAll.checked=false; syncEstadoMode(); }));
  });

  // ─── Mobile sheet ─────────────────────────────────────────────
  const sheet=$('#filterSheet'), overlay=$('#sheetOverlay');
  const openBtn=$('#openFilterSheet');

  function openSheet(){ sheet?.classList.add('open'); overlay?.classList.add('open'); }
  function closeSheet(){ sheet?.classList.remove('open'); overlay?.classList.remove('open'); }

  openBtn?.addEventListener('click', openSheet);
  overlay?.addEventListener('click', closeSheet);

  $$('#estadoMob .pill, #tipoMob .pill').forEach(p => {
    p.addEventListener('click', ()=>{
      const group = p.closest('.pill-group');
      $$('.pill', group).forEach(x=>x.classList.remove('active'));
      p.classList.add('active');
      p.querySelector('input').checked = true;
    });
  });

  $('#btnClear')?.addEventListener('click', ()=>{
    ['#estadoMob','#tipoMob'].forEach(groupSel=>{
      const group = document.querySelector(groupSel);
      if(!group) return;
      $$('.pill', group).forEach(x=>x.classList.remove('active'));
      const allPill = group.querySelector('.pill input[value="all"]')?.closest('.pill');
      if(allPill){ allPill.classList.add('active'); allPill.querySelector('input').checked=true; }
    });
    const mob=$('#txtSearchMobile'); if(mob) mob.value='';
    tipoMode='all'; estadoMode='all';
  });

  $('#btnApply')?.addEventListener('click', ()=>{
    estadoMode = document.querySelector('input[name="estadoMob"]:checked')?.value || 'all';
    tipoMode   = document.querySelector('input[name="tipoMob"]:checked')?.value || 'all';

    const typeBtn = document.querySelector(`.type-btn[data-type="${tipoMode}"]`);
    if(typeBtn){ $$('.type-btn').forEach(b=>{ b.classList.remove('active'); b.setAttribute('aria-pressed','false'); }); typeBtn.classList.add('active'); typeBtn.setAttribute('aria-pressed','true'); }

    const txtDesk=$('#txtSearchDesktop'), txtMob=$('#txtSearchMobile');
    if(txtDesk && txtMob) txtDesk.value = txtMob.value;

    debouncedFilter();
    closeSheet();
  });

  // ─── Intersection observer (reveal) ──────────────────────────
  const io = new IntersectionObserver(entries=>{
    entries.forEach(e=>{ if(e.isIntersecting){ e.target.classList.add('revealed'); io.unobserve(e.target); } });
  },{ rootMargin:'0px 0px -10% 0px', threshold:.1 });
  $$('.card-venta').forEach(c=>io.observe(c));

  // ─── Notificaciones ───────────────────────────────────────────
  function sendNotify(btn){
    const pagoId = btn.dataset.pagoId;
    const csrf   = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if(!pagoId || !csrf) return;
    btn.disabled=true; btn.classList.add('sending');
    btn.querySelector('.label').textContent='Enviando…';
    fetch(`/financiamientos/notificar/${pagoId}`,{ method:'POST', headers:{'X-CSRF-TOKEN':csrf,'Accept':'application/json'} })
      .then(r=>{ if(r.ok){ btn.classList.replace('sending','sent'); btn.querySelector('.label').textContent='Enviado'; setTimeout(()=>{ btn.replaceWith(Object.assign(document.createElement('span'),{className:'badge rounded-pill bg-secondary',textContent:'Notificado hoy'})); },450); } else throw new Error(); })
      .catch(()=>{ btn.classList.remove('sending'); btn.disabled=false; btn.querySelector('.label').textContent='Error, reintentar'; });
  }
  $$('.reenviar-btn').forEach(b=>b.addEventListener('click',function(){ sendNotify(this); }));

  debouncedFilter();
})();
</script>
@endsection