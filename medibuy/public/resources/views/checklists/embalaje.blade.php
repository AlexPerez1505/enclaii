{{-- resources/views/checklists/embalaje.blade.php --}}
@extends('layouts.app')

@section('title', 'Checklists')
@section('titulo', 'Checklists')

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
<link rel="stylesheet" href="{{ asset('css/ingenieria.css') }}?v={{ time() }}">

<div class="container container-page py-4">
  <h3 class="mb-4 fw-bold page-title text-primary">
    Embalaje: Verificación y checklist
  </h3>

  {{-- Flash --}}
  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif
  @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
  @endif

  <div class="row verif-layout g-4">

    <!-- ============ IZQUIERDA: Resumen de Ingeniería ============ -->
    <div class="col-12">
      <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body">
          <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div>
              <h5 class="mb-1">Resumen de Ingeniería</h5>
              @php
                $verifIng   = is_array($ingenieria?->verificados) ? $ingenieria->verificados : (json_decode($ingenieria?->verificados ?? '[]', true) ?: []);
                $noVerifIng = is_array($ingenieria?->no_verificados) ? $ingenieria->no_verificados : (json_decode($ingenieria?->no_verificados ?? '[]', true) ?: []);
                $compIng    = is_array($ingenieria?->componentes) ? $ingenieria->componentes : (json_decode($ingenieria?->componentes ?? '{}', true) ?: []);
                $obsIng     = $ingenieria?->observaciones ?? $ingenieria?->incidente;
                $evIng      = is_array($ingenieria?->evidencias) ? $ingenieria->evidencias : (json_decode($ingenieria?->evidencias ?? '[]', true) ?: []);
              @endphp
              <div class="text-muted small">
                @if($ingenieria)
                  <span class="me-3">Verificados: <b>{{ count($verifIng) }}</b></span>
                  <span class="me-3">No verificados: <b class="{{ count($noVerifIng) ? 'text-danger' : 'text-success' }}">{{ count($noVerifIng) }}</b></span>
                  <span>Venta: <b>#{{ $venta->id }}</b></span>
                @else
                  <span class="text-warning">Aún no se ha completado Ingeniería.</span>
                @endif
              </div>
            </div>

            @if($ingenieria && ($ingenieria->firma_responsable || $ingenieria->firma_supervisor))
              <div class="d-flex align-items-center gap-3">
                @if($ingenieria->firma_responsable)
                  <div class="text-center">
                    <div class="small text-muted">Firma Responsable</div>
                    <img src="{{ asset($ingenieria->firma_responsable) }}" alt="Firma Responsable" style="height:48px;max-width:220px;object-fit:contain;">
                  </div>
                @endif
                @if($ingenieria->firma_supervisor)
                  <div class="text-center">
                    <div class="small text-muted">Firma Supervisor</div>
                    <img src="{{ asset($ingenieria->firma_supervisor) }}" alt="Firma Supervisor" style="height:48px;max-width:220px;object-fit:contain;">
                  </div>
                @endif
              </div>
            @endif
          </div>

          @if($obsIng)
            <div class="mt-3">
              <div class="fw-semibold mb-1">Observaciones de Ingeniería</div>
              <div class="p-3 bg-light rounded-3 border small">{{ $obsIng }}</div>
            </div>
          @endif

          @if(!empty($noVerifIng))
            <div class="mt-3">
              <div class="fw-semibold mb-1 text-danger">Series no verificadas en Ingeniería</div>
              <div class="small">
                @foreach($noVerifIng as $sn)
                  <code class="me-2">{{ $sn }}</code>
                @endforeach
              </div>
            </div>
          @endif

          @if(!empty($compIng))
            <div class="mt-3">
              <details>
                <summary class="fw-semibold cursor-pointer">Componentes registrados en Ingeniería</summary>
                <div class="mt-2 row g-3">
                  @foreach($compIng as $serie => $componentes)
                    @php
                      $vienen = collect($componentes)->filter()->count();
                      $total  = count($componentes);
                    @endphp
                    <div class="col-12 col-md-6">
                      <div class="p-3 rounded-4 border bg-white">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                          <div>
                            <div class="fw-semibold">Serie: <code>{{ $serie }}</code></div>
                            <div class="small text-muted">{{ $vienen }}/{{ $total }} vienen</div>
                          </div>
                        </div>
                        <ul class="small mb-0" style="columns: 2; -webkit-columns:2;">
                          @foreach($componentes as $cname => $ok)
                            <li class="{{ $ok ? 'text-success' : 'text-secondary' }}">
                              {{ $cname }} {!! $ok ? '✅' : '—' !!}
                            </li>
                          @endforeach
                        </ul>
                      </div>
                    </div>
                  @endforeach
                </div>
              </details>
            </div>
          @endif

          @if(!empty($evIng))
            <div class="mt-3">
              <div class="fw-semibold mb-2">Evidencias de Ingeniería</div>
              <div class="d-flex flex-wrap gap-2">
                @foreach($evIng as $path)
                  @php $isImg = preg_match('/\.(jpg|jpeg|png|webp)$/i', $path); @endphp
                  @if($isImg)
                    <a href="{{ asset($path) }}" target="_blank">
                      <img src="{{ asset($path) }}" style="height:60px;width:auto;border-radius:8px;border:1px solid #e5e7eb;object-fit:cover;">
                    </a>
                  @else
                    <a class="btn btn-sm btn-outline-secondary" href="{{ asset($path) }}" target="_blank">Archivo</a>
                  @endif
                @endforeach
              </div>
            </div>
          @endif
        </div>
      </div>
    </div>

    <!-- ============ CONTENIDO PRINCIPAL: Igual que Ingeniería ============ -->
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
                  @php
                    // Intentamos usar numero_serie si viene (si no, muestra vacío)
                    $serie = $p->numero_serie ?? '';
                    $tipo  = strtolower($p->tipo_equipo ?? '');
                    $prod  = trim(($p->marca ?? '').' '.($p->modelo ?? ''));
                  @endphp
                  <tr data-serie="{{ $serie }}"
                      data-tipo="{{ $tipo }}"
                      data-producto="{{ $prod }}">
                    <td class="text-muted">{{ $loop->iteration }}</td>
                    <td class="small">
                      {{ $p->tipo_equipo }}
                      <div class="text-muted small">{{ $prod }}</div>
                    </td>
                    <td class="serie">{{ $serie }}</td>
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
      <form method="POST" action="{{ route('checklists.guardarEmbalaje', $venta->id) }}" enctype="multipart/form-data" id="embalaje-form">
        @csrf
        <input type="hidden" name="verificados" id="verificados-hidden">
        <input type="hidden" name="componentes" id="componentes-hidden"><!-- JSON de componentes -->

        <div class="card mb-4">
          <div class="card-body">
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

            <!-- Observación general -->
            <div class="mb-3">
              <label class="mb-1">Observaciones generales de embalaje</label>
              <textarea name="embalaje_observacion" class="form-control animate-shadow" rows="3" placeholder="Describe hallazgos de embalaje (protecciones, cajas, sellos, etc.)"></textarea>
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
                <label class="form-label">Firma Supervisor</label>
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

