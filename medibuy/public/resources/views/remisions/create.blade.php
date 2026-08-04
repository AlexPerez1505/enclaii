@extends('layouts.app')

@section('title', 'Remisión / Mantenimiento')
@section('titulo', 'Remisión / Mantenimiento')

@section('content')

{{-- Si ya cargas Bootstrap/Icons en layouts.app, puedes quitar estos CDN --}}
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<link rel="stylesheet" href="{{ asset('css/ordenm.css') }}?v={{ time() }}">

<style>
  :root{
    --bg:#f6f7fb;
    --card:#ffffff;
    --ink:#0b1220;
    --muted:#64748b;
    --line:#e5e7eb;
    --brand:#2563eb;
    --brand2:#1d4ed8;
    --soft: rgba(37,99,235,.10);
    --shadow: 0 18px 60px rgba(2,6,23,.08);
    --shadow2: 0 10px 30px rgba(2,6,23,.06);
    --radius: 18px;
  }
  body{ background: var(--bg); }
  .wrap{ max-width: 1250px; margin: 18px auto; padding: 0 14px; }

  .hero{
    background: linear-gradient(180deg, rgba(37,99,235,.12), rgba(255,255,255,0));
    border: 1px solid rgba(226,232,240,.95);
    border-radius: var(--radius);
    box-shadow: var(--shadow);
    overflow:hidden;
  }
  .hero-head{
    padding: 16px 16px 10px 16px;
    border-bottom: 1px solid var(--line);
  }
  .hero-title{
    display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap;
  }
  .hero-title h1{
    margin:0;
    font-size: 16px;
    font-weight: 900;
    color: var(--ink);
    display:flex; align-items:center; gap:10px;
    letter-spacing:.2px;
  }
  .chip{
    display:inline-flex; align-items:center; gap:8px;
    padding: 8px 10px;
    border-radius: 999px;
    border: 1px solid rgba(37,99,235,.25);
    background: rgba(37,99,235,.08);
    color: var(--brand2);
    font-weight: 900;
    font-size: 12px;
    white-space: nowrap;
  }
  .hero-sub{
    color: var(--muted);
    font-size: 12px;
    font-weight: 700;
    margin-top: 6px;
  }

  .grid{
    display:grid;
    grid-template-columns: 1.6fr .9fr;
    gap: 14px;
    padding: 14px;
  }
  @media (max-width: 992px){
    .grid{ grid-template-columns: 1fr; }
  }

  .panel{
    background: var(--card);
    border: 1px solid rgba(226,232,240,.95);
    border-radius: var(--radius);
    box-shadow: var(--shadow2);
    overflow:hidden;
  }
  .panel-head{
    padding: 14px 14px 10px 14px;
    border-bottom: 1px solid var(--line);
    display:flex; align-items:center; justify-content:space-between; gap:10px; flex-wrap:wrap;
  }
  .panel-head .t{
    font-size: 13px;
    font-weight: 1000;
    color: var(--ink);
    display:flex; align-items:center; gap:10px;
  }
  .panel-body{ padding: 14px; }

  .form-label{ font-weight: 900; font-size: 12px; color: var(--ink); }
  .form-control, .form-select, textarea{
    border-radius: 14px !important;
    border: 1px solid var(--line) !important;
  }
  .help{ color: var(--muted); font-size: 12px; font-weight: 700; }

  .btn-soft{
    border-radius: 14px;
    border: 1px solid rgba(37,99,235,.22);
    background: rgba(37,99,235,.08);
    color: var(--brand2);
    font-weight: 1000;
  }
  .btn-soft:hover{ background: rgba(37,99,235,.12); color: var(--brand2); }

  .btn-danger-soft{
    border-radius: 14px;
    border: 1px solid rgba(239,68,68,.22);
    background: rgba(239,68,68,.08);
    color: #b91c1c;
    font-weight: 1000;
  }
  .btn-danger-soft:hover{ background: rgba(239,68,68,.12); color: #b91c1c; }

  .search-wrap{ position: relative; }
  .search-icon{
    position:absolute; left: 12px; top: 50%; transform: translateY(-50%);
    color: var(--muted);
    pointer-events:none;
  }
  .search-input{ padding-left: 40px !important; }

  #cliente_sugerencias{
    border-radius: 14px;
    overflow: hidden;
    border: 1px solid var(--line);
  }
  #cliente_sugerencias .list-group-item{
    border: 0;
    border-bottom: 1px solid rgba(226,232,240,.75);
    padding: 10px 12px;
    font-weight: 800;
  }
  #cliente_sugerencias .list-group-item:last-child{ border-bottom:0; }
  #cliente_sugerencias .list-group-item:hover{ background: rgba(37,99,235,.08); }

  .item-card{
    border: 1px solid rgba(226,232,240,.95);
    border-radius: 18px;
    background: #fff;
    box-shadow: var(--shadow2);
    padding: 12px;
  }
  .item-top{
    display:flex; align-items:center; justify-content:space-between;
    gap: 10px; margin-bottom: 10px;
  }
  .item-badge{
    display:inline-flex; align-items:center; gap:10px;
    font-weight: 1000; color: var(--ink);
    font-size: 12px;
  }
  .dot{
    width: 9px; height: 9px; border-radius: 999px;
    background: var(--brand);
    box-shadow: 0 0 0 5px var(--soft);
  }

  .summary{
    position: sticky;
    top: 14px;
  }
  @media (max-width: 992px){
    .summary{ position: static; }
  }
  .sum-row{
    display:flex; align-items:center; justify-content:space-between;
    padding: 10px 0;
    border-bottom: 1px dashed rgba(226,232,240,.95);
    font-weight: 900;
    color: var(--ink);
  }
  .sum-row:last-child{ border-bottom: 0; }
  .sum-k{ color: var(--muted); font-weight: 1000; font-size: 12px; }
  .sum-v{ font-weight: 1000; }

  .sum-total{
    margin-top: 10px;
    padding: 12px;
    border-radius: 16px;
    border: 1px solid rgba(37,99,235,.22);
    background: rgba(37,99,235,.08);
  }
  .sum-total .big{
    font-size: 20px;
    font-weight: 1100;
    letter-spacing: .2px;
  }

  .actions{
    display:flex; gap:10px; flex-wrap:wrap;
    margin-top: 12px;
  }
  .btn-strong{
    border-radius: 14px;
    font-weight: 1100;
    padding: 10px 14px;
  }
