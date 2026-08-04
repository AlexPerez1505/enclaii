@extends('layouts.app')

@section('title', 'Crear paquete')
@section('header', 'Crear paquete')

@section('content')
<!-- SortableJS (Ligero y potente para el Drag & Drop) -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap');

    :root {
        /* Paleta Enterprise (Stripe / Tailwind UI) */
        --bg-body: #f8fafc;        /* Slate 50 */
        --bg-surface: #ffffff;     /* White */
        --border-color: #e2e8f0;   /* Slate 200 */
        --text-main: #0f172a;      /* Slate 900 */
        --text-muted: #64748b;     /* Slate 500 */
        --brand-primary: #0f172a;  /* Negro/Slate profundo para botones primarios */
        --brand-hover: #1e293b;    /* Slate 800 */
        --accent: #2563eb;         /* Azul corporativo para selecciones/foco */
        --accent-bg: #eff6ff;      /* Azul muy claro para fila seleccionada */
        
        --radius-sm: 6px;
        --radius-md: 10px;
        --radius-lg: 12px;
        
        --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.08), 0 2px 4px -2px rgba(0, 0, 0, 0.04);
        --shadow-float: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
    }

    /* Reset & Base */
    .ent-wrapper {
        max-width: 1024px;
        margin: 32px auto 100px;
        padding: 0 16px;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        color: var(--text-main);
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
        animation: fadeIn 0.4s ease-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Header Corporativo */
    .ent-header {
        margin-bottom: 32px;
    }
    .ent-title {
        font-size: 24px;
        font-weight: 600;
        letter-spacing: -0.025em;
        margin: 0 0 4px 0;
        color: var(--text-main);
    }
    .ent-subtitle {
        font-size: 14px;
        color: var(--text-muted);
        margin: 0;
    }

    /* Tarjetas (Cards) */
    .ent-card {
        background: var(--bg-surface);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-sm);
        margin-bottom: 24px;
        overflow: hidden;
    }
    .ent-card-body {
        padding: 24px;
    }
    .ent-card-header {
        padding: 20px 24px;
        border-bottom: 1px solid var(--border-color);
        background: #fbfcfd;
    }
    .ent-card-title {
        font-size: 15px;
        font-weight: 600;
        margin: 0;
    }

    /* Formularios */
    .ent-label {
        display: block;
        font-size: 13px;
        font-weight: 500;
        color: #334155;
        margin-bottom: 8px;
    }
    .ent-input {
        width: 100%;
        padding: 10px 14px;
        font-size: 14px;
        line-height: 1.5;
        color: var(--text-main);
        background-color: var(--bg-surface);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-sm);
        transition: border-color 0.15s ease, box-shadow 0.15s ease;
        outline: none;
    }
    .ent-input:focus {
        border-color: var(--accent);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
    }

    /* Toolbar de la tabla */
    .ent-toolbar {
        display: flex;
        flex-direction: column;
        gap: 16px;
        padding: 16px 24px;
        border-bottom: 1px solid var(--border-color);
        background: var(--bg-surface);
    }
    @media (min-width: 640px) {
        .ent-toolbar {
            flex-direction: row;
            justify-content: space-between;
            align-items: center;
        }
    }
    .ent-search {
        position: relative;
        width: 100%;
        max-width: 320px;
    }
    .ent-search input {
        padding-left: 36px;
        height: 38px;
    }
    .ent-search svg {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-muted);
        width: 16px;
        height: 16px;
    }
    .ent-toggle {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        color: var(--text-muted);
        cursor: pointer;
        user-select: none;
    }
    .ent-toggle input {
        accent-color: var(--accent);
        width: 16px; height: 16px;
        cursor: pointer;
    }

    /* Tabla Responsive */
    .ent-table-container {
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch; /* Suavidad en iOS */
        max-height: 500px;
        overflow-y: auto;
    }
    .ent-table {
        width: 100%;
        min-width: 600px; /* Evita que las columnas se aplasten en móvil */
        border-collapse: collapse;
        text-align: left;
        font-size: 14px;
    }
    .ent-table th {
        position: sticky;
        top: 0;
        background: #f8fafc;
        padding: 12px 16px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--text-muted);
        border-bottom: 1px solid var(--border-color);
        z-index: 10;
    }
    .ent-table td {
        padding: 14px 16px;
        border-bottom: 1px solid var(--border-color);
        vertical-align: middle;
        background: var(--bg-surface);
        transition: background-color 0.15s ease;
    }
    
    /* Estado Fila Seleccionada */
    .ent-row-selected td {
        background-color: var(--accent-bg) !important;
    }

    /* Drag Handle */
    .ent-drag-handle {
        color: #cbd5e1;
        cursor: grab;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 4px;
        border-radius: 4px;
        transition: color 0.2s, background 0.2s;
    }
    .ent-drag-handle:hover {
        color: var(--text-main);
        background: #f1f5f9;
    }
    .ent-drag-handle:active {
        cursor: grabbing;
    }
    
    /* Clases SortableJS */
    .sortable-ghost td {
        background: #f1f5f9 !important;
        opacity: 0.6;
    }
    .sortable-drag td {
        background: var(--bg-surface) !important;
        box-shadow: var(--shadow-md);
    }

    /* Checkbox Custom */
    .ent-checkbox {
        width: 16px;
        height: 16px;
        border-radius: 4px;
        border: 1px solid #94a3b8;
        accent-color: var(--accent);
        cursor: pointer;
        margin: 0;
    }

    /* Input de Orden (Readonly) */
    .ent-order-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        border-radius: var(--radius-sm);
        background: #f1f5f9;
        font-size: 13px;
        font-weight: 600;
        color: var(--text-muted);
        border: 1px solid transparent;
        transition: all 0.2s;
    }
    .ent-row-selected .ent-order-badge {
        background: var(--bg-surface);
        color: var(--accent);
        border-color: #bfdbfe;
        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
    }
    
    .hidden-input { display: none; }
    .row-hidden { display: none !important; }

    /* Barra Flotante Inferior (Sticky Action Bar) */
    .ent-action-bar {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        border-top: 1px solid var(--border-color);
        padding: 16px;
        display: flex;
        justify-content: center; /* Centrado en móvil */
        gap: 12px;
        z-index: 50;
        box-shadow: 0 -4px 6px -1px rgba(0, 0, 0, 0.05);
    }
    @media (min-width: 768px) {
        .ent-action-bar {
            bottom: 24px;
            left: 50%;
            transform: translateX(-50%);
            width: 100%;
            max-width: 1024px;
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            justify-content: flex-end; /* Alineado a la derecha en escritorio */
            padding: 16px 24px;
            box-shadow: var(--shadow-float);
        }
    }

    /* Botones */
    .ent-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 10px 18px;
        font-size: 14px;
        font-weight: 500;
        border-radius: var(--radius-sm);
        cursor: pointer;
        transition: all 0.15s ease;
        text-decoration: none;
        border: 1px solid transparent;
    }
    .ent-btn:active {
        transform: translateY(1px);
    }
    .ent-btn-secondary {
        background: var(--bg-surface);
        color: var(--text-main);
        border-color: #cbd5e1;
        box-shadow: var(--shadow-sm);
    }
    .ent-btn-secondary:hover {
        background: #f8fafc;
        border-color: #94a3b8;
    }
    .ent-btn-primary {
        background: var(--brand-primary);
        color: #ffffff;
        box-shadow: var(--shadow-sm);
    }
    .ent-btn-primary:hover {
        background: var(--brand-hover);
    }
