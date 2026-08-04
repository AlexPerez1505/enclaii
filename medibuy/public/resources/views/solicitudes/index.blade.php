@extends('layouts.app')
@section('title', 'Requisiciones')
@section('titulo', 'Mis Solicitudes')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
<style>
  :root{
    --bg:#f7f9fc;
    --card:#ffffff;
    --ink:#0f172a;
    --muted:#6b7280;
    --line:#e8eef5;
    --brand:#a6d6f2;    /* azul pastel */
    --brand-ink:#1a4f66;/* más intenso */
    --pill1:#eef7ff;
    --pill2:#e1f0ff;
    --radius:16px;
    --shadow:0 8px 24px rgba(17,24,39,.07);
  }
  *{box-sizing:border-box}
  body{font-family:"Inter",system-ui,-apple-system,Segoe UI,Roboto,Helvetica,Arial;background:var(--bg);color:var(--ink)}

  .wrap{max-width:1000px;margin:24px auto 64px;padding:0 14px}
  .card{background:var(--card);border:1px solid var(--line);border-radius:var(--radius);box-shadow:var(--shadow);overflow:hidden}

  /* Head */
  .head{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:16px;border-bottom:1px solid var(--line)}
  .title{display:flex;gap:4px;flex-direction:column}
  .title h2{margin:0;font-size:20px;font-weight:700;color:var(--brand-ink)}
  .title small{color:var(--muted)}
  .kpis{display:flex;gap:8px;flex-wrap:wrap}
  .kpi{display:flex;align-items:center;gap:8px;padding:6px 10px;border:1px solid var(--line);border-radius:999px;background:#f9fbff;font-size:12px}
  .kpi .dot{width:8px;height:8px;border-radius:50%;background:#cbd5e1}
  .kpi--total .dot{background:#93c5fd}
  .kpi--pend .dot{background:#fbbf24}
  .kpi--planta .dot{background:#34d399}
  .kpi--ent .dot{background:#60a5fa}
  .kpi--rech .dot{background:#fca5a5}

  /* Toolbars */
  .bar{display:flex;align-items:center;justify-content:space-between;gap:10px;flex-wrap:wrap;padding:12px 16px;border-bottom:1px solid var(--line)}
  .left,.right{display:flex;align-items:center;gap:10px;flex-wrap:wrap}
  .input{display:flex;align-items:center;gap:8px;padding:10px 12px;border:1px solid var(--line);border-radius:12px;background:#fbfdff}
  .input input{border:0;outline:none;background:transparent;min-width:220px}
  .chipset{display:flex;gap:8px;flex-wrap:wrap}
  .chip{padding:8px 12px;border:1px solid var(--line);border-radius:999px;background:#fff;font-size:12px;color:#334155;cursor:pointer;transition:transform .12s ease, box-shadow .12s ease}
  .chip:hover{transform:translateY(-1px);box-shadow:0 6px 14px rgba(0,0,0,.05)}
  .chip.is-active{background:var(--brand);border-color:transparent;color:#07364a;font-weight:600}
  .sel{appearance:none;padding:10px 12px;border:1px solid var(--line);border-radius:12px;background:#fff;cursor:pointer}
  .btn{display:inline-flex;align-items:center;gap:8px;padding:10px 12px;border:1px solid var(--line);border-radius:12px;background:#fff;cursor:pointer;transition:transform .12s ease, box-shadow .12s ease}
  .btn:hover{transform:translateY(-1px);box-shadow:0 8px 18px rgba(0,0,0,.06)}
  .btn.primary{background:var(--brand);border-color:transparent;color:#0b3d4e;font-weight:600}

  /* Pill azul tipo captura */
  .pill{background:linear-gradient(180deg,var(--pill1),var(--pill2));border:1px solid #d6e9f9;color:#0b3d4e;border-radius:999px;padding:10px 14px;box-shadow:inset 0 1px 0 #fff, 0 6px 16px rgba(24, 78, 119, .07)}
  .pill .plus{display:inline-flex;align-items:center;justify-content:center;width:18px;height:18px;border-radius:50%;border:1px solid #8ecae6;margin-right:8px;color:#1e5670;font-weight:700}

  .body{padding:8px 16px 16px}
  .tiny{font-size:12px;color:var(--muted)}
  .meta{display:flex;gap:12px;align-items:center;margin:4px 0 12px}
  .meta .sep{width:1px;height:14px;background:var(--line)}
  .hidden{display:none!important}

  /* Skeleton */
  .skel{display:grid;gap:10px}
  .skel .row{height:60px;border-radius:12px;background:linear-gradient(90deg,#f3f4f6 25%,#e5e7eb 37%,#f3f4f6 63%);background-size:400% 100%;animation:shimmer 1.1s infinite}
  @keyframes shimmer{0%{background-position:100% 0}100%{background-position:-100% 0}}

  /* Responsive: desktop full, móvil minimal */
  .bar-desktop{display:flex}
  .bar-mobile{display:none}
  @media (max-width:640px){
    .bar-desktop{display:none}
    .bar-mobile{display:flex}
    .input input{min-width:0}
  }

  /* Bottom sheet */
  .sheet-backdrop{position:fixed;inset:0;background:rgba(0,10,20,.25);backdrop-filter:saturate(120%) blur(2px);display:none;z-index:60}
  .sheet{position:fixed;left:0;right:0;bottom:0;background:#fff;border-top-left-radius:18px;border-top-right-radius:18px;box-shadow:0 -12px 30px rgba(0,0,0,.08);transform:translateY(100%);transition:transform .2s ease;z-index:70;border:1px solid var(--line)}
  .sheet .hd{padding:14px 16px;border-bottom:1px solid var(--line);display:flex;justify-content:space-between;align-items:center}
  .sheet .bd{padding:14px 16px;display:grid;gap:12px}
  .group{display:grid;gap:6px}
  .lab{font-size:12px;color:var(--muted)}
  .ctl{display:flex;gap:10px;align-items:center;flex-wrap:wrap}
  .ctl select,.ctl input[type="checkbox"]{padding:10px 12px;border:1px solid var(--line);border-radius:10px}
  .sheet .ft{padding:12px 16px;border-top:1px solid var(--line);display:flex;gap:10px;justify-content:flex-end}
  .open .sheet{transform:translateY(0)}
  .open .sheet-backdrop{display:block}

  /* toasts */
  .toasts{position:fixed;right:16px;bottom:16px;z-index:90;display:flex;flex-direction:column;gap:10px}
  .toast{min-width:240px;max-width:320px;padding:10px 12px;border-radius:12px;border:1px solid var(--line);background:#fff;box-shadow:var(--shadow)}
  .toast .t{font-weight:600;margin:0 0 2px}
  .toast .m{font-size:12px;color:#475569}
</style>

<div class="wrap" style="margin-top:110px;">
  <div class="card">

    {{-- HEAD --}}
    <div class="head">
      <div class="title">
        <h2>Mis Solicitudes</h2>
        <small>Consulta el estado y detalle en tiempo real.</small>
      </div>
      <div class="kpis">
        <span class="kpi kpi--total"><span class="dot"></span> Total <b id="kTotal">—</b></span>
        <span class="kpi kpi--pend"><span class="dot"></span> Pendiente <b id="kPend">—</b></span>
        <span class="kpi kpi--planta"><span class="dot"></span> En planta <b id="kPlant">—</b></span>
        <span class="kpi kpi--ent"><span class="dot"></span> Entregado <b id="kEnt">—</b></span>
        <span class="kpi kpi--rech"><span class="dot"></span> Rechazada <b id="kRech">—</b></span>
      </div>
    </div>

    {{-- TOOLBAR DESKTOP --}}
    <div class="bar bar-desktop">
      <div class="left">
        <div class="input" title="Buscar por material, categoría o estado">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M21 21l-4.3-4.3M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15Z" stroke="#2d3748" stroke-width="2" stroke-linecap="round"/></svg>
          <input id="q" type="text" placeholder="Buscar…">
        </div>
        <div class="chipset" id="chips">
          <button class="chip is-active" data-v="todos">Todos</button>
          <button class="chip" data-v="Pendiente">Pendiente</button>
          <button class="chip" data-v="En Planta">En Planta</button>
          <button class="chip" data-v="Entregado">Entregado</button>
          <button class="chip" data-v="Rechazada">Rechazada</button>
        </div>
      </div>
      <div class="right">
        <select id="ord" class="sel" title="Orden">
          <option value="recientes">Más recientes</option>
          <option value="antiguos">Más antiguos</option>
        </select>
        <button id="btnRefresh" class="btn" title="Actualizar">Actualizar</button>
        <label class="btn" style="gap:10px;cursor:pointer">
          <span id="liveDot" style="width:8px;height:8px;border-radius:50%;background:#22c55e;display:inline-block"></span>
          <span>Auto</span>
          <input id="auto" type="checkbox" checked class="hidden">
        </label>
        <select id="ms" class="sel" title="Intervalo">
          <option value="10000">10s</option>
          <option value="15000" selected>15s</option>
          <option value="20000">20s</option>
          <option value="30000">30s</option>
        </select>
      </div>
    </div>

    {{-- TOOLBAR MÓVIL (minimal) --}}
    <div class="bar bar-mobile">
      <div class="left" style="flex:1">
        <div class="input" style="width:100%">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M21 21l-4.3-4.3M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15Z" stroke="#2d3748" stroke-width="2" stroke-linecap="round"/></svg>
          <input id="qM" type="text" placeholder="Buscar…">
        </div>
      </div>
      <div class="right">
        <button id="btnSheet" class="pill" title="Opciones">
          <span class="plus">+</span> Opciones
        </button>
      </div>
    </div>

    {{-- BODY --}}
    <div class="body">
      <div class="tiny meta">
        <span>Última actualización: <b id="ts">—</b></span>
        <span class="sep"></span>
        <span id="loading">Cargando…</span>
      </div>

      <div id="contenedor-solicitudes" data-list-root>
        {{-- Skeleton inicial --}}
        <div id="skel" class="skel">
          <div class="row"></div><div class="row"></div><div class="row"></div>
        </div>

        {{-- Render inicial (server) como fallback --}}
        @foreach($solicitudes as $solicitud)
          <div class="card my-2" data-estado="{{ $solicitud->estado }}" data-fecha="{{ \Carbon\Carbon::parse($solicitud->created_at)->format('Y-m-d H:i:s') }}">
            <div class="card-body">
              <strong>{{ $solicitud->material }}</strong> ({{ $solicitud->categoria }}) - {{ $solicitud->cantidad }} unidades
              <br>
              Estado:
              <span class="badge 
                @if($solicitud->estado == 'Pendiente') bg-warning 
                @elseif($solicitud->estado == 'Rechazada') bg-danger 
                @elseif($solicitud->estado == 'Entregado') bg-success 
                @else bg-secondary 
                @endif">
                {{ $solicitud->estado }}
              </span>

              @if($solicitud->estado === 'Rechazada')
                <div class="mt-2 text-danger">
                  <strong>Motivo de rechazo:</strong> {{ $solicitud->motivo_rechazo ?? 'No especificado' }}
                </div>
              @endif

              @if($solicitud->estado === 'Entregado')
                <div class="mt-2">
                  <strong>Entregado por:</strong> {{ $solicitud->entregadoPor->name ?? 'N/A' }} <br>
                  <strong>Fecha de entrega:</strong> {{ \Carbon\Carbon::parse($solicitud->fecha_entrega)->format('d/m/Y H:i') }}
                </div>
              @endif

              <br>
              <small>Solicitado el {{ \Carbon\Carbon::parse($solicitud->created_at)->format('d/m/Y H:i') }}</small>
            </div>
          </div>
        @endforeach
      </div>
    </div>

  </div>
</div>

{{-- Bottom sheet (móvil) --}}
<div id="backdrop" class="sheet-backdrop"></div>
<div id="sheet" class="sheet" role="dialog" aria-modal="true" aria-labelledby="sheetTitle">
  <div class="hd">
    <b id="sheetTitle">Opciones</b>
    <button id="closeSheet" class="btn">Cerrar</button>
  </div>
  <div class="bd">
    <div class="group">
      <span class="lab">Estado</span>
      <div class="ctl">
        <select id="mFiltro">
          <option value="todos">Todos</option>
          <option value="Pendiente">Pendiente</option>
          <option value="En Planta">En Planta</option>
          <option value="Entregado">Entregado</option>
          <option value="Rechazada">Rechazada</option>
        </select>
      </div>
    </div>
    <div class="group">
      <span class="lab">Orden</span>
      <div class="ctl">
        <select id="mOrd">
          <option value="recientes">Más recientes</option>
          <option value="antiguos">Más antiguos</option>
        </select>
      </div>
    </div>
    <div class="group">
      <span class="lab">Actualización</span>
      <div class="ctl">
        <label style="display:flex;align-items:center;gap:8px;">
          <input id="mAuto" type="checkbox" checked> Auto
        </label>
        <select id="mMs">
          <option value="10000">10s</option>
          <option value="15000" selected>15s</option>
          <option value="20000">20s</option>
          <option value="30000">30s</option>
        </select>
        <button id="mRefresh" class="btn">Actualizar</button>
      </div>
    </div>
  </div>
  <div class="ft">
    <button id="mApply" class="btn primary">Aplicar</button>
  </div>
</div>

<div class="toasts" id="toasts"></div>
@endsection

@section('scripts')
<script>
(function(){
  const route = @json(route('solicitudes.ajax'));
  const $root = document.getElementById('contenedor-solicitudes');
  const $skel = document.getElementById('skel');
  const $ts   = document.getElementById('ts');
  const $loading = document.getElementById('loading');
  const $toasts  = document.getElementById('toasts');

  // Desktop controls
  const $q = document.getElementById('q');
  const $chips = Array.from(document.querySelectorAll('#chips .chip'));
  const $ord = document.getElementById('ord');
  const $btnRefresh = document.getElementById('btnRefresh');
  const $auto = document.getElementById('auto');
  const $ms = document.getElementById('ms');
  const $liveDot = document.getElementById('liveDot');

  // Mobile controls
  const $qM = document.getElementById('qM');
  const $sheet = document.getElementById('sheet');
  const $backdrop = document.getElementById('backdrop');
  const $btnSheet = document.getElementById('btnSheet');
  const $mFiltro = document.getElementById('mFiltro');
  const $mOrd = document.getElementById('mOrd');
  const $mAuto = document.getElementById('mAuto');
  const $mMs = document.getElementById('mMs');
  const $mApply = document.getElementById('mApply');
  const $mRefresh = document.getElementById('mRefresh');
  const $closeSheet = document.getElementById('closeSheet');

  let filtro = 'todos';
  let timer = null;
  let intervalMs = parseInt($ms?.value || '15000', 10);

  // Utils
  const nowStr = () => new Date().toLocaleString('es-MX',{hour12:false});
  const toast = (t,m) => {
    const el = document.createElement('div');
    el.className = 'toast';
    el.innerHTML = `<p class="t">${t}</p><p class="m">${m}</p>`;
    $toasts.appendChild(el);
    setTimeout(()=>{ el.style.opacity='0'; setTimeout(()=> el.remove(), 220); }, 2500);
  };
  const setLoading = on => { $loading.textContent = on ? 'Cargando…' : ''; };
  const setLive = on => { if($liveDot) $liveDot.style.background = on ? '#22c55e' : '#94a3b8'; };

  const items = () => Array.from($root.querySelectorAll('[data-estado], .card.my-2')).filter(el => !el.closest('thead'));
  const estado = (el) => {
    const e = el.getAttribute('data-estado'); if (e) return e.trim();
    const t=(el.textContent||'').toLowerCase();
    if (t.includes('en planta')) return 'En Planta';
    if (t.includes('pendiente')) return 'Pendiente';
    if (t.includes('entregado')) return 'Entregado';
    if (t.includes('rechazad')) return 'Rechazada';
    return 'Desconocido';
  };
  const tsOf = (el) => {
    const raw = el.getAttribute('data-fecha'); if (raw) return Date.parse(raw)||0;
    const txt = el.textContent||'';
    const iso = txt.match(/\d{4}-\d{2}-\d{2}[ T]\d{2}:\d{2}(:\d{2})?/); if (iso) return Date.parse(iso[0])||0;
    const m = txt.match(/\b(\d{1,2})\/(\d{1,2})\/(\d{4})\s+(\d{1,2}):(\d{2})/);
    if (m){ const [_,d,mo,y,h,mm] = m.map(Number); return Date.parse(`${y}-${String(mo).padStart(2,'0')}-${String(d).padStart(2,'0')}T${String(h).padStart(2,'0')}:${String(mm).padStart(2,'0')}:00`)||0; }
    return 0;
  };

  function recount(){
    const it = items();
    let total=0, pend=0, plant=0, ent=0, rech=0;
    it.forEach(el=>{
      total++;
      const e=estado(el);
      if(e==='Pendiente') pend++;
      else if(e==='En Planta') plant++;
      else if(e==='Entregado') ent++;
      else if(e==='Rechazada') rech++;
    });
    document.getElementById('kTotal').textContent = total||0;
    document.getElementById('kPend').textContent  = pend||0;
    document.getElementById('kPlant').textContent = plant||0;
    document.getElementById('kEnt').textContent   = ent||0;
    document.getElementById('kRech').textContent  = rech||0;
  }

  const queryText = () => {
    const a = ($q?.value||'').trim(), b = ($qM?.value||'').trim();
    return (a.length>=b.length ? a : b).toLowerCase();
  };

  function apply(){
    const q = queryText();
    const ord = $ord?.value || 'recientes';
    items().forEach(el=>{
      let ok = (filtro==='todos') ? true : (estado(el)===filtro);
      if (ok && q) ok = el.textContent.toLowerCase().includes(q);
      el.classList.toggle('hidden', !ok);
    });

    // Ordenar si hay fechas detectables
    const arr = items().map(el=>({el, ts:tsOf(el)}));
    if (arr.some(x=>x.ts>0)){
      arr.sort((a,b)=> ord==='recientes' ? b.ts-a.ts : a.ts-b.ts);
      arr.forEach(x=> $root.appendChild(x.el));
    }
    recount();
  }

  function load(showToast=false){
    setLoading(true); $skel?.classList.remove('hidden');
    fetch(route, {cache:'no-store'})
      .then(r=>r.text())
      .then(html=>{
        $root.innerHTML = html;
        $ts.textContent = nowStr();
        apply();
        if (showToast) toast('Actualizado','Últimas solicitudes cargadas');
      })
      .catch(()=> toast('Error','No se pudieron cargar las solicitudes'))
      .finally(()=>{ setLoading(false); $skel?.classList.add('hidden'); });
  }

  function start(){ stop(); timer = setInterval(()=> load(false), intervalMs); setLive(true); }
  function stop(){ if (timer){ clearInterval(timer); timer=null; } setLive(false); }

  // Init
  document.addEventListener('DOMContentLoaded', ()=>{
    apply(); recount(); if($ts) $ts.textContent = nowStr(); setLoading(false); $skel?.classList.add('hidden');
    start();

    // Desktop handlers
    $chips.forEach(btn=>btn.addEventListener('click', ev=>{
      $chips.forEach(b=>b.classList.remove('is-active'));
      ev.currentTarget.classList.add('is-active');
      filtro = ev.currentTarget.getAttribute('data-v');
      if ($mFiltro) $mFiltro.value = filtro;
      apply();
    }));
    $q?.addEventListener('input', apply);
    $ord?.addEventListener('change', ()=>{ if($mOrd) $mOrd.value=$ord.value; apply(); });
    $btnRefresh?.addEventListener('click', ()=> load(true));
    $auto?.addEventListener('change', function(){ this.checked ? start() : stop(); if($mAuto) $mAuto.checked = this.checked; });
    $ms?.addEventListener('change', function(){ intervalMs=parseInt(this.value,10)||15000; if($mMs) $mMs.value=this.value; if($auto?.checked) start(); toast('Intervalo',`Cada ${Math.round(intervalMs/1000)}s`); });

    // Mobile handlers
    $qM?.addEventListener('input', apply);
    const openSheet = ()=> { document.body.classList.add('open'); };
    const closeSheet = ()=> { document.body.classList.remove('open'); };
    $btnSheet?.addEventListener('click', ()=>{
      if($mFiltro) $mFiltro.value = filtro;
      if($mOrd && $ord) $mOrd.value = $ord.value;
      if($mAuto && $auto) $mAuto.checked = $auto.checked;
      if($mMs && $ms) $mMs.value = $ms.value;
      openSheet();
    });
    $closeSheet?.addEventListener('click', closeSheet);
    $backdrop?.addEventListener('click', closeSheet);

    $mApply?.addEventListener('click', ()=>{
      filtro = $mFiltro?.value || 'todos';
      $chips.forEach(b => b.classList.toggle('is-active', b.getAttribute('data-v')===filtro));
      if($ord && $mOrd){ $ord.value = $mOrd.value; }
      if($auto && $mAuto){ $auto.checked = $mAuto.checked; $auto.dispatchEvent(new Event('change')); }
      if($ms && $mMs){ $ms.value = $mMs.value; $ms.dispatchEvent(new Event('change')); }
      apply(); closeSheet();
    });
    $mRefresh?.addEventListener('click', ()=> load(true));
  });
})();
</script>
@endsection