</style>

<div class="wrap">
  <div class="hero">
    <div class="hero-head">
      <div class="hero-title">
        <h1><i class="bi bi-clipboard2-check"></i> Remisión / Mantenimiento</h1>
        <span class="chip"><i class="bi bi-lock"></i> Flujo tipo propuesta (Total + Mensualidad)</span>
      </div>
      <div class="hero-sub">
        Selecciona cliente, agrega ítems, configura envío e IVA, elige meses y guarda.
      </div>
    </div>

    <div class="grid">
      {{-- PANEL IZQUIERDO: FORM --}}
      <div class="panel">
        <div class="panel-head">
          <div class="t"><i class="bi bi-ui-checks-grid"></i> Captura</div>
          <button type="button" class="btn btn-soft btn-sm" data-bs-toggle="modal" data-bs-target="#modal_formulario">
            <i class="bi bi-person-plus me-1"></i> Nuevo cliente
          </button>
        </div>

        <div class="panel-body">
          <form method="POST" action="{{ route('remisions.store') }}" id="form-remision" novalidate>
            @csrf

            {{-- CLIENTE --}}
            <div class="mb-3">
              <label class="form-label">Cliente</label>
              <div class="search-wrap">
                <span class="search-icon"><i class="bi bi-search"></i></span>
                <input type="text" id="cliente_busqueda" class="form-control search-input"
                       placeholder="Escribe nombre o apellido…" autocomplete="off">
                <input type="hidden" name="cliente_id" id="cliente_id">

                <ul id="cliente_sugerencias"
                    class="list-group position-absolute w-100 mt-2 shadow-sm"
                    style="z-index: 1300; display:none;"></ul>
              </div>
              <div class="help mt-2">Selecciona de la lista para evitar errores.</div>
            </div>

            <hr class="my-4" style="border-color: var(--line);">

            {{-- ÍTEMS --}}
            <div class="d-flex align-items-center justify-content-between gap-2 flex-wrap mb-2">
              <div class="fw-black" style="font-weight:1100; color:var(--ink);">
                <i class="bi bi-box-seam me-1"></i> Ítems
              </div>
              <button type="button" id="add-item" class="btn btn-soft btn-sm">
                <i class="bi bi-plus-circle me-1"></i> Añadir ítem
              </button>
            </div>

            <div id="items-container" class="d-grid gap-3">
              <div class="item item-card" data-index="0">
                <div class="item-top">
                  <div class="item-badge">
                    <span class="dot"></span> Ítem <span class="item-number">1</span>
                  </div>
                  <button type="button" class="btn btn-danger-soft btn-sm remove-item">
                    <i class="bi bi-trash3 me-1"></i> Eliminar
                  </button>
                </div>

                <div class="row g-3 align-items-end">
                  <div class="col-6 col-lg-2">
                    <label class="form-label">Cantidad</label>
                    <input type="number" min="0" step="1" name="items[0][cantidad]" class="form-control cantidad" required>
                  </div>

                  <div class="col-6 col-lg-2">
                    <label class="form-label">Unidad</label>
                    <input type="text" name="items[0][unidad]" class="form-control unidad" placeholder="pz, serv" required>
                  </div>

                  <div class="col-12 col-lg-4">
                    <label class="form-label">Nombre</label>
                    <input type="text" name="items[0][nombre_item]" class="form-control nombre_item"
                           placeholder="Ej. Mantenimiento preventivo" required>
                  </div>

                  <div class="col-12 col-lg-4">
                    <label class="form-label">Descripción</label>
                    <textarea name="items[0][descripcion_item]" class="form-control descripcion_item" rows="1"
                              placeholder="Detalles (opcional)"></textarea>
                  </div>

                  <div class="col-12 col-lg-4">
                    <label class="form-label">Precio unitario</label>
                    <div class="input-group">
                      <span class="input-group-text" style="border-radius:14px 0 0 14px; border-color:var(--line); background:#f8fafc; font-weight:1000; color:var(--muted);">$</span>
                      <input type="number" min="0" step="0.01" name="items[0][importe_unitario]"
                             class="form-control importe_unitario" required>
                    </div>
                  </div>

                  <div class="col-12 col-lg-8">
                    <div class="help mt-2">
                      Tip: captura cantidad y precio para calcular automáticamente.
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <hr class="my-4" style="border-color: var(--line);">

            {{-- CONDICIONES: IVA + ENVÍO + MESES --}}
            <div class="row g-3">
              <div class="col-12 col-lg-4">
                <label class="form-label">IVA</label>
                <div class="form-check form-switch">
                  <input type="hidden" name="aplicar_iva" value="0">
                  <input class="form-check-input" type="checkbox" id="aplicar_iva" name="aplicar_iva" value="1">
                  <label class="form-check-label fw-semibold" for="aplicar_iva">Aplicar IVA (16%)</label>
                </div>
                <div class="help">Actívalo si corresponde.</div>
              </div>

              <div class="col-12 col-lg-4">
                <label class="form-label">Envío</label>
                <div class="form-check form-switch">
                  <input type="hidden" name="tiene_envio" value="0">
                  <input class="form-check-input" type="checkbox" id="tiene_envio" name="tiene_envio" value="1">
                  <label class="form-check-label fw-semibold" for="tiene_envio">Agregar costo de envío</label>
                </div>
                <div class="help">Si el cliente requiere entrega.</div>
              </div>

              <div class="col-12 col-lg-4">
                <label class="form-label">Meses a pagar</label>
                <select class="form-select" name="meses_a_pagar" id="meses_a_pagar">
                  <option value="">Seleccionar…</option>
                  @for($m=1;$m<=24;$m++)
                    <option value="{{ $m }}">{{ $m }} mes{{ $m>1?'es':'' }}</option>
                  @endfor
                </select>
                <div class="help">Se calcula la mensualidad automáticamente.</div>
              </div>

              <div class="col-12 col-lg-4">
                <label class="form-label">Costo de envío</label>
                <div class="input-group">
                  <span class="input-group-text" style="border-radius:14px 0 0 14px; border-color:var(--line); background:#f8fafc; font-weight:1000; color:var(--muted);">$</span>
                  <input type="number" min="0" step="0.01" class="form-control" name="envio_costo" id="envio_costo" value="0.00" disabled>
                </div>
              </div>

              <div class="col-12 col-lg-8">
                <label class="form-label">Dirección de envío (opcional)</label>
                <input type="text" class="form-control" name="envio_direccion" id="envio_direccion" placeholder="Calle, número, colonia, referencias…" disabled>
              </div>
            </div>

            {{-- BOTONES --}}
            <div class="actions justify-content-end">
              <a href="/remisions" class="btn btn-light btn-strong" style="border:1px solid var(--line);">
                <i class="bi bi-arrow-left-circle me-1"></i> Volver
              </a>
              <button type="submit" class="btn btn-primary btn-strong" style="background:var(--brand); border-color:var(--brand);">
                <i class="bi bi-save2 me-1"></i> Guardar
              </button>
            </div>

          </form>
        </div>
      </div>

      {{-- PANEL DERECHO: RESUMEN --}}
      <div class="panel summary">
        <div class="panel-head">
          <div class="t"><i class="bi bi-receipt"></i> Resumen</div>
          <span class="chip" style="background:rgba(2,6,23,.04); border-color:rgba(2,6,23,.08); color:var(--muted);">
            <i class="bi bi-calculator"></i> Auto-cálculo
          </span>
        </div>

        <div class="panel-body">
          <div class="sum-row">
            <div class="sum-k">Subtotal</div>
            <div class="sum-v">$<span id="subtotal">0.00</span></div>
          </div>
          <div class="sum-row">
            <div class="sum-k">IVA (16%)</div>
            <div class="sum-v">$<span id="iva">0.00</span></div>
          </div>
          <div class="sum-row">
            <div class="sum-k">Envío</div>
            <div class="sum-v">$<span id="envio_show">0.00</span></div>
          </div>

          <div class="sum-total mt-3">
            <div class="d-flex align-items-end justify-content-between">
              <div>
                <div class="sum-k">Total</div>
                <div class="big">$<span id="total">0.00</span></div>
              </div>
              <div class="text-end">
                <div class="sum-k">Mensualidad</div>
                <div class="big" style="font-size:18px;">$<span id="mensualidad">0.00</span></div>
              </div>
            </div>
            <div class="help mt-2">
              Mensualidad = Total / Meses (si seleccionas meses).
            </div>
          </div>

          <div class="mt-3 p-3" style="border:1px solid rgba(226,232,240,.95); border-radius:16px; background:#fff;">
            <div class="sum-k mb-1">Validación rápida</div>
            <div class="help">
              • Cliente seleccionado<br>
              • Al menos 1 ítem con cantidad y precio<br>
              • Envío: si está activo, costo válido
            </div>
          </div>
        </div>
      </div>
    </div> {{-- grid --}}
  </div> {{-- hero --}}