</style>

<div class="ent-wrapper">
    <div class="ent-header">
        <h1 class="ent-title">Crear Paquete</h1>
        <p class="ent-subtitle">Define la información general y organiza los equipos del inventario.</p>
    </div>

    @if ($errors->any())
        <div style="background: #fef2f2; border-left: 4px solid #ef4444; padding: 16px; border-radius: 4px; margin-bottom: 24px;">
            <p style="color: #991b1b; font-weight: 500; margin: 0 0 8px 0; font-size: 14px;">Hay errores en el formulario:</p>
            <ul style="margin: 0; padding-left: 20px; color: #b91c1c; font-size: 13px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('paquetes.store') }}" method="POST">
        @csrf

        {{-- Tarjeta 1: Detalles --}}
        <div class="ent-card">
            <div class="ent-card-header">
                <h2 class="ent-card-title">Información del Paquete</h2>
            </div>
            <div class="ent-card-body">
                <div style="max-width: 480px;">
                    <label class="ent-label" for="nombre">Nombre identificador</label>
                    <input type="text" id="nombre" name="nombre" class="ent-input" value="{{ old('nombre') }}" placeholder="Ej. Torre de Laparoscopía Storz" required autofocus>
                </div>
            </div>
        </div>

        {{-- Tarjeta 2: Tabla de Equipos --}}
        <div class="ent-card">
            <div class="ent-card-header" style="display: flex; justify-content: space-between; align-items: center;">
                <h2 class="ent-card-title">Selección de Equipos</h2>
                <span style="font-size: 12px; color: var(--text-muted); background: #f1f5f9; padding: 4px 8px; border-radius: 99px;">
                    Arrastra para ordenar
                </span>
            </div>
            
            <div class="ent-toolbar">
                <div class="ent-search">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                    <input type="text" id="ent-search-input" class="ent-input" placeholder="Buscar por equipo, modelo...">
                </div>
                <label class="ent-toggle">
                    <input type="checkbox" id="ent-filter-selected">
                    <span>Solo seleccionados</span>
                </label>
            </div>

            {{-- Contenedor con Scroll Horizontal para Móviles --}}
            <div class="ent-table-container">
                <table class="ent-table">
                    <thead>
                        <tr>
                            <th style="width: 48px; padding-right: 0;"></th>
                            <th style="width: 48px; text-align: center;">
                                <input type="checkbox" id="ent-select-all" class="ent-checkbox">
                            </th>
                            <th>Equipo</th>
                            <th>Modelo</th>
                            <th>Marca</th>
                            <th style="text-align: center; width: 80px;">Orden</th>
                        </tr>
                    </thead>
                    <tbody id="sortable-list">
                        @foreach ($productos as $producto)
                            @php
                                $checked = in_array($producto->id, old('productos', []));
                                $ordenOld = old('orden.'.$producto->id, '');
                            @endphp
                            <tr class="ent-row {{ $checked ? 'ent-row-selected' : '' }}" data-id="{{ $producto->id }}">
                                <td style="padding-right: 0;">
                                    <div class="ent-drag-handle">
                                        {{-- Icono Grip minimalista --}}
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                            <circle cx="9" cy="5" r="1.5"></circle><circle cx="15" cy="5" r="1.5"></circle>
                                            <circle cx="9" cy="12" r="1.5"></circle><circle cx="15" cy="12" r="1.5"></circle>
                                            <circle cx="9" cy="19" r="1.5"></circle><circle cx="15" cy="19" r="1.5"></circle>
                                        </svg>
                                    </div>
                                </td>
                                <td style="text-align: center;">
                                    <input type="checkbox"
                                           class="ent-checkbox ent-row-check"
                                           name="productos[]"
                                           value="{{ $producto->id }}"
                                           {{ $checked ? 'checked' : '' }}>
                                </td>
                                <td data-col="equipo" style="font-weight: 500;">{{ $producto->tipo_equipo }}</td>
                                <td data-col="modelo" style="color: var(--text-muted);">{{ $producto->modelo }}</td>
                                <td data-col="marca">
                                    <span style="border: 1px solid var(--border-color); padding: 2px 8px; border-radius: 4px; font-size: 12px; color: var(--text-muted);">
                                        {{ $producto->marca }}
                                    </span>
                                </td>
                                <td style="text-align: center;">
                                    {{-- Elemento visual para el usuario --}}
                                    <div class="ent-order-badge">{{ $checked && $ordenOld ? $ordenOld : '-' }}</div>
                                    {{-- Input oculto real que se envía por POST --}}
                                    <input type="number"
                                           name="orden[{{ $producto->id }}]"
                                           class="hidden-input ent-order-input"
                                           value="{{ $ordenOld }}">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Barra de Acción Flotante --}}
        <div class="ent-action-bar">
            <a href="{{ url()->previous() }}" class="ent-btn ent-btn-secondary">Cancelar</a>
            <button type="submit" class="ent-btn ent-btn-primary">
                Guardar Paquete
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput   = document.getElementById('ent-search-input');
    const toggleSelect  = document.getElementById('ent-filter-selected');
    const selectAll     = document.getElementById('ent-select-all');
    const tbody         = document.getElementById('sortable-list');
    const rowChecks     = document.querySelectorAll('.ent-row-check');
    
    // 1. SortableJS (Drag and Drop optimizado para Desktop y Touch)
    new Sortable(tbody, {
        handle: '.ent-drag-handle', // Obliga a agarrar desde los puntitos
        animation: 150, // Animación rápida y corporativa
        ghostClass: 'sortable-ghost',
        dragClass: 'sortable-drag',
        forceFallback: false, // Mejora el rendimiento nativo
        onEnd: function () {
            recalculateOrder();
        }
    });

    // 2. Lógica de Recálculo de Orden (Solo afecta a los seleccionados)
    function recalculateOrder() {
        let currentOrder = 1;
        // Obtenemos las filas visualmente disponibles (sin estar ocultas por el buscador)
        const visibleRows = document.querySelectorAll('tbody tr.ent-row:not(.row-hidden)');
        
        visibleRows.forEach(row => {
            const chk = row.querySelector('.ent-row-check');
            const hiddenInput = row.querySelector('.ent-order-input');
            const badge = row.querySelector('.ent-order-badge');
            
            if (chk.checked) {
                hiddenInput.value = currentOrder;
                badge.textContent = currentOrder;
                currentOrder++;
                row.classList.add('ent-row-selected');
            } else {
                hiddenInput.value = '';
                badge.textContent = '-';
                row.classList.remove('ent-row-selected');
            }
        });
    }

    // 3. Eventos de los Checkboxes
    rowChecks.forEach(chk => {
        chk.addEventListener('change', function () {
            recalculateOrder();
        });
    });

    // 4. Búsqueda y Filtros en tiempo real
    function applyFilters() {
        const term = (searchInput.value || '').toLowerCase().trim();
        const showOnlySelected = toggleSelect.checked;
        const allRows = document.querySelectorAll('tbody tr.ent-row');

        allRows.forEach(row => {
            const equipo = row.querySelector('[data-col="equipo"]').textContent.toLowerCase();
            const modelo = row.querySelector('[data-col="modelo"]').textContent.toLowerCase();
            const marca  = row.querySelector('[data-col="marca"]').textContent.toLowerCase();
            const isChecked = row.querySelector('.ent-row-check').checked;

            const matchesSearch = term === '' || equipo.includes(term) || modelo.includes(term) || marca.includes(term);
            const matchesToggle = showOnlySelected ? isChecked : true;

            if (matchesSearch && matchesToggle) {
                row.classList.remove('row-hidden');
            } else {
                row.classList.add('row-hidden');
            }
        });
        
        recalculateOrder(); // Recalcular orden visual si se ocultan elementos
    }

    if (searchInput) searchInput.addEventListener('input', applyFilters);
    if (toggleSelect) toggleSelect.addEventListener('change', applyFilters);

    // 5. Seleccionar Todos
    if (selectAll) {
        selectAll.addEventListener('change', function () {
            const isChecked = this.checked;
            const visibleRows = document.querySelectorAll('tbody tr.ent-row:not(.row-hidden)');
            
            visibleRows.forEach(row => {
                row.querySelector('.ent-row-check').checked = isChecked;
            });
            recalculateOrder();
        });
    }

    // Inicializar valores al cargar la página
    recalculateOrder();
});
</script>
@endsection