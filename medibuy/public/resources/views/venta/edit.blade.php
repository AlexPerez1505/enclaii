@extends('layouts.app')
@section('title', 'Editar Remisión')
@section('titulo', 'Editar Remisión')

@section('content')

{{-- Si tu layout NO tiene meta csrf, deja esto --}}
<meta name="csrf-token" content="{{ csrf_token() }}">

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="{{ asset('css/ventascreate.css') }}?v={{ time() }}">

{{-- SweetAlert2 --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@php
  $subInit  = (float) $venta->subtotal;
  $descInit = (float) $venta->descuento;
  $envInit  = (float) $venta->envio;
  $ivaInit  = (float) ($venta->iva ?? 0);
  $totInit  = (float) $venta->total;

  // Anticipo (si existen columnas; si no existen, quedará vacío)
  $anticipoMonto  = $venta->anticipo_monto ?? null;
  $anticipoFecha  = $venta->anticipo_fecha ?? null;
  $anticipoMetodo = $venta->anticipo_metodo ?? null;

  // Tradeins iniciales (intenta relación, si no existe usa campo json)
  $tradeinsInitArr = [];
  if (isset($venta->tradeins) && $venta->tradeins) {
    $tradeinsInitArr = collect($venta->tradeins)->map(function($t){
      return [
        'tipo_equipo'   => $t->tipo_equipo ?? '',
        'marca'         => $t->marca ?? '',
        'modelo'        => $t->modelo ?? '',
        'numero_serie'  => $t->numero_serie ?? '',
        'valor_a_cuenta'=> (float)($t->valor_a_cuenta ?? $t->valor ?? 0),
      ];
    })->values()->all();
  } else {
    // si tienes columna tradeins_json o algo similar
    $raw = $venta->tradeins_json ?? $venta->tradeins ?? null;
    if (is_string($raw) && trim($raw) !== '') {
      try { $tradeinsInitArr = json_decode($raw, true) ?: []; } catch (\Throwable $e) { $tradeinsInitArr = []; }
    }
  }

  // Pagos existentes (si tienes relación pagoFinanciamiento)
  $pagos = $venta->pagoFinanciamiento ?? collect();
  $pagosArr = $pagos->map(function($p){
    return [
      'descripcion' => $p->descripcion,
      'mes' => \Carbon\Carbon::parse($p->fecha_pago)->format('Y-m-d'),
      'cuota' => number_format((float)$p->monto, 2, '.', '')
    ];
  })->values();
@endphp

<div class="container">
  <form id="form-venta" method="POST" action="{{ route('ventas.update', $venta->id) }}">
    @csrf
    @method('PUT')

    <div class="row">
      {{-- =================== Col izquierda =================== --}}
      <div class="col-md-3 mt-3">

        {{-- Cliente --}}
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
                value="{{ $venta->cliente ? ($venta->cliente->nombre.' '.$venta->cliente->apellido) : '' }}"
              >
              <ul class="dropdown-menu modern-dropdown w-100" id="client-list">
                <li>
                  <button type="button" class="dropdown-item modern-dropdown-item"
                    onclick='selectClient({id:1,nombre:"PÚBLICO EN GENERAL",apellido:"",telefono:"",email:"",comentarios:""})'>
                    PÚBLICO EN GENERAL
                  </button>
                </li>
                <li>
                  <button type="button" class="dropdown-item modern-dropdown-item" onclick="openCreateClientModal()">
                    + Crear nuevo cliente
                  </button>
                </li>
                {{-- clientes dinámicos por JS --}}
              </ul>
            </div>
            <input type="hidden" name="cliente_id" id="cliente_id" value="{{ $venta->cliente_id }}">
          </div>

          <div id="client-details" class="mt-3">
            @if($venta->cliente)
              <p><strong>Nombre:</strong> {{ strtoupper($venta->cliente->nombre.' '.$venta->cliente->apellido) }}</p>
              <p><strong>Teléfono:</strong> {{ $venta->cliente->telefono ?? 'No registrado' }}</p>
              <p><strong>Email:</strong> {{ $venta->cliente->email ?? 'No registrado' }}</p>
              <p><strong>Dirección:</strong> {{ $venta->cliente->comentarios ?? 'No registrado' }}</p>
            @endif
          </div>
        </div>

        {{-- Lugar --}}
        <div class="card modern-card mb-3">
          <div class="card-header modern-header">Lugar de la Cotización</div>
          <div class="card-body">
            <select name="lugar" id="lugarCotizacion" class="form-control modern-select" required>
              <option value="">Selecciona un lugar...</option>
              @php
                $lugares = [
                  "AMCG ECOS INTERNACIONAL DE CIRUGIA GENERAL",
                  "AMCG CONGRESO INTERNACIONAL DE CIRUGIA GENERAL",
                  "AMCE CONGRESO INTERNACIONAL DE CIRUGIA ENDOSCOPICA",
                  "AMECRA XXIX CONGRESO INTERNACIONAL DE ASOCIACIÓN MEX DE CIRUGIA RECONS, ARTICULAR Y ARTROSCOPICA",
                  "CVDL CONGRESO DE VETERINARIA",
                  "AMG ECOS INTERNACIONALES DE GASTROENTEROLOGIA",
                  "AMG SEMANA NACIONAL GASTRO",
                  "otro"
                ];
              @endphp
              @foreach($lugares as $l)
                <option value="{{ $l }}" {{ $venta->lugar === $l ? 'selected' : '' }}>{{ $l }}</option>
              @endforeach
            </select>
          </div>
        </div>

        {{-- Nota --}}
        <div class="card modern-card mb-3">
          <div class="card-header modern-header">Nota al Cliente</div>
          <div class="card-body">
            <textarea name="nota" id="notaCliente" class="form-control modern-textarea" rows="4"
              placeholder="Escribe una nota...">{{ $venta->nota }}</textarea>
          </div>
        </div>

        {{-- Registrado por --}}
        <div class="card modern-card">
          <div class="card-header modern-header">Registrado por</div>
          <div class="card-body">
            @auth
              <input type="text" class="form-control modern-textarea" value="{{ Auth::user()->name }}" readonly>
            @else
              <input type="text" class="form-control modern-textarea" value="Desconocido" readonly>
            @endauth
          </div>
        </div>
      </div>

      {{-- =================== Col derecha =================== --}}
      <div class="col-md-9">

        {{-- Productos: buscador --}}
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

                {{-- fallback inicial (si el fetch tarda, sigues viendo lista) --}}
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
                        {{ (float)$producto->precio }},
                        @json($producto->imagen)
                      )"
                    >
                      <img src="/storage/{{ $producto->imagen }}" alt="{{ $producto->tipo_equipo }}"
                        class="modern-product-img me-2" style="width:40px;height:40px;object-fit:cover;border-radius:6px;">
                      <div class="flex-grow-1 modern-product-info">
                        <strong>{{ strtoupper($producto->tipo_equipo) }}</strong> - {{ strtoupper($producto->modelo) }} {{ strtoupper($producto->marca) }}<br>
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

        {{-- Productos seleccionados (tabla) --}}
        <div class="card modern-card mt-3">
          <div class="card-header modern-header">Productos Seleccionados</div>
          <div class="card-body">
            <div class="table-responsive">
              <input type="hidden" name="productos_json" id="productos_json">
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
                    <th>Acción</th>
                  </tr>
                </thead>
                <tbody>
                  {{-- Precarga rows desde $venta->productos --}}
                  @foreach($venta->productos as $vp)
                    @php
                      $img = $vp->producto->imagen ? '/storage/'.$vp->producto->imagen : 'https://via.placeholder.com/80.png?text=IMG';
                      $precioU = (float) $vp->precio_unitario;
                      $sobre = (float) $vp->sobreprecio;
                      $sub = (float) $vp->subtotal;
                      $cant = (int) $vp->cantidad;

                      $seriesIds = [];
                      if (isset($vp->series) && is_array($vp->series)) {
                        $seriesIds = $vp->series;
                      } elseif (!empty($vp->registro_id)) {
                        $seriesIds = is_array($vp->registro_id) ? $vp->registro_id : [$vp->registro_id];
                      }
                    @endphp
                    <tr data-id="{{ $vp->producto_id }}" data-precio="{{ number_format($precioU,2,'.','') }}">
                      <td><img src="{{ $img }}" width="50" alt="Imagen"></td>
                      <td class="equipo">{{ $vp->producto->tipo_equipo }}</td>
                      <td>{{ $vp->producto->modelo }}</td>
                      <td>{{ $vp->producto->marca }}</td>
                      <td>
                        <input type="number" class="form-control cantidad" value="{{ $cant }}" min="1" onchange="actualizarSubtotal(this)">
                      </td>
                      <td class="subtotal">{{ number_format($sub,2,'.','') }}</td>
                      <td>
                        <input type="number" class="form-control sobreprecio" value="{{ number_format($sobre,2,'.','') }}" min="0" step="0.01" onchange="actualizarSubtotal(this)">
                      </td>
                      <td>
                        <div class="serie-container">
                          @for($i=0; $i<$cant; $i++)
                            @php
                              $serieVal = $seriesIds[$i] ?? null;
                              $serieTexto = null;
                              if ($serieVal) {
                                $regTmp = \App\Models\Registro::find($serieVal);
                                $serieTexto = $regTmp?->numero_serie;
                              }
                            @endphp
                            <div class="dropdown-search d-flex align-items-center gap-1 mb-1" style="position: relative; width: 240px;">
                              <input type="text" class="form-control search-input" placeholder="Buscar número de serie..." autocomplete="off" value="{{ $serieTexto ?? '' }}">
                              <input type="hidden" class="registro-id-hidden" value="{{ $serieVal }}">
                              <div class="dropdown-list" style="position:absolute;top:100%;left:0;right:0;background:#fff;border:1px solid #ccc;max-height:150px;overflow-y:auto;display:none;z-index:1000;border-radius:4px;"></div>
                              <button type="button" class="btn btn-outline-info btn-sm ver-registro" title="Ver detalles" style="height: 38px; margin-left: 5px;">
                                <i class="bi bi-eye"></i>
                              </button>
                            </div>
                          @endfor
                        </div>
                      </td>
                      <td>
                        <button type="button" class="btn btn-sm btn-danger eliminar-fila"><i class="bi bi-trash"></i></button>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
        </div>

        {{-- =========================================
             ✅ TRADE-IN (Equipos a cuenta)
             ========================================= --}}
        <div class="card modern-card mt-3">
          <div class="card-header modern-header d-flex align-items-center justify-content-between">
            <span>Equipos a cuenta (Trade-in)</span>
            <button type="button" class="btn btn-sm tradein-pill-btn" id="btn-add-tradein">+ Agregar equipo</button>
          </div>
          <div class="card-body">
            <p class="text-muted mb-2" style="font-size:13px;">
              Agrega equipos que te dejan como parte de pago. Su valor se descuenta del total.
            </p>

            <input type="hidden" name="tradeins_json" id="tradeins_json" value='{{ old('tradeins_json', json_encode($tradeinsInitArr)) }}'>

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
                  {{-- filas por JS --}}
                </tbody>
              </table>
            </div>
          </div>
        </div>

        {{-- Resumen + financiamiento --}}
        <div class="d-flex flex-column flex-md-row">
          <div class="card modern-card mt-3 w-100 w-md-50">
            <div class="card-header modern-header">Resumen</div>
            <div class="card-body">

              <p>Subtotal: $<span id="subtotal" class="modern-value">{{ number_format($subInit,2,'.','') }}</span></p>
              <input type="hidden" name="subtotal" id="subtotal_input" value="{{ number_format($subInit,2,'.','') }}">

              <div class="form-group">
                <label>Descuento</label>
                <input type="number" name="descuento" id="descuento"
                  value="{{ number_format($descInit,2,'.','') }}"
                  class="form-control modern-input w-25 d-inline-block" step="0.01">
              </div>

              <br>

              <div class="form-group">
                <label>Envío</label>
                <input type="number" name="envio" id="envio"
                  value="{{ number_format($envInit,2,'.','') }}"
                  class="form-control modern-input w-25 d-inline-block" step="0.01">
              </div>

              <div class="form-check mt-2">
                <input type="checkbox" class="form-check-input" id="aplica_iva" {{ $ivaInit > 0 ? 'checked' : '' }}>
                <label class="form-check-label">Aplicar IVA (16%)</label>
              </div>

              <input type="hidden" name="iva" id="iva_input" value="{{ number_format($ivaInit,2,'.','') }}">
              <p>IVA: $<span id="iva">{{ number_format($ivaInit,2,'.','') }}</span></p>

              {{-- ✅ ANTICIPO --}}
              <div class="form-group mt-3">
                <label style="font-weight:700;">Anticipo</label>
                <div class="d-flex gap-2 flex-wrap mt-1">
                  <input type="number" step="0.01" min="0"
                         id="anticipo_monto" name="anticipo_monto"
                         class="form-control modern-input" style="max-width:160px;"
                         placeholder="Monto"
                         value="{{ old('anticipo_monto', $anticipoMonto) }}">
                  <input type="date"
                         id="anticipo_fecha" name="anticipo_fecha"
                         class="form-control modern-input" style="max-width:180px;"
                         value="{{ old('anticipo_fecha', $anticipoFecha) }}">
                  <select id="anticipo_metodo" name="anticipo_metodo"
                          class="form-control modern-input" style="max-width:220px;">
                    <option value="">Método de pago</option>
                    @php $m = old('anticipo_metodo', $anticipoMetodo); @endphp
                    <option value="efectivo"        {{ $m==='efectivo'?'selected':'' }}>Efectivo</option>
                    <option value="transferencia"   {{ $m==='transferencia'?'selected':'' }}>Transferencia</option>
                    <option value="tarjeta_credito" {{ $m==='tarjeta_credito'?'selected':'' }}>Tarjeta crédito</option>
                    <option value="tarjeta_debito"  {{ $m==='tarjeta_debito'?'selected':'' }}>Tarjeta débito</option>
                    <option value="cheque"          {{ $m==='cheque'?'selected':'' }}>Cheque</option>
                    <option value="otro"            {{ $m==='otro'?'selected':'' }}>Otro</option>
                  </select>
                </div>
                <small class="text-muted">
                  Si capturas anticipo se registra como pago real aprobado y se descuenta del total a financiar.
                </small>
              </div>

              <hr>

              {{-- Total ORIGINAL --}}
              <p><strong>Total: $<span id="total">{{ number_format($totInit,2,'.','') }}</span></strong></p>

              <input type="hidden" name="total" id="total_input" value="{{ number_format($totInit,2,'.','') }}">
              <input type="hidden" name="total_original" id="total_original_input" value="{{ number_format($totInit,2,'.','') }}">

              {{-- ✅ TRADE-IN y neto --}}
              <p>Valor a cuenta: $<span id="tradein_total_ui" class="modern-value">0.00</span></p>
              <p style="font-weight:800;">Total neto: $<span id="total_neto_ui" class="modern-value">0.00</span></p>

              <input type="hidden" name="tradein_total" id="tradein_total_input" value="{{ old('tradein_total', 0) }}">
              <input type="hidden" name="total_neto" id="total_neto_input" value="{{ old('total_neto', 0) }}">

              <hr>

              <div class="form-group">
                <label for="tipoPago">Selecciona Plan:</label>
                <select id="tipoPago" name="plan" class="form-control modern-input w-50" required>
                  <option value="" disabled {{ $venta->plan ? '' : 'selected' }}>Selecciona un plan</option>
                  <option value="contado"       {{ $venta->plan==='contado'?'selected':'' }}>Pago de Contado</option>
                  <option value="personalizado" {{ $venta->plan==='personalizado'?'selected':'' }}>Plan Personalizado</option>
                  <option value="estatico"      {{ $venta->plan==='estatico'?'selected':'' }}>Plan Fijo</option>
                  <option value="dinamico"      {{ $venta->plan==='dinamico'?'selected':'' }}>Plan Flexible</option>
                  <option value="credito"       {{ $venta->plan==='credito'?'selected':'' }}>Plan a Crédito</option>
                </select>
              </div>

              <div id="opcionesDinamicas" style="display:none; margin-top:1rem;">
                <label for="pagoInicial">Pago Inicial:</label>
                <input type="number" id="pagoInicial" class="form-control modern-input w-50" min="0" step="0.01">
              </div>

              <div id="opcionesCredito" style="display:none; margin-top:1rem;">
                <label for="pagoCreditoInicial">Pago Inicial:</label>
                <input type="number" id="pagoCreditoInicial" class="form-control modern-input w-50" min="0" step="0.01">
                <label for="plazoCredito" style="margin-top:0.5rem;">Plazo (meses):</label>
                <input type="number" id="plazoCredito" class="form-control modern-input w-50" value="6" min="1" step="1">
              </div>

              <div id="opcionesPersonalizado" style="display:none; margin-top:1rem;">
                <label for="mesesPersonalizado">Selecciona el número de meses:</label>
                <input type="number" id="mesesPersonalizado" class="form-control modern-input w-50" min="1" step="1" value="1">
                <div id="listaPagosPersonalizados" class="mt-3"></div>
              </div>

              {{-- ✅ pagos_json que espera el backend --}}
              <input type="hidden" id="pagosJsonInput" name="pagos_json" value="{{ old('pagos_json', $pagosArr->toJson()) }}">

              {{-- Garantía --}}
              <div class="form-group mt-4">
                <label for="meses_garantia">Meses de Garantía:</label>
                <select name="meses_garantia" id="meses_garantia" class="form-control modern-input w-50" required>
                  <option value="" disabled>Selecciona meses de garantía</option>
                  @foreach([6,9,12,15,18] as $mm)
                    <option value="{{ $mm }}" {{ (int)$venta->meses_garantia === $mm ? 'selected' : '' }}>{{ $mm }} meses</option>
                  @endforeach
                </select>
              </div>

              {{-- Carta garantía buscador --}}
              <br>
              <div class="form-group position-relative mt-4" style="max-width: 900px; width: 100%;">
                <label for="carta_garantia_search">Carta de Garantía a incluir en el PDF:</label>
                <input type="text" id="carta_garantia_search" class="form-control"
                  placeholder="Escribe para buscar..." autocomplete="off"
                  value="{{ $venta->cartaGarantia->nombre ?? '' }}">
                <input type="hidden" name="carta_garantia_id" id="carta_garantia_id" value="{{ $venta->carta_garantia_id }}">
                <ul id="dropdown_cartas" class="list-group position-absolute w-100 mt-1 shadow-sm"
                  style="z-index:1000;display:none;max-height:250px;overflow-y:auto;">
                  @foreach ($cartas->sortBy('nombre') as $carta)
                    <li class="list-group-item list-option-carta" data-id="{{ $carta->id }}">{{ strtoupper($carta->nombre) }}</li>
                  @endforeach
                </ul>
              </div>

              <br>

              <button type="submit" class="btn btn-success primary-pill-btn" id="btn-guardar">
                <span class="btn-label">Guardar</span>
                <span class="btn-loader" aria-hidden="true"></span>
              </button>
            </div>
          </div>

          {{-- Detalles del Financiamiento --}}
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
     MODAL: CREAR CLIENTE (igual que create)
     ============================================================ --}}
