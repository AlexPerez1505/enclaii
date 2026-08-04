@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
<link rel="stylesheet" href="{{ asset('css/ingenieria.css') }}?v={{ time() }}">

<div class="container container-page py-4">
  <h3 class="mb-4 fw-bold page-title text-primary">Entrega: verificación final</h3>

  @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
  @if(session('error'))   <div class="alert alert-danger">{{ session('error') }}</div>   @endif

  {{-- ================= Resúmenes ================= --}}
  <div class="row g-4 mb-3">
    {{-- Resumen Ingeniería --}}
    <div class="col-12 col-xl-6">
      <div class="card border-0 shadow-sm rounded-4 h-100">
        <div class="card-body">
          @php
            $verIng  = (array)($ingenieria?->verificados ?? []);
            $noIng   = (array)($ingenieria?->no_verificados ?? []);
            $obsIng  = $ingenieria?->observaciones ?? $ingenieria?->incidente;
            $evIng   = (array)($ingenieria?->evidencias ?? []);
            $compIng = (array)($ingenieria?->componentes ?? []);
            $byIng   = $ingenieria?->usuario?->name ?? '—';
            $atIng   = optional($ingenieria?->created_at)->format('d/m/Y H:i') ?? '—';
          @endphp

          <div class="d-flex justify-content-between align-items-start gap-3">
            <div>
              <h5 class="mb-1">Resumen de Ingeniería</h5>
              <div class="small text-muted">
                @if($ingenieria)
                  Verificados: <b>{{ count($verIng) }}</b> ·
                  No verificados: <b class="{{ count($noIng) ? 'text-danger':'text-success' }}">{{ count($noIng) }}</b>
                @else
                  <span class="text-warning">Aún no se ha completado Ingeniería.</span>
                @endif
              </div>
            </div>
            @if($ingenieria && ($ingenieria->firma_responsable || $ingenieria->firma_supervisor))
              <div class="d-flex gap-3 align-items-center">
                @if($ingenieria->firma_responsable)
                  <img src="{{ asset($ingenieria->firma_responsable) }}" alt="Firma Resp." style="height:38px;max-width:180px;object-fit:contain">
                @endif
                @if($ingenieria->firma_supervisor)
                  <img src="{{ asset($ingenieria->firma_supervisor) }}" alt="Firma Sup." style="height:38px;max-width:180px;object-fit:contain">
                @endif
              </div>
            @endif
          </div>

          <div class="mt-2 small">
            <div><span class="text-muted">Realizado por:</span> <b>{{ $byIng }}</b></div>
            <div><span class="text-muted">Fecha:</span> <b>{{ $atIng }}</b></div>
          </div>

          <details class="mt-2">
            <summary class="fw-semibold cursor-pointer">Ver todo lo registrado</summary>
            <div class="mt-2">
              @if($obsIng)
                <div class="mb-2">
                  <div class="text-muted small">Observaciones</div>
                  <div class="p-2 bg-light rounded border small">{{ $obsIng }}</div>
                </div>
              @endif

              @if(!empty($compIng))
                <div class="mb-2">
                  <div class="text-muted small">Componentes</div>
                  <div class="row g-3 mt-1">
                    @foreach($compIng as $serie => $componentes)
                      <div class="col-12 col-md-6">
                        <div class="p-2 border rounded-3 bg-white">
                          <div class="small"><b>Serie:</b> <code>{{ $serie }}</code></div>
                          <ul class="small mb-0" style="columns:2">
                            @foreach($componentes as $name => $ok)
                              <li class="{{ $ok? 'text-success':'text-secondary' }}">{{ $name }} {!! $ok?'✅':'—' !!}</li>
                            @endforeach
                          </ul>
                        </div>
                      </div>
                    @endforeach
                  </div>
                </div>
              @endif

              @if(!empty($evIng))
                <div class="mb-1">
                  <div class="text-muted small">Evidencias</div>
                  <div class="d-flex flex-wrap gap-2 mt-1">
                    @foreach($evIng as $p)
                      @php $img = preg_match('/\.(jpg|jpeg|png|webp)$/i', $p); @endphp
                      @if($img)
                        <a href="{{ asset($p) }}" target="_blank"><img src="{{ asset($p) }}" style="height:48px;border-radius:8px;border:1px solid #e5e7eb"></a>
                      @else
                        <a class="btn btn-sm btn-outline-secondary" target="_blank" href="{{ asset($p) }}">Archivo</a>
                      @endif
                    @endforeach
                  </div>
                </div>
              @endif
            </div>
          </details>
        </div>
      </div>
    </div>

    {{-- Resumen Embalaje --}}
    <div class="col-12 col-xl-6">
      <div class="card border-0 shadow-sm rounded-4 h-100">
        <div class="card-body">
          @php
            $verEmb = (array)($embalaje?->verificados ?? []);
            $noEmb  = (array)($embalaje?->no_verificados ?? []);
            $obsEmb = $embalaje?->observaciones;
            $evEmb  = (array)($embalaje?->evidencias ?? []);
            $compEmb= (array)($embalaje?->componentes ?? []);
            $byEmb  = $embalaje?->usuario?->name ?? '—';
            $atEmb  = optional($embalaje?->created_at)->format('d/m/Y H:i') ?? '—';
          @endphp

          <div class="d-flex justify-content-between align-items-start gap-3">
            <div>
              <h5 class="mb-1">Resumen de Embalaje</h5>
              <div class="small text-muted">
                @if($embalaje)
                  Verificados: <b>{{ count($verEmb) }}</b> ·
                  No verificados: <b class="{{ count($noEmb) ? 'text-danger':'text-success' }}">{{ count($noEmb) }}</b>
                @else
                  <span class="text-warning">Aún no se ha completado Embalaje.</span>
                @endif
              </div>
            </div>
            @if($embalaje && ($embalaje->firma_responsable || $embalaje->firma_supervisor))
              <div class="d-flex gap-3 align-items-center">
                @if($embalaje->firma_responsable)
                  <img src="{{ asset($embalaje->firma_responsable) }}" alt="Firma Resp." style="height:38px;max-width:180px;object-fit:contain">
                @endif
                @if($embalaje->firma_supervisor)
                  <img src="{{ asset($embalaje->firma_supervisor) }}" alt="Firma Sup." style="height:38px;max-width:180px;object-fit:contain">
                @endif
              </div>
            @endif
          </div>

          <div class="mt-2 small">
            <div><span class="text-muted">Realizado por:</span> <b>{{ $byEmb }}</b></div>
            <div><span class="text-muted">Fecha:</span> <b>{{ $atEmb }}</b></div>
          </div>

          <details class="mt-2">
            <summary class="fw-semibold cursor-pointer">Ver todo lo registrado</summary>
            <div class="mt-2">
              @if($obsEmb)
                <div class="mb-2">
                  <div class="text-muted small">Observaciones</div>
                  <div class="p-2 bg-light rounded border small">{{ $obsEmb }}</div>
                </div>
              @endif

              @if(!empty($compEmb))
                <div class="mb-2">
                  <div class="text-muted small">Componentes</div>
                  <div class="row g-3 mt-1">
                    @foreach($compEmb as $serie => $componentes)
                      <div class="col-12 col-md-6">
                        <div class="p-2 border rounded-3 bg-white">
                          <div class="small"><b>Serie:</b> <code>{{ $serie }}</code></div>
                          <ul class="small mb-0" style="columns:2">
                            @foreach($componentes as $name => $ok)
                              <li class="{{ $ok? 'text-success':'text-secondary' }}">{{ $name }} {!! $ok?'✅':'—' !!}</li>
                            @endforeach
                          </ul>
                        </div>
                      </div>
                    @endforeach
                  </div>
                </div>
              @endif

              @if(!empty($evEmb))
                <div class="mb-1">
                  <div class="text-muted small">Evidencias</div>
                  <div class="d-flex flex-wrap gap-2 mt-1">
                    @foreach($evEmb as $p)
                      @php $img = preg_match('/\.(jpg|jpeg|png|webp)$/i', $p); @endphp
                      @if($img)
                        <a href="{{ asset($p) }}" target="_blank"><img src="{{ asset($p) }}" style="height:48px;border-radius:8px;border:1px solid #e5e7eb"></a>
                      @else
                        <a class="btn btn-sm btn-outline-secondary" target="_blank" href="{{ asset($p) }}">Archivo</a>
                      @endif
                    @endforeach
                  </div>
                </div>
              @endif
            </div>
          </details>
        </div>
      </div>
    </div>
  </div>

  {{-- ================= Formulario Entrega ================= --}}
  <form method="POST" action="{{ route('checklists.guardarEntrega', $venta->id) }}" enctype="multipart/form-data" id="entrega-form">
    @csrf
    <input type="hidden" name="verificados" id="verificados-hidden">

    <div class="row g-4">
      {{-- Series entregadas --}}
      <div class="col-12 col-lg-6">
        <div class="card border-0 shadow-sm rounded-4 h-100">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <h5 class="mb-0">Series entregadas</h5>
              <button type="button" class="btn btn-sm btn-outline-primary" id="btn-select-all">Seleccionar todos</button>
            </div>

            <div class="table-responsive">
              <table class="table table-borderless clean align-middle mb-0">
                <thead>
                  <tr>
                    <th style="width:5%"></th>
                    <th style="width:55%">Producto</th>
                    <th style="width:40%">Serie</th>
                  </tr>
                </thead>
                <tbody id="tbody-series">
                  @foreach($productos as $p)
                    @php
                      $serie = trim((string)($p->numero_serie ?? ''));
                      $prod  = trim(($p->marca ?? '').' '.($p->modelo ?? ''));
                    @endphp
                    <tr>
                      <td>
                        @if($serie)
                          <input class="form-check-input serie-check" type="checkbox" value="{{ $serie }}">
                        @else
                          <span class="badge bg-warning-subtle text-warning">—</span>
                        @endif
                      </td>
                      <td class="small">
                        {{ $p->tipo_equipo }}
                        <div class="text-muted small">{{ $prod }}</div>
                      </td>
                      <td><code>{{ $serie ?: 'Sin serie' }}</code></td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>

            <div class="mt-3">
              <div class="d-flex justify-content-between small text-muted mb-1">
                <span>Progreso</span>
                <span><b id="progress-label">0%</b> (<span id="count-checked">0</span>/<span id="count-total">0</span>)</span>
              </div>
              <div class="progress slim">
                <div id="progress-bar" class="progress-bar warn" role="progressbar" style="width:0%"></div>
              </div>
            </div>

          </div>
        </div>
      </div>

      {{-- Datos de entrega + firmas --}}
      <div class="col-12 col-lg-6">
        <div class="card border-0 shadow-sm rounded-4 h-100">
          <div class="card-body">
            {{-- Tipo de entrega --}}
            <div class="mb-3">
              <label class="form-label">Tipo de entrega</label>
              <div class="d-flex gap-3">
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="tipo_entrega" id="entrega_presencial" value="presencial" checked>
                  <label class="form-check-label" for="entrega_presencial">Presencial</label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="tipo_entrega" id="entrega_envio" value="envio">
                  <label class="form-check-label" for="entrega_envio">Envío</label>
                </div>
              </div>
            </div>

            {{-- PRESENCIAL --}}
            <div id="presencial-fields" class="vstack gap-2 mb-3">
              <div class="row g-2">
                <div class="col-md-6">
                  <label class="form-label small">Recibe (nombre)</label>
                  <input type="text" class="form-control" name="recibe_nombre">
                </div>
                <div class="col-md-6">
                  <label class="form-label small">Cargo / Área</label>
                  <input type="text" class="form-control" name="recibe_cargo">
                </div>
              </div>
              <div class="row g-2">
                <div class="col-md-8">
                  <label class="form-label small">Lugar de entrega</label>
                  <input type="text" class="form-control" name="lugar_entrega" placeholder="Hospital, área, etc.">
                </div>
                <div class="col-md-4">
                  <label class="form-label small">Fecha y hora</label>
                  <input type="datetime-local" class="form-control" name="fecha_entrega" value="{{ now()->format('Y-m-d\TH:i') }}">
                </div>
              </div>
              <div class="row g-2">
                <div class="col-md-6">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="capacitacion" id="capacitacion" value="1">
                    <label class="form-check-label" for="capacitacion">Se brindó capacitación</label>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="documentacion_entregada" id="documentacion_entregada" value="1">
                    <label class="form-check-label" for="documentacion_entregada">Documentación entregada</label>
                  </div>
                </div>
              </div>
            </div>

            {{-- ENVÍO --}}
            <div id="envio-fields" class="vstack gap-2 mb-3 d-none">
              <div class="row g-2">
                <div class="col-md-6">
                  <label class="form-label small">Paquetería</label>
                  <input type="text" class="form-control" name="paqueteria">
                </div>
                <div class="col-md-6">
                  <label class="form-label small">Guía / Tracking</label>
                  <input type="text" class="form-control" name="guia">
                </div>
              </div>
              <div class="row g-2">
                <div class="col-md-8">
                  <label class="form-label small">Dirección de envío</label>
                  <input type="text" class="form-control" name="direccion_envio">
                </div>
                <div class="col-md-4">
                  <label class="form-label small">Fecha de envío</label>
                  <input type="date" class="form-control" name="fecha_envio" value="{{ now()->format('Y-m-d') }}">
                </div>
              </div>
              <div class="row g-2">
                <div class="col-md-6">
                  <label class="form-label small">Contacto</label>
                  <input type="text" class="form-control" name="contacto_envio">
                </div>
                <div class="col-md-6">
                  <label class="form-label small">Teléfono</label>
                  <input type="text" class="form-control" name="telefono_envio">
                </div>
              </div>
            </div>

            {{-- Comentario final --}}
            <div class="mb-3">
              <label class="form-label">Comentario final</label>
              <textarea name="comentario_final" class="form-control animate-shadow" rows="3" placeholder="Observaciones generales de la entrega"></textarea>
            </div>

            {{-- Firmas --}}
            <div class="row g-3 mb-3">
              <div class="col-md-6" id="firmaClienteWrap">
                <label class="form-label">Firma Cliente</label>
                <div class="p-2 rounded border bg-white">
                  <canvas id="firmaCanvas1" width="520" height="120" class="w-100 d-block"></canvas>
                  <button type="button" id="limpiar1" class="btn btn-sm btn-light mt-2">Limpiar</button>
                  <input type="hidden" name="firma_cliente" id="firmaInput1">
                </div>
              </div>
              <div class="col-md-6">
                <label class="form-label">Firma Entrega</label>
                <div class="p-2 rounded border bg-white">
                  <canvas id="firmaCanvas2" width="520" height="120" class="w-100 d-block"></canvas>
                  <button type="button" id="limpiar2" class="btn btn-sm btn-light mt-2">Limpiar</button>
                  <input type="hidden" name="firma_entrega" id="firmaInput2">
                </div>
              </div>
            </div>

            {{-- Evidencias --}}
            <label class="form-label">Evidencias (foto o archivo)</label>
            <input type="file" name="evidencias[]" class="form-control mb-3 animate-shadow" multiple accept="image/*,application/pdf">

            <div class="d-flex justify-content-end">
              <button type="submit" id="submit-btn" class="btn btn-primary btn-lg animate-pop">Completar checklist</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>