<!-- ====== ESTILO Switch Animado (fix dentro del checklist) ====== -->
<style>
#componentes-wrapper{
  --sw-w: 46px; --sw-h: 26px;
  --sw-off:#dfe6ef; --sw-on:#c8edd8; --ring:rgba(99,102,241,.18);
}
#componentes-list > [data-serie]{ border:1px solid #e9eef6; border-radius:16px; background:#fff; box-shadow:0 10px 24px rgba(15,23,42,.06); overflow:hidden; animation:compPop .25s ease-out; }
@keyframes compPop{ from{ transform:translateY(6px); opacity:0 } to{ transform:translateY(0); opacity:1 } }
#componentes-list .form-check{ display:flex; align-items:center; gap:.75rem; padding:.55rem .75rem; border-radius:12px; background:#f8fafc; border:1px solid #eef2f7; }
#componentes-list .form-switch{ padding-left:0 !important; }
#componentes-list .form-switch .form-check-input{
  margin-left:0 !important; position:relative; width:var(--sw-w); height:var(--sw-h);
  border:none; border-radius:999px; background:var(--sw-off);
  box-shadow: inset 0 1px 2px rgba(15,23,42,.12); transition:background .22s ease; background-image:none; flex:none;
}
#componentes-list .form-switch .form-check-input:focus{ outline:none; box-shadow:0 0 0 .22rem var(--ring); }
#componentes-list .form-switch .form-check-input::before{
  content:""; position:absolute; top:3px; left:3px; width:calc(var(--sw-h) - 6px); height:calc(var(--sw-h) - 6px);
  background:#fff; border-radius:50%; box-shadow:0 1px 2px rgba(15,23,42,.25);
  transition: transform .22s cubic-bezier(.22,.61,.36,1), width .12s ease;
}
#componentes-list .form-switch .form-check-input:active::before{ width:calc(var(--sw-h) - 2px); }
#componentes-list .form-switch .form-check-input:checked{ background:var(--sw-on); }
#componentes-list .form-switch .form-check-input:checked::before{ transform: translateX(calc(var(--sw-w) - var(--sw-h))); }
#componentes-list .form-check-label{ font-weight:600; color:#334155; flex:1; min-width:0; }
#componentes-list .comp-badge{ font-size:12px; border-radius:999px; padding:.28rem .6rem; transition:background .2s ease, color .2s ease, transform .2s ease; white-space:nowrap; }
#componentes-list .form-check-input:checked ~ .comp-badge{ background:#e9fbf0 !important; color:#16a34a !important; transform:translateY(-1px); }
#componentes-list .form-check-input:not(:checked) ~ .comp-badge{ background:#e9eef7 !important; color:#64748b !important; }
#componentes-list .btn.btn-link{ color:#2563eb; font-weight:600; text-decoration:none; }
#componentes-list .btn.btn-link:hover{ text-decoration:underline; }
</style>

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
    text: textMsg, duration: 2200, close: true, gravity: "bottom", position: "right",
    backgroundColor: colors[type] || colors.info, stopOnFocus: true, escapeMarkup: false,
    style: { borderRadius:"10px", fontSize:"0.95rem", boxShadow:"0 2px 8px rgba(44,62,80,.07)" }
  }).showToast();
}
</script>

