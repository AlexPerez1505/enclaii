@extends('layouts.app')

@section('title','Tareas')
@section('titulo','Tareas')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet"/>

<style>
  :root{
    --pblue-25:#f7fbff; --pblue-50:#eef6ff; --pblue-75:#f3f8ff; --pblue-100:#e6f0ff; --pblue-700:#1f4bb8;
    --ink:#0f172a; --muted:#6b7280; --line:#e7ebf0; --card:#ffffff;

    /* Paleta pastel botones/badges */
    --soft-blue-bg:#edf4ff;  --soft-blue-fg:#0b4db5;  --soft-blue-br:#cfe0ff;
    --soft-gray-bg:#f4f7fb;  --soft-gray-fg:#475569; --soft-gray-br:#e5eaf5;
    --soft-green-bg:#edf9f2; --soft-green-fg:#0e7a4b; --soft-green-br:#cdeedd;
    --soft-yellow-bg:#fff7e6; --soft-yellow-fg:#8a5a00; --soft-yellow-br:#ffe2b8;
    --soft-red-bg:#ffefef;   --soft-red-fg:#b42318;   --soft-red-br:#ffd3d3;
    --soft-dark-bg:#f1f3f6;  --soft-dark-fg:#0f172a;  --soft-dark-br:#e3e6ee;
  }

  body{
    background:
      radial-gradient(1200px 700px at 0% -10%, var(--pblue-50), transparent 60%),
      radial-gradient(1200px 700px at 100% -10%, var(--pblue-75), transparent 60%),
      linear-gradient(180deg, var(--pblue-25), #f5f8ff 35%, #f0f4ff);
    color:var(--ink);
  }

  .content-wrap,
  .hero{
    --hero-w: min(1100px, calc(100% - 24px));
    width: var(--hero-w);
    margin-left:auto; margin-right:auto;
  }

  .hero{
    margin-top:72px; margin-bottom:10px;
    padding:14px 16px;
    background:
      radial-gradient(120% 120% at 0% 0%, var(--pblue-50) 0%, #fff 55%),
      radial-gradient(120% 120% at 100% 0%, var(--pblue-75) 0%, #fff 55%);
    border:1px solid #dce7ff; border-radius:16px;
    box-shadow:0 10px 26px rgba(31,75,184,.08);
  }
  .fade-slide{animation:fadeSlide .45s ease both}
  @keyframes fadeSlide{ from{opacity:0; transform:translateY(6px)} to{opacity:1; transform:translateY(0)} }

  .desktop-search{
    display:inline-flex; align-items:center; gap:.5rem; padding:.4rem .7rem;
    border:1px solid #dbe7ff; border-radius:999px; background:#fff;
    box-shadow:0 8px 22px rgba(31,75,184,.06);
  }
  .desktop-search input{ border:0; outline:0; width:200px; max-width:38vw; font-weight:500; }

  .btn-pastel{
    display:inline-flex; align-items:center; gap:.5rem;
    background:var(--soft-blue-bg); color:var(--soft-blue-fg);
    border:1px solid var(--soft-blue-br); border-radius:12px;
    padding:.52rem .9rem; font-weight:700; text-decoration:none;
    box-shadow:0 6px 16px rgba(31,75,184,.08);
    transition:background .15s ease, color .15s ease, transform .15s ease, box-shadow .15s ease, border-color .15s ease;
  }
  .btn-pastel:hover{
    background:#ffffff; color:#0f172a; border-color:#dbe7ff;
    box-shadow:0 10px 22px rgba(18,38,63,.18); transform:translateY(-1px);
  }
  .btn-pastel:focus-visible{
    outline:0; box-shadow:0 0 0 3px rgba(31,75,184,.18), 0 10px 22px rgba(18,38,63,.18);
  }

  .fab-search{
    position:fixed; right:16px; bottom:16px; z-index:1050;
    width:52px; height:52px; border-radius:50%;
    display:none; align-items:center; justify-content:center;
    background:#fff; color:#0f172a; border:1px solid #dbe7ff;
    box-shadow:0 14px 32px rgba(18,38,63,.22);
    transition:.2s ease;
  }
  .fab-hide{ opacity:0; transform:scale(.9); pointer-events:none }

  .sheet-mask{
    position:fixed; inset:0; background:rgba(15,23,42,.18);
    opacity:0; pointer-events:none; transition:.2s ease; z-index:1039;
    backdrop-filter: blur(8px) saturate(120%);
    -webkit-backdrop-filter: blur(8px) saturate(120%);
  }
  .sheet-mask.open{ opacity:1; pointer-events:auto }
  body.sheet-open .blur-on-sheet{ filter:blur(4px); transform:translateZ(0); }

  .sheet{
    position:fixed; left:0; right:0; bottom: var(--kb, 0px); z-index:1040;
    background:#fff; border-radius:16px 16px 0 0; border-top:1px solid #dce7ff;
    box-shadow:0 -8px 40px rgba(18,38,63,.18);
    transform:translateY(8px); opacity:0; pointer-events:none; transition:.22s ease;
    touch-action:pan-y; max-height: calc(min(100dvh, 100vh) - 12px);
    padding-bottom: calc(env(safe-area-inset-bottom, 0px) + 8px);
  }
  .sheet.open{ transform:translateY(0); opacity:1; pointer-events:auto }
  .sheet.dragging{ transition:none }
  .sheet .handle{ height:22px; display:grid; place-items:center }
  .sheet .handle span{ width:46px; height:4px; background:#e2e8f0; border-radius:999px; }
  .sheet .body{ padding:12px 14px 16px; overflow:auto; }
  .search-input{
    width:100%; border:1px solid #dbe7ff; border-radius:12px; padding:.72rem .9rem; outline:0;
    transition:.15s ease;
  }
  .search-input:focus{ border-color:#cfe0ff; box-shadow:0 0 0 4px rgba(31,75,184,.08) }
  .actions{ display:flex; gap:.7rem; margin-top:.8rem; align-items:stretch; }
  .btn-soft{
    width:100%; height:42px; border-radius:12px; padding:.66rem 1rem; font-weight:700;
    border:1px solid transparent; box-shadow:0 6px 16px rgba(18,38,63,.06);
    display:flex; align-items:center; justify-content:center; gap:.5rem;
    transition:.12s ease;
  }
  .btn-soft:active{ transform:translateY(1px) }
  .btn-soft-blue{ background:var(--soft-blue-bg); color:var(--soft-blue-fg); border-color:var(--soft-blue-br); }
  .btn-soft-gray{ background:var(--soft-gray-bg); color:var(--soft-gray-fg); border-color:var(--soft-gray-br); }

  .block{
    background: linear-gradient(180deg, #ffffff 0%, #ffffff 60%, #fbfdff 100%);
    border:1px solid var(--line);
    border-radius:16px;
    box-shadow: 0 14px 28px rgba(31,75,184,.08), inset 0 1px 0 rgba(255,255,255,.6);
  }
  .table-wrap{ overflow:hidden; border-radius:16px; }
  .smart-table{ overflow:auto; -webkit-overflow-scrolling:touch; }

  .table{ margin:0; font-size:.935rem; }
  .table> :not(caption)>*>*{ background:transparent; vertical-align:middle; padding:10px 14px; }
  thead th{
    position:sticky; top:0; z-index:1;
    background:linear-gradient(180deg, #f7faff 0%, #f1f6ff 100%);
    border-bottom:1px solid var(--line); color:#0f172a; font-weight:800;
    letter-spacing:.01em; white-space:nowrap;
  }
  tbody tr{ box-shadow: inset 0 -1px 0 #eff2f7; transition: background .15s ease; }
  tbody tr:hover{ background:#f6f9ff; }

  .title-link{ color:var(--ink); text-decoration:none; font-weight:700; }
  .title-link:hover{ text-decoration:underline; text-underline-offset:2px; }

  .badge-soft{
    font-weight:700; border:1px solid; border-radius:999px;
    padding:.12rem .36rem; font-size:.68rem; line-height:1.1;
  }
  .bg-soft-blue   { background:var(--soft-blue-bg);   color:var(--soft-blue-fg);   border-color:var(--soft-blue-br); }
  .bg-soft-yellow { background:var(--soft-yellow-bg); color:var(--soft-yellow-fg); border-color:var(--soft-yellow-br); }
  .bg-soft-green  { background:var(--soft-green-bg);  color:var(--soft-green-fg);  border-color:var(--soft-green-br); }
  .bg-soft-red    { background:var(--soft-red-bg);    color:var(--soft-red-fg);    border-color:var(--soft-red-br); }
  .bg-soft-dark   { background:var(--soft-dark-bg);   color:var(--soft-dark-fg);   border-color:var(--soft-dark-br); }

  .watch-list{ display:flex; flex-wrap:wrap; gap:6px; margin-top:4px; }
  .watch-chip{
    display:inline-flex; align-items:center; gap:6px;
    background:var(--soft-blue-bg); color:var(--soft-blue-fg);
    border:1px solid var(--soft-blue-br);
    padding:.12rem .38rem; border-radius:999px; font-size:.68rem; font-weight:800;
    max-width:160px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;
  }
  .watch-chip .dot{
    width:6px; height:6px; border-radius:50%; background:var(--soft-blue-fg);
  }
  .watch-more{ background:var(--soft-gray-bg); color:var(--soft-gray-fg); border-color:var(--soft-gray-br); }

  .icon-btn{
    display:inline-flex; align-items:center; justify-content:center;
    width:32px; height:32px; border-radius:10px;
    background:#f1f5f9; color:#111827; border:1px solid #e7edf5;
    margin-right:.25rem; text-decoration:none; transition:.15s ease;
  }
  .icon-btn:hover{ transform:translateY(-1px); }
  .icon-btn.is-view{ background:#e9f2ff; color:#0b4db5; border-color:#cfe0ff; }
  .icon-btn.is-view:hover{ background:#deebff; }
  .icon-btn.is-edit{ background:#fff5e6; color:#8a5a00; border-color:#ffe2b8; }
  .icon-btn.is-edit:hover{ background:#ffefda; }

  /* Acciones nuevas */
  .icon-btn.is-progress{ background:#edf9f2; color:#0e7a4b; border-color:#cdeedd; }
  .icon-btn.is-progress:hover{ background:#e6f7ee; }
  .icon-btn.is-work{ background:#fff7e6; color:#8a5a00; border-color:#ffe2b8; }
  .icon-btn.is-work:hover{ background:#ffefda; }

  @media (max-width: 767.98px){
    .hero{ padding:12px 12px; margin-top:68px; }
    .hero .hero-icon{ display:none !important; }
    .hero-actions{ width:100%; gap:.6rem; flex-wrap:wrap; justify-content:center; }
    .desktop-search{ display:none !important; }
    .fab-search{ display:flex }

    .table thead{ display:none; }
    .table tbody tr{
      display:block; margin:10px; padding:10px 12px;
      border:1px solid var(--line); border-radius:14px; background:#fff;
      box-shadow:0 10px 20px rgba(31,75,184,.05);
    }
    .table tbody tr td{
      display:flex; justify-content:space-between; gap:12px; padding:9px 6px;
      border-bottom:1px dashed #eef2f7;
    }
    .table tbody tr td:last-child{ border-bottom:0; }
    .table tbody tr td::before{
      content: attr(data-th);
      font-weight:700; color:#334155;
    }
    .text-end{ justify-content:flex-end; }
    .block{ margin-left:6px; margin-right:6px; }
    .watch-list{ justify-content:flex-end; max-width:60%; }
  }

  @media (min-width: 768px){
    .smart-table table{ min-width: 980px; }
  }
</style>

{{-- ===== HERO ===== --}}
<div class="hero blur-on-sheet fade-slide d-flex align-items-center justify-content-between flex-wrap" style="margin-top:30px;">
  <div class="hero-main d-flex align-items-center gap-3">
    <div class="hero-icon rounded-circle d-inline-flex align-items-center justify-content-center bg-white border"
         style="width:44px;height:44px;border-color:#dce7ff">
      <i class="bi bi-ticket-perforated" style="font-size:1.1rem;color:var(--pblue-700)"></i>
    </div>
    <div>
      <h1 class="h5 mb-0">Tareas</h1>
      <div class="small" style="color:var(--muted)">Consulta, exporta y gestiona tus tickets en tiempo real.</div>
    </div>
  </div>

  <div class="d-flex align-items-center gap-2 hero-actions">
    <div class="desktop-search">
      <i class="fa-solid fa-magnifying-glass" style="opacity:.7"></i>
      <input id="desktopSearch" type="text" placeholder="Buscar…">
    </div>

    <a href="{{ route('tickets.create') }}" class="btn-pastel">
      <i class="bi bi-plus-lg"></i> Nuevo
    </a>
  </div>
</div>

<!-- FAB Buscar (móvil) -->
<button type="button" class="fab-search js-open-search" aria-label="Buscar">
  <i class="fa-solid fa-magnifying-glass"></i>
</button>

{{-- ===== Sheet (móvil) + máscara ===== --}}
<div class="sheet" role="dialog" aria-modal="true" aria-label="Búsqueda de tickets">
  <div class="handle"><span></span></div>
  <div class="body">
    <input id="customSearch" type="search" inputmode="search" enterkeyhint="search"
           class="search-input" placeholder="Escribe para filtrar…">
    <div class="actions">
      <button type="button" class="btn-soft btn-soft-gray" id="btnClearSearch">
        <i class="fa-solid fa-eraser"></i> <span>Limpiar</span>
      </button>
      <button type="button" class="btn-soft btn-soft-blue js-apply-search">
        <i class="fa-solid fa-check"></i> <span>Aplicar</span>
      </button>
    </div>
  </div>
</div>
<div class="sheet-mask"></div>

{{-- ===== TABLA ===== --}}
<div class="content-wrap blur-on-sheet">
  @if(session('success'))
    <div class="alert alert-success rounded-3 mt-3">{{ session('success') }}</div>
  @endif

  <div class="block mt-2">
    <div class="table-wrap smart-table">
      <table class="table table-hover align-middle mb-0" id="ticketsTable">
        <thead>
          <tr>
            <th class="px-3 py-3">ID</th>
            <th class="py-3">Título</th>
            <th class="py-3">Estado</th>
            <th class="py-3">Prioridad</th>
            <th class="py-3">Visibilidad</th>
            <th class="py-3">Asignado / Acceso</th>
            <th class="py-3">Creado</th>
            <th class="py-3 text-end pe-3">Acciones</th>
          </tr>
        </thead>
        <tbody>
        @forelse($tickets as $t)
          @php
            $meId = (int) auth()->id();
            $isCreator = ((int) $t->creator_id === $meId);
            $isAssignee = ((int) ($t->assignee_id ?? 0) === $meId);
            $isWatcher = $t->relationLoaded('watchers')
              ? $t->watchers->contains('id', $meId)
              : false;

            // Regla de trabajo: asignado o watcher (según lo que definiste en TicketController@work)
            $canWork = $isAssignee || $isWatcher;
          @endphp

          <tr>
            <td class="px-3" data-th="ID">{{ $t->id }}</td>

            <td data-th="Título">
              <a href="{{ route('tickets.show',$t) }}" class="title-link">
                {{ \Illuminate\Support\Str::limit($t->title,72) }}
              </a>
              @if($t->comments_count ?? false)
                <span class="ms-2 small text-muted"><i class="bi bi-chat-dots"></i> {{ $t->comments_count }}</span>
              @endif
            </td>

            <td data-th="Estado">
              @switch($t->status)
                @case('open')        <span class="badge-soft bg-soft-blue">Abierto</span> @break
                @case('in_progress') <span class="badge-soft bg-soft-yellow">En progreso</span> @break
                @case('resolved')    <span class="badge-soft bg-soft-green">Resuelto</span> @break
                @case('closed')      <span class="badge-soft bg-soft-dark">Cerrado</span> @break
                @default             <span class="badge-soft bg-soft-dark">{{ $t->status }}</span>
              @endswitch
            </td>

            <td data-th="Prioridad">
              @switch($t->priority)
                @case('low')    <span class="badge-soft bg-soft-green">Baja</span> @break
                @case('medium') <span class="badge-soft bg-soft-yellow">Media</span> @break
                @case('high')   <span class="badge-soft bg-soft-red">Alta</span> @break
                @case('urgent') <span class="badge-soft bg-soft-red">Urgente</span> @break
                @default        <span class="badge-soft bg-soft-dark">{{ $t->priority }}</span>
              @endswitch
            </td>

            <td data-th="Visibilidad">
              @switch($t->visibility)
                @case('private')  <span class="badge-soft bg-soft-dark">Privado</span> @break
                @case('shared')   <span class="badge-soft bg-soft-blue">Seleccionados</span> @break
                @case('public')   <span class="badge-soft bg-soft-blue">Público</span> @break
                @default          <span class="badge-soft bg-soft-dark">{{ ucfirst($t->visibility) }}</span>
              @endswitch
            </td>

            <td class="text-muted" data-th="Asignado / Acceso">
              @php
                $assigneeName = $t->assignee?->name;
                $watchNames   = $t->watchers->pluck('name');
                $totalWatch   = $watchNames->count();
                $firstWatch   = $watchNames->take(3);
                $restWatch    = $totalWatch > 3 ? $watchNames->slice(3) : collect();
              @endphp

              @if($assigneeName)
                <span>{{ $assigneeName }}</span>
              @elseif($totalWatch === 0)
                <span>—</span>
              @endif

              @if($totalWatch)
                <div class="watch-list" title="{{ $watchNames->implode(', ') }}">
                  @foreach($firstWatch as $n)
                    <span class="watch-chip"><span class="dot"></span>{{ $n }}</span>
                  @endforeach
                  @if($restWatch->count())
                    <span class="watch-chip watch-more" title="{{ $restWatch->implode(', ') }}">+{{ $restWatch->count() }}</span>
                  @endif
                </div>
              @endif
            </td>

            <td class="text-muted small" data-th="Creado">{{ optional($t->created_at)->diffForHumans() }}</td>

            <td class="text-end pe-3" data-th="Acciones">
              <a href="{{ route('tickets.show',$t) }}" class="icon-btn is-view" title="Ver"><i class="bi bi-eye"></i></a>

              @if($isCreator)
                <a href="{{ route('tickets.progress',$t) }}" class="icon-btn is-progress" title="Ver avance (creador)">
                  <i class="bi bi-graph-up"></i>
                </a>
              @endif

              @if($canWork)
                <a href="{{ route('tickets.work',$t) }}" class="icon-btn is-work" title="Trabajar checklist">
                  <i class="bi bi-play-circle"></i>
                </a>
              @endif

              @if($isCreator)
                <a href="{{ route('tickets.edit',$t) }}" class="icon-btn is-edit" title="Editar"><i class="bi bi-pencil"></i></a>
              @endif
            </td>
          </tr>
        @empty
          <tr class="empty-server">
            <td colspan="8" class="text-center p-4 text-muted">Sin registros.</td>
          </tr>
        @endforelse
        </tbody>
      </table>
    </div>

    <div class="py-2 d-flex justify-content-center" id="paginationWrap">
      {{ $tickets->withQueryString()->links() }}
    </div>
  </div>
</div>

<!-- FAB Buscar (móvil; al final) -->
<button type="button" class="fab-search js-open-search" aria-label="Buscar">
  <i class="fa-solid fa-magnifying-glass"></i>
</button>

<script>
(function(){
  const body = document.body;
  const sheet = document.querySelector('.sheet');
  const mask  = document.querySelector('.sheet-mask');
  const openBtns  = document.querySelectorAll('.js-open-search');
  const closeBtns = document.querySelectorAll('.js-close-sheet, .sheet-mask');
  const inputDesk = document.getElementById('desktopSearch');
  const inputMob  = document.getElementById('customSearch');
  const btnClear  = document.getElementById('btnClearSearch');
  const table = document.getElementById('ticketsTable');
  const tbody = table?.querySelector('tbody');
  const pagination = document.getElementById('paginationWrap');

  function openSheet(){ sheet.classList.add('open'); mask.classList.add('open'); body.classList.add('sheet-open'); setTimeout(()=> inputMob?.focus(), 120); }
  function closeSheet(){ sheet.classList.remove('open'); mask.classList.remove('open'); body.classList.remove('sheet-open'); }
  openBtns.forEach(b=> b.addEventListener('click', openSheet));
  closeBtns.forEach(b=> b.addEventListener('click', closeSheet));
  document.addEventListener('keydown', (e)=>{ if(e.key === 'Escape') closeSheet(); });

  const rows = Array.from(tbody?.querySelectorAll('tr') || []).filter(tr => !tr.classList.contains('empty-server'));
  rows.forEach(tr => { tr.dataset.search = tr.innerText.replace(/\s+/g,' ').trim().toLowerCase(); });

  let noResRow = null;
  function ensureNoResultsRow(){
    if(!noResRow){
      noResRow = document.createElement('tr');
      noResRow.className = 'no-results';
      noResRow.innerHTML = '<td colspan="8" class="text-center p-4 text-muted">Sin coincidencias.</td>';
      tbody.appendChild(noResRow);
    }
  }

  function applyFilter(q){
    const term = (q || '').trim().toLowerCase();
    if(inputDesk) inputDesk.value = q;
    if(inputMob)  inputMob.value = q;

    let visible = 0;
    if(term === ''){
      rows.forEach(tr => { tr.style.display = ''; });
      if(noResRow) noResRow.style.display = 'none';
      if(pagination) pagination.style.display = '';
      return;
    }

    rows.forEach(tr => {
      const hit = tr.dataset.search.includes(term);
      tr.style.display = hit ? '' : 'none';
      if(hit) visible++;
    });

    ensureNoResultsRow();
    noResRow.style.display = (visible === 0) ? '' : 'none';
    if(pagination) pagination.style.display = 'none';
  }

  let tHandle=null;
  function onType(e){ const val = e.target.value; clearTimeout(tHandle); tHandle = setTimeout(()=> applyFilter(val), 120); }
  inputDesk?.addEventListener('input', onType);
  inputMob?.addEventListener('input', onType);
  inputMob?.addEventListener('keydown', (e)=>{ if(e.key === 'Enter'){ applyFilter(e.target.value); closeSheet(); }});
  document.querySelector('.js-apply-search')?.addEventListener('click', ()=>{ applyFilter(inputMob?.value || ''); closeSheet(); });
  btnClear?.addEventListener('click', ()=>{ applyFilter(''); inputMob?.focus(); });

  if (window.visualViewport) {
    const updateKB = () => {
      const gap = Math.max(0, (window.innerHeight - visualViewport.height));
      document.documentElement.style.setProperty('--kb', gap ? gap + 'px' : '0px');
    };
    visualViewport.addEventListener('resize', updateKB);
    visualViewport.addEventListener('scroll', updateKB);
  }
})();
</script>
@endsection