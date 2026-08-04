@extends('layouts.app')

@section('title', 'Nueva Propuesta')
@section('content')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="{{ asset('css/propuestacreate.css') }}?v={{ time() }}">
<meta name="csrf-token" content="{{ csrf_token() }}">

<style>
  .gift-switch{display:flex;align-items:center;gap:10px;user-select:none}
  .gift-switch .form-check-input{width:44px;height:22px;border-radius:999px;cursor:pointer}
  .gift-badge{
    display:inline-flex;align-items:center;justify-content:center;
    font-weight:700;font-size:.78rem;padding:6px 10px;border-radius:999px;
    background: rgba(16,185,129,.12);color:#065f46;border:1px solid rgba(16,185,129,.22);
    white-space:nowrap
  }
  .gift-muted{color:#64748b;font-size:.9rem}
  .pay-summary span{display:inline-flex;gap:.35rem;align-items:center;margin-left:.6rem;font-weight:700}
  .pay-summary .ok{color:#166534}
  .pay-summary .bad{color:#b91c1c}
  .pay-summary .warn{color:#92400e}
  .pay-field{position:relative}
  .pay-input{padding-right:55px}
  .pay-suffix{position:absolute;right:10px;top:50%;transform:translateY(-50%);font-size:.85rem;color:#64748b}
</style>

<div class="container">
    <form id="form-propuesta" method="POST" action="{{ route('propuestas.store') }}">
        @csrf

        <div class="row">
            <div class="col-md-3 mt-3">
                <div class="card modern-card mb-3">
                    <div class="card-header modern-heade">Cliente</div>
                    <div class="card-body">
                        <div class="dropdown position-relative">
                            <input
                                type="text"
                                id="search-client"
                                class="form-control modern-input dropdown-toggle"
                                data-bs-toggle="dropdown"
                                placeholder="Buscar cliente..."
                                autocomplete="off"
                                aria-haspopup="true"
                                aria-expanded="false"
                            >
                            <ul class="dropdown-menu modern-dropdown w-100" id="client-list">
                                <li>
                                    <button type="button" class="dropdown-item modern-dropdown-item" onclick='selectClient({
                                        id: 1,
                                        nombre: "Público en General",
                                        apellido: "",
                                        telefono: "",
                                        email: "",
                                        comentarios: ""
                                    })'>
                                        Público en General
                                    </button>
                                </li>
                                <li>
                                    <button type="button" class="dropdown-item modern-dropdown-item" onclick="openCreateClientModal()">
                                        + Crear nuevo cliente
                                    </button>
                                </li>
                            </ul>
                        </div>
                        <input type="hidden" name="cliente_id" id="cliente_id">
                    </div>
                    <div id="client-details" class="mt-3"></div>
                </div>

                <div class="card modern-card mb-3">
                    <div class="card-header modern-heade">Lugar de la Propuesta</div>
                    <div class="card-body">
                        <select name="lugar" id="lugarPropuesta" class="form-control modern-select" required>
                            <option value="">Selecciona un lugar...</option>
                            <option value="AMCG ECOS INTERNACIONAL DE CIRUGIA GENERAL">AMCG ECOS INTERNACIONAL DE CIRUGIA GENERAL</option>
                            <option value="AMCG CONGRESO INTERNACIONAL DE CIRUGIA GENERAL">AMCG CONGRESO INTERNACIONAL DE CIRUGIA GENERAL</option>
                            <option value="AMCE CONGRESO INTERNACIONAL DE CIRUGIA ENDOSCOPICA">AMCE CONGRESO INTERNACIONAL DE CIRUGIA ENDOSCOPICA</option>
                            <option value="AMECRA XXIX CONGRESO INTERNACIONAL DE ASOCIACIÓN MEX DE CIRUGIA RECONS, ARTICULAR Y ARTROSCOPICA">
                                AMECRA XXIX CONGRESO INTERNACIONAL DE ASOCIACIÓN MEX DE CIRUGIA RECONS, ARTICULAR Y ARTROSCOPICA
                            </option>
                            <option value="CVDL CONGRESO DE VETERINARIA">CVDL CONGRESO DE VETERINARIA</option>
                            <option value="AMG ECOS INTERNACIONALES DE GASTROENTEROLOGIA">AMG ECOS INTERNACIONALES DE GASTROENTEROLOGIA</option>
                            <option value="AMG SEMANA NACIONAL GASTRO">AMG SEMANA NACIONAL GASTRO</option>
                            <option value="COLEGIO DE ESPECIALISTAS EN CIRUGIA GENERAL">COLEGIO DE ESPECIALISTAS EN CIRUGIA GENERAL</option>
                            <option value="otro">Otro</option>
                        </select>
                    </div>
                </div>

                <div class="card modern-card mb-3">
                    <div class="card-header modern-heade">Nota al Cliente</div>
                    <div class="card-body">
                        <textarea name="nota" id="notaCliente" class="form-control modern-textarea" rows="4" placeholder="Escribe una nota..."></textarea>
                    </div>
                </div>

                <div class="card modern-card">
                    <div class="card-header modern-heade">Registrado por</div>
                    <div class="card-body">
                        @auth
                            <input type="text" name="registrado_por" class="form-control modern-textarea" value="{{ Auth::user()->name }}" readonly>
                        @else
                            <input type="text" name="registrado_por" class="form-control modern-textarea" value="Desconocido" readonly>
                        @endauth
                    </div>
                </div>
            </div>

            <div class="col-md-9">
                <div class="card modern-card mt-3">
                    <div class="card-header modern-header">Productos</div>
                    <div class="card-body">
                        <div class="dropdown">
                            <input
                                type="text"
                                id="buscarProducto"
                                class="form-control modern-input dropdown-toggle"
                                data-bs-toggle="dropdown"
                                placeholder="Buscar producto..."
                                autocomplete="off"
                                onkeyup="filtrarProductos(this.value)"
                            >
                            <ul class="dropdown-menu modern-dropdown w-100" id="dropdownProductos">
                                <li>
                                    <button type="button" class="dropdown-item modern-dropdown-item" data-bs-toggle="modal" data-bs-target="#modal1">
                                        + Crear Producto
                                    </button>
                                </li>
                                @foreach($productos->sortBy('tipo_equipo') as $producto)
                                    <li>
                                        <button
                                            type="button"
                                            class="dropdown-item modern-dropdown-item d-flex align-items-center"
                                            onclick="agregarProductoDesdeDropdown(
                                                {{ $producto->id }},
                                                @json($producto->tipo_equipo),
                                                @json($producto->modelo),
                                                @json($producto->marca),
                                                {{ $producto->precio }},
                                                @json($producto->imagen)
                                            )"
                                        >
                                            <img src="/storage/{{ $producto->imagen }}" alt="{{ $producto->tipo_equipo }}" class="modern-product-img me-2" style="width: 40px; height: 40px; object-fit: cover; border-radius: 6px;">
                                            <div class="flex-grow-1 modern-product-info">
                                                <strong>{{ strtoupper($producto->tipo_equipo) }}</strong> - {{ strtoupper($producto->modelo) }} {{ strtoupper($producto->marca) }}
                                                <br>
                                                <span class="text-muted modern-product-price">${{ number_format($producto->precio, 2) }}</span>
                                            </div>
                                            <span class="badge bg-secondary modern-badge">{{ $producto->stock }} unidades</span>
                                        </button>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="card modern-card mt-3">
                    <div class="card-header modern-header d-flex justify-content-between align-items-center">
                        <span>Productos Seleccionados</span>
                        <span class="gift-muted">Marca <span class="gift-badge">REGALO</span> para excluir del total y ocultar precio.</span>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <input type="hidden" name="productos_json" id="productos_json" value="">
                            <table id="tabla-productos" class="table modern-table">
                                <thead>
                                    <tr>
                                        <th>Imagen</th>
                                        <th>Equipo</th>
                                        <th>Modelo</th>
                                        <th>Marca</th>
                                        <th>Cantidad</th>
                                        <th>Subtotal</th>
                                        <th>Sobreprecio</th>
                                        <th style="width:170px;">Regalo</th>
                                        <th>Acción</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="card modern-card mt-3">
                    <div class="card-header modern-header d-flex justify-content-between align-items-center">
                        <span>Equipos / mercancía a cuenta (Trade-in)</span>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="btn-add-equipo-cuenta">
                            + Agregar equipo a cuenta
                        </button>
                    </div>
                    <div class="card-body">
                        <input type="hidden" name="equipos_cuenta_json" id="equipos_cuenta_json" value="[]">

                        <div class="table-responsive">
                            <table class="table modern-table" id="tabla-equipos-cuenta">
                                <thead>
                                    <tr>
                                        <th>Tipo de equipo</th>
                                        <th>Marca</th>
                                        <th>Modelo</th>
                                        <th>No. de serie</th>
                                        <th style="width: 140px;">Valor a cuenta</th>
                                        <th style="width: 70px;">Acción</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                        <small class="text-muted">
                            Estos equipos se tomarán a cuenta y su suma se reflejará como <strong>valor a cuenta</strong> en el resumen.
                        </small>
                    </div>
                </div>

                <div class="d-flex flex-column flex-md-row">
                    <div class="card modern-card mt-3 w-100 w-md-50">
                        <div class="card-header modern-header">Resumen</div>
                        <div class="card-body">
                            <p>Subtotal: $<span id="subtotal" class="modern-value">0.00</span></p>
                            <input type="hidden" name="subtotal" id="subtotal_input" value="0">

                            <div class="form-group">
                                <label>Descuento</label>
                                <input type="number" name="descuento" id="descuento" value="0" class="form-control modern-input w-25 d-inline-block" onchange="actualizarTotal()">
                            </div>
                            <br>
                            <div class="form-group">
                                <label>Envío</label>
                                <input type="number" name="envio" id="envio" value="0" class="form-control modern-input w-25 d-inline-block" onchange="actualizarTotal()">
                            </div>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="aplica_iva" onchange="actualizarTotal()">
                                <label class="form-check-label">Aplicar IVA (16%)</label>
                            </div>
                            <input type="hidden" name="iva" id="iva_input" value="0">
                            <p>IVA: $<span id="iva">0.00</span></p>

                            <p><strong>Total: $<span id="total">0.00</span></strong></p>
                            <input type="hidden" name="total" id="total_input" value="0">

                            <div class="form-group mt-3">
                                <label>Valor a cuenta (equipos / mercancía a cuenta)</label>
                                <input
                                    type="number"
                                    name="valor_a_cuenta"
                                    id="valor_a_cuenta"
                                    value="0"
                                    class="form-control modern-input w-25 d-inline-block"
                                    min="0"
                                    step="0.01"
                                    readonly
                                >
                                <small class="text-muted d-block mt-1">
                                    Este monto es la suma de los equipos a cuenta de la tabla superior y se resta del total para calcular el contrato.
                                </small>
                            </div>

                            <p class="mt-3 mb-0">
                                <strong>Valor a cuenta:</strong>
                                $<span id="valor_cuenta_view" class="modern-value">0.00</span>
                            </p>
                            <p class="mb-2">
                                <strong>Total del contrato:</strong>
                                $<span id="total_contrato_view" class="modern-value">0.00</span>
                            </p>

                            <input type="hidden" name="total_a_cuenta" id="total_a_cuenta_input" value="0">
                            <input type="hidden" name="total_contrato" id="total_contrato_input" value="0">

                            <div class="form-group mt-3">
                                <label for="tipoPago">Selecciona Plan:</label>
                                <select id="tipoPago" name="plan" class="form-control modern-input w-50" required>
                                    <option value="" selected disabled>Selecciona un plan</option>
                                    <option value="contado">Pago de Contado</option>
                                    <option value="personalizado">Plan Personalizado</option>
                                    <option value="estatico">Plan Fijo</option>
                                    <option value="dinamico">Plan Flexible</option>
                                    <option value="credito">Plan a Crédito</option>
                                </select>
                            </div>

                            <div id="opcionesDinamicas" style="display: none; margin-top: 1rem;">
                                <label for="pagoInicial">Pago Inicial:</label>
                                <input type="number" id="pagoInicial" class="form-control modern-input w-50" min="0" step="0.01">
                            </div>

                            <div id="opcionesCredito" style="display: none; margin-top: 1rem;">
                                <label for="pagoCreditoInicial">Pago Inicial:</label>
                                <input type="number" id="pagoCreditoInicial" class="form-control modern-input w-50" min="0" step="0.01">
                                <label for="plazoCredito" style="margin-top: 0.5rem;">Plazo (meses):</label>
                                <input type="number" id="plazoCredito" class="form-control modern-input w-50" value="6" min="1" step="1">
                            </div>

                            <div id="opcionesPersonalizado" style="display: none; margin-top: 1rem;">
                                <label for="mesesPersonalizado">Selecciona el número de meses:</label>
                                <input type="number" id="mesesPersonalizado" class="form-control modern-input w-50" min="1" step="1" value="1">
                            </div>

                            <input type="hidden" id="pagosJsonInput" name="pagos_json" value="">

                            <br>
                            <div class="form-group position-relative mt-4" style="max-width: 900px; width: 100%;">
                                <label for="ficha_tecnica_search">Ficha Técnica a incluir en el PDF:</label>
                                <input type="text" id="ficha_tecnica_search" class="form-control" placeholder="Escribe para buscar..." autocomplete="off">
                                <input type="hidden" name="ficha_tecnica_id" id="ficha_tecnica_id">

                                <ul id="dropdown_fichas" class="list-group position-absolute mt-1 w-100 shadow-sm" style="z-index: 1000; display: none; max-height: 250px; overflow-y: auto;">
                                    @foreach ($fichas->sortBy('nombre') as $ficha)
                                        <li class="list-group-item list-option" data-id="{{ $ficha->id }}">{{ strtoupper($ficha->nombre) }}</li>
                                    @endforeach
                                </ul>
                            </div>

                            <br>
                            <div class="botones-compactos">
                                <button type="submit" class="btn-guardar">Guardar</button>
                                <a href="{{ route('propuestas.index') }}" class="btn-regresar">Regresar</a>
                            </div>
                        </div>
                    </div>

                    <div class="card modern-card mt-3 w-100 w-md-50 ms-md-3">
                        <div class="card-header modern-header">Detalles del Financiamiento</div>
                        <div class="card-body" id="plan-pagos"></div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<div class="modal fade" id="modal_formulario" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">
          <span class="cm-icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" fill="none" stroke-width="1.8">
              <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
              <circle cx="12" cy="7" r="4"/>
            </svg>
          </span>
          Crear cliente
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>

      <form id="formCrearCliente">
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-6">
              <div class="cm-field">
                <div class="cm-label">Nombre</div>
                <input type="text" name="nombre" class="form-control modern-input" placeholder="Ej. Juan" required>
              </div>
            </div>

            <div class="col-md-6">
              <div class="cm-field">
                <div class="cm-label">Apellido</div>
                <input type="text" name="apellido" class="form-control modern-input" placeholder="Ej. Pérez" required>
              </div>
            </div>

            <div class="col-md-6">
              <div class="cm-field">
                <div class="cm-label">Teléfono</div>
                <input type="text" name="telefono" class="form-control modern-input" placeholder="Ej. 55 1234 5678">
              </div>
            </div>

            <div class="col-md-6">
              <div class="cm-field">
                <div class="cm-label">Email</div>
                <input type="email" name="email" class="form-control modern-input" placeholder="correo@ejemplo.com">
              </div>
            </div>

            <div class="col-md-6">
              <div class="cm-field">
                <div class="cm-label">¿Recibe promoción?</div>
                <select name="recibe_promocion" class="form-control modern-input" required>
                  <option value="">Selecciona una opción</option>
                  <option value="1">Sí</option>
                  <option value="0">No</option>
                </select>
                <div class="cm-help">
                  <span class="dot"></span>
                  <span>Indica si este cliente puede recibir promociones.</span>
                </div>
              </div>
            </div>
            {{-- ✅ Campo nuevo: Categoría --}}
            <div class="col-md-6">
                <div class="cm-field">
                <div class="cm-label">Categoría</div>
                <select name="categoria_id" id="categoria_id_modal" class="form-control modern-input" required>
                <option value="" disabled selected>Selecciona una categoría</option>
                @foreach($categorias as $categoria)
                <option value="{{ $categoria->id }}">
                {{ mb_strtoupper($categoria->nombre, 'UTF-8') }}
                </option>
                @endforeach
                </select>
                <div class="cm-help">
                <span class="dot"></span>
                <span>Clasifica el tipo de cliente.</span>
                </div>
            </div>
            </div>

            <div class="col-12">
              <div class="cm-field">
                <div class="cm-label">Dirección / Comentarios</div>
                <textarea name="comentarios" class="form-control modern-textarea" rows="3" placeholder="Ej. Hospital Ángeles, piso 3..."></textarea>
                <div class="cm-help">
                  <span class="dot"></span>
                  <span>Tip: agrega referencias útiles.</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="modal-footer d-flex justify-content-end">
          <button type="button" class="btn btn-cancel" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-save">Guardar cliente</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
function filtrarProductos(valor){ return true; }
</script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const input = document.getElementById('ficha_tecnica_search');
    const hiddenInput = document.getElementById('ficha_tecnica_id');
    const dropdown = document.getElementById('dropdown_fichas');
    const originalOptions = [...dropdown.querySelectorAll('.list-option')];

    const normalize = str => str.normalize("NFD").replace(/[\u0300-\u036f]/g, "").toLowerCase();

    input.addEventListener('input', function () {
        const value = normalize(input.value);
        dropdown.innerHTML = '';
        let matches = 0;

        originalOptions.forEach(option => {
            const textOriginal = option.textContent;
            const textNormalized = normalize(textOriginal);

            if (textNormalized.includes(value)) {
                matches++;
                const li = document.createElement('li');
                li.classList.add('list-group-item', 'list-option');
                li.setAttribute('data-id', option.getAttribute('data-id'));
                li.textContent = textOriginal;
                li.addEventListener('click', () => {
                    input.value = textOriginal;
                    hiddenInput.value = option.getAttribute('data-id');
                    dropdown.style.display = 'none';
                });
                dropdown.appendChild(li);
            }
        });

        dropdown.style.display = matches ? 'block' : 'none';
    });

    document.addEventListener('click', function (e) {
        if (!dropdown.contains(e.target) && e.target !== input) {
            dropdown.style.display = 'none';
        }
    });
});
</script>
{{-- ==================== Generación de plan de pagos SIMPLE / LIBRE (Auto-Balanceo Sin Decimales en %) ==================== --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
  const tipoPago = document.getElementById('tipoPago');
  const opcionesDinamicas = document.getElementById('opcionesDinamicas');
  const opcionesCredito = document.getElementById('opcionesCredito');
  const opcionesPersonalizado = document.getElementById('opcionesPersonalizado');

  const planPagosDiv = document.getElementById('plan-pagos');
  const pagoInicial = document.getElementById('pagoInicial');
  const pagoCreditoInicial = document.getElementById('pagoCreditoInicial');
  const plazoCredito = document.getElementById('plazoCredito');
  const mesesPersonalizado = document.getElementById('mesesPersonalizado');

  window.pagos = [];

  const NOMBRES = [
    "Primer","Segundo","Tercer","Cuarto","Quinto","Sexto",
    "Séptimo","Octavo","Noveno","Décimo","Undécimo","Duodécimo"
  ];

  function obtenerTotalContrato() {
    const hContrato = document.getElementById('total_contrato_input');
    if (hContrato && hContrato.value !== '') {
      const v = parseFloat(hContrato.value);
      return isNaN(v) ? 0 : Math.max(0, v);
    }
    return 0;
  }

  function fmtMXN(n) {
    return (Number(n) || 0).toLocaleString('es-MX', {
      style: 'currency',
      currency: 'MXN'
    });
  }

  function toISO(d) {
    return d.toISOString().split('T')[0];
  }

  function clamp(n, min, max) {
    n = Number(n);
    if (isNaN(n)) n = min;
    return Math.max(min, Math.min(max, n));
  }

  function clampInt(n, min, max) {
    n = parseInt(n || 0, 10);
    if (isNaN(n)) n = min;
    return Math.max(min, Math.min(max, n));
  }

  function dateByOffsetMonths(offset) {
    const base = new Date();
    const f = new Date(base);
    f.setMonth(f.getMonth() + offset);
    return f;
  }

  function dateLabel(d) {
    return d.toLocaleDateString('es-MX', {
      day: '2-digit',
      month: 'long',
      year: 'numeric'
    });
  }

  function limpiarPlan() {
    planPagosDiv.innerHTML = '';
    window.pagos = [];
  }

  function setEditorTotalBase(v) {
    planPagosDiv.dataset.totalBase = String(Number(v) || 0);
  }

  function getEditorTotalBase() {
    return Number(planPagosDiv.dataset.totalBase || 0) || 0;
  }

  function buildEditorUI(title, totalBase, rows, opts = {}) {
    limpiarPlan();

    const total = Math.max(0, Number(totalBase) || 0);
    if (!total || !rows.length) {
      planPagosDiv.innerHTML = `<p class="text-muted mb-0">Agrega productos para calcular el plan.</p>`;
      return;
    }

    const wrap = document.createElement('div');
    wrap.className = 'pay-editor';

    const head = document.createElement('div');
    head.className = 'pay-head';
    head.innerHTML = `
      <div>
        <div class="pay-title">${title}</div>
        <div class="pay-sub">Sistema de Auto-balanceo: Al editar un pago, los siguientes se ajustan para cuadrar el total. Porcentajes en números enteros.</div>
      </div>
      <div class="pay-base">
        <div class="lbl">Base</div>
        <div class="val">${fmtMXN(total)}</div>
      </div>
    `;
    wrap.appendChild(head);

    const list = document.createElement('div');
    list.className = 'pay-list';
    wrap.appendChild(list);

    const footer = document.createElement('div');
    footer.className = 'pay-footer';
    footer.innerHTML = `
      <div class="text-muted" style="font-size:.9rem">
        Bloquea los pagos que no quieras que el sistema modifique automáticamente.
      </div>
      <div id="paySummary" class="pay-summary"></div>
    `;
    wrap.appendChild(footer);

    if (!document.getElementById('pay-simple-styles')) {
      const style = document.createElement('style');
      style.id = 'pay-simple-styles';
      style.innerHTML = `
        .pay-editor{display:flex;flex-direction:column;gap:12px}
        .pay-head{display:flex;justify-content:space-between;gap:12px;align-items:flex-start;padding:12px 14px;border:1px solid #e5e7eb;border-radius:14px;background:#fff}
        .pay-title{font-weight:700;font-size:1rem}
        .pay-sub{font-size:.88rem;color:#6b7280}
        .pay-base{min-width:170px;text-align:right}
        .pay-base .lbl{font-size:.82rem;color:#6b7280}
        .pay-base .val{font-weight:800;font-size:1rem}
        .pay-list{display:flex;flex-direction:column;gap:12px}
        .pay-card{border:1px solid #e5e7eb;border-radius:16px;background:#fff;padding:14px;transition:border-color 0.2s}
        .pay-card:focus-within{border-color:#3b82f6}
        .pay-card-top{display:flex;justify-content:space-between;gap:10px;align-items:flex-start;margin-bottom:12px}
        .pay-card-top .h{font-weight:700}
        .pay-card-top .d{font-size:.88rem;color:#6b7280}
        .pay-real{text-align:right}
        .pay-real .mxn{font-weight:800}
        .pay-real .pct{font-size:.84rem;color:#6b7280}
        .pay-card-mid{display:flex;flex-wrap:wrap;gap:10px;align-items:center}
        .pay-card .field{position:relative}
        .pay-card .field .suffix{position:absolute;right:12px;top:50%;transform:translateY(-50%);font-size:.84rem;color:#6b7280;pointer-events:none}
        .pay-card .input{padding-right:48px}
        .pay-card .lock{display:flex;align-items:center;gap:8px;font-size:.92rem;cursor:pointer}
        .pay-card .btn-del{border:none;background:#fee2e2;color:#991b1b;padding:9px 12px;border-radius:10px;font-weight:700}
        .pay-card .btn-del:hover{background:#fecaca}
        .pay-note{margin-top:10px;font-size:.86rem;color:#6b7280}
        .pay-summary{display:flex;flex-wrap:wrap;gap:8px;justify-content:flex-end}
        .pay-summary .pill{display:inline-flex;align-items:center;gap:6px;padding:7px 10px;border-radius:999px;font-weight:700;font-size:.83rem;transition:all 0.3s}
        .pay-summary .ok{background:#dcfce7;color:#166534}
        .pay-summary .warn{background:#fef3c7;color:#92400e}
        .pay-summary .bad{background:#fee2e2;color:#b91c1c}
        @media(max-width:768px){
          .pay-head{flex-direction:column}
          .pay-base{text-align:left}
          .pay-card-mid{flex-direction:column;align-items:stretch}
          .pay-card .field{width:100% !important;min-width:100% !important}
          .pay-card .btn-del{width:100%}
        }
      `;
      document.head.appendChild(style);
    }

    planPagosDiv.appendChild(wrap);

    rows.forEach((r, i) => {
      const f = dateByOffsetMonths(r.offset || 0);
      const pct = clamp(r.pct ?? (100 / rows.length), 0, 100);
      const amount = total * pct / 100;

      const canDelete = rows.length > 1 && !opts.readonly && (i !== 0);
      const delBtn = canDelete
        ? `<button type="button" class="btn-del" data-action="delete">Eliminar</button>`
        : `<button type="button" class="btn-del" style="opacity:.45" disabled>Eliminar</button>`;

      const card = document.createElement('div');
      card.className = 'pay-card';
      card.dataset.idx = String(i);
      card.dataset.mes = toISO(f);
      card.innerHTML = `
        <div class="pay-card-top">
          <div>
            <div class="h">${r.desc || (i === 0 ? 'Pago inicial' : ('Pago ' + (i + 1)))}</div>
            <div class="d">${dateLabel(f)}</div>
          </div>
          <div class="pay-real">
            <div class="mxn" data-real="mxn">${fmtMXN(amount)}</div>
            <div class="pct" data-real="pct">% real: ${Math.round(pct)}</div>
          </div>
        </div>

        <div class="pay-card-mid">
          <label class="lock">
            <input type="checkbox" class="form-check-input lock-input" ${r.locked ? 'checked' : ''}>
            <span>Bloquear</span>
          </label>

          <div class="field" style="flex:1;min-width:220px;">
            <input type="number" class="form-control input pct-input" min="0" max="100" step="1" value="${Math.round(pct)}">
            <span class="suffix">%</span>
          </div>

          <div class="field" style="flex:1;min-width:220px;">
            <input type="number" class="form-control input amt-input" min="0" max="${total.toFixed(2)}" step="0.01" value="${amount.toFixed(2)}">
            <span class="suffix">MXN</span>
          </div>

          ${delBtn}
        </div>
      `;

      list.appendChild(card);

      const pctEl = card.querySelector('.pct-input');
      const amtEl = card.querySelector('.amt-input');
      const lockEl = card.querySelector('.lock-input');

      if (opts.readonly) {
        pctEl.disabled = true;
        amtEl.disabled = true;
        lockEl.disabled = true;
      } else if (lockEl.checked) {
        pctEl.disabled = true;
        amtEl.disabled = true;
      }
    });

    // ========================================================
    // FUNCIÓN DE CASCADA (Balanceo hacia abajo)
    // ========================================================
    function redistribuirHaciaAbajo(editIndex, allCards, totalMonto) {
      if (editIndex >= allCards.length - 1) return;

      let sumaArriba = 0;
      for (let i = 0; i < editIndex; i++) {
        sumaArriba += parseFloat(allCards[i].querySelector('.amt-input').value) || 0;
      }

      const cardActual = allCards[editIndex];
      let montoActual = parseFloat(cardActual.querySelector('.amt-input').value) || 0;

      const maximoPermitido = totalMonto - sumaArriba;
      if (montoActual > maximoPermitido) {
        montoActual = maximoPermitido; 
      }

      let restanteParaAbajo = Math.max(0, totalMonto - sumaArriba - montoActual);

      let filasDesbloqueadas = [];
      for (let i = editIndex + 1; i < allCards.length; i++) {
        if (!allCards[i].querySelector('.lock-input').checked) {
          filasDesbloqueadas.push(allCards[i]);
        } else {
          restanteParaAbajo -= (parseFloat(allCards[i].querySelector('.amt-input').value) || 0);
        }
      }

      restanteParaAbajo = Math.max(0, restanteParaAbajo);
      if (filasDesbloqueadas.length > 0) {
        const montoPorFila = restanteParaAbajo / filasDesbloqueadas.length;
        const pctPorFila = totalMonto > 0 ? (montoPorFila / totalMonto) * 100 : 0;

        filasDesbloqueadas.forEach(c => {
          c.querySelector('.amt-input').value = montoPorFila.toFixed(2);
          c.querySelector('.pct-input').value = Math.round(pctPorFila); // Sin decimales
        });
      }
    }

    // ========================================================
    // EVENTOS DE ENTRADA (TOTALMENTE LIBRES)
    // ========================================================
    list.addEventListener('input', function (ev) {
      const card = ev.target.closest('.pay-card');
      if (!card) return;

      const cards = Array.from(planPagosDiv.querySelectorAll('.pay-card'));
      const editIndex = cards.indexOf(card);
      const total = getEditorTotalBase();
      const pctEl = card.querySelector('.pct-input');
      const amtEl = card.querySelector('.amt-input');

      // Si escribimos en porcentaje, no tocamos su valor, solo actualizamos el monto
      if (ev.target.classList.contains('pct-input')) {
        if (pctEl.value === '') {
          amtEl.value = ''; 
        } else {
          let pct = parseFloat(pctEl.value);
          if (!isNaN(pct)) {
            const amt = total > 0 ? (total * pct / 100) : 0;
            amtEl.value = amt.toFixed(2);
          }
        }
      }

      if (ev.target.classList.contains('amt-input')) {
        if (amtEl.value === '') {
          pctEl.value = ''; 
        } else {
          let amt = parseFloat(amtEl.value);
          if (!isNaN(amt)) {
            const pct = total > 0 ? (amt / total) * 100 : 0;
            pctEl.value = Math.round(pct); // Entero inmediato
          }
        }
      }

      redistribuirHaciaAbajo(editIndex, cards, total);
      actualizarResumenPagos();
    });

    // Validar topes y dar formato solo al quitar el foco (blur/change)
    list.addEventListener('change', function (ev) {
      const card = ev.target.closest('.pay-card');
      if (!card) return;

      const total = getEditorTotalBase();
      const pctEl = card.querySelector('.pct-input');
      const amtEl = card.querySelector('.amt-input');
      const cards = Array.from(planPagosDiv.querySelectorAll('.pay-card'));
      const editIndex = cards.indexOf(card);

      if (ev.target.classList.contains('pct-input') || ev.target.classList.contains('amt-input')) {
        let sumaArriba = 0;
        for (let i = 0; i < editIndex; i++) {
          sumaArriba += parseFloat(cards[i].querySelector('.amt-input').value) || 0;
        }

        let maximoPermitido = total - sumaArriba;
        let finalAmt = parseFloat(amtEl.value) || 0;

        // Limite superior a 100% solo si el usuario se excede al salir del campo
        if (ev.target.classList.contains('pct-input')) {
           let rawPct = parseFloat(pctEl.value) || 0;
           if (rawPct > 100) rawPct = 100;
           finalAmt = total > 0 ? (total * rawPct / 100) : 0;
        }

        if(finalAmt > maximoPermitido) {
           finalAmt = maximoPermitido; 
        }

        amtEl.value = finalAmt.toFixed(2);
        pctEl.value = Math.round(total > 0 ? (finalAmt/total)*100 : 0); // Cerrar a entero
      }

      if (ev.target.classList.contains('lock-input')) {
        const lockEl = card.querySelector('.lock-input');
        if (lockEl.checked) {
          pctEl.disabled = true;
          amtEl.disabled = true;
        } else {
          pctEl.disabled = false;
          amtEl.disabled = false;
        }
        redistribuirHaciaAbajo(editIndex, cards, total);
      }

      actualizarResumenPagos();
    });

    list.addEventListener('click', function (ev) {
      const btn = ev.target.closest('[data-action="delete"]');
      if (!btn) return;

      const card = btn.closest('.pay-card');
      if (!card) return;

      card.remove();

      const cards = Array.from(planPagosDiv.querySelectorAll('.pay-card'));
      cards.forEach((c, i) => c.dataset.idx = String(i));
      
      if(cards.length > 0) redistribuirHaciaAbajo(0, cards, getEditorTotalBase());
      actualizarResumenPagos();
    });

    actualizarResumenPagos();
  }

  function actualizarResumenPagos() {
    const total = getEditorTotalBase();
    const cards = Array.from(planPagosDiv.querySelectorAll('.pay-card'));
    if (!cards.length) {
      window.pagos = [];
      return;
    }

    let sumaPct = 0;
    let sumaAmt = 0;

    window.pagos = cards.map((card, i) => {
      const pctEl = card.querySelector('.pct-input');
      const amtEl = card.querySelector('.amt-input');
      const mxn = card.querySelector('[data-real="mxn"]');
      const pct = card.querySelector('[data-real="pct"]');

      let porcentaje = Math.round(parseFloat(pctEl?.value) || 0);
      let monto = Math.max(0, parseFloat(amtEl?.value) || 0);

      sumaPct += porcentaje;
      sumaAmt += monto;

      if (mxn) mxn.textContent = fmtMXN(monto);
      if (pct) pct.textContent = `% real: ${porcentaje}`;

      return {
        cuota: monto.toFixed(2),
        descripcion: (card.querySelector('.h')?.textContent || (i === 0 ? 'Pago inicial' : `Pago ${i + 1}`)).trim(),
        mes: card.dataset.mes
      };
    });

    const diffAmt = total - sumaAmt;
    const okAmt = Math.abs(diffAmt) < 0.5;

    const summary = planPagosDiv.querySelector('#paySummary');
    if (!summary) return;

    const amtClass = okAmt ? 'ok' : (sumaAmt > total + 0.5 ? 'bad' : 'warn');
    let amtMsg = okAmt ? `Cuadre exacto: ${fmtMXN(sumaAmt)}` : (sumaAmt > total + 0.5 ? `Te pasas ${fmtMXN(Math.abs(diffAmt))}` : `Faltan ${fmtMXN(Math.abs(diffAmt))}`);

    summary.innerHTML = `<span class="pill ${amtClass}">${amtMsg}</span>`;
  }

  function renderContado() {
    const total = obtenerTotalContrato();
    setEditorTotalBase(total);
    buildEditorUI('Pago de Contado', total, [
      { desc: 'Pago único', offset: 0, pct: 100, locked: true }
    ], { readonly: true });
  }

  function renderEstatico() {
    const total = obtenerTotalContrato();
    setEditorTotalBase(total);
    if (total <= 0) return buildEditorUI('Plan Fijo', 0, []);

    if (total < 500000) {
      buildEditorUI('Plan Fijo', total, [
        { desc: 'Pago inicial', offset: 0, pct: 50, locked: false },
        { desc: 'Primer pago', offset: 1, pct: 25, locked: false },
        { desc: 'Segundo pago', offset: 2, pct: 25, locked: false },
      ]);
      return;
    }

    const pctIni = 40;
    const restantePct = 100 - pctIni;
    const restante = total * (restantePct / 100);
    const n = Math.min(6, Math.max(4, Math.ceil(restante / 50000)));

    const each = restantePct / n;
    const rows = [{ desc: 'Pago inicial', offset: 0, pct: pctIni, locked: false }];
    for (let i = 0; i < n; i++) {
      rows.push({
        desc: `${NOMBRES[i] || ('Pago ' + (i + 1))} pago`,
        offset: i + 1,
        pct: each,
        locked: false
      });
    }

    buildEditorUI('Plan Fijo', total, rows);
  }

  function renderDinamico() {
    const total = obtenerTotalContrato();
    setEditorTotalBase(total);
    if (total <= 0) return buildEditorUI('Plan Flexible', 0, []);

    const ini = parseFloat(pagoInicial?.value) || 0;
    if (ini <= 0 || ini >= total) {
      buildEditorUI('Plan Flexible', total, [
        { desc: 'Pago inicial', offset: 0, pct: 0, locked: false },
        { desc: 'Pago 1', offset: 1, pct: 50, locked: false },
        { desc: 'Pago 2', offset: 2, pct: 50, locked: false },
      ]);
      return;
    }

    const n = (total < 150000) ? 2 : (total < 400000) ? 4 : 6;
    const pctIni = (ini / total) * 100;
    const each = (100 - pctIni) / n;

    const rows = [{ desc: 'Pago inicial', offset: 0, pct: pctIni, locked: true }];
    for (let i = 0; i < n; i++) {
      rows.push({ desc: `Pago ${i + 1}`, offset: i + 1, pct: each, locked: false });
    }

    buildEditorUI('Plan Flexible', total, rows);
  }

  function renderCredito() {
    const totalContrato = obtenerTotalContrato();
    if (totalContrato <= 0) {
      setEditorTotalBase(0);
      return buildEditorUI('Plan a Crédito', 0, []);
    }

    const ini = parseFloat(pagoCreditoInicial?.value) || 0;
    const plazo = clampInt(plazoCredito?.value, 1, 120);

    if (ini < 0 || ini >= totalContrato) {
      setEditorTotalBase(totalContrato);
      buildEditorUI('Plan a Crédito', totalContrato, [
        { desc: 'Pago inicial', offset: 0, pct: 0, locked: false },
        { desc: 'Pago 1', offset: 1, pct: 50, locked: false },
        { desc: 'Pago 2', offset: 2, pct: 50, locked: false },
      ]);
      return;
    }

    const tasa = 0.05;
    const monto = totalContrato - ini;
    const totalFinanciadoTeorico = monto + (monto * tasa * plazo);
    const totalConCredito = ini + totalFinanciadoTeorico;

    setEditorTotalBase(totalConCredito);

    const pctIni = (ini / totalConCredito) * 100;
    const each = (100 - pctIni) / plazo;

    const rows = [{ desc: 'Pago inicial', offset: 0, pct: pctIni, locked: true }];
    for (let i = 0; i < plazo; i++) {
      rows.push({ desc: `Pago ${i + 1}`, offset: i + 1, pct: each, locked: false });
    }

    buildEditorUI('Plan a Crédito', totalConCredito, rows);
  }

  function renderPersonalizado() {
    const total = obtenerTotalContrato();
    const meses = clampInt(mesesPersonalizado?.value, 1, 60);
    const count = meses + 1;
    setEditorTotalBase(total);

    if (total <= 0) return buildEditorUI('Plan Personalizado', 0, []);

    const rows = [];
    for (let i = 0; i < count; i++) {
      rows.push({
        desc: i === 0 ? 'Pago inicial' : `${NOMBRES[i - 1] || ('Pago ' + (i + 1))} pago`,
        offset: i,
        pct: (100 / count),
        locked: false
      });
    }

    buildEditorUI('Plan Personalizado', total, rows);
  }

  function renderSegunTipo() {
    const t = (tipoPago?.value || '').toLowerCase();

    if (opcionesDinamicas) opcionesDinamicas.style.display = (t === 'dinamico') ? 'block' : 'none';
    if (opcionesCredito) opcionesCredito.style.display = (t === 'credito') ? 'block' : 'none';
    if (opcionesPersonalizado) opcionesPersonalizado.style.display = (t === 'personalizado') ? 'block' : 'none';

    if (t === 'contado') return renderContado();
    if (t === 'estatico') return renderEstatico();
    if (t === 'dinamico') return renderDinamico();
    if (t === 'credito') return renderCredito();
    if (t === 'personalizado') return renderPersonalizado();

    limpiarPlan();
  }

  tipoPago?.addEventListener('change', renderSegunTipo);

  [pagoInicial, pagoCreditoInicial, plazoCredito].forEach(inp => {
    inp?.addEventListener('input', function () {
      const t = (tipoPago?.value || '').toLowerCase();
      if (t === 'dinamico' || t === 'credito') renderSegunTipo();
    });
  });

  mesesPersonalizado?.addEventListener('input', function () {
    if ((tipoPago?.value || '').toLowerCase() === 'personalizado') renderPersonalizado();
  });

  mesesPersonalizado?.addEventListener('change', function () {
    if ((tipoPago?.value || '').toLowerCase() === 'personalizado') renderPersonalizado();
  });

  const onTotalChanged = () => {
    const t = (tipoPago?.value || '').toLowerCase();
    if (t === 'personalizado') {
      renderPersonalizado();
      return;
    }
    renderSegunTipo();
  };

  window.addEventListener('total:changed', onTotalChanged);
  renderSegunTipo();
});
</script>

