@extends('layouts.app')

@section('title', 'Checklists')
@section('titulo', 'Checklists')

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
<link rel="stylesheet" href="{{ asset('css/ingenieria.css') }}?v={{ time() }}">

<div class="container container-page py-4">
  <h3 class="mb-4 fw-bold page-title text-primary">
    Ingeniería: Verifica productos por código de barras o QR
  </h3>

  <div class="row verif-layout g-4">
    <!-- ============ IZQUIERDA: Pendientes ============ -->
    <div class="col-12 col-lg-5">
      <div class="card verif-sticky">
        <div class="card-body">
          <p class="helper mb-2">
            Escanea el <b>número de serie</b> con tu lector/QR. Se moverán a la lista de verificados.
          </p>

          <!-- input oculto para lector -->
          <input id="barcode-input" type="text" autocomplete="off" inputmode="text" class="hidden-scanner">

          <div class="table-responsive">
            <table class="table table-borderless clean align-middle">
              <thead class="sticky-top">
                <tr>
                  <th>#</th>
                  <th>Equipo</th>
                  <th>Número de serie</th>
                  <th>Verificación</th>
                </tr>
              </thead>
              <tbody id="tabla-pendientes">
                @foreach($productos as $p)
                <tr data-serie="{{ $p->numero_serie }}"
                    data-tipo="{{ strtolower($p->tipo_equipo) }}"
                    data-producto="{{ $p->marca }} {{ $p->modelo }}">
                  <td class="text-muted">{{ $loop->iteration }}</td>
                  <td class="small">
                    {{ $p->tipo_equipo }}<br>
                    <span class="text-muted">{{ $p->marca }} {{ $p->modelo }}</span>
                  </td>
                  <td class="serie">{{ $p->numero_serie }}</td>
                  <td>
                    <span class="badge-chip chip-pend">
                      <span class="dot"></span>
                      Pendiente
                    </span>
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>

          <!-- Progreso -->
          <div class="mt-3">
            <div class="d-flex justify-content-between small text-muted mb-1">
              <span>Progreso del escaneo</span>
              <span><b id="progress-label">0%</b></span>
            </div>
            <div class="progress slim">
              <div id="progress-bar" class="progress-bar warn" role="progressbar" style="width:0%"></div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ============ DERECHA: Verificados + Formulario ============ -->
    <div class="col-12 col-lg-7 right-col-offset">
      <form method="POST" action="{{ route('checklists.guardarIngenieria', $venta->id) }}" enctype="multipart/form-data" id="ingenieria-form">
        @csrf
        <input type="hidden" name="verificados" id="verificados-hidden">
        <input type="hidden" name="componentes" id="componentes-hidden"><!-- JSON de componentes -->

        <div class="card mb-4">
          <div class="card-body">

            <!-- Tabla de verificados dinámica -->
            <h6 class="text-muted mb-2">Verificados (<span id="count-verificados">0</span>)</h6>
            <div class="table-responsive mb-3">
              <table class="table table-borderless clean align-middle mb-0">
                <thead>
                  <tr>
                    <th style="width:50%">Producto</th>
                    <th style="width:30%">Serie</th>
                    <th style="width:15%">Estado</th>
                    <th style="width:5%"></th>
                  </tr>
                </thead>
                <tbody id="tabla-verificados">
                  <!-- Se agregan aquí al escanear -->
                </tbody>
              </table>
            </div>

            <!-- Checklist de componentes -->
            <div id="componentes-wrapper" class="mb-3 d-none">
              <h6 class="text-muted mb-2">Checklist de componentes</h6>
              <div id="componentes-list" class="vstack gap-3"></div>
            </div>

            <hr class="my-3">

            <!-- Bloque que se muestra solo si hay pendientes -->
            <div id="incidente-wrapper">
              <label class="mb-1">¿Incidente general?</label>
              <textarea id="incidenteTextarea" name="ingenieria_incidente" class="form-control mb-3 animate-shadow" rows="3" placeholder="Describe hallazgos generales"></textarea>
            </div>

            <!-- Firmas -->
            <div class="row g-3 mb-3">
              <div class="col-md-6">
                <label class="form-label">Firma Responsable</label>
                <div class="p-2 rounded border bg-white">
                  <canvas id="firmaCanvas1" width="520" height="120" class="w-100 d-block"></canvas>
                  <button type="button" id="limpiar1" class="btn btn-sm btn-light mt-2">Limpiar</button>
                  <input type="hidden" name="firma_responsable" id="firmaInput1">
                </div>
              </div>
              <div class="col-md-6">
                <label class="form-label">Firma Sucesor</label>
                <div class="p-2 rounded border bg-white">
                  <canvas id="firmaCanvas2" width="520" height="120" class="w-100 d-block"></canvas>
                  <button type="button" id="limpiar2" class="btn btn-sm btn-light mt-2">Limpiar</button>
                  <input type="hidden" name="firma_supervisor" id="firmaInput2">
                </div>
              </div>
            </div>

            <label class="form-label">Evidencias (foto o archivo)</label>
            <input type="file" name="evidencias[]" class="form-control mb-3 animate-shadow" multiple accept="image/*,application/pdf">

            <div class="d-flex justify-content-end">
              <button type="submit" id="submit-btn" class="btn btn-primary btn-lg animate-pop">Guardar y continuar</button>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div><!-- /row -->
