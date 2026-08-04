@extends('layouts.app')

@section('content')
<link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600,700" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/prestamo.css') }}?v={{ time() }}">

<!-- Ajustes específicos + Toasts visibles -->
<style>
  /* --- Firma: evitar scroll/zoom y selección en móviles --- */
  .sig-wrap{
    touch-action: none;
    -webkit-user-select: none;
    user-select: none;
    overscroll-behavior: contain;
  }
  .sig-wrap canvas{
    display:block;
    width:100%;
    height:160px;
    background:#fff;
    border:1px dashed var(--line, #e9ecef);
    border-radius:12px;
  }
  body.drawing-lock{
    touch-action: none;
    overflow: hidden;
  }

  /* --- Toasts mínimos para asegurar visibilidad --- */
  .toasts{
    position: fixed;
    top: 12px;
    right: 12px;
    z-index: 99999;
    display: flex;
    flex-direction: column;
    gap: 10px;
    pointer-events: none;
  }
  .toasts .toast{
    pointer-events: auto;
    display:flex; align-items:center; gap:10px;
    min-width: 260px; max-width: 420px;
    padding:10px 12px; border-radius:10px;
    background:#fff; color:#2a2e35;
    box-shadow:0 10px 30px rgba(18,38,63,.12), 0 1px 0 rgba(0,0,0,.04);
    border:1px solid #e9ecef;
  }
  .toasts .toast .icon{ flex:0 0 18px }
  .toasts .toast .title{ font-weight:700; font-size:12px; opacity:.85 }
  .toasts .toast .msg{ font-size:13px; }
  .toasts .toast .close{
    margin-left:auto; border:0; background:transparent;
    font-size:18px; line-height:1; cursor:pointer; color:#7a7f87;
  }
  .toasts .toast .bar{
    position:absolute; left:0; bottom:0; height:2px; width:100%;
    background:linear-gradient(90deg, #48cfad, #0b4c8c);
    transform-origin:left center;
  }
  .toasts .toast.hide{ opacity:0; transform:translateY(-6px); transition:.16s ease; }
  .toasts .toast.warn{ border-color:#fde68a; background:#fffbeb; }
  .toasts .toast.error{ border-color:#fecaca; background:#fef2f2; }
  .toasts .toast.success{ border-color:#bbf7d0; background:#f0fdf4; }
</style>

<div class="edit-wrap" style="margin-top:25px;">
  <div class="panel">
    {{-- Encabezado --}}
    <div class="panel-head">
      <div class="hgroup">
        <h2>Nuevo paquete de préstamo</h2>
        <p>1) Datos del paquete y firma · 2) Escanear equipos por número de serie</p>
      </div>
      <a href="{{ route('prestamos.index') }}" class="back-link" title="Volver">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <polyline points="15 18 9 12 15 6"></polyline>
        </svg>
        Volver
      </a>
    </div>

    {{-- Stepper --}}
    <div class="stepper">
      <div class="dot active" data-step="1">1</div>
      <div class="line"></div>
      <div class="dot" data-step="2">2</div>
    </div>

    <form class="form" id="wizardForm" method="POST" action="{{ route('prestamos.store') }}">
      @csrf

      {{-- STEP 1 --}}
      <div class="step active" id="step-1">
        <div class="grid">

          {{-- Cliente -> buscador tipo dropdown --}}
          @php
            $clientesData = collect($clientes ?? \App\Models\Cliente::orderBy('nombre')->get())
              ->map(function($c){
                  $apellidos = trim(($c->apellido_paterno ?? '').' '.($c->apellido_materno ?? ''));
                  if ($apellidos === '') { $apellidos = trim(($c->apellidos ?? '') ?: ($c->apellido ?? '')); }
                  $nombreCompleto = trim(($c->nombre ?? '').' '.$apellidos);
                  return ['id'=>$c->id,'nombre'=>$nombreCompleto,'extra'=>$c->rfc ?? null];
              })->values()->toArray();
          @endphp

          <div>
            <div class="field combo" id="clienteCombo">
              <input type="text" id="clienteInput" placeholder="— Selecciona cliente —" autocomplete="off"
                    role="combobox" aria-expanded="false" aria-controls="clienteList" aria-autocomplete="list">
              <label>Cliente</label>
              <button type="button" class="clear-x" id="clienteClear" aria-label="Limpiar selección">✕</button>
              <input type="hidden" name="cliente_id" id="cliente_id" value="">
              <div class="dropdown" id="clienteList" role="listbox" aria-label="Resultados"></div>
            </div>
            @error('cliente_id')<div class="error">{{ $message }}</div>@enderror
          </div>

          {{-- Estado --}}
          <div>
            <div class="field">
              <select name="estado" required>
                <option value="activo" selected>Activo</option>
                <option value="devuelto">Devuelto</option>
                <option value="retrasado">Retrasado</option>
                <option value="cancelado">Cancelado</option>
                <option value="vendido">Vendido</option>
              </select>
              <label>Estado del paquete</label>
            </div>
          </div>

          {{-- Fechas --}}
          <div>
            <div class="field">
              <input type="date" name="fecha_prestamo" placeholder=" " required>
              <label>Fecha de salida</label>
            </div>
          </div>
          <div>
            <div class="field">
              <input type="date" name="fecha_devolucion_estimada" placeholder=" " required>
              <label>Fecha estimada de regreso</label>
            </div>
          </div>

          {{-- Observaciones --}}
          <div class="grid" style="grid-column:1/-1;">
            <div style="grid-column:1/-1">
              <div class="field">
                <textarea name="observaciones" placeholder=" "></textarea>
                <label>Observaciones (opcional)</label>
              </div>
            </div>
          </div>

          {{-- Firma --}}
          <div style="grid-column:1/-1">
            <div class="sigbox">
              <div class="small">Firma de quien registra</div>
              <div class="sig-wrap"><canvas id="sigPad"></canvas></div>
              <div class="sig-actions">
                <button type="button" class="btn btn-ghost btn-sm" id="sigUndo">Deshacer</button>
                <button type="button" class="btn btn-ghost btn-sm" id="sigClear">Limpiar</button>
                <span class="small" style="margin-left:auto">Requerida para continuar</span>
              </div>
            </div>
          </div>
        </div>

        <div class="actions">
          <button type="button" id="toStep2" class="btn btn-primary">Continuar a escaneo</button>
        </div>
      </div>

      {{-- STEP 2 --}}
      <div class="step" id="step-2">
        <div class="grid">
          <div>
            <div class="field">
              <input type="text" id="scanInput" placeholder=" " autocomplete="off"
                     inputmode="none" autocapitalize="off" spellcheck="false" enterkeyhint="done">
              <label>Escanea número de serie (Enter o Tab al final)</label>
            </div>

            <!-- Barra de control de captura -->
            <div class="scan-toolbar" style="display:flex;gap:10px;align-items:center;margin:6px 0 8px;">
              <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                <input type="checkbox" id="scanToggle" checked>
                <span class="small">Captura de escáner activa</span>
              </label>
              <button type="button" class="btn btn-ghost btn-sm" id="focusScan">Enfocar captura</button>
            </div>

            <div class="small">Se aceptan lecturas de pistola o captura manual.</div>
          </div>
          <div>
            <div class="field">
              <input type="text" value="0 equipos" id="countInfo" placeholder=" " readonly>
              <label>Total en paquete</label>
            </div>
          </div>
        </div>

        <div class="block" style="margin-top:20px;">
          <div class="table-wrap">
            <table class="table">
              <thead>
                <tr>
                  <th style="width:60px">#</th>
                  <th>Serie</th>
                  <th>Subtipo</th>
                  <th>Marca</th>
                  <th>Modelo</th>
                  <th style="text-align:right">Acciones</th>
                </tr>
              </thead>
              <tbody id="scanTbody"></tbody>
            </table>
          </div>
        </div>

        {{-- hidden fields --}}
        <div id="hiddenSerials"></div>
        <input type="hidden" name="firmaDigital" id="firmaDigital">
        <input type="hidden" name="firmaJson" id="firmaJson"><!-- ⬅️ para fallback en backend -->

        <div class="actions">
          <button type="button" class="btn btn-ghost" id="backStep1">Regresar</button>
          <button type="submit" class="btn btn-primary" id="saveBtn" disabled>Guardar paquete</button>
        </div>
      </div>
    </form>
  </div>
</div>

{{-- Toast container --}}
<div id="toasts" class="toasts" aria-live="polite" aria-atomic="true"></div>

{{-- Scripts --}}
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
/* ===== Toasts robustos (con fallback de estilos) ===== */
function getToastWrap(){
  let w = document.getElementById('toasts');
  if(!w){
    w = document.createElement('div');
    w.id = 'toasts';
    w.className = 'toasts';
    w.setAttribute('aria-live','polite');
    w.setAttribute('aria-atomic','true');
    document.body.appendChild(w);
  } else if (w.parentElement !== document.body) {
    document.body.appendChild(w); // evitar que quede dentro de un contenedor con overflow
  }
  return w;
}
function toast(msg, type='info', opts={}){
  try{
    const {title=null, duration=2600} = opts;
    const wrap = getToastWrap();
    const el = document.createElement('div');
    el.className = `toast ${type}`;
    el.setAttribute('role', type==='error' ? 'alert' : 'status');
    el.style.position = 'relative';

    const icons = {
      success: `<svg class="icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--ok,#22c55e)" stroke-width="2"><path d="M20 6L9 17l-5-5"/></svg>`,
      error:   `<svg class="icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--err,#ef4444)" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 8v5M12 16h.01"/></svg>`,
      warn:    `<svg class="icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--warn,#f59e0b)" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12" y2="17"/></svg>`,
      info:    `<svg class="icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--info,#0b4c8c)" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12" y2="8"/></svg>`
    };

    el.innerHTML = `
      ${icons[type]||icons.info}
      <div style="display:flex;flex-direction:column;gap:2px">
        ${title ? `<div class="title">${title}</div>` : ''}
        <div class="msg"></div>
      </div>
      <button class="close" aria-label="Cerrar">&times;</button>
      <div class="bar"></div>
    `;
    el.querySelector('.msg').textContent = msg;

    const close = ()=>{ el.classList.add('hide'); setTimeout(()=> el.remove(), 160); };
    el.querySelector('.close').addEventListener('click', close);
    wrap.appendChild(el);

    const bar = el.querySelector('.bar');
    if (bar && bar.animate) {
      bar.animate([{transform:'scaleX(1)'},{transform:'scaleX(0)'}], {duration, easing:'linear', fill:'forwards'});
    } else if (bar) {
      bar.style.transition = `transform ${duration}ms linear`;
      requestAnimationFrame(()=> bar.style.transform = 'scaleX(0)');
    }
    setTimeout(close, duration);
  }catch(err){
    console.error('Toast error:', err, msg);
    alert(msg); // último recurso si algo raro pasa
  }
}
window.toast = toast;

// Mostrar errores JS en toasts para depurar
window.addEventListener('error', (e)=> toast(e.message, 'error', {title:'Error JS'}));
window.addEventListener('unhandledrejection', (e)=> toast(String(e.reason), 'error', {title:'Promise rechazada'}));

/* ===== Stepper ===== */
function gotoStep(n){
  document.querySelectorAll('.step').forEach(s=>s.classList.remove('active'));
  document.getElementById('step-'+n).classList.add('active');
  document.querySelectorAll('.dot').forEach(d=>d.classList.remove('active'));
  document.querySelector('.dot[data-step="'+n+'"]').classList.add('active');
  window.scrollTo({top:0,behavior:'smooth'});
}

/* ===== Firma en canvas (móvil estable + nítida) ===== */
(() => {
  const pad  = document.getElementById('sigPad');
  const wrap = pad.parentElement;
  const ctx  = pad.getContext('2d');
  let drawing = false, strokes = [], last = null;

  function setupCanvas(){
    const dpr   = Math.max(window.devicePixelRatio || 1, 1);
    const cssW  = wrap.clientWidth;
    const cssH  = 160;
    pad.width   = Math.round(cssW * dpr);
    pad.height  = Math.round(cssH * dpr);
    pad.style.width  = cssW + 'px';
    pad.style.height = cssH + 'px';
    ctx.setTransform(dpr,0,0,dpr,0,0);
    ctx.lineWidth = 2;
    ctx.lineCap   = 'round';
    ctx.lineJoin  = 'round';
    ctx.strokeStyle = '#2a2e35';
    redraw();
  }

  function pos(e){
    const r = pad.getBoundingClientRect();
    return { x: e.clientX - r.left, y: e.clientY - r.top };
  }

  function redraw(){
    ctx.clearRect(0,0,pad.width,pad.height);
    ctx.beginPath();
    for(const s of strokes){
      for(let i=1;i<s.length;i++){
        ctx.moveTo(s[i-1].x, s[i-1].y);
        ctx.lineTo(s[i].x,   s[i].y);
      }
    }
    ctx.stroke();
  }

  pad.addEventListener('pointerdown', (e)=>{
    pad.setPointerCapture?.(e.pointerId);
    drawing = true;
    document.body.classList.add('drawing-lock');
    last = pos(e);
    strokes.push([last]);
    e.preventDefault();
  }, {passive:false});

  pad.addEventListener('pointermove', (e)=>{
    if(!drawing) return;
    const p = pos(e);
    ctx.beginPath(); ctx.moveTo(last.x, last.y); ctx.lineTo(p.x, p.y); ctx.stroke();
    strokes[strokes.length-1].push(p);
    last = p;
    e.preventDefault();
  }, {passive:false});

  function endDraw(e){
    if(!drawing) return;
    drawing = false;
    document.body.classList.remove('drawing-lock');
    try{ pad.releasePointerCapture?.(e.pointerId); }catch(_){}
    e.preventDefault();
  }
  pad.addEventListener('pointerup', endDraw, {passive:false});
  pad.addEventListener('pointercancel', endDraw, {passive:false});

  $('#sigClear').on('click', ()=>{ strokes=[]; redraw(); });
  $('#sigUndo').on('click',  ()=>{ strokes.pop(); redraw(); });

  // Helpers para submit
  window._sig_hasInk  = ()=> strokes.some(s=> s.length>1);
  window._sig_dataURL = ()=> pad.toDataURL('image/png');
  window._sig_export  = ()=> ({ w: pad.clientWidth, h: 160, lines: strokes.map(line => line.map(p => ({x:p.x, y:p.y}))) });

  window.addEventListener('resize', setupCanvas);
  setupCanvas();
})();

/* ===== Navegación ===== */
$('#toStep2').on('click', function(){
  const req = ['[name="fecha_prestamo"]','[name="fecha_devolucion_estimada"]'];
  for(const sel of req){
    if(!$(sel).val()){
      $(sel).focus();
      toast('Completa este campo para continuar.', 'warn', {title:'Falta información'});
      return;
    }
  }
  // 🔐 Validar cliente antes de ir al paso 2
  if(!$('#cliente_id').val()){
    toast('Selecciona un cliente para continuar.', 'warn', {title:'Falta cliente'});
    $('#clienteInput').focus();
    return;
  }
  // 🔐 Validar firma
  if(!window._sig_hasInk()){
    toast('Firma en el recuadro para avanzar al escaneo.', 'warn', {title:'Falta firma'});
    return;
  }
  gotoStep(2);
  focusScanIfActive();
});
$('#backStep1').on('click', ()=> { setScanPaused(true); gotoStep(1); });

/* ===== Utilidad: normalización de series (sin 4-3-3) ===== */
function normalizeSerie(input){
  if (!input) return '';
  let s = String(input).trim();
  s = s.replace(/^\s*(s\/?n|sn|serie|serial)\s*[:#\-]?\s*/i, ''); // prefijos
  s = s.replace(/[\/\\_.\s]+/g, '-');                             // separadores → '-'
  s = s.replace(/-+/g,'-').replace(/^-|-$/g,'');                  // colapsar y recortar
  return s;
}

/* ===== Escaneo con pistola (o manual) ===== */
const scanned = new Map(); // serie normalizada -> registro
const $tbody = $('#scanTbody'), $hidden = $('#hiddenSerials'), $countInfo = $('#countInfo');

const beep = (ok=true) => { try{
  const ctx = new (window.AudioContext||window.webkitAudioContext)();
  const o = ctx.createOscillator(), g = ctx.createGain();
  o.type = ok ? 'triangle' : 'sawtooth'; o.frequency.value = ok ? 880 : 220;
  o.connect(g); g.connect(ctx.destination); g.gain.setValueAtTime(.05, ctx.currentTime);
  o.start(); o.stop(ctx.currentTime+0.12);
}catch{} };

/* --- Pausar/reanudar captura del escáner --- */
let scanPaused = false;

function setScanPaused(v){
  scanPaused = !!v;
  $('#scanToggle').prop('checked', !scanPaused);
  $('#scanInput').prop('readonly', v);
  if (scanPaused){
    $('#scanInput').blur();
    toast('Captura pausada. Puedes navegar y escribir con normalidad.', 'info', {title:'Escáner desactivado'});
  } else {
    $('#scanInput').focus();
    toast('Captura activada. Listo para leer códigos.', 'success');
  }
}
$('#scanToggle').on('change', function(){ setScanPaused(!this.checked); });
$('#focusScan').on('click', function(){ if (scanPaused) setScanPaused(false); $('#scanInput').focus(); });
function focusScanIfActive(){ if (!scanPaused) $('#scanInput').focus(); }

/* ===== Tabla: render + quitar con data-serie exacto (sin .data()) ===== */
function renderTable(){
  $tbody.empty();
  let i=1;

  const escAttr = (s) => String(s)
    .replace(/&/g,'&amp;')
    .replace(/"/g,'&quot;')
    .replace(/</g,'&lt;')
    .replace(/>/g,'&gt;');

  const escHtml = (s) => $('<div/>').text(String(s)).html();

  scanned.forEach((reg, serie)=>{
    const safeAttr = escAttr(serie);
    const safeText = escHtml(serie);

    $tbody.append(`
      <tr>
        <td data-label="#">${i++}</td>
        <td data-label="Serie"><code>${safeText}</code></td>
        <td data-label="Subtipo">${reg?.subtipo_equipo ?? '-'}</td>
        <td data-label="Marca">${reg?.marca ?? '-'}</td>
        <td data-label="Modelo">${reg?.modelo ?? '-'}</td>
        <td data-label="Acciones" class="td-actions" style="text-align:right">
          <button type="button" class="btn btn-ghost btn-sm" data-serie="${safeAttr}">Quitar</button>
        </td>
      </tr>
    `);
  });

  $('#saveBtn').prop('disabled', scanned.size===0);
  $countInfo.val(`${scanned.size} equipos`);

  $hidden.empty();
  Array.from(scanned.keys()).forEach(s =>
    $hidden.append(`<input type="hidden" name="seriales[]" value="${$('<div/>').text(s).html()}">`)
  );
}

$tbody.on('click', function(e){
  const btn = e.target.closest('[data-serie]');
  if (!btn) return;
  const serie = btn.getAttribute('data-serie');
  scanned.delete(serie);
  renderTable();
  focusScanIfActive();
  toast('Equipo quitado del paquete.', 'info');
});

/* ===== Captura Enter/Tab: sin heurística, siempre procesa ===== */
function processScan(rawValue){
  const norm = normalizeSerie(rawValue);
  if(!norm){ toast('Serie vacía.', 'warn'); return; }

  if(scanned.has(norm)){
    beep(false);
    toast(`El número de serie ${norm} ya fue agregado.`, 'info');
    return;
  }

  $.ajax({
    method: 'POST',
    url: '{{ route('registros.lookup') }}',
    data: { numero_serie: norm, _token: '{{ csrf_token() }}' },
    success: function(res){
      scanned.set(norm, res.registro);
      renderTable();
      beep(true);
      toast(`Equipo agregado: ${norm}`, 'success', {title:'Registro encontrado'});
    },
    error: function(xhr){
      const msg = (xhr.responseJSON && xhr.responseJSON.msg) ? xhr.responseJSON.msg : 'No se pudo buscar el número de serie.';
      toast(`${msg} (${norm})`, 'error', {title:'No agregado'});
      beep(false);
      console.error('Lookup error', xhr);
    },
    complete: ()=> {
      $('#scanInput').val('');
      if (!scanPaused) $('#scanInput').focus();
    }
  });
}

$('#scanInput')
 .on('focus', function(){ this.select(); })
 .on('paste', e=> e.preventDefault())
 .on('keydown', function(e){
   if (scanPaused) return;
   if(e.key === 'Enter' || e.key === 'Tab'){
     e.preventDefault();
     const raw = this.value.trim();
     if(!raw) return;
     processScan(raw);
   }
 });

/* ===== Submit: adjuntar firma + JSON de trazos + regenerar ocultos + lock ===== */
let submitting = false;
$('#wizardForm').on('submit', function(e){
  if(submitting){
    e.preventDefault();
    return;
  }

  // 🔐 Validaciones finales
  if(scanned.size===0){
    e.preventDefault();
    toast('Agrega al menos un equipo antes de guardar.', 'warn', {title:'Paquete vacío'});
    gotoStep(2);
    return;
  }
  if(!$('#cliente_id').val()){
    e.preventDefault();
    toast('Selecciona un cliente para guardar.', 'warn', {title:'Falta cliente'});
    gotoStep(1);
    $('#clienteInput').focus();
    return;
  }
  if(!window._sig_hasInk()){
    e.preventDefault();
    toast('Falta la firma para guardar.', 'warn', {title:'Falta firma'});
    gotoStep(1);
    return;
  }

  // Asegurar inputs ocultos y firma frescos
  renderTable(); // regenera seriales[]
  $('#firmaDigital').val(window._sig_dataURL());
  try {
    if (window._sig_export) {
      $('#firmaJson').val(JSON.stringify(window._sig_export()));
    }
  } catch(err){
    console.warn('No se pudo serializar firmaJson:', err);
    $('#firmaJson').val('');
  }

  // Lock de envío
  submitting = true;
  const $btn = $('#saveBtn');
  $btn.data('orig', $btn.html());
  $btn.prop('disabled', true).attr('aria-busy','true').html('Guardando…');
});
/* Consejo: si el backend redirige con error/validación, el lock se libera al recargar.
   Si haces submit por AJAX, habría que revertir el lock en error. */

/* ====== Cliente Combo (buscador dropdown) ====== */
(function ClienteCombo(){
  const DATA = @json($clientesData);
  const wrap = document.getElementById('clienteCombo');
  const input = document.getElementById('clienteInput');
  const list  = document.getElementById('clienteList');
  const hid   = document.getElementById('cliente_id');
  const clearBtn = document.getElementById('clienteClear');

  let open = false, activeIndex = -1, filtered = DATA.slice();

  function setOpen(v){
    open = v;
    wrap.classList.toggle('open', v);
    input.setAttribute('aria-expanded', v ? 'true' : 'false');
  }
  function escapeHtml(s){ return String(s).replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[m])); }
  function render(items){
    list.innerHTML = '';
    if(items.length === 0){
      list.innerHTML = `<div class="muted">Sin resultados</div>`;
      return;
    }
    items.forEach((it, idx)=>{
      const div = document.createElement('div');
      div.className = 'option' + (idx===activeIndex?' active':'');
      div.setAttribute('role','option');
      div.setAttribute('data-id', it.id);
      div.setAttribute('data-name', it.nombre);
      div.innerHTML = `<span>${escapeHtml(it.nombre)}</span>` + (it.extra ? `<span class="badge">${escapeHtml(it.extra)}</span>` : '');
      div.addEventListener('mousedown', (e)=>{ e.preventDefault(); select(idx); });
      list.appendChild(div);
    });
  }
  function normalizeTxt(s){ return (s||'').toLowerCase().normalize('NFD').replace(/\p{Diacritic}/gu,''); }
  function filter(q){
    q = normalizeTxt(q);
    filtered = DATA.filter(it=> normalizeTxt(it.nombre).includes(q) || normalizeTxt(it.extra).includes(q));
    activeIndex = filtered.length ? 0 : -1;
    render(filtered);
  }
  function select(idx){
    if(idx < 0 || idx >= filtered.length) return;
    const it = filtered[idx];
    input.value = it.nombre;
    hid.value = it.id;
    wrap.classList.add('has-value');
    setOpen(false);
    toast(`Cliente seleccionado: ${it.nombre}`, 'info');
  }
  function clearSelection(){
    input.value = '';
    hid.value = '';
    wrap.classList.remove('has-value');
    filter('');
    toast('Selección de cliente limpia.', 'info');
  }
  function moveActive(delta){
    if(!filtered.length) return;
    activeIndex = (activeIndex + delta + filtered.length) % filtered.length;
    render(filtered);
  }

  filter('');

  input.addEventListener('focus', ()=>{ setOpen(true); filter(input.value); });
  input.addEventListener('input', ()=>{ hid.value=''; wrap.classList.remove('has-value'); setOpen(true); filter(input.value); });
  input.addEventListener('keydown', (e)=>{
    if(!open && ['ArrowDown','ArrowUp'].includes(e.key)){ setOpen(true); e.preventDefault(); return; }
    switch(e.key){
      case 'ArrowDown': e.preventDefault(); moveActive(1); break;
      case 'ArrowUp':   e.preventDefault(); moveActive(-1); break;
      case 'Enter':     e.preventDefault(); select(activeIndex); break;
      case 'Escape':    setOpen(false); break;
    }
  });
  input.addEventListener('blur', ()=>{ setTimeout(()=> setOpen(false), 120); });
  clearBtn.addEventListener('click', clearSelection);
  document.addEventListener('click', (e)=>{ if(!wrap.contains(e.target)) setOpen(false); });
})();
</script>
@endsection