{{-- ==================== Serializar pagos_json al enviar ==================== --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('form-propuesta');
  const inputPagosJson = document.getElementById('pagosJsonInput');
  const selectPlan = document.getElementById('tipoPago');
  const planPagosDiv = document.getElementById('plan-pagos');

  form.addEventListener('submit', function (e) {
    if (!selectPlan || !selectPlan.value) {
      alert('Selecciona un plan de pagos antes de continuar.');
      e.preventDefault();
      return;
    }

    if (!window.pagos || window.pagos.length === 0) {
      alert('No hay pagos definidos, por favor selecciona o genera un plan de pagos.');
      e.preventDefault();
      return;
    }

    const totalBase = Number(planPagosDiv?.dataset?.totalBase || 0) || 0;
    const suma = window.pagos.reduce((acc, p) => acc + (parseFloat(p.cuota) || 0), 0);

    if (suma > totalBase + 0.5) {
      alert('La suma de los pagos supera el total permitido del contrato. Ajusta los montos antes de guardar.');
      e.preventDefault();
      return;
    }

    inputPagosJson.value = JSON.stringify(window.pagos);
  });
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('form-propuesta');
  const inputPagosJson = document.getElementById('pagosJsonInput');
  const selectPlan = document.getElementById('tipoPago');
  const planPagosDiv = document.getElementById('plan-pagos');

  form.addEventListener('submit', function (e) {
    if (!selectPlan || !selectPlan.value) {
      alert('Selecciona un plan de pagos antes de continuar.');
      e.preventDefault();
      return;
    }

    if (!window.pagos || window.pagos.length === 0) {
      alert('No hay pagos definidos, por favor selecciona o genera un plan de pagos.');
      e.preventDefault();
      return;
    }

    const totalBase = Number(planPagosDiv?.dataset?.totalBase || 0) || 0;
    const suma = window.pagos.reduce((acc, p) => acc + (parseFloat(p.cuota) || 0), 0);

    if (suma > totalBase + 0.5) {
      alert('La suma de los pagos supera el total permitido del contrato. Ajusta los montos antes de guardar.');
      e.preventDefault();
      return;
    }

    inputPagosJson.value = JSON.stringify(window.pagos);
  });
});
</script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const buscarProducto = document.getElementById("buscarProducto");
    const dropdownProductos = document.getElementById("dropdownProductos");
    let controladorAbort = new AbortController();

    function realizarBusqueda(searchQuery) {
        controladorAbort.abort();
        controladorAbort = new AbortController();

        if (searchQuery.length > 1) {
            Promise.all([
                fetch("{{ route('productos.search') }}?search=" + encodeURIComponent(searchQuery), { signal: controladorAbort.signal }).then(res => res.json()),
                fetch("{{ route('paquetes.search') }}?search=" + encodeURIComponent(searchQuery), { signal: controladorAbort.signal }).then(res => res.json())
            ])
            .then(([productos, paquetes]) => mostrarResultados(paquetes, productos))
            .catch(error => {
                if (error.name !== "AbortError") console.error("Error en la búsqueda:", error);
            });
        } else {
            restaurarListaOriginal();
        }
    }

    function mostrarResultados(paquetes, productos) {
        dropdownProductos.innerHTML = `
            <li>
                <button type="button" class="dropdown-item modern-dropdown-item" data-bs-toggle="modal" data-bs-target="#modal1">
                    + Crear Producto
                </button>
            </li>
        `;

        if (paquetes.length > 0) {
            paquetes.forEach(paquete => {
                let li = document.createElement("li");
                li.innerHTML = `
                    <button type="button" class="dropdown-item modern-dropdown-item"
                            data-id="${paquete.id}"
                            data-productos='${JSON.stringify(paquete.productos)}'
                            onclick="agregarPaqueteDesdeData(this)">
                        ${String(paquete.nombre || '').toUpperCase()} - PAQUETE
                    </button>
                `;
                dropdownProductos.appendChild(li);
            });
        }

        productos.sort((a, b) => (a.tipo_equipo || '').localeCompare(b.tipo_equipo || ''));

        if (productos.length > 0) {
            productos.forEach(producto => {
                const tipo = (producto.tipo_equipo || '').replace(/'/g,"\\'");
                const modelo = (producto.modelo || '').replace(/'/g,"\\'");
                const marca = (producto.marca || '').replace(/'/g,"\\'");
                const img = (producto.imagen || '').replace(/'/g,"\\'");

                let li = document.createElement("li");
                li.innerHTML = `
                    <button type="button" class="dropdown-item modern-dropdown-item d-flex align-items-center"
                            onclick="agregarProductoDesdeDropdown(${producto.id}, '${tipo}', '${modelo}', '${marca}', ${producto.precio}, '${img}')">
                        <img src="/storage/${img}" alt="${tipo}" class="modern-product-img me-2" style="width: 40px; height: 40px; object-fit: cover; border-radius: 6px;">
                        <div class="flex-grow-1 modern-product-info">
                            <strong>${tipo}</strong> - ${modelo} ${marca}
                            <br>
                            <span class="text-muted modern-product-price">$${producto.precio}</span>
                        </div>
                        <span class="badge modern-badge">${producto.stock} unidades</span>
                    </button>
                `;
                dropdownProductos.appendChild(li);
            });
        }

        if (paquetes.length === 0 && productos.length === 0) {
            dropdownProductos.innerHTML += '<li><button type="button" class="dropdown-item text-muted">No se encontraron resultados</button></li>';
        }
    }

    function restaurarListaOriginal() {
        Promise.all([
            fetch("{{ route('productos.search') }}?search=").then(res => res.json()),
            fetch("{{ route('paquetes.search') }}?search=").then(res => res.json())
        ])
        .then(([productos, paquetes]) => mostrarResultados(paquetes, productos))
        .catch(error => console.error("Error al restaurar lista:", error));
    }

    buscarProducto.addEventListener("input", function () {
        realizarBusqueda(this.value.trim());
    });

    buscarProducto.addEventListener("keydown", function (event) {
        if (event.key === "Enter") {
            event.preventDefault();
            realizarBusqueda(this.value.trim());
        }
    });

    buscarProducto.addEventListener("focus", function () {
        if (this.value.trim() === "") {
            restaurarListaOriginal();
        }
    });

    restaurarListaOriginal();

    window.agregarPaqueteDesdeData = function (element) {
        let productos = JSON.parse(element.getAttribute("data-productos"));
        (productos || []).forEach(producto => {
            agregarProductoDesdeDropdown(
                producto.id,
                producto.tipo_equipo,
                producto.modelo,
                producto.marca,
                parseFloat(producto.precio),
                producto.imagen
            );
        });
    };
});
</script>

<script>
const IVA_RATE = 0.16;

function formatMoney(n) {
  const num = Number(n || 0);
  return num.toLocaleString('es-MX', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  });
}

const to2 = (n) => Number(n || 0).toFixed(2);

document.addEventListener('DOMContentLoaded', () => {
  bindRealtimeListeners();

  const form = document.getElementById('form-propuesta');
  if (form) {
    form.addEventListener('submit', (e) => {
      actualizarTotal();
      prepararProductosJSON();
      prepararEquiposCuentaJSON();

      const productosJson = document.getElementById('productos_json')?.value || '[]';
      if (!productosJson || productosJson === '[]') {
        e.preventDefault();
        alert('Debes agregar al menos un producto.');
      }
    });
  }

  actualizarTotal();
  prepararProductosJSON();
  prepararEquiposCuentaJSON();
});

function bindRealtimeListeners() {
  const recalcNow = () => { actualizarTotal(); prepararProductosJSON(); prepararEquiposCuentaJSON(); };

  if (window.jQuery) {
    $(document).on('input keyup change paste mouseup wheel', '#descuento, #envio', recalcNow);
    $(document).on('change click keyup', '#aplica_iva', recalcNow);
    $(document).on('input keyup change paste wheel', '.cantidad, .sobreprecio', function () {
      actualizarSubtotal(this);
    });
    $(document).on('change', '.gift-toggle', function () {
      actualizarSubtotal(this);
    });
  }
}

function isGiftRow(tr){
  return !!tr.querySelector('.gift-toggle')?.checked;
}

function agregarProductoDesdeDropdown(id, nombre, modelo, marca, precio, imagen) {
  if (!id || !nombre) return;

  if (document.querySelector(`#tabla-productos tbody tr[data-id="${id}"]`)) {
    alert('Este producto ya ha sido agregado.');
    return;
  }

  let img = (imagen || '').toString();
  img = img.replace(/^\/+/, '');
  const imagenRuta = img.startsWith('storage/') ? '/' + img : '/storage/' + img;

  const fila = `
    <tr data-id="${id}" data-precio="${Number(precio) || 0}">
      <td><img src="${imagenRuta}" alt="${nombre}" style="width:50px;height:50px;object-fit:cover;border-radius:6px;"></td>
      <td class="equipo">${nombre}</td>
      <td class="modelo">${modelo || ''}</td>
      <td class="marca">${marca || ''}</td>
      <td><input type="number" class="form-control cantidad" value="1" min="1" step="1"></td>
      <td class="subtotal">${(Number(precio) || 0).toFixed(2)}</td>
      <td><input type="number" class="form-control sobreprecio" value="0" min="0" step="0.01"></td>
      <td>
        <div class="gift-switch">
          <input type="checkbox" class="form-check-input gift-toggle" title="Marcar como regalo">
          <span class="gift-muted">Regalo</span>
        </div>
      </td>
      <td><button type="button" class="btn btn-sm btn-danger" onclick="eliminarFila(this)">Eliminar</button></td>
    </tr>
  `;
  const tbody = document.querySelector('#tabla-productos tbody');
  if (tbody) tbody.insertAdjacentHTML('beforeend', fila);

  actualizarTotal();
  prepararProductosJSON();

  const buscador = document.getElementById('buscarProducto');
  if (buscador) buscador.value = '';
  document.querySelectorAll('.dropdown-menu.show').forEach(el => el.classList.remove('show'));
}

function eliminarFila(btn) {
  const tr = btn.closest('tr');
  if (tr) tr.remove();
  actualizarTotal();
  prepararProductosJSON();
}

function actualizarSubtotal(input) {
  const fila = input.closest('tr');
  if (!fila) return;

  const regalo = isGiftRow(fila);
  const cantidad = Math.max(1, parseFloat(fila.querySelector('.cantidad')?.value) || 1);
  const sobreprecioEl = fila.querySelector('.sobreprecio');
  let sobreprecio = Math.max(0, parseFloat(sobreprecioEl?.value) || 0);
  const precioBase = Math.max(0, parseFloat(fila.getAttribute('data-precio')) || 0);

  const celda = fila.querySelector('.subtotal');

  if (regalo) {
    if (sobreprecioEl) { sobreprecioEl.value = to2(0); sobreprecioEl.disabled = true; }
    if (celda) celda.innerHTML = `<span class="gift-badge">REGALO</span>`;
  } else {
    if (sobreprecioEl) sobreprecioEl.disabled = false;
    const nuevoSubtotal = cantidad * (precioBase + sobreprecio);
    if (celda) celda.textContent = to2(nuevoSubtotal);
  }

  actualizarTotal();
  prepararProductosJSON();
}

function actualizarTotal() {
  let subtotal = 0;

  document.querySelectorAll('#tabla-productos tbody tr').forEach(tr => {
    const regalo = isGiftRow(tr);

    const cantidad = Math.max(1, parseFloat(tr.querySelector('.cantidad')?.value) || 1);
    const sobreprecioEl = tr.querySelector('.sobreprecio');
    const sobreprecio = regalo ? 0 : Math.max(0, parseFloat(sobreprecioEl?.value) || 0);
    const precioBase = Math.max(0, parseFloat(tr.getAttribute('data-precio')) || 0);

    const celda = tr.querySelector('.subtotal');

    if (regalo) {
      if (celda) celda.innerHTML = `<span class="gift-badge">REGALO</span>`;
      if (sobreprecioEl) { sobreprecioEl.value = to2(0); sobreprecioEl.disabled = true; }
      return;
    }

    if (sobreprecioEl) sobreprecioEl.disabled = false;

    const sub = cantidad * (precioBase + sobreprecio);
    if (celda) celda.textContent = to2(sub);
    subtotal += sub;
  });

  const descuentoInput = document.getElementById('descuento');
  const envioInput = document.getElementById('envio');

  const descuento = Math.max(0, parseFloat(descuentoInput?.value) || 0);
  const envio = Math.max(0, parseFloat(envioInput?.value) || 0);

  const descuentoValidado = Math.min(descuento, subtotal);

  let baseIVA = subtotal - descuentoValidado + envio;
  if (baseIVA < 0) baseIVA = 0;

  const aplicaIVA = document.getElementById('aplica_iva')?.checked ? true : false;
  const iva = aplicaIVA ? baseIVA * IVA_RATE : 0;

  const total = subtotal - descuentoValidado + envio + iva;

  const aCuentaInput = document.getElementById('valor_a_cuenta');
  let valorCuenta = Math.max(0, parseFloat(aCuentaInput?.value) || 0);
  if (valorCuenta > total) valorCuenta = total;
  if (aCuentaInput) aCuentaInput.value = to2(valorCuenta);

  const totalContrato = total - valorCuenta;

  const subSpan = document.getElementById('subtotal');
  const ivaSpan = document.getElementById('iva');
  const totSpan = document.getElementById('total');
  if (subSpan) subSpan.textContent = formatMoney(subtotal);
  if (ivaSpan) ivaSpan.textContent = formatMoney(iva);
  if (totSpan) totSpan.textContent = formatMoney(total);

  const cuentaSpan = document.getElementById('valor_cuenta_view');
  const contratoSpan = document.getElementById('total_contrato_view');
  if (cuentaSpan) cuentaSpan.textContent = formatMoney(valorCuenta);
  if (contratoSpan) contratoSpan.textContent = formatMoney(totalContrato);

  const setVal = (id, val) => { const el = document.getElementById(id); if (el) el.value = to2(val); };
  setVal('subtotal_input', subtotal);
  setVal('iva_input', iva);
  setVal('total_input', total);
  setVal('total_a_cuenta_input', valorCuenta);
  setVal('total_contrato_input', totalContrato);

  if (typeof window !== 'undefined') window.dispatchEvent(new Event('total:changed'));
}

function prepararProductosJSON() {
  const productos = [];
  document.querySelectorAll('#tabla-productos tbody tr').forEach(tr => {
    const regalo = isGiftRow(tr);

    const cantidad = Math.max(1, parseFloat(tr.querySelector('.cantidad')?.value) || 1);
    const precioUnit = Math.max(0, parseFloat(tr.getAttribute('data-precio')) || 0);
    const sobreprecioEl = tr.querySelector('.sobreprecio');
    const sobreprecio = regalo ? 0 : Math.max(0, parseFloat(sobreprecioEl?.value) || 0);

    let subtotal = 0;
    if (!regalo) {
      const celda = tr.querySelector('.subtotal');
      subtotal = Math.max(0, parseFloat(celda?.textContent) || 0);
      if (!subtotal) subtotal = cantidad * (precioUnit + sobreprecio);
    }

    productos.push({
      producto_id: tr.getAttribute('data-id'),
      cantidad: cantidad,
      precio_unitario: regalo ? 0 : precioUnit,
      sobreprecio: sobreprecio,
      subtotal: subtotal,
      es_regalo: regalo
    });
  });

  const hidden = document.getElementById('productos_json');
  if (hidden) hidden.value = JSON.stringify(productos);
}
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const btnAdd = document.getElementById('btn-add-equipo-cuenta');
    const tablaEquipos = document.getElementById('tabla-equipos-cuenta').querySelector('tbody');

    btnAdd?.addEventListener('click', function () {
        agregarEquipoCuentaFila();
    });

    tablaEquipos.addEventListener('input', function (e) {
        if (e.target.classList.contains('ec-valor') ||
            e.target.classList.contains('ec-tipo') ||
            e.target.classList.contains('ec-marca') ||
            e.target.classList.contains('ec-modelo') ||
            e.target.classList.contains('ec-serie')) {
            prepararEquiposCuentaJSON();
        }
    });

    tablaEquipos.addEventListener('click', function (e) {
        if (e.target.classList.contains('btn-remove-equipo')) {
            const tr = e.target.closest('tr');
            if (tr) tr.remove();
            prepararEquiposCuentaJSON();
        }
    });
});

