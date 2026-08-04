@extends('layouts.app')

@section('title', 'Nueva Propuesta')
@section('content')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>



<link rel="stylesheet" href="{{ asset('css/propuestacreate.css') }}?v={{ time() }}">
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="container">
    <form id="form-propuesta" method="POST" action="{{ route('propuestas.store') }}">
        @csrf

        <div class="row">
            {{-- ================================== COLUMNA IZQUIERDA ================================== --}}
            <div class="col-md-3 mt-3">
                {{-- Cliente --}}
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

                {{-- Lugar de la propuesta --}}
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
                            <option value="otro">Otro</option>
                        </select>
                    </div>
                </div>

                {{-- Nota al cliente --}}
                <div class="card modern-card mb-3">
                    <div class="card-header modern-heade">Nota al Cliente</div>
                    <div class="card-body">
                        <textarea name="nota" id="notaCliente" class="form-control modern-textarea" rows="4" placeholder="Escribe una nota..."></textarea>
                    </div>
                </div>

                {{-- Registrado por --}}
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

            {{-- ================================== COLUMNA DERECHA ================================== --}}
            <div class="col-md-9">
                {{-- Buscador de productos / paquetes --}}
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
                                    <button class="dropdown-item modern-dropdown-item" data-bs-toggle="modal" data-bs-target="#modal1">
                                        + Crear Producto
                                    </button>
                                </li>
                                @foreach($productos->sortBy('tipo_equipo') as $producto)
                                    <li>
                                        <button 
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

                {{-- Tabla productos seleccionados --}}
                <div class="card modern-card mt-3">
                    <div class="card-header modern-header">Productos Seleccionados</div>
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
                                        <th>Acción</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Equipos / mercancía a cuenta (TRADE-IN PROPUESTA) --}}
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
                                <tbody>
                                    {{-- filas dinámicas --}}
                                </tbody>
                            </table>
                        </div>
                        <small class="text-muted">
                            Estos equipos se tomarán a cuenta y su suma se reflejará como <strong>valor a cuenta</strong> en el resumen.
                        </small>
                    </div>
                </div>

                {{-- Resumen + Plan de pagos --}}
                <div class="d-flex flex-column flex-md-row">
                    {{-- Resumen --}}
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

                            {{-- Valor a cuenta (calculado desde equipos trade-in) --}}
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

                            {{-- Plan / tipo de pago --}}
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

                            {{-- Dinámico --}}
                            <div id="opcionesDinamicas" style="display: none; margin-top: 1rem;">
                                <label for="pagoInicial">Pago Inicial:</label>
                                <input type="number" id="pagoInicial" class="form-control modern-input w-50" min="0" step="0.01">
                            </div>

                            {{-- Crédito --}}
                            <div id="opcionesCredito" style="display: none; margin-top: 1rem;">
                                <label for="pagoCreditoInicial">Pago Inicial:</label>
                                <input type="number" id="pagoCreditoInicial" class="form-control modern-input w-50" min="0" step="0.01">
                                <label for="plazoCredito" style="margin-top: 0.5rem;">Plazo (meses):</label>
                                <input type="number" id="plazoCredito" class="form-control modern-input w-50" value="6" min="1" step="1">
                            </div>

                            {{-- Personalizado --}}
                            <div id="opcionesPersonalizado" style="display: none; margin-top: 1rem;">
                                <label for="mesesPersonalizado">Selecciona el número de meses:</label>
                                <input type="number" id="mesesPersonalizado" class="form-control modern-input w-50" min="1" step="1" value="1">
                                <div id="listaPagosPersonalizados" class="mt-3"></div>
                            </div>

                            <input type="hidden" id="pagosJsonInput" name="pagos_json" value="">

                            {{-- Ficha técnica --}}
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

                    {{-- Detalle del financiamiento (vista previa) --}}
                    <div class="card modern-card mt-3 w-100 w-md-50 ms-md-3">
                        <div class="card-header modern-header">Detalles del Financiamiento</div>
                        <div class="card-body" id="plan-pagos"></div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