<form id="form-cliente" method="POST" action="{{ route('clientes.store') }}">
  @csrf
  <div class="modal fade" id="modal_formulario" tabindex="-1" role="dialog" aria-labelledby="FormularioLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="createClientModalLabel">Registrar Cliente</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body">
          <div class="row mb-3">
            <div class="col-md-6">
              <label for="nombre" class="form-label">Nombre</label>
              <input type="text" class="form-control text-uppercase" id="nombre" name="nombre" placeholder="Ingresar nombre" required>
            </div>
            <div class="col-md-6">
              <label for="apellido" class="form-label">Apellido</label>
              <input type="text" class="form-control text-uppercase" id="apellido" name="apellido" placeholder="Ingresar apellido" required>
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-6">
              <label for="telefono" class="form-label">Teléfono</label>
              <input type="tel" class="form-control" id="telefono" name="telefono"
                     placeholder="10 dígitos" required inputmode="numeric"
                     pattern="^\d{10}$" maxlength="10" minlength="10">
              <small id="help-telefono" class="text-muted">Debe contener exactamente 10 números.</small><br>
              <span id="error-telefono" class="text-danger" style="display:none;">El teléfono ya está registrado.</span>
            </div>

            <div class="col-md-6">
              <label for="email" class="form-label">Email</label>
              <input type="email" class="form-control" id="email" name="email" placeholder="Ingresar email">
              <span id="error-email" class="text-danger" style="display:none;">El correo ya está registrado.</span>
            </div>
          </div>

          <div class="mb-3">
            <label for="comentarios" class="form-label">Dirección</label>
            <textarea id="comentarios" name="comentarios" class="form-control text-uppercase"
              placeholder="Agrega información de tu cliente"></textarea>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button id="btn-submit" type="submit" class="btn btn-primary">Agregar</button>
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
     MODAL: CREAR PRODUCTO (igual que create)
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
{{-- ============================================================
     SCRIPTS
     ============================================================ --}}