</div>

<!-- Libs -->
<script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// ===== Toast helper =====
function showToast(message, type = 'info', icon = null) {
  const colors = { info:"#0d6efd", success:"#16a34a", warning:"#f59e0b", danger:"#dc3545" };
  let textMsg = message;
  if (icon) textMsg = `<span style="font-size:1.25em;vertical-align:middle;margin-right:7px;">${icon}</span>${message}`;
  Toastify({
    text: textMsg, duration: 2200, close: true, gravity: "bottom", position: "right",
    backgroundColor: colors[type] || colors.info, stopOnFocus: true, escapeMarkup: false,
    style: { borderRadius:"10px", fontSize:"0.95rem", boxShadow:"0 2px 8px rgba(44,62,80,.07)" }
  }).showToast();
}
</script>

<script>
/* ========== SOLO ESCÁNER + CHECKLIST DE COMPONENTES ========== */
document.addEventListener('DOMContentLoaded', () => {
  const $hiddenInput   = document.getElementById('barcode-input');
  const $tbodyPend     = document.getElementById('tabla-pendientes');
  const $tbodyVerif    = document.getElementById('tabla-verificados');
  const $countVerif    = document.getElementById('count-verificados');
  const $progressBar   = document.getElementById('progress-bar');
  const $progressLabel = document.getElementById('progress-label');
  const $form          = document.getElementById('ingenieria-form');
  const $hiddenVerif   = document.getElementById('verificados-hidden');
  const $incWrap       = document.getElementById('incidente-wrapper');
  const $incTextarea   = document.getElementById('incidenteTextarea');

  // Componentes UI/estado
  const $compWrap      = document.getElementById('componentes-wrapper');
  const $compList      = document.getElementById('componentes-list');
  const $compHidden    = document.getElementById('componentes-hidden');

  // Mantener foco en input oculto
  const focusHidden = () => $hiddenInput && $hiddenInput.focus();
  document.addEventListener('click', (e)=>{
    const TAGS = ['INPUT','SELECT','TEXTAREA','BUTTON','LABEL','CANVAS'];
    if (!TAGS.includes(e.target.tagName)) focusHidden();
  });
  focusHidden();

  // Series y mapas
  const allRows   = Array.from($tbodyPend.querySelectorAll('tr[data-serie]'));
  const allSeries = allRows.map(r => r.dataset.serie);
  const productBySerie = {};
  const tipoBySerie    = {};
  allRows.forEach(r=>{
    productBySerie[r.dataset.serie] = r.dataset.producto || r.children[1].innerText.split('\n')[0].trim();
    tipoBySerie[r.dataset.serie]    = (r.dataset.tipo || '').toLowerCase();
  });

  const verifiedSet = new Set();
  const componentsState = {}; // { serie: { 'Componente': true/false } }

  // Anti-teclado: solo escáner
  let typingStart = null, typingChars = 0;
  $hiddenInput.addEventListener('keydown', (e) => {
    if (e.key.length === 1) {
      if (typingStart === null) typingStart = Date.now();
      typingChars++;
    }
    if (e.key === 'Enter') {
      e.preventDefault();
      const code = $hiddenInput.value.trim();
      const duration = typingStart ? (Date.now() - typingStart) : 0;
      const avgMsPerChar = typingChars > 0 ? (duration / typingChars) : 0;
      typingStart = null; typingChars = 0;

      if (code && avgMsPerChar > 60) {
        showToast('Usa el escáner (no teclado).', 'warning', '🚫');
        $hiddenInput.value = '';
        return;
      }
      $hiddenInput.value = '';
      if (!code) return;
      verificarSerie(code);
    }
  });

  // Mapeo de componentes por tipo
  function componentsFor(tipo='') {
    const t = (tipo||'').toLowerCase();
    if (t.includes('video carro'))  return ['Cable de poder','Llave','Multicontacto'];
    if (t.includes('monitor'))      return ['Cable de poder','Eliminador','Base'];
    if (t.includes('camara') || t.includes('cámara')) return ['Cabezal','Cable DVI','Cable de poder'];
    if (t.includes('fuente de luz'))return ['Cable de poder'];
    if (t.includes('grabador'))     return ['Cable de poder'];
    if (t.includes('lente'))        return ['Tapa delantera','Tapa trasera','Estuche'];
    return [];
  }

  function syncComponentsHidden(){
    $compHidden.value = JSON.stringify(componentsState);
  }

  function buildChecklistCard(serie, producto, tipo) {
    const comps = componentsFor(tipo);
    if (!comps.length) return null;

    componentsState[serie] = componentsState[serie] || {};
    comps.forEach(c => { if (componentsState[serie][c] === undefined) componentsState[serie][c] = true; });

    const wrapper = document.createElement('div');
    wrapper.className = 'p-3 rounded-4 border bg-white shadow-sm';
    wrapper.setAttribute('data-serie', serie);
    wrapper.innerHTML = `
      <div class="d-flex justify-content-between align-items-center mb-2">
        <div>
          <div class="fw-semibold">${producto}</div>
          <div class="text-muted small">Serie: <code>${serie}</code></div>
        </div>
        <div class="small">
          <button type="button" class="btn btn-link p-0 me-2 btn-all" data-serie="${serie}" data-val="1">Todos vienen</button>
          <button type="button" class="btn btn-link p-0 btn-all" data-serie="${serie}" data-val="0">Todos no</button>
        </div>
      </div>
      <div class="row g-2">
        ${comps.map((c,i)=>`
          <div class="col-12 col-md-6">
            <div class="form-check form-switch d-flex align-items-center gap-2 px-2 py-1 rounded bg-light">
              <input class="form-check-input comp-check" type="checkbox"
                     id="chk-${serie}-${i}" ${componentsState[serie][c] ? 'checked' : ''}
                     data-serie="${serie}" data-comp="${c}">
              <label class="form-check-label" for="chk-${serie}-${i}">${c}</label>
              <span class="badge ms-auto ${componentsState[serie][c] ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary'} comp-badge"
                    data-serie="${serie}" data-comp="${c}">
                ${componentsState[serie][c] ? 'Viene' : 'No viene'}
              </span>
            </div>
          </div>
        `).join('')}
      </div>
    `;
    return wrapper;
  }

  function addChecklistFor(serie){
    const producto = productBySerie[serie] || 'Equipo';
    const tipo     = tipoBySerie[serie] || '';
    const card = buildChecklistCard(serie, producto, tipo);
    if (!card) return;
    $compWrap.classList.remove('d-none');
    $compList.appendChild(card);
    syncComponentsHidden();
  }

  function removeChecklistFor(serie){
    delete componentsState[serie];
    const el = $compList.querySelector(`div[data-serie="${CSS.escape(serie)}"]`);
    if (el) el.remove();
    if (!$compList.children.length) $compWrap.classList.add('d-none');
    syncComponentsHidden();
  }

  // Delegación: switches y "Todos vienen / Todos no"
  $compList.addEventListener('change', (e) => {
    const chk = e.target.closest('.comp-check');
    if (!chk) return;
    const serie = chk.dataset.serie;
    const comp  = chk.dataset.comp;
    const val   = !!chk.checked;
    componentsState[serie] = componentsState[serie] || {};
    componentsState[serie][comp] = val;

    const badge = $compList.querySelector(`.comp-badge[data-serie="${CSS.escape(serie)}"][data-comp="${CSS.escape(comp)}"]`);
    if (badge){
      badge.textContent = val ? 'Viene' : 'No viene';
      badge.className = `badge ms-auto ${val ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary'} comp-badge`;
    }
    syncComponentsHidden();
  });

  $compList.addEventListener('click', (e) => {
    const btn = e.target.closest('.btn-all');
    if (!btn) return;
    const serie = btn.dataset.serie;
    const val   = btn.dataset.val === '1';
    $compList.querySelectorAll(`input.comp-check[data-serie="${CSS.escape(serie)}"]`).forEach(chk => {
      chk.checked = val;
      chk.dispatchEvent(new Event('change', { bubbles: true }));
    });
  });

  // Verificación por escaneo
  function verificarSerie(code){
    const trLeft = $tbodyPend.querySelector(`tr[data-serie="${CSS.escape(code)}"]`);
    if(!trLeft){ flashWarn(); showToast('Código no reconocido', 'danger', '❌'); return; }
    if(verifiedSet.has(code)){ showToast('¡Ya escaneaste este producto!', 'warning', '⚠️'); return; }

    const productoTxt = productBySerie[code] || 'Equipo';
    const trNew = document.createElement('tr');
    trNew.innerHTML = `
      <td class="small">${productoTxt}</td>
      <td class="fw-semibold">${code}</td>
      <td><span class="badge-chip chip-ok"><span class="dot ok"></span> Verificado</span></td>
      <td class="text-end">
        <button type="button" class="btn btn-sm btn-outline-secondary btn-undo" data-serie="${code}">Deshacer</button>
      </td>
    `;
    $tbodyVerif.appendChild(trNew);
    trLeft.remove();

    verifiedSet.add(code);
    addChecklistFor(code); // crea checklist según tipo
    updateCounters();
    showToast('¡Producto verificado!', 'success', '✅');
  }

  // Deshacer verificación
  $tbodyVerif.addEventListener('click', (e)=>{
    const btn = e.target.closest('.btn-undo');
    if(!btn) return;
    const serie = btn.dataset.serie;

    const indexNew = $tbodyPend.children.length + 1;
    const tr = document.createElement('tr');
    tr.setAttribute('data-serie', serie);
    tr.setAttribute('data-tipo', tipoBySerie[serie] || '');
    tr.setAttribute('data-producto', productBySerie[serie] || 'Equipo');
    tr.innerHTML = `
      <td class="text-muted">${indexNew}</td>
      <td class="small">${productBySerie[serie] || 'Equipo'}</td>
      <td class="serie">${serie}</td>
      <td><span class="badge-chip chip-pend"><span class="dot"></span>Pendiente</span></td>
    `;
    $tbodyPend.appendChild(tr);

    btn.closest('tr').remove();
    verifiedSet.delete(serie);
    removeChecklistFor(serie);
    updateCounters();
    showToast('Se revirtió la verificación', 'info', '↩️');
  });

  function flashWarn(){
    $progressBar.classList.add('shake-red');
    setTimeout(()=> $progressBar.classList.remove('shake-red'), 600);
  }

  // Incidente general visible solo si hay pendientes
  function toggleIncidente(pendientes){
    if (!$incWrap || !$incTextarea) return;
    if (pendientes > 0) {
      $incWrap.classList.remove('d-none');
      $incTextarea.disabled = false;
    } else {
      $incWrap.classList.add('d-none');
      $incTextarea.value = '';
      $incTextarea.disabled = true;
    }
  }

  function updateCounters(){
    const total = allSeries.length || 1;
    const verified = verifiedSet.size;
    const pendientes = total - verified;

    const pct = Math.round((verified / total) * 100);
    $progressBar.style.width = pct + '%';
    $progressLabel.textContent = pct + '%';
    $countVerif.textContent = verified;
    $hiddenVerif.value = JSON.stringify(Array.from(verifiedSet));

    toggleIncidente(pendientes);
  }

  // Submit
  $form.addEventListener('submit', (e)=>{
    e.preventDefault();

    const pendientes = allSeries.filter(s => !verifiedSet.has(s));
    const pendientesHTML = pendientes.length
      ? `<ul style="text-align:left;margin-left:1rem">${pendientes.map(s=>`<li><code>${s}</code></li>`).join('')}</ul>`
      : '<p class="text-success">Todos verificados 🎉</p>';

    Swal.fire({
      title: pendientes.length ? 'Faltan productos por verificar' : '¿Guardar checklist?',
      html: pendientes.length
        ? `<p>Estos <b>${pendientes.length}</b> productos se enviarán como <b>no verificados</b>:</p>${pendientesHTML}`
        : '<p>Se enviará la información registrada.</p>',
      icon: pendientes.length ? 'warning' : 'question',
      showCancelButton: true,
      confirmButtonText: 'Guardar',
      cancelButtonText: 'Volver',
      confirmButtonColor: '#0d6efd'
    }).then(result=>{
      if(!result.isConfirmed) return;
      $hiddenVerif.value = JSON.stringify(Array.from(verifiedSet));
      syncComponentsHidden(); // manda JSON de componentes
      $form.submit();
    });
  });

  // Estado inicial
  updateCounters();
});
</script>