{{-- ======================== SweetAlert2 ======================== --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

{{-- ==================== Buscador de fichas técnicas ==================== --}}
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
                const highlighted = textOriginal.replace(new RegExp(value, 'i'), match => {
                    return `<strong style="color:#4a148c">${match}</strong>`;
                });

                const li = document.createElement('li');
                li.classList.add('list-group-item', 'list-option');
                li.setAttribute('data-id', option.getAttribute('data-id'));
                li.innerHTML = highlighted;
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

{{-- ==================== Generación de plan de pagos ==================== --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
  const tipoPago = document.getElementById('tipoPago');
  const opcionesDinamicas = document.getElementById('opcionesDinamicas');
  const opcionesCredito   = document.getElementById('opcionesCredito');
  const opcionesPersonalizado = document.getElementById('opcionesPersonalizado');

  const listaPagosPersonalizados = document.getElementById('listaPagosPersonalizados');
  const planPagosDiv = document.getElementById('plan-pagos');

  const pagoInicial = document.getElementById('pagoInicial');
  const pagoCreditoInicial = document.getElementById('pagoCreditoInicial');
  const plazoCredito = document.getElementById('plazoCredito');
  const mesesPersonalizado = document.getElementById('mesesPersonalizado');

  const nodoTotal = document.getElementById('total'); // span con el total formateado

  window.pagos = [];

  // 👉 NOMBRES para los pagos
  const NOMBRES = ["Primer","Segundo","Tercer","Cuarto","Quinto","Sexto","Séptimo","Octavo","Noveno","Décimo","Undécimo","Duodécimo"];

  // 👉 Granularidad para redondear: 500
  const PASO_REDONDEO = 500;

  function obtenerTotal() {
    const hContrato = document.getElementById('total_contrato_input');
    if (hContrato && hContrato.value !== '') {
      const v = parseFloat(hContrato.value);
      return isNaN(v) ? 0 : v;
    }
    const txt = nodoTotal ? nodoTotal.textContent : '0';
    return parseFloat((txt || '0').replace(/[^0-9.-]/g, '')) || 0;
  }

  function fmt(n) {
    return (Number(n) || 0).toLocaleString('es-MX', { style:'currency', currency:'MXN' });
  }
  function toISO(d) { return d.toISOString().split('T')[0]; }
  function clampInt(n, min, max){ n=parseInt(n||0,10); if(isNaN(n)) n=min; return Math.max(min, Math.min(max, n)); }
  function cents(n){ return Math.round((Number(n)||0)*100); }

  function distribuirIgual(total, count, fixedMap) {
    const totalC = cents(total);
    const arrC = new Array(count).fill(0);

    const fixedIdx = Object.keys(fixedMap||{}).map(i=>parseInt(i,10)).filter(i => i>=0 && i<count);
    const fixedSum = fixedIdx.reduce((acc,i)=> acc + cents(fixedMap[i]||0), 0);

    let libres = [];
    for (let i=0;i<count;i++) if (!fixedIdx.includes(i)) libres.push(i);

    const remC = totalC - fixedSum;
    if (libres.length <= 0) {
      fixedIdx.forEach(i => arrC[i] = cents(fixedMap[i]||0));
      return arrC.map(c=>c/100);
    }

    const base = Math.floor(remC / libres.length);
    const resto = remC - base*libres.length;

    fixedIdx.forEach(i => arrC[i] = cents(fixedMap[i]||0));

    libres.forEach((i, k) => {
      arrC[i] = base + (k === libres.length-1 ? resto : 0);
    });

    return arrC.map(c=>c/100);
  }

  function ajustarMontosCerrados(total, montos, paso = PASO_REDONDEO, fixedIdx = []) {
    const n = montos.length;
    const totalPesos = Math.round(total);
    const res = new Array(n).fill(0);
    const fixed = new Set(fixedIdx);

    let fixedSum = 0;
    fixed.forEach(idx => {
      if (idx >= 0 && idx < n) {
        const v = Math.round(montos[idx] || 0);
        res[idx] = v;
        fixedSum += v;
      }
    });

    let remainTotal = totalPesos - fixedSum;
    if (remainTotal < 0) remainTotal = 0;

    const libres = [];
    for (let i = 0; i < n; i++) {
      if (!fixed.has(i)) libres.push(i);
    }

    if (!libres.length) {
      const sumRes = res.reduce((a,b)=>a+b,0);
      if (sumRes !== totalPesos) {
        const diff = totalPesos - sumRes;
        res[n-1] += diff;
      }
      return res;
    }

    for (let k = 0; k < libres.length; k++) {
      const idx = libres[k];
      const restantes = libres.length - k;

      if (restantes === 1) {
        res[idx] = remainTotal;
        remainTotal = 0;
        break;
      }

      const target = montos[idx] || (remainTotal / restantes);
      let candidato = Math.floor(target / paso) * paso;
      if (candidato <= 0) candidato = paso;

      while (candidato > 0 && (remainTotal - candidato) < paso * (restantes - 1)) {
        candidato -= paso;
      }

      if (candidato <= 0) {
        candidato = Math.floor(remainTotal / restantes);
      }

      res[idx] = candidato;
      remainTotal -= candidato;
    }

    if (remainTotal !== 0) {
      const last = libres[libres.length - 1] ?? (n - 1);
      res[last] += remainTotal;
    }

    return res;
  }

  function limpiarPlan() {
    planPagosDiv.innerHTML = '';
    window.pagos = [];
  }

  function addPago(monto, mesesOffset, desc) {
    const base = new Date();
    const f = new Date(base); f.setMonth(f.getMonth() + mesesOffset);
    const legible = f.toLocaleDateString('es-MX', { day:'2-digit', month:'long', year:'numeric' });

    const p = document.createElement('p');
    p.innerHTML = `<strong>${desc} - ${legible}:</strong> ${fmt(monto)}`;
    planPagosDiv.appendChild(p);

    window.pagos.push({ cuota: (Number(monto)||0).toFixed(2), descripcion: desc, mes: toISO(f) });
  }

  function renderContado(total) {
    limpiarPlan();
    if (total <= 0) return;
    const arr = ajustarMontosCerrados(total, [total], PASO_REDONDEO);
    addPago(arr[0], 0, 'Pago único');
  }

  function renderDinamico(total) {
    limpiarPlan();
    if (total <= 0) return;
    const ini = parseFloat(pagoInicial?.value)||0;
    if (ini <= 0 || ini >= total) {
      planPagosDiv.innerHTML = '<p style="color:red">Pago inicial inválido</p>';
      return;
    }

    const restante = total - ini;
    const n = (total < 150000) ? 2 : (total < 400000) ? 4 : 6;

    const base = restante / n;
    const montos = [ini];
    for (let i=0; i<n; i++) montos.push(base);

    const cerrados = ajustarMontosCerrados(total, montos, PASO_REDONDEO, [0]);

    addPago(cerrados[0], 0, 'Pago inicial');
    for (let i=1; i<cerrados.length; i++){
      addPago(cerrados[i], i, `Pago ${i}`);
    }
  }

  function renderCredito(total) {
    limpiarPlan();
    if (total <= 0) return;
    const ini   = parseFloat(pagoCreditoInicial?.value)||0;
    const plazo = parseInt(plazoCredito?.value)||6;
    if (ini < 0 || ini >= total) {
      planPagosDiv.innerHTML = '<p style="color:red">Pago inicial de crédito inválido</p>';
      return;
    }
    if (plazo <= 0) {
      planPagosDiv.innerHTML = '<p style="color:red">Plazo inválido</p>';
      return;
    }

    const tasa = 0.05;
    const monto = total - ini;
    const totalCredBase = monto + (monto * tasa * plazo);

    const baseMensual = totalCredBase / plazo;
    const montos = [ini];
    for (let i=0; i<plazo; i++) montos.push(baseMensual);

    const totalConCredito = ini + totalCredBase;
    const cerrados = ajustarMontosCerrados(totalConCredito, montos, PASO_REDONDEO, [0]);

    addPago(cerrados[0], 0, 'Pago inicial');

    let totalFinanciadoReal = 0;
    for (let i=1; i<cerrados.length; i++){
      addPago(cerrados[i], i, `Pago ${i}`);
      totalFinanciadoReal += cerrados[i];
    }

    const info = document.createElement('p');
    info.innerHTML = `<strong>Total a pagar con crédito (sin contar el pago inicial):</strong> ${fmt(totalFinanciadoReal)}`;
    planPagosDiv.appendChild(info);
  }

  function renderEstatico(total) {
    limpiarPlan();
    if (total <= 0) return;

    if (total < 500000) {
      let montos = [
        total * 0.50,
        total * 0.25,
        total * 0.25
      ];
      montos = ajustarMontosCerrados(total, montos, PASO_REDONDEO);

      addPago(montos[0], 0, 'Pago inicial');
      addPago(montos[1], 1, 'Primer pago');
      addPago(montos[2], 2, 'Segundo pago');
      return;
    }

    const ini = total * 0.4;
    const restante = total - ini;

    const n = Math.min(6, Math.max(4, Math.ceil(restante / 50000)));
    const base = restante / n;

    const montos = [ini];
    for (let i=0; i<n; i++) montos.push(base);

    const cerrados = ajustarMontosCerrados(total, montos, PASO_REDONDEO);

    addPago(cerrados[0], 0, 'Pago inicial');
    for (let i=1; i<cerrados.length; i++) {
      addPago(cerrados[i], i, `${NOMBRES[i-1] || ('Pago '+i)} pago`);
    }
  }

  let writingInputs = false;

  function generarPersonalizadoIgualitario() {
    const total = obtenerTotal();
    const meses = clampInt(mesesPersonalizado?.value, 0, 60);
    const count = meses + 1;

    const prev = Array.from(listaPagosPersonalizados.querySelectorAll('input[data-idx]')).map(inp => ({
      idx: parseInt(inp.dataset.idx,10),
      val: parseFloat(inp.value)||0,
      locked: !!inp.dataset.locked
    }));

    const fixedMap = {};
    prev.forEach(p => { if (p.idx < count && p.locked) fixedMap[p.idx] = p.val; });

    const dist = distribuirIgual(total, count, fixedMap);

    const fixedIdx = Object.keys(fixedMap).map(i => parseInt(i,10));
    const distCerr = ajustarMontosCerrados(total, dist, PASO_REDONDEO, fixedIdx);

    writingInputs = true;
    listaPagosPersonalizados.innerHTML = '';

    for (let i=0;i<count;i++){
      const wrap = document.createElement('div');
      wrap.className = 'mb-2';

      const f = new Date(); f.setMonth(f.getMonth() + i);
      const legible = f.toLocaleDateString('es-MX', { day:'2-digit', month:'long', year:'numeric' });

      const label = document.createElement('label');
      label.innerHTML = i===0
        ? `<strong>Pago inicial - ${legible}:</strong>`
        : `<strong>${NOMBRES[i-1] || (i+1 + '°')} pago - ${legible}:</strong>`;

      const row = document.createElement('div');
      row.className = 'd-flex align-items-center gap-2';

      const input = document.createElement('input');
      input.type = 'number';
      input.step = '0.01';
      input.className = 'form-control modern-input w-50';
      input.dataset.idx = i;

      const was = prev.find(p => p.idx === i);
      if (was && was.locked) {
        input.value = (was.val||0).toFixed(2);
        input.dataset.locked = 'true';
      } else {
        input.value = (distCerr[i]||0).toFixed(2);
      }

      const lockWrap = document.createElement('label');
      lockWrap.className = 'd-flex align-items-center gap-1';
      lockWrap.style.cursor = 'pointer';
      lockWrap.innerHTML = `
        <input type="checkbox" class="form-check-input lock-switch" ${ (was && was.locked) ? 'checked' : '' } />
        <span style="user-select:none">Bloquear</span>
      `;

      input.addEventListener('input', () => {
        if (writingInputs) return;
        input.dataset.locked = 'true';
        lockWrap.querySelector('.lock-switch').checked = true;
        reequilibrarPersonalizado();
      });

      lockWrap.querySelector('.lock-switch').addEventListener('change', (e) => {
        if (e.target.checked) input.dataset.locked = 'true';
        else delete input.dataset.locked;
        reequilibrarPersonalizado();
      });

      row.appendChild(input);
      row.appendChild(lockWrap);
      wrap.appendChild(label);
      wrap.appendChild(row);
      listaPagosPersonalizados.appendChild(wrap);
    }
    writingInputs = false;

    renderPlanPersonalizado();
  }

  function reequilibrarPersonalizado() {
    if (writingInputs) return;
    const total = obtenerTotal();
    const inputs = Array.from(listaPagosPersonalizados.querySelectorAll('input[data-idx]'));
    if (!inputs.length) { limpiarPlan(); return; }

    const count = inputs.length;
    const fixedMap = {};
    inputs.forEach(inp => {
      const idx = parseInt(inp.dataset.idx,10);
      if (inp.dataset.locked === 'true') fixedMap[idx] = parseFloat(inp.value)||0;
    });

    const dist = distribuirIgual(total, count, fixedMap);
    const fixedIdx = Object.keys(fixedMap).map(i=>parseInt(i,10));
    const distCerr = ajustarMontosCerrados(total, dist, PASO_REDONDEO, fixedIdx);

    writingInputs = true;
    inputs.forEach((inp, i) => {
      if (inp.dataset.locked === 'true') return;
      inp.value = (distCerr[i]||0).toFixed(2);
    });
    writingInputs = false;

    renderPlanPersonalizado();
  }

  function renderPlanPersonalizado() {
    limpiarPlan();
    const inputs = Array.from(listaPagosPersonalizados.querySelectorAll('input[data-idx]'));
    if (!inputs.length) return;

    let suma = 0;
    inputs.forEach((inp, i) => {
      const val = parseFloat(inp.value)||0;
      suma += val;
      addPago(val, i, i===0 ? 'Pago inicial' : `${NOMBRES[i-1] || ('Pago '+(i+1))} pago`);
    });

    const total = obtenerTotal();
    const pTot = document.createElement('p'); pTot.style.fontWeight='600';
    pTot.textContent = Math.abs(suma - total) < 1
      ? `Total de pagos: ${fmt(suma)} ✅ (Coincide)`
      : `Total de pagos: ${fmt(suma)} ⚠️ (No coincide exactamente con ${fmt(total)})`;
    planPagosDiv.appendChild(pTot);
  }

  function renderSegunTipo() {
    const total = obtenerTotal();
    const t = (tipoPago?.value || '').toLowerCase();

    if (opcionesDinamicas)     opcionesDinamicas.style.display = (t==='dinamico') ? 'block':'none';
    if (opcionesCredito)       opcionesCredito.style.display   = (t==='credito')  ? 'block':'none';
    if (opcionesPersonalizado) opcionesPersonalizado.style.display = (t==='personalizado') ? 'block':'none';

    if (t === 'contado')      return renderContado(total);
    if (t === 'dinamico')     return renderDinamico(total);
    if (t === 'credito')      return renderCredito(total);
    if (t === 'estatico')     return renderEstatico(total);
    if (t === 'personalizado'){ generarPersonalizadoIgualitario(); return; }

    limpiarPlan();
  }

  tipoPago?.addEventListener('change', renderSegunTipo);
  [pagoInicial, pagoCreditoInicial, plazoCredito].forEach(inp => {
    inp?.addEventListener('input', renderSegunTipo);
  });

  mesesPersonalizado?.addEventListener('input', () => {
    if ((tipoPago?.value||'').toLowerCase() !== 'personalizado') return;
    generarPersonalizadoIgualitario();
  });
  mesesPersonalizado?.addEventListener('change', () => {
    if ((tipoPago?.value||'').toLowerCase() !== 'personalizado') return;
    generarPersonalizadoIgualitario();
  });

  const onTotalChanged = () => {
    const t = (tipoPago?.value||'').toLowerCase();
    if (t === 'personalizado') reequilibrarPersonalizado();
    else renderSegunTipo();
  };
  window.addEventListener('total:changed', onTotalChanged);

  if (nodoTotal && typeof MutationObserver !== 'undefined') {
    const obs = new MutationObserver(onTotalChanged);
    obs.observe(nodoTotal, { childList:true, characterData:true, subtree:true });
  }

  renderSegunTipo();
});
</script>

{{-- ==================== Serializar pagos_json al enviar ==================== --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('form-propuesta');
    const inputPagosJson = document.getElementById('pagosJsonInput');
    const selectPlan = document.getElementById('tipoPago');

    form.addEventListener('submit', function (e) {
        if (!selectPlan) {
            alert('Error: el select de plan no existe o tiene otro id.');
            e.preventDefault();
            return;
        }

        const planSeleccionado = selectPlan.value;
        if (!planSeleccionado) {
            alert('Selecciona un plan de pagos antes de continuar.');
            e.preventDefault();
            return;
        }

        if (!window.pagos || window.pagos.length === 0) {
            alert('No hay pagos definidos, por favor selecciona o genera un plan de pagos.');
            e.preventDefault();
            return;
        }

        const pagosFormateados = window.pagos.map(pago => ({
            ...pago,
            mes: pago.mes
        }));

        inputPagosJson.value = JSON.stringify(pagosFormateados);
    });
});
</script>

{{-- ==================== Buscador de productos + paquetes ==================== --}}
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
            .then(([productos, paquetes]) => {
                mostrarResultados(paquetes, productos);
            })
            .catch(error => {
                if (error.name !== "AbortError") {
                    console.error("Error en la búsqueda:", error);
                }
            });
        } else {
            restaurarListaOriginal();
        }
    }

    function mostrarResultados(paquetes, productos) {
        dropdownProductos.innerHTML = `
            <li>
                <button class="dropdown-item modern-dropdown-item" data-bs-toggle="modal" data-bs-target="#modal1">
                    + Crear Producto
                </button>
            </li>
        `;

        if (paquetes.length > 0) {
            paquetes.forEach(paquete => {
                let li = document.createElement("li");
                li.innerHTML = `
                    <button class="dropdown-item modern-dropdown-item"
                            data-id="${paquete.id}"
                            data-productos='${JSON.stringify(paquete.productos)}'
                            onclick="agregarPaqueteDesdeData(this)">
                        📦 ${paquete.nombre.toUpperCase()} - Paquete
                    </button>
                `;
                dropdownProductos.appendChild(li);
            });
        }

        productos.sort((a, b) => a.tipo_equipo.localeCompare(b.tipo_equipo));

        if (productos.length > 0) {
            productos.forEach(producto => {
                let li = document.createElement("li");
                li.innerHTML = `
                    <button class="dropdown-item modern-dropdown-item d-flex align-items-center"
                            onclick="agregarProductoDesdeDropdown(${producto.id}, '${producto.tipo_equipo}', '${producto.modelo}', '${producto.marca}', ${producto.precio}, '${producto.imagen}')">
                        <img src="/storage/${producto.imagen}" alt="${producto.tipo_equipo}" class="modern-product-img me-2" style="width: 40px; height: 40px; object-fit: cover; border-radius: 6px;">
                        <div class="flex-grow-1 modern-product-info">
                            <strong>${producto.tipo_equipo}</strong> - ${producto.modelo} ${producto.marca}
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
            dropdownProductos.innerHTML += '<li><button class="dropdown-item text-muted">No se encontraron resultados</button></li>';
        }
    }

    function restaurarListaOriginal() {
        Promise.all([
            fetch("{{ route('productos.search') }}?search=").then(res => res.json()),
            fetch("{{ route('paquetes.search') }}?search=").then(res => res.json())
        ])
        .then(([productos, paquetes]) => {
            mostrarResultados(paquetes, productos);
        })
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
        let id = element.getAttribute("data-id");
        let productos = JSON.parse(element.getAttribute("data-productos"));
        agregarPaquete(id, productos);
    };

    function agregarPaquete(paqueteId, productos) {
        productos.forEach(producto => {
            agregarProductoDesdeDropdown(
                producto.id,
                producto.tipo_equipo,
                producto.modelo,
                producto.marca,
                parseFloat(producto.precio),
                producto.imagen
            );
        });
    }
});
</script>

{{-- ==================== Productos seleccionados + totales ==================== --}}
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
  } else {
    const d = document.getElementById('descuento');
    const e = document.getElementById('envio');
    const iva = document.getElementById('aplica_iva');
    ['input','keyup','change','paste','mouseup','wheel'].forEach(evt => {
      if (d) d.addEventListener(evt, recalcNow, {passive:true});
      if (e) e.addEventListener(evt, recalcNow, {passive:true});
    });
    if (iva) ['change','click','keyup'].forEach(evt => iva.addEventListener(evt, recalcNow));
    document.addEventListener('input', (ev) => {
      const t = ev.target;
      if (t && (t.classList.contains('cantidad') || t.classList.contains('sobreprecio'))) {
        actualizarSubtotal(t);
      }
    }, {passive:true});
  }
}

function agregarProductoDesdeDropdown(id, nombre, modelo, marca, precio, imagen) {
  if (!id || !nombre) return;

  if (document.querySelector(`#tabla-productos tbody tr[data-id="${id}"]`)) {
    alert('Este producto ya ha sido agregado.');
    return;
  }

  const imagenRuta = (imagen && imagen.includes('/storage')) ? imagen : '/storage/' + (imagen || '');
  const fila = `
    <tr data-id="${id}" data-precio="${Number(precio) || 0}">
      <td><img src="${imagenRuta}" alt="${nombre}" style="width:50px;height:50px;object-fit:cover;border-radius:6px;"></td>
      <td class="equipo">${nombre}</td>
      <td>${modelo || ''}</td>
      <td>${marca || ''}</td>
      <td><input type="number" class="form-control cantidad" value="1" min="1" step="1"></td>
      <td class="subtotal">${(Number(precio) || 0).toFixed(2)}</td>
      <td><input type="number" class="form-control sobreprecio" value="0" min="0" step="0.01"></td>
      <td><button type="button" class="btn btn-sm btn-danger" onclick="eliminarFila(this)">Eliminar</button></td>
    </tr>
  `;
  const tbody = document.querySelector('#tabla-productos tbody');
  if (tbody) {
    tbody.insertAdjacentHTML('beforeend', fila);
  }
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

  const cantidad = Math.max(1, parseFloat(fila.querySelector('.cantidad')?.value) || 1);
  const sobreprecio = Math.max(0, parseFloat(fila.querySelector('.sobreprecio')?.value) || 0);
  const precioBase = Math.max(0, parseFloat(fila.getAttribute('data-precio')) || 0);

  const nuevoSubtotal = cantidad * (precioBase + sobreprecio);
  const celda = fila.querySelector('.subtotal');
  if (celda) celda.textContent = to2(nuevoSubtotal);

  actualizarTotal();
  prepararProductosJSON();
}

function actualizarTotal() {
  let subtotal = 0;

  document.querySelectorAll('#tabla-productos tbody tr').forEach(tr => {
    const cantidad = Math.max(1, parseFloat(tr.querySelector('.cantidad')?.value) || 1);
    const sobreprecio = Math.max(0, parseFloat(tr.querySelector('.sobreprecio')?.value) || 0);
    const precioBase = Math.max(0, parseFloat(tr.getAttribute('data-precio')) || 0);
    const sub = cantidad * (precioBase + sobreprecio);
    const celda = tr.querySelector('.subtotal');
    if (celda) celda.textContent = to2(sub);
    subtotal += sub;
  });

  const descuentoInput = document.getElementById('descuento');
  const envioInput = document.getElementById('envio');

  const descuento = Math.max(0, parseFloat(descuentoInput?.value) || 0);
  const envio     = Math.max(0, parseFloat(envioInput?.value) || 0);

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

  if (typeof window !== 'undefined') {
    window.dispatchEvent(new Event('total:changed'));
  }
}

function prepararProductosJSON() {
  const productos = [];
  document.querySelectorAll('#tabla-productos tbody tr').forEach(tr => {
    productos.push({
      producto_id: tr.getAttribute('data-id'),
      cantidad: Math.max(1, parseFloat(tr.querySelector('.cantidad')?.value) || 1),
      precio_unitario: Math.max(0, parseFloat(tr.getAttribute('data-precio')) || 0),
      sobreprecio: Math.max(0, parseFloat(tr.querySelector('.sobreprecio')?.value) || 0),
      subtotal: Math.max(0, parseFloat(tr.querySelector('.subtotal')?.textContent) || 0),
    });
  });
  const hidden = document.getElementById('productos_json');
  if (hidden) hidden.value = JSON.stringify(productos);
}
</script>

{{-- ==================== Equipos / mercancía a cuenta (tabla + JSON) ==================== --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const btnAdd = document.getElementById('btn-add-equipo-cuenta');
    const tablaEquipos = document.getElementById('tabla-equipos-cuenta').querySelector('tbody');

    btnAdd?.addEventListener('click', function () {
        agregarEquipoCuentaFila();
    });

    tablaEquipos.addEventListener('input', function (e) {
        if (e.target.classList.contains('ec-valor') ||
            e.target.classList.contains('ec-tipo')  ||
            e.target.classList.contains('ec-marca') ||
            e.target.classList.contains('ec-modelo')||
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
        <td>
            <input type="text" class="form-control ec-tipo text-uppercase" placeholder="Ej. TORRE 1588">
        </td>
        <td>
            <input type="text" class="form-control ec-marca text-uppercase" placeholder="Marca">
        </td>
        <td>
            <input type="text" class="form-control ec-modelo text-uppercase" placeholder="Modelo">
        </td>
        <td>
            <input type="text" class="form-control ec-serie text-uppercase" placeholder="N° serie">
        </td>
        <td>
            <input type="number" class="form-control ec-valor" min="0" step="0.01" value="0">
        </td>
        <td>
            <button type="button" class="btn btn-sm btn-outline-danger btn-remove-equipo">&times;</button>
        </td>
    `;
    tbody.appendChild(row);
}

function prepararEquiposCuentaJSON() {
    const filas = document.querySelectorAll('#tabla-equipos-cuenta tbody tr');
    const equipos = [];
    let totalCuenta = 0;

    filas.forEach(tr => {
        const tipo  = tr.querySelector('.ec-tipo')?.value?.trim() || null;
        const marca = tr.querySelector('.ec-marca')?.value?.trim() || null;
        const modelo= tr.querySelector('.ec-modelo')?.value?.trim() || null;
        const serie = tr.querySelector('.ec-serie')?.value?.trim() || null;
        let valor   = parseFloat(tr.querySelector('.ec-valor')?.value) || 0;
        valor = Math.max(0, valor);

        if (!tipo && !marca && !modelo && !serie && valor === 0) {
            return;
        }

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
    if (inputCuenta) {
        inputCuenta.value = to2(totalCuenta);
    }

    actualizarTotal();
}
</script>

{{-- ==================== Buscador de clientes ==================== --}}
<script>
document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById("search-client");
    const clientList = document.getElementById("client-list");
    const clienteIdInput = document.getElementById("cliente_id");
    const clientDetails = document.getElementById("client-details");
    const formVenta = document.getElementById("form-propuesta");
    const CLIENT_SEARCH_URL = "{{ route('clientes.encontrar') }}";

    if (!searchInput || !clientList || !clienteIdInput || !clientDetails || !formVenta) {
        console.error("Alguno de los elementos no se encontró. Revisa los IDs.");
        return;
    }

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
                clientList.innerHTML += `
                    <li><button type="button" class="dropdown-item disabled">No se encontraron resultados</button></li>
                `;
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
        .catch(error => {
            console.error("Error al cargar clientes:", error);
        });
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

    // ✅ Versión robusta para abrir el modal
    window.openCreateClientModal = function () {
        const el = document.getElementById("modal_formulario");
        if (!el) {
            console.error("No existe #modal_formulario en el DOM.");
            alert("Error: No se encontró el modal de crear cliente.");
            return;
        }
        if (typeof bootstrap === "undefined" || !bootstrap.Modal) {
            console.error("Bootstrap JS no está cargado.");
            alert("Error: Bootstrap no está cargado. El modal no puede abrir.");
            return;
        }
        const instance = bootstrap.Modal.getOrCreateInstance(el, { backdrop: 'static', keyboard: true });
        instance.show();
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

{{-- ==================== MODAL CREAR CLIENTE (RESPONSIVE) ==================== --}}
<style>
  .modal-modern .modal-content{
    border: 1px solid rgba(0,0,0,.08);
    border-radius: 16px;
    overflow: hidden;
  }
  .modal-modern .modal-header{
    border-bottom: 1px solid rgba(0,0,0,.08);
    padding: 14px 16px;
  }
  .modal-modern .modal-title{
    font-weight: 700;
    letter-spacing: .2px;
  }
  .modal-modern .modal-body{
    padding: 16px;
  }
  .modal-modern .modal-footer{
    border-top: 1px solid rgba(0,0,0,.08);
    padding: 12px 16px;
    gap: 10px;
  }
  .modal-modern .form-label{
    font-size: .9rem;
    font-weight: 600;
    margin-bottom: 6px;
  }
  .modal-modern .form-control{
    border-radius: 12px;
    padding: 10px 12px;
  }
  .modal-modern .btn{
    border-radius: 12px;
    padding: 10px 12px;
    font-weight: 600;
  }

  @media (max-width: 576px){
    .modal-modern .modal-header{
      position: sticky;
      top: 0;
      background: #fff;
      z-index: 2;
    }
    .modal-modern .modal-footer{
      position: sticky;
      bottom: 0;
      background: #fff;
      z-index: 2;
    }
  }
</style>

<div class="modal fade modal-modern" id="modal_formulario" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-fullscreen-sm-down">
    <div class="modal-content">
      <div class="modal-header">
        <div>
          <h5 class="modal-title mb-0">Crear nuevo cliente</h5>
          <small class="text-muted">Se agregará y podrás seleccionarlo en la propuesta.</small>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>

      <div class="modal-body">
        <div class="row g-3">
          <div class="col-12 col-md-6">
            <label class="form-label">Nombre</label>
            <input type="text" class="form-control" id="mc_nombre" placeholder="Nombre">
          </div>
          <div class="col-12 col-md-6">
            <label class="form-label">Apellido</label>
            <input type="text" class="form-control" id="mc_apellido" placeholder="Apellido">
          </div>

          <div class="col-12 col-md-6">
            <label class="form-label">Teléfono</label>
            <input type="text" class="form-control" id="mc_telefono" placeholder="Ej. 55 1234 5678">
          </div>
          <div class="col-12 col-md-6">
            <label class="form-label">Email</label>
            <input type="email" class="form-control" id="mc_email" placeholder="correo@dominio.com">
          </div>

          <div class="col-12">
            <label class="form-label">Dirección / Comentarios</label>
            <textarea class="form-control" id="mc_comentarios" rows="3" placeholder="Dirección o notas"></textarea>
          </div>

          <div class="col-12">
            <div id="mc_error" class="alert alert-danger d-none mb-0"></div>
            <div id="mc_ok" class="alert alert-success d-none mb-0"></div>
          </div>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary w-100 w-sm-auto" data-bs-dismiss="modal">
          Cancelar
        </button>
        <button type="button" class="btn btn-primary w-100 w-sm-auto" id="btn_guardar_cliente">
          Guardar cliente
        </button>
      </div>
    </div>
  </div>
</div>

{{-- ==================== Crear cliente por AJAX y seleccionarlo ==================== --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
  const btn = document.getElementById('btn_guardar_cliente');
  if (!btn) return;

  const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
  const urlStore = @json( route('clientes.store') ); // ajusta si tu ruta es otra

  const $err = document.getElementById('mc_error');
  const $ok  = document.getElementById('mc_ok');

  function showErr(msg){
    $ok.classList.add('d-none');
    $err.textContent = msg;
    $err.classList.remove('d-none');
  }
  function showOk(msg){
    $err.classList.add('d-none');
    $ok.textContent = msg;
    $ok.classList.remove('d-none');
  }

  btn.addEventListener('click', async () => {
    btn.disabled = true;
    btn.textContent = 'Guardando...';
    $err.classList.add('d-none');
    $ok.classList.add('d-none');

    const payload = {
      nombre: (document.getElementById('mc_nombre')?.value || '').trim(),
      apellido: (document.getElementById('mc_apellido')?.value || '').trim(),
      telefono: (document.getElementById('mc_telefono')?.value || '').trim(),
      email: (document.getElementById('mc_email')?.value || '').trim(),
      comentarios: (document.getElementById('mc_comentarios')?.value || '').trim(),
    };

    if (!payload.nombre) {
      showErr('El nombre es obligatorio.');
      btn.disabled = false;
      btn.textContent = 'Guardar cliente';
      return;
    }

    try {
      const res = await fetch(urlStore, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrf,
          'Accept': 'application/json'
        },
        body: JSON.stringify(payload)
      });

      const data = await res.json().catch(() => ({}));

      if (!res.ok) {
        const msg =
          data?.message ||
          (data?.errors ? Object.values(data.errors).flat().join(' ') : 'No se pudo crear el cliente.');
        showErr(msg);
      } else {
        showOk('Cliente creado correctamente.');

        if (window.selectClient && data?.cliente) {
          window.selectClient(data.cliente);
        } else if (window.selectClient && data?.id) {
          window.selectClient(data);
        }

        const el = document.getElementById("modal_formulario");
        if (typeof bootstrap !== 'undefined' && bootstrap.Modal && el) {
          const instance = bootstrap.Modal.getOrCreateInstance(el);
          setTimeout(() => instance.hide(), 500);
        }
      }
    } catch (e) {
      console.error(e);
      showErr('Error de red al crear el cliente.');
    } finally {
      btn.disabled = false;
      btn.textContent = 'Guardar cliente';
    }
  });
});
</script>
@endsection
