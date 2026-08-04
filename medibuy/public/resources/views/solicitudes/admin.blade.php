@extends('layouts.app')
@section('title', 'Requisiciones')
@section('titulo', 'Solicitudes')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
<style>
  :root{
    --bg:#f7f8fb;
    --card:#ffffff;
    --ink:#0f172a;
    --muted:#6b7280;
    --line:#e9eef5;
    --brand:#a6d6f2;        /* azul pastel */
    --brand-ink:#194b63;    /* texto más intenso */
    --pill1:#eef7ff;
    --pill2:#e2f1ff;
    --radius:16px;
    --shadow:0 8px 24px rgba(17,24,39,.07);
  }
  *{box-sizing:border-box}
  body{font-family:"Inter",system-ui,-apple-system,Segoe UI,Roboto,Helvetica,Arial;background:var(--bg);color:var(--ink)}

  .wrap{max-width:1100px;margin:24px auto 64px;padding:0 14px}
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

  /* Toolbars */
  .bar{display:flex;align-items:center;justify-content:space-between;gap:10px;flex-wrap:wrap;padding:12px 16px;border-bottom:1px solid var(--line)}
  .left,.right{display:flex;align-items:center;gap:10px;flex-wrap:wrap}
  .input{display:flex;align-items:center;gap:8px;padding:10px 12px;border:1px solid var(--line);border-radius:12px;background:#fbfdff}
  .input input{border:0;outline:none;background:transparent;min-width:200px}

  .chipset{display:flex;gap:8px;flex-wrap:wrap}
  .chip{padding:8px 12px;border:1px solid var(--line);border-radius:999px;background:#ffffff;font-size:12px;color:#334155;cursor:pointer;transition:transform .12s ease, box-shadow .12s ease}
  .chip:hover{transform:translateY(-1px);box-shadow:0 6px 14px rgba(0,0,0,.05)}
  .chip.is-active{background:var(--brand);border-color:transparent;color:#07364a;font-weight:600}

  .sel{appearance:none;padding:10px 12px;border:1px solid var(--line);border-radius:12px;background:#fff;cursor:pointer}
  .btn{display:inline-flex;align-items:center;gap:8px;padding:10px 12px;border:1px solid var(--line);border-radius:12px;background:#fff;cursor:pointer;transition:transform .12s ease, box-shadow .12s ease}
  .btn:hover{transform:translateY(-1px);box-shadow:0 8px 18px rgba(0,0,0,.06)}
  .btn.primary{background:var(--brand);border-color:transparent;color:#0b3d4e;font-weight:600}

  /* Botón estilo “pill” azul (como tu captura) */
  .pill{background:linear-gradient(180deg,var(--pill1),var(--pill2));border:1px solid #d6e9f9;color:#0b3d4e;border-radius:999px;padding:10px 14px;box-shadow:inset 0 1px 0 #fff, 0 6px 16px rgba(24, 78, 119, .07)}
  .pill .plus{display:inline-flex;align-items:center;justify-content:center;width:18px;height:18px;border-radius:50%;border:1px solid #8ecae6;margin-right:8px;color:#1e5670;font-weight:700}

  /* Meta/loader */
  .body{padding:8px 16px 16px}
  .tiny{font-size:12px;color:var(--muted)}
  .meta{display:flex;gap:12px;align-items:center;margin:4px 0 12px}
  .meta .sep{width:1px;height:14px;background:var(--line)}

  .skel{display:grid;gap:10px}
  .skel .row{height:58px;border-radius:12px;background:linear-gradient(90deg,#f3f4f6 25%,#e5e7eb 37%,#f3f4f6 63%);background-size:400% 100%;animation:shimmer 1.1s infinite}
  @keyframes shimmer{0%{background-position:100% 0}100%{background-position:-100% 0}}

  .hidden{display:none!important}

  /* —— Responsive:
       Desktop = toolbar completa
       Mobile  = búsqueda + 1 botón “Opciones”; resto vive en bottom-sheet —— */
  .bar.bar-desktop{display:flex}
  .bar.bar-mobile{display:none}
  @media (max-width: 640px){
    .bar.bar-desktop{display:none}
    .bar.bar-mobile{display:flex}
    .input input{min-width:0}
  }

  /* Bottom sheet minimal */
  .sheet-backdrop{position:fixed;inset:0;background:rgba(0,10,20,.25);backdrop-filter:saturate(120%) blur(2px);display:none;z-index:60}
  .sheet{position:fixed;left:0;right:0;bottom:0;background:#fff;border-top-left-radius:18px;border-top-right-radius:18px;box-shadow:0 -12px 30px rgba(0,0,0,.08);transform:translateY(100%);transition:transform .2s ease;z-index:70;border:1px solid var(--line)}
  .sheet .hd{padding:14px 16px;border-bottom:1px solid var(--line);display:flex;justify-content:space-between;align-items:center}
  .sheet .hd b{color:var(--brand-ink)}
  .sheet .bd{padding:14px 16px;display:grid;gap:12px}
  .group{display:grid;gap:6px}
  .lab{font-size:12px;color:var(--muted)}
  .ctl{display:flex;gap:10px;align-items:center}
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
        <h2>Solicitudes</h2>
        <small>Seguimiento en tiempo real: Pendiente / En Planta</small>
      </div>
      <div class="kpis">
        <span class="kpi kpi--total"><span class="dot"></span> Total <b id="kTotal">—</b></span>
        <span class="kpi kpi--pend"><span class="dot"></span> Pendiente <b id="kPend">—</b></span>
        <span class="kpi kpi--planta"><span class="dot"></span> En planta <b id="kPlant">—</b></span>
      </div>
    </div>

    {{-- TOOLBAR DESKTOP (igual que antes) --}}
    <div class="bar bar-desktop">
      <div class="left">
        <div class="input" title="Buscar material, categoría o usuario">
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
        <select id="ord" class="sel" title="Ordenar">
          <option value="recientes">Más recientes</option>
          <option value="antiguos">Más antiguos</option>
        </select>
        <button id="btnRefresh" class="btn" title="Actualizar">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M21 12a9 9 0 1 1-2.64-6.36M21 3v6h-6" stroke="#1f2937" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
          Actualizar
        </button>
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

    {{-- TOOLBAR MOBILE (minimal) --}}
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
        <span id="loading" class="">Cargando…</span>
      </div>

      <div id="contenedor-solicitudes" data-list-root>
        {{-- Loader inicial --}}
        <div id="skel" class="skel">
          <div class="row"></div><div class="row"></div><div class="row"></div>
        </div>
        @isset($solicitudes)
          @include('partials.listado', ['solicitudes' => $solicitudes])
        @endisset
      </div>
    </div>

  </div>
</div>

{{-- Bottom sheet opciones (solo se muestra en móvil) --}}
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
      </div>
    </div>
  </div>
  <div class="ft">
    <button id="mRefresh" class="btn">Actualizar</button>
    <button id="mApply" class="btn primary">Aplicar</button>
  </div>
</div>

<div class="toasts" id="toasts"></div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
(function(){
  const route = @json(route('solicitudes.listado'));
  const $root = $('#contenedor-solicitudes');
  const $skel = $('#skel');
  const $ts   = $('#ts');
  const $loading = $('#loading');
  const $toasts  = $('#toasts');

  // Desktop controls
  const $q = $('#q'); const $chips = $('#chips .chip'); const $ord = $('#ord');
  const $btnRefresh = $('#btnRefresh'); const $auto = $('#auto'); const $ms = $('#ms'); const $liveDot = $('#liveDot');

  // Mobile controls
  const $qM = $('#qM');
  const $sheet = $('#sheet'); const $backdrop = $('#backdrop'); const $btnSheet = $('#btnSheet');
  const $mFiltro = $('#mFiltro'); const $mOrd = $('#mOrd'); const $mAuto = $('#mAuto'); const $mMs = $('#mMs');
  const $mApply = $('#mApply'); const $mRefresh = $('#mRefresh'); const $closeSheet = $('#closeSheet');

  let filtro = 'todos';
  let timer = null;
  let intervalMs = parseInt($ms.val(),10) || 15000;

  // ---------- Utils ----------
  const nowStr = () => new Date().toLocaleString('es-MX',{hour12:false});
  const toast = (t,m) => { const $t=$(`<div class="toast"><p class="t">${t}</p><p class="m">${m}</p></div>`); $toasts.append($t); setTimeout(()=> $t.fadeOut(200,()=> $t.remove()), 2500); };
  const setLoading = on => $loading.text(on?'Cargando…':'');
  const setLive = on => $liveDot.css('background', on ? '#22c55e' : '#94a3b8');

  function items(){
    let $it = $root.find('[data-estado],[data-solicitud-id],[data-id],.solicitud,.solic-card,tr');
    $it = $it.filter(function(){ return !$(this).closest('thead').length; });
    return $it;
  }
  function estado($el){
    const e = $el.data('estado'); if (e) return (e+'').trim();
    const t = ($el.text()||'').toLowerCase();
    if (t.includes('en planta')) return 'En Planta';
    if (t.includes('pendiente')) return 'Pendiente';
    if (t.includes('entregado')) return 'Entregado';
    if (t.includes('rechazad')) return 'Rechazada';
    return 'Desconocido';
  }
  function tsOf($el){
    const raw = $el.data('fecha'); if (raw) return Date.parse(raw)||0;
    const txt = $el.text();
    const iso = txt.match(/\d{4}-\d{2}-\d{2}[ T]\d{2}:\d{2}(:\d{2})?/);
    if (iso) return Date.parse(iso[0])||0;
    const m = txt.match(/\b(\d{1,2})\/(\d{1,2})\/(\d{4})\s+(\d{1,2}):(\d{2})/);
    if (m){ const [_,d,mo,y,h,mm] = m.map(Number); return Date.parse(`${y}-${String(mo).padStart(2,'0')}-${String(d).padStart(2,'0')}T${String(h).padStart(2,'0')}:${String(mm).padStart(2,'0')}:00`)||0; }
    return 0;
  }
  function recount(){
    const $it = items(); let total=0, pend=0, plant=0;
    $it.each(function(){ total++; const e=estado($(this)); if (e==='Pendiente') pend++; if (e==='En Planta') plant++; });
    $('#kTotal').text(total||0); $('#kPend').text(pend||0); $('#kPlant').text(plant||0);
  }

  function queryText(){
    // Unifica búsqueda desktop/móvil
    const a = ($q.val()||'').trim(); const b = ($qM.val()||'').trim();
    return (a.length>=b.length ? a : b).toLowerCase();
  }

  function apply(){
    const q = queryText();
    const ord = $ord.val();
    const $it = items();

    $it.each(function(){
      const $el = $(this);
      let ok = (filtro==='todos') ? true : (estado($el)===filtro);
      if (ok && q) ok = $el.text().toLowerCase().includes(q);
      $el.toggleClass('hidden', !ok);
    });

    const arr = items().toArray().map(el=>({el, ts:tsOf($(el))}));
    if (arr.some(x=>x.ts>0)){
      arr.sort((a,b)=> ord==='recientes' ? b.ts-a.ts : a.ts-b.ts);
      arr.forEach(x=> $root.get(0).appendChild(x.el));
    }
    recount();
  }

  function load(showToast=false){
    setLoading(true); $skel.removeClass('hidden');
    $.ajax({ url:route, type:'GET', cache:false })
      .done(html=>{
        $root.html(html);
        $ts.text(nowStr());
        apply();
        if (showToast) toast('Actualizado','Últimas solicitudes cargadas');
      })
      .fail(()=> toast('Error','No se pudieron cargar las solicitudes'))
      .always(()=>{ setLoading(false); $skel.addClass('hidden'); });
  }

  function start(){ stop(); timer=setInterval(()=> load(false), intervalMs); setLive(true); }
  function stop(){ if (timer){ clearInterval(timer); timer=null; } setLive(false); }

  // ---------- Init ----------
  $(function(){
    apply(); recount(); $ts.text(nowStr()); setLoading(false); $skel.addClass('hidden');
    start();

    // Desktop
    $chips.on('click', function(){ $chips.removeClass('is-active'); $(this).addClass('is-active'); filtro=$(this).data('v'); $('#mFiltro').val(filtro); apply(); });
    $q.on('input', apply);
    $ord.on('change', function(){ $('#mOrd').val($(this).val()); apply(); });
    $btnRefresh.on('click', ()=> load(true));
    $auto.on('change', function(){ if (this.checked) start(); else stop(); $('#mAuto').prop('checked', this.checked); });
    $ms.on('change', function(){ intervalMs=parseInt($(this).val(),10)||15000; $('#mMs').val($(this).val()); if ($auto.is(':checked')) start(); toast('Intervalo',`Cada ${Math.round(intervalMs/1000)}s`); });

    // Mobile
    $qM.on('input', apply);
    const openSheet = ()=> { $('body').addClass('open'); $sheet.focus(); };
    const closeSheet = ()=> { $('body').removeClass('open'); };
    $btnSheet.on('click', ()=> {
      // sincroniza valores actuales
      $mFiltro.val(filtro);
      $mOrd.val($ord.val());
      $mAuto.prop('checked', $auto.is(':checked'));
      $mMs.val($ms.val());
      openSheet();
    });
    $closeSheet.on('click', closeSheet); $backdrop.on('click', closeSheet);
    $mApply.on('click', ()=>{
      filtro = $mFiltro.val();
      $('#chips .chip').removeClass('is-active'); $('#chips .chip[data-v="'+filtro+'"]').addClass('is-active');
      $ord.val($mOrd.val()).trigger('change');
      $auto.prop('checked',$mAuto.is(':checked')).trigger('change');
      $ms.val($mMs.val()).trigger('change');
      apply(); closeSheet();
    });
    $mRefresh.on('click', ()=> load(true));
  });
})();
</script>
@endsection