<script>
/* ========== ESCÁNER + CHECKLIST (igual a Ingeniería) ========== */
document.addEventListener('DOMContentLoaded', () => {
  const $hiddenInput   = document.getElementById('barcode-input');
  const $tbodyPend     = document.getElementById('tabla-pendientes');
  const $tbodyVerif    = document.getElementById('tabla-verificados');
  const $countVerif    = document.getElementById('count-verificados');
  const $progressBar   = document.getElementById('progress-bar');
  const $progressLabel = document.getElementById('progress-label');
  const $form          = document.getElementById('embalaje-form');
  const $hiddenVerif   = document.getElementById('verificados-hidden');

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
  const allSeries = allRows.map(r => r.dataset.serie).filter(Boolean);
  const productBySerie = {};
  const tipoBySerie    = {};
  allRows.forEach(r=>{
    productBySerie[r.dataset.serie] = r.dataset.producto || r.children[1]?.innerText?.split('\n')[0]?.trim() || 'Equipo';
    tipoBySerie[r.dataset.serie]    = (r.dataset.tipo || '').toLowerCase();
  });

  const verifiedSet = new Set();
  const componentsState = {}; // { serie: { 'Componente': true/false } }

  // Anti-teclado: solo escáner
  let typingStart = null, typingChars = 0;
  $hiddenInput.addEventListener('keydown', (e) => {
    if (e.key.length === 1) { if (typingStart === null) typingStart = Date.now(); typingChars++; }
    if (e.key === 'Enter') {
      e.preventDefault();
      const code = $hiddenInput.value.trim();
      const duration = typingStart ? (Date.now() - typingStart) : 0;
      const avgMsPerChar = typingChars > 0 ? (duration / typingChars) : 0;
      typingStart = null; typingChars = 0;
      $hiddenInput.value = '';
      if (!code) return;
      if (avgMsPerChar > 60) { showToast('Usa el escáner (no teclado).', 'warning', '🚫'); return; }
      verificarSerie(code);
    }
  });

  // Mapeo de componentes (puedes ajustar a embalaje si gustas)
  function componentsFor(tipo='') {
    const t = (tipo||'').toLowerCase();
    if (t.includes('video carro'))  return ['Cable de poder','Llave','Protección espuma','Caja transporte'];
    if (t.includes('monitor'))      return ['Cable de poder','Eliminador','Base','Protector pantalla'];
    if (t.includes('camara') || t.includes('cámara')) return ['Cabezal','Cable DVI','Cable de poder','Caja acolchada'];
    if (t.includes('fuente de luz'))return ['Cable de poder','Protector'];
    if (t.includes('grabador'))     return ['Cable de poder','Protector'];
    if (t.includes('lente'))        return ['Tapa delantera','Tapa trasera','Estuche','Protección'];
    return ['Protección','Caja','Sellos']; // default embalaje
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

  // Click en fila para togglear
  $compList.addEventListener('click', (e) => {
    const btnAll = e.target.closest('.btn-all');
    if (btnAll){
      const serie = btnAll.dataset.serie;
      const val   = btnAll.dataset.val === '1';
      $compList.querySelectorAll(`input.comp-check[data-serie="${CSS.escape(serie)}"]`).forEach(chk => {
        chk.checked = val;
        chk.dispatchEvent(new Event('change', { bubbles: true }));
      });
      return;
    }
    const row = e.target.closest('.form-check');
    if (!row) return;
    const isInteractive = e.target.closest('input,button,a,label,.btn-all');
    if (isInteractive) return;
    const chk = row.querySelector('input.comp-check');
    if (chk){
      chk.checked = !chk.checked;
      chk.dispatchEvent(new Event('change', { bubbles:true }));
    }
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
    addChecklistFor(code);
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

  function updateCounters(){
    const total = allSeries.length || 1;
    const verified = verifiedSet.size;
    const pendientes = total - verified;

    const pct = Math.round((verified / total) * 100);
    $progressBar.style.width = pct + '%';
    $progressLabel.textContent = pct + '%';
    $countVerif.textContent = verified;
    $hiddenVerif.value = JSON.stringify(Array.from(verifiedSet));
  }

  // Submit con confirmación
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
      showCancelButton: true, confirmButtonText: 'Guardar', cancelButtonText: 'Volver',
      confirmButtonColor: '#0d6efd'
    }).then(result=>{
      if(!result.isConfirmed) return;
      document.getElementById('verificados-hidden').value = JSON.stringify(Array.from(verifiedSet));
      document.getElementById('componentes-hidden').value = JSON.stringify(componentsState);
      $form.submit();
    });
  });

  // Estado inicial
  updateCounters();
});
</script>