</div>

{{-- MODAL REGISTRAR CLIENTE --}}
<form id="form-cliente" method="POST" action="{{ route('clientes.store') }}">
  @csrf
  <input type="hidden" name="redirect_to" value="{{ url()->current() }}">

  <div class="modal fade" id="modal_formulario" tabindex="-1" aria-labelledby="createClientModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content" style="border-radius:16px; overflow:hidden; border:1px solid var(--line);">
        <div class="modal-header" style="background: rgba(37,99,235,.08); border-bottom:1px solid var(--line);">
          <h5 class="modal-title fw-bold" id="createClientModalLabel">
            <i class="bi bi-person-plus me-2"></i> Registrar Cliente
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label for="nombre" class="form-label">Nombre</label>
              <input type="text" class="form-control text-uppercase" id="nombre" name="nombre" placeholder="Nombre" required>
            </div>
            <div class="col-md-6">
              <label for="apellido" class="form-label">Apellido</label>
              <input type="text" class="form-control text-uppercase" id="apellido" name="apellido" placeholder="Apellido" required>
            </div>

            <div class="col-md-6">
              <label for="telefono" class="form-label">Teléfono</label>
              <input type="tel" inputmode="numeric" class="form-control" id="telefono" name="telefono" placeholder="Ej. 72 9390 3384" maxlength="12" required>
              <div id="error-telefono" class="text-danger mt-1" style="display:none; font-weight:800; font-size:12px;">
                <i class="bi bi-exclamation-triangle me-1"></i> El teléfono ya está registrado o es inválido.
              </div>
            </div>

            <div class="col-md-6">
              <label for="email" class="form-label">Email</label>
              <input type="email" class="form-control" id="email" name="email" placeholder="correo@dominio.com" required>
              <div id="error-email" class="text-danger mt-1" style="display:none; font-weight:800; font-size:12px;">
                <i class="bi bi-exclamation-triangle me-1"></i> El correo ya está registrado o es inválido.
              </div>
            </div>

            <div class="col-12">
              <label for="comentarios" class="form-label">Dirección</label>
              <textarea id="comentarios" name="comentarios" class="form-control text-uppercase" placeholder="Dirección / referencias (opcional)" rows="3"></textarea>
            </div>
          </div>
        </div>

        <div class="modal-footer" style="border-top:1px solid var(--line);">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border:1px solid var(--line); border-radius:12px; font-weight:900;">
            Cancelar
          </button>
          <button type="submit" class="btn btn-primary" style="border-radius:12px; font-weight:1000;">
            <i class="bi bi-check2-circle me-1"></i> Agregar
          </button>
        </div>
      </div>
    </div>
  </div>
