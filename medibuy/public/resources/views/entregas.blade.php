@extends('layouts.app')
@section('title', 'Guías')
@section('titulo', 'Guías')

@section('content')
<style>
  :root{
    --bg:#eaebec; --panel:#ffffff; --text:#0f172a; --muted:#667085; --border:#e7eaf0;
    --pblue:#dbeafe; --pblue-700:#1d4ed8; --pgreen:#dcfce7;
    --shadow:0 10px 30px rgba(2,6,23,.06); --radius:22px;
  }
  *,*::before,*::after{ box-sizing:border-box; }
  body{ background:var(--bg); color:var(--text); }
  .page-wrap{ max-width:1160px; margin:0 auto; padding:0 16px; overflow-x:hidden; }

  /* HERO */
  .hero{
    background:
      radial-gradient(1200px 150px at 0% 0%, rgba(96,165,250,.18), transparent 40%),
      radial-gradient(1200px 150px at 100% 0%, rgba(14,165,233,.14), transparent 40%),
      #fff;
    border:1px solid var(--border); border-radius:18px; padding:16px 18px;
    box-shadow:var(--shadow);
    display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap;
    margin:18px 0;
    overflow:hidden;
  }
  .hero .chip{ width:56px; height:56px; border-radius:16px; display:inline-flex; align-items:center; justify-content:center; background:#fff; border:1px solid #dce7ff; }
  .hero h1{ margin:0; font-weight:800; letter-spacing:-.02em; }
  .subtle{ color:var(--muted); }
  .hero-actions{ display:flex; align-items:center; gap:10px; flex-wrap:wrap; width:100%; }
  .search{ position:relative; flex:1 1 420px; min-width:0; width:auto; background:#fff; border:1px solid var(--border); border-radius:14px; padding-left:42px; }
  .search input{ border:none; outline:none; background:transparent; padding:12px 14px; width:100%; color:#111827; }
  .search .ico{ position:absolute; left:12px; top:50%; transform:translateY(-50%); font-size:18px; color:var(--pblue-700); opacity:.9; }
  .btn{ display:inline-flex; align-items:center; gap:.45rem; padding:12px 14px; border-radius:14px; border:1px solid var(--border); background:#fff; color:#334155; font-weight:800; text-decoration:none; cursor:pointer; }
  .btn-blue{ background:var(--pblue); color:#0b2a4a; border-color:rgba(96,165,250,.45); }
  .btn-green{ background:var(--pgreen); color:#064e3b; border-color:rgba(52,211,153,.45); }

  /* TABLA */
  .table-wrap{ background:#fff; border:1px solid var(--border); border-radius:var(--radius); box-shadow:var(--shadow); overflow:hidden; }
  .table-scroll{ overflow:auto; max-width:100%; }
  .entregas-table{ width:100%; border-collapse:separate; border-spacing:0; table-layout:fixed; }
  .entregas-table .th,.entregas-table .td{ padding:16px 18px; text-align:left; font-size:14px; }
  .entregas-table .th{ color:#6b7280; font-weight:700; background:#eef3ff; border-bottom:1px solid var(--border); white-space:nowrap; }
  .entregas-table tr.trow{ display:table-row; border-bottom:1px solid var(--border); background:#fff; }
  .entregas-table tr.trow:hover{ background:#fcfcff; }
  .entregas-table td.td{ display:table-cell; vertical-align:middle; }
  .cell-actions{ display:flex; gap:8px; justify-content:flex-end; }
  .chip-state{ display:inline-flex; align-items:center; padding:6px 10px; border-radius:999px; font-weight:700; font-size:12px; background:var(--pgreen); color:#166534; }
  .no-results{ color:var(--muted); }

  /* Anchos (ajústalos a tu gusto) */
  .entregas-table col:nth-child(1){ width:170px; } /* RASTREO */
  .entregas-table col:nth-child(2){ width:auto; }  /* CONTENIDO */
  .entregas-table col:nth-child(3){ width:120px; } /* ESTADO */
  .entregas-table col:nth-child(4){ width:160px; } /* FECHA */
  .entregas-table col:nth-child(5){ width:140px; } /* SERIE */
  .entregas-table col:nth-child(6){ width:220px; } /* DESTINATARIO */
  .entregas-table col:nth-child(7){ width:160px; } /* USUARIO */
  .entregas-table col:nth-child(8){ width:120px; } /* ACCIONES */

  .footer{ display:flex; justify-content:space-between; align-items:center; gap:10px; padding:16px 18px; background:#fafafa; border-top:1px solid var(--border); }
  .pager{ display:flex; gap:10px; align-items:center; min-width:0; }
  .pager .btn{ border-radius:18px; box-shadow:0 4px 12px rgba(2,6,23,.04); padding:10px 12px; }

  /* Móvil */
  @media (max-width:576px){
    .hero-actions .search{ flex-basis:100%; width:100%; }
    .hero-actions .btn-utility{ display:none; }

    .entregas-table.is-stacked thead{ display:none; }
    .entregas-table.is-stacked,
    .entregas-table.is-stacked tbody,
    .entregas-table.is-stacked tr.trow,
    .entregas-table.is-stacked td.td{ display:block; width:100%; }
    .entregas-table.is-stacked tr.trow{ padding:12px 14px; }
    .entregas-table.is-stacked tr.trow + tr.trow{ border-top:1px solid var(--border); }
    .entregas-table.is-stacked td.td{
      border:none; padding:10px 0;
      display:grid; grid-template-columns:minmax(96px,40%) 1fr; gap:8px; align-items:flex-start;
      word-wrap:break-word;
    }
    .entregas-table.is-stacked td.td::before{ content:attr(data-label); font-weight:700; color:#6b7280; }
    .entregas-table.is-stacked td.td[data-label="ACCIONES"]{ grid-template-columns:1fr; }
    .entregas-table.is-stacked .cell-actions{ justify-content:flex-start; }
  }

  /* FAB + Bottom sheet */
  .fab{ position:fixed; right:16px; bottom:18px; z-index:60; display:none; }
  .fab button{ border:none; border-radius:999px; padding:.9rem 1.05rem; font-weight:800; cursor:pointer; background:#2563eb; color:#fff; box-shadow:0 12px 28px rgba(29,78,216,.28); }
  @media (max-width:860px){ .fab{ display:block; } }
  .sheet-backdrop{ position:fixed; inset:0; background:rgba(15,23,42,.35); backdrop-filter: blur(8px) saturate(1.05); opacity:0; pointer-events:none; transition:opacity .2s ease; z-index:70; }
  .sheet-backdrop.show{ opacity:1; pointer-events:auto; }
  .sheet{ position:fixed; left:0; right:0; bottom:-100%; z-index:80; background:#fff; border-radius:18px 18px 0 0; box-shadow:0 -20px 40px rgba(2,6,23,.16); padding:16px; transition:bottom .28s ease; will-change:bottom; }
  .sheet .grab{ width:60px; height:6px; background:#e5e7eb; border-radius:999px; margin:6px auto 12px; }
  .sheet .grid{ display:grid; gap:12px; }
  .sheet .link{ display:flex; align-items:center; gap:10px; padding:14px; border:1px solid #efe9ff; border-radius:14px; background:#fafaff; color:#4c1d95; font-weight:700; text-decoration:none; }
  body.sheet-open .page-wrap{ filter: blur(6px); transform: scale(.995); }
  body.sheet-open{ overflow:hidden; }
</style>

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<div class="page-wrap">
  <div class="hero">
    <div class="d-flex align-items-center gap-3">
      <div class="chip"><i class="bi bi-clipboard-check" style="font-size:1.25rem;color:var(--pblue-700)"></i></div>
      <div>
        <h1 class="h4 mb-0">Entregas de guías</h1>
        <div class="small subtle">Consulta y gestiona tus entregas en tiempo real.</div>
      </div>
    </div>

    <div class="hero-actions">
      <div class="search">
        <i class="ico bi bi-search"></i>
        <input id="q" type="search" placeholder="Buscar por rastreo, contenido, serie, destinatario…">
      </div>
      <button id="refreshBtn" class="btn btn-utility">Actualizar</button>
      <a href="{{ route('guias.create') }}" class="btn btn-green btn-utility"><i class="bi bi-box-seam"></i> Guías</a>
      <a href="{{ url('/entrega') }}" class="btn btn-blue btn-utility"><i class="bi bi-list-ul"></i> Entregas</a>
    </div>
  </div>

  <div class="table-wrap">
    <div class="table-scroll">
      <table class="entregas-table" id="entregasTable">
        <colgroup><col><col><col><col><col><col><col><col></colgroup>
        <thead>
          <tr>
            <th class="th">RASTREO</th>
            <th class="th">CONTENIDO</th>
            <th class="th">ESTADO</th>
            <th class="th">FECHA</th>
            <th class="th">SERIE</th>
            <th class="th">DESTINATARIO</th>
            <th class="th">USUARIO</th>
            <th class="th" style="text-align:right;">ACCIONES</th>
          </tr>
        </thead>
        <tbody id="tbody"></tbody>
      </table>
    </div>

    <div class="footer">
      <div class="count" id="count">—</div>
      <div class="pager">
        <button class="btn" id="prevBtn">Anterior</button>
        <span id="pageInfo" class="count">Página 1 de 1</span>
        <button class="btn" id="nextBtn">Siguiente</button>
      </div>
    </div>
  </div>
</div>

<div class="fab"><button id="openSheet">Menú</button></div>
<div class="sheet-backdrop" id="sheetBackdrop"></div>
<div class="sheet" id="sheet">
  <div class="grab"></div>
  <div style="text-align:center; font-weight:800; color:#111827; margin-bottom:6px;">Navegación rápida</div>
  <div class="grid">
    <a class="link" href="{{ route('guias.create') }}">📦 Guías</a>
    <a class="link" href="{{ url('/entrega') }}">🧾 Entregas</a>
  </div>
</div>

<script>
  const routes = { list: '{{ route('entregas.list') }}' };
  let page = 1, lastPage = 1, q = '', total = 0, perPage = 12;

  const tableEl = document.getElementById('entregasTable');
  function toggleStack(){
    const isPhone = window.matchMedia('(max-width: 576px)').matches;
    tableEl.classList.toggle('is-stacked', isPhone);
  }
  window.addEventListener('resize', toggleStack);
  toggleStack();

  function renderRow(item){
    return `
      <tr class="trow">
        <td class="td" data-label="RASTREO">${item.rastreo || '—'}</td>
        <td class="td" data-label="CONTENIDO">${item.contenido || '—'}</td>
        <td class="td" data-label="ESTADO"><span class="chip-state">${item.estado || 'Entregado'}</span></td>
        <td class="td" data-label="FECHA">${item.fecha || '—'}</td>
        <td class="td" data-label="SERIE">${item.serie || '—'}</td>
        <td class="td" data-label="DESTINATARIO">${item.destinatario || '—'}</td>
        <td class="td" data-label="USUARIO">${item.usuario || '—'}</td>
        <td class="td" data-label="ACCIONES">
          <div class="cell-actions">
            ${item.imagen_url
              ? `<a class="btn" style="padding:8px 10px" target="_blank" href="${item.imagen_url}" title="Ver evidencia"><i class="bi bi-eye"></i></a>`
              : `<button class="btn" style="padding:8px 10px" disabled title="Sin imagen">—</button>`}
          </div>
        </td>
      </tr>
    `;
  }

  async function load(){
    const url = new URL(routes.list, window.location.origin);
    url.searchParams.set('page', page);
    url.searchParams.set('per_page', perPage);
    if(q.trim()) url.searchParams.set('q', q.trim());

    const res  = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
    const json = await res.json();

    const tbody = document.getElementById('tbody');
    if (!json.data.length){
      tbody.innerHTML = `<tr class="trow"><td class="td" colspan="8"><span class="no-results">Sin resultados</span></td></tr>`;
    } else {
      tbody.innerHTML = json.data.map((it)=> renderRow(it)).join('');
    }

    lastPage = json.meta.last_page; total = json.meta.total;
    document.getElementById('count').textContent = total ? `Total: ${total} entregas` : 'Sin resultados';
    document.getElementById('pageInfo').textContent = `Página ${json.meta.page} de ${lastPage}`;
    document.getElementById('prevBtn').disabled = page <= 1;
    document.getElementById('nextBtn').disabled = page >= lastPage;
  }

  let t=null;
  document.getElementById('q').addEventListener('input', (e)=>{
    clearTimeout(t);
    t = setTimeout(()=>{ q = e.target.value; page = 1; load(); }, 300);
  });
  document.getElementById('refreshBtn')?.addEventListener('click', ()=> load());
  document.getElementById('prevBtn').addEventListener('click', ()=> { if(page>1){ page--; load(); }});
  document.getElementById('nextBtn').addEventListener('click', ()=> { if(page<lastPage){ page++; load(); }});

  const sheet = document.getElementById('sheet');
  const backdrop = document.getElementById('sheetBackdrop');
  const openSheet = document.getElementById('openSheet');
  function showSheet(show){
    sheet.style.bottom = show ? '0' : '-100%';
    backdrop.classList.toggle('show', show);
    document.body.classList.toggle('sheet-open', show);
  }
  openSheet.addEventListener('click', ()=> showSheet(true));
  backdrop.addEventListener('click', ()=> showSheet(false));

  load();
</script>
@endsection
