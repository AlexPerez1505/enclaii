@extends('layouts.app')
@section('title', 'Guías')
@section('titulo', 'Guías')

@section('content')
<style>
  :root{
    --bg:#eaebec; --panel:#ffffff; --text:#0f172a; --muted:#667085; --border:#e7eaf0;
    --pblue:#dbeafe; --pblue-700:#1d4ed8; --pgreen:#dcfce7;
    --shadow:0 10px 30px rgba(2,6,23,.06); --radius:22px;
    --navbar-h: 64px;   /* ← ajusta si tu navbar mide diferente */
  }
  *, *::before, *::after { box-sizing: border-box; }
  body { background: var(--bg); color: var(--text); overflow: hidden; } /* sin scroll de página */

  /* ── CONTENEDOR PRINCIPAL ────────────────────────────────────────
     Ocupa exactamente el espacio bajo el navbar, sin scroll propio.
  ──────────────────────────────────────────────────────────────── */
  .page-shell {
    position: fixed;
    top: var(--navbar-h);
    left: 0; right: 0; bottom: 0;
    display: flex;
    flex-direction: column;
    padding: 14px 20px 14px;
    gap: 10px;
    overflow: hidden;     /* el scroll lo lleva la tabla, no este div */
  }

  /* ── HERO (fijo arriba, no sticky) ──────────────────────────── */
  .hero {
    background:
      radial-gradient(1200px 150px at 0% 0%,  rgba(96,165,250,.18), transparent 40%),
      radial-gradient(1200px 150px at 100% 0%, rgba(14,165,233,.14), transparent 40%),
      #fff;
    border: 1px solid var(--border);
    border-radius: 18px;
    padding: 14px 18px;
    box-shadow: var(--shadow);
    display: flex; align-items: center;
    justify-content: space-between;
    gap: 12px; flex-wrap: wrap;
  }
  .hero .chip {
    width:52px; height:52px; border-radius:16px; flex-shrink:0;
    display:inline-flex; align-items:center; justify-content:center;
    background:#fff; border:1px solid #dce7ff;
  }
  .hero h1   { margin:0; font-weight:800; letter-spacing:-.02em; }
  .subtle    { color:var(--muted); }
  .hero-actions { display:flex; align-items:center; gap:10px; flex-wrap:wrap; width:100%; }
  .search {
    position:relative; flex:1 1 380px; min-width:0;
    background:#fff; border:1px solid var(--border);
    border-radius:14px; padding-left:42px;
  }
  .search input {
    border:none; outline:none; background:transparent;
    padding:11px 14px; width:100%; color:#111827; font-size:14px;
  }
  .search .ico {
    position:absolute; left:12px; top:50%; transform:translateY(-50%);
    font-size:18px; color:var(--pblue-700); opacity:.9;
  }

  /* Botones icono-only */
  .btn-icon {
    width:42px; height:42px; padding:0; border-radius:14px;
    border:1px solid var(--border); background:#fff; color:#334155;
    display:inline-flex; align-items:center; justify-content:center;
    font-size:18px; cursor:pointer; text-decoration:none;
    transition: background .15s;
  }
  .btn-icon:hover                  { background:#f1f5f9; }
  .btn-icon.btn-blue               { background:var(--pblue);  color:#0b2a4a; border-color:rgba(96,165,250,.45); }
  .btn-icon.btn-blue:hover         { background:#bfdbfe; }
  .btn-icon.btn-green              { background:var(--pgreen); color:#064e3b; border-color:rgba(52,211,153,.45); }
  .btn-icon.btn-green:hover        { background:#bbf7d0; }
  .btn-icon.btn-refresh            { color:var(--pblue-700); border-color:rgba(29,78,216,.25); }
  .btn-icon.btn-refresh:hover      { background:#eff6ff; }

  /* Botón paginación (con texto) */
  .btn {
    display:inline-flex; align-items:center; justify-content:center;
    gap:.4rem; padding:9px 16px; border-radius:14px;
    border:1px solid var(--border); background:#fff; color:#334155;
    font-weight:700; text-decoration:none; cursor:pointer; font-size:13px;
    transition: background .15s;
  }
  .btn:hover   { background:#f1f5f9; }
  .btn:disabled{ opacity:.4; cursor:not-allowed; }

  /* ── TARJETA TABLA (crece y tiene scroll interno) ─────────────── */
  .table-card {
    flex: 1 1 0;          /* ocupa todo el espacio restante */
    min-height: 0;        /* necesario para que flex-child pueda shrink */
    background: #fff;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    box-shadow: var(--shadow);
    display: flex;
    flex-direction: column;
    overflow: hidden;
  }

  /* ── SCROLL INTERNO de la tabla ──────────────────────────────── */
  .table-scroll {
    flex: 1 1 0;
    overflow: auto;       /* scroll solo aquí */
    min-height: 0;
  }

  .entregas-table {
    width: 100%;
    border-collapse: collapse;
    table-layout: fixed;
  }

  /* Anchos de columna */
  .entregas-table col:nth-child(1){ width:160px; }
  .entregas-table col:nth-child(2){ width:180px; }
  .entregas-table col:nth-child(3){ width:110px; }
  .entregas-table col:nth-child(4){ width:155px; }
  .entregas-table col:nth-child(5){ width:110px; }
  .entregas-table col:nth-child(6){ width:190px; }
  .entregas-table col:nth-child(7){ width:160px; }
  .entregas-table col:nth-child(8){ width: 90px; }

  /* Encabezado sticky DENTRO del scroll interno */
  .entregas-table thead th.th {
    position: sticky;
    top: 0;               /* pega al tope del .table-scroll */
    z-index: 10;
    background: #eef3ff;
    color: #6b7280; font-weight: 700; font-size: 13px;
    padding: 13px 14px; text-align: left;
    border-bottom: 1px solid var(--border);
    white-space: nowrap;
  }

  .entregas-table tr.trow       { border-bottom:1px solid var(--border); background:#fff; }
  .entregas-table tr.trow:hover { background:#fcfcff; }

  .entregas-table td.td {
    padding: 12px 14px; font-size: 13px; vertical-align: middle;
    overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
    max-width: 0;
  }
  /* Columnas que sí pueden romper línea */
  .entregas-table td[data-label="CONTENIDO"],
  .entregas-table td[data-label="DESTINATARIO"] {
    white-space: normal; word-break: break-word;
  }

  .chip-state {
    display:inline-flex; align-items:center;
    padding:4px 10px; border-radius:999px;
    font-weight:700; font-size:11px;
    background:var(--pgreen); color:#166534;
  }
  .cell-actions { display:flex; gap:6px; justify-content:flex-end; }
  .no-results   { color:var(--muted); }

  /* ── FOOTER / PAGINACIÓN ─────────────────────────────────────── */
  .footer {
    flex-shrink: 0;
    display:flex; justify-content:space-between; align-items:center;
    gap:10px; padding:12px 18px;
    background:#fafafa; border-top:1px solid var(--border);
  }
  .pager { display:flex; gap:10px; align-items:center; }
  .count { font-size:13px; color:var(--muted); font-weight:600; }

  /* ── FAB + BOTTOM SHEET (móvil) ──────────────────────────────── */
  .fab { position:fixed; right:16px; bottom:18px; z-index:60; display:none; }
  .fab button {
    border:none; border-radius:999px; padding:.9rem 1.05rem;
    font-weight:800; cursor:pointer;
    background:#2563eb; color:#fff;
    box-shadow:0 12px 28px rgba(29,78,216,.28);
  }
  @media (max-width:860px){ .fab{ display:block; } }

  .sheet-backdrop {
    position:fixed; inset:0; background:rgba(15,23,42,.35);
    backdrop-filter:blur(8px) saturate(1.05);
    opacity:0; pointer-events:none; transition:opacity .2s ease; z-index:70;
  }
  .sheet-backdrop.show { opacity:1; pointer-events:auto; }
  .sheet {
    position:fixed; left:0; right:0; bottom:-100%; z-index:80;
    background:#fff; border-radius:18px 18px 0 0;
    box-shadow:0 -20px 40px rgba(2,6,23,.16);
    padding:16px; transition:bottom .28s ease;
  }
  .sheet .grab { width:60px; height:6px; background:#e5e7eb; border-radius:999px; margin:6px auto 12px; }
  .sheet .sgrid { display:grid; gap:12px; }
  .sheet .link  {
    display:flex; align-items:center; gap:10px; padding:14px;
    border:1px solid #efe9ff; border-radius:14px;
    background:#fafaff; color:#4c1d95; font-weight:700; text-decoration:none;
  }

  /* ── MÓVIL: tabla apilada ────────────────────────────────────── */
  @media (max-width:576px){
    .hero-actions .search { flex-basis:100%; }
    .entregas-table.is-stacked thead { display:none; }
    .entregas-table.is-stacked,
    .entregas-table.is-stacked tbody,
    .entregas-table.is-stacked tr.trow,
    .entregas-table.is-stacked td.td { display:block; width:100%; }
    .entregas-table.is-stacked tr.trow { padding:12px 14px; }
    .entregas-table.is-stacked tr.trow + tr.trow { border-top:1px solid var(--border); }
    .entregas-table.is-stacked td.td {
      border:none; padding:8px 0; max-width:none;
      white-space:normal; overflow:visible; text-overflow:clip;
      display:grid; grid-template-columns:minmax(96px,40%) 1fr;
      gap:8px; align-items:flex-start; word-wrap:break-word;
    }
    .entregas-table.is-stacked td.td::before {
      content:attr(data-label); font-weight:700; color:#6b7280; font-size:12px;
    }
    .entregas-table.is-stacked td.td[data-label="ACCIONES"] { grid-template-columns:1fr; }
    .entregas-table.is-stacked .cell-actions { justify-content:flex-start; }
  }
</style>

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

{{-- ── SHELL FIJO (sin scroll de página) ── --}}
<div class="page-shell">

  {{-- HERO --}}
  <div class="hero">
    <div class="d-flex align-items-center gap-3">
      <div class="chip">
        <i class="bi bi-clipboard-check" style="font-size:1.25rem;color:var(--pblue-700)"></i>
      </div>
      <div>
        <h1 class="h4 mb-0">Entregas de guías</h1>
        <div class="small subtle">Consulta y gestiona tus entregas en tiempo real.</div>
      </div>
    </div>

    <div class="hero-actions">
      {{-- BOTÓN REGRESAR --}}
      <button onclick="window.history.back();" class="btn-icon" title="Regresar">
        <i class="bi bi-arrow-left"></i>
      </button>

      {{-- BOTÓN HOME --}}
      <a href="{{ url('home/') }}" class="btn-icon" title="Inicio / Home">
        <i class="bi bi-house"></i>
      </a>

      <div class="search">
        <i class="ico bi bi-search"></i>
        <input id="q" type="search" placeholder="Buscar por rastreo, contenido, serie, destinatario…">
      </div>
      <button id="refreshBtn" class="btn-icon btn-refresh" title="Actualizar">
        <i class="bi bi-arrow-clockwise"></i>
      </button>
      <a href="{{ route('guias.create') }}" class="btn-icon btn-green" title="Guías">
        <i class="bi bi-box-seam"></i>
      </a>
      <a href="{{ url('/entrega') }}" class="btn-icon btn-blue" title="Entregas">
        <i class="bi bi-list-ul"></i>
      </a>
    </div>
  </div>

  {{-- TABLA con scroll interno --}}
  <div class="table-card">
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

</div>{{-- /page-shell --}}

{{-- FAB + sheet (móvil) --}}
<div class="fab"><button id="openSheet"><i class="bi bi-grid"></i></button></div>
<div class="sheet-backdrop" id="sheetBackdrop"></div>
<div class="sheet" id="sheet">
  <div class="grab"></div>
  <div style="text-align:center;font-weight:800;color:#111827;margin-bottom:6px;">Navegación rápida</div>
  <div class="sgrid">
    <a class="link" href="{{ route('guias.create') }}">📦 Guías</a>
    <a class="link" href="{{ url('/entrega') }}">🧾 Entregas</a>
  </div>
</div>

<script>
  const routes = { list: '{{ route('entregas.list') }}' };
  let page = 1, lastPage = 1, q = '', total = 0, perPage = 20;

  const tableEl = document.getElementById('entregasTable');
  function toggleStack(){
    tableEl.classList.toggle('is-stacked', window.matchMedia('(max-width:576px)').matches);
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
              ? `<a class="btn-icon" style="width:34px;height:34px;font-size:16px;" target="_blank" href="${item.imagen_url}" title="Ver evidencia"><i class="bi bi-eye"></i></a>`
              : `<button class="btn-icon" style="width:34px;height:34px;font-size:16px;opacity:.35;" disabled title="Sin imagen"><i class="bi bi-eye-slash"></i></button>`}
          </div>
        </td>
      </tr>`;
  }

  async function load(){
    const url = new URL(routes.list, window.location.origin);
    url.searchParams.set('page', page);
    url.searchParams.set('per_page', perPage);
    if(q.trim()) url.searchParams.set('q', q.trim());

    const res  = await fetch(url, { headers:{ 'X-Requested-With':'XMLHttpRequest' } });
    const json = await res.json();
    const tbody = document.getElementById('tbody');

    tbody.innerHTML = json.data.length
      ? json.data.map(renderRow).join('')
      : `<tr class="trow"><td class="td" colspan="8"><span class="no-results">Sin resultados</span></td></tr>`;

    lastPage = json.meta.last_page; total = json.meta.total;
    document.getElementById('count').textContent    = total ? `Total: ${total} entregas` : 'Sin resultados';
    document.getElementById('pageInfo').textContent = `Página ${json.meta.page} de ${lastPage}`;
    document.getElementById('prevBtn').disabled     = page <= 1;
    document.getElementById('nextBtn').disabled     = page >= lastPage;
  }

  let t = null;
  document.getElementById('q').addEventListener('input', e => {
    clearTimeout(t);
    t = setTimeout(() => { q = e.target.value; page = 1; load(); }, 300);
  });
  document.getElementById('refreshBtn').addEventListener('click', () => load());
  document.getElementById('prevBtn').addEventListener('click', () => { if(page > 1)      { page--; load(); }});
  document.getElementById('nextBtn').addEventListener('click', () => { if(page < lastPage){ page++; load(); }});

  const sheet    = document.getElementById('sheet');
  const backdrop = document.getElementById('sheetBackdrop');
  function showSheet(v){ sheet.style.bottom = v ? '0':'-100%'; backdrop.classList.toggle('show',v); }
  document.getElementById('openSheet').addEventListener('click', () => showSheet(true));
  backdrop.addEventListener('click', () => showSheet(false));

  load();
</script>
@endsection