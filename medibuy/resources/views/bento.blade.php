@extends('layouts.app')

@section('title', 'Panel · Operaciones')

@section('content')
@php
    use Illuminate\Support\Facades\Route;

    $routeFirst = function (array $names, $fallback = '#') {
        foreach ($names as $name) {
            if (Route::has($name)) {
                return route($name);
            }
        }

        return $fallback;
    };

    $fmt = fn($v) => number_format((int)($v ?? 0));

    $cotizacionesRentasCount = (int) ($cotizacionesRentasCount ?? 0);
    $cotizacionesVentasCount = (int) ($cotizacionesVentasCount ?? 0);
    $ordenesServicioCount    = (int) ($ordenesServicioCount ?? 0);
    $ventasRealizadasCount   = (int) ($ventasRealizadasCount ?? 0);
    $deudoresCount           = (int) ($deudoresCount ?? 0);
    $clientesCount           = (int) ($clientesCount ?? 0);

    $ventasTotal             = (float) ($ventasTotal ?? 0);
    $deudaTotal              = (float) ($deudaTotal ?? 0);

    $ordenesPendientesCount  = (int) ($ordenesPendientesCount ?? 0);
    $ordenesProcesoCount     = (int) ($ordenesProcesoCount ?? 0);
    $ordenesTerminadasCount  = (int) ($ordenesTerminadasCount ?? 0);

    $conversionRate          = (int) ($conversionRate ?? 0);
    $actividadHoyCount       = (int) ($actividadHoyCount ?? 0);

    $auditoriaUrl  = $routeFirst(['financiamientos.auditoria']);
    $bentoUrl      = $routeFirst(['bento.index']);
    $propuestasUrl = $routeFirst(['propuestas.index']);
    $ordenesUrl    = $routeFirst(['ordenes.index']);
    $ventasUrl     = $routeFirst(['ventas.index']);
    $deudoresUrl   = $routeFirst(['ventas.deudores']);
    $clientesUrl   = $routeFirst(['clientes.index']);
@endphp

