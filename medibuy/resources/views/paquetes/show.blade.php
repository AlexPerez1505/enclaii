@extends('layouts.app')

@section('title', 'Detalle del paquete')
@section('header', 'Detalle del paquete')

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
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        margin-bottom: 18px;
    }
    .pk-title-wrap {
        min-width: 0;
    }
    .pk-kicker {
        font-size: 11px;
        letter-spacing: 0.16em;
        text-transform: uppercase;
        color: var(--pk-muted);
        margin-bottom: 4px;
    }
    .pk-title {
        font-size: 24px;
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
        margin-bottom: 10px;
    }

    .pk-chip {
        border-radius: 999px;
        padding: 3px 9px;
        font-size: 11px;
        background: var(--pk-accent-soft);
        color: #166534;
        border: 1px solid #bbf7d0;
    }

    .pk-meta {
        font-size: 12px;
        color: var(--pk-muted);
    }

    .pk-table-wrap {
        border-radius: 18px;
        border: 1px solid var(--pk-border);
        overflow: hidden;
        margin-top: 6px;
    }
    .pk-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }
    .pk-table thead {
        background: linear-gradient(90deg,#fdf2ff,#e0f2fe);
    }
    .pk-table th,
    .pk-table td {
        padding: 8px 10px;
        border-bottom: 1px solid #e5e7eb;
        vertical-align: middle;
        white-space: nowrap;
    }
    .pk-table th {
        font-weight: 600;
        text-align: left;
        font-size: 12px;
        color: #4b5563;
    }
    .pk-table tbody tr:nth-child(even) {
        background: #f9fafb;
    }
    .pk-table tbody tr:hover {
        background: #eef2ff;
    }
    .pk-center {
        text-align: center;
    }
    .pk-right {
        text-align: right;
    }

    .pk-footnote {
        padding: 9px 11px;
        font-size: 11px;
        color: var(--pk-muted);
        background: #f9fafb;
        border-top: 1px dashed #e5e7eb;
    }

    @media (max-width: 768px) {
        .pk-header {
            flex-direction: column;
            align-items: flex-start;
        }
        .pk-card {
            padding: 16px 14px;
        }
        .pk-table-wrap {
            overflow-x: auto;
        }
    }
</style>

<div class="pk-page" style="margin-top:100px;">
    {{-- Header --}}
    <div class="pk-header">
        <div class="pk-title-wrap">
            <div class="pk-kicker">Paquete</div>
            <h1 class="pk-title">{{ $paquete->nombre }}</h1>
            <p class="pk-subtitle">
                Vista detallada del paquete y el orden en que se muestran los equipos.
            </p>
        </div>

        <div style="display:flex; flex-wrap:wrap; gap:8px;">
            {{-- Volver a donde estabas (no usa cotizaciones) --}}
            <a href="{{ url()->previous() }}" class="pk-btn pk-btn-outline">
                ← Volver
            </a>

            <a href="{{ route('paquetes.edit', $paquete) }}" class="pk-btn pk-btn-black">
                Editar paquete
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

    @if (session('success'))
        <div class="alert alert-success" style="font-size:13px; border-radius:12px;">
            {{ session('success') }}
        </div>
    @endif

    {{-- Card principal --}}
    <div class="pk-card">
        <div class="pk-card-header">
            <div>
                <div class="pk-meta">
                    Creado:
                    {{ $paquete->created_at ? $paquete->created_at->format('d/m/Y H:i') : 'Sin fecha' }}
                </div>
                <div class="pk-meta">
                    {{ $paquete->productos->count() }} equipo(s) asignado(s).
                </div>
            </div>
            <div class="pk-chip">
                Orden interno respetado (1 → n)
            </div>
        </div>

        {{-- Tabla de equipos --}}
        @if ($paquete->productos->isEmpty())
            <p class="pk-meta" style="margin-bottom:0;">
                Este paquete aún no tiene equipos asignados.
            </p>
        @else
            <div class="pk-table-wrap">
                <table class="pk-table">
                    <thead>
                        <tr>
                            <th style="width:80px;">Orden</th>
                            <th>Equipo</th>
                            <th>Modelo</th>
                            <th>Marca</th>
                            <th class="pk-right" style="width:120px;">Precio</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($paquete->productos as $producto)
                            <tr>
                                <td class="pk-center">
                                    {{ $producto->pivot->orden ?? '-' }}
                                </td>
                                <td>{{ $producto->tipo_equipo }}</td>
                                <td>{{ $producto->modelo }}</td>
                                <td>{{ $producto->marca }}</td>
                                <td class="pk-right">
                                    @if (!is_null($producto->precio))
                                        $ {{ number_format($producto->precio, 2) }}
                                    @else
                                        —
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="pk-footnote">
                    El <strong>orden</strong> corresponde al valor guardado en
                    la tabla pivote <code>paquete_producto.orden</code>. Así puedes mostrar siempre
                    primero la cámara, luego el cabezal, luego la fuente de luz, etc.
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