</form>

{{-- Modal cliente creado --}}
<div class="modal fade" id="cliente_creado" tabindex="-1" aria-labelledby="ClienteCreadoLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius:16px; overflow:hidden; border:1px solid var(--line);">
      <div class="modal-header" style="background: rgba(22,163,74,.10); border-bottom:1px solid var(--line);">
        <h5 class="modal-title fw-bold" id="ClienteCreadoLabel">
          <i class="bi bi-check-circle me-2"></i> ¡Cliente guardado exitosamente!
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body text-center">
        <img src="{{ asset('images/confirmar.jpeg') }}" alt="Confirmación"
             style="width:110px; height:auto; border-radius:14px; border:1px solid var(--line);" class="mb-3">
        <p class="mb-0" style="color:var(--ink); font-weight:800;">
          El cliente se registró correctamente. Ahora puedes seleccionarlo.
        </p>
        <div class="mt-2" style="color:var(--muted); font-size:12px; font-weight:800;">
          Grupo MediBuy
        </div>
      </div>
      <div class="modal-footer d-flex justify-content-center" style="border-top:1px solid var(--line);">
        <button class="btn btn-success px-4" style="border-radius:12px; font-weight:1000;" onclick="cerrarModalCreado()">
          Listo
        </button>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

{{-- =========================
   JS: Items + Totales + Envío + Meses + Autocomplete
   ========================= --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
  let index = 1;

  const $items = document.getElementById('items-container');
  const $add   = document.getElementById('add-item');

  const $iva = document.getElementById('aplicar_iva');
  const $envioToggle = document.getElementById('tiene_envio');
  const $envioCosto  = document.getElementById('envio_costo');
  const $envioDir    = document.getElementById('envio_direccion');
  const $meses       = document.getElementById('meses_a_pagar');

  const fmt = (n) => (Number(n || 0)).toFixed(2);

  function refreshItemNumbers(){
    const items = document.querySelectorAll('#items-container .item');
    items.forEach((item, i) => {
      item.querySelector('.item-number').textContent = String(i + 1);
      const delBtn = item.querySelector('.remove-item');
      if(delBtn) delBtn.style.display = (items.length > 1) ? 'inline-flex' : 'none';
    });
  }

  function getEnvio(){
    const on = $envioToggle?.checked;
    const costo = on ? (parseFloat($envioCosto?.value) || 0) : 0;
    return { on, costo };
  }

  function calcularTotales() {
    let subtotal = 0;

    document.querySelectorAll('#items-container .item').forEach(item => {
      const cantidad = parseFloat(item.querySelector('.cantidad')?.value) || 0;
      const precio   = parseFloat(item.querySelector('.importe_unitario')?.value) || 0;
      subtotal += (cantidad * precio);
    });

    const iva = ($iva?.checked) ? (subtotal * 0.16) : 0;

    const envio = getEnvio().costo;

    const total = subtotal + iva + envio;

    const meses = parseInt($meses?.value || '0', 10);
    const mensualidad = (meses > 0) ? (total / meses) : 0;

    document.getElementById('subtotal').textContent   = fmt(subtotal);
    document.getElementById('iva').textContent        = fmt(iva);
    document.getElementById('envio_show').textContent = fmt(envio);
    document.getElementById('total').textContent      = fmt(total);
    document.getElementById('mensualidad').textContent= fmt(mensualidad);
  }

  // Toggle envío (habilita/inhabilita inputs)
  function syncEnvioUI(){
    const on = $envioToggle?.checked;
    if($envioCosto){
      $envioCosto.disabled = !on;
      if(!on) $envioCosto.value = '0.00';
    }
    if($envioDir){
      $envioDir.disabled = !on;
      if(!on) $envioDir.value = '';
    }
    calcularTotales();
  }

  // Añadir ítem
  $add?.addEventListener('click', function () {
    const original = document.querySelector('#items-container .item');
    const clone = original.cloneNode(true);
    clone.setAttribute('data-index', index);

    // limpiar valores
    clone.querySelectorAll('input, textarea').forEach(el => el.value = '');

    // actualizar name items[0] -> items[index]
    clone.querySelectorAll('input, textarea').forEach(el => {
      const name = el.getAttribute('name');
      if (name) el.setAttribute('name', name.replace(/\[\d+\]/, `[${index}]`));
    });

    $items.appendChild(clone);
    index++;

    refreshItemNumbers();
    calcularTotales();
  });

  // eliminar ítem (delegado)
  $items?.addEventListener('click', function (e) {
    const btn = e.target.closest('.remove-item');
    if(!btn) return;

    const items = document.querySelectorAll('#items-container .item');
    if(items.length > 1){
      btn.closest('.item').remove();
      refreshItemNumbers();
      calcularTotales();
    }
  });

  // Recalcular en cambios
  $items?.addEventListener('input', calcularTotales);
  $iva?.addEventListener('change', calcularTotales);
  $meses?.addEventListener('change', calcularTotales);
  $envioCosto?.addEventListener('input', calcularTotales);
  $envioToggle?.addEventListener('change', syncEnvioUI);

  // Validación submit: cliente seleccionado + envío costo si toggle activo
  document.getElementById('form-remision')?.addEventListener('submit', function (e) {
    const clienteId = (document.getElementById('cliente_id')?.value || '').trim();
    if(!clienteId){
      e.preventDefault();
      document.getElementById('cliente_busqueda')?.focus();
      alert('Selecciona un cliente de la lista antes de guardar.');
      return;
    }

    // Validación envío
    const envioOn = $envioToggle?.checked;
    const envioCosto = parseFloat($envioCosto?.value || '0');
    if(envioOn && (!envioCosto || envioCosto < 0)){
      e.preventDefault();
      $envioCosto?.focus();
      alert('Captura un costo de envío válido.');
      return;
    }
  });

  refreshItemNumbers();
  syncEnvioUI();
  calcularTotales();
});
</script>

