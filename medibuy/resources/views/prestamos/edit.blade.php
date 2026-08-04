@extends('layouts.app')

@section('title', 'Préstamos')
@section('titulo', 'Préstamos')

@section('content')
<link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600,700" rel="stylesheet">

<style>
:root{
  --mint:#48cfad; --mint-dark:#34c29e;
  --ink:#2a2e35; --muted:#7a7f87; --line:#e9ecef; --card:#ffffff;
}
*{box-sizing:border-box}
body{font-family:"Open Sans",sans-serif;background:#eaebec}

/* Page */
.edit-wrap{max-width:980px;margin:110px auto 40px;padding:0 16px;}
.panel{background:var(--card);border-radius:16px;box-shadow:0 16px 40px rgba(18,38,63,.12);overflow:hidden;}
.panel-head{padding:22px 26px;border-bottom:1px solid var(--line);display:flex;align-items:center;gap:14px;justify-content:space-between;}
.hgroup h2{margin:0;font-weight:700;color:var(--ink);}
.hgroup p{margin:2px 0 0;color:var(--muted);font-size:14px}
.back-link{display:inline-flex;align-items:center;gap:8px;color:var(--muted);text-decoration:none;padding:8px 12px;border-radius:10px;border:1px solid var(--line);background:#fff;}
.back-link:hover{color:var(--ink);border-color:#dfe3e8}

/* Stepper */
.stepper{display:flex;align-items:center;gap:10px;padding:18px 26px;background:#fff;border-bottom:1px solid var(--line);}
.dot{width:28px;height:28px;border-radius:50%;display:grid;place-items:center;font-weight:700;color:#fff;background:#cfd4da;}
.dot.active{background:var(--mint);}
.line{flex:1;height:2px;background:#e6eaef}

/* Form base */
.form{padding:26px;}
.grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:22px;}
@media (max-width: 800px){.grid{grid-template-columns:1fr}}

.field{position:relative;background:#fff;border:1px solid var(--line);border-radius:12px;padding:16px 14px 10px;transition:box-shadow .2s,border-color .2s;}
.field:focus-within{border-color:#d8dee6;box-shadow:0 8px 24px rgba(18,38,63,.08)}
.field input,.field select,.field textarea{
  width:100%;border:0;outline:0;background:transparent;font-size:15px;color:var(--ink);padding-top:10px;resize:vertical;
}
.field textarea{min-height:80px}
.field label{position:absolute;left:14px;top:14px;color:var(--muted);font-size:13px;transition:transform .15s,color .15s,font-size .15s,top .15s;pointer-events:none;}
.field input::placeholder{color:transparent;}
.field input:focus + label,
.field input:not(:placeholder-shown) + label,
.field textarea:focus + label,
.field textarea:not(:placeholder-shown) + label{
  top:8px;transform:translateY(-10px);font-size:11px;color:var(--mint-dark);
}

/* Blocks / tables */
.block{border:1px dashed #dfe3e8;border-radius:14px;padding:16px;background:#fafbfc;}
.table{width:100%;border-collapse:collapse}
.table th,.table td{padding:10px;border-bottom:1px solid #edf0f3;text-align:left}
.table thead th{font-size:12px;color:var(--muted);text-transform:uppercase;letter-spacing:.03em}
tbody tr{transition:background .2s ease, transform .08s ease}
tbody tr:hover{background:#fafbfc}
tbody tr:active{transform:scale(.999)}

/* Responsive table -> cards */
@media (max-width: 760px){
  .table thead{display:none}
  .table, .table tbody, .table tr, .table td{display:block;width:100%}
  .table tr{
    background:#fff;border:1px solid var(--line);border-radius:12px;
    padding:10px;margin:10px 0;box-shadow:0 10px 22px rgba(18,38,63,.06);
  }
  .table td{border-bottom:none;padding:8px 6px;}
  .table td::before{
    content: attr(data-label);
    display:block;font-size:11px;color:var(--muted);
    text-transform:uppercase;letter-spacing:.03em;margin-bottom:2px;
  }
  .td-actions{display:flex;justify-content:flex-end;gap:8px;margin-top:8px}
}

/* Firma */
.sigbox{border:1px dashed #dfe3e8;border-radius:14px;background:#fafbfc;display:grid;gap:8px;padding:12px}
.sig-wrap{position:relative;border:1px solid #edf0f3;border-radius:12px;overflow:hidden;background:#fff}
#sigPad{width:100%;height:160px;display:block}
.sig-actions{display:flex;gap:8px;align-items:center;justify-content:flex-end}

/* Buttons */
.actions{display:flex;gap:12px;justify-content:flex-end;margin-top:10px;}
.btn{border:0;border-radius:12px;padding:12px 18px;font-weight:700;cursor:pointer;transition:transform .05s,box-shadow .2s,background .2s,color .2s;}
.btn:active{transform:translateY(1px)}
.btn-primary{background:var(--mint);color:#fff;box-shadow:0 12px 22px rgba(72,207,173,.26);}
.btn-primary:hover{background:var(--mint-dark)}
.btn-ghost{background:#fff;color:var(--ink);border:1px solid var(--line);}
.btn-ghost:hover{border-color:#dfe3e8}
.btn-sm{padding:8px 12px;font-size:13px}

.small{color:var(--muted);font-size:12px}
.is-invalid{border-color:#f9c0c0 !important}
.error{color:#cc4b4b;font-size:12px;margin-top:6px}
.step{display:none;animation:fade .2s ease;}
.step.active{display:block}
@keyframes fade{from{opacity:0;transform:translateY(6px)}to{opacity:1;transform:translateY(0)}}
</style>

<div class="edit-wrap" style="margin-top:25px;">
  <div class="panel">
    {{-- Encabezado --}}
    <div class="panel-head">
      <div class="hgroup">
        <h2>Editar paquete #{{ $prestamo->id }}</h2>
        <p>Actualiza datos y equipos del préstamo.</p>
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

    <form class="form" id="editForm" method="POST" action="{{ route('prestamos.update', $prestamo->id) }}">
      @csrf
      @method('PUT')

      {{-- STEP 1 --}}
      <div class="step active" id="step-1">
        <div class="grid">
          {{-- Cliente --}}
          <div>
            <div class="field">
              <select name="cliente_id">
                <option value="">— Selecciona cliente —</option>
                @foreach(\App\Models\Cliente::orderBy('nombre')->get() as $c)
                  <option value="{{ $c->id }}" {{ $prestamo->cliente_id == $c->id ? 'selected' : '' }}>
                    {{ $c->nombre }}
                  </option>
                @endforeach
              </select>
              <label>Cliente</label>
            </div>
          </div>

          {{-- Estado --}}
          <div>
            <div class="field">
              <select name="estado" required>
                @foreach(['activo','devuelto','retrasado','cancelado','vendido'] as $st)
                  <option value="{{ $st }}" {{ $prestamo->estado === $st ? 'selected' : '' }}>
                    {{ ucfirst($st) }}
                  </option>
                @endforeach
              </select>
              <label>Estado del paquete</label>
            </div>
          </div>

          {{-- Fechas --}}
          <div>
            <div class="field">
              <input type="date" name="fecha_prestamo" value="{{ optional($prestamo->fecha_prestamo)->format('Y-m-d') }}" placeholder=" " required>
              <label>Fecha de salida</label>
            </div>
          </div>
          <div>
            <div class="field">
              <input type="date" name="fecha_devolucion_estimada" value="{{ optional($prestamo->fecha_devolucion_estimada)->format('Y-m-d') }}" placeholder=" " required>
              <label>Fecha estimada de regreso</label>
            </div>
          </div>

          {{-- Regreso real (opcional) --}}
          <div>
            <div class="field">
              <input type="date" name="fecha_devolucion_real" value="{{ optional($prestamo->fecha_devolucion_real)->format('Y-m-d') }}" placeholder=" ">
              <label>Fecha de devolución real (opcional)</label>
            </div>
          </div>

          {{-- Observaciones --}}
          <div style="grid-column:1/-1">
            <div class="field">
              <textarea name="observaciones" placeholder=" ">{{ $prestamo->observaciones }}</textarea>
              <label>Observaciones (opcional)</label>
            </div>
          </div>

          {{-- Firma: mantener o actualizar --}}
          <div style="grid-column:1/-1">
            <div class="sigbox">
              <div class="small" style="display:flex;align-items:center;gap:10px;justify-content:space-between;">
                <div>
                  <strong>Firma</strong> · Por defecto se mantendrá la firma actual.
                </div>
                <label style="display:flex;align-items:center;gap:6px;">
                  <input type="checkbox" id="toggleUpdateSig"> Actualizar firma
                </label>
              </div>

              {{-- Vista de la firma actual --}}
              <div id="currentSig" class="sig-wrap" style="display: {{ $prestamo->firmaDigital ? 'block' : 'none' }};">
                @if($prestamo->firmaDigital)
                  <img src="{{ $prestamo->firmaDigital }}" alt="Firma actual" style="max-height:160px;display:block;margin:auto;">
                @endif
              </div>

              {{-- Canvas para nueva firma (oculto hasta activar switch) --}}
              <div id="canvasWrap" class="sig-wrap" style="display:none;">
                <canvas id="sigPad"></canvas>
              </div>
              <div id="sigBtns" class="sig-actions" style="display:none;">
                <button type="button" class="btn btn-ghost btn-sm" id="sigUndo">Deshacer</button>
                <button type="button" class="btn btn-ghost btn-sm" id="sigClear">Limpiar</button>
                <span class="small" style="margin-left:auto">Si activas “Actualizar firma”, será obligatoria.</span>
              </div>
            </div>
          </div>
        </div>

        <div class="actions">
          <button type="button" id="toStep2" class="btn btn-primary">Continuar a equipos</button>
        </div>
      </div>

      {{-- STEP 2 --}}
      <div class="step" id="step-2">
        <div class="grid">
          <div>
            <div class="field">
              <input type="text" id="scanInput" placeholder=" " autocomplete="off">
              <label>Escanea o escribe número de serie (Enter al final)</label>
            </div>
            <div class="small">Sugerido con pistola; también acepta tecleo. Ventana de 30 s.</div>
          </div>
          <div>
            <div class="field">
              <input type="text" value="0 equipos" id="countInfo" placeholder=" " readonly>
              <label>Total en paquete</label>
            </div>
          </div>
        </div>

        <div class="block" style="margin-top:20px;">
          <table class="table">
            <thead>
              <tr>
                <th style="width:60px">#</th>
                <th>Serie</th>
                <th>Subtipo</th>
                <th>Marca</th>
                <th>Modelo</th>
                <th style="text-align:right">Quitar</th>
              </tr>
            </thead>
            <tbody id="scanTbody"></tbody>
          </table>
        </div>

        {{-- hidden fields --}}
        <div id="hiddenSerials"></div>
        <input type="hidden" name="firmaDigital" id="firmaDigital">

        <div class="actions">
          <a href="{{ route('prestamos.index') }}" class="btn btn-ghost">Cancelar</a>
          <button type="button" class="btn btn-ghost" id="backStep1">Regresar</button>
          <button type="submit" class="btn btn-primary" id="saveBtn" disabled>Guardar cambios</button>
        </div>
      </div>
    </form>
  </div>
</div>

@php
  // PREPARA datos de registros en PHP para evitar errores de Blade al usar @json
  $initialRegistros = $prestamo->registros->map(function($r){
      return [
          'numero_serie'   => $r->numero_serie,
          'subtipo_equipo' => $r->subtipo_equipo,
          'marca'          => $r->marca,
          'modelo'         => $r->modelo,
      ];
  })->values()->toArray();
@endphp

{{-- Scripts --}}
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
/* ===== Stepper ===== */
function gotoStep(n){
  document.querySelectorAll('.step').forEach(s=>s.classList.remove('active'));
  document.getElementById('step-'+n).classList.add('active');
  document.querySelectorAll('.dot').forEach(d=>d.classList.remove('active'));
  document.querySelector('.dot[data-step="'+n+'"]').classList.add('active');
  window.scrollTo({top:0,behavior:'smooth'});
}
$('#toStep2').on('click', function(){
  if(document.getElementById('toggleUpdateSig').checked){
    if(!(window._sig_hasInk && window._sig_hasInk())){ alert('Por favor firma para continuar.'); return; }
  }
  gotoStep(2); $('#scanInput').focus();
});
$('#backStep1').on('click', ()=> gotoStep(1));

/* ===== Firma en canvas (solo si se activa) ===== */
(() => {
  const wrap = document.getElementById('canvasWrap');
  const cur  = document.getElementById('currentSig');
  const btns = document.getElementById('sigBtns');
  const chk  = document.getElementById('toggleUpdateSig');
  let pad, ctx, strokes=[], drawing=false;

  function ensurePad(){
    if(pad) return;
    pad = document.getElementById('sigPad');
    ctx = pad.getContext('2d');
    ctx.lineWidth=2; ctx.lineCap='round'; ctx.strokeStyle='#2a2e35';
    const resize = ()=>{ pad.width = wrap.clientWidth; pad.height=160; drawAll(); };
    window.addEventListener('resize', resize); resize();

    const pos = (e)=>{ const r=pad.getBoundingClientRect(); const t=e.touches?e.touches[0]:e; return {x:t.clientX-r.left,y:t.clientY-r.top}; };
    const line=(a,b)=>{ ctx.beginPath(); ctx.moveTo(a.x,a.y); ctx.lineTo(b.x,b.y); ctx.stroke(); };
    function drawAll(){ ctx.clearRect(0,0,pad.width,pad.height); strokes.forEach(s=>{ for(let i=1;i<s.length;i++) line(s[i-1], s[i]); }); }

    pad.addEventListener('mousedown', e=>{drawing=true; strokes.push([pos(e)])});
    pad.addEventListener('mousemove', e=>{ if(!drawing) return; const p=pos(e); const s=strokes.at(-1); line(s.at(-1),p); s.push(p); });
    window.addEventListener('mouseup', ()=> drawing=false);

    pad.addEventListener('touchstart', e=>{drawing=true; strokes.push([pos(e)])},{passive:true});
    pad.addEventListener('touchmove', e=>{ if(!drawing) return; const p=pos(e); const s=strokes.at(-1); line(s.at(-1),p); s.push(p); },{passive:true});
    pad.addEventListener('touchend', ()=> drawing=false);

    document.getElementById('sigClear').addEventListener('click', ()=>{strokes=[]; drawAll();});
    document.getElementById('sigUndo').addEventListener('click', ()=>{strokes.pop(); drawAll();});
    window._sig_hasInk = ()=> strokes.length>0 && strokes.some(s=>s.length>1);
    window._sig_dataURL = ()=> pad.toDataURL('image/png');
  }

  chk.addEventListener('change', ()=>{
    const on = chk.checked;
    wrap.style.display = on?'block':'none';
    btns.style.display = on?'flex':'none';
    cur.style.display  = on?'none':'block';
    if(on) ensurePad();
  });
})();

/* ===== Escaneo / tipeo: sin bloqueo por "manual" y con ventana de 30s ===== */
const scanned = new Map(); // serie -> registro
const $tbody = $('#scanTbody'), $hidden = $('#hiddenSerials'), $countInfo = $('#countInfo');
const ACCEPT_WINDOW_MS = 30000; // 30s de comodidad para escribir antes de Enter

const beep = (ok=true) => { try{
  const ctx = new (window.AudioContext||window.webkitAudioContext)();
  const o = ctx.createOscillator(), g = ctx.createGain();
  o.type = ok ? 'triangle' : 'sawtooth'; o.frequency.value = ok ? 880 : 220;
  o.connect(g); g.connect(ctx.destination); g.gain.setValueAtTime(.05, ctx.currentTime);
  o.start(); o.stop(ctx.currentTime+0.12);
}catch{} };

function renderTable(){
  $tbody.empty();
  let i=1;
  scanned.forEach((reg, serie)=>{
    $tbody.append(`
      <tr>
        <td data-label="#">${i++}</td>
        <td data-label="Serie"><code>${serie}</code></td>
        <td data-label="Subtipo">${reg.subtipo_equipo ?? '-'}</td>
        <td data-label="Marca">${reg.marca ?? '-'}</td>
        <td data-label="Modelo">${reg.modelo ?? '-'}</td>
        <td data-label="Quitar" style="text-align:right">
          <button type="button" class="btn btn-ghost btn-sm" data-serie="${serie}">Quitar</button>
        </td>
      </tr>
    `);
  });
  $('#saveBtn').prop('disabled', scanned.size===0);
  $countInfo.val(`${scanned.size} equipos`);

  // inputs ocultos
  $hidden.empty();
  Array.from(scanned.keys()).forEach(s => $hidden.append(`<input type="hidden" name="seriales[]" value="${$('<div/>').text(s).html()}">`));
}
$tbody.on('click','[data-serie]', function(){ scanned.delete($(this).data('serie')); renderTable(); $('#scanInput').focus(); });

// Estado de sesión de tipeo para limpiar tras 30s
let sessionStart = null;
let sessionTimer = null;
function startSessionTimer(){
  if (sessionTimer) clearTimeout(sessionTimer);
  sessionTimer = setTimeout(()=>{ sessionStart = null; }, ACCEPT_WINDOW_MS);
}

$('#scanInput')
  .attr('autocomplete','off')
  .on('focus', function(){ this.select(); })
  .on('paste', e=> e.preventDefault())
  .on('keydown', function(e){
    // registrar inicio de sesión de tipeo y renovar timer de 30s
    if (sessionStart === null) sessionStart = performance.now();
    startSessionTimer();

    // Enter (o Tab si tu pistola lo envía)
    if (e.key === 'Enter' /* || e.key === 'Tab' */) {
      e.preventDefault();
      const serie = this.value.trim();

      // Reiniciar sesión y timer; NO mostramos ninguna alerta de “manual”
      sessionStart = null; startSessionTimer();

      if (!serie) { this.value=''; return; }
      if (scanned.has(serie)){ beep(false); this.value=''; return; }

      $.ajax({
        method: 'POST',
        url: '{{ route('registros.lookup') }}',
        data: { numero_serie: serie, _token: '{{ csrf_token() }}' },
        success: function(res){
          scanned.set(serie, res.registro);
          renderTable(); beep(true);
        },
        error: function(xhr){
          const msg = (xhr.responseJSON && xhr.responseJSON.msg) ? xhr.responseJSON.msg : 'Error';
          alert(`No agregado (${serie}): ${msg}`); beep(false);
        },
        complete: ()=> { $('#scanInput').val('').focus(); }
      });
    }
  })
  .on('blur', function(){ /* opcional: no reseteamos tabla ni input en blur */ });

/* ===== Precarga con equipos actuales (seguro para Blade) ===== */
(function preload(){
  const initial = @json($initialRegistros);
  initial.forEach(obj=>{
    scanned.set(obj.numero_serie, {
      subtipo_equipo: obj.subtipo_equipo,
      marca: obj.marca,
      modelo: obj.modelo
    });
  });
  renderTable();
})();

/* ===== Submit ===== */
$('#editForm').on('submit', function(){
  if(document.getElementById('toggleUpdateSig').checked){
    if(!(window._sig_hasInk && window._sig_hasInk())){ alert('Falta la firma.'); return false; }
    $('#firmaDigital').val(window._sig_dataURL());
  } else {
    $('#firmaDigital').val(''); // no enviar -> backend mantiene la actual
  }

  if(scanned.size===0){
    if(!confirm('No hay equipos en el paquete. ¿Continuar de todas formas?')) return false;
  }
});
</script>
@endsection
