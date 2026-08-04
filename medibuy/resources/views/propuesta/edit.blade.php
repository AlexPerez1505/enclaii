@extends('layouts.app')

@section('title', 'Editar Cotización')
@section('titulo', 'Editar Propuesta')

@section('content')

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fuse.js@6.6.2/dist/fuse.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/propuestaedit.css') }}?v={{ time() }}">

<style>
    .ficha-field-wrapper {
        position: relative;
    }

    #ficha_tecnica_search {
        width: 100%;
    }

    #dropdown_fichas {
        position: absolute;
        left: 0;
        right: 0;
        top: calc(100% + 6px);
        z-index: 3000;
        display: none;
        max-height: 260px;
        overflow-y: auto;
        padding: 6px;
        margin: 0;
        list-style: none;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        box-shadow: 0 18px 40px rgba(15, 23, 42, 0.12);
    }

    .ficha-option {
        padding: 10px 12px;
        border-radius: 10px;
        font-size: 0.9rem;
        font-weight: 600;
        color: #334155;
        cursor: pointer;
        transition: all .18s ease;
    }

    .ficha-option:hover {
        background: #eff6ff;
        color: #007aff;
    }

    .ficha-empty {
        padding: 10px 12px;
        font-size: .88rem;
        color: #64748b;
    }
</style>