</div>

<style>
.progress.slim{height:8px;border-radius:6px;overflow:hidden;background:#eef2f7}
.progress-bar.warn{background:#86b7fe}
summary{cursor:pointer}
</style>

<script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

<script>
/* ===== Series seleccionadas + progreso ===== */
document.addEventListener('DOMContentLoaded', () => {
  const $checks = Array.from(document.querySelectorAll('.serie-check'));
  const $countTotal = document.getElementById('count-total');
  const $countChecked = document.getElementById('count-checked');
  const $progressBar = document.getElementById('progress-bar');
  const $progressLabel = document.getElementById('progress-label');
  const $hidden = document.getElementById('verificados-hidden');

  function update() {
    const total = $checks.length || 1;
    const sel = $checks.filter(c => c.checked).map(c => c.value);
    const pct = Math.round((sel.length / total) * 100);
    ($countTotal||{}).textContent = $checks.length;
    ($countChecked||{}).textContent = sel.length;
    if ($progressBar) $progressBar.style.width = pct + '%';
    if ($progressLabel) $progressLabel.textContent = pct + '%';
    if ($hidden) $hidden.value = JSON.stringify(sel);
  }
  update();
  $checks.forEach(c => c.addEventListener('change', update));
  document.getElementById('btn-select-all')?.addEventListener('click', () => {
    const allOn = $checks.some(c => !c.checked);
    $checks.forEach(c => c.checked = allOn);
    update();
  });
});
</script>

<script>
/* ===== Signature Pad + reglas: en ENVÍO no se pide firma del cliente ===== */
function makeSignaturePad(canvasId, clearBtnId, inputId, options = {}) {
  const cfg = { minWidth:1.4, maxWidth:3.2, color:'#111827', background:'#ffffff', smoothing:0.3, velocityFilter:0.8, onChange:null, ...options };
  const canvas=document.getElementById(canvasId), input=document.getElementById(inputId), clear=document.getElementById(clearBtnId);
  if (!canvas) return { isEmpty:()=>true, syncToInput:()=>{} };
  canvas.style.touchAction='none'; canvas.style.webkitUserSelect='none'; canvas.style.userSelect='none';
  const ctx=canvas.getContext('2d'); let dpr=Math.max(window.devicePixelRatio||1,1);
  let strokes=[], current=[], isDrawing=false, lastWidth=cfg.maxWidth, lastVelocity=0;
  const css=()=>{const r=canvas.getBoundingClientRect(); return {w:Math.max(1,r.width), h:Math.max(1,r.height)}};
  function setup(){const {w,h}=css(); dpr=Math.max(window.devicePixelRatio||1,1); canvas.width=Math.round(w*dpr); canvas.height=Math.round(h*dpr); ctx.setTransform(1,0,0,1,0,0); ctx.scale(dpr,dpr); ctx.lineCap='round'; ctx.lineJoin='round'; ctx.miterLimit=2; redraw();}
  const dist=(a,b)=>Math.hypot(a.x-b.x,a.y-b.y), mid=(a,b)=>({x:(a.x+b.x)/2,y:(a.y+b.y)/2}), clamp=(v,a,b)=>Math.max(a,Math.min(b,v));
  const vel=(p1,p2)=>dist(p1,p2)/Math.max(1,(p2.t-p1.t));
  const widthFromV=(v)=>{ lastVelocity=cfg.velocityFilter*v+(1-cfg.velocityFilter)*lastVelocity; const target=cfg.maxWidth-lastVelocity*15; const w=clamp(target,cfg.minWidth,cfg.maxWidth); lastWidth=cfg.smoothing*w+(1-cfg.smoothing)*lastWidth; return lastWidth; };
  function seg(p0,p1,p2){ const m1=mid(p0,p1), m2=mid(p1,p2), w=widthFromV(vel(p1,p2)); ctx.strokeStyle=cfg.color; ctx.lineWidth=w; ctx.beginPath(); ctx.moveTo(m1.x,m1.y); ctx.quadraticCurveTo(p1.x,p1.y,m2.x,m2.y); ctx.stroke(); }
  function draw(pts){ if(pts.length<2) return; while(pts.length<3) pts.unshift(pts[0]); lastWidth=cfg.maxWidth; lastVelocity=0; for(let i=2;i<pts.length;i++) seg(pts[i-2],pts[i-1],pts[i]); }
  function redraw(){ const {w,h}=css(); ctx.clearRect(0,0,w,h); strokes.forEach(draw); if(current.length) draw(current); }
  const pos=e=>{const r=canvas.getBoundingClientRect(); return {x:e.clientX-r.left,y:e.clientY-r.top,t:e.timeStamp||performance.now()}};
  function down(e){ if(e.button!==undefined && e.button!==0) return; isDrawing=true; current=[pos(e)]; canvas.setPointerCapture?.(e.pointerId); e.preventDefault(); }
  function move(e){ if(!isDrawing) return; current.push(pos(e)); if(current.length>=3){ seg(current[current.length-3],current[current.length-2],current[current.length-1]); cfg.onChange && cfg.onChange(false); } e.preventDefault(); }
  function up(e){ if(!isDrawing) return; isDrawing=false; if(current.length>1){ strokes.push(current.slice()); sync(); cfg.onChange && cfg.onChange(false); } current=[]; e.preventDefault(); }
  function bind(){ canvas.addEventListener('pointerdown',down,{passive:false}); canvas.addEventListener('pointermove',move,{passive:false}); canvas.addEventListener('pointerup',up,{passive:false}); canvas.addEventListener('pointerleave',up,{passive:false}); clear?.addEventListener('click',()=>{strokes=[]; current=[]; setup(); input && (input.value=''); cfg.onChange && cfg.onChange(true);}); }
  function toDataURL(){ const out=document.createElement('canvas'); out.width=canvas.width; out.height=canvas.height; const o=out.getContext('2d'); o.fillStyle=cfg.background; o.fillRect(0,0,out.width,out.height); o.drawImage(canvas,0,0); return out.toDataURL('image/png'); }
  function isEmpty(){ return strokes.length===0 && current.length===0; }
  function sync(){ if(!input) return; input.value = isEmpty() ? '' : toDataURL(); }
  setup(); bind();
  return { isEmpty, syncToInput: sync };
}

const $saveBtn = document.getElementById('submit-btn');
const pad1 = makeSignaturePad('firmaCanvas1','limpiar1','firmaInput1',{ onChange:toggleSave });
const pad2 = makeSignaturePad('firmaCanvas2','limpiar2','firmaInput2',{ onChange:toggleSave });

const $radioPres = document.getElementById('entrega_presencial');
const $radioEnv  = document.getElementById('entrega_envio');
const $firmaClienteWrap = document.getElementById('firmaClienteWrap');
const $presWrap  = document.getElementById('presencial-fields');
const $envWrap   = document.getElementById('envio-fields');

function needsClientSignature(){ return $radioPres.checked; }
function syncTipoEntrega(){
  const envio = $radioEnv.checked;
  $envWrap.classList.toggle('d-none', !envio);
  $presWrap.classList.toggle('d-none', envio);
  $firmaClienteWrap.classList.toggle('d-none', envio); // <-- en envío no se muestra firma del cliente
  toggleSave();
}
[$radioPres,$radioEnv].forEach(r=> r.addEventListener('change', syncTipoEntrega));
syncTipoEntrega();

function toggleSave(){
  // En presencial: se requieren ambas firmas; en envío: solo la de quien entrega (pad2)
  const needClient = needsClientSignature();
  const hidden = needClient ? (pad1.isEmpty() || pad2.isEmpty()) : (pad2.isEmpty());
  if ($saveBtn) $saveBtn.classList.toggle('d-none', hidden);
}
toggleSave();

document.getElementById('entrega-form')?.addEventListener('submit', (e) => {
  const needClient = needsClientSignature();
  if ((needClient && (pad1.isEmpty() || pad2.isEmpty())) || (!needClient && pad2.isEmpty())) {
    e.preventDefault();
    Toastify({ text:'Faltan firmas obligatorias.', duration:2200, backgroundColor:'#f59e0b' }).showToast();
    toggleSave();
    return;
  }
  pad2.syncToInput();
  if (needClient) pad1.syncToInput();
});
</script>
@endsection