<script>
  // --- Autocomplete Clientes ---
  const clientes = @json($clientes);

  const inputBusqueda = document.getElementById('cliente_busqueda');
  const inputHiddenId = document.getElementById('cliente_id');
  const sugerencias   = document.getElementById('cliente_sugerencias');

  function hideSuggestions(){
    sugerencias.style.display = 'none';
    sugerencias.innerHTML = '';
  }

  function showSuggestions(list){
    sugerencias.innerHTML = '';
    list.slice(0, 6).forEach(cliente => {
      const li = document.createElement('li');
      li.className = 'list-group-item list-group-item-action';
      li.style.cursor = 'pointer';
      li.innerHTML = `
        <div class="d-flex align-items-center justify-content-between">
          <div><i class="bi bi-person me-2 text-primary"></i>${cliente.nombre} ${cliente.apellido}</div>
          <small class="text-muted fw-semibold">ID: ${cliente.id}</small>
        </div>
      `;
      li.addEventListener('click', () => {
        inputBusqueda.value = `${cliente.nombre} ${cliente.apellido}`;
        inputHiddenId.value = cliente.id;
        hideSuggestions();
      });
      sugerencias.appendChild(li);
    });
    sugerencias.style.display = 'block';
  }

  inputBusqueda?.addEventListener('input', function () {
    const query = (this.value || '').toLowerCase().trim();
    inputHiddenId.value = ''; // invalida hasta seleccionar

    if (!query) return hideSuggestions();

    const results = clientes.filter(c =>
      (`${c.nombre} ${c.apellido}`).toLowerCase().includes(query)
    );

    if(results.length) showSuggestions(results);
    else hideSuggestions();
  });

  inputBusqueda?.addEventListener('keydown', function(e){
    if(e.key === 'Enter' && sugerencias.style.display === 'block'){
      e.preventDefault();
      const first = sugerencias.querySelector('.list-group-item');
      if(first) first.click();
    }
  });

  document.addEventListener('click', function (e) {
    if (!e.target.closest('.search-wrap')) hideSuggestions();
  });