<div class="container py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4 animate-entry page-heading">
        <div class="d-flex align-items-center gap-3 flex-wrap">
            <a href="{{ url('/propuestas') }}" class="back-btn">
                <i class="bi bi-arrow-left"></i>
                <span>Regresar a propuestas</span>
            </a>
        </div>

        <div class="text-md-end">
            <h1 class="fw-bold mb-1">Editar Propuesta</h1>
            <p class="text-muted mb-0">Cotización #{{ $propuesta->id }}</p>
        </div>
    </div>

    <form action="{{ route('propuestas.update', $propuesta->id) }}" method="POST" id="form-propuesta">
        @csrf
        @method('PUT')

        <div class="search-wrapper mb-4 mb-md-5 animate-entry delay-1">
            <span class="search-leading-icon"><i class="bi bi-search"></i></span>
            <input
                type="text"
                id="buscador-producto"
                class="premium-input search-input"
                placeholder="Buscar productos, modelos o marcas..."
                autocomplete="off"
            >

            <ul id="lista-productos" class="dropdown-list">
                @foreach($productos as $producto)
                    <li class="dropdown-item"
                        data-id="{{ $producto->id }}"
                        data-nombre="{{ $producto->tipo_equipo }}"
                        data-modelo="{{ $producto->modelo }}"
                        data-marca="{{ $producto->marca }}"
                        data-precio="{{ $producto->precio }}"
                        data-imagen="{{ asset('storage/'.$producto->imagen) }}">
                        <img src="{{ asset('storage/'.$producto->imagen) }}" class="item-img" alt="producto">
                        <div class="flex-grow-1 min-w-0">
                            <div class="fw-bold text-uppercase" style="font-size: 0.9rem;">{{ $producto->tipo_equipo }}</div>
                            <div class="text-muted" style="font-size: 0.8rem;">{{ $producto->marca }} - {{ $producto->modelo }}</div>
                        </div>
                        <div class="fw-bold text-primary">${{ number_format($producto->precio, 2) }}</div>
                    </li>
                @endforeach
            </ul>
        </div>

        <div class="premium-card animate-entry delay-1">
            <div class="card-header-title">
                <span class="section-icon"><i class="bi bi-box-seam"></i></span>
                <span>Productos en Cotización</span>
            </div>

            <div class="table-shell">
                <table id="tabla-productos" class="table-fintech">
                    <thead>
                        <tr>
                            <th>Equipo</th>
                            <th style="width: 110px;">Cantidad</th>
                            <th style="width: 150px;">Sobreprecio</th>
                            <th style="width: 150px;">Subtotal</th>
                            <th class="text-center" style="width: 110px;">Regalo</th>
                            <th class="text-end" style="width: 90px;">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($propuesta->productos as $pp)
                        <tr
                            data-id="{{ $pp->producto_id }}"
                            data-precio="{{ (float)$pp->precio_unitario }}"
                            data-es-regalo="{{ (int)($pp->es_regalo ?? 0) === 1 ? 1 : 0 }}"
                        >
                            <td data-label="Equipo" class="stack-main-cell">
                                <div class="row-title-inline">
                                    <img src="{{ asset('storage/' . $pp->producto->imagen) }}" class="item-img shadow-sm" alt="producto">
                                    <div class="title-copy">
                                        <div class="product-name">{{ $pp->producto->tipo_equipo }}</div>
                                        <div class="product-meta">{{ $pp->producto->marca }} | {{ $pp->producto->modelo }}</div>
                                        <div class="product-price-mini">Precio base: ${{ number_format((float)$pp->precio_unitario, 2) }}</div>
                                    </div>
                                </div>
                            </td>

                            <td data-label="Cantidad" class="stack-inline-cell">
                                <input type="number" name="productos[{{ $pp->producto_id }}][cantidad]" class="premium-input text-center cantidad p-2" value="{{ $pp->cantidad }}" min="1">
                            </td>

                            <td data-label="Sobreprecio" class="stack-inline-cell">
                                <div class="position-relative">
                                    <span class="mini-money-prefix">$</span>
                                    <input type="number" name="productos[{{ $pp->producto_id }}][sobreprecio]" class="premium-input sobreprecio ps-4 p-2" value="{{ (float)$pp->sobreprecio }}" step="0.01">
                                </div>
                            </td>

                            <td data-label="Subtotal" class="subtotal-cell">
                                @if((int)($pp->es_regalo ?? 0) === 1)
                                    <span class="badge-gift"><i class="bi bi-gift"></i> REGALO</span>
                                @else
                                    ${{ number_format((float)$pp->subtotal, 2) }}
                                @endif
                            </td>

                            <td data-label="Regalo" class="text-center stack-switch-cell">
                                <label class="custom-switch">
                                    <input type="checkbox" class="gift-toggle" {{ (int)($pp->es_regalo ?? 0) === 1 ? 'checked' : '' }}>
                                    <span class="switch-slider"></span>
                                </label>
                            </td>

                            <td data-label="Acción" class="text-end stack-action-cell">
                                <button type="button" class="icon-btn-danger border-0" onclick="eliminarFila(this)" title="Eliminar">
                                    <i class="bi bi-trash3"></i>
                                </button>
                            </td>

                            <input type="hidden" class="es_regalo_hidden" name="productos[{{ $pp->producto_id }}][es_regalo]" value="{{ (int)($pp->es_regalo ?? 0) === 1 ? 1 : 0 }}">
                            <input type="hidden" class="precio_unitario" name="productos[{{ $pp->producto_id }}][precio_unitario]" value="{{ (float)$pp->precio_unitario }}">
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-7 mb-4">
                <div class="premium-card h-100 animate-entry delay-2">
                    <div class="card-header-title">
                        <span class="section-icon"><i class="bi bi-file-earmark-text"></i></span>
                        <span>Detalles del Contrato</span>
                    </div>

                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="input-label">Cliente</label>
                            <select name="cliente_id" class="premium-input" required>
                                @foreach($clientes as $cliente)
                                    <option value="{{ $cliente->id }}" {{ $propuesta->cliente_id == $cliente->id ? 'selected' : '' }}>
                                        {{ $cliente->nombre }} {{ $cliente->apellido }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="input-label">Lugar de Instalación</label>
                            <input type="text" name="lugar" class="premium-input" value="{{ $propuesta->lugar }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="input-label">Esquema de Pago</label>
                            <select name="plan" id="plan" class="premium-input">
                                <option value="contado" {{ $propuesta->plan == 'contado' ? 'selected' : '' }}>Pago de Contado</option>
                                <option value="credito" {{ $propuesta->plan == 'credito' ? 'selected' : '' }}>Plan a Crédito</option>
                                <option value="personalizado" {{ $propuesta->plan == 'personalizado' ? 'selected' : '' }}>Plan Personalizado</option>
                                <option value="estatico" {{ $propuesta->plan == 'estatico' ? 'selected' : '' }}>Plan Fijo</option>
                                <option value="dinamico" {{ $propuesta->plan == 'dinamico' ? 'selected' : '' }}>Plan Flexible</option>
                            </select>
                        </div>

                        <div class="col-md-6 ficha-field-wrapper">
                            <label class="input-label">Ficha Técnica a incluir en el PDF</label>
                            <input
                                type="text"
                                id="ficha_tecnica_search"
                                class="premium-input"
                                placeholder="Buscar ficha técnica..."
                                autocomplete="off"
                                value="{{ $propuesta->fichaTecnica ? mb_strtoupper($propuesta->fichaTecnica->nombre, 'UTF-8') : '' }}"
                            >

                            <input
                                type="hidden"
                                name="ficha_tecnica_id"
                                id="ficha_tecnica_id"
                                value="{{ $propuesta->ficha_tecnica_id }}"
                            >

                            <ul id="dropdown_fichas">
                                @foreach($fichas->sortBy('nombre') as $ficha)
                                    <li class="ficha-option" data-id="{{ $ficha->id }}">
                                        {{ mb_strtoupper($ficha->nombre, 'UTF-8') }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        <div class="col-12">
                            <label class="input-label">Notas / Observaciones</label>
                            <textarea name="nota" class="premium-input" rows="2">{{ $propuesta->nota }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5 mb-4">
                <div class="premium-card h-100 animate-entry delay-2 p-0 overflow-hidden">
                    <div class="p-4 bg-primary bg-opacity-10 d-flex align-items-center gap-3">
                        <span class="section-icon"><i class="bi bi-receipt-cutoff"></i></span>
                        <h4 class="fw-bold mb-0 text-primary">Resumen Financiero</h4>
                    </div>

                    <div class="p-4 totals-panel">
                        <div class="total-row">
                            <span class="text-muted fw-semibold">Subtotal</span>
                            <input type="number" id="subtotal" name="subtotal" class="premium-input text-end bg-transparent border-0 w-50 p-0 fw-bold" readonly>
                        </div>

                        <div class="total-row">
                            <span class="text-muted fw-semibold">Descuento</span>
                            <div class="w-50 position-relative">
                                <span class="mini-money-prefix">-$</span>
                                <input type="number" id="descuento" name="descuento" class="premium-input text-end p-1 ps-4" value="{{ $propuesta->descuento }}" step="0.01">
                            </div>
                        </div>

                        <div class="total-row">
                            <span class="text-muted fw-semibold">Gastos de Envío</span>
                            <div class="w-50 position-relative">
                                <span class="mini-money-prefix">+$</span>
                                <input type="number" id="envio" name="envio" class="premium-input text-end p-1 ps-4" value="{{ $propuesta->envio }}" step="0.01">
                            </div>
                        </div>

                        <hr class="my-3" style="border-color: var(--border-soft);">

                        <div class="total-row">
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <span class="text-muted fw-semibold">Aplicar IVA (16%)</span>
                                <label class="custom-switch" style="transform: scale(.9)">
                                    <input type="checkbox" id="toggleIva" {{ $propuesta->iva > 0 ? 'checked' : '' }}>
                                    <span class="switch-slider"></span>
                                </label>
                            </div>
                            <input type="number" id="iva" name="iva" class="premium-input text-end bg-transparent border-0 w-25 p-0" readonly>
                        </div>

                        <div class="total-row grand-total">
                            <span>Total</span>
                            <span id="display-total">$ 0.00</span>
                        </div>

                        <div class="text-end text-muted small mt-2">
                            Valor a financiar:
                            <span id="display-contrato" class="fw-bold"></span>
                        </div>

                        <input type="hidden" name="total" id="total">
                        <input type="hidden" name="aplica_iva" id="aplica_iva">
                        <input type="hidden" id="valor_a_cuenta" value="0">
                        <input type="hidden" id="total_contrato" value="0">
                    </div>
                </div>
            </div>
        </div>

        <div class="premium-card animate-entry delay-2">
            <div class="d-flex justify-content-between align-items-center mb-4 mobile-stack-header">
                <div class="card-header-title border-0 mb-0">
                    <span class="section-icon"><i class="bi bi-arrow-left-right"></i></span>
                    <span>Equipos a Cuenta (Trade-In)</span>
                </div>

                <button type="button" class="btn-outline-custom" id="btn-add-equipo-cuenta">
                    <i class="bi bi-plus-lg"></i>
                    <span>Añadir Equipo</span>
                </button>
            </div>

            <div class="table-shell">
                <table class="table-fintech" id="tabla-equipos-cuenta">
                    <thead>
                        <tr>
                            <th>Tipo de Equipo</th>
                            <th>Marca</th>
                            <th>Modelo</th>
                            <th>No. Serie</th>
                            <th style="width: 160px;">Valor a cuenta</th>
                            <th style="width: 90px;">Acción</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>

            <p class="soft-note mt-2 mb-0">
                El total de estos equipos se restará del total general para calcular el monto a financiar.
            </p>
        </div>

        <div class="premium-card animate-entry delay-2">
            <div class="d-flex justify-content-between align-items-center mb-4 mobile-stack-header">
                <div class="card-header-title border-0 mb-0">
                    <span class="section-icon"><i class="bi bi-calendar3"></i></span>
                    <span>Cronograma de Pagos</span>
                </div>

                <div id="paySummary"></div>
            </div>

            <div id="planContadoNote" class="plan-note">
                <i class="bi bi-info-circle"></i>
                <span>En plan contado solo se permite un solo pago y se ajusta automáticamente al total del contrato.</span>
            </div>

            <div class="table-shell mb-3">
                <table id="tabla-pagos" class="table-fintech">
                    <thead>
                        <tr>
                            <th>Concepto</th>
                            <th style="width: 170px;">Fecha</th>
                            <th style="width: 130px;">Porcentaje</th>
                            <th style="width: 190px;">Monto</th>
                            <th class="text-center" style="width: 110px;">Bloquear</th>
                            <th style="width: 90px;">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($propuesta->pagos as $pago)
                        <tr data-pago-id="{{ $pago->id }}">
                            <td data-label="Concepto" class="stack-main-cell">
                                <input type="text" class="premium-input p-2 p-desc" value="{{ $pago->descripcion }}" readonly>
                            </td>

                            <td data-label="Fecha" class="stack-inline-cell">
                                <input type="date" class="premium-input p-2 p-fecha" value="{{ \Carbon\Carbon::parse($pago->fecha_pago)->format('Y-m-d') }}" required>
                            </td>

                            <td data-label="Porcentaje" class="stack-inline-cell">
                                <div class="position-relative">
                                    <input type="number" class="premium-input pct-pago p-2 pe-4" step="0.01" value="{{ $pago->porcentaje }}">
                                    <span class="mini-suffix">%</span>
                                </div>
                            </td>

                            <td data-label="Monto" class="stack-inline-cell">
                                <div class="position-relative">
                                    <span class="mini-money-prefix">$</span>
                                    <input type="number" class="premium-input monto-pago p-2 ps-4" value="{{ $pago->monto }}" step="0.01" required>
                                </div>
                            </td>

                            <td data-label="Bloquear" class="text-center stack-switch-cell">
                                <label class="custom-switch" style="transform: scale(.9)">
                                    <input type="checkbox" class="switch-bloqueo" checked>
                                    <span class="switch-slider"></span>
                                </label>
                            </td>

                            <td data-label="Acción" class="text-end stack-action-cell">
                                <button type="button" class="icon-btn-danger border-0 btn-eliminar-pago" title="Eliminar pago">
                                    <i class="bi bi-trash3"></i>
                                </button>
                                <input type="hidden" class="p-id" value="{{ $pago->id }}">
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <button type="button" id="agregarPago" class="btn-outline-custom">
                <i class="bi bi-plus-lg"></i>
                <span>Nuevo Pago</span>
            </button>
        </div>

        <input type="hidden" name="equipos_cuenta_json" id="equipos_cuenta_json" value="{{ $propuesta->equipos_cuenta_json }}">
        <input type="hidden" name="pagos_json" id="pagos_json">
        <input type="hidden" name="productos_json" id="productos_json">

        <div class="text-center pb-5 pt-2">
            <button type="submit" class="btn-premium">
                <i class="bi bi-floppy"></i>
                <span>Guardar Propuesta</span>
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const input = document.getElementById('ficha_tecnica_search');
    const hiddenInput = document.getElementById('ficha_tecnica_id');
    const dropdown = document.getElementById('dropdown_fichas');

    if (!input || !hiddenInput || !dropdown) return;

    const originalOptions = Array.from(dropdown.querySelectorAll('.ficha-option'));

    const normalize = str => {
        return (str || '')
            .toString()
            .normalize("NFD")
            .replace(/[\u0300-\u036f]/g, "")
            .toLowerCase();
    };

    function renderOptions(value = '') {
        const search = normalize(value);
        dropdown.innerHTML = '';

        let matches = 0;

        originalOptions.forEach(option => {
            const textOriginal = option.textContent.trim();
            const textNormalized = normalize(textOriginal);

            if (search === '' || textNormalized.includes(search)) {
                matches++;

                const li = document.createElement('li');
                li.className = 'ficha-option';
                li.dataset.id = option.dataset.id;
                li.textContent = textOriginal;

                li.addEventListener('click', function () {
                    input.value = textOriginal;
                    hiddenInput.value = option.dataset.id;
                    dropdown.style.display = 'none';
                });

                dropdown.appendChild(li);
            }
        });

        if (matches === 0) {
            const empty = document.createElement('li');
            empty.className = 'ficha-empty';
            empty.textContent = 'No se encontraron fichas técnicas.';
            dropdown.appendChild(empty);
        }

        dropdown.style.display = 'block';
    }

    input.addEventListener('focus', function () {
        renderOptions(input.value);
    });

    input.addEventListener('input', function () {
        hiddenInput.value = '';
        renderOptions(input.value);
    });

    document.addEventListener('click', function (e) {
        if (!dropdown.contains(e.target) && e.target !== input) {
            dropdown.style.display = 'none';
        }
    });
});
</script>

<script>
    const IVA_RATE = 0.16;
    const to2 = n => Number(n || 0).toFixed(2);

    function obtenerNombrePago(index) {
        const nombres = [
            'Pago inicial',
            'Primer pago',
            'Segundo pago',
            'Tercer pago',
            'Cuarto pago',
            'Quinto pago',
            'Sexto pago',
            'Séptimo pago',
            'Octavo pago',
            'Noveno pago',
            'Décimo pago',
            'Undécimo pago',
            'Duodécimo pago',
            'Décimo tercer pago',
            'Décimo cuarto pago',
            'Décimo quinto pago',
            'Décimo sexto pago',
            'Décimo séptimo pago',
            'Décimo octavo pago',
            'Décimo noveno pago',
            'Vigésimo pago'
        ];

        return nombres[index] ?? `Pago ${index + 1}`;
    }

    function hoyYMD() {
        const d = new Date();
        const y = d.getFullYear();
        const m = String(d.getMonth() + 1).padStart(2, '0');
        const day = String(d.getDate()).padStart(2, '0');
        return `${y}-${m}-${day}`;
    }

    function esPlanContado() {
        return ($('#plan').val() || '') === 'contado';
    }

    function renombrarPagos() {
        $('#tabla-pagos tbody tr').each(function(index) {
            $(this).find('.p-desc').val(obtenerNombrePago(index));
        });
    }

    function crearFilaPago(nombre = 'Pago inicial', fecha = '') {
        return `
            <tr class="animate-entry">
                <td data-label="Concepto" class="stack-main-cell">
                    <input type="text" class="premium-input p-2 p-desc" value="${nombre}" readonly>
                </td>

                <td data-label="Fecha" class="stack-inline-cell">
                    <input type="date" class="premium-input p-2 p-fecha" value="${fecha}" required>
                </td>

                <td data-label="Porcentaje" class="stack-inline-cell">
                    <div class="position-relative">
                        <input type="number" class="premium-input pct-pago p-2 pe-4" step="0.01" value="0">
                        <span class="mini-suffix">%</span>
                    </div>
                </td>

                <td data-label="Monto" class="stack-inline-cell">
                    <div class="position-relative">
                        <span class="mini-money-prefix">$</span>
                        <input type="number" class="premium-input monto-pago p-2 ps-4" value="0" step="0.01" required>
                    </div>
                </td>

                <td data-label="Bloquear" class="text-center stack-switch-cell">
                    <label class="custom-switch" style="transform: scale(.9)">
                        <input type="checkbox" class="switch-bloqueo">
                        <span class="switch-slider"></span>
                    </label>
                </td>

                <td data-label="Acción" class="text-end stack-action-cell">
                    <button type="button" class="icon-btn-danger border-0 btn-eliminar-pago" title="Eliminar pago">
                        <i class="bi bi-trash3"></i>
                    </button>
                    <input type="hidden" class="p-id" value="">
                </td>
            </tr>
        `;
    }

    function syncGiftState(tr, isGift) {
        const realGift = isGift === true;

        tr.find('.gift-toggle').prop('checked', realGift);
        tr.find('.es_regalo_hidden').val(realGift ? '1' : '0');
        tr.attr('data-es-regalo', realGift ? '1' : '0');

        if (realGift) {
            tr.find('.sobreprecio').prop('readonly', true).val('0.00');
        } else {
            tr.find('.sobreprecio').prop('readonly', false);

            const sobre = parseFloat(tr.find('.sobreprecio').val());
            if (isNaN(sobre)) {
                tr.find('.sobreprecio').val('0.00');
            }
        }
    }

    function actualizarTotal(redistribuir = true) {
        let subtotal = 0;

        $('#tabla-productos tbody tr').each(function() {
            const tr = $(this);
            const isGift = tr.find('.gift-toggle').prop('checked') === true;

            syncGiftState(tr, isGift);

            const qty = parseFloat(tr.find('.cantidad').val()) || 1;
            const price = parseFloat(tr.data('precio')) || parseFloat(tr.find('.precio_unitario').val()) || 0;
            const extra = isGift ? 0 : (parseFloat(tr.find('.sobreprecio').val()) || 0);

            if (isGift) {
                tr.find('.subtotal-cell').html('<span class="badge-gift"><i class="bi bi-gift"></i> REGALO</span>');
            } else {
                const lineSub = qty * (price + extra);
                tr.find('.subtotal-cell').text(`$ ${to2(lineSub)}`);
                subtotal += lineSub;
            }
        });

        const desc = parseFloat($('#descuento').val()) || 0;
        const envio = parseFloat($('#envio').val()) || 0;
        const aplicaIva = $('#toggleIva').is(':checked');

        const base = Math.max(0, subtotal - desc + envio);
        const iva = aplicaIva ? base * IVA_RATE : 0;
        const total = base + iva;

        let tradeIn = 0;
        $('#tabla-equipos-cuenta tbody tr').each(function() {
            tradeIn += parseFloat($(this).find('.ec-valor').val()) || 0;
        });

        const totalContrato = Math.max(0, total - tradeIn);

        $('#subtotal').val(to2(subtotal));
        $('#iva').val(to2(iva));
        $('#total').val(to2(total));
        $('#aplica_iva').val(aplicaIva ? 1 : 0);
        $('#display-total').text(`$ ${to2(total)}`);

        $('#valor_a_cuenta').val(tradeIn);
        $('#total_contrato').val(totalContrato);
        $('#display-contrato').text(`$ ${to2(totalContrato)}`);

        if (redistribuir) {
            ajustarPagos();
        } else {
            renombrarPagos();
            validarPagosVisual(totalContrato);
        }
    }

    function sincronizarPlanContado() {
        const tbody = $('#tabla-pagos tbody');

        if (!tbody.find('tr').length) {
            tbody.append(crearFilaPago('Pago inicial', hoyYMD()));
        }

        tbody.find('tr:gt(0)').remove();
        renombrarPagos();

        const totalBase = parseFloat($('#total_contrato').val()) || 0;
        const tr = tbody.find('tr').first();

        tr.find('.monto-pago').val(to2(totalBase));
        tr.find('.pct-pago').val(totalBase > 0 ? '100.00' : '0.00');
        tr.find('.switch-bloqueo').prop('checked', true).prop('disabled', true);
        tr.find('.btn-eliminar-pago').prop('disabled', true);
        tr.find('.p-desc').prop('readonly', true);

        $('#agregarPago').prop('disabled', true);
        $('#planContadoNote').css('display', 'flex');

        validarPagosVisual(totalBase);
    }

    function liberarPlanNoContado() {
        $('#agregarPago').prop('disabled', false);
        $('#planContadoNote').hide();

        $('#tabla-pagos tbody tr').each(function() {
            $(this).find('.switch-bloqueo').prop('disabled', false);
            $(this).find('.btn-eliminar-pago').prop('disabled', false);
            $(this).find('.p-desc').prop('readonly', true);
        });

        renombrarPagos();
    }

    function ajustarPagos(triggerEl = null) {
        const totalBase = parseFloat($('#total_contrato').val()) || 0;
        const rows = $('#tabla-pagos tbody tr');

        if (esPlanContado()) {
            sincronizarPlanContado();
            return;
        }

        liberarPlanNoContado();

        if (!rows.length) {
            $('#paySummary').html('');
            return;
        }

        renombrarPagos();

        let sumaFija = 0;
        let libres = [];

        if (triggerEl) {
            $(triggerEl).closest('tr').find('.switch-bloqueo').prop('checked', true);

            const tr = $(triggerEl).closest('tr');
            if ($(triggerEl).hasClass('monto-pago')) {
                const m = parseFloat(tr.find('.monto-pago').val()) || 0;
                tr.find('.pct-pago').val(to2(totalBase > 0 ? (m / totalBase) * 100 : 0));
            } else if ($(triggerEl).hasClass('pct-pago')) {
                const p = parseFloat(tr.find('.pct-pago').val()) || 0;
                tr.find('.monto-pago').val(to2((p / 100) * totalBase));
            }
        }

        $('#tabla-pagos tbody tr').each(function() {
            const tr = $(this);
            const isLocked = tr.find('.switch-bloqueo').is(':checked');
            const monto = parseFloat(tr.find('.monto-pago').val()) || 0;

            if (isLocked) {
                sumaFija += monto;
            } else {
                libres.push(tr);
            }
        });

        if (libres.length > 0) {
            const rem = Math.max(0, totalBase - sumaFija);
            const cadaUno = rem / libres.length;

            libres.forEach(tr => {
                tr.find('.monto-pago').val(to2(cadaUno));
                tr.find('.pct-pago').val(to2(totalBase > 0 ? (cadaUno / totalBase) * 100 : 0));
            });
        }

        validarPagosVisual(totalBase);
    }

    function validarPagosVisual(totalBase) {
        let sum = 0;

        $('#tabla-pagos tbody tr').each(function() {
            sum += parseFloat($(this).find('.monto-pago').val()) || 0;
        });

        const diff = Math.abs(sum - totalBase);
        const ok = diff < 0.05;

        const sumEl = $('#paySummary');
        sumEl.html(
            ok
                ? `<div class="summary-chip ok"><i class="bi bi-check-circle-fill"></i><span>Cuadrado correctamente ($${to2(sum)})</span></div>`
                : `<div class="summary-chip bad"><i class="bi bi-exclamation-triangle-fill"></i><span>Diferencia: $${to2(totalBase - sum)}</span></div>`
        );
    }

    function eliminarFila(btn) {
        $(btn).closest('tr').remove();
        actualizarTotal();
    }

    function addEquipoCuentaRow(data = {}) {
        const v = val => val ? String(val).replace(/"/g, '&quot;') : '';

        const row = `
            <tr class="animate-entry">
                <td data-label="Tipo de equipo" class="stack-inline-cell">
                    <input type="text" class="premium-input p-2 ec-tipo" placeholder="Ej: Computadora" value="${v(data.tipo_equipo)}">
                </td>

                <td data-label="Marca" class="stack-inline-cell">
                    <input type="text" class="premium-input p-2 ec-marca" placeholder="Marca" value="${v(data.marca)}">
                </td>

                <td data-label="Modelo" class="stack-inline-cell">
                    <input type="text" class="premium-input p-2 ec-modelo" placeholder="Modelo" value="${v(data.modelo)}">
                </td>

                <td data-label="No. serie" class="stack-inline-cell">
                    <input type="text" class="premium-input p-2 ec-serie" placeholder="Número de serie" value="${v(data.numero_serie)}">
                </td>

                <td data-label="Valor a cuenta" class="stack-inline-cell">
                    <div class="position-relative">
                        <span class="mini-money-prefix">$</span>
                        <input type="number" class="premium-input p-2 ps-4 ec-valor" min="0" step="0.01" value="${data.valor_a_cuenta || 0}">
                    </div>
                </td>

                <td data-label="Acción" class="text-end stack-action-cell">
                    <button type="button" class="icon-btn-danger border-0" onclick="eliminarFila(this)" title="Eliminar">
                        <i class="bi bi-trash3"></i>
                    </button>
                </td>
            </tr>
        `;

        $('#tabla-equipos-cuenta tbody').append(row);
        actualizarTotal();
    }

    $(document).on('change', '.gift-toggle', function() {
        const tr = $(this).closest('tr');
        const isGift = $(this).prop('checked') === true;
        syncGiftState(tr, isGift);
        actualizarTotal();
    });

    $(document).on('input change', '.cantidad, .sobreprecio, #descuento, #envio, #toggleIva, .ec-valor', () => actualizarTotal());
    $(document).on('input', '.monto-pago, .pct-pago', function() { ajustarPagos(this); });
    $(document).on('change', '.switch-bloqueo', () => ajustarPagos());

    $('#plan').on('change', function() {
        if (esPlanContado()) {
            sincronizarPlanContado();
        } else {
            liberarPlanNoContado();
            ajustarPagos();
        }
    });

    $('#btn-add-equipo-cuenta').click(() => addEquipoCuentaRow());

    $(document).on('click', '.btn-eliminar-pago', function() {
        if (esPlanContado()) return;
        $(this).closest('tr').remove();
        renombrarPagos();
        ajustarPagos();
    });

    $('#agregarPago').click(function() {
        if (esPlanContado()) return;

        const rowsCount = $('#tabla-pagos tbody tr').length;
        const nombre = obtenerNombrePago(rowsCount);
        const fechaBase = $('#tabla-pagos tbody tr:last .p-fecha').val() || hoyYMD();

        $('#tabla-pagos tbody').append(crearFilaPago(nombre, fechaBase));
        renombrarPagos();
        ajustarPagos();
    });

    $(function() {
        const $input = $('#buscador-producto');
        const $lista = $('#lista-productos');
        let products = [];

        $lista.find('.dropdown-item').each(function() {
            const it = $(this);
            products.push({
                id: it.data('id'),
                nombre: it.data('nombre'),
                modelo: it.data('modelo'),
                marca: it.data('marca'),
                precio: parseFloat(it.data('precio')),
                imagen: it.data('imagen'),
                html: this.outerHTML
            });
        });

        const fuse = new Fuse(products, { keys: ['nombre', 'modelo', 'marca'], threshold: 0.3 });

        function renderList(list) {
            $lista.empty();
            list.slice(0, 8).forEach(p => $lista.append(p.item ? p.item.html : p.html));
            $lista.show();
        }

        $input.on('focus input', function() {
            const q = $(this).val();
            renderList(q ? fuse.search(q) : products);
        });

        $(document).on('click', function(e) {
            if (!$(e.target).closest('.search-wrapper').length) $lista.hide();
        });

        $(document).on('click', '.dropdown-item', function() {
            const p = $(this).data();

            if ($(`#tabla-productos tr[data-id="${p.id}"]`).length) {
                return alert('Este producto ya está en la cotización.');
            }

            const row = `
                <tr data-id="${p.id}" data-precio="${p.precio}" data-es-regalo="0" class="animate-entry" style="box-shadow: 0 0 0 2px rgba(16,185,129,.25);">
                    <td data-label="Equipo" class="stack-main-cell">
                        <div class="row-title-inline">
                            <img src="${p.imagen}" class="item-img shadow-sm" alt="producto">
                            <div class="title-copy">
                                <div class="product-name">${p.nombre}</div>
                                <div class="product-meta">${p.marca || ''} ${p.modelo || ''}</div>
                                <div class="product-price-mini">Precio base: $${to2(p.precio)}</div>
                            </div>
                        </div>
                    </td>

                    <td data-label="Cantidad" class="stack-inline-cell">
                        <input type="number" name="productos[${p.id}][cantidad]" class="premium-input text-center cantidad p-2" value="1" min="1">
                    </td>

                    <td data-label="Sobreprecio" class="stack-inline-cell">
                        <div class="position-relative">
                            <span class="mini-money-prefix">$</span>
                            <input type="number" name="productos[${p.id}][sobreprecio]" class="premium-input sobreprecio ps-4 p-2" value="0.00" step="0.01">
                        </div>
                    </td>

                    <td data-label="Subtotal" class="fw-bold subtotal-cell">$ ${to2(p.precio)}</td>

                    <td data-label="Regalo" class="text-center stack-switch-cell">
                        <label class="custom-switch">
                            <input type="checkbox" class="gift-toggle">
                            <span class="switch-slider"></span>
                        </label>
                    </td>

                    <td data-label="Acción" class="text-end stack-action-cell">
                        <button type="button" class="icon-btn-danger border-0" onclick="eliminarFila(this)" title="Eliminar">
                            <i class="bi bi-trash3"></i>
                        </button>
                    </td>

                    <input type="hidden" class="es_regalo_hidden" name="productos[${p.id}][es_regalo]" value="0">
                    <input type="hidden" class="precio_unitario" name="productos[${p.id}][precio_unitario]" value="${p.precio}">
                </tr>
            `;

            $('#tabla-productos tbody').prepend(row);
            const newRow = $('#tabla-productos tbody tr').first();

            syncGiftState(newRow, false);

            $lista.hide();
            $input.val('');

            setTimeout(() => {
                newRow.css('box-shadow', '');
            }, 1000);

            actualizarTotal();
        });
    });

    $('#form-propuesta').on('submit', function(e) {
        renombrarPagos();

        if (esPlanContado()) {
            sincronizarPlanContado();
        }

        const prods = [];
        $('#tabla-productos tbody tr').each(function() {
            const tr = $(this);

            const isGift = tr.find('.gift-toggle').prop('checked') === true;
            syncGiftState(tr, isGift);

            let sobreprecio = parseFloat(tr.find('.sobreprecio').val());
            if (isNaN(sobreprecio)) sobreprecio = 0;

            if (isGift) {
                sobreprecio = 0;
                tr.find('.sobreprecio').val('0.00');
            }

            prods.push({
                producto_id: parseInt(tr.data('id')) || 0,
                cantidad: parseFloat(tr.find('.cantidad').val()) || 1,
                precio_unitario: parseFloat(tr.find('.precio_unitario').val()) || parseFloat(tr.data('precio')) || 0,
                sobreprecio: sobreprecio,
                es_regalo: isGift ? 1 : 0
            });
        });

        $('#productos_json').val(JSON.stringify(prods));

        const trade = [];
        $('#tabla-equipos-cuenta tbody tr').each(function() {
            const tr = $(this);
            const v = parseFloat(tr.find('.ec-valor').val()) || 0;

            if (v > 0 || tr.find('.ec-tipo').val()) {
                trade.push({
                    tipo_equipo: tr.find('.ec-tipo').val(),
                    marca: tr.find('.ec-marca').val(),
                    modelo: tr.find('.ec-modelo').val(),
                    numero_serie: tr.find('.ec-serie').val(),
                    valor_a_cuenta: v
                });
            }
        });
        $('#equipos_cuenta_json').val(JSON.stringify(trade));

        const pagos = [];
        const filasPagos = esPlanContado()
            ? $('#tabla-pagos tbody tr').slice(0, 1)
            : $('#tabla-pagos tbody tr');

        filasPagos.each(function(index) {
            const tr = $(this);
            const fechaPago = tr.find('.p-fecha').val();
            const monto = parseFloat(tr.find('.monto-pago').val()) || 0;
            const porcentaje = parseFloat(tr.find('.pct-pago').val()) || 0;

            pagos.push({
                id: tr.find('.p-id').val() || null,
                descripcion: obtenerNombrePago(index),
                fecha_pago: fechaPago,
                mes: fechaPago,
                porcentaje: porcentaje,
                monto: monto,
                cuota: monto,
                bloqueado: tr.find('.switch-bloqueo').is(':checked') ? 1 : 0
            });
        });

        $('#pagos_json').val(JSON.stringify(pagos));

        const tBase = parseFloat($('#total_contrato').val()) || 0;
        let sum = 0;
        pagos.forEach(p => sum += p.monto);

        if (pagos.length > 0 && Math.abs(sum - tBase) > 0.05) {
            e.preventDefault();
            alert('El cronograma de pagos no cuadra con el Total del Contrato. Por favor, revisa los montos.');
            return false;
        }
    });

    $(document).ready(() => {
        try {
            let eqJson = $('#equipos_cuenta_json').val();
            if (eqJson && eqJson !== 'null' && eqJson !== '[]') {
                let parsed = typeof eqJson === 'string' ? JSON.parse(eqJson) : eqJson;
                if (typeof parsed === 'string') parsed = JSON.parse(parsed);
                parsed.forEach(eq => addEquipoCuentaRow(eq));
            }
        } catch(err) {
            console.warn('No hay equipos a cuenta previos.');
        }

        $('#tabla-productos tbody tr').each(function() {
            const tr = $(this);
            const hiddenVal = String(tr.find('.es_regalo_hidden').val() ?? '0');
            const isGift = hiddenVal === '1';
            syncGiftState(tr, isGift);
        });

        renombrarPagos();

        if (esPlanContado()) {
            sincronizarPlanContado();
        } else {
            liberarPlanNoContado();
            actualizarTotal(false);
            ajustarPagos();
        }

        actualizarTotal(false);
    });
</script>
@endsection