<!-- Libs -->
<script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
/* =================== TOAST =================== */
function showToast(message, type = 'info', icon = null) {
  const colors = { info:"#0d6efd", success:"#16a34a", warning:"#f59e0b", danger:"#dc3545" };
  let textMsg = message;
  if (icon) textMsg = `<span style="font-size:1.25em;vertical-align:middle;margin-right:7px;">${icon}</span>${message}`;
  Toastify({
    text: textMsg,
    duration: 2200,
    close: true,
    gravity: "bottom",
    position: "right",
    backgroundColor: colors[type] || colors.info,
    stopOnFocus: true,
    escapeMarkup: false,
    style: { borderRadius:"10px", fontSize:"0.95rem", boxShadow:"0 2px 8px rgba(44,62,80,.07)" }
  }).showToast();
}
</script>
<script>
// =================== SIGNATURE PAD HD (Desktop + iOS/Android) ===================
function makeSignaturePad(canvasId, clearBtnId, inputId, options = {}) {
  const cfg = {
    minWidth: 1.4,
    maxWidth: 3.2,
    color: '#111827',
    background: '#ffffff',
    smoothing: 0.3,
    velocityFilter: 0.8,
    onChange: null, // <--- callback cuando cambia la firma
    ...options
  };

  const canvas = document.getElementById(canvasId);
  const input  = document.getElementById(inputId);
  const clear  = document.getElementById(clearBtnId);

  canvas.style.touchAction = 'none';
  canvas.style.webkitUserSelect = 'none';
  canvas.style.userSelect = 'none';

  let ctx = canvas.getContext('2d');
  let dpr = Math.max(window.devicePixelRatio || 1, 1);

  let strokes = [];
  let current = [];
  let isDrawing = false;
  let lastWidth = cfg.maxWidth;
  let lastVelocity = 0;

  function cssSize() {
    const r = canvas.getBoundingClientRect();
    return { w: Math.max(1, r.width), h: Math.max(1, r.height) };
  }

  function setupCanvas() {
    const { w, h } = cssSize();
    dpr = Math.max(window.devicePixelRatio || 1, 1);
    canvas.width  = Math.round(w * dpr);
    canvas.height = Math.round(h * dpr);
    ctx.setTransform(1,0,0,1,0,0);
    ctx.scale(dpr, dpr);
    ctx.lineCap   = 'round';
    ctx.lineJoin  = 'round';
    ctx.miterLimit = 2;
    redraw();
  }

  function distance(a,b){ const dx=a.x-b.x, dy=a.y-b.y; return Math.hypot(dx,dy); }
  function midpoint(a,b){ return { x:(a.x+b.x)/2, y:(a.y+b.y)/2 }; }
  function clamp(v,min,max){ return Math.max(min, Math.min(max, v)); }

  function velocity(p1, p2){
    const dt = Math.max(1, (p2.t - p1.t));
    return distance(p1,p2) / dt;
  }

  function widthFromVelocity(v){
    lastVelocity = cfg.velocityFilter * v + (1 - cfg.velocityFilter) * lastVelocity;
    const target = cfg.maxWidth - lastVelocity * 15;
    const w = clamp(target, cfg.minWidth, cfg.maxWidth);
    lastWidth = cfg.smoothing * w + (1 - cfg.smoothing) * lastWidth;
    return lastWidth;
  }

  function strokeSegment(p0, p1, p2){
    const m1 = midpoint(p0, p1);
    const m2 = midpoint(p1, p2);
    const v  = velocity(p1, p2);
    const w  = widthFromVelocity(v);

    ctx.strokeStyle = cfg.color;
    ctx.lineWidth   = w;
    ctx.beginPath();
    ctx.moveTo(m1.x, m1.y);
    ctx.quadraticCurveTo(p1.x, p1.y, m2.x, m2.y);
    ctx.stroke();
  }

  function drawStroke(points){
    if (points.length < 2) return;
    while (points.length < 3) points.unshift(points[0]);
    lastWidth = cfg.maxWidth;
    lastVelocity = 0;

    for (let i = 2; i < points.length; i++){
      strokeSegment(points[i-2], points[i-1], points[i]);
    }
  }

  function redraw(){
    const { w, h } = cssSize();
    ctx.clearRect(0, 0, w, h);
    for (const s of strokes) drawStroke(s);
    if (current.length) drawStroke(current);
  }

  function pointerPos(e){
    const r = canvas.getBoundingClientRect();
    return { x: e.clientX - r.left, y: e.clientY - r.top, t: e.timeStamp || performance.now() };
  }

  function onPointerDown(e){
    if (e.button !== undefined && e.button !== 0) return;
    isDrawing = true;
    current = [ pointerPos(e) ];
    canvas.setPointerCapture?.(e.pointerId);
    e.preventDefault();
  }

  function onPointerMove(e){
    if (!isDrawing) return;
    const p = pointerPos(e);
    current.push(p);
    if (current.length >= 3) {
      strokeSegment(current[current.length-3], current[current.length-2], current[current.length-1]);
      cfg.onChange && cfg.onChange(false); // hay cambios (no vacío)
    }
    e.preventDefault();
  }

  function onPointerUp(e){
    if (!isDrawing) return;
    isDrawing = false;
    if (current.length > 1) {
      strokes.push(current.slice());
      syncToInput();
      cfg.onChange && cfg.onChange(false);
    }
    current = [];
    e.preventDefault();
  }

  function bindEvents(){
    canvas.addEventListener('pointerdown', onPointerDown, { passive:false });
    canvas.addEventListener('pointermove', onPointerMove, { passive:false });
    canvas.addEventListener('pointerup',   onPointerUp,   { passive:false });
    canvas.addEventListener('pointerleave',onPointerUp,   { passive:false });

    let resizeTO = null;
    window.addEventListener('resize', () => {
      clearTimeout(resizeTO);
      resizeTO = setTimeout(setupCanvas, 120);
    });
    window.addEventListener('orientationchange', () => {
      setTimeout(setupCanvas, 200);
    });

    clear?.addEventListener('click', () => {
      strokes = [];
      current = [];
      setupCanvas();
      if (input) input.value = '';
      cfg.onChange && cfg.onChange(true); // ahora vacío
    });
  }

  function toDataURL(){
    const out = document.createElement('canvas');
    out.width  = canvas.width;
    out.height = canvas.height;
    const octx = out.getContext('2d');
    if (cfg.background) {
      octx.fillStyle = cfg.background;
      octx.fillRect(0,0,out.width,out.height);
    }
    octx.drawImage(canvas, 0, 0);
    return out.toDataURL('image/png');
  }

  function isEmpty(){
    return strokes.length === 0 && current.length === 0;
  }

  function syncToInput(){
    if (!input) return;
    input.value = isEmpty() ? '' : toDataURL();
  }

  setupCanvas();
  bindEvents();

  return {
    clear: () => { strokes=[]; current=[]; setupCanvas(); input && (input.value=''); cfg.onChange && cfg.onChange(true); },
    isEmpty,
    toDataURL,
    syncToInput
  };
}