</script>

@if(session('cliente_creado'))
<script>
  window.addEventListener('DOMContentLoaded', () => {
    const modal = new bootstrap.Modal(document.getElementById('cliente_creado'));
    modal.show();
  });

  function cerrarModalCreado() {
    const el = document.getElementById('cliente_creado');
    const instance = bootstrap.Modal.getInstance(el);
    instance.hide();
  }
</script>
@endif

<script>
document.addEventListener("DOMContentLoaded", function () {
  const telefonoInput = document.getElementById("telefono");
  const emailInput = document.getElementById("email");

  telefonoInput?.addEventListener("input", function () {
    let value = this.value.replace(/\D/g, "").substring(0, 10);
    if (value.length > 6) this.value = value.replace(/(\d{2})(\d{4})(\d{1,4})/, "$1 $2 $3");
    else if (value.length > 2) this.value = value.replace(/(\d{2})(\d{1,4})/, "$1 $2");
    else this.value = value;
  });

  telefonoInput?.addEventListener("blur", function () {
    validarCampo('telefono', this.value.replace(/\D/g, ""), 'error-telefono');
  });

  emailInput?.addEventListener("blur", function () {
    validarCampo('email', this.value.trim(), 'error-email');
  });

  function validarCampo(tipo, valor, errorId) {
    if (!valor) return;

    fetch(`/validar-${tipo}?valor=${encodeURIComponent(valor)}`)
      .then(r => r.json())
      .then(data => {
        document.getElementById(errorId).style.display = data.existe ? 'block' : 'none';
      })
      .catch(err => console.error('Error en validación:', err));
  }
});
</script>

@endsection
