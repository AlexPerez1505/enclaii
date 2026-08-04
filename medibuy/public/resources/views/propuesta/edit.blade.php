@extends('layouts.app')
@section('title', 'Cotización')
@section('titulo', 'Editar')
@section('content')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
    body {
        background: linear-gradient(120deg, #f0f4ff, #e8f9f3);
        font-family: 'Segoe UI', sans-serif;
        font-size: 0.95rem;
    }

    h1, h3, h4 {
        color: #333;
        font-weight: 600;
    }

    .form-section {
        background: #ffffff;
        border-radius: 0.8rem;
        box-shadow: 0 3px 12px rgba(0, 0, 0, 0.03);
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        border-left: 4px solid #8ec9ff;
    }

    @media (min-width: 768px) {
        .form-section.double {
            display: flex;
            gap: 1.5rem;
        }

        .form-section.double > .form-section-inner {
            flex: 1;
        }
    }

    label {
        font-weight: 500;
        color: #444;
        margin-bottom: 0.4rem;
    }

    .form-control {
        border-radius: 0.5rem;
        border: 1px solid #d0dce9;
        background-color: #fff;
        font-size: 0.92rem;
    }

    .form-control:focus {
        border-color: #8ec9ff;
        box-shadow: 0 0 0 0.15rem rgba(167, 214, 255, 0.35);
    }

    .btn {
        font-weight: 500;
        border-radius: 0.5rem;
    }

    .btn-primary {
        background-color: #8ec9ff;
        border-color: #8ec9ff;
        color: #1f1f1f;
    }

    .btn-primary:hover {
        background-color: #73bdf8;
        border-color: #73bdf8;
    }

    .btn-danger {
        background-color: #f8bcbc;
        border-color: #f8bcbc;
        color: #5c1a1a;
    }

    .btn-danger:hover {
        background-color: #f69c9c;
        border-color: #f69c9c;
    }

    .btn-success {
        background-color: #c1f3d3;
        border-color: #c1f3d3;
        color: #215c3b;
    }

    .btn-success:hover {
        background-color: #a8e9bf;
    }

    .table thead {
        background: #e7f3ff;
    }

    .table th {
        color: #333;
        font-weight: 500;
        font-size: 0.92rem;
    }

    .table td {
        font-size: 0.9rem;
    }

    .table td, .table th {
        vertical-align: middle;
        text-align: center;
    }

    .table img {
        border-radius: 0.4rem;
    }

    .alert-danger {
        font-size: 0.9rem;
        padding: 0.6rem 1rem;
    }
    .toggle-switch {
    position: relative;
    display: inline-block;
    width: 40px;
    height: 22px;
}
.toggle-switch input {
    display: none;
}
.toggle-slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #c8d6e5;
    transition: 0.4s;
    border-radius: 34px;
}
.toggle-slider:before {
    position: absolute;
    content: "";
    height: 16px;
    width: 16px;
    left: 3px;
    bottom: 3px;
    background-color: #576574;
    transition: 0.4s;
    border-radius: 50%;
}
.toggle-switch input:checked + .toggle-slider {
    background-color: #74b9ff;
}
.toggle-switch input:checked + .toggle-slider:before {
    transform: translateX(18px);
}
.dropdown-container {
    position: relative;
    width: 100%;
}

.dropdown-input {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid #ccc;
    border-radius: 8px;
    font-size: 14px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.dropdown-list {
    position: absolute;
    z-index: 1000;
    width: 100%;
    max-height: 300px;
    overflow-y: auto;
    background: white;
    border: 1px solid #ddd;
    border-top: none;
    border-radius: 0 0 8px 8px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    margin-top: -1px;
    display: none;
    padding: 0;
    list-style: none;
}

.dropdown-item {
    padding: 10px 12px;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 10px;
    border-bottom: 1px solid #eee;
}

.dropdown-item:hover {
    background-color: #f2f2f2;
}

.img-preview {
    width: 40px;
    height: 40px;
    object-fit: contain;
    border: 1px solid #ddd;
    border-radius: 6px;
    background-color: #fafafa;
}

.item-info {
    flex: 1;
    font-size: 13px;
    color: #333;
    line-height: 1.2;
}

.stock-badge {
    font-size: 11px;
    color: #2e7d32;
    background-color: #e8f5e9;
    padding: 4px 8px;
    border-radius: 12px;
    white-space: nowrap;
}

</style>
<div class="container mt-4">
    <h1 class="mb-4 text-center">Editar Propuesta</h1>

    <form action="{{ route('propuestas.update', $propuesta->id) }}" method="POST" id="form-propuesta" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <!-- Productos -->
<!-- 1) Incluye Fuse.js antes de tu script -->
<script src="https://cdn.jsdelivr.net/npm/fuse.js@6.6.2/dist/fuse.min.js"></script>

