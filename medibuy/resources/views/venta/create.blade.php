@extends('layouts.app')
@section('title', 'Remisión')
@section('titulo', 'Remisión')
@section('content')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> 
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="stylesheet" href="{{ asset('css/ventascreate.css') }}?v={{ time() }}">
<style>
    /* =======================================================
   ESTILOS MODAL PREMIUM
   ======================================================= */

/* Contenedor principal del modal */
.premium-modal {
    border: none;
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
    overflow: hidden;
}

/* Cabecera */
.premium-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #edf2f7;
    padding: 1.25rem 1.5rem;
}

.premium-header .modal-title {
    font-weight: 600;
    color: #1a202c;
    font-size: 1.15rem;
    letter-spacing: -0.01em;
}

.cm-icon {
    color: #0d6efd; /* Color corporativo principal (Bootstrap primary) */
    display: flex;
    align-items: center;
}

/* Cuerpo */
.premium-body {
    padding: 2rem 1.5rem;
    background-color: #ffffff;
}

/* Etiquetas (Labels) */
.cm-label {
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #4a5568;
    margin-bottom: 0.5rem;
    display: block;
}

/* Inputs y Textareas modernos */
.modern-input, 
.modern-textarea {
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 0.6rem 1rem;
    font-size: 0.95rem;
    color: #2d3748;
    background-color: #fcfcfc;
    transition: all 0.2s ease-in-out;
}

.modern-input:focus, 
.modern-textarea:focus {
    border-color: #0d6efd; /* Tu color corporativo */
    background-color: #ffffff;
    box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.15);
    outline: none;
}

.modern-input::placeholder, 
.modern-textarea::placeholder {
    color: #a0aec0;
}

/* Texto de ayuda */
.cm-help {
    display: flex;
    align-items: center;
    font-size: 0.75rem;
    color: #718096;
}

.cm-help .dot {
    width: 6px;
    height: 6px;
    background-color: #cbd5e0;
    border-radius: 50%;
    margin-right: 6px;
    display: inline-block;
}

/* Pie del modal */
.premium-footer {
    border-top: 1px solid #edf2f7;
    padding: 1.25rem 1.5rem;
    background-color: #fafbfc;
    gap: 10px;
}

/* Botones */
.btn-cancel {
    background-color: #ffffff;
    border: 1px solid #e2e8f0;
    color: #4a5568;
    font-weight: 500;
    border-radius: 6px;
    padding: 0.5rem 1.25rem;
    transition: all 0.2s ease;
}

.btn-cancel:hover {
    background-color: #edf2f7;
    color: #1a202c;
}

.btn-save {
    background-color: #1a202c; /* Negro corporativo / Cambiar si deseas otro tono */
    border: none;
    color: #ffffff;
    font-weight: 500;
    border-radius: 6px;
    padding: 0.5rem 1.5rem;
    transition: all 0.2s ease;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.btn-save:hover {
    background-color: #2d3748;
    color: #ffffff;
    box-shadow: 0 4px 6px rgba(0,0,0,0.15);
    transform: translateY(-1px);
}
</style>
<div class="container">
    <form id="form-venta" method="POST" action="{{ route('ventas.store') }}">
        @csrf

        <div class="row">
            {{-- =======================
                 COLUMNA IZQUIERDA
                 ======================= --}}
            <div class="col-md-3 mt-3">

                <!-- Tarjeta de Cliente --> 
                <div class="card modern-card mb-3">
                    <div class="card-header modern-header">Cliente</div>
                    <div class="card-body">
                        <div class="dropdown">
                            <input
                                type="text"
                                id="search-client"
                                class="form-control modern-input dropdown-toggle"
                                data-bs-toggle="dropdown"
                                placeholder="Buscar cliente..."
                                autocomplete="off"
                            >
                            <ul class="dropdown-menu modern-dropdown w-100" id="client-list">
                                <li>
                                    <button type="button" class="dropdown-item modern-dropdown-item" onclick='selectClient({
                                        id: 1,
                                        nombre: "PÚBLICO EN GENERAL",
                                        apellido: "",
                                        telefono: "",
                                        email: "",
                                        comentarios: ""
                                    })'>
                                        PÚBLICO EN GENERAL
                                    </button>
                                </li>
                                <li>
                                    <button type="button" class="dropdown-item modern-dropdown-item" onclick="openCreateClientModal()">
                                        + Crear nuevo cliente
                                    </button>
                                </li>
                                <!-- Aquí se insertarán dinámicamente los clientes -->
                            </ul>
                        </div>

                        <!-- Campo oculto para enviar ID del cliente seleccionado -->
                        <input type="hidden" name="cliente_id" id="cliente_id" value="{{ old('cliente_id') }}">
                    </div>

                    <!-- Detalles del cliente -->
                    <div id="client-details" class="mt-3"></div>
                </div>

                <!-- Tarjeta de Lugar de la Cotización -->
                <div class="card modern-card mb-3">
                    <div class="card-header modern-header">Lugar de la Cotización</div>
                    <div class="card-body">
                        <select name="lugar" id="lugarCotizacion" class="form-control modern-select" required>
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

                <!-- Tarjeta de Nota al Cliente -->
                <div class="card modern-card mb-3">
                    <div class="card-header modern-header">Nota al Cliente</div>
                    <div class="card-body">
                        <textarea name="nota" id="notaCliente" class="form-control modern-textarea" rows="4" placeholder="Escribe una nota...">{{ old('nota') }}</textarea>
                    </div>
                </div>

                <!-- Tarjeta de Registrado por -->
                <div class="card modern-card">
                    <div class="card-header modern-header">Registrado por</div>
                    <div class="card-body">
                        @auth
                            <input type="text" name="registrado_por" class="form-control modern-textarea" value="{{ auth()->user()->name }}" readonly>
                        @else
                            <input type="text" name="registrado_por" class="form-control modern-textarea" value="Desconocido" readonly>
                        @endauth
                    </div>
                </div>
            </div>

            {{-- =======================
                 COLUMNA DERECHA
                 ======================= --}}
            <div class="col-md-9">

                <!-- Productos -->
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
                                            <img src="/storage/{{ $producto->imagen }}" alt="{{ $producto->tipo_equipo }}"
                                                 class="modern-product-img me-2"
                                                 style="width: 40px; height: 40px; object-fit: cover; border-radius: 6px;">

                                            <div class="flex-grow-1 modern-product-info">
                                                <strong>{{ strtoupper($producto->tipo_equipo) }}</strong>
                                                - {{ strtoupper($producto->modelo) }} {{ strtoupper($producto->marca) }}
                                                <br>
                                                <span class="text-muted modern-product-price">
                                                    ${{ number_format($producto->precio, 2) }}
                                                </span>
                                            </div>

                                            <span class="badge bg-secondary modern-badge">
                                                {{ $producto->stock }} unidades
                                            </span>
                                        </button>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Productos seleccionados -->
                <div class="card modern-card mt-3">
                    <div class="card-header modern-header">Productos Seleccionados</div>
                    <div class="card-body">
                        <div class="table-responsive">
                            {{-- ✅ JSON que espera el controlador: productos_json --}}
                            <input type="hidden" name="productos_json" id="productos_json" value="{{ old('productos_json') }}">
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
    <th>Número de Serie</th>
    <th>Regalo</th>
    <th>Acción</th>
  </tr>
</thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- =========================================
                     ✅ EQUIPOS A CUENTA (TRADE-IN)
                     ========================================= --}}
                <div class="card modern-card mt-3">
                    <div class="card-header modern-header d-flex align-items-center justify-content-between">
                        <span>Equipos a cuenta (Trade-in)</span>
                        <button type="button" class="btn btn-sm tradein-pill-btn" id="btn-add-tradein">
    + Agregar equipo
