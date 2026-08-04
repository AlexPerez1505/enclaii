@extends('layouts.app')

@section('title', 'Editar paquete')
@section('header', 'Editar paquete')

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
        padding: 0 16px 40px;
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

    /* GRID PRINCIPAL: TABLA IZQ / RESUMEN DER */
    .pk-main-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.7fr) minmax(340px, 1.3fr);
        gap: 20px;
        align-items: flex-start;
    }
    .pk-col-left {
        min-width: 0;
    }
    .pk-col-right {
        min-width: 0;
        position: sticky;
        top: 88px;
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
        max-height: 420px;
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
    .pk-center { text-align: center; }
    .pk-right  { text-align: right;  }

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
        border-radius: 12px;
        margin-top: 10px;
    }

    /* Resumen (columna derecha) */
    .pk-summary-card {
        border-radius: 18px;
        border: 1px solid rgba(148, 163, 184, 0.35);
        padding: 14px 16px;
        background: linear-gradient(135deg,#f9fafb,#eef2ff);
        box-shadow: 0 14px 30px rgba(15,23,42,0.12);
        display: grid;
        grid-template-columns: minmax(0, 1.2fr) minmax(0, 1.4fr);
        gap: 12px 16px;
        min-height: 220px;
    }

    .pk-summary-main {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .pk-summary-row {
        display: flex;
        justify-content: space-between;
        font-size: 13px;
    }

    .pk-summary-label {
        color: var(--pk-muted);
    }

    .pk-summary-value {
        font-weight: 600;
    }

    .pk-summary-total {
        font-weight: 600;
        font-size: 16px;
        display: inline-flex;
        gap: 4px;
        align-items: baseline;
    }

    .pk-summary-total span:first-child {
        font-size: 12px;
    }

    .pk-summary-order-title {
        font-size: 12px;
        font-weight: 600;
        color: var(--pk-muted);
        text-transform: uppercase;
        letter-spacing: .08em;
        margin-bottom: 6px;
    }

    .pk-order-list {
        list-style: none;
        padding: 0;
        margin: 0;
        max-height: 210px;
        overflow-y: auto;
        font-size: 12px;
    }

    .pk-order-item {
        display: flex;
        justify-content: space-between;
        gap: 8px;
        padding: 4px 0;
        border-bottom: 1px dashed rgba(148,163,184,0.4);
    }

    .pk-order-left {
        display: inline-flex;
        gap: 6px;
        align-items: baseline;
    }

    .pk-order-index {
        font-weight: 600;
        color: #4f46e5;
    }

    .pk-order-name {
        color: var(--pk-ink);
    }

    .pk-order-price {
        white-space: nowrap;
        font-variant-numeric: tabular-nums;
    }

    .pk-summary-side-footnote {
        font-size: 11px;
        color: var(--pk-muted);
        margin-top: 8px;
    }

    /* Botones en columna derecha */
    .pk-actions-side {
        margin-top: 12px;
        display: flex;
        justify-content: flex-end;
        gap: 8px;
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
    .pk-btn-green {
        background: #16a34a;
        color: #ffffff;
        box-shadow: 0 10px 22px rgba(22,163,74,.35);
    }
    .pk-btn-green:hover {
        background: #15803d;
        box-shadow: 0 14px 26px rgba(22,163,74,.45);
    }

    @media (max-width: 960px) {
        .pk-main-grid {
            grid-template-columns: minmax(0, 1fr);
        }
        .pk-col-right {
            position: static;
        }
        .pk-summary-card {
            grid-template-columns: minmax(0, 1fr);
        }
        .pk-actions-side {
            justify-content: flex-start;
        }
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

@php
    // Productos ya asociados al paquete (ordenados por pivot->orden desde el modelo)
    $productosSeleccionados = $paquete->productos->keyBy('id');
@endphp

<div class="pk-page" style="margin-top:100px;">
    <div class="pk-header">
        <div class="pk-header-main">
            <div class="pk-kicker">Paquetes</div>
            <h1 class="pk-title">Editar paquete</h1>
            <p class="pk-subtitle">
                Ajusta el nombre, los equipos incluidos y el orden del paquete
                <strong>{{ $paquete->nombre }}</strong>.
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

    <form action="{{ route('paquetes.update', $paquete) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- Datos generales --}}
        <div class="pk-card" >
            <div class="pk-card-header">
                <div>
                    <div class="pk-card-title">Información del paquete</div>
                    <div class="pk-card-sub">Nombre interno/comercial del paquete.</div>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-md-6 col-lg-5">
                    <label class="pk-label">Nombre del paquete</label>
                    <input type="text"
                           name="nombre"
                           class="pk-input"
                           value="{{ old('nombre', $paquete->nombre) }}"
                           required>
                </div>
            </div>
        </div>

        {{-- GRID: tabla izquierda + resumen derecha --}}
        <div class="pk-main-grid">
            {{-- Columna izquierda: tabla --}}
            <div class="pk-col-left">
                <div class="pk-card">
                    <div class="pk-card-header">
                        <div>
                            <div class="pk-card-title">Equipos incluidos</div>
                            <div class="pk-card-sub">
                                Marca los equipos que pertenecen al paquete, ajusta el <strong>orden</strong>
                                y visualiza el total del paquete.
                            </div>
                        </div>
                    </div>

                    <div class="pk-table-toolbar">
                        <div class="pk-table-search">
                            <span class="pk-table-search-icon">
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
                                    <th class="pk-right" style="width:110px;">Precio</th>
                                    <th class="pk-center" style="width:80px;">Orden</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($productos as $producto)
                                    @php
                                        $pivotItem  = $productosSeleccionados->get($producto->id);
                                        $checked    = $pivotItem !== null;
                                        $ordenValor = old(
                                            'orden.'.$producto->id,
                                            $pivotItem ? $pivotItem->pivot->orden : $loop->iteration
                                        );
                                        $precio = $producto->precio ?? 0;
                                    @endphp
                                    <tr class="pk-row {{ $checked ? 'pk-row-selected' : '' }}"
                                        data-precio="{{ $precio }}">
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
                                        <td class="pk-right">
                                            $ {{ number_format($precio, 2) }}
                                        </td>
                                        <td class="pk-center">
                                            <input type="number"
                                                name="orden[{{ $producto->id }}]"
                                                class="pk-input pk-input-sm pk-input-orden"
                                                min="1"
                                                value="{{ $ordenValor }}">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="pk-table-footnote">
                        Ajusta la columna <strong>Orden</strong> sin volver a seleccionar todo.  
                        El resumen de la derecha se actualiza automáticamente con el orden y el total.
                    </div>
                </div>
            </div>

            {{-- Columna derecha: resumen + botones --}}
            <div class="pk-col-right">
                <div class="pk-summary-card">
                    <div class="pk-summary-main">
                        <div class="pk-summary-row">
                            <span class="pk-summary-label">Productos seleccionados</span>
                            <span class="pk-summary-value" id="pk-selected-count">
                                {{ $paquete->productos->count() }}
                            </span>
                        </div>
                        <div class="pk-summary-row">
                            <span class="pk-summary-label">Total estimado del paquete</span>
                            <span class="pk-summary-total">
                                <span>$</span>
                                <span id="pk-total">
                                    {{ number_format($paquete->productos->sum('precio'), 2) }}
                                </span>
                            </span>
                        </div>
                        <div style="font-size:11px; color:var(--pk-muted); margin-top:4px;">
                            El total considera el precio actual de cada producto seleccionado (1 unidad por equipo).
                        </div>
                    </div>

                    <div>
                        <div class="pk-summary-order-title">Orden del paquete</div>
                        <ol id="pk-order-list" class="pk-order-list">
                            @foreach ($paquete->productos as $p)
                                <li class="pk-order-item">
                                    <div class="pk-order-left">
                                        <span class="pk-order-index">#{{ $p->pivot->orden }}</span>
                                        <span class="pk-order-name">
                                            {{ $p->tipo_equipo }} {{ $p->modelo }}
                                        </span>
                                    </div>
                                    <span class="pk-order-price">
                                        $ {{ number_format($p->precio ?? 0, 2) }}
                                    </span>
                                </li>
                            @endforeach
                        </ol>
                    </div>
                </div>

                <div class="pk-summary-side-footnote">
                    Revisa el orden y el total del paquete y haz clic en <strong>Guardar cambios</strong>.
                </div>

                <div class="pk-actions-side">
                    <a href="{{ url()->previous() }}" class="pk-btn pk-btn-outline">
                        <span class="pk-btn-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <polyline points="15 6 9 12 15 18"
                                          stroke-linecap="round"
                                          stroke-linejoin="round"></polyline>
                            </svg>
                        </span>
                        <span>Cancelar</span>
                    </a>

                    <button type="submit" class="pk-btn pk-btn-green">
                        <span class="pk-btn-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <polyline points="5 13 9 17 19 7"
                                          stroke-linecap="round"
                                          stroke-linejoin="round"></polyline>
                            </svg>
                        </span>
                        <span>Guardar cambios</span>
                    </button>
                </div>
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
    const totalEl       = document.getElementById('pk-total');
    const countEl       = document.getElementById('pk-selected-count');
    const orderListEl   = document.getElementById('pk-order-list');

    function recalcSummary() {
        let total = 0;
        let selected = [];

        rows.forEach(row => {
            const chk = row.querySelector('.pk-row-check');
            if (!chk || !chk.checked) return;

            const precio  = parseFloat(row.dataset.precio || '0') || 0;
            const equipo  = row.querySelector('[data-col="equipo"]')?.textContent.trim() || '';
            const modelo  = row.querySelector('[data-col="modelo"]')?.textContent.trim() || '';
            const ordenIn = row.querySelector('.pk-input-orden');
            let   orden   = 0;

            if (ordenIn) {
                orden = parseInt(ordenIn.value, 10);
                if (isNaN(orden)) orden = 0;
            }

            total += precio;
            selected.push({
                equipo,
                modelo,
                precio,
                orden: orden || 99999
            });
        });

        // Ordenar por orden
        selected.sort((a, b) => a.orden - b.orden);

        // Actualizar DOM total y count
        if (totalEl) {
            totalEl.textContent = total.toFixed(2);
        }
        if (countEl) {
            countEl.textContent = selected.length;
        }

        // Actualizar lista de orden
        if (orderListEl) {
            orderListEl.innerHTML = '';
            if (selected.length === 0) {
                const li = document.createElement('li');
                li.className = 'pk-order-item';
                li.innerHTML = `
                    <div class="pk-order-left">
                        <span class="pk-order-name" style="color:var(--pk-muted);">
                            No hay equipos seleccionados.
                        </span>
                    </div>
                `;
                orderListEl.appendChild(li);
            } else {
                selected.forEach(item => {
                    const li = document.createElement('li');
                    li.className = 'pk-order-item';
                    li.innerHTML = `
                        <div class="pk-order-left">
                            <span class="pk-order-index">#${item.orden || '-'}</span>
                            <span class="pk-order-name">${item.equipo} ${item.modelo}</span>
                        </div>
                        <span class="pk-order-price">$ ${item.precio.toFixed(2)}</span>
                    `;
                    orderListEl.appendChild(li);
                });
            }
        }
    }

    // Búsqueda
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const term = this.value.toLowerCase();

            rows.forEach(row => {
                const equipo = row.querySelector('[data-col="equipo"]')?.textContent.toLowerCase() || '';
                const modelo = row.querySelector('[data-col="modelo"]')?.textContent.toLowerCase() || '';
                const marca  = row.querySelector('[data-col="marca"]')?.textContent.toLowerCase() || '';
                const chk    = row.querySelector('.pk-row-check');
                const isChecked = chk && chk.checked;

                const match = equipo.includes(term) || modelo.includes(term) || marca.includes(term);

                if (onlySelected && onlySelected.checked) {
                    row.style.display = (match && isChecked) ? '' : 'none';
                } else {
                    row.style.display = match ? '' : 'none';
                }
            });
        });
    }

    // Mostrar solo seleccionados
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

    // Seleccionar / deseleccionar todos
    if (selectAll) {
        selectAll.addEventListener('change', function () {
            rowChecks.forEach(chk => {
                chk.checked = selectAll.checked;
                toggleRowSelected(chk.closest('tr'), chk.checked);
            });
            recalcSummary();
        });
    }

    // Marcar filas seleccionadas + recalcular resumen
    rowChecks.forEach(chk => {
        chk.addEventListener('change', function () {
            toggleRowSelected(this.closest('tr'), this.checked);
            recalcSummary();
        });
    });

    // Cambios en orden => actualizar resumen
    document.querySelectorAll('.pk-input-orden').forEach(input => {
        input.addEventListener('input', function () {
            recalcSummary();
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

    // Primer cálculo al cargar
    recalcSummary();
});
</script>
@endsection