<script>
// =========================
// Evitar submit por Enter (excepto textarea)
// =========================
document.addEventListener('DOMContentLoaded', function () {
  $('#form-venta').on('keydown', function(e){
    if (e.key === 'Enter' && !$(e.target).is('textarea')) e.preventDefault();
  });
});
</script>

<script>
// =========================
// Crear producto vía modal (AJAX)
// =========================
document.addEventListener('DOMContentLoaded', function () {
  const formProducto = document.getElementById("formProducto");
  if (!formProducto) return;

  formProducto.addEventListener("submit", function (event) {
    event.preventDefault();

    const form = this;
    const formData = new FormData(form);

    fetch("{{ route('productos.store') }}", {
      method: "POST",
      body: formData,
      headers: {
        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
        "Accept": "application/json"
      }
    })
    .then(function (response) {
      return response.json().catch(()=>({})).then(function(data){
        if (!response.ok) throw data;
        return data;
      });
    })
    .then(function (data) {
      Swal.fire({
        toast:true, icon:'success',
        title: data.message || 'Producto creado',
        position:'top-end', showConfirmButton:false, timer:2500, timerProgressBar:true
      });

      form.reset();

      const previewIcon = document.getElementById('preview-icon');
      const previewText = document.getElementById('preview-text');
      if (previewIcon && previewText) {
        previewIcon.src = "https://cdn-icons-png.flaticon.com/512/1829/1829586.png";
        previewIcon.style.width = '';
        previewIcon.style.height = '';
        previewText.style.display = 'inline';
      }

      const modalEl = document.getElementById('modal1');
      if (modalEl && window.bootstrap?.Modal) {
        const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
        modal.hide();
      }

      // Refrescar dropdown productos/paquetes
      if (typeof window.__restaurarListaOriginal === 'function') window.__restaurarListaOriginal();
    })
    .catch(function (error) {
      console.error("Error:", error);
      Swal.fire({ icon:'error', title:'Error', text: (error && error.message) ? error.message : 'No se pudo crear el producto.' });
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
// Buscador carta garantía (con normalize)
// =========================
document.addEventListener("DOMContentLoaded", function () {
  const input       = document.getElementById('carta_garantia_search');
  const hiddenInput = document.getElementById('carta_garantia_id');
  const dropdown    = document.getElementById('dropdown_cartas');

  if (!input || !hiddenInput || !dropdown) return;

  const originalOptions = Array.prototype.slice.call(dropdown.querySelectorAll('.list-option-carta'));

  function normalize(str) {
    return (str || '').normalize("NFD").replace(/[\u0300-\u036f]/g, "").toLowerCase();
  }

  input.addEventListener('input', function () {
    const term = input.value.trim();
    const nterm = normalize(term);
    dropdown.innerHTML = '';
    let matches = 0;

    if (!nterm) {
      dropdown.style.display = 'none';
      hiddenInput.value = '';
      return;
    }

    originalOptions.forEach(function (option) {
      const txt = option.textContent || '';
      if (normalize(txt).includes(nterm)) {
        matches++;
        const li = document.createElement('li');
        li.classList.add('list-group-item', 'list-option-carta');
        li.setAttribute('data-id', option.getAttribute('data-id'));
        li.textContent = txt;
        li.addEventListener('click', function () {
          input.value = txt;
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
// Buscador de productos + paquetes (AJAX)
// =====================================================
document.addEventListener("DOMContentLoaded", function () {
  const buscarProducto    = document.getElementById("buscarProducto");
  const dropdownProductos = document.getElementById("dropdownProductos");
  if (!buscarProducto || !dropdownProductos) return;

  let controladorAbort = new AbortController();

  function mostrarResultados(paquetes, productos) {
    dropdownProductos.innerHTML = `
      <li>
        <button type="button" class="dropdown-item modern-dropdown-item" data-bs-toggle="modal" data-bs-target="#modal1">
          + Crear Producto
        </button>
      </li>
    `;

    if (paquetes && paquetes.length > 0) {
      paquetes.forEach(function (paquete) {
        var li = document.createElement("li");
        li.innerHTML = `
          <button type="button" class="dropdown-item modern-dropdown-item"
                  data-id="${paquete.id}"
                  data-productos='${JSON.stringify(paquete.productos || [])}'
                  onclick="agregarPaqueteDesdeData(this)">
            📦 ${(paquete.nombre || '').toUpperCase()} - Paquete
          </button>
        `;
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

        li.innerHTML = `
          <button type="button" class="dropdown-item modern-dropdown-item d-flex align-items-center"
                  onclick="agregarProductoDesdeDropdown(${producto.id}, '${tipo.replace(/'/g,"\\'")}', '${modelo.replace(/'/g,"\\'")}', '${marca.replace(/'/g,"\\'")}', ${precio}, '${img.replace(/'/g,"\\'")}')">
            <img src="/storage/${img}" alt="${tipo}" class="modern-product-img me-2"
                 style="width:40px;height:40px;object-fit:cover;border-radius:6px;">
            <div class="flex-grow-1 modern-product-info">
              <strong>${tipo.toUpperCase()}</strong> - ${modelo.toUpperCase()} ${marca.toUpperCase()}<br>
              <span class="text-muted modern-product-price">$${precio.toFixed(2)}</span>
            </div>
            <span class="badge modern-badge">${producto.stock || 0} unidades</span>
          </button>
        `;
        dropdownProductos.appendChild(li);
      });
    }

    if ((!paquetes || paquetes.length === 0) && (!productos || productos.length === 0)) {
      dropdownProductos.innerHTML += `<li><button type="button" class="dropdown-item text-muted" disabled>No se encontraron resultados</button></li>`;
    }
  }

  function restaurarListaOriginal() {
    Promise.all([
      fetch("{{ route('productos.search') }}?search=").then(res => res.json()),
      fetch("{{ route('paquetes.search') }}?search=").then(res => res.json())
    ])
    .then(([productos, paquetes]) => mostrarResultados(paquetes, productos))
    .catch(err => console.error("Error al restaurar lista:", err));
  }
  window.__restaurarListaOriginal = restaurarListaOriginal;

  function realizarBusqueda(searchQuery) {
    controladorAbort.abort();
    controladorAbort = new AbortController();

    if (searchQuery.length > 1) {
      Promise.all([
        fetch("{{ route('productos.search') }}?search=" + encodeURIComponent(searchQuery), { signal: controladorAbort.signal }).then(res => res.json()),
        fetch("{{ route('paquetes.search') }}?search=" + encodeURIComponent(searchQuery), { signal: controladorAbort.signal }).then(res => res.json())
      ])
      .then(([productos, paquetes]) => mostrarResultados(paquetes, productos))
      .catch(err => { if (err.name !== "AbortError") console.error("Error búsqueda:", err); });
    } else {
      restaurarListaOriginal();
    }
  }

  buscarProducto.addEventListener("input", function () {
    realizarBusqueda(this.value.trim());
  });

  buscarProducto.addEventListener("keydown", function (e) {
    if (e.key === "Enter") {
      e.preventDefault();
      realizarBusqueda(this.value.trim());
    }
  });

  buscarProducto.addEventListener("focus", function () {
    if (this.value.trim() === "") restaurarListaOriginal();
  });

  window.agregarPaqueteDesdeData = function (el) {
    var productos = [];
    try { productos = JSON.parse(el.getAttribute("data-productos") || '[]'); } catch(e){ productos = []; }
    (productos || []).forEach(function (p) {
      agregarProductoDesdeDropdown(p.id, p.tipo_equipo, p.modelo, p.marca, parseFloat(p.precio || 0), p.imagen);
    });
  };

  restaurarListaOriginal();
});
</script>

<script>
// =====================================================
// Productos: tabla, totales, IVA, series
// =====================================================
$(document).ready(function () {
  actualizarTotal();
  $(document).on('input change', '#aplica_iva, #envio, #descuento', function(){ actualizarTotal(); });
});

// Agregar producto a la tabla
function agregarProductoDesdeDropdown(id, nombre, modelo, marca, precio, imagen) {
  if (!id || !nombre) return;
  if ($('#tabla-productos tbody tr[data-id="' + id + '"]').length) {
    alert('Este producto ya ha sido agregado.');
    return;
  }
  var p = parseFloat(precio) || 0;

  var fila = '' +
    '<tr data-id="' + id + '" data-precio="' + p + '">' +
    '  <td><img src="/storage/' + imagen + '" width="50" alt="Imagen producto"></td>' +
    '  <td class="equipo">' + nombre + '</td>' +
    '  <td>' + (modelo || '') + '</td>' +
    '  <td>' + (marca || '') + '</td>' +
    '  <td><input type="number" class="form-control cantidad" value="1" min="1" onchange="actualizarSubtotal(this)"></td>' +
    '  <td class="subtotal">' + p.toFixed(2) + '</td>' +
    '  <td><input type="number" class="form-control sobreprecio" value="0" min="0" step="0.01" onchange="actualizarSubtotal(this)"></td>' +
    '  <td><div class="serie-container"></div></td>' +
    '  <td><button type="button" class="btn btn-sm btn-danger eliminar-fila"><i class="bi bi-trash"></i></button></td>' +
    '</tr>';

  $('#tabla-productos tbody').append(fila);
  var nueva = $('#tabla-productos tbody tr').last();
  generarSelects(nueva, 1);
  actualizarTotal();
}

// Eliminar fila
$(document).on('click', '.eliminar-fila', function () {
  $(this).closest('tr').remove();
  actualizarTotal();
});

// Actualizar subtotal + selects cuando cambia cantidad o sobreprecio
$(document).on('input change', '.cantidad, .sobreprecio', function () {
  actualizarSubtotal(this);
});

function actualizarSubtotal(el) {
  var tr   = $(el).closest('tr');
  var qty  = Math.max(1, parseInt(tr.find('.cantidad').val(), 10) || 1);
  var base = parseFloat(tr.data('precio')) || 0;
  var extra= parseFloat(tr.find('.sobreprecio').val()) || 0;
  var sub  = (base + extra) * qty;
  tr.find('.subtotal').text(sub.toFixed(2));
  generarSelects(tr, qty);
  actualizarTotal();
}

// Recalcula totales (IVA incluye envío)
function actualizarTotal() {
  var subtotal = 0;
  $('#tabla-productos tbody tr').each(function () {
    subtotal += parseFloat($(this).find('.subtotal').text()) || 0;
  });

  var desc  = parseFloat($('#descuento').val()) || 0;
  var envio = parseFloat($('#envio').val()) || 0;

  var ivaBase = Math.max(0, subtotal - desc + envio);
  var iva     = $('#aplica_iva').is(':checked') ? (ivaBase * 0.16) : 0;
  var total   = ivaBase + iva;

  $('#subtotal').text(subtotal.toFixed(2));
  $('#subtotal_input').val(subtotal.toFixed(2));

  $('#iva').text(iva.toFixed(2));
  $('#iva_input').val(iva.toFixed(2));

  $('#total').text(total.toFixed(2));
  $('#total_input').val(total.toFixed(2));
  $('#total_original_input').val(total.toFixed(2));

  if (typeof CustomEvent !== 'undefined') {
    window.dispatchEvent(new CustomEvent('total:changed', { detail: { total: total } }));
  }

  return total;
}

// Genera inputs con buscador dinámico para número de serie (preserva selecciones previas)
function generarSelects(tr, n) {
  var cont = tr.find('.serie-container');
  var prev = cont.find('.dropdown-search').map(function(){
    return {
      text: $(this).find('.search-input').val() || '',
      id: $(this).find('.registro-id-hidden').val() || ''
    };
  }).get();

  cont.empty();
  for (var i = 0; i < n; i++) {
    var sel = prev[i] || { text:'', id:'' };
    cont.append('' +
      '<div class="dropdown-search d-flex align-items-center gap-1 mb-1" style="position: relative; width: 240px;">' +
      '  <input type="text" class="form-control search-input" placeholder="Buscar número de serie..." autocomplete="off" value="'+(sel.text||'')+'" />' +
      '  <input type="hidden" class="registro-id-hidden" value="'+(sel.id||'')+'" />' +
      '  <div class="dropdown-list" style="position:absolute;top:100%;left:0;right:0;background:white;border:1px solid #ccc;max-height:150px;overflow-y:auto;display:none;z-index:1000;border-radius:4px;"></div>' +
      '  <button type="button" class="btn btn-outline-info btn-sm ver-registro" title="Ver detalles" style="height: 38px; margin-left: 5px;">' +
      '    <i class="bi bi-eye"></i>' +
      '  </button>' +
      '</div>'
    );
  }
}

// Buscar registros disponibles
$(document).on('input', '.search-input', function () {
  var input    = $(this);
  var query    = (input.val() || '').toLowerCase();
  var dropdown = input.siblings('.dropdown-list');

  if (query.length < 1) { dropdown.hide(); return; }

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
      customClass: { popup:'rounded-4 shadow', confirmButton:'btn btn-primary' },
      buttonsStyling:false,
      width:500
    });
  }).fail(function () {
    Swal.fire('Error', 'No se pudo cargar la información.', 'error');
  });
});

// JSON productos para backend
function prepararProductosJSON() {
  var arr = [];
  $('#tabla-productos tbody tr').each(function () {
    var tr  = $(this);
    var pid = tr.data('id');
    var qty = Math.max(1, parseInt(tr.find('.cantidad').val(), 10) || 1);
    var pu  = parseFloat(tr.data('precio')) || 0;
    var sp  = parseFloat(tr.find('.sobreprecio').val()) || 0;
    var st  = parseFloat(tr.find('.subtotal').text()) || 0;

    var regs = tr.find('.registro-id-hidden').map(function () {
      return $(this).val() || null;
    }).get();

    arr.push({
      producto_id: pid,
      cantidad: qty,
      precio_unitario: pu,
      sobreprecio: sp,
      subtotal: st,
      registro_id: regs
    });
  });
  $('#productos_json').val(JSON.stringify(arr));
}
</script>

<script>
/**
 * =========================================================
 * ✅ TRADE-IN + TOTAL NETO (precarga + cálculo)
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
    return num(totalNode ? totalNode.textContent : '0');
  }

  function addTradeinRow(initial) {
    initial = initial || {};
    var tbody = q('#tabla-tradeins tbody');
    if (!tbody) return;

    var tr = document.createElement('tr');
    var initValor = num(initial.valor_a_cuenta != null ? initial.valor_a_cuenta : initial.valor);

    tr.innerHTML =
      '<td><input type="text" class="form-control ti-tipo" placeholder="Tipo de equipo" value="' + (initial.tipo_equipo || '') + '"></td>' +
      '<td><input type="text" class="form-control ti-marca" placeholder="Marca" value="' + (initial.marca || '') + '"></td>' +
      '<td><input type="text" class="form-control ti-modelo" placeholder="Modelo" value="' + (initial.modelo || '') + '"></td>' +
      '<td><input type="text" class="form-control ti-serie" placeholder="Serie" value="' + (initial.numero_serie || '') + '"></td>' +
      '<td><input type="number" step="0.01" min="0" class="form-control ti-valor" placeholder="0.00" value="' + (initValor || '') + '"></td>' +
      '<td><button type="button" class="btn btn-sm btn-danger ti-remove">Eliminar</button></td>';

    tbody.appendChild(tr);

    tr.querySelectorAll('input').forEach(function(inp){
      inp.addEventListener('input', function(){
        prepararTradeinsJSON();
        recalcularTradeinsYTotalNeto();
        window.dispatchEvent(new CustomEvent('total:changed', { detail:{ total: getTotalOriginal() } }));
      });
    });

    tr.querySelector('.ti-remove').addEventListener('click', function(){
      tr.remove();
      prepararTradeinsJSON();
      recalcularTradeinsYTotalNeto();
      window.dispatchEvent(new CustomEvent('total:changed', { detail:{ total: getTotalOriginal() } }));
    });

    prepararTradeinsJSON();
    recalcularTradeinsYTotalNeto();
  }

  function prepararTradeinsJSON() {
    var arr = [];
    qa('#tabla-tradeins tbody tr').forEach(function(tr){
      var valorInput = tr.querySelector('.ti-valor');
      var valor = num(valorInput ? valorInput.value : 0);
      var tipo  = tr.querySelector('.ti-tipo');
      var marca = tr.querySelector('.ti-marca');
      var modelo= tr.querySelector('.ti-modelo');
      var serie = tr.querySelector('.ti-serie');

      arr.push({
        tipo_equipo:  tipo  ? tipo.value   : '',
        marca:        marca ? marca.value  : '',
        modelo:       modelo? modelo.value : '',
        numero_serie: serie ? serie.value  : '',
        valor_a_cuenta: valor,
        valor: valor
      });
    });

    window.tradeins = arr;
    var h = q('#tradeins_json');
    if (h) h.value = JSON.stringify(arr);
    return arr;
  }

  function recalcularTradeinsYTotalNeto() {
    var totalOriginal = getTotalOriginal();
    var tradeTotal = (window.tradeins || []).reduce(function(acc, t){
      return acc + num(t.valor_a_cuenta != null ? t.valor_a_cuenta : t.valor);
    }, 0);

    var neto = Math.max(0, totalOriginal - tradeTotal);

    var uiTrade = q('#tradein_total_ui');
    var uiNeto  = q('#total_neto_ui');
    var inTrade = q('#tradein_total_input');
    var inNeto  = q('#total_neto_input');

    if (uiTrade) uiTrade.textContent = tradeTotal.toFixed(2);
    if (uiNeto)  uiNeto.textContent  = neto.toFixed(2);
    if (inTrade) inTrade.value = tradeTotal.toFixed(2);
    if (inNeto)  inNeto.value  = neto.toFixed(2);

    return { totalOriginal: totalOriginal, tradeTotal: tradeTotal, neto: neto };
  }

  if (typeof window.actualizarTotal === 'function' && !window.__tradeinWrapped) {
    var original = window.actualizarTotal;
    window.actualizarTotal = function () {
      var r = original.apply(this, arguments);
      prepararTradeinsJSON();
      recalcularTradeinsYTotalNeto();
      return r;
    };
    window.__tradeinWrapped = true;
  }

  document.addEventListener('DOMContentLoaded', function(){
    var btn = q('#btn-add-tradein');
    if (btn) btn.addEventListener('click', function(){ addTradeinRow(); });

    var initRaw = q('#tradeins_json') ? q('#tradeins_json').value : '[]';
    var init = [];
    try { init = JSON.parse(initRaw || '[]') || []; } catch(e){ init = []; }

    if (Array.isArray(init) && init.length) {
      init.forEach(function(t){ addTradeinRow(t); });
    } else {
      prepararTradeinsJSON();
      recalcularTradeinsYTotalNeto();
    }

    var totalNode = q('#total');
    if (totalNode && typeof MutationObserver !== 'undefined') {
      var obs = new MutationObserver(function(){
        prepararTradeinsJSON();
        recalcularTradeinsYTotalNeto();
      });
      obs.observe(totalNode, { childList:true, characterData:true, subtree:true });
    }
  });

  window.addTradeinRow = addTradeinRow;
  window.prepararTradeinsJSON = prepararTradeinsJSON;
  window.recalcularTradeinsYTotalNeto = recalcularTradeinsYTotalNeto;
})();
</script>

<script>
// =====================================================
// PLAN DE PAGOS (edición + nuevo)
//  - Respeta fechas guardadas en BD
//  - Plan personalizado con lista de pagos
//  - Pagos nuevos en secuencia (última fecha + 1 mes)
//  - Envío de pagos actualizados al backend (window.pagos)
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

  // === Datos server ===
  const pagosIniciales      = @json($pagosArr);
  const tienePagosIniciales = Array.isArray(pagosIniciales) && pagosIniciales.length > 0;
  const fechaBaseVentaISO   = "{{ \Carbon\Carbon::parse($venta->created_at)->format('Y-m-d') }}";

  window.pagos = [];
  const nombresPagos = ["Primer","Segundo","Tercer","Cuarto","Quinto","Sexto","Séptimo","Octavo","Noveno","Décimo","Undécimo","Duodécimo"];

  // ===== Helper para comparar montos sin falsos negativos =====
  function cantidadesCoinciden(a, b) {
    const ra = Math.round((a + Number.EPSILON) * 100);
    const rb = Math.round((b + Number.EPSILON) * 100);
    return ra === rb;
  }

  // === Helpers de fecha / monto ===
  function isoToDate(iso) {
    if (!iso) return new Date();
    const parts = String(iso).split('-');
    if (parts.length !== 3) return new Date();
    const y = parseInt(parts[0],10);
    const m = parseInt(parts[1],10)-1;
    const d = parseInt(parts[2],10);
    const dt = new Date(y,m,d);
    if (isNaN(dt.getTime())) return new Date();
    return dt;
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
    if (d.getDate() < originalDay) {
      d.setDate(0);
    }
    return formatoFechaISO(d);
  }

  function formatear(moneda) {
    return (moneda || 0).toLocaleString('es-MX', { style:'currency', currency:'MXN' });
  }

  function obtenerTotalBase() {
    const netNode = document.getElementById('total_neto_ui');
    if (netNode && netNode.textContent && netNode.textContent.trim() !== '') {
      const netVal = parseFloat(netNode.textContent.replace(/[$,]/g,'')) || 0;
      if (netVal > 0) return netVal;
    }
    const totalEl   = document.getElementById('total');
    const totalText = totalEl && totalEl.textContent ? totalEl.textContent : '0';
    return parseFloat(totalText.replace(/[$,]/g,'')) || 0;
  }

  function obtenerTotal() {
    let base = obtenerTotalBase();
    let anticipo = 0;
    if (anticipoMontoInput && anticipoMontoInput.value !== '') {
      anticipo = parseFloat(String(anticipoMontoInput.value).replace(/[$,]/g,'')) || 0;
    }
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

  // ================================
  // PERSONALIZADO: filas con input
  // ================================
  function crearFilaPersonalizada(desc, isoDate, monto) {
    if (!listaPagosPersonalizados) return;
    const cont = document.createElement('div');
    cont.classList.add('mb-2');
    cont.setAttribute('data-pago-personalizado','1');
    cont.dataset.date = isoDate;
    cont.dataset.desc = desc;

    const fechaStr = isoToDate(isoDate).toLocaleDateString('es-MX',{day:'2-digit',month:'long',year:'numeric'});

    const label = document.createElement('label');
    label.innerHTML = '<strong>' + desc + ' - ' + fechaStr + ':</strong>';

    const inputDiv = document.createElement('div');
    inputDiv.classList.add('d-flex','align-items-center','gap-2');

    const input = document.createElement('input');
    input.type = 'number';
    input.classList.add('form-control','modern-input','w-50');
    input.value = (parseFloat(monto)||0).toFixed(2);

    input.addEventListener('input', actualizarDesdeFilasPersonalizadas);

    inputDiv.appendChild(input);

    const btnDel = document.createElement('button');
    btnDel.type = 'button';
    btnDel.className = 'btn btn-sm btn-outline-danger';
    btnDel.textContent = 'Eliminar';
    btnDel.addEventListener('click', function(){
      cont.remove();
      actualizarDesdeFilasPersonalizadas();
    });
    inputDiv.appendChild(btnDel);

    cont.appendChild(label);
    cont.appendChild(inputDiv);
    listaPagosPersonalizados.appendChild(cont);
  }

  function agregarPagoPersonalizadoExtra() {
    if (!listaPagosPersonalizados) return;
    const filas = listaPagosPersonalizados.querySelectorAll('[data-pago-personalizado]');
    let lastISO = fechaBaseVentaISO || formatoFechaISO(new Date());

    if (filas.length > 0) {
      const last = filas[filas.length - 1];
      lastISO = last.dataset.date || lastISO;
    }

    const nuevoISO = addMonthsToISO(lastISO, 1);
    const idx = filas.length;
    const desc = (idx === 0)
      ? 'Pago inicial'
      : (nombresPagos[idx-1] || ((idx+1)+'º')) + ' pago';

    crearFilaPersonalizada(desc, nuevoISO, 0);
    actualizarDesdeFilasPersonalizadas();
  }

  function ponerBotonAgregarPersonalizado() {
    if (!listaPagosPersonalizados) return;
    let btn = listaPagosPersonalizados.querySelector('#btn-add-pago-personalizado');
    if (btn) return;
    btn = document.createElement('button');
    btn.type = 'button';
    btn.id = 'btn-add-pago-personalizado';
    btn.className = 'btn btn-sm btn-outline-secondary mt-2';
    btn.textContent = '+ Agregar pago';
    btn.addEventListener('click', agregarPagoPersonalizadoExtra);
    listaPagosPersonalizados.appendChild(btn);
  }

  function limpiarBotonAgregarPersonalizado() {
    if (!listaPagosPersonalizados) return;
    const btn = listaPagosPersonalizados.querySelector('#btn-add-pago-personalizado');
    if (btn) btn.remove();
  }

  function actualizarDesdeFilasPersonalizadas() {
    if (!listaPagosPersonalizados) return;
    const totalFin = obtenerTotal();
    const filas = listaPagosPersonalizados.querySelectorAll('[data-pago-personalizado]');
    planPagosDiv.innerHTML = '';
    window.pagos = [];

    let suma = 0;
    filas.forEach(function(row, idx){
      const iso = row.dataset.date || fechaBaseVentaISO;
      const desc = row.dataset.desc || (idx === 0 ? 'Pago inicial' : 'Pago ' + idx);
      const input = row.querySelector('input[type="number"]');
      const monto = parseFloat(input ? input.value : '0') || 0;
      suma += monto;

      window.pagos.push({
        cuota: monto.toFixed(2),
        descripcion: desc,
        mes: iso
      });

      const fechaStr = isoToDate(iso).toLocaleDateString('es-MX',{day:'2-digit',month:'long',year:'numeric'});
      const p = document.createElement('p');
      p.innerHTML = '<strong>' + desc + ' - ' + fechaStr + ':</strong> ' + formatear(monto);
      planPagosDiv.appendChild(p);
    });

    // sincronizar "número de meses": pagos - 1 (mínimo 1)
    if (mesesPersonalizado) {
      const meses = Math.max(1, filas.length - 1);
      mesesPersonalizado.value = meses;
    }

    const resumen = document.createElement('p');
    resumen.style.fontWeight = 'bold';
    const coincide = cantidadesCoinciden(suma, totalFin);
    resumen.textContent =
      'Total de pagos: ' + formatear(suma) +
      ' | Número de pagos: ' + filas.length +
      (coincide ? ' ✅ (Coincide con el total a financiar)' : ' ⚠️ (No coincide con el total a financiar)');
    planPagosDiv.appendChild(resumen);
  }

  function generarPersonalizadoNuevo(totalFin) {
    if (!listaPagosPersonalizados) return;
    listaPagosPersonalizados.innerHTML = '';
    const meses = parseInt(mesesPersonalizado && mesesPersonalizado.value ? mesesPersonalizado.value : '1',10) || 1;
    const totalPagos = meses + 1;
    const baseISO = fechaBaseVentaISO || formatoFechaISO(new Date());
    let currentISO = baseISO;
    const cuotaBase = totalFin / totalPagos;

    for (let i = 0; i < totalPagos; i++) {
      if (i > 0) currentISO = addMonthsToISO(currentISO, 1);
      const desc = (i === 0)
        ? 'Pago inicial'
        : (nombresPagos[i-1] || ((i+1)+'º')) + ' pago';
      crearFilaPersonalizada(desc, currentISO, cuotaBase);
    }

    ponerBotonAgregarPersonalizado();
    actualizarDesdeFilasPersonalizadas();
  }

  // ====================================
  // NO PERSONALIZADOS
  // ====================================
  function actualizarPlanPagosNoPersonalizado(totalFin) {
    const tipo = tipoPago.value || '';
    planPagosDiv.innerHTML = '';
    window.pagos = [];

    if (!tipo) {
      planPagosDiv.innerHTML = '<p style="color:#666;">Selecciona un plan para ver el detalle de pagos.</p>';
      return;
    }
    if (totalFin <= 0) {
      planPagosDiv.innerHTML = '<p style="color:red;">Total a financiar inválido o cero</p>';
      return;
    }

    const baseISO = fechaBaseVentaISO || formatoFechaISO(new Date());
    const baseDate = isoToDate(baseISO);

    function agregarPago(cuota, offsetMeses, descripcion) {
      const d = new Date(baseDate);
      d.setMonth(d.getMonth() + offsetMeses);
      const iso   = formatoFechaISO(d);
      const monto = parseFloat(cuota) || 0;

      window.pagos.push({
        cuota: monto.toFixed(2),
        descripcion: descripcion,
        mes: iso
      });

      const fechaStr = d.toLocaleDateString('es-MX',{day:'2-digit',month:'long',year:'numeric'});
      const p = document.createElement('p');
      p.innerHTML = '<strong>' + descripcion + ' - ' + fechaStr + ':</strong> ' + formatear(monto);
      planPagosDiv.appendChild(p);
    }

    if (tipo === 'contado') {
      agregarPago(totalFin, 0, 'Pago único');

    } else if (tipo === 'estatico') {
      if (totalFin < 500000) {
        agregarPago(totalFin * 0.5, 0, 'Pago inicial');
        agregarPago(totalFin * 0.25, 1, 'Primer pago');
        agregarPago(totalFin * 0.25, 2, 'Segundo pago');
      } else {
        const primerPago = totalFin * 0.4;
        agregarPago(primerPago, 0, 'Pago inicial');
        const restante = totalFin - primerPago;
        const num = Math.min(6, Math.max(4, Math.ceil(restante / 50000)));
        const cuota = restante / num;
        for (let i = 0; i < num; i++) {
          agregarPago(cuota, i+1, (nombresPagos[i] || ((i+1)+'º')) + ' pago');
        }
      }

    } else if (tipo === 'dinamico') {
      const ini = parseFloat(pagoInicial && pagoInicial.value ? pagoInicial.value : '0') || 0;
      if (ini <= 0 || ini >= totalFin) {
        planPagosDiv.innerHTML = '<p style="color:red;">Pago inicial inválido</p>';
        return;
      }
      const restante2 = totalFin - ini;
      const num2 = (totalFin < 150000) ? 2 : (totalFin < 400000) ? 4 : 6;
      const cuota2 = restante2 / num2;

      agregarPago(ini, 0, 'Pago inicial');
      for (let j = 0; j < num2; j++) {
        agregarPago(cuota2, j+1, (nombresPagos[j] || ((j+1)+'º')) + ' pago');
      }

    } else if (tipo === 'credito') {
      const iniC = parseFloat(pagoCreditoInicial && pagoCreditoInicial.value ? pagoCreditoInicial.value : '0') || 0;
      const plazo = parseInt(plazoCredito && plazoCredito.value ? plazoCredito.value : '6',10) || 6;
      if (iniC < 0 || iniC >= totalFin) {
        planPagosDiv.innerHTML = '<p style="color:red;">Pago inicial de crédito inválido</p>';
        return;
      }
      if (plazo <= 0) {
        planPagosDiv.innerHTML = '<p style="color:red;">Plazo inválido</p>';
        return;
      }

      const tasa = 0.05;
      const monto = totalFin - iniC;
      const totalCred = monto + (monto * tasa * plazo);
      const cuotaM = totalCred / plazo;

      agregarPago(iniC, 0, 'Pago inicial');

      const info = document.createElement('p');
      info.innerHTML = '<strong>Total a pagar con crédito:</strong> ' + formatear(totalCred);
      planPagosDiv.appendChild(info);

      for (let k = 0; k < plazo; k++) {
        agregarPago(cuotaM, k+1, (nombresPagos[k] || ((k+1)+'º')) + ' pago');
      }

    } else {
      planPagosDiv.innerHTML = '<p style="color:#666;">Tipo de plan no soportado.</p>';
      return;
    }

    const sumaFinal = window.pagos.reduce(function(acc, p){ return acc + (parseFloat(p.cuota)||0); }, 0);
    const totalP = document.createElement('p');
    totalP.style.fontWeight = 'bold';
    const coincide = cantidadesCoinciden(sumaFinal, totalFin);
    totalP.textContent =
      'Total de pagos: ' + formatear(sumaFinal) +
      ' | Número de pagos: ' + window.pagos.length +
      (coincide ? ' ✅ (Coincide)' : ' ⚠️ (No coincide)');
    planPagosDiv.appendChild(totalP);
  }

  function actualizarPlanPagos(totalOpt) {
    const totalFin = typeof totalOpt === 'number' ? totalOpt : obtenerTotal();
    const tipo = tipoPago.value || '';

    if (tipo === 'personalizado') {
      if (listaPagosPersonalizados && listaPagosPersonalizados.querySelector('[data-pago-personalizado]')) {
        ponerBotonAgregarPersonalizado();
        actualizarDesdeFilasPersonalizadas();
      } else {
        generarPersonalizadoNuevo(totalFin);
      }
    } else {
      if (listaPagosPersonalizados) listaPagosPersonalizados.innerHTML = '';
      limpiarBotonAgregarPersonalizado();
      actualizarPlanPagosNoPersonalizado(totalFin);
    }
  }

  // ================================
  // RENDER INICIAL (edición / nuevo)
  // ================================
  function renderPagosExistentes() {
    mostrarOpcionesTipo();
    const tipo = tipoPago.value || '';
    const totalFin = obtenerTotal();

    if (!tienePagosIniciales) {
      actualizarPlanPagos(totalFin);
      return;
    }

    window.pagos = pagosIniciales.map(function(p){
      return {
        cuota: (parseFloat(p.cuota)||0).toFixed(2),
        descripcion: p.descripcion || '',
        mes: p.mes
      };
    });

    if (tipo === 'personalizado' && listaPagosPersonalizados) {
      listaPagosPersonalizados.innerHTML = '';
      window.pagos.forEach(function(p, idx){
        const desc = p.descripcion || (idx === 0 ? 'Pago inicial' : (nombresPagos[idx-1] || ((idx+1)+'º')) + ' pago');
        crearFilaPersonalizada(desc, p.mes, p.cuota);
      });

      if (mesesPersonalizado) {
        const meses = Math.max(1, window.pagos.length - 1);
        mesesPersonalizado.value = meses;
      }

      ponerBotonAgregarPersonalizado();
      actualizarDesdeFilasPersonalizadas();
    } else {
      planPagosDiv.innerHTML = '';
      let suma = 0;
      window.pagos.forEach(function(p, idx){
        const fecha = isoToDate(p.mes);
        const fechaStr = fecha.toLocaleDateString('es-MX',{day:'2-digit',month:'long',year:'numeric'});
        const desc = p.descripcion || (idx === 0 ? 'Pago inicial' : 'Pago ' + idx);
        const monto = parseFloat(p.cuota)||0;
        suma += monto;
        const node = document.createElement('p');
        node.innerHTML = '<strong>' + desc + ' - ' + fechaStr + ':</strong> ' + formatear(monto);
        planPagosDiv.appendChild(node);
      });
      const resumen = document.createElement('p');
      resumen.style.fontWeight = 'bold';
      const coincide = cantidadesCoinciden(suma, totalFin);
      resumen.textContent =
        'Total de pagos: ' + formatear(suma) +
        ' | Número de pagos: ' + window.pagos.length +
        (coincide ? ' ✅ (Coincide con el total a financiar)' : ' ⚠️ (No coincide con el total a financiar)');
      planPagosDiv.appendChild(resumen);
    }
  }

  mostrarOpcionesTipo();
  if (tienePagosIniciales) {
    renderPagosExistentes();
  } else {
    actualizarPlanPagos(obtenerTotal());
  }

  // ================================
  // EVENTOS
  // ================================
  tipoPago.addEventListener('change', function(){
    mostrarOpcionesTipo();
    actualizarPlanPagos(obtenerTotal());
  });

  [pagoInicial, pagoCreditoInicial, plazoCredito, mesesPersonalizado].forEach(function(inp){
    if (inp) inp.addEventListener('input', function(){
      actualizarPlanPagos(obtenerTotal());
    });
  });

  if (anticipoMontoInput) {
    anticipoMontoInput.addEventListener('input', function(){
      actualizarPlanPagos(obtenerTotal());
    });
    anticipoMontoInput.addEventListener('change', function(){
      actualizarPlanPagos(obtenerTotal());
    });
  }

  function debounce(fn, delay) {
    let t;
    return function(){
      const args = arguments;
      clearTimeout(t);
      t = setTimeout(function(){ fn.apply(null,args); }, delay);
    };
  }

  const onTotalEvent = debounce(function(){
    actualizarPlanPagos(obtenerTotal());
  }, 80);

  window.addEventListener('total:changed', onTotalEvent);

  if (typeof MutationObserver !== 'undefined') {
    const totalNode = document.getElementById('total');
    const netNode   = document.getElementById('total_neto_ui');
    if (totalNode) {
      new MutationObserver(onTotalEvent).observe(totalNode,{childList:true,characterData:true,subtree:true});
    }
    if (netNode) {
      new MutationObserver(onTotalEvent).observe(netNode,{childList:true,characterData:true,subtree:true});
    }
  }
});
</script>

<script>
// =====================================================
// Submit: validar cliente + preparar JSONs (productos, pagos, tradeins)
// =====================================================
document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('form-venta');
  const inputPagosJson = document.getElementById('pagosJsonInput');

  if (!form) return;

  form.addEventListener('submit', function (e) {
    if (!document.getElementById('cliente_id')?.value) {
      e.preventDefault();
      alert('Por favor selecciona un cliente antes de continuar.');
      return;
    }

    prepararProductosJSON();

    if (typeof window.prepararTradeinsJSON === 'function') window.prepararTradeinsJSON();

    if (inputPagosJson) {
      const pagosFormateados = (window.pagos || []).map(function (p) {
        return { cuota: p.cuota, descripcion: p.descripcion, mes: p.mes };
      });
      inputPagosJson.value = JSON.stringify(pagosFormateados);
    }
  });
});
</script>

<script>
// =====================================================
// Clientes dropdown dinámico + creación rápida
// =====================================================

function cerrarModal() {
  const modalEl = document.getElementById("cliente_creado");
  if (!modalEl) return;
  if (window.bootstrap?.Modal) {
    const modalInstance = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
    modalInstance.hide();
  }
}

document.addEventListener("DOMContentLoaded", function () {
  const searchInput    = document.getElementById("search-client");
  const clientList     = document.getElementById("client-list");
  const clienteIdInput = document.getElementById("cliente_id");
  const clientDetails  = document.getElementById("client-details");
  const formVenta      = document.getElementById("form-venta");

  if (!searchInput || !clientList || !clienteIdInput || !clientDetails || !formVenta) return;

  function loadClients(search) {
    if (typeof search === 'undefined') search = "";
    fetch('/buscar-clientes?search=' + encodeURIComponent(search), {
      method: "GET",
      headers: { "Accept": "application/json" }
    })
    .then(r => r.json())
    .then(function (clients) {
      clientList.innerHTML =
        '<li>' +
        '  <button type="button" class="dropdown-item modern-dropdown-item" onclick=\'selectClientFromEncoded("' +
            encodeURIComponent(JSON.stringify({ id: 1, nombre: "PÚBLICO EN GENERAL", apellido: "", telefono: "", email: "", comentarios: "" })) +
        '")\'>PÚBLICO EN GENERAL</button>' +
        '</li>' +
        '<li>' +
        '  <button type="button" class="dropdown-item modern-dropdown-item" onclick="openCreateClientModal()">+ Crear nuevo cliente</button>' +
        '</li>';

      if ((!clients || clients.length === 0) && search !== "") {
        clientList.innerHTML += '<li><button type="button" class="dropdown-item disabled">No se encontraron resultados</button></li>';
      } else {
        (clients || []).forEach(function (client) {
          const name = ((client.nombre || '').toUpperCase() + ' ' + (client.apellido || '').toUpperCase()).trim();
          const encoded = encodeURIComponent(JSON.stringify(client));
          const li = document.createElement('li');
          li.innerHTML = '<button type="button" class="dropdown-item modern-dropdown-item" onclick=\'selectClientFromEncoded("' + encoded + '")\'>' + (name || '(SIN NOMBRE)') + '</button>';
          clientList.appendChild(li);
        });
      }
    })
    .catch(err => console.error("Error al cargar clientes:", err));
  }

  window.selectClientFromEncoded = function (encoded) {
    const client = JSON.parse(decodeURIComponent(encoded));
    selectClient(client);
  };

  window.selectClient = function (client) {
    const nombre = (client.nombre || '').toUpperCase();
    const apellido = (client.apellido || '').toUpperCase();

    searchInput.value = (nombre + ' ' + apellido).trim();
    clienteIdInput.value = client.id != null ? client.id : "";

    clientDetails.innerHTML =
      '<p><strong>Nombre:</strong> ' + nombre + ' ' + apellido + '</p>' +
      '<p><strong>Teléfono:</strong> ' + (client.telefono || "No registrado") + '</p>' +
      '<p><strong>Email:</strong> ' + (client.email || "No registrado") + '</p>' +
      '<p><strong>Dirección:</strong> ' + (client.comentarios || "No registrado") + '</p>';

    clientDetails.style.padding = "15px";
    clientList.classList.remove("show");
  };

  window.openCreateClientModal = function () {
    const modalFormularioEl = document.getElementById("modal_formulario");
    if (modalFormularioEl && window.bootstrap?.Modal) {
      (bootstrap.Modal.getInstance(modalFormularioEl) || new bootstrap.Modal(modalFormularioEl)).show();
    }
  };

  searchInput.addEventListener("input", function () {
    loadClients(searchInput.value.trim());
    clientList.classList.add("show");
  });

  searchInput.addEventListener("keydown", function (e) {
    if (e.key === "Enter") {
      e.preventDefault();
      loadClients(searchInput.value.trim());
      clientList.classList.add("show");
    }
  });

  loadClients();

  const formCliente            = document.getElementById("form-cliente");
  const modalFormularioElement = document.getElementById("modal_formulario");
  const modalExitoElement      = document.getElementById("cliente_creado");
  if (!formCliente || !modalFormularioElement || !modalExitoElement) return;

  const modalFormulario = (window.bootstrap?.Modal) ? new bootstrap.Modal(modalFormularioElement) : null;
  const modalExito      = (window.bootstrap?.Modal) ? new bootstrap.Modal(modalExitoElement) : null;

  formCliente.addEventListener("submit", function (event) {
    event.preventDefault();

    const errorTelefono = document.getElementById("error-telefono");
    const errorEmail    = document.getElementById("error-email");
    if (errorTelefono) { errorTelefono.style.display = "none"; errorTelefono.textContent = ""; }
    if (errorEmail)    { errorEmail.style.display    = "none"; errorEmail.textContent    = ""; }

    const nombre      = document.getElementById("nombre").value.trim();
    const apellido    = document.getElementById("apellido").value.trim();
    const telefono    = document.getElementById("telefono").value.trim();
    const email       = document.getElementById("email").value.trim();
    const comentarios = document.getElementById("comentarios").value.trim();

    if (!nombre || !apellido || !telefono) {
      alert("Nombre, apellido y teléfono son obligatorios.");
      return;
    }

    fetch("{{ route('clientes.check_unique') }}", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
        "Accept": "application/json"
      },
      body: JSON.stringify({ telefono: telefono, email: email })
    })
    .then(res => res.json())
    .then(function (data) {
      if (data.success) {
        fetch("{{ route('clientes.store') }}", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            "Accept": "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
          },
          body: JSON.stringify({ nombre, apellido, telefono, email, comentarios })
        })
        .then(res => res.json())
        .then(function (data) {
          if (data.success && data.cliente) {
            if (modalFormulario) modalFormulario.hide();

            modalFormularioElement.addEventListener("hidden.bs.modal", function () {
              if (modalExito) modalExito.show();
              loadClients();
            }, { once: true });

            formCliente.reset();
            selectClient(data.cliente);
          } else {
            alert(data.message || "Ocurrió un error al guardar el cliente.");
          }
        })
        .catch(function (err) {
          console.error(err);
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
    .catch(function (err) {
      console.error(err);
      alert("Error al verificar la existencia del teléfono o correo.");
    });
  });
});
</script>


@endsection