<style>
    html,
    body {
        background: #f4f4f5 !important;
        font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif !important;
    }

    body {
        margin: 0;
        color: #18181b;
    }

    .ops-shell {
        width: 100%;
        box-sizing: border-box;
        margin: 0 auto;
        padding: 48px 32px 80px;
    }

    .ops-header {
        margin-bottom: 36px;
        display: flex;
        justify-content: space-between;
        gap: 24px;
        align-items: flex-end;
    }

    .ops-title {
        margin: 0;
        font-size: clamp(2rem, 4vw, 3rem);
        font-weight: 600;
        letter-spacing: -0.025em;
        color: #09090b;
        line-height: 1;
    }

    .ops-sub {
        margin: 12px 0 0;
        font-size: 1.08rem;
        color: #52525b;
        max-width: 720px;
        line-height: 1.55;
    }

    .ops-summary-pill {
        background: #ffffff;
        border: 1px solid rgba(0, 0, 0, 0.06);
        border-radius: 999px;
        padding: 10px 16px;
        color: #3f3f46;
        font-size: .9rem;
        font-weight: 500;
        box-shadow: 0 10px 30px rgba(0,0,0,.04);
        white-space: nowrap;
    }

    .bento-grid {
        display: grid;
        width: 100%;
        grid-auto-rows: 20rem;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 1.25rem;
    }

    .col-span-1 { grid-column: span 1; }
    .col-span-2 { grid-column: span 2; }
    .col-span-3 { grid-column: span 3; }

    .bento-card {
        position: relative;
        display: flex;
        flex-direction: column;
        justify-content: flex-end;
        overflow: hidden;
        border-radius: 30px;
        background: #ffffff;
        border: 1px solid rgba(0, 0, 0, 0.04);
        box-shadow: 0 4px 20px rgba(0,0,0,0.03), 0 1px 3px rgba(0,0,0,0.02);
        text-decoration: none !important;
        color: inherit;
        transition: transform .35s cubic-bezier(.4,0,.2,1), box-shadow .35s cubic-bezier(.4,0,.2,1), border-color .35s;
    }

    .bento-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 18px 55px rgba(0,0,0,.09);
        border-color: rgba(0,0,0,.08);
    }

    .bento-inner {
        position: relative;
        z-index: 10;
        height: 100%;
        padding: 1.75rem;
        display: flex;
        flex-direction: column;
        justify-content: flex-end;
        background: linear-gradient(to top, #fff 36%, rgba(255,255,255,.9) 66%, transparent 100%);
    }

    .bento-content {
        display: flex;
        flex-direction: column;
        gap: .55rem;
        transform: translateY(0);
        transition: transform .35s cubic-bezier(.4,0,.2,1);
    }

    .bento-card:hover .bento-content {
        transform: translateY(-2rem);
    }

    .bento-icon-wrapper {
        width: 3.5rem;
        height: 3.5rem;
        border-radius: 16px;
        background: #f4f4f5;
        border: 1px solid rgba(0,0,0,.04);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: .45rem;
        transition: all .35s ease;
    }

    .bento-card:hover .bento-icon-wrapper {
        background: #18181b;
        border-color: #18181b;
    }

    .bento-icon {
        width: 1.85rem;
        height: 1.85rem;
        color: #3f3f46;
        transition: all .35s ease;
    }

    .bento-icon svg {
        width: 100%;
        height: 100%;
        display: block;
    }

    .bento-card:hover .bento-icon {
        color: #ffffff;
        transform: scale(.92);
    }

    .bento-title {
        margin: 0;
        font-size: 1.35rem;
        font-weight: 600;
        color: #18181b;
        line-height: 1.15;
        letter-spacing: -.015em;
    }

    .bento-desc {
        margin: 0;
        font-size: .96rem;
        color: #71717a;
        max-width: 92%;
        line-height: 1.5;
    }

    .bento-desc strong {
        color: #18181b;
        font-weight: 600;
    }

    .bento-cta {
        position: absolute;
        bottom: 1.5rem;
        left: 1.75rem;
        display: flex;
        align-items: center;
        gap: .4rem;
        font-size: .9rem;
        font-weight: 500;
        color: #09090b;
        opacity: 0;
        transform: translateY(14px);
        transition: all .35s cubic-bezier(.4,0,.2,1);
    }

    .bento-card:hover .bento-cta {
        opacity: 1;
        transform: translateY(0);
    }

    .bento-arrow {
        width: 1rem;
        height: 1rem;
        transition: transform .2s ease;
    }

    .bento-card:hover .bento-cta:hover .bento-arrow {
        transform: translateX(4px);
    }

    .bento-bg {
        position: absolute;
        inset: 0;
        z-index: 1;
        pointer-events: none;
        overflow: hidden;
        -webkit-mask-image: linear-gradient(to bottom, black 58%, transparent 100%);
        mask-image: linear-gradient(to bottom, black 58%, transparent 100%);
    }

    .card-overview {
        background: #ffffff;
    }

    .overview-grid {
        position: absolute;
        inset: 0;
        z-index: 0;
        background-image:
            linear-gradient(rgba(0,0,0,.035) 1px, transparent 1px),
            linear-gradient(90deg, rgba(0,0,0,.035) 1px, transparent 1px);
        background-size: 42px 42px;
    }

    .pipeline-flow {
        position: absolute;
        inset: 18% 0 auto 0;
        height: 44%;
        z-index: 2;
        overflow: hidden;
        -webkit-mask-image: linear-gradient(to right, transparent, black 10%, black 90%, transparent);
        mask-image: linear-gradient(to right, transparent, black 10%, black 90%, transparent);
    }

    .pipeline-line {
        position: absolute;
        left: 0;
        width: 200%;
        height: 100%;
        background-repeat: repeat-x;
        background-size: 50% 100%;
        animation: pipelineMove 9s linear infinite;
    }

    .pipeline-blue {
        background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 520 150' preserveAspectRatio='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M0,75 C100,30 150,110 260,85 C360,60 420,120 520,75' fill='none' stroke='%2306b6d4' stroke-width='5' stroke-linecap='round'/%3E%3C/svg%3E");
    }

    .pipeline-orange {
        background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 520 150' preserveAspectRatio='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M0,85 C90,130 160,40 260,75 C350,110 430,40 520,85' fill='none' stroke='%23f97316' stroke-width='4' stroke-linecap='round'/%3E%3C/svg%3E");
        animation-duration: 13s;
        animation-direction: reverse;
        opacity: .92;
    }

    @keyframes pipelineMove {
        to { transform: translateX(-50%); }
    }

    .overview-layout {
        position: relative;
        z-index: 10;
        height: 100%;
        padding: 1.75rem;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .overview-top {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 18px;
    }

    .overview-label {
        display: block;
        color: #18181b;
        font-size: .96rem;
        font-weight: 600;
        margin-bottom: 8px;
    }

    .overview-legend {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }

    .legend-item {
        font-size: .8rem;
        color: #52525b;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .legend-dot {
        width: 13px;
        height: 4px;
        border-radius: 99px;
    }

    .dot-blue { background: #06b6d4; box-shadow: 0 0 10px rgba(6,182,212,.45); }
    .dot-orange { background: #f97316; box-shadow: 0 0 10px rgba(249,115,22,.45); }

    .live-badge {
        background: rgba(34,197,94,.1);
        color: #16a34a;
        border: 1px solid rgba(34,197,94,.2);
        padding: 6px 12px;
        border-radius: 999px;
        font-size: .75rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 6px;
        white-space: nowrap;
    }

    .live-badge svg {
        width: 14px;
        height: 14px;
        animation: pulseOpacity 2s infinite;
    }

    @keyframes pulseOpacity {
        50% { opacity: .4; }
    }

    .overview-bottom {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 22px;
    }

    .mini-metrics {
        display: flex;
        gap: 18px;
        align-items: flex-end;
    }

    .mini-metric {
        min-width: 72px;
    }

    .mini-metric span {
        display: block;
        font-size: .72rem;
        color: #71717a;
        font-weight: 600;
        margin-bottom: 6px;
    }

    .mini-bars {
        display: flex;
        gap: 4px;
        align-items: flex-end;
        height: 48px;
    }

    .mini-bars i {
        width: 8px;
        border-radius: 999px 999px 0 0;
        background: #06b6d4;
        animation: barFloat 1.8s ease-in-out infinite alternate;
    }

    .mini-bars i:nth-child(2) { background: #38bdf8; animation-delay: .25s; }
    .mini-bars i:nth-child(3) { background: #f97316; animation-delay: .5s; }

    .h1 { height: 35%; }
    .h2 { height: 55%; }
    .h3 { height: 75%; }
    .h4 { height: 95%; }

    @keyframes barFloat {
        from { transform: scaleY(.72); }
        to { transform: scaleY(1); }
    }

    .gauge {
        position: relative;
        width: 82px;
        height: 82px;
        flex-shrink: 0;
    }

    .gauge svg {
        width: 82px;
        height: 82px;
        transform: rotate(-90deg);
    }

    .gauge-bg {
        fill: none;
        stroke: rgba(0,0,0,.06);
        stroke-width: 3;
    }

    .gauge-bar {
        fill: none;
        stroke: #06b6d4;
        stroke-width: 3;
        stroke-linecap: round;
        filter: drop-shadow(0 0 4px rgba(6,182,212,.4));
        animation: gaugeDraw 1s ease-out forwards;
    }

    @keyframes gaugeDraw {
        from { stroke-dasharray: 0 100; }
    }

    .gauge-value {
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        font-weight: 600;
        color: #18181b;
    }

    .bg-quotes {
        background:
            radial-gradient(circle at 80% 12%, rgba(14,165,233,.14), transparent 26%),
            linear-gradient(180deg, rgba(0,0,0,.02), transparent);
    }

    .quote-stack {
        position: absolute;
        top: 34px;
        right: 28px;
        width: 150px;
        display: flex;
        flex-direction: column;
        gap: 12px;
        animation: floatSoft 5s ease-in-out infinite;
    }

    .quote-paper {
        background: rgba(255,255,255,.9);
        border: 1px solid rgba(0,0,0,.05);
        border-radius: 14px;
        padding: 12px;
        box-shadow: 0 10px 28px rgba(0,0,0,.05);
    }

    .quote-paper b {
        display: block;
        font-size: .66rem;
        color: #71717a;
        margin-bottom: 6px;
    }

    .quote-line {
        height: 5px;
        background: #e4e4e7;
        border-radius: 99px;
        margin-bottom: 5px;
    }

    .quote-line.short {
        width: 62%;
    }

    @keyframes floatSoft {
        50% { transform: translateY(-12px); }
    }

    .bg-tags {
        background:
            radial-gradient(circle at 80% 20%, rgba(249,115,22,.2), transparent 30%),
            radial-gradient(circle at 30% 30%, rgba(6,182,212,.18), transparent 32%);
    }

    .tag-cloud {
        position: absolute;
        top: 38px;
        right: 24px;
        display: grid;
        gap: 10px;
        animation: driftTags 6s ease-in-out infinite;
    }

    .tag-chip {
        padding: 8px 13px;
        border-radius: 999px;
        background: rgba(255,255,255,.82);
        border: 1px solid rgba(0,0,0,.05);
        box-shadow: 0 10px 24px rgba(0,0,0,.05);
        color: #52525b;
        font-size: .72rem;
        font-weight: 600;
    }

    .tag-chip:nth-child(2) { margin-left: 34px; }
    .tag-chip:nth-child(3) { margin-left: 12px; }

    @keyframes driftTags {
        50% { transform: translate(-8px, 10px); }
    }

    .bg-orders {
        background:
            radial-gradient(circle at 75% 25%, rgba(59,130,246,.2), transparent 28%),
            radial-gradient(circle at 45% 50%, rgba(34,197,94,.15), transparent 32%);
    }

    .order-board {
        position: absolute;
        top: 34px;
        right: 28px;
        width: 170px;
        display: grid;
        gap: 10px;
    }

    .order-row {
        height: 38px;
        border-radius: 12px;
        background: rgba(255,255,255,.84);
        border: 1px solid rgba(0,0,0,.05);
        box-shadow: 0 10px 24px rgba(0,0,0,.04);
        position: relative;
        overflow: hidden;
    }

    .order-row::before {
        content: "";
        position: absolute;
        top: 0;
        left: -70%;
        width: 70%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(59,130,246,.18), transparent);
        animation: scanOrder 2.7s infinite;
    }

    .order-row:nth-child(2)::before { animation-delay: .45s; }
    .order-row:nth-child(3)::before { animation-delay: .9s; }

    @keyframes scanOrder {
        to { left: 120%; }
    }

    .bg-sales {
        background:
            radial-gradient(circle at 72% 12%, rgba(34,197,94,.18), transparent 30%),
            radial-gradient(circle at 20% 30%, rgba(6,182,212,.15), transparent 28%);
    }

    .sales-chart {
        position: absolute;
        top: 34px;
        right: 30px;
        height: 120px;
        display: flex;
        gap: 10px;
        align-items: flex-end;
    }

    .sales-bar {
        width: 18px;
        border-radius: 999px 999px 0 0;
        background: linear-gradient(to top, #22c55e, #86efac);
        box-shadow: 0 8px 24px rgba(34,197,94,.22);
        animation: salesPulse 2.2s ease-in-out infinite alternate;
    }

    .sales-bar:nth-child(1) { height: 48px; }
    .sales-bar:nth-child(2) { height: 82px; animation-delay: .2s; }
    .sales-bar:nth-child(3) { height: 64px; animation-delay: .4s; }
    .sales-bar:nth-child(4) { height: 106px; animation-delay: .6s; }

    @keyframes salesPulse {
        from { transform: scaleY(.72); opacity: .75; }
        to { transform: scaleY(1); opacity: 1; }
    }

    .bg-debt {
        background:
            radial-gradient(circle at 70% 20%, rgba(239,68,68,.18), transparent 30%),
            radial-gradient(circle at 30% 25%, rgba(249,115,22,.16), transparent 30%);
    }

    .debt-orbits {
        position: absolute;
        top: 32px;
        right: 35px;
        width: 130px;
        height: 130px;
        border-radius: 999px;
        border: 1px solid rgba(239,68,68,.16);
        animation: rotateSlow 12s linear infinite;
    }

    .debt-orbits::before,
    .debt-orbits::after {
        content: "";
        position: absolute;
        border-radius: 999px;
    }

    .debt-orbits::before {
        inset: 22px;
        border: 1px solid rgba(249,115,22,.22);
    }

    .debt-orbits::after {
        width: 14px;
        height: 14px;
        background: #ef4444;
        top: 13px;
        left: 50%;
        transform: translateX(-50%);
        box-shadow: 0 0 20px rgba(239,68,68,.45);
    }

    @keyframes rotateSlow {
        to { transform: rotate(360deg); }
    }

    .bg-clients {
        background:
            radial-gradient(circle at 75% 20%, rgba(99,102,241,.18), transparent 30%),
            radial-gradient(circle at 28% 36%, rgba(14,165,233,.14), transparent 28%);
    }

    .client-nodes {
        position: absolute;
        top: 34px;
        right: 34px;
        width: 150px;
        height: 120px;
    }

    .node {
        position: absolute;
        width: 34px;
        height: 34px;
        border-radius: 999px;
        background: rgba(255,255,255,.9);
        border: 1px solid rgba(0,0,0,.05);
        box-shadow: 0 10px 22px rgba(0,0,0,.06);
        animation: nodeFloat 3s ease-in-out infinite;
    }

    .node::after {
        content: "";
        position: absolute;
        inset: 10px;
        border-radius: 999px;
        background: #6366f1;
    }

    .node.n1 { top: 10px; left: 12px; }
    .node.n2 { top: 55px; left: 68px; animation-delay: .3s; }
    .node.n3 { top: 18px; right: 8px; animation-delay: .6s; }

    .client-link {
        position: absolute;
        height: 2px;
        background: rgba(99,102,241,.18);
        transform-origin: left center;
    }

    .l1 { width: 70px; top: 42px; left: 42px; transform: rotate(22deg); }
    .l2 { width: 72px; top: 36px; left: 84px; transform: rotate(-20deg); }

    @keyframes nodeFloat {
        50% { transform: translateY(-8px); }
    }

    @media (max-width: 1024px) {
        .bento-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .col-span-3 {
            grid-column: span 2;
        }
    }

    @media (max-width: 768px) {
        .ops-shell {
            padding: 28px 16px 56px;
        }

        .ops-header {
            display: block;
        }

        .ops-summary-pill {
            display: inline-flex;
            margin-top: 16px;
        }

        .bento-grid {
            grid-template-columns: 1fr;
            grid-auto-rows: 18rem;
        }

        .col-span-1,
        .col-span-2,
        .col-span-3 {
            grid-column: span 1;
        }

        .overview-bottom {
            gap: 14px;
        }

        .mini-metrics {
            display: none;
        }

        .bento-desc {
            max-width: 100%;
        }
    }
</style>

<div class="ops-shell">
    <div class="ops-header">
        <div>
            <h1 class="ops-title">Panel de Operaciones</h1>
            <p class="ops-sub">
                Seguimiento de cotizaciones, propuestas, órdenes de servicio, ventas, cartera y cuentas pendientes.
            </p>
        </div>

        <div class="ops-summary-pill">
            {{ $fmt($actividadHoyCount) }} movimientos hoy
        </div>
    </div>

    <div class="bento-grid">

        <a href="{{ $auditoriaUrl }}" class="bento-card col-span-2 card-overview">
            <div class="overview-grid"></div>

            <div class="pipeline-flow">
                <div class="pipeline-line pipeline-blue"></div>
                <div class="pipeline-line pipeline-orange"></div>
            </div>

            <div class="overview-layout">
                <div class="overview-top">
                    <div>
                        <span class="overview-label">Flujo comercial</span>
                        <div class="overview-legend">
                            <span class="legend-item"><span class="legend-dot dot-blue"></span> Cotizaciones</span>
                            <span class="legend-item"><span class="legend-dot dot-orange"></span> Ventas / Cobranza</span>
                        </div>
                    </div>

                    <div class="live-badge">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <circle cx="12" cy="12" r="10"/>
                            <polyline points="12 6 12 12 16 14"/>
                        </svg>
                        Datos reales
                    </div>
                </div>

                <div class="overview-bottom">
                    <div>
                        <div class="bento-icon-wrapper">
                            <div class="bento-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                                    <path d="M4 19V5"/>
                                    <path d="M8 19v-6"/>
                                    <path d="M12 19v-10"/>
                                    <path d="M16 19v-3"/>
                                    <path d="M20 19V8"/>
                                </svg>
                            </div>
                        </div>

                        <h3 class="bento-title">Asistente IA</h3>
                        <p class="bento-desc">
                            <strong>{{ $fmt($cotizacionesVentasCount + $cotizacionesRentasCount) }}</strong> cotizaciones,
                            <strong>{{ $fmt($ventasRealizadasCount) }}</strong> ventas y conversión del
                            <strong>{{ $conversionRate }}%</strong>.
                        </p>
                    </div>

                    <div class="mini-metrics">
                        <div class="mini-metric">
                            <span>Ventas</span>
                            <div class="mini-bars">
                                <i class="h2"></i><i class="h4"></i><i class="h3"></i>
                            </div>
                        </div>

                        <div class="mini-metric">
                            <span>Órdenes</span>
                            <div class="mini-bars">
                                <i class="h1"></i><i class="h3"></i><i class="h2"></i>
                            </div>
                        </div>
                    </div>

                    <div class="gauge">
                        <svg viewBox="0 0 36 36">
                            <path class="gauge-bg"
                                  d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
                            <path class="gauge-bar"
                                  stroke-dasharray="{{ max(0, min(100, $conversionRate)) }}, 100"
                                  d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
                        </svg>
                        <div class="gauge-value">{{ $conversionRate }}%</div>
                    </div>
                </div>
            </div>
        </a>

        <a href="{{ $clientesUrl }}" class="bento-card col-span-1">
            <div class="bento-bg bg-clients">
                <div class="client-nodes">
                    <div class="client-link l1"></div>
                    <div class="client-link l2"></div>
                    <div class="node n1"></div>
                    <div class="node n2"></div>
                    <div class="node n3"></div>
                </div>
            </div>

            <div class="bento-inner">
                <div class="bento-content">
                    <div class="bento-icon-wrapper">
                        <div class="bento-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                                <circle cx="9" cy="7" r="4"/>
                                <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                            </svg>
                        </div>
                    </div>

                    <h3 class="bento-title">Cartera de Clientes</h3>
                    <p class="bento-desc">
                        <strong>{{ $fmt($clientesCount) }}</strong> clientes activos en cartera.
                    </p>
                </div>

                <div class="bento-cta">
                    Abrir clientes
                    <svg class="bento-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M5 12h14"/><path d="m12 5 7 7-7 7"/>
                    </svg>
                </div>
            </div>
        </a>

        <a href="{{ $propuestasUrl }}" class="bento-card col-span-1">
            <div class="bento-bg bg-tags">
                <div class="tag-cloud">
                    <span class="tag-chip">PROPUESTA</span>
                    <span class="tag-chip">PRECIO</span>
                    <span class="tag-chip">CIERRE</span>
                </div>
            </div>

            <div class="bento-inner">
                <div class="bento-content">
                    <div class="bento-icon-wrapper">
                        <div class="bento-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                                <path d="M20.59 13.41 11 3.83A2 2 0 0 0 9.59 3H4v5.59A2 2 0 0 0 4.59 10l9.58 9.59a2 2 0 0 0 2.83 0l3.59-3.59a2 2 0 0 0 0-2.83z"/>
                                <circle cx="7.5" cy="7.5" r=".5"/>
                            </svg>
                        </div>
                    </div>

                    <h3 class="bento-title">Cotizaciones de Venta</h3>
                    <p class="bento-desc">
                        <strong>{{ $fmt($cotizacionesVentasCount) }}</strong> propuestas comerciales creadas.
                    </p>
                </div>

                <div class="bento-cta">
                    Ver propuestas
                    <svg class="bento-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M5 12h14"/><path d="m12 5 7 7-7 7"/>
                    </svg>
                </div>
            </div>
        </a>

        <a href="{{ $ordenesUrl }}" class="bento-card col-span-2">
            <div class="bento-bg bg-orders">
                <div class="order-board">
                    <div class="order-row"></div>
                    <div class="order-row"></div>
                    <div class="order-row"></div>
                </div>
            </div>

            <div class="bento-inner">
                <div class="bento-content">
                    <div class="bento-icon-wrapper">
                        <div class="bento-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                                <path d="M9 11l3 3L22 4"/>
                                <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
                            </svg>
                        </div>
                    </div>

                    <h3 class="bento-title">Órdenes de Servicio</h3>
                    <p class="bento-desc">
                        <strong>{{ $fmt($ordenesPendientesCount) }}</strong> pendientes,
                        <strong>{{ $fmt($ordenesProcesoCount) }}</strong> en proceso y
                        <strong>{{ $fmt($ordenesTerminadasCount) }}</strong> terminadas.
                    </p>
                </div>

                <div class="bento-cta">
                    Gestionar órdenes
                    <svg class="bento-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M5 12h14"/><path d="m12 5 7 7-7 7"/>
                    </svg>
                </div>
            </div>
        </a>

        <a href="{{ $ventasUrl }}" class="bento-card col-span-2">
            <div class="bento-bg bg-sales">
                <div class="sales-chart">
                    <div class="sales-bar"></div>
                    <div class="sales-bar"></div>
                    <div class="sales-bar"></div>
                    <div class="sales-bar"></div>
                </div>
            </div>

            <div class="bento-inner">
                <div class="bento-content">
                    <div class="bento-icon-wrapper">
                        <div class="bento-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                                <circle cx="9" cy="21" r="1"/>
                                <circle cx="20" cy="21" r="1"/>
                                <path d="M1 1h4l2.68 13.39A2 2 0 0 0 9.64 16h7.72a2 2 0 0 0 2-1.61L21 6H6"/>
                            </svg>
                        </div>
                    </div>

                    <h3 class="bento-title">Ventas Realizadas</h3>
                    <p class="bento-desc">
                        <strong>{{ $fmt($ventasRealizadasCount) }}</strong> ventas registradas,
                        total vendido <strong>${{ number_format($ventasTotal, 2) }}</strong>.
                    </p>
                </div>

                <div class="bento-cta">
                    Ver ventas
                    <svg class="bento-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M5 12h14"/><path d="m12 5 7 7-7 7"/>
                    </svg>
                </div>
            </div>
        </a>

        <a href="{{ $deudoresUrl }}" class="bento-card col-span-1">
            <div class="bento-bg bg-debt">
                <div class="debt-orbits"></div>
            </div>

            <div class="bento-inner">
                <div class="bento-content">
                    <div class="bento-icon-wrapper">
                        <div class="bento-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                                <rect x="2" y="5" width="20" height="14" rx="2"/>
                                <path d="M2 10h20"/>
                                <path d="M7 15h.01"/>
                                <path d="M11 15h2"/>
                            </svg>
                        </div>
                    </div>

                    <h3 class="bento-title">Control de Deudores</h3>
                    <p class="bento-desc">
                        <strong>{{ $fmt($deudoresCount) }}</strong> cuentas pendientes,
                        deuda total <strong>${{ number_format($deudaTotal, 2) }}</strong>.
                    </p>
                </div>

                <div class="bento-cta">
                    Revisar cartera vencida
                    <svg class="bento-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M5 12h14"/><path d="m12 5 7 7-7 7"/>
                    </svg>
                </div>
            </div>
        </a>

        <a href="{{ $bentoUrl }}" class="bento-card col-span-1">
            <div class="bento-bg bg-quotes">
                <div class="quote-stack">
                    <div class="quote-paper">
                        <b>RENTA</b>
                        <div class="quote-line"></div>
                        <div class="quote-line short"></div>
                    </div>
                    <div class="quote-paper">
                        <b>EQUIPO</b>
                        <div class="quote-line"></div>
                        <div class="quote-line short"></div>
                    </div>
                    <div class="quote-paper">
                        <b>CLIENTE</b>
                        <div class="quote-line"></div>
                        <div class="quote-line short"></div>
                    </div>
                </div>
            </div>

            <div class="bento-inner">
                <div class="bento-content">
                    <div class="bento-icon-wrapper">
                        <div class="bento-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                <path d="M14 2v6h6"/>
                                <path d="M8 13h8"/>
                                <path d="M8 17h5"/>
                            </svg>
                        </div>
                    </div>

                    <h3 class="bento-title">Cotizaciones de Renta</h3>
                    <p class="bento-desc">
                        <strong>{{ $fmt($cotizacionesRentasCount) }}</strong> cotizaciones de renta registradas.
                    </p>
                </div>

                <div class="bento-cta">
                    Abrir rentas
                    <svg class="bento-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M5 12h14"/><path d="m12 5 7 7-7 7"/>
                    </svg>
                </div>
            </div>
        </a>

    </div>
</div>
@endsection