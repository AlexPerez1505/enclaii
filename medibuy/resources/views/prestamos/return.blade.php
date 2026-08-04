{{-- resources/views/prestamos/return.blade.php --}}
@extends('layouts.app')

@section('title', 'Registrar regreso · Paquete #'.$prestamo->id)
@section('titulo', 'Registrar regreso')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

<style>
:root{
  --bg:#f7f8fb; --card:#ffffff; --ink:#1f2937; --muted:#6b7280; --line:#e5e7eb;
  --accent:#c7d2fe; --accent-ink:#3730a3;
  --ok:#a7f3d0; --ok-ink:#065f46; --warn:#fde68a; --warn-ink:#92400e; --error:#fecaca; --error-ink:#7f1d1d;
}
*{box-sizing:border-box}
body{font-family:Inter,system-ui,Segoe UI,Roboto,Arial,sans-serif;background:var(--bg)}
.page{max-width:1100px;margin:110px auto 40px;padding:0 16px}

.panel{background:var(--card);border:1px solid var(--line);border-radius:16px;box-shadow:0 16px 40px rgba(18,38,63,.08);overflow:hidden}
.head{padding:18px 22px;border-bottom:1px solid var(--line);display:flex;align-items:center;justify-content:space-between;gap:10px}
.head h2{margin:0;color:var(--ink);font-size:20px}
.back{display:inline-flex;align-items:center;gap:8px;color:var(--muted);text-decoration:none;border:1px solid var(--line);padding:8px 12px;border-radius:10px;background:#fff}
.back:hover{color:var(--ink);border-color:#dfe3e8}

.body{padding:18px 22px;display:grid;grid-template-columns:1.2fr .8fr;gap:18px}
@media (max-width: 980px){ .body{grid-template-columns:1fr} }

.card{border:1px solid var(--line);border-radius:14px;background:#fff;padding:14px}
.h{display:flex;align-items:center;gap:8px;margin-bottom:10px}
.dot{width:10px;height:10px;border-radius:999px;background:var(--accent)}
.h h3{margin:0;color:var(--accent-ink);font-size:16px}

.row{display:flex;gap:10px;align-items:center}
.row input{
  flex:1;border:1.5px solid var(--line);border-radius:12px;padding:12px 14px;font-size:16px;outline:none;
  transition:border-color .2s, box-shadow .2s;
}
.row input:focus{border-color:var(--accent-ink);box-shadow:0 0 0 4px rgba(55,48,163,.08)}
.btn{border:0;border-radius:12px;padding:10px 14px;font-weight:700;cursor:pointer;background:var(--accent);color:var(--accent-ink)}
.small{font-size:12px;color:var(--muted);margin-top:4px}

.tabs{display:flex;gap:8px;margin:8px 0 12px}
.tab{border:1px solid var(--line);background:#f8fafc;color:var(--ink);padding:8px 12px;border-radius:999px;cursor:pointer;font-size:13px}
.tab.active{background:#eef2ff;border-color:#c7d2fe;color:#3730a3;font-weight:700}

.kpis{display:grid;grid-template-columns:repeat(4,1fr);gap:10px;margin-top:10px}
.kpi{background:#f8fafc;border:1px solid var(--line);border-radius:12px;padding:10px;text-align:center}
.kpi b{display:block;font-size:20px;color:var(--ink)}
.kpi span{font-size:12px;color:var(--muted)}

.badge{display:inline-flex;align-items:center;gap:6px;border-radius:999px;padding:4px 10px;font-size:12px;border:1px solid var(--line)}
.bg-warn{background:var(--warn);color:var(--warn-ink)}
.bg-ok{background:var(--ok);color:var(--ok-ink)}
.bg-error{background:var(--error);color:var(--error-ink)}

.list{display:grid;gap:8px;max-height:260px;overflow:auto;margin-top:6px}

.log{font-family:ui-monospace,Consolas,Menlo,monospace;font-size:12px;background:#0b1020;color:#d1d5db;border-radius:12px;padding:10px;max-height:240px;overflow:auto}
.hr{border:0;border-top:1px dashed var(--line);margin:10px 0}
.note{font-size:12px;color:var(--muted)}
</style>

<div class="page">
  <div class="panel">
    <div class="head">
      <h2>Registrar regreso · Paquete #{{ $prestamo->id }} — {{ optional($prestamo->cliente)->nombre ?? 'Sin cliente' }}</h2>
      <a class="back" href="{{ route('prestamos.show', $prestamo->id) }}">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
        Volver
      </a>
    </div>

    <div class="body">
      {{-- IZQUIERDA: escaneo, tabs y KPIs --}}
      <div class="card">
        <div class="h"><span class="dot"></span><h3>Escaneo</h3></div>

        <div class="tabs" role="tablist" aria-label="Modo de escaneo">
          <button class="tab active" data-mode="devolucion" role="tab" aria-selected="true">Devolución</button>
          <button class="tab"        data-mode="vendido"    role="tab" aria-selected="false">Vendido</button>
        </div>

        <div class="row">
          <input id="scanInput" autocomplete="off" placeholder="Escanea o escribe número de serie y Enter…">
          <button class="btn" id="scanBtn">Marcar</button>
        </div>
        <div class="note" id="modeHelp">
          En <b>Devolución</b>, registra el regreso del equipo. En <b>Vendido</b>, se marca como no retornará.
        </div>

        <div class="kpis" style="margin-top:12px" aria-live="polite">
          <div class="kpi"><b id="kpiTotal">0</b><span>Total</span></div>
          <div class="kpi"><b id="kpiDevueltos">0</b><span>Devueltos</span></div>
          <div class="kpi"><b id="kpiVendidos">0</b><span>Vendidos</span></div>
          <div class="kpi"><b id="kpiPendDevol">0</b><span>Pend. devolución</span></div>
        </div>

        <div class="hr"></div>

        <div>
          <div class="h" style="margin-top:4px"><span class="dot"></span><h3>Faltantes de devolución</h3></div>
          <div id="faltantesDevol" class="list"></div>
        </div>
      </div>

      {{-- DERECHA: registro/log + metadatos --}}
      <div class="card">
        <div class="h"><span class="dot"></span><h3>Registro</h3></div>
        <div id="log" class="log" aria-live="polite"></div>
        <div class="hr"></div>
        <div class="small">
          Estado: <strong>{{ ucfirst($prestamo->estado) }}</strong><br>
          Salida: <strong>{{ optional($prestamo->fecha_prestamo)->format('Y-m-d') }}</strong><br>
          Est. regreso: <strong>{{ optional($prestamo->fecha_devolucion_estimada)->format('Y-m-d') }}</strong>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  // === Endpoints ===
  const URL_RESUMEN   = @json(route('prestamos.scan.resumen',    $prestamo->id));
  const URL_SCAN_DEV  = @json(route('prestamos.scan.devolucion', $prestamo->id));
  const URL_SCAN_VEND = @json(route('prestamos.scan.vendido',    $prestamo->id));
  const CSRF          = @json(csrf_token());

  // === UI helpers ===
  let MODE = 'devolucion'; // 'devolucion' | 'vendido'

  function nowHHMMSS(){ const d=new Date(); return d.toTimeString().slice(0,8); }
  function logLine(msg, ok=null){
    const el = document.getElementById('log');
    const pref = ok===true ? '✔' : ok===false ? '✘' : '•';
    el.textContent = `[${nowHHMMSS()}] ${pref} ${msg}\n` + el.textContent;
  }
  function beep(ok=true){
    try{
      const ctx = new (window.AudioContext||window.webkitAudioContext)();
      const o = ctx.createOscillator(), g = ctx.createGain();
      o.type = ok ? 'triangle' : 'sawtooth'; o.frequency.value = ok ? 880 : 220;
      o.connect(g); g.connect(ctx.destination); g.gain.setValueAtTime(.05, ctx.currentTime);
      o.start(); o.stop(ctx.currentTime+0.12);
    }catch{}
  }
  function setMode(m){
    MODE = m;
    document.querySelectorAll('.tab').forEach(t=>{
      t.classList.toggle('active', t.dataset.mode===m);
      t.setAttribute('aria-selected', t.dataset.mode===m ? 'true':'false');
    });
    const help = document.getElementById('modeHelp');
    if(m==='devolucion') help.innerHTML = 'En <b>Devolución</b>, registra el regreso del equipo.';
    if(m==='vendido')    help.innerHTML = 'En <b>Vendido</b>, se marca como no retornará (queda como vendido).';
  }
  document.querySelectorAll('.tab').forEach(t=>{
    t.addEventListener('click', ()=> setMode(t.dataset.mode));
  });

  // === Red ===
  async function getResumen(){
    const res = await fetch(URL_RESUMEN, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept':'application/json' }, cache:'no-store' });
    if(!res.ok) throw new Error(`HTTP ${res.status}`);
    const j = await res.json();
    if(!j.ok) throw new Error(j.msg || 'Respuesta inválida');
    return j.metrics || {};
  }

  function renderResumen(m){
    // KPIs (sin salida)
    document.getElementById('kpiTotal')    .textContent = m.total     ?? 0;
    document.getElementById('kpiDevueltos').textContent = m.devueltos ?? 0;
    document.getElementById('kpiVendidos') .textContent = m.vendidos  ?? 0;
    document.getElementById('kpiPendDevol').textContent = m.pendDevol ?? 0;

    // Faltantes devolución
    const cont = document.getElementById('faltantesDevol');
    cont.innerHTML = '';
    (m.faltantesDevol||[]).forEach(s=>{
      const div = document.createElement('div');
      div.className = 'badge bg-warn';
      div.textContent = `• ${s}`;
      cont.appendChild(div);
    });
  }

  async function refreshResumen(){
    try{
      const m = await getResumen();
      renderResumen(m);
      logLine('Resumen actualizado');
    }catch(err){
      logLine('No se pudo actualizar el resumen', false);
      console.error(err);
    }
  }

  async function postScan(url, numero_serie){
    const res = await fetch(url, {
      method:'POST',
      headers:{
        'Content-Type':'application/json',
        'X-Requested-With':'XMLHttpRequest',
        'X-CSRF-TOKEN': CSRF,
        'Accept':'application/json'
      },
      body: JSON.stringify({ numero_serie })
    });
    const j = await res.json().catch(()=> ({}));
    return { httpOk: res.ok, data: j };
  }

  async function marcar(serieRaw){
    const serie = (serieRaw || '').trim();
    if(!serie) return;

    const url = (MODE==='devolucion') ? URL_SCAN_DEV : URL_SCAN_VEND;

    try{
      const { httpOk, data } = await postScan(url, serie);
      if(httpOk && data && data.ok){
        beep(true);
        const dup  = data.duplicate ? ' (ya estaba marcado)' : '';
        const kind = MODE==='devolucion' ? 'Devolución' : 'Vendido';
        logLine(`${kind} registrada para ${serie}${dup}`, true);

        if(data.metrics){ renderResumen(data.metrics); } else { await refreshResumen(); }
      }else{
        beep(false);
        const msg = data?.msg || 'No se pudo registrar';
        logLine(`${msg} (${serie})`, false);
      }
    }catch(err){
      beep(false);
      logLine(`Error de red (${serie})`, false);
    }
  }

  // === Wiring de inputs ===
  const $in = document.getElementById('scanInput');
  document.getElementById('scanBtn').addEventListener('click', ()=>{ marcar($in.value); $in.value=''; $in.focus(); });
  $in.addEventListener('keydown', (e)=>{
    if(e.key==='Enter' || e.key==='Tab'){
      e.preventDefault();
      marcar($in.value);
      $in.value = '';
    }
  });

  // === Init ===
  document.addEventListener('DOMContentLoaded', ()=>{
    setMode('devolucion'); // por defecto
    refreshResumen();
    $in.focus();
  });
</script>
@endsection