function agregarEquipoCuentaFila() {
    const tbody = document.querySelector('#tabla-equipos-cuenta tbody');
    if (!tbody) return;

    const row = document.createElement('tr');
    row.innerHTML = `
        <td><input type="text" class="form-control ec-tipo text-uppercase" placeholder="Ej. TORRE 1588"></td>
        <td><input type="text" class="form-control ec-marca text-uppercase" placeholder="Marca"></td>
        <td><input type="text" class="form-control ec-modelo text-uppercase" placeholder="Modelo"></td>
        <td><input type="text" class="form-control ec-serie text-uppercase" placeholder="N° serie"></td>
        <td><input type="number" class="form-control ec-valor" min="0" step="0.01" value="0"></td>
        <td><button type="button" class="btn btn-sm btn-outline-danger btn-remove-equipo">&times;</button></td>
    `;
    tbody.appendChild(row);
}

function prepararEquiposCuentaJSON() {
    const filas = document.querySelectorAll('#tabla-equipos-cuenta tbody tr');
    const equipos = [];
    let totalCuenta = 0;

    filas.forEach(tr => {
        const tipo = tr.querySelector('.ec-tipo')?.value?.trim() || null;
        const marca = tr.querySelector('.ec-marca')?.value?.trim() || null;
        const modelo = tr.querySelector('.ec-modelo')?.value?.trim() || null;
        const serie = tr.querySelector('.ec-serie')?.value?.trim() || null;
        let valor = parseFloat(tr.querySelector('.ec-valor')?.value) || 0;
        valor = Math.max(0, valor);

        if (!tipo && !marca && !modelo && !serie && valor === 0) return;

        equipos.push({
            tipo_equipo: tipo,
            marca: marca,
            modelo: modelo,
            numero_serie: serie,
            valor_a_cuenta: valor
        });

        totalCuenta += valor;
    });

    const hidden = document.getElementById('equipos_cuenta_json');
    if (hidden) hidden.value = JSON.stringify(equipos);

    const inputCuenta = document.getElementById('valor_a_cuenta');
    if (inputCuenta) inputCuenta.value = to2(totalCuenta);

    actualizarTotal();
}
</script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById("search-client");
    const clientList = document.getElementById("client-list");
    const clienteIdInput = document.getElementById("cliente_id");
    const clientDetails = document.getElementById("client-details");
    const formVenta = document.getElementById("form-propuesta");
    const CLIENT_SEARCH_URL = "{{ route('clientes.encontrar') }}";

    if (!searchInput || !clientList || !clienteIdInput || !clientDetails || !formVenta) return;

    formVenta.addEventListener("submit", function (e) {
        if (!clienteIdInput.value) {
            e.preventDefault();
            alert("Por favor selecciona un cliente antes de continuar.");
        }
    });

    function loadClients(search = "") {
        fetch(`${CLIENT_SEARCH_URL}?search=${encodeURIComponent(search)}`, {
            method: "GET",
            headers: {
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                "Accept": "application/json",
            },
        })
        .then(response => response.json())
        .then(clients => {
            clientList.innerHTML = `
                <li>
                    <button type="button" class="dropdown-item modern-dropdown-item" onclick='selectClientFromEncoded("${encodeURIComponent(JSON.stringify({
                        id: 1,
                        nombre: "Público en General",
                        apellido: "",
                        telefono: "",
                        email: "",
                        comentarios: ""
                    }))}")'>
                        Público en General
                    </button>
                </li>
                <li>
                    <button type="button" class="dropdown-item modern-dropdown-item" onclick="openCreateClientModal()">
                        + Crear nuevo cliente
                    </button>
                </li>
            `;

            if (clients.length === 0 && search !== "") {
                clientList.innerHTML += `<li><button type="button" class="dropdown-item disabled">No se encontraron resultados</button></li>`;
            } else {
                clients.forEach(client => {
                    const clientFullName = `${(client.nombre || '').toUpperCase()} ${(client.apellido || '').toUpperCase()}`.trim();
                    const encodedClient = encodeURIComponent(JSON.stringify(client));
                    const clientItem = document.createElement("li");
                    clientItem.innerHTML = `
                        <button type="button" class="dropdown-item modern-dropdown-item" onclick='selectClientFromEncoded("${encodedClient}")'>
                            ${clientFullName || 'SIN NOMBRE'}
                        </button>
                    `;
                    clientList.appendChild(clientItem);
                });
            }
        })
        .catch(error => console.error("Error al cargar clientes:", error));
    }

    window.selectClientFromEncoded = function (encoded) {
        const client = JSON.parse(decodeURIComponent(encoded));
        selectClient(client);
    };

    window.selectClient = function (client) {
        const nombre = (client.nombre || '').toUpperCase();
        const apellido = (client.apellido || '').toUpperCase();

        searchInput.value = `${nombre} ${apellido}`.trim();
        clienteIdInput.value = client.id ?? "";

        clientDetails.innerHTML = `
            <p><strong>Nombre:</strong> ${nombre} ${apellido}</p>
            <p><strong>Teléfono:</strong> ${client.telefono || "No registrado"}</p>
            <p><strong>Email:</strong> ${client.email || "No registrado"}</p>
            <p><strong>Dirección:</strong> ${client.comentarios || "No registrado"}</p>
        `;
        clientDetails.style.padding = "15px";
        clientList.classList.remove("show");
    };

    window.openCreateClientModal = function () {
        const modalFormulario = new bootstrap.Modal(document.getElementById("modal_formulario"));
        modalFormulario.show();
    };

    searchInput.addEventListener("input", () => {
        const search = searchInput.value.trim();
        loadClients(search);
        clientList.classList.add("show");
    });

    searchInput.addEventListener("keydown", e => {
        if (e.key === "Enter") {
            e.preventDefault();
            const search = searchInput.value.trim();
            loadClients(search);
            clientList.classList.add("show");
        }
    });

    loadClients();
});
</script>

