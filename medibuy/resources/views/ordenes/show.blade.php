@extends('layouts.app')
@section('title','Orden de Servicio')
@section('titulo','Orden de Servicio')

@section('content')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@500;600;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

<style>
    :root {
        --bg: #f9fafb;
        --card: #ffffff;
        --ink: #333333;
        --ink-dark: #111111;
        --muted: #888888;
        --line: #ebebeb;
        --blue: #007aff;
        --blue-soft: #e6f0ff;
        --success: #15803d;
        --success-soft: #e6ffe6;
        --danger: #ff4a4a;
        --danger-soft: #ffebeb;
        --font-main: 'Quicksand', sans-serif;
    }

    /* Reset y Base */
    body {
        background-color: var(--bg);
        font-family: var(--font-main);
        color: var(--ink);
        margin: 0;
        -webkit-font-smoothing: antialiased;
    }

    .wrap {
        max-width: 1400px;
        margin: 0 auto;
        padding: 40px 24px;
    }

    /* Animaciones */
    .animate-enter {
        animation: fadeIn 0.6s ease-out forwards;
        opacity: 0;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Header */
    .header-flex {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        margin-bottom: 48px;
        gap: 20px;
        flex-wrap: wrap;
    }

    .page-title {
        font-size: 32px;
        font-weight: 700;
        color: var(--ink-dark);
        margin: 0 0 8px 0;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .page-subtitle {
        color: var(--muted);
        font-size: 16px;
        font-weight: 500;
    }

    /* Cards */
    .card-ux {
        background: var(--card);
        border: 1px solid var(--line);
        border-radius: 16px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.02);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        height: 100%;
        overflow: hidden;
    }

    .card-ux:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.04);
    }

    .card-ux-header {
        padding: 24px 32px;
        border-bottom: 1px solid var(--line);
        font-weight: 700;
        color: var(--ink-dark);
        font-size: 18px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .card-ux-body {
        padding: 32px;
    }

    /* Grid de Datos */
    .data-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 24px;
    }

    .data-item {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .data-label {
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        color: var(--muted);
        letter-spacing: 0.5px;
    }

    .data-val {
        font-size: 15px;
        font-weight: 600;
        color: var(--ink-dark);
    }

    /* Inputs y Botones */
    .input-compact {
        border: 1px solid var(--line);
        border-radius: 8px;
        padding: 12px 16px;
        font-family: var(--font-main);
        font-size: 14px;
        width: 100%;
        transition: all 0.2s;
        background: #fff;
    }

    .input-compact:focus {
        outline: none;
        border-color: var(--blue);
        box-shadow: 0 0 0 3px var(--blue-soft);
    }

    .btn-ux {
        padding: 12px 24px;
        border-radius: 999px;
        font-weight: 700;
        font-size: 14px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
        text-decoration: none;
        border: none;
        font-family: var(--font-main);
    }

    .btn-ux:active {
        transform: scale(0.98);
    }

    .btn-primary {
        background: var(--blue);
        color: #ffffff;
    }

    .btn-outline {
        background: #ffffff;
        color: var(--blue);
        border: 1px solid var(--blue);
    }

    .btn-ghost {
        background: transparent;
        color: #555;
    }

    .btn-ghost:hover {
        background: #f9fafb;
    }

    /* Badges */
    .badge-ux {
        padding: 6px 14px;
        border-radius: 999px;
        font-weight: 700;
        font-size: 12px;
    }

    .badge-success { background: var(--success-soft); color: var(--success); }
    .badge-info { background: var(--blue-soft); color: var(--blue); }
    .badge-danger { background: var(--danger-soft); color: var(--danger); }

    /* Tablas */
    .table-container {
        overflow-x: auto;
    }

    .parts-table {
        width: 100%;
        border-collapse: collapse;
    }

    .parts-table th {
        text-align: left;
        padding: 16px;
        border-bottom: 2px solid var(--line);
        color: var(--muted);
        font-size: 12px;
        text-transform: uppercase;
    }

    .parts-table td {
        padding: 16px;
        border-bottom: 1px solid var(--line);
        font-size: 14px;
    }

    /* Money Box */
    .money-box {
        margin-top: 24px;
        display: flex;
        flex-direction: column;
        gap: 8px;
        max-width: 350px;
        margin-left: auto;
    }

    .money-row {
        display: flex;
        justify-content: space-between;
        padding: 12px 20px;
        background: #fcfcfc;
        border-radius: 10px;
        border: 1px solid var(--line);
    }

    .money-row.total {
        background: var(--ink-dark);
        color: #fff;
        border: none;
        font-size: 18px;
        font-weight: 700;
    }

    /* Visuals */
    .photo-stack {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .img-frame {
        width: 100%;
        aspect-ratio: 4/3;
        border-radius: 12px;
        background: #fdfdfd;
        border: 1px solid var(--line);
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }

    .img-frame img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .text-box {
        background: #f9fafb;
        border-radius: 12px;
        padding: 20px;
        line-height: 1.6;
        color: var(--ink);
        border: 1px solid var(--line);
    }

    /* Layout Grid */
    .main-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 32px;
    }

    @media (max-width: 992px) {
        .main-grid { grid-template-columns: 1fr; }
        .header-flex { flex-direction: column; align-items: flex-start; }
    }
</style>

@php
    $ordenId = (int)($orden->id ?? 0);
    $tecnicoNombre = optional($orden->tecnico)->name ?? optional($orden->user)->name ?? '—';
    $clienteNombre = trim((optional($orden->cliente)->nombre ?? '') . ' ' . (optional($orden->cliente)->apellido ?? '')) ?: '—';
    $money = fn($n) => '$' . number_format((float)($n ?? 0), 2);
    $partidas = is_string($orden->remision_partidas) ? json_decode($orden->remision_partidas, true) : ($orden->remision_partidas ?? []);
    $mtoPreventivo = is_string($orden->mto_preventivo) ? json_decode($orden->mto_preventivo, true) : ($orden->mto_preventivo ?? []);
    $mtoRealizado = is_string($orden->mto_realizado) ? json_decode($orden->mto_realizado, true) : ($orden->mto_realizado ?? []);
@endphp

<div class="wrap" x-data="OrdenUI()" x-init="init()">
    
    <div x-show="toastShow" 
         x-transition 
         style="position: fixed; top: 24px; right: 24px; z-index: 2000; padding: 16px 24px; border-radius: 12px; color: white; font-weight: 600; box-shadow: 0 10px 30px rgba(0,0,0,0.1);"
         :style="{ background: toastOk ? 'var(--success)' : 'var(--danger)' }"
         x-text="toastMsg">
    </div>

    <div class="header-flex animate-enter">
        <div>
            <h1 class="page-title">
                Orden de Servicio 
                <span class="badge-ux badge-success">#{{ $ordenId }}</span>
            </h1>
            <div class="page-subtitle">
                <i class="bi bi-person"></i> {{ $clienteNombre }} 
                <span style="margin: 0 12px; color: #eee;">|</span> 
                <i class="bi bi-person-badge"></i> {{ $tecnicoNombre }}
            </div>
        </div>
        <div style="display: flex; gap: 12px;">
            <button type="button"
            onclick="history.back()" class="btn-ux btn-ghost">
                <i class="bi bi-arrow-left"></i> Regresar
            </a>
            </button>
            @if($ordenId > 0)
                <a href="{{ route('ordenes.edit', $ordenId) }}" class="btn-ux btn-outline">
                    <i class="bi bi-pencil"></i> Editar
                </a>
            @endif
        </div>
    </div>

    <div class="main-grid">
        <div style="display: flex; flex-direction: column; gap: 32px;">
            
            <div class="card-ux animate-enter" style="animation-delay: 0.1s;">
                <div class="card-ux-header"><i class="bi bi-cpu"></i> Detalle del Equipo</div>
                <div class="card-ux-body">
                    <div class="data-grid">
                        <div class="data-item"><span class="data-label">Equipo</span><span class="data-val">{{ $orden->equipo ?? '—' }}</span></div>
                        <div class="data-item"><span class="data-label">Marca</span><span class="data-val">{{ $orden->marca ?? '—' }}</span></div>
                        <div class="data-item"><span class="data-label">Modelo</span><span class="data-val">{{ $orden->modelo ?? '—' }}</span></div>
                        <div class="data-item"><span class="data-label">Serie</span><span class="data-val">{{ $orden->numero_serie ?? '—' }}</span></div>
                    </div>
                    <hr style="border: 0; border-top: 1px solid var(--line); margin: 32px 0;">
                    <div class="data-grid">
                        <div class="data-item"><span class="data-label">Entrada</span><span class="data-val">{{ $orden->fecha_entrada ?? '—' }}</span></div>
                        <div class="data-item"><span class="data-label">Próximo Mto.</span><span class="data-val">{{ $orden->proximo_mantenimiento_fecha ?? '—' }}</span></div>
                    </div>
                </div>
            </div>

            <div class="card-ux animate-enter" style="animation-delay: 0.2s;">
                <div class="card-ux-header"><i class="bi bi-chat-left-text"></i> Diagnóstico y Acciones</div>
                <div class="card-ux-body">
                    <span class="data-label">Observaciones Iniciales</span>
                    <div class="text-box" style="margin: 12px 0 32px 0;">
                        {{ $orden->observaciones ?? 'Sin observaciones registradas.' }}
                    </div>

                    <span class="data-label">Checklist de Servicio</span>
                    <div style="display: flex; flex-direction: column; gap: 12px; margin-top: 12px;">
                        @forelse($mtoPreventivo as $item)
                            <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px; border-bottom: 1px solid var(--line);">
                                <span style="font-weight: 500;">{{ $item['item'] ?? '—' }}</span>
                                <span class="badge-ux badge-info">{{ $item['estatus'] ?? 'OK' }}</span>
                            </div>
                        @empty
                            <p style="color: var(--muted); font-style: italic;">No hay checklist disponible.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="card-ux animate-enter" style="animation-delay: 0.3s;">
                <div class="card-ux-header"><i class="bi bi-cart-check"></i> Partidas y Costos</div>
                <div class="card-ux-body">
                    <div class="table-container">
                        <table class="parts-table">
                            <thead>
                                <tr>
                                    <th>Descripción</th>
                                    <th>Cantidad</th>
                                    <th>Unitario</th>
                                    <th>Importe</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($partidas as $p)
                                <tr>
                                    <td style="font-weight: 600;">{{ $p['descripcion'] ?? '—' }}</td>
                                    <td>{{ $p['cantidad'] ?? 1 }}</td>
                                    <td>{{ $money($p['precio_unitario'] ?? 0) }}</td>
                                    <td style="font-weight: 700;">{{ $money($p['importe'] ?? 0) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="money-box">
                        <div class="money-row"><span>Subtotal</span><strong>{{ $money($orden->remision_subtotal ?? 0) }}</strong></div>
                        <div class="money-row"><span>IVA</span><strong>{{ $money($orden->remision_iva ?? 0) }}</strong></div>
                        <div class="money-row total"><span>Total Final</span><strong>{{ $money($orden->remision_total_pagar ?? 0) }}</strong></div>
                    </div>
                </div>
            </div>
        </div>

        <div style="display: flex; flex-direction: column; gap: 32px;">
            
            <div class="card-ux animate-enter" style="animation-delay: 0.4s;">
                <div class="card-ux-header"><i class="bi bi-camera"></i> Galería</div>
                <div class="card-ux-body">
                    <div class="photo-stack">
                        @foreach(['foto_url', 'foto_url_2', 'foto_url_3'] as $f)
                            <div class="img-frame">
                                @if(!empty($$f))
                                    <img src="{{ $$f }}" alt="Evidencia">
                                @else
                                    <i class="bi bi-image" style="font-size: 32px; color: var(--line);"></i>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="card-ux animate-enter" style="animation-delay: 0.5s;">
                <div class="card-ux-header"><i class="bi bi-shield-check"></i> Validación</div>
                <div class="card-ux-body">
                    <div style="background: #f0f7ff; border: 1px dashed var(--blue); border-radius: 12px; padding: 24px; text-align: center;">
                        <span class="data-label">Token de Seguridad</span>
                        <div id="tkVal" style="font-size: 24px; font-weight: 700; color: var(--blue); margin: 8px 0; letter-spacing: 2px;">
                            {{ $orden->codigo_validacion_servicio ?? '---' }}
                        </div>
                        <button @click="copyToken('{{ $orden->codigo_validacion_servicio }}')" class="btn-ux btn-primary" style="width: 100%; justify-content: center; margin-top: 12px;">
                            <i class="bi bi-clipboard"></i> Copiar Token
                        </button>
                    </div>
                </div>
            </div>

            <div class="card-ux animate-enter" style="animation-delay: 0.6s;">
                <div class="card-ux-header"><i class="bi bi-file-earmark-pdf"></i> Documentos</div>
                <div class="card-ux-body" style="display: flex; flex-direction: column; gap: 12px;">
                    <a href="{{ route('ordenes.pdf', $ordenId) }}" target="_blank" class="btn-ux btn-outline" style="width: 100%; justify-content: center;">
                        Orden de Servicio PDF
                    </a>
                    <a href="{{ route('ordenes.remision.pdf', $ordenId) }}" target="_blank" class="btn-ux btn-ghost" style="width: 100%; justify-content: center; border: 1px solid var(--line);">
                        Remisión PDF
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('OrdenUI', () => ({
            toastShow: false,
            toastMsg: '',
            toastOk: true,

            showToast(msg, ok = true) {
                this.toastMsg = msg;
                this.toastOk = ok;
                this.toastShow = true;
                setTimeout(() => this.toastShow = false, 3000);
            },

            async copyToken(val) {
                if (!val) return;
                try {
                    await navigator.clipboard.writeText(val);
                    this.showToast('Código copiado al portapapeles');
                } catch (err) {
                    this.showToast('Error al copiar', false);
                }
            }
        }));
    });
</script>
@endsection