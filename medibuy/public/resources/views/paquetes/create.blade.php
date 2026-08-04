@extends('layouts.app')

@section('title', 'Crear paquete')
@section('header', 'Crear paquete')

@section('content')
<style>
    :root {
        --pk-bg: #f7f5fb;
        --pk-surface: #ffffff;
        --pk-border: #e5e7eb;
        --pk-muted: #6b7280;
        --pk-ink: #0f172a;
        --pk-accent: #22c55e;
        --pk-accent-soft: #e0f2fe;
        --pk-radius-lg: 18px;
        --pk-shadow-soft: 0 18px 40px rgba(15, 23, 42, 0.10);
    }

    .pk-page {
        max-width: 1120px;
        margin: 24px auto 40px;
        padding: 0 16px 80px;
        font-family: "Söhne", "Circular Std", "Poppins", system-ui, -apple-system,
            "Segoe UI", "Helvetica Neue", Arial, sans-serif;
        color: var(--pk-ink);
    }

    .pk-header {
        margin-bottom: 18px;
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        justify-content: space-between;
        align-items: flex-end;
    }
    .pk-header-main {
        min-width: 240px;
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
    .pk-header-meta {
        font-size: 12px;
        color: var(--pk-muted);
        background: #eef2ff;
        border-radius: 999px;
        padding: 6px 12px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        white-space: nowrap;
        box-shadow: 0 6px 14px rgba(148, 163, 184, .45);
    }
    .pk-header-dot {
        width: 7px;
        height: 7px;
        border-radius: 999px;
        background: #22c55e;
    }

    .pk-card {
        background: var(--pk-surface);
        border-radius: var(--pk-radius-lg);
        box-shadow: var(--pk-shadow-soft);
        padding: 16px 18px;
        margin-bottom: 16px;
        border: 1px solid rgba(148, 163, 184, 0.26);
    }

    .pk-card-header {
        display: flex;
        align-items: baseline;
        justify-content: space-between;
        gap: 10px;
        margin-bottom: 12px;
    }
    .pk-card-title {
        font-size: 15px;
        font-weight: 600;
    }
    .pk-card-sub {
        font-size: 13px;
        color: var(--pk-muted);
    }

    .pk-label {
        font-size: 13px;
        font-weight: 500;
        margin-bottom: 4px;
    }
    .pk-input {
        width: 100%;
        border-radius: 12px;
        border: 1px solid var(--pk-border);
        padding: 8px 11px;
        font-size: 13px;
        outline: none;
        transition: border-color .18s ease, box-shadow .18s ease, background-color .18s ease;
        background: #fbfbff;
    }
    .pk-input:focus {
        border-color: #4f46e5;
        box-shadow: 0 0 0 1px rgba(79, 70, 229, 0.35);
        background: #ffffff;
    }

    .pk-errors {
        background: #fef2f2;
        border-radius: 12px;
        border: 1px solid #fecaca;
        padding: 10px 14px;
        font-size: 13px;
        color: #b91c1c;
        margin-bottom: 14px;
    }
    .pk-errors ul {
        margin: 0;
        padding-left: 18px;
    }

    /* Toolbar tabla */
    .pk-table-toolbar {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        align-items: center;
        margin-bottom: 10px;
    }

    .pk-table-search {
        position: relative;
        flex: 1;
        min-width: 200px;
        max-width: 320px;
    }
    .pk-table-search input {
        padding-left: 30px;
    }
    .pk-table-search-icon {
        position: absolute;
        left: 9px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 0;
        pointer-events: none;
    }
    .pk-table-search-icon svg {
        width: 13px;
        height: 13px;
        stroke: var(--pk-muted);
    }

    .pk-toggle {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        color: var(--pk-muted);
        cursor: pointer;
        user-select: none;
    }
    .pk-toggle input {
        accent-color: #0f172a;
    }

    /* Tabla productos */
    .pk-table-wrap {
        border-radius: 14px;
        border: 1px solid var(--pk-border);
        overflow: hidden;
        max-height: 420px;       /* 🔹 Scroll interno, no toda la página */
        overflow-y: auto;
        background: #f9fafb;
    }
    .pk-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }
    .pk-table thead {
        position: sticky;
        top: 0;
        z-index: 1;
        background: linear-gradient(90deg,#fdf2ff,#e0f2fe);
    }
    .pk-table th, .pk-table td {
        padding: 8px 9px;
        border-bottom: 1px solid #e5e7eb;
        vertical-align: middle;
        white-space: nowrap;
        background-color: #ffffff;
    }
    .pk-table th {
        font-weight: 600;
        text-align: left;
        font-size: 12px;
        color: #4b5563;
    }
    .pk-table tbody tr:nth-child(even) td {
        background-color: #f8fafc;
    }
    .pk-table tbody tr:hover td {
        background-color: #eef2ff;
    }
    .pk-center {
        text-align: center;
    }

    .pk-row-selected td {
        background-color: #ecfdf3 !important;
        box-shadow: inset 3px 0 0 #16a34a;
    }

    .pk-checkbox {
        width: 16px;
        height: 16px;
        border-radius: 6px;
        border: 1px solid #cbd5e1;
        accent-color: #0f172a;
        cursor: pointer;
    }

    .pk-input-sm {
        width: 70px;
        border-radius: 9px;
        font-size: 12px;
        padding: 4px 6px;
        text-align: center;
    }

    .pk-table-footnote {
        padding: 6px 10px;
        font-size: 11px;
        color: var(--pk-muted);
        background: #f3f4f6;
        border-top: 1px dashed #e5e7eb;
    }

    /* Barra de acciones sticky (no tienes que bajar hasta abajo de todo) */
    .pk-actions-bar {
        position: sticky;
        bottom: 0;
        padding-top: 10px;
        margin-top: 16px;
        background: linear-gradient(to top, rgba(249,250,251,0.95), rgba(249,250,251,0.3));
        backdrop-filter: blur(6px);
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
        border-top: 1px solid #e5e7eb;
    }

    .pk-actions-text {
        font-size: 12px;
        color: var(--pk-muted);
    }

    .pk-actions-buttons {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
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
    .pk-btn-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    .pk-btn-icon svg {
        width: 14px;
        height: 14px;
        stroke-width: 1.8;
    }
    .pk-btn:active {
        transform: translateY(1px) scale(.98);
    }
    .pk-btn-outline {
        background: transparent;
        color: var(--pk-ink);
        border: 1px solid #cbd5f5;
    }
    .pk-btn-outline:hover {
        background: #e5e7eb;
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
    .pk-btn-green {
        background: #16a34a;
        color: #ffffff;
        box-shadow: 0 10px 22px rgba(22,163,74,.35);
    }
    .pk-btn-green:hover {
        background: #15803d;
        box-shadow: 0 14px 26px rgba(22,163,74,.45);
    }

    @media (max-width: 768px) {
        .pk-card {
            padding: 14px 12px;
        }
        .pk-table th:nth-child(2),
        .pk-table td:nth-child(2) {
            max-width: 180px;
            white-space: normal;
        }
    }
</style>

<div class="pk-page" style="margin-top:100px;">
    <div class="pk-header">
        <div class="pk-header-main">
            <div class="pk-kicker">Paquetes</div>
            <h1 class="pk-title">Crear paquete</h1>
            <p class="pk-subtitle">
                Ponle un nombre y elige los equipos con su orden exacto (1, 2, 3...).
            </p>
        </div>
        <div class="pk-header-meta">
            <span class="pk-header-dot"></span>
            {{ $productos->count() }} productos disponibles
        </div>
    </div>

    @if ($errors->any())
        <div class="pk-errors">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>• {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('paquetes.store') }}" method="POST">
        @csrf

        {{-- Datos generales --}}
        <div class="pk-card">
            <div class="pk-card-header">
                <div>
                    <div class="pk-card-title">Información del paquete</div>
                    <div class="pk-card-sub">Este nombre se usará en todo el sistema.</div>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-md-6 col-lg-5">
                    <label class="pk-label">Nombre del paquete</label>
                    <input type="text"
                           name="nombre"
                           class="pk-input"
                           value="{{ old('nombre') }}"
                           placeholder="Ej. Torre laparoscopía 4K"
                           required>
                </div>
            </div>
        </div>

        {{-- Productos del paquete --}}
        <div class="pk-card">
            <div class="pk-card-header">
                <div>
                    <div class="pk-card-title">Selecciona los equipos</div>
                    <div class="pk-card-sub">
                        Marca los equipos que pertenezcan al paquete y define su orden de aparición.
                    </div>
                </div>
            </div>

            <div class="pk-table-toolbar">
                <div class="pk-table-search">
                    <span class="pk-table-search-icon">
                        {{-- ícono de lupa (SVG) --}}
                        <svg viewBox="0 0 24 24" fill="none">
                            <circle cx="11" cy="11" r="6" stroke="currentColor"></circle>
                            <line x1="15.5" y1="15.5" x2="20" y2="20" stroke="currentColor"
                                  stroke-linecap="round"></line>
                        </svg>
                    </span>
                    <input type="text"
                           id="pk-search"
                           class="pk-input"
                           placeholder="Buscar por equipo, modelo o marca">
                </div>

                <label class="pk-toggle">
                    <input type="checkbox" id="pk-only-selected">
                    <span>Mostrar solo seleccionados</span>
                </label>
            </div>

            <div class="pk-table-wrap">
                <table class="pk-table" id="pk-table">
                    <thead>
                        <tr>
                            <th class="pk-center" style="width:40px;">
                                <input type="checkbox" id="pk-select-all" class="pk-checkbox">
                            </th>
                            <th>Equipo</th>
                            <th>Modelo</th>
                            <th>Marca</th>
                            <th class="pk-center" style="width:80px;">Orden</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($productos as $producto)
                            @php
                                $checked = in_array($producto->id, old('productos', []));
                                $ordenOld = old('orden.'.$producto->id, $loop->iteration);
                            @endphp
                            <tr class="pk-row {{ $checked ? 'pk-row-selected' : '' }}">
                                <td class="pk-center">
                                    <input type="checkbox"
                                           class="pk-checkbox pk-row-check"
                                           name="productos[]"
                                           value="{{ $producto->id }}"
                                           {{ $checked ? 'checked' : '' }}>
                                </td>
                                <td data-col="equipo">{{ $producto->tipo_equipo }}</td>
                                <td data-col="modelo">{{ $producto->modelo }}</td>
                                <td data-col="marca">{{ $producto->marca }}</td>
                                <td class="pk-center">
                                    <input type="number"
                                           name="orden[{{ $producto->id }}]"
                                           class="pk-input pk-input-sm"
                                           min="1"
                                           value="{{ $ordenOld }}">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="pk-table-footnote">
                El campo <strong>Orden</strong> controla la secuencia en que se mostrarán los equipos
                (1 = primero, 2 = segundo, etc.).
            </div>
        </div>

        {{-- Barra de acciones sticky (siempre visible aunque la tabla sea larga) --}}
        <div class="pk-actions-bar">
            <div class="pk-actions-text">
                Selecciona los equipos y haz clic en <strong>Guardar paquete</strong> cuando termines.
            </div>
            <div class="pk-actions-buttons">
                <a href="{{ url()->previous() }}" class="pk-btn pk-btn-outline">
                    <span class="pk-btn-icon">
                        {{-- ícono flecha izquierda --}}
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <polyline points="15 6 9 12 15 18" stroke-linecap="round" stroke-linejoin="round"></polyline>
                        </svg>
                    </span>
                    <span>Cancelar</span>
                </a>

                <button type="submit" class="pk-btn pk-btn-green">
                    <span class="pk-btn-icon">
                        {{-- ícono check --}}
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <polyline points="5 13 9 17 19 7" stroke-linecap="round" stroke-linejoin="round"></polyline>
                        </svg>
                    </span>
                    <span>Guardar paquete</span>
                </button>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput   = document.getElementById('pk-search');
    const table         = document.getElementById('pk-table');
    const rows          = table.querySelectorAll('tbody tr.pk-row');
    const selectAll     = document.getElementById('pk-select-all');
    const rowChecks     = document.querySelectorAll('.pk-row-check');
    const onlySelected  = document.getElementById('pk-only-selected');

    // 🔍 Búsqueda por equipo/modelo/marca
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const term = this.value.toLowerCase();

            rows.forEach(row => {
                const equipo = row.querySelector('[data-col="equipo"]')?.textContent.toLowerCase() || '';
                const modelo = row.querySelector('[data-col="modelo"]')?.textContent.toLowerCase() || '';
                const marca  = row.querySelector('[data-col="marca"]')?.textContent.toLowerCase() || '';

                const match = equipo.includes(term) || modelo.includes(term) || marca.includes(term);

                // Si está activado "solo seleccionados", además de hacer match tiene que estar checked
                const chk = row.querySelector('.pk-row-check');
                const isChecked = chk && chk.checked;

                if (onlySelected.checked) {
                    row.style.display = (match && isChecked) ? '' : 'none';
                } else {
                    row.style.display = match ? '' : 'none';
                }
            });
        });
    }

    // ✅ Toggle "solo seleccionados"
    if (onlySelected) {
        onlySelected.addEventListener('change', function () {
            const term = (searchInput?.value || '').toLowerCase();

            rows.forEach(row => {
                const equipo = row.querySelector('[data-col="equipo"]')?.textContent.toLowerCase() || '';
                const modelo = row.querySelector('[data-col="modelo"]')?.textContent.toLowerCase() || '';
                const marca  = row.querySelector('[data-col="marca"]')?.textContent.toLowerCase() || '';
                const chk    = row.querySelector('.pk-row-check');
                const isChecked = chk && chk.checked;
                const match = equipo.includes(term) || modelo.includes(term) || marca.includes(term);

                if (this.checked) {
                    row.style.display = (match && isChecked) ? '' : 'none';
                } else {
                    row.style.display = match ? '' : 'none';
                }
            });
        });
    }

    // ☑️ Seleccionar / deseleccionar todos
    if (selectAll) {
        selectAll.addEventListener('change', function () {
            rowChecks.forEach(chk => {
                chk.checked = selectAll.checked;
                toggleRowSelected(chk.closest('tr'), chk.checked);
            });
        });
    }

    // Resaltar filas seleccionadas
    rowChecks.forEach(chk => {
        chk.addEventListener('change', function () {
            toggleRowSelected(this.closest('tr'), this.checked);
        });
    });

    function toggleRowSelected(row, isChecked) {
        if (!row) return;
        if (isChecked) {
            row.classList.add('pk-row-selected');
        } else {
            row.classList.remove('pk-row-selected');
        }
    }
});
</script>
@endsection