</button>

                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-2" style="font-size:13px;">
                            Agrega equipos que te dejan como parte de pago. Su valor se descuenta del total.
                        </p>

                        <!-- ✅ Input oculto que se enviará al backend -->
                        <input type="hidden" name="tradeins_json" id="tradeins_json" value='{{ old('tradeins_json', '[]') }}'>

                        <div class="table-responsive">
                            <table id="tabla-tradeins" class="table modern-table">
                                <thead>
                                    <tr>
                                        <th style="width:16%;">Tipo de equipo</th>
                                        <th style="width:12%;">Marca</th>
                                        <th style="width:12%;">Modelo</th>
                                        <th style="width:12%;">Núm. Serie</th>
                                        <th style="width:12%;">Valor a cuenta</th>
                                        <th style="width:4%;">Acción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- filas dinámicas por JS --}}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="d-flex flex-column flex-md-row">

                    <!-- Resumen -->
                    <div class="card modern-card mt-3 w-100 w-md-50">
                        <div class="card-header modern-header">Resumen</div>
                        <div class="card-body">

                            <p>Subtotal: $<span id="subtotal" class="modern-value">0.00</span></p>
                            <input type="hidden" name="subtotal" id="subtotal_input" value="{{ old('subtotal', 0) }}">

                            <div class="form-group">
                                <label>Descuento</label>
                                <input type="number" name="descuento" id="descuento" value="{{ old('descuento', 0) }}"
                                       class="form-control modern-input w-25 d-inline-block" step="0.01">
                            </div>

                            <br>

                            <div class="form-group">
                                <label>Envío</label>
                                <input type="number" name="envio" id="envio" value="{{ old('envio', 0) }}"
                                       class="form-control modern-input w-25 d-inline-block" step="0.01">
                            </div>

                            <div class="form-check mt-2">
                                <input type="checkbox" class="form-check-input" id="aplica_iva">
                                <label class="form-check-label">Aplicar IVA (16%)</label>
                            </div>

                            <input type="hidden" name="iva" id="iva_input" value="{{ old('iva', 0) }}">

                            <p>IVA: $<span id="iva">0.00</span></p>
                            
                            {{-- ✅ ANTICIPO REAL (se guarda como Pago aprobado y es_anticipo = true) --}}
  
                            <div class="form-group mt-3">
                                <label style="font-weight:700;">Anticipo</label>
                                <div class="d-flex gap-2 flex-wrap mt-1">
                                    <input type="number" step="0.01" min="0"
                                           id="anticipo_monto" name="anticipo_monto"
                                           class="form-control modern-input" style="max-width:160px;"
                                           placeholder="Monto"
                                           value="{{ old('anticipo_monto') }}">
                                    <input type="date"
                                           id="anticipo_fecha" name="anticipo_fecha"
                                           class="form-control modern-input" style="max-width:180px;"
                                           value="{{ old('anticipo_fecha') }}">
                                    <select id="anticipo_metodo" name="anticipo_metodo"
                                            class="form-control modern-input" style="max-width:220px;">
                                        <option value="">Método de pago</option>
                                        <option value="efectivo"          {{ old('anticipo_metodo') === 'efectivo' ? 'selected' : '' }}>Efectivo</option>
                                        <option value="transferencia"     {{ old('anticipo_metodo') === 'transferencia' ? 'selected' : '' }}>Transferencia</option>
                                        <option value="tarjeta_credito"   {{ old('anticipo_metodo') === 'tarjeta_credito' ? 'selected' : '' }}>Tarjeta crédito</option>
                                        <option value="tarjeta_debito"    {{ old('anticipo_metodo') === 'tarjeta_debito' ? 'selected' : '' }}>Tarjeta débito</option>
                                        <option value="cheque"            {{ old('anticipo_metodo') === 'cheque' ? 'selected' : '' }}>Cheque</option>
                                        <option value="otro"              {{ old('anticipo_metodo') === 'otro' ? 'selected' : '' }}>Otro</option>
                                    </select>
                                </div>
                                <small class="text-muted">
                                    Si capturas anticipo se registra como pago real aprobado y aplicado al saldo de esta venta.
                                </small>
                            </div>
                            <hr>
                            {{-- Total ORIGINAL (productos/iva/envío/descuento) --}}
                            <p><strong>Total: $<span id="total">0.00</span></strong></p>

                            {{-- ✅ hidden total ORIGINAL que usa el controlador --}}
                            <input type="hidden" name="total" id="total_input" value="{{ old('total', 0) }}">
                            <input type="hidden" name="total_original" id="total_original_input" value="{{ old('total_original', 0) }}">

                            {{-- ✅ TOTAL TRADE-IN Y NETO (solo informativo pero se mandan también) --}}
                            <p>
                                Valor a cuenta: $<span id="tradein_total_ui" class="modern-value">0.00</span>
                            </p>
                            <p style="font-weight:800;">
                                Total neto: $<span id="total_neto_ui" class="modern-value">0.00</span>
                            </p>
                            <input type="hidden" name="tradein_total" id="tradein_total_input" value="{{ old('tradein_total', 0) }}">
                            <input type="hidden" name="total_neto" id="total_neto_input" value="{{ old('total_neto', 0) }}">

                            <hr>

                            <div class="form-group">
                                <label for="tipoPago">Selecciona Plan:</label>
                                <select id="tipoPago" name="plan" class="form-control modern-input w-50" required>
                                    <option value="" disabled {{ old('plan') ? '' : 'selected' }}>Selecciona un plan</option>
                                    <option value="contado"      {{ old('plan') === 'contado' ? 'selected' : '' }}>Pago de Contado</option>
                                    <option value="personalizado"{{ old('plan') === 'personalizado' ? 'selected' : '' }}>Plan Personalizado</option>
                                    <option value="estatico"     {{ old('plan') === 'estatico' ? 'selected' : '' }}>Plan Fijo</option>
                                    <option value="dinamico"     {{ old('plan') === 'dinamico' ? 'selected' : '' }}>Plan Flexible</option>
                                    <option value="credito"      {{ old('plan') === 'credito' ? 'selected' : '' }}>Plan a Crédito</option>
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
                                <div id="listaPagosPersonalizados" class="mt-3"></div>
                            </div>

                            {{-- ✅ JSON con el calendario de pagos que espera el controlador: pagos_json --}}
                            <input type="hidden" id="pagosJsonInput" name="pagos_json" value="{{ old('pagos_json') }}">

                            {{-- Meses de garantía --}}
                            <div class="form-group mt-4">
                                <label for="meses_garantia">Meses de Garantía:</label>
                                <select name="meses_garantia" id="meses_garantia" class="form-control modern-input w-50" required>
                                    <option value="" disabled {{ old('meses_garantia') ? '' : 'selected' }}>Selecciona meses de garantía</option>
                                    <option value="6"  {{ old('meses_garantia') == 6  ? 'selected' : '' }}>6 meses</option>
                                    <option value="9"  {{ old('meses_garantia') == 9  ? 'selected' : '' }}>9 meses</option>
                                    <option value="12" {{ old('meses_garantia') == 12 ? 'selected' : '' }}>12 meses</option>
                                    <option value="15" {{ old('meses_garantia') == 15 ? 'selected' : '' }}>15 meses</option>
                                    <option value="18" {{ old('meses_garantia') == 18 ? 'selected' : '' }}>18 meses</option>
                                </select>
                            </div>

                            {{-- Carta de garantía buscador --}}
                            <br>
                            <div class="form-group position-relative mt-4" style="max-width: 900px; width: 100%;">
                                <label for="carta_garantia_search">Carta de Garantía a incluir en el PDF:</label>

                                <input type="text" id="carta_garantia_search" class="form-control" placeholder="Escribe para buscar..." autocomplete="off">
                                <input type="hidden" name="carta_garantia_id" id="carta_garantia_id" value="{{ old('carta_garantia_id') }}">

                                <ul id="dropdown_cartas"
                                    class="list-group position-absolute w-100 mt-1 shadow-sm"
                                    style="z-index:1000;display:none;max-height:250px;overflow-y:auto;">
                                    @foreach ($cartas->sortBy('nombre') as $carta)
                                        <li class="list-group-item list-option-carta" data-id="{{ $carta->id }}">
                                            {{ strtoupper($carta->nombre) }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>

                            <br>
                            <button type="button" onclick="debugForm()" class="btn btn-success">Guardar</button>

<script>
function debugForm() {
    const form = document.getElementById('form-venta');
    if (!form.checkValidity()) {
        console.log("El formulario es inválido");
        form.reportValidity();
    } else {
        console.log("El formulario es válido, enviando...");
        form.requestSubmit(); // ✅ Dispara 'submit' y respeta tus listeners
    }
}
</script>
                        </div>
                    </div>

                    <!-- Detalles del financiamiento -->
                    <div class="card modern-card mt-3 w-100 w-md-50 ms-md-3">
                        <div class="card-header modern-header">Detalles del Financiamiento</div>
                        <div class="card-body" id="plan-pagos"></div>
                    </div>

                </div>
            </div>
        </div>
    </form>
</div>

{{-- ============================================================
     MODAL: CREAR CLIENTE
     ============================================================ --}}
{{-- ============================================================
     MODAL: CREAR CLIENTE (PREMIUM)
     ============================================================ --}}
<form id="form-cliente" method="POST" action="{{ route('clientes.store') }}">
  @csrf

  <div class="modal fade" id="modal_formulario" tabindex="-1" aria-labelledby="createClientModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content premium-modal">

        <div class="modal-header premium-header">
          <h5 class="modal-title d-flex align-items-center" id="createClientModalLabel">
            <span class="cm-icon me-2" aria-hidden="true">
              <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" fill="none" stroke-width="2">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                <circle cx="12" cy="7" r="4"/>
              </svg>
            </span>
            Registrar Nuevo Cliente
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>

        <div class="modal-body premium-body">
          <div class="row g-4">
            
            <!-- Nombre -->
            <div class="col-md-6">
              <div class="cm-field">
                <label for="nombre" class="cm-label">Nombre</label>
                <input type="text" class="form-control modern-input text-uppercase" id="nombre" name="nombre" placeholder="Ej. Juan" required>
                @error('nombre') <small class="text-danger mt-1 d-block"><i class="bi bi-exclamation-circle"></i> {{ $message }}</small> @enderror
              </div>
            </div>

            <!-- Apellido -->
            <div class="col-md-6">
              <div class="cm-field">
                <label for="apellido" class="cm-label">Apellido</label>
                <input type="text" class="form-control modern-input text-uppercase" id="apellido" name="apellido" placeholder="Ej. Pérez" required>
                @error('apellido') <small class="text-danger mt-1 d-block"><i class="bi bi-exclamation-circle"></i> {{ $message }}</small> @enderror
              </div>
            </div>

            <!-- Teléfono -->
            <div class="col-md-6">
              <div class="cm-field">
                <label for="telefono" class="cm-label">Teléfono</label>
                <input type="tel" class="form-control modern-input" id="telefono" name="telefono" placeholder="10 dígitos" required inputmode="numeric" pattern="^\d{10}$" maxlength="10" minlength="10">
                <div class="cm-help mt-1">
                  <span class="dot"></span>
                  <span>Debe contener exactamente 10 números.</span>
                </div>
                <span id="error-telefono" class="text-danger mt-1 d-block" style="display:none;"><i class="bi bi-exclamation-circle"></i> El teléfono ya está registrado.</span>
                @error('telefono') <small class="text-danger mt-1 d-block"><i class="bi bi-exclamation-circle"></i> {{ $message }}</small> @enderror
              </div>
            </div>

            <!-- Email -->
            <div class="col-md-6">
              <div class="cm-field">
                <label for="email" class="cm-label">Email</label>
                <input type="email" class="form-control modern-input" id="email" name="email" placeholder="correo@ejemplo.com">
                <span id="error-email" class="text-danger mt-1 d-block" style="display:none;"><i class="bi bi-exclamation-circle"></i> El correo ya está registrado.</span>
                @error('email') <small class="text-danger mt-1 d-block"><i class="bi bi-exclamation-circle"></i> {{ $message }}</small> @enderror
              </div>
            </div>

            <!-- Promociones -->
            <div class="col-md-6">
              <div class="cm-field">
                <label for="recibe_promocion" class="cm-label">¿Recibe promociones?</label>
                <select name="recibe_promocion" id="recibe_promocion" class="form-select modern-input" required>
                  <option value="" disabled selected>Selecciona una opción</option>
                  <option value="1">Sí, enviar promociones</option>
                  <option value="0">No, no enviar</option>
                </select>
                <div class="cm-help mt-1">
                  <span class="dot"></span>
                  <span>Autorización para campañas de marketing.</span>
                </div>
            <!-- Categoría -->
            <div class="col-md-6">
                <div class="cm-field">
                    <label for="categoria_id_modal" class="cm-label">Categoría</label>
                    <select name="categoria_id" id="categoria_id_modal" class="form-select modern-input" required>
                        <option value="" disabled selected>Selecciona una categoría</option>
                        @foreach($categorias as $categoria)
                        <option value="{{ $categoria->id }}">
                            {{ mb_strtoupper($categoria->nombre, 'UTF-8') }}
                            </option>
                            @endforeach
                    </select>
                </div>
                </div>
              </div>
            </div>

            <!-- Dirección / Comentarios -->
            <div class="col-12">
              <div class="cm-field">
                <label for="comentarios" class="cm-label">Dirección / Comentarios</label>
                <textarea id="comentarios" name="comentarios" class="form-control modern-textarea text-uppercase" rows="3" placeholder="Agrega información útil (ej. referencias, condiciones de entrega...)"></textarea>
                @error('comentarios') <small class="text-danger mt-1 d-block"><i class="bi bi-exclamation-circle"></i> {{ $message }}</small> @enderror
              </div>
            </div>

          </div>
        </div>

        <div class="modal-footer premium-footer d-flex justify-content-end">
          <button type="button" class="btn btn-cancel" data-bs-dismiss="modal">Cancelar</button>
          <button id="btn-submit" type="submit" class="btn btn-save">
             Guardar Cliente
          </button>
        </div>

      </div>
    </div>
  </div>
</form>

{{-- Modal Cliente guardado --}}
<div class="modal fade" id="cliente_creado" tabindex="-1" role="dialog" aria-labelledby="ClienteCreadoLabel"
     aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header encabezado_modal text-center">
                <h5 class="modal-title titulo_modal">¡Cliente guardado exitosamente!</h5>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <img src="{{ asset('images/confirmar.jpeg') }}" alt="Logo de encabezado" class="logo-modal">
                </div>
                <p class="text-center mensaje-modal">
                    El cliente se ha registrado correctamente en el sistema.
                    Puedes proceder a cerrar este mensaje.
                    <b>Grupo MediBuy</b>.
                </p>
            </div>
            <div class="modal-footer d-flex justify-content-center">
                <button class="btn btn-listo" onclick="cerrarModal()">Listo</button>
            </div>
        </div>
    </div>
</div>

{{-- ============================================================
     MODAL: CREAR PRODUCTO
     ============================================================ --}}
<form id="formProducto" method="POST" action="{{ route('productos.store') }}" enctype="multipart/form-data">
    @csrf
    <div class="modal fade" id="modal1" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Registrar Equipo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text" name="tipo_equipo" class="form-control" placeholder="Ej: Monitor" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Modelo</label>
                            <input type="text" name="modelo" class="form-control" placeholder="Ej: Vision Pro" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Marca</label>
                            <input type="text" name="marca" class="form-control" placeholder="Ej: Stryker" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Existencias</label>
                            <input type="number" name="stock" class="form-control" value="1" required min="1">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Precio</label>
                            <input type="number" name="precio" class="form-control" value="0.00" required min="0" step="0.01">
                        </div>
                    </div>

                    <div class="mb-3 text-center">
                        <label class="form-label d-block">Imagen</label>
                        <div class="image-container">
                            <label for="image-upload" class="image-preview">
                                <img id="preview-icon" src="https://cdn-icons-png.flaticon.com/512/1829/1829586.png" alt="Añadir imagen">
                                <span id="preview-text">Añadir imagen</span>
                            </label>
                            <input type="file" id="image-upload" name="imagen" accept="image/*" hidden>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Agregar</button>
                </div>
            </div>
        </div>
    </div>
</form>
<script>
// =========================
// Crear producto vía modal
// =========================
document.addEventListener('DOMContentLoaded', function () {
    const formProducto = document.getElementById("formProducto");
    if (!formProducto) return;

    formProducto.addEventListener("submit", function (event) {
        event.preventDefault(); // Evita recarga

        const form = this;
        var formData = new FormData(form);

        fetch("{{ route('productos.store') }}", {
            method: "POST",
            body: formData,
            headers: {
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                "Accept": "application/json" // para que Laravel retorne JSON
            }
        })
        .then(function (response) {
            return response.json()
                .catch(function () { return {}; })
                .then(function (data) {
                    if (!response.ok) {
                        throw data;
                    }
                    return data;
                });
        })
        .then(function (data) {
            if (data.message) {
                Swal.fire({
                    toast: true,
                    icon: 'success',
                    title: data.message,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    didOpen: function (toast) {
                        toast.addEventListener('mouseenter', Swal.stopTimer);
                        toast.addEventListener('mouseleave', Swal.resumeTimer);
                    }
                });

                // Limpiar formulario
                form.reset();

                // Resetear preview de imagen
                const previewIcon = document.getElementById('preview-icon');
                const previewText = document.getElementById('preview-text');
                if (previewIcon && previewText) {
                    previewIcon.src = "https://cdn-icons-png.flaticon.com/512/1829/1829586.png";
                    previewIcon.style.width = '';
                    previewIcon.style.height = '';
                    previewText.style.display = 'inline';
                }

                // Cerrar modal
                const modalEl = document.getElementById('modal1');
                if (modalEl && window.bootstrap && bootstrap.Modal) {
                    const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                    modal.hide();
                }
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.message || 'No se pudo crear el producto.',
                });
            }
        })
        .catch(function (error) {
            console.error("Error:", error);
            Swal.fire({
                icon: 'error',
                title: 'Error inesperado',
                text: 'Ocurrió un error al enviar el formulario.',
            });
        });
    });
});
</script>