// ===== Crea los 2 pads y controla el botón Guardar =====
const $saveBtn = document.getElementById('submit-btn');

function updateSaveVisibility() {
  const hidden = pad1.isEmpty() || pad2.isEmpty();
  if ($saveBtn) $saveBtn.classList.toggle('d-none', hidden);
}

// Instancias con onChange -> cada trazo/limpieza evalúa visibilidad
const pad1 = makeSignaturePad('firmaCanvas1','limpiar1','firmaInput1', {
  minWidth: 1.4, maxWidth: 3.4, color:'#111827', background:'#ffffff',
  onChange: () => updateSaveVisibility()
});
const pad2 = makeSignaturePad('firmaCanvas2','limpiar2','firmaInput2', {
  minWidth: 1.4, maxWidth: 3.4, color:'#111827', background:'#ffffff',
  onChange: () => updateSaveVisibility()
});

// Estado inicial (por si ya venían firmas precargadas)
updateSaveVisibility();

// Antes de enviar, valida y sincroniza
document.getElementById('ingenieria-form')?.addEventListener('submit', (e) => {
  if (pad1.isEmpty() || pad2.isEmpty()) {
    e.preventDefault();
    showToast('Faltan firmas obligatorias.', 'warning', '✍️');
    updateSaveVisibility();
    return;
  }
  pad1.syncToInput();
  pad2.syncToInput();
});
</script>
@endsection
