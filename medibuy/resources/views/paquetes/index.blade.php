@extends('layouts.app')

@section('title', 'Paquetes')
@section('header', 'Paquetes')

@section('content')
<style>
    :root {
        --pk-bg: #f6f5fb;
        --pk-surface: #ffffff;
        --pk-border: #e5e7eb;
        --pk-muted: #6b7280;
        --pk-ink: #0f172a;
        --pk-accent: #22c55e;
        --pk-accent-soft: #dcfce7;
        --pk-radius-lg: 20px;
        --pk-shadow-soft: 0 18px 40px rgba(15, 23, 42, 0.08);
    }

    .pk-page {
        max-width: 1120px;
        margin: 24px auto 40px;
        padding: 0 16px 32px;
        font-family: "Söhne", "Circular Std", "Poppins", system-ui, -apple-system,
            "Segoe UI", "Helvetica Neue", Arial, sans-serif;
        color: var(--pk-ink);
    }

    .pk-header {
        display: flex;
        flex-wrap: wrap;
        align-items: flex-end;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 20px;
    }
    .pk-kicker {
        font-size: 11px;
        letter-spacing: 0.16em;
        text-transform: uppercase;
        color: var(--pk-muted);
        margin-bottom: 4px;
    }
    .pk-title {
        font-size: 26px;
        font-weight: 600;
        margin: 0 0 4px;
    }
    .pk-subtitle {
        font-size: 13px;
        color: var(--pk-muted);
        margin: 0;
    }

    .pk-btn {
        border: none;
        border-radius: 999px;
        padding: 8px 16px;
        font-size: 13px;
        font-weight: 500;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: transform .14s ease, box-shadow .14s ease, background-color .14s ease, opacity .14s ease;
        text-decoration: none;
        white-space: nowrap;
    }
    .pk-btn:active {
        transform: translateY(1px) scale(.98);
    }
    .pk-btn-black {
        background: #000000;
        color: #ffffff;
        box-shadow: 0 10px 22px rgba(0,0,0,.22);
    }
    .pk-btn-black:hover {
        background: #020617;
        box-shadow: 0 14px 26px rgba(15,23,42,.4);
    }
    .pk-btn-outline {
        background: transparent;
        color: var(--pk-ink);
        border: 1px solid #d1d5db;
    }
    .pk-btn-outline:hover {
        background: #e5e7eb;
    }
    .pk-btn-danger {
        background: #ef4444;
        color: #ffffff;
        box-shadow: 0 10px 22px rgba(239, 68, 68, .35);
    }
    .pk-btn-danger:hover {
        background: #b91c1c;
        box-shadow: 0 14px 26px rgba(185, 28, 28, .45);
    }

    .pk-card {
        background: var(--pk-surface);
        border-radius: var(--pk-radius-lg);
        box-shadow: var(--pk-shadow-soft);
        padding: 18px 18px 14px;
        margin-bottom: 18px;
        border: 1px solid rgba(148, 163, 184, 0.28);
    }

    .pk-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        margin-bottom: 8px;
    }
    .pk-card-title {
        font-size: 15px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .pk-badge-count {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 22px;
        height: 22px;
        border-radius: 999px;
        padding: 0 8px;
        font-size: 11px;
        font-weight: 500;
        background: var(--pk-accent-soft);
        color: #166534;
    }
    .pk-card-meta {
        font-size: 12px;
        color: var(--pk-muted);
    }

    .pk-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 4px;
        margin-top: 6px;
        margin-bottom: 4px;
    }
    .pk-tag {
        font-size: 11px;
        border-radius: 999px;
        padding: 4px 9px;
        background: #f3f4ff;
        color: #4b5563;
        border: 1px solid #e5e7ff;
        max-width: 100%;
        white-space: nowrap;
        text-overflow: ellipsis;
        overflow: hidden;
    }

    .pk-row-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        margin-top: 8px;
    }
    .pk-footer-left {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 11px;
        color: var(--pk-muted);
    }
    .pk-dot {
        width: 6px;
        height: 6px;
        border-radius: 999px;
        background: #10b981;
    }
    .pk-order-pill {
        border-radius: 999px;
        padding: 3px 8px;
        font-size: 11px;
        background: #ecfeff;
        color: #0369a1;
        border: 1px solid #bae6fd;
    }

    .pk-actions-inline {
        display: flex;
        flex-wrap: wrap;
        justify-content: flex-end;
        gap: 6px;
    }

    .pk-empty {
        text-align: center;
        padding: 40px 16px;
        border-radius: 18px;
        border: 1px dashed #d1d5db;
        background: rgba(249, 250, 251, .9);
        font-size: 13px;
        color: var(--pk-muted);
    }

    @media (max-width: 768px) {
        .pk-header {
            align-items: flex-start;
        }
        .pk-row-footer {
            flex-direction: column;
            align-items: flex-start;
        }
        .pk-actions-inline {
            width: 100%;
            justify-content: flex-start;
        }
    }
