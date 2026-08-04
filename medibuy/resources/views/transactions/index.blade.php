@extends('layouts.app')

@section('title', 'Gastos')
@section('titulo', 'Gastos')

@section('content')

{{-- Si tu layout no trae Bootstrap Icons, descomenta la línea de abajo --}}
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<style>
  :root{
    --bg:#f6f8fb; --panel:#ffffff; --text:#0f172a; --muted:#667085; --border:#e7eaf0;
    --pblue:#dbeafe; --pblue-strong:#60a5fa; --pblue-700:#1d4ed8;
    --pgreen:#dcfce7; --pgreen-strong:#34d399;
    --pred:#ffe4e6; --pred-strong:#ef4444;
    --pteal:#e0f2fe; --pteal-strong:#0ea5e9;
    --shadow:0 10px 30px rgba(2,6,23,.06);
  }
  body{ background:var(--bg); color:var(--text); }
  .page-wrap{ max-width:1200px; }

  /* Hero */
  .hero {
    background: radial-gradient(1200px 150px at 0% 0%, rgba(96,165,250,.18), transparent 40%),
                radial-gradient(1200px 150px at 100% 0%, rgba(14,165,233,.14), transparent 40%),
                #fff;
    border:1px solid var(--border);
    border-radius:18px; padding:16px 18px; box-shadow: var(--shadow);
  }
  .hero h1{ font-weight:800; letter-spacing:-.02em; }
  .subtle{ color:var(--muted) }

  /* Buttons pastel */
  .btn-pastel{
    --bg: var(--pblue); --bg-strong: var(--pblue-strong); --txt:#0b2a4a;
    color:var(--txt); background:var(--bg);
    border:1px solid rgba(96,165,250,.45); border-radius:14px; font-weight:800;
    box-shadow:0 10px 22px rgba(96,165,250,.25);
    transition:transform .12s ease, box-shadow .2s ease, filter .2s ease;
  }
  .btn-pastel:hover{ transform: translateY(-1px); filter:brightness(1.02); box-shadow:0 12px 26px rgba(96,165,250,.32); }
  .btn-pastel:active{ transform: translateY(0); }
  .btn-pastel-green{ --bg: var(--pgreen); --bg-strong:var(--pgreen-strong); --txt:#064e3b; border-color: rgba(52,211,153,.45); box-shadow:0 10px 22px rgba(52,211,153,.22); }
  .btn-outline-soft{ border-radius:12px; font-weight:700; border:1px solid var(--border); color:#334155; background:#fff; }
  .btn-outline-soft:hover{ background:#f8fafc; }

  /* Cards */
  .card{ background:var(--panel); border:1px solid var(--border); border-radius:16px; box-shadow:var(--shadow); }
  .card .card-header{ background:#fff; border-bottom:1px solid var(--border); color:var(--muted); font-weight:700; }

  /* Filtros: versión mobile primero */
  #filters .form-label{ color:var(--muted); font-size:.86rem;}
  #filters .form-control, #filters .form-select{ border:1px solid var(--border); border-radius:12px; }
  .filters-wrap{ gap:.75rem; }
  @media (max-width: 768px){
    .filters-wrap{ display:grid; grid-template-columns: 1fr 1fr; }
    .filters-actions{ grid-column: 1 / -1; display:flex; gap:.5rem; }
  }

  /* Métricas grid */
  .metrics-grid{ display:grid; gap:12px; grid-template-columns: repeat(4, 1fr); }
  @media (max-width: 992px){ .metrics-grid{ grid-template-columns: repeat(2, 1fr); } }
  @media (max-width: 576px){ .metrics-grid{ grid-template-columns: 1fr; } }

  .metric-card{ padding:14px }
  .metric-title{ color:var(--muted); font-size:.78rem; text-transform:uppercase; letter-spacing:.04em;}
  .metric-value{ font-weight:900; font-size:1.6rem; line-height:1.1; }
  .metric-pill{ display:inline-flex; align-items:center; gap:.4rem; padding:.25rem .55rem; border-radius:999px; font-size:.75rem; font-weight:700; }
  .pill-in{ background:var(--pgreen); color:#065f46; border:1px solid rgba(52,211,153,.5) }
  .pill-out{ background:var(--pred); color:#7f1d1d; border:1px solid rgba(239,68,68,.45) }
  .pill-ret{ background:var(--pteal); color:#0c4a6e; border:1px solid rgba(14,165,233,.45) }
  .expected-ok{ background: #f1fff7; }
  .expected-bad{ background:#fff1f2; }

  /* Skeletons */
  .skeleton{ position:relative; overflow:hidden; background:#eef2f8; border-radius:8px; min-height:22px }
  .skeleton::after{
    content:""; position:absolute; inset:0; transform:translateX(-100%);
    background: linear-gradient(90deg, transparent, rgba(255,255,255,.6), transparent);
    animation: shimmer 1.1s infinite;
  }
  @keyframes shimmer{ 100% { transform: translateX(100%); } }

  /* Tabla responsive */
  .table-responsive{ border-radius: 0 0 16px 16px; }
  .badge-type{ padding:.35rem .6rem; border-radius:999px; font-weight:800; font-size:.72rem; display:inline-block;}
  .t-allocation{ color:#166534; background:#eaf7ef; border:1px solid rgba(22,101,52,.18) }
  .t-disbursement{ color:#b91c1c; background:#fdecec; border:1px solid rgba(185,28,28,.18) }
  .t-return{ color:#0e7490; background:#eaf7fb; border:1px solid rgba(14,116,144,.18) }

  /* Chart */
  #chart{ width:100%; height:330px; }

  /* Animaciones sutiles */
  .fade-slide{ opacity:0; transform: translateY(8px); animation: fs .45s ease forwards; }
  @keyframes fs{ to{ opacity:1; transform: none } }

  /* FAB mobile */
  .fab{
    position: fixed; right: 16px; bottom: 18px; z-index: 30;
    border-radius: 999px; padding:.9rem 1.05rem;
    background: var(--pblue-strong); color:#fff; box-shadow: 0 12px 28px rgba(29,78,216,.28);
    display:none;
  }
  .fab:hover{ filter:brightness(1.05); transform: translateY(-1px); }
  @media (max-width: 768px){ .fab{ display:inline-flex; align-items:center; gap:.5rem; } }

  /* Aviso */
  .mini-error{ background:#fff3cd; border:1px solid #ffe69c; color:#664d03; padding:.6rem .8rem; border-radius:10px; font-size:.9rem; }
</style>

<div class="container page-wrap" style="margin-top:110px;">

  {{-- HERO --}}
  <div class="hero fade-slide mt-2 mb-3 d-flex align-items-center justify-content-between flex-wrap">
    <div class="d-flex align-items-center gap-3">
      <div class="rounded-circle d-inline-flex align-items-center justify-content-center bg-white border"
           style="width:44px;height:44px;border-color:#dce7ff">
        <i class="bi bi-graph-up-arrow" style="font-size:1.2rem;color:var(--pblue-700)"></i>
      </div>
      <div>
        <h1 class="h4 mb-0">Flujo de efectivo</h1>
        <div class="small subtle">Consulta KPIs, gráfica y movimientos en tiempo real.</div>
      </div>
    </div>
    <div class="mt-2 mt-md-0 d-none d-md-flex gap-2">
      <a href="{{ route('transactions.create', [], false) }}" class="btn btn-pastel">
        <i class="bi bi-plus-lg me-1"></i> Nueva operación
      </a>
    </div>
  </div>

  {{-- FILTROS (mobile first) --}}
  <form id="filters" class="card fade-slide mb-3">
    <div class="card-body">
      <div class="filters-wrap">
        <div>
          <label class="form-label">Desde</label>
          <input type="date" name="from" class="form-control" value="{{ $filters['from'] }}">
        </div>
        <div>
          <label class="form-label">Hasta</label>
          <input type="date" name="to" class="form-control" value="{{ $filters['to'] }}">
        </div>
        <div>
          <label class="form-label">Tipo</label>
          <select name="type" class="form-select">
            <option value="">Todos</option>
            <option value="allocation"   @selected(($filters['type']??'')==='allocation')>Entradas</option>
            <option value="disbursement" @selected(($filters['type']??'')==='disbursement')>Entregas</option>
            <option value="return"       @selected(($filters['type']??'')==='return')>Devoluciones</option>
          </select>
        </div>
        <div>
          <label class="form-label">Encargado</label>
          <select name="manager_id" class="form-select">
            <option value="">Todos</option>
            @foreach($managers as $m)
              <option value="{{ $m->id }}" @selected(($filters['manager_id']??'')==$m->id)>{{ $m->name }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="form-label">Usuario</label>
          <select name="user_id" class="form-select">
            <option value="">Todos</option>
            @foreach($people as $p)
              <option value="{{ $p->id }}" @selected(($filters['user_id']??'')==$p->id)>{{ $p->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="filters-actions">
          <button class="btn btn-pastel-green w-100"><i class="bi bi-funnel-fill me-1"></i> Aplicar</button>
          <button type="button" id="btnClear" class="btn btn-outline-soft w-100"><i class="bi bi-arrow-counterclockwise me-1"></i> Limpiar</button>
        </div>
      </div>
    </div>
  </form>

  {{-- AVISO API --}}
  <div id="warn" class="mini-error mb-3 d-none"><i class="bi bi-wifi-off me-1"></i> No se pudo contactar a la API.</div>

  {{-- KPIs --}}
  <div class="metrics-grid fade-slide">
    <div class="card metric-card">
      <div class="metric-title">Total recibido <span class="metric-pill pill-in ms-1"><i class="bi bi-arrow-down-left"></i> IN</span></div>
      <div class="metric-value" id="recibido"><span class="skeleton" style="height:28px; width:140px;"></span></div>
    </div>
    <div class="card metric-card">
      <div class="metric-title">Total entregado <span class="metric-pill pill-out ms-1"><i class="bi bi-arrow-up-right"></i> OUT</span></div>
      <div class="metric-value" id="entregado"><span class="skeleton" style="height:28px; width:140px;"></span></div>
    </div>
    <div class="card metric-card">
      <div class="metric-title">Total devuelto <span class="metric-pill pill-ret ms-1"><i class="bi bi-arrow-repeat"></i> RET</span></div>
      <div class="metric-value" id="devuelto"><span class="skeleton" style="height:28px; width:140px;"></span></div>
    </div>
    <div class="card metric-card expected-ok" id="cardCaja">
      <div class="metric-title">Esperado en caja</div>
      <div class="metric-value" id="esperado"><span class="skeleton" style="height:28px; width:140px;"></span></div>
    </div>
  </div>

  {{-- CHART --}}
  <div class="card mt-3 fade-slide">
    <div class="card-header d-flex align-items-center justify-content-between">
      <span>Flujo por día</span>
      <small class="subtle">Apila entradas / entregas / devoluciones</small>
    </div>
    <div class="card-body"><canvas id="chart"></canvas></div>
  </div>

  {{-- RANKING --}}
  <div class="card mt-3 fade-slide">
    <div class="card-header">Usuarios designados</div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table mb-0 align-middle">
          <thead class="table-light">
            <tr><th>Usuario</th><th class="text-end">Gastado</th><th class="text-center">Días desde última entrega</th></tr>
          </thead>
          <tbody id="rankingBody">
            <tr><td colspan="3" class="p-3"><span class="skeleton" style="height:18px; display:block;"></span></td></tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  {{-- MOVIMIENTOS --}}
  <div class="card mt-3 fade-slide">
    <div class="card-header d-flex align-items-center justify-content-between">
      <span>Movimientos</span>
      <small class="text-muted d-none d-md-inline">Actualiza cada 20s</small>
    </div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
          <thead class="table-light">
            <tr><th>#</th><th>Fecha</th><th>Tipo</th><th class="text-end">Monto</th><th>Encargado</th><th>Contraparte</th><th>PDF</th></tr>
          </thead>
          <tbody id="tbody">
            <tr>
              <td colspan="7" class="p-3">
                <div class="skeleton" style="height:18px; margin-bottom:6px;"></div>
                <div class="skeleton" style="height:18px; width:70%;"></div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <div class="card-footer d-flex justify-content-between align-items-center">
      <button class="btn btn-outline-soft btn-sm" id="prevPage"><i class="bi bi-chevron-left"></i></button>
      <div id="pageInfo" class="small text-muted">Página 1</div>
      <button class="btn btn-outline-soft btn-sm" id="nextPage"><i class="bi bi-chevron-right"></i></button>
    </div>
  </div>
</div>

{{-- FAB mobile --}}
<a href="{{ route('transactions.create', [], false) }}" class="fab">
  <i class="bi bi-plus-lg"></i> Operación
</a>

{{-- Scripts --}}
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(function(){
  const URL_METRICS = "{{ route('transactions.metrics', [], false) }}";
  const URL_CHART   = "{{ route('transactions.chart',   [], false) }}";
  const URL_LIST    = "{{ route('transactions.list',    [], false) }}";
  const warn = $('#warn');
  let page = 1, lastPage = 1;

  // Chart
  const chart = new Chart(document.getElementById('chart'), {
    type:'bar',
    data:{ labels:[], datasets:[
      {label:'Entradas',     data:[], backgroundColor:'rgba(52,211,153,.85)', borderColor:'rgba(16,185,129,.9)', borderWidth:0, borderRadius:7, borderSkipped:false},
      {label:'Entregas',     data:[], backgroundColor:'rgba(239,68,68,.85)',  borderColor:'rgba(239,68,68,1)',  borderWidth:0, borderRadius:7, borderSkipped:false},
      {label:'Devoluciones', data:[], backgroundColor:'rgba(14,165,233,.85)', borderColor:'rgba(14,165,233,1)', borderWidth:0, borderRadius:7, borderSkipped:false},
    ]},
    options:{
      responsive:true, maintainAspectRatio:false,
      interaction:{ mode:'index', intersect:false },
      plugins:{
        legend:{ labels:{ boxWidth:10, boxHeight:10, usePointStyle:true } },
        tooltip:{ backgroundColor:'#0f172a', titleFont:{weight:'700'}, bodyFont:{weight:'600'} }
      },
      scales:{
        x:{ stacked:true, ticks:{ maxRotation:0, autoSkip:true, color:'#475569' }, grid:{ display:false } },
        y:{ stacked:true, ticks:{ color:'#475569' }, grid:{ color:'rgba(148,163,184,.25)' } }
      }
    }
  });

  // Helpers
  function qparams(extra={}) {
    const o = Object.fromEntries(new FormData(document.getElementById('filters')).entries());
    return {...o, ...extra};
  }
  function money(n){ return new Intl.NumberFormat('es-MX',{style:'currency',currency:'MXN'}).format(Number(n||0)); }
  function typeBadge(t){
    return t==='allocation' ? '<span class="badge-type t-allocation">Entrada</span>' :
           t==='disbursement' ? '<span class="badge-type t-disbursement">Entrega</span>' :
           '<span class="badge-type t-return">Devolución</span>';
  }
  function apiFail(where,x){ console.error(where,x?.status); warn.removeClass('d-none').html('<i class="bi bi-wifi-off me-1"></i> No se pudo cargar '+where+'.'); }

  // Cargas
  function loadMetrics(){
    $.getJSON(URL_METRICS, qparams(), function(res){
      warn.addClass('d-none');
      $('#recibido').text(money(res.totales.recibido));
      $('#entregado').text(money(res.totales.entregado));
      $('#devuelto').text(money(res.totales.devuelto));
      $('#esperado').text(money(res.totales.esperado_caja));
      $('#cardCaja')
        .toggleClass('expected-ok', Number(res.totales.esperado_caja)>=0)
        .toggleClass('expected-bad', Number(res.totales.esperado_caja)<0);

      const rb = $('#rankingBody').empty();
      if(!res.ranking_pendientes.length){
        rb.append('<tr><td colspan="3" class="text-center p-3 text-muted">Sin pendientes.</td></tr>');
      }else{
        res.ranking_pendientes.forEach(r=>{
          rb.append(`<tr>
            <td>${$('<div>').text(r.user_name).html()}</td>
            <td class="text-end">${money(r.pendiente)}</td>
            <td class="text-center">${r.dias ?? '-'}</td>
          </tr>`);
        });
      }
    }).fail((x)=>apiFail('métricas',x));
  }

  function loadChart(){
    $.getJSON(URL_CHART, qparams(), function(rows){
      if(!rows.length){
        const t=$('input[name=to]').val(); rows=[{date:t,in:0,out:0,ret:0}]
      }
      chart.data.labels = rows.map(r=>r.date);
      chart.data.datasets[0].data = rows.map(r=>r.in);
      chart.data.datasets[1].data = rows.map(r=>r.out);
      chart.data.datasets[2].data = rows.map(r=>r.ret);
      chart.update();
    }).fail((x)=>apiFail('gráfica',x));
  }

  function loadTable(){
    $.getJSON(URL_LIST, qparams({page}), function(res){
      warn.addClass('d-none');
      const tb = $('#tbody').empty();
      if(!res.data.length){
        tb.append('<tr><td colspan="7" class="text-center p-3 text-muted">Sin movimientos.</td></tr>');
      }else{
        res.data.forEach(t=>tb.append(`<tr>
          <td>${t.id}</td>
          <td>${t.date}</td>
          <td>${typeBadge(t.type)}</td>
          <td class="text-end">${money(t.amount)}</td>
          <td>${t.manager ?? '-'}</td>
          <td>${t.counterparty ?? '-'}</td>
          <td>${t.pdf ? `<a target="_blank" class="btn btn-sm btn-outline-soft" href="${t.pdf}"><i class="bi bi-file-earmark-pdf"></i></a>` : '-'}</td>
        </tr>`));
      }
      lastPage = res.meta.last_page;
      $('#pageInfo').text(`Página ${res.meta.page} de ${res.meta.last_page} — ${res.meta.total} movs`);
      $('#prevPage').prop('disabled', page<=1); $('#nextPage').prop('disabled', page>=lastPage);
    }).fail((x)=>apiFail('tabla',x));
  }

  function refreshAll(){ loadMetrics(); loadChart(); loadTable(); }

  // Eventos
  $('#filters').on('submit', e=>{e.preventDefault(); page=1; refreshAll();});
  $('#btnClear').on('click', ()=>{ document.getElementById('filters').reset(); page=1; refreshAll(); });
  $('#prevPage').on('click', ()=>{ if(page>1){ page--; loadTable(); }});
  $('#nextPage').on('click', ()=>{ if(page<lastPage){ page++; loadTable(); }});
  setInterval(refreshAll, 20000);

  // Primer render
  refreshAll();
});
</script>
@endsection