<script>
/* ================ SIGNATURE PAD (igual al de ingeniería) ================ */
function makeSignaturePad(canvasId, clearBtnId, inputId, options = {}) {
  const cfg = { minWidth: 1.4, maxWidth: 3.2, color:'#111827', background:'#ffffff', smoothing: 0.3, velocityFilter: 0.8, onChange: null, ...options };
  const canvas = document.getElementById(canvasId);
  const input  = document.getElementById(inputId);
  const clear  = document.getElementById(clearBtnId);
  canvas.style.touchAction = 'none'; canvas.style.webkitUserSelect = 'none'; canvas.style.userSelect = 'none';
  let ctx = canvas.getContext('2d'); let dpr = Math.max(window.devicePixelRatio || 1, 1);
  let strokes = [], current = [], isDrawing = false, lastWidth = cfg.maxWidth, lastVelocity = 0;
  function cssSize(){ const r = canvas.getBoundingClientRect(); return { w: Math.max(1, r.width), h: Math.max(1, r.height) }; }
  function setup(){ const { w, h } = cssSize(); dpr = Math.max(window.devicePixelRatio || 1, 1); canvas.width=Math.round(w*dpr); canvas.height=Math.round(h*dpr); ctx.setTransform(1,0,0,1,0,0); ctx.scale(dpr, dpr); ctx.lineCap='round'; ctx.lineJoin='round'; ctx.miterLimit=2; redraw(); }
  function dist(a,b){ const dx=a.x-b.x, dy=a.y-b.y; return Math.hypot(dx,dy); }
  function mid(a,b){ return { x:(a.x+b.x)/2, y:(a.y+b.y)/2 }; }
  function clamp(v,min,max){ return Math.max(min, Math.min(max, v)); }
  function velocity(p1, p2){ const dt = Math.max(1, (p2.t - p1.t)); return dist(p1,p2) / dt; }
  function widthFromVelocity(v){ lastVelocity = cfg.velocityFilter * v + (1 - cfg.velocityFilter) * lastVelocity; const target = cfg.maxWidth - lastVelocity * 15; const w = clamp(target, cfg.minWidth, cfg.maxWidth); lastWidth = cfg.smoothing * w + (1 - cfg.smoothing) * lastWidth; return lastWidth; }
  function seg(p0,p1,p2){ const m1=mid(p0,p1), m2=mid(p1,p2), v=velocity(p1,p2), w=widthFromVelocity(v); ctx.strokeStyle=cfg.color; ctx.lineWidth=w; ctx.beginPath(); ctx.moveTo(m1.x,m1.y); ctx.quadraticCurveTo(p1.x,p1.y,m2.x,m2.y); ctx.stroke(); }
  function draw(points){ if (points.length < 2) return; while (points.length < 3) points.unshift(points[0]); lastWidth = cfg.maxWidth; lastVelocity = 0; for (let i = 2; i < points.length; i++) seg(points[i-2], points[i-1], points[i]); }
  function redraw(){ const { w, h } = cssSize(); ctx.clearRect(0,0,w,h); for (const s of strokes) draw(s); if (current.length) draw(current); }
  function pos(e){ const r = canvas.getBoundingClientRect(); return { x:e.clientX-r.left, y:e.clientY-r.top, t:e.timeStamp || performance.now() }; }
  function down(e){ if (e.button !== undefined && e.button !== 0) return; isDrawing = true; current = [ pos(e) ]; canvas.setPointerCapture?.(e.pointerId); e.preventDefault(); }
  function move(e){ if (!isDrawing) return; const p=pos(e); current.push(p); if (current.length >= 3) { seg(current[current.length-3], current[current.length-2], current[current.length-1]); cfg.onChange && cfg.onChange(false); } e.preventDefault(); }
  function up(e){ if (!isDrawing) return; isDrawing = false; if (current.length > 1) { strokes.push(current.slice()); sync(); cfg.onChange && cfg.onChange(false); } current = []; e.preventDefault(); }
  function bind(){ canvas.addEventListener('pointerdown', down, { passive:false }); canvas.addEventListener('pointermove', move, { passive:false }); canvas.addEventListener('pointerup', up, { passive:false }); canvas.addEventListener('pointerleave', up, { passive:false });
    let to=null; window.addEventListener('resize', ()=>{ clearTimeout(to); to=setTimeout(setup, 120); }); window.addEventListener('orientationchange', ()=> setTimeout(setup, 200));
    clear?.addEventListener('click', ()=>{ strokes=[]; current=[]; setup(); if (input) input.value=''; cfg.onChange && cfg.onChange(true); });
  }
  function toDataURL(){ const out=document.createElement('canvas'); out.width=canvas.width; out.height=canvas.height; const o=out.getContext('2d'); if (cfg.background) { o.fillStyle=cfg.background; o.fillRect(0,0,out.width,out.height); } o.drawImage(canvas,0,0); return out.toDataURL('image/png'); }
  function isEmpty(){ return strokes.length===0 && current.length===0; }
  function sync(){ if (!input) return; input.value = isEmpty() ? '' : toDataURL(); }
  setup(); bind();
  return { clear: ()=>{strokes=[]; current=[]; setup(); input && (input.value=''); cfg.onChange && cfg.onChange(true); }, isEmpty, toDataURL, syncToInput: sync };
}

// Crear pads y controlar visibilidad de guardar
const $saveBtn = document.getElementById('submit-btn');
const pad1 = makeSignaturePad('firmaCanvas1','limpiar1','firmaInput1', { onChange: toggleSave });
const pad2 = makeSignaturePad('firmaCanvas2','limpiar2','firmaInput2', { onChange: toggleSave });

function toggleSave(){
  const hidden = pad1.isEmpty() || pad2.isEmpty();
  if ($saveBtn) $saveBtn.classList.toggle('d-none', hidden);
}
toggleSave();

// Validación antes de enviar
document.getElementById('embalaje-form')?.addEventListener('submit', (e) => {
  if (pad1.isEmpty() || pad2.isEmpty()) {
    e.preventDefault();
    showToast('Faltan firmas obligatorias.', 'warning', '✍️');
    toggleSave();
    return;
  }
  pad1.syncToInput();
  pad2.syncToInput();
});
</script>
@endsection