<script>
// =========================
// Preview imagen producto
// =========================
document.addEventListener('DOMContentLoaded', function () {
    const inputImg = document.getElementById('image-upload');
    if (!inputImg) return;

    inputImg.addEventListener('change', function (event) {
        const file = event.target.files[0];
        if (file && file.type && file.type.indexOf('image/') === 0) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const previewIcon = document.getElementById('preview-icon');
                const previewText = document.getElementById('preview-text');

                if (!previewIcon || !previewText) return;

                previewIcon.src = e.target.result;
                previewIcon.style.width = '100%';
                previewIcon.style.height = '100%';
                previewText.style.display = 'none';
            };
            reader.readAsDataURL(file);
        }
    });
});
</script>

<script>
// =========================
// Buscador carta garantía
// =========================
document.addEventListener("DOMContentLoaded", function () {
    const input       = document.getElementById('carta_garantia_search');
    const hiddenInput = document.getElementById('carta_garantia_id');
    const dropdown    = document.getElementById('dropdown_cartas');

    if (!input || !hiddenInput || !dropdown) return;

    const originalOptions = Array.prototype.slice.call(
        dropdown.querySelectorAll('.list-option-carta')
    );

    function normalize(str) {
        return (str || '')
            .normalize("NFD")
            .replace(/[\u0300-\u036f]/g, "")
            .toLowerCase();
    }
    function escapeReg(str) {
        return (str || '').replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    }

    input.addEventListener('input', function () {
        const term = input.value.trim();
        const normalizedValue = normalize(term);
        dropdown.innerHTML = '';
        let matches = 0;

        if (!normalizedValue) {
            dropdown.style.display = 'none';
            hiddenInput.value = '';
            return;
        }

        originalOptions.forEach(function (option) {
            const textOriginal   = option.textContent || '';
            const textNormalized = normalize(textOriginal);

            if (textNormalized.indexOf(normalizedValue) !== -1) {
                matches++;

                // Resaltado seguro
                const regex = new RegExp(escapeReg(term), 'i');
                const highlighted = textOriginal.replace(regex, function (match) {
                    return '<strong style="color:#4a148c">' + match + '</strong>';
                });

                const li = document.createElement('li');
                li.classList.add('list-group-item', 'list-option-carta');
                li.setAttribute('data-id', option.getAttribute('data-id'));
                li.innerHTML = highlighted;
                li.addEventListener('click', function () {
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
<script>
// =====================================================
// PLAN DE PAGOS (AUTO-DISTRIBUCIÓN + % = 100 + BLOQUEO + PRIORIDAD)
// - Tipos: contado, estático, dinámico, crédito, personalizado
// - Bloquear = no se mueve al redistribuir
// - NUEVO: "Prioridad por orden" -> si el usuario edita un pago (ej. el 1ro),
//         se respeta sí o sí y SOLO se ajustan los que van abajo.
// - Modo Monto: editas $; % se calcula
// - Modo %: editas %; $ se calcula; el resto se ajusta a 100%
// - Anticipo (anticipo_monto) + trade-in (total_neto_ui) considerados
// - Al agregar pago personalizado: redistribuye automáticamente
// - Genera window.pagos y llena #pagosJsonInput / #pagos_json
// =====================================================
document.addEventListener('DOMContentLoaded', function () {
  const tipoPago              = document.getElementById('tipoPago');
  const opcionesDinamicas     = document.getElementById('opcionesDinamicas');
  const opcionesCredito       = document.getElementById('opcionesCredito');
  const opcionesPersonalizado = document.getElementById('opcionesPersonalizado');
  const listaPagosPersonalizados = document.getElementById('listaPagosPersonalizados');
  const planPagosDiv          = document.getElementById('plan-pagos');

  const pagoInicial           = document.getElementById('pagoInicial');
  const pagoCreditoInicial    = document.getElementById('pagoCreditoInicial');
  const plazoCredito          = document.getElementById('plazoCredito');
  const mesesPersonalizado    = document.getElementById('mesesPersonalizado');
  const anticipoMontoInput    = document.getElementById('anticipo_monto');

  if (!tipoPago || !planPagosDiv) return;

  // Estado global
  window.pagos = [];        // [{cuota, descripcion, mes}]
  window.__pagosMeta = [];  // [{locked, mode:'monto'|'pct', pct, pctDraft}]
  window.__pagoUI = { rows: [], resumenEl: null, infoCreditoEl: null };

  // ✅ NUEVO: índice "ancla" (último pago que el usuario tocó)
  window.__pagosAnchorIdx = -1;

  const nombresPagos = ["Primer","Segundo","Tercer","Cuarto","Quinto","Sexto","Séptimo","Octavo","Noveno","Décimo","Undécimo","Duodécimo"];

  // =======================
  // Helpers
  // =======================
  function num(v){
    const n = parseFloat(String(v == null ? '' : v).replace(/[$,\s]/g,''));
    return isNaN(n) ? 0 : n;
  }
  function clamp(n, a, b){ return Math.min(b, Math.max(a, n)); }
  function money(n){ return (num(n)||0).toLocaleString('es-MX',{style:'currency',currency:'MXN'}); }

  function debounce(fn, delay) {
    let t;
    return function(){
      const args = arguments;
      clearTimeout(t);
      t = setTimeout(() => fn.apply(null,args), delay);
    };
  }

  function isoToDate(iso) {
    if (!iso) return new Date();
    const parts = String(iso).split('-');
    if (parts.length !== 3) return new Date();
    const y = parseInt(parts[0],10);
    const m = parseInt(parts[1],10)-1;
    const d = parseInt(parts[2],10);
    const dt = new Date(y,m,d);
    return isNaN(dt.getTime()) ? new Date() : dt;
  }
  function formatoFechaISO(date) {
    const y = date.getFullYear();
    const m = String(date.getMonth()+1).padStart(2,'0');
    const d = String(date.getDate()).padStart(2,'0');
    return y + '-' + m + '-' + d;
  }
  function addMonthsToISO(iso, months) {
    const d = isoToDate(iso);
    const originalDay = d.getDate();
    d.setMonth(d.getMonth() + months);
    if (d.getDate() < originalDay) d.setDate(0);
    return formatoFechaISO(d);
  }

  function obtenerTotalBase() {
    // Primero total_neto_ui (por trade-in), luego #total
    const netNode = document.getElementById('total_neto_ui');
    if (netNode && netNode.textContent && netNode.textContent.trim() !== '') {
      const netVal = num(netNode.textContent);
      if (netVal > 0) return netVal;
    }
    const totalEl = document.getElementById('total');
    return num(totalEl ? totalEl.textContent : 0);
  }

  function obtenerTotalFinanciar() {
    let base = obtenerTotalBase();
    let anticipo = anticipoMontoInput ? num(anticipoMontoInput.value) : 0;
    let totalFin = base - anticipo;
    if (totalFin < 0) totalFin = 0;
    return totalFin;
  }

  function mostrarOpcionesTipo() {
    const t = tipoPago.value || '';
    if (opcionesDinamicas)     opcionesDinamicas.style.display     = (t === 'dinamico')      ? 'block' : 'none';
    if (opcionesCredito)       opcionesCredito.style.display       = (t === 'credito')       ? 'block' : 'none';
    if (opcionesPersonalizado) opcionesPersonalizado.style.display = (t === 'personalizado') ? 'block' : 'none';
  }

  function ensureMetaLen() {
    while (window.__pagosMeta.length < window.pagos.length) {
      window.__pagosMeta.push({ locked:false, mode:'monto', pct:0, pctDraft:'' });
    }
    if (window.__pagosMeta.length > window.pagos.length) {
      window.__pagosMeta = window.__pagosMeta.slice(0, window.pagos.length);
    }
  }

  // =====================================================
  // CORE: Distribución "inteligente"
  // - Respeta locked
  // - Respeta "ancla": solo ajusta pagos > anchorIdx
  // =====================================================
  function distribuirParaQuePctSea100(totalFin, anchorIdx) {
    totalFin = Math.max(0, num(totalFin));
    ensureMetaLen();
    if (totalFin <= 0 || window.pagos.length === 0) return;

    // ✅ Si no se pasa anchorIdx, usa el global
    if (typeof anchorIdx !== 'number') anchorIdx = window.__pagosAnchorIdx;
    anchorIdx = (anchorIdx == null) ? -1 : parseInt(anchorIdx, 10);
    if (isNaN(anchorIdx)) anchorIdx = -1;
    anchorIdx = clamp(anchorIdx, -1, window.pagos.length - 1);

    // ✅ Definimos "fijos":
    // - locked siempre fijo
    // - si anchorIdx >= 0, todos los pagos con índice <= anchorIdx son fijos (prioridad por orden)
    const fixed = (i) => {
      if (window.__pagosMeta[i] && window.__pagosMeta[i].locked) return true;
      if (anchorIdx >= 0 && i <= anchorIdx) return true;
      return false;
    };

    // 0) Primero: si algún fijo está en modo %, conviértelo a monto (pero sin moverlo)
    for (let i = 0; i < window.pagos.length; i++) {
      if (!fixed(i)) continue;
      const meta = window.__pagosMeta[i];
      if (meta.mode === 'pct') {
        const raw = String(meta.pctDraft || '').trim().replace(',', '.');
        let p = meta.pct;
        if (!(raw === '' || raw === '.' || raw === '-' || raw === '-.')) p = num(raw);
        p = clamp(p, 0, 100);
        meta.pct = p;
        const m = (totalFin * p) / 100;
        window.pagos[i].cuota = Math.max(0, m).toFixed(2);
      } else {
        // monto fijo: normaliza string
        window.pagos[i].cuota = Math.max(0, num(window.pagos[i].cuota)).toFixed(2);
      }
    }

    // 1) Suma fijos
    let fixedSum = 0;
    let fixedIdx = [];
    for (let i = 0; i < window.pagos.length; i++) {
      if (fixed(i)) {
        fixedIdx.push(i);
        fixedSum += num(window.pagos[i].cuota);
      }
    }
    fixedSum = Math.max(0, fixedSum);

    // ✅ Si los fijos exceden el total: NO tocar el primero, recortar de arriba hacia abajo
    // (preserva el pago inicial y los primeros que el usuario puso)
    if (fixedSum > totalFin) {
      let remaining = totalFin;

      // recorta desde el final hacia el inicio (pero sin pasar por debajo de 0)
      for (let k = fixedIdx.length - 1; k >= 0; k--) {
        const i = fixedIdx[k];
        const cur = Math.max(0, num(window.pagos[i].cuota));
        const newVal = Math.max(0, Math.min(cur, remaining));
        window.pagos[i].cuota = newVal.toFixed(2);
        remaining -= newVal;
        if (remaining <= 0) remaining = 0;
      }
      // todo lo NO fijo queda en 0
      for (let i = 0; i < window.pagos.length; i++) {
        if (!fixed(i)) window.pagos[i].cuota = '0.00';
      }
      // actualizar pct real
      for (let i = 0; i < window.pagos.length; i++) {
        const m = num(window.pagos[i].cuota);
        const pReal = (totalFin > 0) ? (m / totalFin) * 100 : 0;
        window.__pagosMeta[i].pct = Math.max(0, pReal);
      }
      return;
    }

    const remainingAmount = Math.max(0, totalFin - fixedSum);
    const remainingPctMax = (totalFin > 0) ? (remainingAmount / totalFin) * 100 : 0;

    // 2) % mandatorios (modo % y NO fijos)
    let pctMandIdx = [];
    let pctMandSum = 0;

    for (let i = 0; i < window.pagos.length; i++) {
      if (fixed(i)) continue;
      const meta = window.__pagosMeta[i];
      if (meta.mode === 'pct') {
        const raw = String(meta.pctDraft || '').trim().replace(',', '.');
        let p = meta.pct;
        if (!(raw === '' || raw === '.' || raw === '-' || raw === '-.')) p = num(raw);
        p = clamp(p, 0, 100);
        meta.pct = p;
        pctMandIdx.push(i);
        pctMandSum += p;
      }
    }

    // Si pasan el % disponible, escalar proporcional (solo los de abajo)
    if (pctMandSum > remainingPctMax && pctMandSum > 0) {
      const f = remainingPctMax / pctMandSum;
      pctMandSum = 0;
      pctMandIdx.forEach(i => {
        window.__pagosMeta[i].pct = clamp(window.__pagosMeta[i].pct * f, 0, 100);
        pctMandSum += window.__pagosMeta[i].pct;
      });
    }

    // 3) Convertir % mandatorios a montos (sobre totalFin)
    pctMandIdx.forEach(i => {
      const p = clamp(window.__pagosMeta[i].pct, 0, 100);
      const m = (totalFin * p) / 100;
      window.pagos[i].cuota = Math.max(0, m).toFixed(2);
    });

    // 4) Repartir el resto entre NO fijos y modo != pct
    const usedAmt = pctMandIdx.reduce((acc,i)=> acc + num(window.pagos[i].cuota), 0);
    let amountLeft = Math.max(0, remainingAmount - usedAmt);

    let poolIdx = [];
    let wSum = 0;
    let weights = [];

    for (let i = 0; i < window.pagos.length; i++) {
      if (fixed(i)) continue;
      const meta = window.__pagosMeta[i];
      if (meta.mode !== 'pct') {
        poolIdx.push(i);
        const w = Math.max(0, num(window.pagos[i].cuota)); // peso actual
        weights.push(w);
        wSum += w;
      }
    }

    if (poolIdx.length > 0) {
      if (wSum <= 0) {
        const eachAmt = amountLeft / poolIdx.length;
        poolIdx.forEach(i => window.pagos[i].cuota = Math.max(0, eachAmt).toFixed(2));
      } else {
        for (let k = 0; k < poolIdx.length; k++) {
          const i = poolIdx[k];
          const w = weights[k];
          const m = (amountLeft * w) / wSum;
          window.pagos[i].cuota = Math.max(0, m).toFixed(2);
        }
      }
    }

    // 5) Ajuste final por centavos:
    // ✅ Ajustar SOLO en el último NO fijo (para no mover los de arriba)
    const suma = window.pagos.reduce((acc,p)=> acc + num(p.cuota), 0);
    let diff = Math.round((totalFin - suma) * 100) / 100;

    if (Math.abs(diff) >= 0.01) {
      let idxAjuste = -1;
      for (let i = window.pagos.length - 1; i >= 0; i--) {
        if (!fixed(i)) { idxAjuste = i; break; }
      }
      // si todos son fijos (raro), ajusta el último fijo
      if (idxAjuste === -1 && fixedIdx.length) idxAjuste = fixedIdx[fixedIdx.length - 1];

      if (idxAjuste !== -1) {
        const v = num(window.pagos[idxAjuste].cuota) + diff;
        window.pagos[idxAjuste].cuota = Math.max(0, v).toFixed(2);
      }
    }

    // 6) Reflejar % real (para todos, incluyendo fijos)
    for (let i = 0; i < window.pagos.length; i++) {
      const m = num(window.pagos[i].cuota);
      const pReal = (totalFin > 0) ? (m / totalFin) * 100 : 0;
      window.__pagosMeta[i].pct = Math.max(0, pReal);

      // si está en modo %, deja draft coherente si no está siendo editado
      if (window.__pagosMeta[i].mode === 'pct') {
        // no sobrescribimos el draft si el usuario está escribiendo (se maneja en UI con skipIfFocused)
        if (window.__pagosMeta[i].pctDraft === '' || window.__pagosMeta[i].pctDraft == null) {
          window.__pagosMeta[i].pctDraft = (Math.round(pReal * 100) / 100).toFixed(2);
        }
      }
    }
  }

  // =======================
  // UI (Editor)
  // =======================
  function buildEditorUI() {
    planPagosDiv.innerHTML = '';
    window.__pagoUI.rows = [];
    window.__pagoUI.resumenEl = null;

    const tipo = tipoPago.value || '';
    const totalFin = obtenerTotalFinanciar();

    if (!tipo) {
      planPagosDiv.innerHTML = '<p style="color:#666;">Selecciona un plan para ver el detalle de pagos.</p>';
      return;
    }
    if (totalFin <= 0) {
      planPagosDiv.innerHTML = '<p style="color:#b91c1c;">Total a financiar inválido o cero</p>';
      return;
    }

    window.__pagoUI.infoCreditoEl = document.createElement('div');
    window.__pagoUI.infoCreditoEl.style.marginBottom = '10px';
    planPagosDiv.appendChild(window.__pagoUI.infoCreditoEl);

    ensureMetaLen();

    for (let i=0;i<window.pagos.length;i++){
      const pago = window.pagos[i];
      const meta = window.__pagosMeta[i];

      const fecha = isoToDate(pago.mes);
      const fechaStr = fecha.toLocaleDateString('es-MX',{day:'2-digit',month:'long',year:'numeric'});

      const row = document.createElement('div');
      row.className = 'mb-2';
      row.dataset.idx = String(i);

      const tipoActual = tipoPago.value || '';
      const puedeEliminar = (tipoActual === 'personalizado');

      row.innerHTML = `
        <div style="display:flex; gap:10px; align-items:flex-start; flex-wrap:wrap;">
          <div style="flex:1; min-width:260px;">
            <div style="margin-bottom:6px;">
              <span style="font-weight:600;">${(pago.descripcion || ('Pago ' + (i+1)))}</span>
              <span style="color:#6b7280; font-weight:400;"> - ${fechaStr}</span>
            </div>

            <div class="d-flex align-items-center gap-2 flex-wrap">
              <label class="form-check d-flex align-items-center gap-2" style="margin:0;">
                <input class="form-check-input pago-lock" type="checkbox" ${meta.locked ? 'checked':''}>
                <span class="form-check-label">Bloquear</span>
              </label>

              <select class="form-select pago-mode" style="max-width:170px;">
                <option value="monto">Monto (MXN)</option>
                <option value="pct">Porcentaje (%)</option>
              </select>

              <input type="number" step="0.01" min="0" class="form-control pago-monto" style="max-width:190px;">
              <input type="text" inputmode="decimal" class="form-control pago-pct" style="max-width:140px;" placeholder="Ej: 10">

              ${puedeEliminar ? `<button type="button" class="btn btn-sm btn-outline-danger pago-del">Eliminar</button>` : ''}

              <div class="ms-auto" style="color:#6b7280; font-size:13px; min-width:200px; text-align:right;">
                <div><span class="pago-ui-preview"></span></div>
                <div style="font-size:12px; color:#94a3b8;">% real: <span class="pago-ui-pct">0</span></div>
              </div>
            </div>

            <div class="small" style="color:#94a3b8; margin-top:6px;">
              ✅ Inteligente: al editar un pago, solo se ajustan los pagos de abajo. Bloquear = nunca se mueve.
            </div>
          </div>
        </div>
      `;

      const lockEl = row.querySelector('.pago-lock');
      const modeEl = row.querySelector('.pago-mode');
      const montoEl= row.querySelector('.pago-monto');
      const pctEl  = row.querySelector('.pago-pct');
      const prevEl = row.querySelector('.pago-ui-preview');
      const pctUi  = row.querySelector('.pago-ui-pct');

      modeEl.value = (meta.mode === 'pct') ? 'pct' : 'monto';

      function syncRowUI(skipIfFocused) {
        const totalFin = obtenerTotalFinanciar();
        const m = num(window.pagos[i].cuota);
        prevEl.textContent = money(m);

        const pReal = (totalFin > 0) ? (m / totalFin) * 100 : 0;
        pctUi.textContent = (Math.round(pReal * 100) / 100).toFixed(2);

        lockEl.checked = !!window.__pagosMeta[i].locked;
        const mode = window.__pagosMeta[i].mode === 'pct' ? 'pct' : 'monto';

        if (mode === 'pct') {
          montoEl.disabled = true;
          pctEl.disabled = false;

          const show = (window.__pagosMeta[i].pctDraft !== '' ? window.__pagosMeta[i].pctDraft : (window.__pagosMeta[i].pct || ''));
          if (!(skipIfFocused && document.activeElement === pctEl)) pctEl.value = String(show);
          if (!(skipIfFocused && document.activeElement === montoEl)) montoEl.value = m.toFixed(2);
        } else {
          pctEl.disabled = true;
          montoEl.disabled = false;

          if (!(skipIfFocused && document.activeElement === montoEl)) montoEl.value = m.toFixed(2);
          if (!(skipIfFocused && document.activeElement === pctEl)) pctEl.value = '';
        }
      }

      // EVENTS
      lockEl.addEventListener('change', function(){
        window.__pagosMeta[i].locked = !!lockEl.checked;

        // ✅ al bloquear, establecemos ancla aquí (lo de arriba se respeta)
        window.__pagosAnchorIdx = i;

        const totalFin = obtenerTotalFinanciar();
        distribuirParaQuePctSea100(totalFin, i);
        updateUI(true);
      });

      modeEl.addEventListener('change', function(){
        window.__pagosMeta[i].mode = (modeEl.value === 'pct') ? 'pct' : 'monto';

        // ✅ al cambiar modo, también se vuelve "ancla" el índice editado
        window.__pagosAnchorIdx = i;

        if (window.__pagosMeta[i].mode === 'pct') {
          const totalFin = obtenerTotalFinanciar();
          const pReal = (totalFin > 0) ? (num(window.pagos[i].cuota) / totalFin) * 100 : 0;
          window.__pagosMeta[i].pct = clamp(pReal, 0, 100);
          window.__pagosMeta[i].pctDraft = String(Math.round(window.__pagosMeta[i].pct * 100) / 100);
        } else {
          window.__pagosMeta[i].pctDraft = '';
          window.__pagosMeta[i].pct = 0;
        }

        const totalFin = obtenerTotalFinanciar();
        distribuirParaQuePctSea100(totalFin, i);
        updateUI(true);
      });

      // Monto
      montoEl.addEventListener('input', function(){
        // ✅ ancla en este pago -> no mover arriba
        window.__pagosAnchorIdx = i;

        const v = Math.max(0, num(montoEl.value));
        window.pagos[i].cuota = v.toFixed(2);

        const totalFin = obtenerTotalFinanciar();
        distribuirParaQuePctSea100(totalFin, i);
        updateUI(true);
      });

      // % con debounce (sin perder foco)
      const applyPctDebounced = debounce(function(){
        // ✅ ancla en este pago
        window.__pagosAnchorIdx = i;

        const totalFin = obtenerTotalFinanciar();
        const raw = String(window.__pagosMeta[i].pctDraft || '').trim().replace(',', '.');
        let p = 0;
        if (!(raw === '' || raw === '.' || raw === '-' || raw === '-.')) p = clamp(num(raw), 0, 100);
        window.__pagosMeta[i].pct = p;

        if (window.__pagosMeta[i].mode === 'pct') {
          const m = (totalFin * p) / 100;
          window.pagos[i].cuota = Math.max(0, m).toFixed(2);
        }

        distribuirParaQuePctSea100(totalFin, i);
        updateUI(true);
      }, 220);

      pctEl.addEventListener('input', function(){
        // ✅ ancla aquí también (mientras escribe)
        window.__pagosAnchorIdx = i;

        window.__pagosMeta[i].pctDraft = pctEl.value;

        // live preview de monto mientras escribe
        const totalFin = obtenerTotalFinanciar();
        const raw = String(window.__pagosMeta[i].pctDraft || '').trim().replace(',', '.');
        if (!(raw === '' || raw === '.' || raw === '-' || raw === '-.')) {
          const pLive = clamp(num(raw), 0, 100);
          window.__pagosMeta[i].pct = pLive;

          if (window.__pagosMeta[i].mode === 'pct') {
            const mLive = (totalFin * pLive) / 100;
            window.pagos[i].cuota = Math.max(0, mLive).toFixed(2);
          }
          updateUI(true);
        }

        applyPctDebounced();
      });

      pctEl.addEventListener('blur', function(){
        // ✅ ancla aquí
        window.__pagosAnchorIdx = i;

        const totalFin = obtenerTotalFinanciar();
        const raw = String(window.__pagosMeta[i].pctDraft || '').trim().replace(',', '.');
        window.__pagosMeta[i].pct = (raw === '' || raw === '.' || raw === '-' || raw === '-.') ? 0 : clamp(num(raw), 0, 100);

        if (window.__pagosMeta[i].mode === 'pct') {
          const m = (totalFin * window.__pagosMeta[i].pct) / 100;
          window.pagos[i].cuota = Math.max(0, m).toFixed(2);
        }

        distribuirParaQuePctSea100(totalFin, i);
        updateUI(true);
      });

      // Eliminar (solo personalizado)
      const delBtn = row.querySelector('.pago-del');
      if (delBtn) {
        delBtn.addEventListener('click', function(){
          window.pagos.splice(i,1);
          window.__pagosMeta.splice(i,1);

          // ✅ reajustar ancla (si borras arriba, baja)
          if (window.__pagosAnchorIdx >= window.pagos.length) window.__pagosAnchorIdx = window.pagos.length - 1;

          if (mesesPersonalizado) mesesPersonalizado.value = Math.max(1, window.pagos.length - 1);

          const totalFin = obtenerTotalFinanciar();
          distribuirParaQuePctSea100(totalFin, window.__pagosAnchorIdx);
          rebuildAll();
        });
      }

      planPagosDiv.appendChild(row);
      window.__pagoUI.rows.push({ sync: syncRowUI });
      syncRowUI(false);
    }

    const resumen = document.createElement('p');
    resumen.style.marginTop = '10px';
    resumen.style.color = '#334155';
    resumen.style.fontWeight = '600';
    planPagosDiv.appendChild(resumen);
    window.__pagoUI.resumenEl = resumen;

    updateUI(false);
  }

  function updateResumen(totalFin) {
    const suma = window.pagos.reduce((acc,p)=> acc + num(p.cuota), 0);
    const diff = Math.round((totalFin - suma) * 100) / 100;
    const pctTotal = (totalFin > 0) ? (suma / totalFin) * 100 : 0;

    const okMonto = Math.abs(diff) < 0.01;
    const okPct   = Math.abs(pctTotal - 100) < 0.01;

    if (window.__pagoUI.resumenEl) {
      window.__pagoUI.resumenEl.textContent =
        'Total a financiar: ' + money(totalFin) +
        ' | Total pagos: ' + money(suma) +
        ' | # pagos: ' + window.pagos.length +
        ' | % total: ' + (Math.round(pctTotal*100)/100).toFixed(2) +
        ' | Estado: ' + ((okMonto && okPct) ? 'OK' : 'Revisar');
    }
  }

  function updateUI(skipIfFocused) {
    const totalFin = obtenerTotalFinanciar();
    ensureMetaLen();

    for (let i=0;i<window.__pagoUI.rows.length;i++){
      const r = window.__pagoUI.rows[i];
      if (r && typeof r.sync === 'function') r.sync(!!skipIfFocused);
    }

    // Hidden JSON para backend
    const pagosHidden = document.getElementById('pagosJsonInput') || document.getElementById('pagos_json');
    if (pagosHidden) {
      const clean = window.pagos.map(p => ({
        cuota: String(p.cuota),
        descripcion: String(p.descripcion||''),
        mes: String(p.mes||'')
      }));
      pagosHidden.value = JSON.stringify(clean);
    }

    updateResumen(totalFin);
  }

  function rebuildAll() {
    buildEditorUI();
  }

  // ==========================
  // Generación de planes base
  // ==========================
  function setPagosBase(arr) {
    window.pagos = (arr || []).map(p => ({
      cuota: (num(p.cuota)).toFixed(2),
      descripcion: p.descripcion || '',
      mes: p.mes
    }));
    window.__pagosMeta = window.pagos.map(() => ({ locked:false, mode:'monto', pct:0, pctDraft:'' }));
    ensureMetaLen();

    // ✅ reset ancla cuando generas base
    window.__pagosAnchorIdx = -1;
  }

  function actualizarPlanPagosNoPersonalizado(totalFin) {
    const tipo = tipoPago.value || '';
    const baseISO = formatoFechaISO(new Date());
    const baseDate = isoToDate(baseISO);

    function pagoObj(cuota, offsetMeses, descripcion) {
      const d = new Date(baseDate);
      d.setMonth(d.getMonth() + offsetMeses);
      return { cuota: num(cuota), descripcion: descripcion, mes: formatoFechaISO(d) };
    }

    if (!tipo) return;
    if (window.__pagoUI.infoCreditoEl) window.__pagoUI.infoCreditoEl.innerHTML = '';

    if (tipo === 'contado') {
      setPagosBase([ pagoObj(totalFin, 0, 'Pago único') ]);

    } else if (tipo === 'estatico') {
      if (totalFin < 500000) {
        setPagosBase([
          pagoObj(totalFin * 0.5, 0, 'Pago inicial'),
          pagoObj(totalFin * 0.25, 1, 'Primer pago'),
          pagoObj(totalFin * 0.25, 2, 'Segundo pago'),
        ]);
      } else {
        const primerPago = totalFin * 0.4;
        const arr = [pagoObj(primerPago, 0, 'Pago inicial')];
        const restante = totalFin - primerPago;
        const numPagos = Math.min(6, Math.max(4, Math.ceil(restante / 50000)));
        const cuota = restante / numPagos;
        for (let i=0;i<numPagos;i++){
          arr.push(pagoObj(cuota, i+1, (nombresPagos[i] || ((i+1)+'º')) + ' pago'));
        }
        setPagosBase(arr);
      }

    } else if (tipo === 'dinamico') {
      const ini = num(pagoInicial && pagoInicial.value ? pagoInicial.value : '0');
      if (ini <= 0 || ini >= totalFin) {
        planPagosDiv.innerHTML = '<p style="color:#b91c1c;">Pago inicial inválido</p>';
        return;
      }
      const restante2 = totalFin - ini;
      const num2 = (totalFin < 150000) ? 2 : (totalFin < 400000) ? 4 : 6;
      const cuota2 = restante2 / num2;

      const arr = [pagoObj(ini, 0, 'Pago inicial')];
      for (let j=0;j<num2;j++){
        arr.push(pagoObj(cuota2, j+1, (nombresPagos[j] || ((j+1)+'º')) + ' pago'));
      }
      setPagosBase(arr);

    } else if (tipo === 'credito') {
      const iniC = num(pagoCreditoInicial && pagoCreditoInicial.value ? pagoCreditoInicial.value : '0');
      const plazo = parseInt(plazoCredito && plazoCredito.value ? plazoCredito.value : '6',10) || 6;

      if (iniC < 0 || iniC >= totalFin) {
        planPagosDiv.innerHTML = '<p style="color:#b91c1c;">Pago inicial de crédito inválido</p>';
        return;
      }
      if (plazo <= 0) {
        planPagosDiv.innerHTML = '<p style="color:#b91c1c;">Plazo inválido</p>';
        return;
      }

      const tasa = 0.05;
      const monto = totalFin - iniC;
      const totalCred = monto + (monto * tasa * plazo);
      const cuotaM = totalCred / plazo;

      const arr = [pagoObj(iniC, 0, 'Pago inicial')];
      for (let k=0;k<plazo;k++){
        arr.push(pagoObj(cuotaM, k+1, (nombresPagos[k] || ((k+1)+'º')) + ' pago'));
      }
      setPagosBase(arr);

      setTimeout(function(){
        if (window.__pagoUI.infoCreditoEl) {
          window.__pagoUI.infoCreditoEl.innerHTML =
            '<p style="margin:0; color:#334155;"><strong>Total a pagar con crédito:</strong> ' + money(totalCred) + '</p>';
        }
      }, 0);

    } else if (tipo === 'personalizado') {
      return;
    } else {
      planPagosDiv.innerHTML = '<p style="color:#666;">Tipo de plan no soportado.</p>';
      return;
    }

    // ✅ sin ancla aquí (solo respeta locked)
    distribuirParaQuePctSea100(totalFin, -1);
    rebuildAll();
  }

  function generarPersonalizadoNuevo(totalFin) {
    const meses = parseInt(mesesPersonalizado && mesesPersonalizado.value ? mesesPersonalizado.value : '1',10) || 1;
    const totalPagos = meses + 1;

    const baseISO = formatoFechaISO(new Date());
    let currentISO = baseISO;

    const cuotaBase = totalFin / totalPagos;

    const arr = [];
    for (let i=0;i<totalPagos;i++){
      if (i > 0) currentISO = addMonthsToISO(currentISO, 1);
      const desc = (i === 0)
        ? 'Pago inicial'
        : (nombresPagos[i-1] || ((i+1)+'º')) + ' pago';
      arr.push({ cuota: cuotaBase, descripcion: desc, mes: currentISO });
    }

    setPagosBase(arr);
    distribuirParaQuePctSea100(totalFin, -1);
    rebuildAll();
  }

  function agregarPagoPersonalizadoExtra() {
    const totalFin = obtenerTotalFinanciar();
    const baseISO = formatoFechaISO(new Date());

    let lastISO = baseISO;
    if (window.pagos.length > 0) lastISO = window.pagos[window.pagos.length-1].mes || lastISO;

    const nuevoISO = addMonthsToISO(lastISO, 1);
    const idx = window.pagos.length;
    const desc = (idx === 0) ? 'Pago inicial' : (nombresPagos[idx-1] || ((idx+1)+'º')) + ' pago';

    window.pagos.push({ cuota:'0.00', descripcion: desc, mes: nuevoISO });
    window.__pagosMeta.push({ locked:false, mode:'pct', pct:0, pctDraft:'' });

    if (mesesPersonalizado) mesesPersonalizado.value = Math.max(1, window.pagos.length - 1);

    // ✅ al agregar, no pongas ancla (solo respeta locked)
    distribuirParaQuePctSea100(totalFin, -1);
    rebuildAll();
  }

  function ponerBotonAgregarPersonalizado() {
    if (!listaPagosPersonalizados) return;
    let btn = listaPagosPersonalizados.querySelector('#btn-add-pago-personalizado');
    if (btn) return;

    btn = document.createElement('button');
    btn.type = 'button';
    btn.id = 'btn-add-pago-personalizado';
    btn.className = 'btn btn-sm btn-outline-secondary mt-2';
    btn.textContent = 'Agregar pago';
    btn.addEventListener('click', agregarPagoPersonalizadoExtra);
    listaPagosPersonalizados.appendChild(btn);
  }

  function limpiarBotonAgregarPersonalizado() {
    if (!listaPagosPersonalizados) return;
    const btn = listaPagosPersonalizados.querySelector('#btn-add-pago-personalizado');
    if (btn) btn.remove();
  }

  function actualizarPlanPagos() {
    mostrarOpcionesTipo();
    const totalFin = obtenerTotalFinanciar();
    const tipo = tipoPago.value || '';

    if (!tipo) {
      planPagosDiv.innerHTML = '<p style="color:#666;">Selecciona un plan para ver el detalle de pagos.</p>';
      return;
    }

    if (tipo === 'personalizado') {
      ponerBotonAgregarPersonalizado();
      if (window.pagos && window.pagos.length) {
        distribuirParaQuePctSea100(totalFin, -1);
        rebuildAll();
      } else {
        generarPersonalizadoNuevo(totalFin);
      }
    } else {
      limpiarBotonAgregarPersonalizado();
      actualizarPlanPagosNoPersonalizado(totalFin);
    }
  }

  // ==========================
  // Eventos
  // ==========================
  tipoPago.addEventListener('change', function(){
    window.pagos = [];
    window.__pagosMeta = [];
    window.__pagosAnchorIdx = -1;
    actualizarPlanPagos();
  });

  [pagoInicial, pagoCreditoInicial, plazoCredito, mesesPersonalizado].forEach(function(inp){
    if (inp) inp.addEventListener('input', function(){
      window.pagos = [];
      window.__pagosMeta = [];
      window.__pagosAnchorIdx = -1;
      actualizarPlanPagos();
    });
  });

  if (anticipoMontoInput) {
    anticipoMontoInput.addEventListener('input', function(){
      const totalFin = obtenerTotalFinanciar();
      // ✅ anticipo cambia: NO uses ancla (solo locked). Si quieres, puedes conservar ancla global.
      distribuirParaQuePctSea100(totalFin, -1);
      updateUI(true);
    });
    anticipoMontoInput.addEventListener('change', function(){
      const totalFin = obtenerTotalFinanciar();
      distribuirParaQuePctSea100(totalFin, -1);
      updateUI(true);
    });
  }

  const onTotalEvent = debounce(function(){
    const totalFin = obtenerTotalFinanciar();
    distribuirParaQuePctSea100(totalFin, -1);
    updateUI(true);
  }, 80);

  window.addEventListener('total:changed', onTotalEvent);

  if (typeof MutationObserver !== 'undefined') {
    const totalNode = document.getElementById('total');
    const netNode   = document.getElementById('total_neto_ui');
    if (totalNode) new MutationObserver(onTotalEvent).observe(totalNode,{childList:true,characterData:true,subtree:true});
    if (netNode)   new MutationObserver(onTotalEvent).observe(netNode,{childList:true,characterData:true,subtree:true});
  }

  // Render inicial
  mostrarOpcionesTipo();
  actualizarPlanPagos();

});
</script>
<script>
// =====================================================
// Form submit: adjuntar window.pagos a pagos_json
// =====================================================
document.addEventListener('DOMContentLoaded', function () {
    const form           = document.getElementById('form-venta');
    const inputPagosJson = document.getElementById('pagosJsonInput');
    const selectPlan     = document.getElementById('tipoPago');

    if (!form || !inputPagosJson || !selectPlan) return;

    form.addEventListener('submit', function (e) {
        console.log('Form submit capturado (pagos_json)');
        console.log('window.pagos:', window.pagos);

        const planSeleccionado = selectPlan.value;

        if (!planSeleccionado) {
            alert('Selecciona un plan de pagos antes de continuar.');
            e.preventDefault();
            return;
        }

        // Debe haber al menos un pago
        if (!window.pagos || window.pagos.length === 0) {
            alert('No hay pagos definidos, por favor selecciona o genera un plan de pagos.');
            e.preventDefault();
            return;
        }

        // Formatear los pagos y pasarlos al input oculto como JSON
        const pagosFormateados = window.pagos.map(function (pago) {
            return {
                cuota: pago.cuota,
                descripcion: pago.descripcion,
                mes: pago.mes // 'YYYY-MM-DD'
            };
        });

        inputPagosJson.value = JSON.stringify(pagosFormateados);
        console.log('Pagos a enviar:', inputPagosJson.value);
    });
});
</script>

<script>
// =====================================================
// Buscador de productos + paquetes
// =====================================================
document.addEventListener("DOMContentLoaded", function () {
    const buscarProducto    = document.getElementById("buscarProducto");
    const dropdownProductos = document.getElementById("dropdownProductos");
    if (!buscarProducto || !dropdownProductos) return;

    let controladorAbort = new AbortController();

    function realizarBusqueda(searchQuery) {
        controladorAbort.abort();
        controladorAbort = new AbortController();

        if (searchQuery.length > 1) {
            Promise.all([
                fetch("{{ route('productos.search') }}?search=" + encodeURIComponent(searchQuery), { signal: controladorAbort.signal }).then(function (res) { return res.json(); }),
                fetch("{{ route('paquetes.search') }}?search=" + encodeURIComponent(searchQuery), { signal: controladorAbort.signal }).then(function (res) { return res.json(); })
            ])
            .then(function (results) {
                var productos = results[0];
                var paquetes  = results[1];
                mostrarResultados(paquetes, productos);
            })
            .catch(function (error) {
                if (error.name !== "AbortError") {
                    console.error("Error en la búsqueda:", error);
                }
            });
        } else {
            restaurarListaOriginal();
        }
    }

    function mostrarResultados(paquetes, productos) {
        dropdownProductos.innerHTML = '' +
            '<li>' +
            '   <button type="button" class="dropdown-item modern-dropdown-item" data-bs-toggle="modal" data-bs-target="#modal1">' +
            '       + Crear Producto' +
            '   </button>' +
            '</li>';

        if (paquetes && paquetes.length > 0) {
            paquetes.forEach(function (paquete) {
                var li = document.createElement("li");
                li.innerHTML = '' +
                    '<button type="button" class="dropdown-item modern-dropdown-item"' +
                    '        data-id="' + paquete.id + '"' +
                    '        data-productos=\'' + JSON.stringify(paquete.productos || []) + '\'' +
                    '        onclick="agregarPaqueteDesdeData(this)">' +
                    '   📦 ' + String(paquete.nombre || '').toUpperCase() + ' - Paquete' +
                    '</button>';
                dropdownProductos.appendChild(li);
            });
        }

        if (productos && productos.length > 0) {
            productos.sort(function (a, b) {
                var ta = (a.tipo_equipo || '').toLowerCase();
                var tb = (b.tipo_equipo || '').toLowerCase();
                if (ta < tb) return -1;
                if (ta > tb) return 1;
                return 0;
            });

            productos.forEach(function (producto) {
                var li = document.createElement("li");
                var tipo   = String(producto.tipo_equipo || '');
                var modelo = String(producto.modelo || '');
                var marca  = String(producto.marca || '');
                var img    = String(producto.imagen || '');
                var precio = Number(producto.precio || 0);

                li.innerHTML = '' +
                    '<button type="button" class="dropdown-item modern-dropdown-item d-flex align-items-center"' +
                    '        onclick="agregarProductoDesdeDropdown(' +
                               producto.id + ', ' +
                               '\'' + tipo.replace(/'/g, "\\'") + '\', ' +
                               '\'' + modelo.replace(/'/g, "\\'") + '\', ' +
                               '\'' + marca.replace(/'/g, "\\'") + '\', ' +
                               precio + ', ' +
                               '\'' + img.replace(/'/g, "\\'") + '\'' +
                    ')">' +
                    '   <img src="/storage/' + img + '" alt="' + tipo + '" class="modern-product-img me-2" ' +
                    '        style="width: 40px; height: 40px; object-fit: cover; border-radius: 6px;">' +
                    '   <div class="flex-grow-1 modern-product-info">' +
                    '       <strong>' + tipo.toUpperCase() + '</strong> - ' + modelo.toUpperCase() + ' ' + marca.toUpperCase() +
                    '       <br>' +
                    '       <span class="text-muted modern-product-price">' + (Math.abs(precio) <= 0.00001 ? 'REGALO' : ('$' + precio.toFixed(2))) + '</span>' +
                    '   </div>' +
                    '   <span class="badge modern-badge">' + (producto.stock || 0) + ' unidades</span>' +
                    '</button>';
                dropdownProductos.appendChild(li);
            });
        }

        if ((!paquetes || paquetes.length === 0) && (!productos || productos.length === 0)) {
            dropdownProductos.innerHTML += '<li><button type="button" class="dropdown-item text-muted" disabled>No se encontraron resultados</button></li>';
        }
    }

    function restaurarListaOriginal() {
        Promise.all([
            fetch("{{ route('productos.search') }}?search=").then(function (res) { return res.json(); }),
            fetch("{{ route('paquetes.search') }}?search=").then(function (res) { return res.json(); })
        ])
        .then(function (results) {
            var productos = results[0];
            var paquetes  = results[1];
            mostrarResultados(paquetes, productos);
        })
        .catch(function (error) {
            console.error("Error al restaurar lista:", error);
        });
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

    // Mostrar todos al iniciar
    restaurarListaOriginal();

    // Función global para insertar paquetes
    window.agregarPaqueteDesdeData = function (element) {
        var productos = [];
        try {
            productos = JSON.parse(element.getAttribute("data-productos") || '[]');
        } catch (e) {
            productos = [];
        }
        agregarPaquete(productos);
    };

    function agregarPaquete(productos) {
        (productos || []).forEach(function (producto) {
            agregarProductoDesdeDropdown(
                producto.id,
                producto.tipo_equipo,
                producto.modelo,
                producto.marca,
                parseFloat(producto.precio || 0),
                producto.imagen
            );
        });
    }
});
</script>

<script>
// =====================================================
// Productos: tabla, totales, IVA, series  (REGALO + switch)
// =====================================================
$(document).ready(function () {
    // Al enviar, preparamos productos_json
    $('#form-venta').on('submit', function () {
        prepararProductosJSON();

        var productos = JSON.parse($('#productos_json').val() || '[]');
        console.log('🧪 Enviando productos_json:', productos);

        var algunoSinSerie = productos.some(function (p) {
            return !Array.isArray(p.registro_id) ||
                   p.registro_id.length !== p.cantidad ||
                   p.registro_id.indexOf(null) !== -1;
        });
        /*
        if (algunoSinSerie) {
            e.preventDefault();
            alert('Faltan números de serie en uno o más productos.');
            return;
        }
        */
    });

    // Actualizar totales en tiempo real si cambian descuento, envío o IVA
    $(document).on('input change', '#aplica_iva, #envio, #descuento', function () {
        actualizarTotal();
    });
});

// EPS para detectar cero
var EPS_REGALO = 0.00001;

// Render de subtotal (texto) para UI
function renderSubtotalTexto(v) {
    v = parseFloat(v) || 0;
    return (Math.abs(v) <= EPS_REGALO) ? 'REGALO' : v.toFixed(2);
}

// Agregar producto a la tabla  ✅ (con switch Regalo)
function agregarProductoDesdeDropdown(id, nombre, modelo, marca, precio, imagen) {
    if (!id || !nombre) return;
    if ($('#tabla-productos tbody tr[data-id="' + id + '"]').length) {
        alert('Este producto ya ha sido agregado.');
        return;
    }

    var p = parseFloat(precio) || 0;
    var subInit = p; // Subtotal inicial = p * 1 (cantidad default)

    var esRegaloInit = (Math.abs(p) <= EPS_REGALO);

    var fila = '' +
      '<tr data-id="' + id + '" data-precio="' + p + '" data-subtotal="' + subInit.toFixed(2) + '" data-es-regalo="' + (esRegaloInit ? '1' : '0') + '">' +
      '  <td><img src="/storage/' + imagen + '" width="50" alt="Imagen producto"></td>' +
      '  <td class="equipo">' + nombre + '</td>' +
      '  <td>' + modelo + '</td>' +
      '  <td>' + marca + '</td>' +
      '  <td><input type="number" class="form-control cantidad" value="1" min="1" onchange="actualizarSubtotal(this)"></td>' +
      '  <td class="subtotal">' + renderSubtotalTexto(subInit) + '</td>' +
      '  <td><input type="number" class="form-control sobreprecio" value="0" min="0" onchange="actualizarSubtotal(this)"></td>' +
      '  <td><div class="serie-container"></div></td>' +

      // ✅ Columna REGALO (switch)
      '  <td class="text-center">' +
      '    <div class="gift-switch">' +
      '      <input type="checkbox" class="gift-toggle" ' + (esRegaloInit ? 'checked' : '') + '>' +
      '      <span class="gift-muted">Regalo</span>' +
      '    </div>' +
      '  </td>' +

      '  <td class="text-center"><button type="button" class="btn btn-sm btn-danger eliminar-fila btn-icon"><i class="fas fa-trash-alt"></i></button></td>' +
      '</tr>';

    $('#tabla-productos tbody').append(fila);
    var nueva = $('#tabla-productos tbody tr').last();
    generarSelects(nueva, 1);

    // Si entró como regalo, forzar UI/estado
    if (esRegaloInit) {
        nueva.attr('data-subtotal', '0.00');
        nueva.find('.subtotal').text('REGALO');
        nueva.find('.sobreprecio').val('0').prop('disabled', true);
    }

    actualizarTotal();
}

// ✅ Switch regalo ON/OFF
$(document).on('change', '.gift-toggle', function () {
    var tr = $(this).closest('tr');
    var checked = $(this).is(':checked');

    tr.attr('data-es-regalo', checked ? '1' : '0');

    if (checked) {
        tr.attr('data-subtotal', '0.00');
        tr.find('.subtotal').text('REGALO');
        tr.find('.sobreprecio').val('0').prop('disabled', true);
    } else {
        tr.find('.sobreprecio').prop('disabled', false);
        actualizarSubtotal(tr.find('.cantidad')[0]); // recalcula
        return; // actualizarSubtotal ya llama actualizarTotal
    }

    actualizarTotal();
});

// Eliminar fila
$(document).on('click', '.eliminar-fila', function () {
    $(this).closest('tr').remove();
    actualizarTotal();
});

// Actualizar subtotal + selects cuando cambia cantidad o sobreprecio
$(document).on('input change', '.cantidad, .sobreprecio', function () {
    actualizarSubtotal(this);
});

// =====================================================
// ✅ FIX: ya no se borran los números de serie capturados
// al modificar el Sobreprecio. Solo se regeneran los
// inputs de serie cuando cambia la CANTIDAD real de
// inputs necesarios (es decir, cuando cambia "cantidad").
// =====================================================
function actualizarSubtotal(el) {
    var tr   = $(el).closest('tr');
    var qty  = parseInt(tr.find('.cantidad').val(), 10) || 1;

    // ✅ Si es regalo, siempre subtotal = 0
    var esRegalo = (tr.attr('data-es-regalo') === '1');
    if (esRegalo) {
        tr.attr('data-subtotal', '0.00');
        tr.find('.subtotal').text('REGALO');
        tr.find('.sobreprecio').val('0').prop('disabled', true);
    } else {
        tr.find('.sobreprecio').prop('disabled', false);

        var base = parseFloat(tr.data('precio')) || 0;
        var extra= parseFloat(tr.find('.sobreprecio').val()) || 0;

        var sub  = (base + extra) * qty;

        // Guardar subtotal real para cálculos aunque UI diga REGALO
        tr.attr('data-subtotal', sub.toFixed(2));

        // UI
        tr.find('.subtotal').text(renderSubtotalTexto(sub));
    }

    // ✅ Solo regenerar los inputs de número de serie si la cantidad de
    // inputs actuales no coincide con la cantidad solicitada. Así, si
    // solo cambió el sobreprecio (qty igual), NO se pierden las series
    // ya capturadas por el usuario.
    var inputsActuales = tr.find('.serie-container .dropdown-search').length;
    if (inputsActuales !== qty) {
        generarSelects(tr, qty);
    }

    actualizarTotal();
}

// Recalcula totales (IVA incluye envío)
function actualizarTotal() {
    var subtotal = 0;

    $('#tabla-productos tbody tr').each(function () {
        // Tomar el subtotal REAL desde data-subtotal (no del texto)
        var st = parseFloat($(this).attr('data-subtotal')) || 0;
        subtotal += st;
    });

    var desc  = parseFloat($('#descuento').val()) || 0;
    var envio = parseFloat($('#envio').val()) || 0;

    // IVA ahora contempla el envío
    var ivaBase = Math.max(0, subtotal - desc + envio);
    var iva     = $('#aplica_iva').is(':checked') ? (ivaBase * 0.16) : 0;

    var total   = ivaBase + iva;

    // Mostrar/mandar valores
    $('#subtotal').text(subtotal.toFixed(2));
    $('#subtotal_input').val(subtotal.toFixed(2));

    $('#iva').text(iva.toFixed(2));
    $('#iva_input').val(iva.toFixed(2));

    $('#total').text(total.toFixed(2));
    $('#total_input').val(total.toFixed(2));

    // Evento para plan de pagos / trade-in
    if (typeof CustomEvent !== 'undefined') {
        var evt = new CustomEvent('total:changed', { detail: { total: total } });
        window.dispatchEvent(evt);
    }

    return total;
}

// Genera inputs con buscador dinámico para número de serie
function generarSelects(tr, n) {
    var cont = tr.find('.serie-container').empty();
    for (var i = 0; i < n; i++) {
        cont.append('' +
          '<div class="dropdown-search d-flex align-items-center gap-1 mb-1" style="position: relative; width: 220px;">' +
          '  <input type="text" class="form-control search-input" placeholder="Buscar número de serie..." autocomplete="off" />' +
          '  <input type="hidden" class="registro-id-hidden" />' +
          '  <div class="dropdown-list" style="position: absolute; top: 100%; left: 0; right: 0; background: white; border: 1px solid #ccc; max-height: 150px; overflow-y: auto; display: none; z-index: 1000; border-radius: 4px;"></div>' +
          '  <button type="button" class="btn btn-outline-info btn-sm ver-registro" title="Ver detalles" style="height: 38px; margin-left: 5px;">' +
          '    <i class="bi bi-eye"></i>' +
          '  </button>' +
          '</div>'
        );
    }
}

// Buscar registros disponibles
$(document).on('input', '.search-input', function () {
    var input       = $(this);
    var query       = (input.val() || '').toLowerCase();
    var dropdown    = input.siblings('.dropdown-list');
    var hiddenInput = input.siblings('.registro-id-hidden');

    if (query.length < 1) {
        dropdown.hide();
        hiddenInput.val('');
        return;
    }

    $.get('/registros-disponibles', function (data) {
        var filtered = (data || []).filter(function (item) {
            return ((item.numero_serie || '').toLowerCase().indexOf(query) !== -1);
        });
        dropdown.empty();
        if (filtered.length === 0) {
            dropdown.html('<div style="padding: 5px;">No hay resultados</div>').show();
        } else {
            filtered.forEach(function (item) {
                dropdown.append('<div data-id="' + item.id + '" style="padding: 5px; cursor: pointer;">' + item.numero_serie + '</div>');
            });
            dropdown.show();
        }
    });
});

// Seleccionar registro
$(document).on('click', '.dropdown-list div', function () {
    var div       = $(this);
    var id        = div.data('id');
    var val       = div.text();
    var container = div.closest('.dropdown-search');
    container.find('.search-input').val(val);
    container.find('.registro-id-hidden').val(id);
    container.find('.dropdown-list').hide();
});

// Cierra dropdown si clic fuera
$(document).on('click', function (e) {
    if (!$(e.target).closest('.dropdown-search').length) {
        $('.dropdown-list').hide();
    }
});

// Mostrar detalles con SweetAlert
$(document).on('click', '.ver-registro', function () {
    var container  = $(this).closest('.dropdown-search');
    var registroId = container.find('.registro-id-hidden').val();

    if (!registroId) {
        Swal.fire('Atención', 'Primero elige un número de serie.', 'warning');
        return;
    }

    $.get('/registro-info/' + registroId, function (data) {
        var evidenciaUrl = data.evidencia1 ? data.evidencia1.replace(/^,/, '') : null;

        Swal.fire({
            title: 'Detalles del Registro',
            html:
              '<p><strong>Equipo:</strong> ' + (data.tipo_equipo || '—') + '</p>' +
              '<p><strong>Subtipo:</strong> ' + (data.subtipo_equipo || '—') + '</p>' +
              '<p><strong>Modelo:</strong> ' + (data.modelo || '—') + '</p>' +
              '<p><strong>Marca:</strong> ' + (data.marca || '—') + '</p>' +
              '<p><strong>Serie:</strong> ' + (data.numero_serie || '') + '</p>' +
              '<p><strong>Estado:</strong> ' + (data.estado_proceso || '') + '</p>' +
              (evidenciaUrl
                ? '<img src="' + evidenciaUrl + '" style="width:100%; max-height:200px; object-fit:contain; border-radius:15px; margin-top:8px;">'
                : '<em style="color:#999;">Sin evidencia</em>'),
            confirmButtonText: 'Cerrar',
            customClass: {
                popup: 'rounded-4 shadow',
                confirmButton: 'btn btn-primary'
            },
            buttonsStyling: false,
            width: 500
        });
    }).fail(function () {
        Swal.fire('Error', 'No se pudo cargar la información.', 'error');
    });
});

// Prepara JSON final para enviar al backend ✅ (incluye es_regalo)
function prepararProductosJSON() {
    var arr = [];
    $('#tabla-productos tbody tr').each(function (i) {
        var tr  = $(this);
        var pid = tr.data('id');
        var qty = parseInt(tr.find('.cantidad').val(), 10) || 1;
        var pu  = parseFloat(tr.data('precio')) || 0;
        var sp  = parseFloat(tr.find('.sobreprecio').val()) || 0;

        // Subtotal REAL desde data-subtotal
        var st  = parseFloat(tr.attr('data-subtotal')) || 0;

        var regs = tr.find('.registro-id-hidden').map(function () {
            return $(this).val() || null;
        }).get();

        var esRegalo = (tr.attr('data-es-regalo') === '1');

        arr.push({
            producto_id:     pid,
            cantidad:        qty,
            precio_unitario: pu,
            sobreprecio:     sp,
            subtotal:        st,
            registro_id:     regs,
            es_regalo:       esRegalo ? 1 : 0
        });
        console.log('🧾 Producto ' + (i + 1) + ':', arr[i]);
    });
    $('#productos_json').val(JSON.stringify(arr));
    console.log('🧪 JSON generado:', arr);
}
</script>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// =====================================================
// Búsqueda / selección de cliente + creación rápida
// =====================================================

// Cerrar modal de éxito de cliente (botón "Listo")
function cerrarModal() {
    const modalEl = document.getElementById("cliente_creado");
    if (!modalEl) return;

    if (window.bootstrap && bootstrap.Modal) {
        const modalInstance = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
        modalInstance.hide();
    }
}

document.addEventListener("DOMContentLoaded", function () {
    const searchInput   = document.getElementById("search-client");
    const clientList    = document.getElementById("client-list");
    const clienteIdInput= document.getElementById("cliente_id");
    const clientDetails = document.getElementById("client-details");
    const formVenta     = document.getElementById("form-venta");

    if (!searchInput || !clientList || !clienteIdInput || !clientDetails || !formVenta) return;

    // Validación antes de enviar el formulario
    formVenta.addEventListener("submit", function (e) {
        console.log("cliente_id al enviar:", clienteIdInput.value);
        if (!clienteIdInput.value) {
            e.preventDefault();
            alert("Por favor selecciona un cliente antes de continuar.");
        }
    });

    // Función para cargar clientes dinámicamente desde el backend
    function loadClients(search) {
        if (typeof search === 'undefined') search = "";

        fetch('/buscar-clientes?search=' + encodeURIComponent(search), {
            method: "GET",
            headers: {
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                "Accept": "application/json"
            }
        })
        .then(function (response) {
            return response.json();
        })
        .then(function (clients) {
            clientList.innerHTML =
                '<li>' +
                '  <button type="button" class="dropdown-item modern-dropdown-item" onclick=\'selectClientFromEncoded("' +
                    encodeURIComponent(JSON.stringify({ id: 1, nombre: "Público en General", apellido: "", telefono: "", email: "", comentarios: "" })) +
                '")\'>' +
                '      Público en General' +
                '  </button>' +
                '</li>' +
                '<li>' +
                '  <button type="button" class="dropdown-item modern-dropdown-item" onclick="openCreateClientModal()">' +
                '      + Crear nuevo cliente' +
                '  </button>' +
                '</li>';

            if ((!clients || clients.length === 0) && search !== "") {
                clientList.innerHTML +=
                    '<li><button type="button" class="dropdown-item disabled">No se encontraron resultados</button></li>';
            } else {
                (clients || []).forEach(function (client) {
                    const clientFullName = ((client.nombre || '').toUpperCase() + ' ' + (client.apellido || '').toUpperCase()).trim();
                    const encodedClient  = encodeURIComponent(JSON.stringify(client));
                    const clientItem     = document.createElement("li");
                    clientItem.innerHTML =
                        '<button type="button" class="dropdown-item modern-dropdown-item" onclick=\'selectClientFromEncoded("' + encodedClient + '")\'>' +
                        (clientFullName || '(SIN NOMBRE)') +
                        '</button>';
                    clientList.appendChild(clientItem);
                });
            }
        })
        .catch(function (error) {
            console.error("Error al cargar clientes:", error);
        });
    }

    // Función para decodificar el cliente
    window.selectClientFromEncoded = function (encoded) {
        const client = JSON.parse(decodeURIComponent(encoded));
        selectClient(client);
    };

    // Función para seleccionar cliente
    window.selectClient = function (client) {
        console.log("Seleccionado:", client);
        console.log("ID del cliente:", client.id);

        const nombre = (client.nombre || '').toUpperCase();
        const apellido = (client.apellido || '').toUpperCase();

        searchInput.value = (nombre + ' ' + apellido).trim();
        clienteIdInput.value = client.id != null ? client.id : "";

        clientDetails.innerHTML =
              '<p><strong>Nombre:</strong> ' + nombre + ' ' + apellido + '</p>'
            + '<p><strong>Teléfono:</strong> ' + (client.telefono || "No registrado") + '</p>'
            + '<p><strong>Email:</strong> ' + (client.email || "No registrado") + '</p>'
            + '<p><strong>Dirección:</strong> ' + (client.comentarios || "No registrado") + '</p>';
        clientDetails.style.padding = "15px";
        clientList.classList.remove("show");
    };

    // Mostrar modal crear cliente
    window.openCreateClientModal = function () {
        const modalFormularioEl = document.getElementById("modal_formulario");
        if (modalFormularioEl && window.bootstrap && bootstrap.Modal) {
            const modalFormulario = new bootstrap.Modal(modalFormularioEl);
            modalFormulario.show();
        }
    };

    // Eventos de búsqueda
    searchInput.addEventListener("input", function () {
        const search = searchInput.value.trim();
        loadClients(search);
        clientList.classList.add("show");
    });

    searchInput.addEventListener("keydown", function (e) {
        if (e.key === "Enter") {
            e.preventDefault();
            const search = searchInput.value.trim();
            loadClients(search);
            clientList.classList.add("show");
        }
    });

    loadClients(); // Carga inicial

    // ------------------------ LÓGICA DE CREACIÓN DE CLIENTE ------------------------
    const formCliente            = document.getElementById("form-cliente");
    const modalFormularioElement = document.getElementById("modal_formulario");
    const modalExitoElement      = document.getElementById("cliente_creado");
    if (!formCliente || !modalFormularioElement || !modalExitoElement) return;

    const modalFormulario = (window.bootstrap && bootstrap.Modal) ? new bootstrap.Modal(modalFormularioElement) : null;
    const modalExito      = (window.bootstrap && bootstrap.Modal) ? new bootstrap.Modal(modalExitoElement) : null;

    formCliente.addEventListener("submit", function (event) {
        event.preventDefault();

        // Limpiar errores
        const errorTelefono = document.getElementById("error-telefono");
        const errorEmail    = document.getElementById("error-email");
        if (errorTelefono) { errorTelefono.style.display = "none"; errorTelefono.textContent = ""; }
        if (errorEmail)    { errorEmail.style.display    = "none"; errorEmail.textContent    = ""; }

        const nombre      = document.getElementById("nombre").value.trim();
        const apellido    = document.getElementById("apellido").value.trim();
        const telefono    = document.getElementById("telefono").value.trim();
        const email       = document.getElementById("email").value.trim();
        const comentarios = document.getElementById("comentarios").value.trim();
        const recibePromocion = document.getElementById("recibe_promocion").value;
        const categoriaId     = document.getElementById("categoria_id_modal").value; 


        if (!nombre || !apellido || !telefono) {
            alert("Nombre, apellido y teléfono son obligatorios.");
            return;
        }

        // Validar duplicados
        fetch("{{ route('clientes.check_unique') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                "Accept": "application/json"
            },
            body: JSON.stringify({ telefono: telefono, email: email, origen: 'remision'   })
        })
        .then(function (res) { return res.json(); })
        .then(function (data) {
            if (data.success) {
                // Guardar nuevo cliente
                fetch("{{ route('clientes.store') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "Accept": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        nombre: nombre,
                        apellido: apellido,
                        telefono: telefono,
                        email: email,
                        comentarios: comentarios,
                        recibe_promocion: recibePromocion,
                        categoria_id:      categoriaId,
                        origen: 'remision' ,
                        
                    })
                })
                .then(function (res) { return res.json(); })
                .then(function (data) {
                    if (data.success && data.cliente) {
                        if (modalFormulario) {
                            modalFormulario.hide();
                        }

                        modalFormularioElement.addEventListener("hidden.bs.modal", function () {
                            if (modalExito) {
                                modalExito.show();
                            }
                            loadClients(); // Recargar lista sin recargar página
                        }, { once: true });

                        formCliente.reset();

                        // Seleccionar automáticamente al cliente recién creado
                        selectClient(data.cliente);
                    } else {
                        alert(data.message || "Ocurrió un error al guardar el cliente.");
                    }
                })
                .catch(function (error) {
                    console.error("Error:", error);
                    alert("Error al guardar el cliente.");
                });
            } else {
                if (data.error_telefono && errorTelefono) {
                    errorTelefono.textContent = data.error_telefono;
                    errorTelefono.style.display = "block";
                }
                if (data.error_email && errorEmail) {
                    errorEmail.textContent = data.error_email;
                    errorEmail.style.display = "block";
                }
            }
        })
        .catch(function (error) {
            console.error("Error:", error);
            alert("Error al verificar la existencia del teléfono o correo.");
        });
    });

    modalExitoElement.addEventListener("hidden.bs.modal", function () {
        // Si quisieras, puedes recargar la página
        // location.reload();
    });
});
</script>

<script>
/**
 * =========================================================
 * ✅ TRADE-IN + TOTAL NETO
 * - Genera tradeins_json
 * - Calcula tradein_total y total_neto para mostrar
 * =========================================================
 */
(function () {
  window.tradeins = window.tradeins || [];

  function q(sel){ return document.querySelector(sel); }
  function qa(sel){ return document.querySelectorAll(sel); }

  function num(v){
    var n = parseFloat(String(v == null ? '' : v).replace(/[$,\s]/g,'')); 
    return isNaN(n) ? 0 : n;
  }

  function getTotalOriginal() {
    var hidden = q('#total_input');
    if (hidden) return num(hidden.value);

    var totalNode = q('#total');
    var txt = totalNode && totalNode.textContent ? totalNode.textContent : "0";
    return num(txt);
  }

  // 1) Agregar fila Trade-in
  function addTradeinRow(initial) {
    if (typeof initial === 'undefined') initial = {};
    var tbody = q('#tabla-tradeins tbody');
    if (!tbody) return;

    var tr = document.createElement('tr');

    // Soportar inicial desde back: valor_a_cuenta o valor
    var initValor = num(initial.valor_a_cuenta != null ? initial.valor_a_cuenta : initial.valor);

    tr.innerHTML =
      '<td>' +
      '  <input type="text" class="form-control ti-tipo"' +
      '         placeholder="Tipo de equipo (Ej: Laptop)"' +
      '         value="' + (initial.tipo_equipo || '') + '">' +
      '</td>' +
      '<td><input type="text" class="form-control ti-marca" placeholder="Marca"  value="' + (initial.marca || '') + '"></td>' +
      '<td><input type="text" class="form-control ti-modelo"placeholder="Modelo" value="' + (initial.modelo || '') + '"></td>' +
      '<td><input type="text" class="form-control ti-serie" placeholder="Serie"  value="' + (initial.numero_serie || '') + '"></td>' +
      '<td>' +
      '  <input type="number" step="0.01" min="0" class="form-control ti-valor"' +
      '         placeholder="0.00" value="' + (initValor || '') + '">' +
      '</td>' +
      '<td>' +
      '  <button type="button" class="btn btn-sm btn-danger ti-remove">Eliminar</button>' +
      '</td>';

    tbody.appendChild(tr);

    var inputs = tr.querySelectorAll('input');
    Array.prototype.forEach.call(inputs, function (inp) {
      inp.addEventListener('input', function () {
        prepararTradeinsJSON();
        recalcularTradeinsYTotalNeto();
      });
    });

    var btnRemove = tr.querySelector('.ti-remove');
    if (btnRemove) {
      btnRemove.addEventListener('click', function () {
        tr.remove();
        prepararTradeinsJSON();
        recalcularTradeinsYTotalNeto();
      });
    }

    prepararTradeinsJSON();
    recalcularTradeinsYTotalNeto();
  }

  // 2) Generar JSON trade-ins
  function prepararTradeinsJSON() {
    var arr = [];

    qa('#tabla-tradeins tbody tr').forEach(function (tr) {
      var valor_a_cuenta = num(tr.querySelector('.ti-valor') ? tr.querySelector('.ti-valor').value : 0);

      arr.push({
        tipo_equipo:   tr.querySelector('.ti-tipo')  ? tr.querySelector('.ti-tipo').value  || '' : '',
        descripcion:   tr.querySelector('.ti-desc')  ? tr.querySelector('.ti-desc').value  || '' : '',
        marca:         tr.querySelector('.ti-marca') ? tr.querySelector('.ti-marca').value || '' : '',
        modelo:        tr.querySelector('.ti-modelo')? tr.querySelector('.ti-modelo').value|| '' : '',
        numero_serie:  tr.querySelector('.ti-serie') ? tr.querySelector('.ti-serie').value || '' : '',
        valor_a_cuenta: valor_a_cuenta,          // campo real para BD
        valor:          valor_a_cuenta,          // alias compat
        observaciones: tr.querySelector('.ti-obs') ? tr.querySelector('.ti-obs').value || '' : ''
      });
    });

    window.tradeins = arr;

    var h = q('#tradeins_json');
    if (h) h.value = JSON.stringify(arr);

    return arr;
  }

  // 3) Recalcular total neto
  function recalcularTradeinsYTotalNeto() {
    var totalOriginal = getTotalOriginal();

    var tradeTotal = (window.tradeins || []).reduce(function (acc, t) {
      return acc + num(t.valor_a_cuenta != null ? t.valor_a_cuenta : t.valor);
    }, 0);

    var neto = Math.max(0, totalOriginal - tradeTotal);

    var uiTrade = q('#tradein_total_ui');
    if (uiTrade) uiTrade.textContent = tradeTotal.toFixed(2);
    var uiNeto = q('#total_neto_ui');
    if (uiNeto) uiNeto.textContent = neto.toFixed(2);

    var inTrade = q('#tradein_total_input');
    if (inTrade) inTrade.value = tradeTotal.toFixed(2);
    var inNeto = q('#total_neto_input');
    if (inNeto) inNeto.value = neto.toFixed(2);

    return { totalOriginal: totalOriginal, tradeTotal: tradeTotal, neto: neto };
  }

  // 4) Enganche suave a tu total actual (envolvemos actualizarTotal una sola vez)
  if (typeof window.actualizarTotal === 'function' && !window.__tradeinWrapped) {
    var originalActualizarTotal = window.actualizarTotal;
    window.actualizarTotal = function () {
      var r = originalActualizarTotal.apply(this, arguments);
      prepararTradeinsJSON();
      recalcularTradeinsYTotalNeto();
      return r;
    };
    window.__tradeinWrapped = true;
  }

  document.addEventListener('DOMContentLoaded', function () {
    var btnAdd = q('#btn-add-tradein');
    if (btnAdd) {
      btnAdd.addEventListener('click', function () {
        addTradeinRow();
      });
    }

    var totalNode = q('#total');
    if (totalNode && typeof MutationObserver !== 'undefined') {
      var obs = new MutationObserver(function () {
        prepararTradeinsJSON();
        recalcularTradeinsYTotalNeto();
      });
      obs.observe(totalNode, { childList: true, characterData: true, subtree: true });
    }

    prepararTradeinsJSON();
    recalcularTradeinsYTotalNeto();
  });

  document.addEventListener('DOMContentLoaded', function () {
    var formVenta = q('#form-venta');
    if (!formVenta) return;

    formVenta.addEventListener('submit', function () {
      prepararTradeinsJSON();
      recalcularTradeinsYTotalNeto();
    });
  });

  window.addTradeinRow                = addTradeinRow;
  window.prepararTradeinsJSON         = prepararTradeinsJSON;
  window.recalcularTradeinsYTotalNeto = recalcularTradeinsYTotalNeto;

})();
</script>
@endsection