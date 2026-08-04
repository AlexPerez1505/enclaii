@extends('layouts.app')
@section('title', 'Mantenimiento')
@section('titulo', 'Mantenimiento')

@section('content')
{{-- ✅ Tipografía: usa la GLOBAL --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
  :root{
    --pblue-50:#eef6ff; --pblue-75:#f3f8ff; --pblue-700:#1f4bb8;
    --ink:#0f172a; --muted:#6b7280; --line:#e7ebf0;

    --soft-blue-bg:#edf4ff;  --soft-blue-fg:#1f4bb8;  --soft-blue-br:#dbe7ff;
    --soft-gray-bg:#f4f7fb;  --soft-gray-fg:#475569;  --soft-gray-br:#e5eaf5;
  }

  body{ background:#f6f7fb; color:var(--ink); }

  /* HERO (mismo ancho que la tabla) */
  .hero{
    width: var(--hero-w, min(1320px, calc(100% - 24px)));
    margin:90px auto 10px;
    padding:18px 20px;
    background:
      radial-gradient(120% 120% at 0% 0%, var(--pblue-50) 0%, #fff 55%),
      radial-gradient(120% 120% at 100% 0%, var(--pblue-75) 0%, #fff 55%);
    border:1px solid #dce7ff; border-radius:18px;
    box-shadow:0 8px 24px rgba(31,75,184,.08);
  }
  .fade-slide{animation:fadeSlide .45s ease both}
  @keyframes fadeSlide{ from{opacity:0; transform:translateY(6px)} to{opacity:1; transform:translateY(0)} }

  .h-title{ font-weight:700; color:var(--ink); margin:0; }
  .h-sub{ color:var(--muted); font-weight:500; }

  /* Search desktop */
  .desktop-search{
    display:inline-flex; align-items:center; gap:.5rem; padding:.45rem .75rem;
    border:1px solid #dbe7ff; border-radius:999px; background:#fff;
    box-shadow:0 8px 22px rgba(31,75,184,.06);
  }
  .desktop-search input{
    border:0; outline:0; width:240px; max-width:40vw;
    font-weight:500; background:transparent;
  }

  /* Button pastel */
  .btn-pastel{
    display:inline-flex; align-items:center; gap:.55rem;
    background:var(--soft-blue-bg); color:var(--soft-blue-fg);
    border:1px solid var(--soft-blue-br); border-radius:14px;
    padding:.6rem 1rem; font-weight:650; text-decoration:none;
    box-shadow:0 6px 16px rgba(31,75,184,.08);
    transition:.15s ease;
    white-space:nowrap;
  }
  .btn-pastel:hover{ filter:brightness(1.02); transform:translateY(-1px) }

  /* FAB buscar (móvil) */
  .fab-search{
    position:fixed; right:16px; bottom:16px; z-index:1050;
    width:56px; height:56px; border-radius:50%;
    display:none; align-items:center; justify-content:center;
    background:#fff; color:#0f172a; border:1px solid #dbe7ff;
    box-shadow:0 14px 32px rgba(18,38,63,.22);
    transition:.2s ease;
  }
  .fab-search i{ font-size:1.15rem }
  .fab-hide{ opacity:0; transform:scale(.9); pointer-events:none }

  /* Sheet móvil */
  .sheet-mask{
    position:fixed; inset:0;
    background:rgba(15,23,42,.18);
    opacity:0; pointer-events:none; transition:.2s ease; z-index:1039;
    backdrop-filter: blur(8px) saturate(120%);
    -webkit-backdrop-filter: blur(8px) saturate(120%);
  }
  .sheet-mask.open{ opacity:1; pointer-events:auto }
  body.sheet-open .blur-on-sheet{ filter:blur(4px); transform:translateZ(0); }

  .sheet{
    position:fixed; left:0; right:0;
    bottom: var(--kb, 0px);
    z-index:1040;
    background:#fff; border-radius:16px 16px 0 0;
    border-top:1px solid #dbe7ff; box-shadow:0 -8px 40px rgba(18,38,63,.18);
    transform:translateY(8px); opacity:0; pointer-events:none; transition:.22s ease;
    touch-action:pan-y;
    max-height: calc(min(100dvh, 100vh) - 12px);
    padding-bottom: calc(env(safe-area-inset-bottom, 0px) + 8px);
  }
  .sheet.open{ transform:translateY(0); opacity:1; pointer-events:auto }
  .sheet.dragging{ transition:none }
  .sheet .handle{ height:22px; display:grid; place-items:center }
  .sheet .handle span{ width:46px; height:4px; background:#e2e8f0; border-radius:999px; }
  .sheet .body{ padding:12px 14px 16px; overflow:auto; }

  .search-input{
    width:100%; border:1px solid #dbe7ff; border-radius:12px;
    padding:.78rem .98rem; outline:0;
    transition:.15s ease;
    font-weight:500;
  }
  .search-input:focus{ border-color:#cfe0ff; box-shadow:0 0 0 4px rgba(31,75,184,.08) }

  .actions{ display:flex; gap:.7rem; margin-top:.85rem; align-items:stretch; }
  .btn-soft{
    width:100%; height:44px; border-radius:12px; padding:.72rem 1rem; font-weight:650;
    border:1px solid transparent; box-shadow:0 6px 16px rgba(18,38,63,.06);
    display:flex; align-items:center; justify-content:center; gap:.5rem;
    transition:.12s ease;
    background:#fff;
    text-decoration:none;
  }
  .btn-soft:active{ transform:translateY(1px) }
  .btn-soft-blue{ background:var(--soft-blue-bg); color:var(--soft-blue-fg); border-color:var(--soft-blue-br); }
  .btn-soft-gray{ background:var(--soft-gray-bg); color:var(--soft-gray-fg); border-color:var(--soft-gray-br); }

  /* Tabla */
  .table-wrap{
    background:#fff;
    border:1px solid #dce7ff;
    border-radius:16px;
    box-shadow:0 8px 24px rgba(31,75,184,.06);
    overflow:hidden;
  }
  table{ width:100%; border-collapse:separate; border-spacing:0; margin:0; }
  thead th{
    background: rgba(31,75,184,.08);
    color:#0f172a;
    font-weight:600;
    font-size:.9rem;
    border-bottom:1px solid #dce7ff;
    padding:12px 12px;
    white-space:nowrap;
  }
  tbody td{
    padding:12px 12px;
    border-bottom:1px solid #eef2f7;
    vertical-align:middle;
    font-weight:500;
    color:#0f172a;
  }
  tbody tr:hover{ background:#f7fbff; }

  .muted{ color:var(--muted); font-weight:450; font-size:.88rem; }
  .money{ font-variant-numeric: tabular-nums; }

  /* Fecha legible */
  .date{ line-height:1.15; }
  .date .d{ font-weight:600; color:var(--ink); }
  .date .t{ margin-top:2px; color:var(--muted); font-size:.86rem; font-weight:450; }

  .icon-btn{
    display:inline-flex; align-items:center; justify-content:center;
    width:34px; height:34px; border-radius:8px;
    background:#f1f5f9; color:#111827;
    margin-right:.25rem; text-decoration:none; transition:.15s ease;
    border:0;
  }
  .icon-btn:hover{ background:#e2e8f0; }

  .empty{
    border:1px dashed #cfe0ff;
    border-radius:16px;
    background:#fff;
    padding:16px;
    color:var(--muted);
    font-weight:600;
  }

  /* Responsivo */
  @media (max-width: 767.98px){
    .hero{ padding:14px 14px; }
    .hero-actions{ width:100%; gap:.6rem; flex-wrap:wrap }
    .desktop-search{ display:none !important; }
    .fab-search{ display:flex }
  }
  @media (min-width: 768px){
    .desktop-search{ display:inline-flex }
  }
</style>

@php
  $countTotal = method_exists($remisiones, 'total') ? $remisiones->total() : $remisiones->count();

  $createUrl = \Illuminate\Support\Facades\Route::has('remisions.create')
    ? route('remisions.create')
    : url('/remisions/create');
@endphp

{{-- HERO --}}
<div class="hero blur-on-sheet fade-slide d-flex align-items-center justify-content-between flex-wrap" style="margin-top:20px;">
  <div class="d-flex align-items-center gap-3">
    <div class="rounded-circle d-inline-flex align-items-center justify-content-center bg-white border"
         style="width:44px;height:44px;border-color:#dce7ff">
      <i class="fa-solid fa-screwdriver-wrench" style="font-size:1.1rem;color:var(--pblue-700)"></i>
    </div>
    <div>
      <h1 class="h4 mb-0 h-title">Historial de Mantenimiento</h1>
      <div class="small h-sub">Consulta y filtra remisiones de servicio (uso interno).</div>
    </div>
  </div>

  <div class="d-flex align-items-center gap-2 hero-actions">
    <div class="desktop-search">
      <i class="fa-solid fa-magnifying-glass" style="opacity:.7"></i>
      <input id="desktopSearchRemi" type="text" placeholder="Buscar…">
    </div>

    {{-- ✅ QUITADO el badge al lado del botón (ya no hay chip/contador aquí) --}}

    <a href="{{ $createUrl }}" class="btn-pastel">
      <i class="fa-solid fa-plus"></i> Nueva remisión
    </a>
  </div>
</div>

<!-- FAB (móvil) -->
<button type="button" class="fab-search js-open-search" aria-label="Buscar">
  <i class="fa-solid fa-magnifying-glass"></i>
</button>

{{-- Sheet (móvil) --}}
<div class="sheet" role="dialog" aria-modal="true" aria-label="Búsqueda de mantenimiento">
  <div class="handle"><span></span></div>
  <div class="body">
    <input id="customSearchRemi" type="search" inputmode="search" enterkeyhint="search"
           class="search-input" placeholder="Escribe para filtrar…">

    <div class="actions">
      <button type="button" class="btn-soft btn-soft-gray" id="btnClearSearchRemi">
        <i class="fa-solid fa-eraser"></i> <span>Limpiar</span>
      </button>
      <button type="button" class="btn-soft btn-soft-blue js-close-sheet">
        <i class="fa-solid fa-check"></i> <span>Aplicar</span>
      </button>
    </div>

    <div style="margin-top:.8rem;">
      <a href="{{ $createUrl }}" class="btn-soft btn-soft-blue" style="height:46px;">
        <i class="fa-solid fa-plus"></i> <span>Nueva remisión</span>
      </a>
    </div>
  </div>
</div>
<div class="sheet-mask"></div>

{{-- TABLA --}}
<div id="remiContainer" class="container blur-on-sheet">
  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif
  @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
  @endif

  @if(($countTotal ?? 0) == 0)
    <div class="empty">
      <i class="fa-regular fa-circle-check"></i>
      Aún no hay remisiones de mantenimiento registradas.
    </div>
  @else
    <div class="table-wrap" id="tableWrap">
      <div class="table-responsive">
        <table id="remiTable" class="table w-100">
          <thead>
            <tr>
              <th>Cliente</th>
              <th style="width:220px;">Usuario</th>
              <th style="width:200px;">Fecha</th>
              <th style="width:150px;">Total</th>
              <th style="width:170px;">Acciones</th>
            </tr>
          </thead>
          <tbody>
            @foreach($remisiones as $remision)
              @php
                $cliente = trim(($remision->cliente->nombre ?? '').' '.($remision->cliente->apellido ?? '')) ?: ($remision->cliente->nombre ?? '—');
                $tel = $remision->cliente->telefono ?? null;

                $usuario = $remision->user->name ?? ($remision->usuario->name ?? '—');

                $fechaDia = $remision->created_at?->format('d/m/Y') ?? '—';
                $fechaHora = $remision->created_at?->format('H:i') ?? '';
              @endphp

              <tr class="remi-row">
                <td>
                  <div style="line-height:1.15;">
                    {{ mb_strtoupper($cliente,'UTF-8') }}
                  </div>
                  <div class="muted">
                    <i class="fa-solid fa-phone" style="opacity:.7"></i>
                    {{ $tel ?: 'Sin teléfono' }}
                  </div>
                </td>

                <td>
                  {{ mb_strtoupper($usuario,'UTF-8') }}
                </td>

                <td class="date" data-order="{{ $remision->created_at?->format('Y-m-d H:i:s') }}">
                  <div class="d"><i class="fa-regular fa-calendar" style="opacity:.7"></i> {{ $fechaDia }}</div>
                  @if($fechaHora)
                    <div class="t"><i class="fa-regular fa-clock" style="opacity:.65"></i> {{ $fechaHora }}</div>
                  @endif
                </td>

                <td class="money">
                  ${{ number_format((float)($remision->total ?? 0), 2) }}
                </td>

                <td>
                  <a href="{{ route('remisions.show', $remision) }}" class="icon-btn" title="Ver">
                    <i class="fa-solid fa-eye"></i>
                  </a>
                  <a href="{{ route('remisions.descargarPdf', $remision) }}" class="icon-btn" title="PDF">
                    <i class="fa-solid fa-file-pdf"></i>
                  </a>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>

    @if(method_exists($remisiones, 'links'))
      <div style="margin-top:12px;">
        {{ $remisiones->links() }}
      </div>
    @endif
  @endif
</div>

<script>
  (function () {
    /* ancho hero = ancho tabla */
    function syncHeroWidth(){
      const hero = document.querySelector('.hero');
      const tw   = document.getElementById('tableWrap');
      const cont = document.getElementById('remiContainer');
      if(!hero) return;
      const ref = tw || cont;
      if(!ref) return;
      hero.style.setProperty('--hero-w', Math.round(ref.getBoundingClientRect().width) + 'px');
    }
    syncHeroWidth();
    let _t=null;
    window.addEventListener('resize', () => { clearTimeout(_t); _t=setTimeout(syncHeroWidth, 120); });

    /* buscador sin datatable */
    const rows = Array.from(document.querySelectorAll('#remiTable tbody tr.remi-row'));
    const normalize = (s) => (s || '')
      .toString()
      .normalize('NFD')
      .replace(/[\u0300-\u036f]/g, '')
      .toLowerCase()
      .trim();

    let lastSearchRaw = '';
    function applySearch(raw){
      lastSearchRaw = raw || '';
      const q = normalize(raw);

      rows.forEach(tr => {
        const hay = normalize(tr.innerText);
        tr.style.display = (!q || hay.includes(q)) ? '' : 'none';
      });

      const d = document.getElementById('desktopSearchRemi');
      if(d && d !== document.activeElement) d.value = lastSearchRaw;
    }
    function debounce(fn, wait){
      let t; return function(){
        clearTimeout(t);
        const args = arguments;
        t = setTimeout(()=>fn.apply(this,args), wait);
      }
    }
    const onSearch = debounce((val)=>applySearch(val), 80);

    const desktop = document.getElementById('desktopSearchRemi');
    if(desktop) desktop.addEventListener('input', function(){ onSearch(this.value); });

    const mobile = document.getElementById('customSearchRemi');
    if(mobile) mobile.addEventListener('input', function(){ onSearch(this.value); });

    const btnClear = document.getElementById('btnClearSearchRemi');
    if(btnClear){
      btnClear.addEventListener('click', function(){
        applySearch('');
        if(mobile){ mobile.value=''; mobile.focus(); }
      });
    }

    /* sheet móvil */
    const sheet = document.querySelector('.sheet');
    const mask  = document.querySelector('.sheet-mask');
    const fab   = document.querySelector('.fab-search');
    const root  = document.documentElement;
    const bodyEl = document.body;
    const isMobile = () => window.matchMedia('(max-width: 767.98px)').matches;

    (function attachVisualViewport(){
      const vv = window.visualViewport;
      if(!vv) return;
      const updateKB = () => {
        const kb = Math.max(0, window.innerHeight - vv.height - vv.offsetTop);
        root.style.setProperty('--kb', kb + 'px');
      };
      vv.addEventListener('resize', updateKB);
      vv.addEventListener('scroll', updateKB);
      updateKB();
    })();

    function openSheet(){
      if(!isMobile()) return;
      sheet?.classList.add('open');
      mask?.classList.add('open');
      fab?.classList.add('fab-hide');
      bodyEl.classList.add('sheet-open');

      if(mobile){
        mobile.value = lastSearchRaw;
        setTimeout(() => {
          mobile.focus({ preventScroll:true });
          mobile.scrollIntoView({block:'center'});
        }, 50);
      }
    }
    function closeSheet(preserve=true){
      sheet?.classList.remove('open','dragging');
      if(sheet) sheet.style.transform = '';
      mask?.classList.remove('open');
      fab?.classList.remove('fab-hide');
      bodyEl.classList.remove('sheet-open');
      if(!preserve) applySearch('');
      root.style.setProperty('--kb','0px');
    }

    document.addEventListener('click', (e) => {
      if(e.target.closest('.js-open-search')) openSheet();
      if(e.target.closest('.js-close-sheet')) closeSheet(true);
    });
    mask?.addEventListener('click', () => closeSheet(true));
    document.addEventListener('keydown', (e) => { if(e.key === 'Escape') closeSheet(true); });

    (function(){
      if(!sheet) return;
      let startY=null, delta=0;
      const start = (y)=>{ startY=y; delta=0; sheet.classList.add('dragging'); };
      const move  = (y)=>{ if(startY===null) return; delta=Math.max(0, y-startY); sheet.style.transform = `translateY(${delta}px)`; };
      const end   = ()=> {
        if(startY===null) return;
        const shouldClose = delta > 90;
        if(shouldClose) closeSheet(true);
        else { sheet.classList.remove('dragging'); sheet.style.transform=''; }
        startY=null; delta=0;
      };
      sheet.addEventListener('touchstart', (e)=>{
        if(!isMobile()) return;
        const t=e.touches[0];
        const rect=sheet.getBoundingClientRect();
        if(t.clientY < rect.top + 80) start(t.clientY);
      }, {passive:true});
      sheet.addEventListener('touchmove', (e)=>{ if(!isMobile()) return; move(e.touches[0].clientY); }, {passive:true});
      sheet.addEventListener('touchend', end);
    })();
  })();
</script>
@endsection