<div class="dropdown-container">
  <input type="text"
         id="buscador-producto"
         class="form-control dropdown-input"
         placeholder="Buscar producto..."
         autocomplete="off">
  <ul id="lista-productos" class="dropdown-list" style="display:none">
    @foreach($productos as $producto)
      <li class="dropdown-item"
          data-id="{{ $producto->id }}"
          data-nombre="{{ $producto->tipo_equipo }}"
          data-modelo="{{ $producto->modelo }}"
          data-marca="{{ $producto->marca }}"
          data-precio="{{ $producto->precio }}"
          data-imagen="{{ asset('storage/'.$producto->imagen) }}">
        
        <img src="{{ asset('storage/'.$producto->imagen) }}"
             alt="img" class="img-preview">
        
        <div class="item-info">
          <strong>{{ strtoupper($producto->tipo_equipo) }}</strong><br>
          {{ $producto->modelo }} {{ $producto->marca }}<br>
          <small>${{ number_format($producto->precio,2) }}</small>
        </div>
      </li>
    @endforeach
  </ul>


<br>


            <div class="table-responsive">
                <table id="tabla-productos" class="table table-bordered">
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
                    <tbody>
                        @foreach($propuesta->productos as $pp)
                            <tr data-id="{{ $pp->producto_id }}">
                                <td><img src="{{ asset('storage/' . $pp->producto->imagen) }}" width="50"></td>
                                <td class="equipo">{{ $pp->producto->tipo_equipo }}</td>
                                <td>{{ $pp->producto->modelo }}</td>
                                <td>{{ $pp->producto->marca }}</td>
                                <td>
                                    <input type="number" class="form-control cantidad" name="productos[{{ $pp->producto_id }}][cantidad]" value="{{ $pp->cantidad }}" onchange="actualizarSubtotal(this)">
                                </td>
                                <td class="subtotal">{{ $pp->subtotal }}</td>
                                <td>
                                    <input type="number" class="form-control sobreprecio" name="productos[{{ $pp->producto_id }}][sobreprecio]" value="{{ $pp->sobreprecio }}" onchange="actualizarSubtotal(this)">
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-danger" onclick="eliminarFila(this)">Eliminar</button>
                                </td>
                                <input type="hidden" class="precio_unitario" name="productos[{{ $pp->producto_id }}][precio_unitario]" value="{{ $pp->precio_unitario }}">
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Datos Cliente y Totales -->
        <div class="form-section double">
            <div class="form-section-inner">
                <div class="mb-3">
                    <label for="cliente_id">Cliente</label>
                    <select name="cliente_id" id="cliente_id" class="form-control" required>
                        @foreach($clientes as $cliente)
                            <option value="{{ $cliente->id }}" {{ $propuesta->cliente_id == $cliente->id ? 'selected' : '' }}>
                                {{ $cliente->nombre }} {{ $cliente->apellido }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label for="lugar">Lugar</label>
                    <input type="text" name="lugar" id="lugar" class="form-control" value="{{ $propuesta->lugar }}" required>
                </div>
                <div class="mb-3">
                    <label for="nota">Nota</label>
                    <textarea name="nota" id="nota" class="form-control" rows="2">{{ $propuesta->nota }}</textarea>
                </div>
              <div class="mb-3">
    <label for="plan">Plan</label>
    <select name="plan" id="plan" class="form-control">
        <option value="" disabled {{ $propuesta->plan == '' ? 'selected' : '' }}>Selecciona un plan</option>
        <option value="contado" {{ $propuesta->plan == 'contado' ? 'selected' : '' }}>Pago de Contado</option>
        <option value="personalizado" {{ $propuesta->plan == 'personalizado' ? 'selected' : '' }}>Plan Personalizado</option>
        <option value="estatico" {{ $propuesta->plan == 'estatico' ? 'selected' : '' }}>Plan Fijo</option>
        <option value="dinamico" {{ $propuesta->plan == 'dinamico' ? 'selected' : '' }}>Plan Flexible</option>
        <option value="credito" {{ $propuesta->plan == 'credito' ? 'selected' : '' }}>Plan a Crédito</option>
    </select>
</div>

            </div>

            <div class="form-section-inner">
                <div class="mb-3">
                    <label for="subtotal">Subtotal</label>
                    <input type="number" name="subtotal" id="subtotal" class="form-control" value="{{ $propuesta->subtotal }}" required>
                </div>
                <div class="mb-3">
                    <label for="descuento">Descuento</label>
                    <input type="number" name="descuento" id="descuento" class="form-control" value="{{ $propuesta->descuento }}" step="0.01" oninput="actualizarTotal()">
                </div>
                <div class="mb-3">
                    <label for="envio">Envío</label>
                    <input type="number" name="envio" id="envio" class="form-control" value="{{ $propuesta->envio }}" step="0.01" oninput="actualizarTotal()">
                </div>
                <div class="mb-3">
                    <label for="iva">IVA</label>
                    <input type="number" name="iva" id="iva" class="form-control" readonly>
                    <div class="form-check form-switch mt-1">
                        <input class="form-check-input" type="checkbox" id="toggleIva">
                        <label class="form-check-label" for="toggleIva">Aplicar IVA (16%)</label>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="total">Total</label>
                    <input type="number" name="total" id="total" class="form-control" value="{{ $propuesta->total }}" required readonly>
                </div>
            </div>
        </div>

   <!-- Pagos Planeados -->
<div class="form-section">
    <h4 class="mb-3">Pagos Planeados (Financiamiento)</h4>
    <table id="tabla-pagos" class="table table-bordered align-middle">
        <thead>
            <tr>
                <th>Descripción</th>
                <th>Fecha</th>
                <th>Monto</th>
                <th>Bloqueado</th>
                <th>¿Eliminar?</th>
            </tr>
        </thead>
        <tbody>
            @foreach($propuesta->pagos as $pago)
                <tr>
                    <td>
                        <input type="text" name="pagos_financiamiento[{{ $pago->id }}][descripcion]" value="{{ $pago->descripcion }}" class="form-control" required>
                    </td>
                    <td>
                        <input type="date" name="pagos_financiamiento[{{ $pago->id }}][fecha_pago]" value="{{ \Carbon\Carbon::parse($pago->fecha_pago)->format('Y-m-d') }}" class="form-control" required>
                    </td>
                    <td>
                        <input type="number" step="0.01" name="pagos_financiamiento[{{ $pago->id }}][monto]" value="{{ $pago->monto }}" class="form-control monto-pago" required>
                    </td>
                    <td class="text-center">
                        <!-- Switch visual de bloqueo (solo frontend) -->
                        <label class="toggle-switch">
                            <input type="checkbox" class="switch-bloqueo" checked>
                            <span class="toggle-slider"></span>
                        </label>
                    </td>
                    <td>
                        <button type="button" class="btn btn-sm btn-danger eliminar-fila">Eliminar</button>
                        <input type="hidden" name="pagos_financiamiento[{{ $pago->id }}][eliminar]" value="0" class="eliminar-hidden">
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <button type="button" id="agregarPago" class="btn btn-success btn-sm mb-3">Agregar Pago</button>

    <div id="error-total-pagos" class="alert alert-danger d-none">
        La suma de los pagos planeados debe ser igual al total de la propuesta.
    </div>
</div>

<!-- Campo oculto para enviar pagos_json al backend -->
<input type="hidden" name="pagos_json" id="pagos_json">

        <input type="hidden" name="productos_json" id="productos_json">

        <div class="text-center mb-5">
            <button type="submit" class="btn btn-primary px-5 py-2">Guardar Cambios</button>
        </div>
    </form>
</div>

<script>
$(document).ready(function () {
    console.log('Documento listo, inicializando funciones...');
    actualizarTotal();
    manejarSeleccionProducto();
    manejarCambioTotales();
    manejarAgregarPago();
    manejarEliminacionPago();
    manejarEnvioFormulario();
});
</script>
<script>
function manejarSeleccionProducto() {
    $('#producto').change(function () {
        const selected = $(this).find(':selected');
        const id = selected.val();
        const nombre = selected.data('nombre');
        const modelo = selected.data('modelo');
        const marca = selected.data('marca');
        const precio = parseFloat(selected.data('precio'));
        const imagen = selected.data('imagen');

        console.log('Producto seleccionado:', { id, nombre, modelo, marca, precio });

        if (!id || !nombre) return;

        if ($(`#tabla-productos tbody tr[data-id="${id}"]`).length > 0) {
            alert('Este producto ya ha sido agregado.');
            console.warn('Producto duplicado:', id);
            return;
        }

        const fila = `
            <tr data-id="${id}" data-precio="${precio}">
                <td><img src="${imagen}" width="50"></td>
                <td class="equipo">${nombre}</td>
                <td>${modelo}</td>
                <td>${marca}</td>
                <td><input type="number" class="form-control cantidad" name="productos[${id}][cantidad]" value="1" min="1" onchange="actualizarSubtotal(this)"></td>
                <td class="subtotal">${precio.toFixed(2)}</td>
                <td><input type="number" class="form-control sobreprecio" name="productos[${id}][sobreprecio]" value="0" min="0" onchange="actualizarSubtotal(this)"></td>
                <td><button type="button" class="btn btn-sm btn-danger" onclick="eliminarFila(this)">Eliminar</button></td>
                <input type="hidden" class="precio_unitario" name="productos[${id}][precio_unitario]" value="${precio}">
            </tr>`;
        $('#tabla-productos tbody').append(fila);
        actualizarTotal();
        $(this).val('');
    });
}
</script>
<script>
function actualizarSubtotal(input) {
    const tr = $(input).closest('tr');
    const cantidad = parseFloat(tr.find('.cantidad').val()) || 0;
    const precio = parseFloat(tr.find('.precio_unitario').val()) || 0;
    const sobreprecio = parseFloat(tr.find('.sobreprecio').val()) || 0;
    const subtotal = (cantidad * precio) + sobreprecio;
    tr.find('.subtotal').text(subtotal.toFixed(2));
    console.log('Subtotal actualizado:', subtotal);
    actualizarTotal();
}

function actualizarTotal() {
    let subtotal = 0;
    $('#tabla-productos tbody tr').each(function () {
        const cantidad = parseFloat($(this).find('.cantidad').val()) || 0;
        const precio = parseFloat($(this).find('.precio_unitario').val()) || 0;
        const sobreprecio = parseFloat($(this).find('.sobreprecio').val()) || 0;
        const filaSubtotal = (cantidad * precio) + sobreprecio;
        subtotal += filaSubtotal;
        $(this).find('.subtotal').text(filaSubtotal.toFixed(2));
    });

    const descuento = parseFloat($('#descuento').val()) || 0;
    const envio = parseFloat($('#envio').val()) || 0;
    const base = subtotal - descuento + envio;
    const iva = $('#toggleIva').is(':checked') ? base * 0.16 : 0;
    const total = base + iva;

    $('#subtotal').val(subtotal.toFixed(2));
    $('#iva').val(iva.toFixed(2));
    $('#total').val(total.toFixed(2));

    console.log('Totales:', { subtotal, descuento, envio, iva, total });

    // IMPORTANTE: NO LLAMES actualizarPagos() aquí para no sobrescribir los montos existentes
    // actualizarPagos();
}

function eliminarFila(btn) {
    console.log('Eliminando producto...');
    $(btn).closest('tr').remove();
    actualizarTotal();
}
</script>


<script>
function manejarCambioTotales() {
    $('#descuento, #envio').on('input', function () {
        console.log('Cambio en descuento/envío');
        actualizarTotal();
    });

    $('#toggleIva').on('change', function () {
        console.log('Cambio en IVA');
        if (navigator.vibrate) navigator.vibrate(80);
        $('#iva').prop('readonly', true);
        actualizarTotal();
    });
}
</script>
<script>
document.getElementById('form-propuesta').addEventListener('submit', function(event) {
    const totalPropuesta = parseFloat(document.getElementById('total').value) || 0;
    let sumaPagos = 0;

    document.querySelectorAll('#tabla-pagos tbody tr').forEach(tr => {
        const monto = parseFloat(tr.querySelector('input[name$="[monto]"]')?.value) || 0;
        const bloqueado = tr.querySelector('.switch-bloqueo')?.checked ?? true;
        const eliminar = tr.querySelector('.eliminar-hidden')?.value === '1';

        if (!eliminar) {
            sumaPagos += monto;
        }
    });

    const errorDiv = document.getElementById('error-total-pagos');

    if (Math.abs(sumaPagos - totalPropuesta) > 0.01) {
        event.preventDefault();
        errorDiv.classList.remove('d-none');
        errorDiv.scrollIntoView({ behavior: 'smooth' });
    } else {
        errorDiv.classList.add('d-none');
    }

    prepararPagosJSON();
});

function manejarAgregarPago() {
    let contadorNuevoPago = 0;

    $('#agregarPago').on('click', function () {
        const nuevoId = 'nuevo_' + contadorNuevoPago++;
        const filas = $('#tabla-pagos tbody tr');
        const descripcion = `${numeroEnLetras(filas.length + 1)} pago`;

        let ultimaFecha = null;
        filas.each(function () {
            const fecha = $(this).find('input[name$="[fecha_pago]"]').val();
            if (fecha && (!ultimaFecha || new Date(fecha) > new Date(ultimaFecha))) {
                ultimaFecha = fecha;
            }
        });

        let nuevaFecha = '';
        if (ultimaFecha) {
            const fecha = new Date(ultimaFecha);
            fecha.setMonth(fecha.getMonth() + 1);
            nuevaFecha = fecha.toISOString().split('T')[0];
        }

        const fila = `
            <tr>
                <td>
                    <input type="hidden" name="pagos_financiamiento[${nuevoId}][descripcion]" value="${descripcion}">
                    ${descripcion}
                </td>
                <td><input type="date" name="pagos_financiamiento[${nuevoId}][fecha_pago]" class="form-control" value="${nuevaFecha}" required></td>
                <td><input type="number" step="0.01" name="pagos_financiamiento[${nuevoId}][monto]" class="form-control monto-pago" required></td>
                <td class="text-center">
                    <label class="toggle-switch">
                        <input type="checkbox" class="switch-bloqueo" checked>
                        <span class="toggle-slider"></span>
                    </label>
                </td>
                <td>
                    <button type="button" class="btn btn-sm btn-danger eliminar-fila">Eliminar</button>
                    <input type="hidden" name="pagos_financiamiento[${nuevoId}][eliminar]" class="eliminar-hidden" value="0">
                </td>
            </tr>`;
        $('#tabla-pagos tbody').append(fila);
        ajustarPagos();
    });

    // Escuchar cambios en montos y switches
    $(document).on('input', '.monto-pago', ajustarPagos);
    $(document).on('change', '.switch-bloqueo', ajustarPagos);
}

function ajustarPagos() {
    const total = parseFloat($('#total').val()) || 0;
    const filas = $('#tabla-pagos tbody tr');

    let totalManual = 0;
    const modificables = [];

    filas.each(function () {
        const tr = $(this);
        const montoInput = tr.find('input[name$="[monto]"]');
        const monto = parseFloat(montoInput.val()) || 0;
        const bloqueado = tr.find('.switch-bloqueo').is(':checked');
        const eliminado = tr.find('.eliminar-hidden').val() === '1';

        if (eliminado) return;

        if (bloqueado) {
            totalManual += monto;
        } else {
            modificables.push(montoInput);
        }
    });

    const montoRestante = modificables.length > 0 ? (total - totalManual) / modificables.length : 0;

    modificables.forEach(input => {
        input.val(montoRestante.toFixed(2));
    });

    validarTotalPagos();
}

function actualizarPagos() {
    const total = parseFloat($('#total').val()) || 0;
    const filas = $('#tabla-pagos tbody tr').filter(function () {
        return $(this).find('.eliminar-hidden').val() !== '1';
    });
    const num = filas.length;

    if (num === 0) return;

    const monto = (total / num).toFixed(2);
    filas.each(function () {
        $(this).find('input[name$="[monto]"]').val(monto);
        $(this).find('.switch-bloqueo').prop('checked', true);
    });

    validarTotalPagos();
}

function prepararPagosJSON() {
    const pagos = [];

    $('#tabla-pagos tbody tr').each(function () {
        const tr = $(this);
        const eliminado = tr.find('.eliminar-hidden').val() === '1';
        if (eliminado) return;

        const descripcion = tr.find('input[name$="[descripcion]"]').val();
        const fechaPago = tr.find('input[name$="[fecha_pago]"]').val();
        const monto = parseFloat(tr.find('input[name$="[monto]"]').val()) || 0;

        pagos.push({
            descripcion: descripcion,
            mes: fechaPago,
            cuota: monto
        });
    });

    console.log('Pagos serializados para envío:', pagos);
    $('#pagos_json').val(JSON.stringify(pagos));
}

function validarTotalPagos() {
    const total = parseFloat($('#total').val()) || 0;
    let suma = 0;

    $('#tabla-pagos tbody tr').each(function () {
        const tr = $(this);
        const eliminado = tr.find('.eliminar-hidden').val() === '1';
        if (eliminado) return;

        const monto = parseFloat(tr.find('input[name$="[monto]"]').val()) || 0;
        suma += monto;
    });

    const diferencia = Math.abs(suma - total);
    console.log('Validando total de pagos:', { total, suma, diferencia });

    const errorDiv = $('#error-total-pagos');
    if (diferencia > 0.01) {
        errorDiv.removeClass('d-none');
        return false;
    } else {
        errorDiv.addClass('d-none');
        return true;
    }
}

function manejarEliminacionPago() {
    $(document).on('click', '.eliminar-fila', function () {
        console.log('Eliminando fila de pago');
        const fila = $(this).closest('tr');
        fila.find('.eliminar-hidden').val('1');
        fila.hide();
        ajustarPagos();
    });
}
</script>



<script>
function manejarEnvioFormulario() {
    $('#form-propuesta').submit(function (e) {
        prepararProductosJSON();
        prepararPagosJSON();

        if (!validarTotalPagos()) {
            console.warn('El total de pagos no cuadra con el total general');
            e.preventDefault();
        } else {
            console.log('Formulario enviado correctamente');
        }
    });
}

</script>

<script>
function prepararProductosJSON() {
    const productos = [];
    $('#tabla-productos tbody tr').each(function () {
        productos.push({
            producto_id: $(this).data('id'),
            cantidad: $(this).find('.cantidad').val(),
            precio_unitario: $(this).find('.precio_unitario').val(),
            sobreprecio: $(this).find('.sobreprecio').val(),
            subtotal: $(this).find('.subtotal').text()
        });
    });
    console.log('Productos serializados para envío:', productos);
    $('#productos_json').val(JSON.stringify(productos));
}

function numeroEnLetras(num) {
    const lista = ['Primer', 'Segundo', 'Tercer', 'Cuarto', 'Quinto', 'Sexto', 'Séptimo', 'Octavo', 'Noveno', 'Décimo',
                   'Undécimo', 'Duodécimo', 'Décimotercer', 'Décimocuarto', 'Décimoquinto', 'Décimosexto'];
    return lista[num - 1] || `${num}°`;
}
</script>
<script>
$(function() {
  const $input   = $('#buscador-producto');
  const $lista   = $('#lista-productos');
  let products   = [];
  let fuse; 
  let selectedIndex = -1; // para navegación con teclado

  // 2) Cargar array JS desde las <li>
  $lista.find('.dropdown-item').each(function(){
    const $it = $(this);
    products.push({
      id:      $it.data('id'),
      nombre:  $it.data('nombre'),
      modelo:  $it.data('modelo'),
      marca:   $it.data('marca'),
      precio:  parseFloat($it.data('precio')),
      imagen:  $it.data('imagen'),
      html:    this.outerHTML // plantilla para clonarlo
    });
  });

  // 3) Crear instancia Fuse
  fuse = new Fuse(products, {
    keys: ['nombre','modelo','marca'],
    threshold: 0.4,          // qué tan “fuzzy”
    includeScore: true
  });

  // 4) Función para mostrar sugerencias
  function renderSuggestions(list) {
    $lista.empty();
    list.slice(0,10).forEach(entry => {
      // entry podría ser { item, score } o directamente item
      const item = entry.item || entry;
      $lista.append(item.html);
    });
    $lista.slideDown(100);
    selectedIndex = -1;
    highlightItem(selectedIndex);
  }

  // 5) Debounce helper
  function debounce(fn, ms) {
    let t;
    return function(...args) {
      clearTimeout(t);
      t = setTimeout(() => fn.apply(this,args), ms);
    };
  }

  // 6) Highlight en teclado
  function highlightItem(idx) {
    $lista
      .find('.dropdown-item')
      .removeClass('highlighted')
      .eq(idx)
      .addClass('highlighted');
  }

  // Al enfocar: primeras 10 recomendaciones (p.ej. más vendidas)
  $input.on('focus', () => renderSuggestions(products));

  // Al perder foco fuera del dropdown
  $(document).on('click', e => {
    if (!$(e.target).closest('.dropdown-container').length) {
      $lista.slideUp(100);
    }
  });

  // Al escribir: fuzzy-search
  $input.on('input', debounce(function() {
    const q = $(this).val().trim();
    if (!q) {
      renderSuggestions(products);
    } else {
      const results = fuse.search(q);
      renderSuggestions(results);
    }
  }, 200));

  // Navegación con flechas y Enter
  $input.on('keydown', function(e) {
    const $items = $lista.find('.dropdown-item');
    if (!$items.length) return;

    if (e.key === 'ArrowDown') {
      e.preventDefault();
      selectedIndex = (selectedIndex + 1) % $items.length;
      highlightItem(selectedIndex);
    }
    else if (e.key === 'ArrowUp') {
      e.preventDefault();
      selectedIndex = (selectedIndex - 1 + $items.length) % $items.length;
      highlightItem(selectedIndex);
    }
    else if (e.key === 'Enter' && selectedIndex > -1) {
      e.preventDefault();
      $items.eq(selectedIndex).trigger('click');
    }
  });

  // Click / selección de un item
  $(document).on('click', '.dropdown-item', function(){
    const $it     = $(this);
    const id      = $it.data('id');
    const nombre  = $it.data('nombre');
    const modelo  = $it.data('modelo');
    const marca   = $it.data('marca');
    const precio  = parseFloat($it.data('precio'));
    const imagen  = $it.data('imagen');

    // Rellenar input y cerrar
    $input.val(`${nombre} — ${modelo} ${marca} ($${precio.toFixed(2)})`);
    $lista.slideUp(100);

    // Evitar duplicados
    if ($(`#tabla-productos tbody tr[data-id="${id}"]`).length) {
      return alert('Este producto ya ha sido agregado.');
    }

    // Agregar a la tabla (ajusta selector si tu tabla es distinta)
    const fila = `
      <tr data-id="${id}" data-precio="${precio}">
        <td><img src="${imagen}" width="50"></td>
        <td class="equipo">${nombre}</td>
        <td>${modelo}</td>
        <td>${marca}</td>
        <td>
          <input type="number" class="form-control cantidad"
                 name="productos[${id}][cantidad]" value="1" min="1"
                 onchange="actualizarSubtotal(this)">
        </td>
        <td class="subtotal">${precio.toFixed(2)}</td>
        <td>
          <input type="number" class="form-control sobreprecio"
                 name="productos[${id}][sobreprecio]" value="0" min="0"
                 onchange="actualizarSubtotal(this)">
        </td>
        <td>
          <button type="button"
                  class="btn btn-sm btn-danger"
                  onclick="eliminarFila(this)">
            Eliminar
          </button>
        </td>
        <input type="hidden" class="precio_unitario"
               name="productos[${id}][precio_unitario]"
               value="${precio}">
      </tr>`;
    $('#tabla-productos tbody').append(fila);
    actualizarTotal();
  });

});
</script>



@endsection 