</style>

<div class="pk-page" style="margin-top:100px;">

    {{-- Encabezado --}}
    <div class="pk-header">
        <div>
            <div class="pk-kicker">Catálogo</div>
            <h1 class="pk-title">Paquetes de equipo</h1>
            <p class="pk-subtitle">
                Administra los paquetes armados (cámara, cabezal, fuente de luz, etc.) con su orden visual definido.
            </p>
        </div>

        <div style="display:flex; gap:8px;">
            <a href="{{ route('paquetes.create') }}" class="pk-btn pk-btn-black">
                <span>＋ Nuevo paquete</span>
            </a>
        </div>
    </div>

    {{-- Mensajes de éxito --}}
    @if (session('success'))
        <div class="alert alert-success" style="font-size:13px; border-radius:12px;">
            {{ session('success') }}
        </div>
    @endif

    {{-- Listado de paquetes --}}
    @if ($paquetes->isEmpty())
        <div class="pk-empty">
            <p style="margin-bottom:8px;">Todavía no has creado ningún paquete.</p>
            <a href="{{ route('paquetes.create') }}" class="pk-btn pk-btn-black">
                Crear el primer paquete
            </a>
        </div>
    @else
        @foreach ($paquetes as $paquete)
            <div class="pk-card">
                <div class="pk-card-header">
                    <div>
                        <div class="pk-card-title">
                            {{ $paquete->nombre }}
                            <span class="pk-badge-count">
                                {{ $paquete->productos_count ?? $paquete->productos->count() }} equipos
                            </span>
                        </div>
                        <div class="pk-card-meta">
                            {{ $paquete->created_at ? $paquete->created_at->format('d/m/Y') : 'Sin fecha' }}
                        </div>
                    </div>

                    {{-- Acciones principales --}}
                    <div class="pk-actions-inline">
                        <a href="{{ route('paquetes.show', $paquete) }}" class="pk-btn pk-btn-outline">
                            Ver detalle
                        </a>
                        <a href="{{ route('paquetes.edit', $paquete) }}" class="pk-btn pk-btn-outline">
                            Editar
                        </a>
                        <form action="{{ route('paquetes.destroy', $paquete) }}"
                              method="POST"
                              onsubmit="return confirm('¿Eliminar este paquete? Esta acción no se puede deshacer.');"
                              style="margin:0;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="pk-btn pk-btn-danger">
                                Eliminar
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Tags con resumen de equipos --}}
                @php
                    $equipos = $paquete->productos->take(6);
                @endphp

                @if ($equipos->count() > 0)
                    <div class="pk-tags">
                        @foreach ($equipos as $producto)
                            <div class="pk-tag" title="{{ $producto->tipo_equipo }} {{ $producto->modelo }} {{ $producto->marca }}">
                                {{ $producto->tipo_equipo }}
                                @if ($producto->modelo)
                                    · {{ $producto->modelo }}
                                @endif
                                @if ($producto->marca)
                                    · {{ $producto->marca }}
                                @endif
                            </div>
                        @endforeach

                        @if ($paquete->productos->count() > $equipos->count())
                            <div class="pk-tag">
                                + {{ $paquete->productos->count() - $equipos->count() }} más
                            </div>
                        @endif
                    </div>
                @else
                    <div class="pk-card-meta" style="margin-top:4px;">
                        Este paquete aún no tiene equipos asignados.
                    </div>
                @endif

                <div class="pk-row-footer">
                    <div class="pk-footer-left">
                        <span class="pk-dot"></span>
                        <span>Orden interno respetado (1 = primero, 2 = segundo…)</span>
                        <span class="pk-order-pill">Orden definido en la tabla pivote</span>
                    </div>
                    <div style="font-size:11px; color:var(--pk-muted);">
                        Usa este paquete directamente en tus flujos de cotización o armado de equipos.
                    </div>
                </div>
            </div>
        @endforeach
    @endif

</div>
@endsection