<script>
document.addEventListener("DOMContentLoaded", function () {
  const formCrear = document.getElementById("formCrearCliente");
  if (!formCrear) return;

  const CREATE_URL = "{{ route('clientes.store') }}";

  formCrear.addEventListener("submit", async function (e) {
    e.preventDefault();

    try {
      const fd = new FormData(formCrear);
      fd.append('origen', 'cotizacion');

      const res = await fetch(CREATE_URL, {
        method: "POST",
        headers: {
          "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
          "Accept": "application/json",
        },
        body: fd
      });

      const data = await res.json().catch(() => ({}));
      if (!res.ok) {
        alert(data.message || "No se pudo crear el cliente.");
        return;
      }

      const newClientId = data?.cliente_id ?? data?.id ?? null;

      if (newClientId) {
        const clientObj = data?.id ? data : {
          id: newClientId,
          nombre: fd.get('nombre') || '',
          apellido: fd.get('apellido') || '',
          telefono: fd.get('telefono') || '',
          email: fd.get('email') || '',
          comentarios: fd.get('comentarios') || '',
          categoria_id: fd.get('categoria_id') || '',
          origen: 'cotizacion',
        };

        if (typeof window.selectClient === "function") window.selectClient(clientObj);

        const el = document.getElementById("modal_formulario");
        const instance = window.bootstrap?.Modal?.getOrCreateInstance(el);
        instance?.hide();

        formCrear.reset();

        const searchInput = document.getElementById("search-client");
        if (searchInput) searchInput.dispatchEvent(new Event("input"));
      } else {
        alert("Cliente creado, pero el servidor no regresó el ID del cliente.");
      }
    } catch (err) {
      alert("Error al crear cliente.");
    }
  });
});
</script>
@endsection