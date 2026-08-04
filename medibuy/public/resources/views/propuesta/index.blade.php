@extends('layouts.app')

@section('title', 'Cotizaciones')
@section('titulo', 'Cotizaciones')

@section('content')
<link rel="stylesheet" href="{{ asset('css/remision.css') }}?v={{ time() }}">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

<!-- DataTables + Buttons -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">

<style>
  :root{
    --pblue-50:#eef6ff; --pblue-75:#f3f8ff; --pblue-100:#e3efff; --pblue-700:#1f4bb8;
    --ink:#0f172a; --muted:#6b7280; --line:#e7ebf0; --card:#ffffff;

    /* Paleta pastel */
    --soft-blue-bg:#edf4ff;  --soft-blue-fg:#1f4bb8;  --soft-blue-br:#dbe7ff;
    --soft-gray-bg:#f4f7fb;  --soft-gray-fg:#475569;  --soft-gray-br:#e5eaf5;
  }

  /* ---------- HERO (sin container; mismo ancho que el .container) ---------- */
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

  /* --- Buscador inline (desktop) --- */
  .desktop-search{
    display:inline-flex; align-items:center; gap:.5rem; padding:.45rem .75rem;
    border:1px solid #dbe7ff; border-radius:999px; background:#fff;
    box-shadow:0 8px 22px rgba(31,75,184,.06);
  }
  .desktop-search input{
    border:0; outline:0; width:220px; max-width:38vw; font-weight:500;
  }

  /* --- Botón "Nueva cotización" pastel --- */
  .btn-pastel{
    display:inline-flex; align-items:center; gap:.5rem;
    background:var(--soft-blue-bg);
    color:var(--soft-blue-fg);
    border:1px solid var(--soft-blue-br); border-radius:14px;
    padding:.6rem 1rem; font-weight:700; text-decoration:none;
    box-shadow:0 6px 16px rgba(31,75,184,.08); transition:.15s ease;
  }
  .btn-pastel:hover{ filter:brightness(1.02); transform:translateY(-1px) }

  /* ---------- FAB Buscar (solo móvil) ---------- */
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

  /* ---------- MASK + BLUR ---------- */
  .sheet-mask{
    position:fixed; inset:0;
    background:rgba(15,23,42,.18);
    opacity:0; pointer-events:none; transition:.2s ease; z-index:1039;
    backdrop-filter: blur(8px) saturate(120%);
    -webkit-backdrop-filter: blur(8px) saturate(120%);
  }
  .sheet-mask.open{ opacity:1; pointer-events:auto }

  /* Blur extra directo a secciones (por si algún navegador no aplica backdrop) */
  body.sheet-open .blur-on-sheet{ filter:blur(4px); transform:translateZ(0); }

  /* ---------- SHEET (móvil) con soporte teclado iOS/Android ---------- */
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
    width:100%; border:1px solid #dbe7ff; border-radius:12px; padding:.78rem .98rem; outline:0;
    transition:.15s ease;
  }
  .search-input:focus{ border-color:#cfe0ff; box-shadow:0 0 0 4px rgba(31,75,184,.08) }

  .actions{ display:flex; gap:.7rem; margin-top:.85rem; align-items:stretch; }
  .btn-soft{
    width:100%; height:44px; border-radius:12px; padding:.72rem 1rem; font-weight:700;
    border:1px solid transparent; box-shadow:0 6px 16px rgba(18,38,63,.06);
    display:flex; align-items:center; justify-content:center; gap:.5rem;
    transition:.12s ease;
  }
  .btn-soft:active{ transform:translateY(1px) }
  .btn-soft-blue{ background:var(--soft-blue-bg); color:var(--soft-blue-fg); border-color:var(--soft-blue-br); }
  .btn-soft-gray{ background:var(--soft-gray-bg); color:var(--soft-gray-fg); border-color:var(--soft-gray-br); }

  /* ---------- DataTables ---------- */
  .icon-btn{
    display:inline-flex; align-items:center; justify-content:center;
    width:34px; height:34px; border-radius:8px; background:#f1f5f9; color:#111827;
    margin-right:.25rem; text-decoration:none; transition:.15s ease;
  }
  .icon-btn:hover{ background:#e2e8f0; }
  .dataTables_wrapper .dt-buttons .dt-button{
    background:#fff; border:1px solid #e5e7eb; border-radius:10px; padding:.45rem .75rem;
    margin-right:.4rem; font-weight:700; color:#111827; box-shadow:0 2px 6px rgba(0,0,0,.04);
  }

  /* ---------- Responsivo ---------- */
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

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

<script>
  $(function () {
    /* ===== Igualar ancho del HERO al .container ===== */
    function syncHeroWidth(){
      const cont = document.getElementById('cotizContainer');
      const hero = document.querySelector('.hero');
      if(!cont || !hero) return;
      const w = Math.round(cont.getBoundingClientRect().width);
      hero.style.setProperty('--hero-w', w + 'px');
    }
    syncHeroWidth();
    let _t=null;
    window.addEventListener('resize', () => { clearTimeout(_t); _t=setTimeout(syncHeroWidth, 120); });

    /* ===== DataTables ===== */
    // Búsqueda insensible a acentos
    jQuery.fn.dataTable.ext.type.search.string = function ( data ) {
      if (data === null) return '';
      return data.toString().normalize('NFD').replace(/[\u0300-\u036f]/g, '');
    };

    const tabla = $('#propuestasTable').DataTable({
      order: [[4, 'desc']],
      language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json' },
      pageLength: 25,
      dom: '<"d-flex justify-content-between align-items-center mb-3"B>rt<"d-flex justify-content-between align-items-center mt-3"ip>',
      buttons: [
        { extend: 'excelHtml5', text: '<i class="fa-solid fa-file-excel"></i> Excel', titleAttr: 'Exportar a Excel' },
        { extend: 'pdfHtml5',   text: '<i class="fa-solid fa-file-pdf"></i> PDF',   titleAttr: 'Exportar a PDF', orientation: 'landscape', pageSize: 'A4' },
        { extend: 'print',      text: '<i class="fa-solid fa-print"></i> Imprimir', titleAttr: 'Imprimir tabla' }
      ]
    });

    /* ===== Búsqueda unificada (desktop + sheet) ===== */
    let lastSearchRaw = '';  // lo que escribió el usuario (con acentos)
    const normalize = s => (s || '').toString().normalize('NFD').replace(/[\u0300-\u036f]/g, '');
    function debounce(fn, wait){ let t; return function(){ clearTimeout(t); const a=arguments; t=setTimeout(()=>fn.apply(this,a), wait); }; }
    const applySearch = debounce((raw) => {
      lastSearchRaw = raw;
      const norm = normalize(raw);
      tabla.search(norm).draw();
      // sincroniza con el buscador desktop si existe
      const d = document.getElementById('desktopSearch');
      if(d && d !== document.activeElement) d.value = raw;
    }, 80);

    // Desktop input
    $('#desktopSearch').on('input keyup change search', function(){ applySearch(this.value); });

    /* ===== Sheet (móvil) + teclado seguro ===== */
    const $sheet = $('.sheet');
    const $mask  = $('.sheet-mask');
    const $fab   = $('.fab-search');
    const root   = document.documentElement;
    const isMobile = () => window.matchMedia('(max-width: 767.98px)').matches;
    const bodyEl = document.body;

    // Ajuste por teclado virtual con visualViewport
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
      $sheet.addClass('open'); $mask.addClass('open'); $fab.addClass('fab-hide');
      bodyEl.classList.add('sheet-open');             // activa blur directo a secciones
      const input = document.getElementById('customSearch');
      input.value = lastSearchRaw;                    // ← rellena con lo último buscado
      setTimeout(() => { input.focus({ preventScroll:true }); input.scrollIntoView({block:'center'}); }, 50);
    }
    function closeSheet(preserve=true){
      // preserve=true: NO limpiar búsqueda (Aplicar, tap en máscara, gesto, etc.)
      $sheet.removeClass('open dragging').css('transform','');
      $mask.removeClass('open');
      $fab.removeClass('fab-hide');
      bodyEl.classList.remove('sheet-open');
      if(!preserve){
        lastSearchRaw = '';
        applySearch(''); // limpia tabla y sincroniza inputs
      }
      root.style.setProperty('--kb','0px');
    }

    // Abrir / Cerrar
    $(document).on('click', '.js-open-search', openSheet);
    $(document).on('click', '.js-close-sheet', function(){ closeSheet(true); });  // Aplicar = conservar filtro
    $mask.on('click', function(){ closeSheet(true); });                           // Tap fuera = conservar filtro
    $(document).on('keydown', e => { if(e.key === 'Escape') closeSheet(true); });

    // Gesto: deslizar hacia abajo para cerrar (conservar filtro)
    (function(){
      const el = document.querySelector('.sheet');
      let startY=null, delta=0;
      const start = (y)=>{ startY = y; delta=0; el.classList.add('dragging'); };
      const move  = (y)=>{ if(startY===null) return; delta = Math.max(0, y-startY); el.style.transform = `translateY(${delta}px)`; };
      const end   = ()=>{ 
        if(startY===null) return;
        const shouldClose = delta > 90;
        if(shouldClose) closeSheet(true);
        else { el.classList.remove('dragging'); el.style.transform=''; }
        startY=null; delta=0;
      };
      el.addEventListener('touchstart', (e)=>{ 
        if(!isMobile()) return;
        const t=e.touches[0];
        const rect=el.getBoundingClientRect();
        if(t.clientY < rect.top + 80) start(t.clientY);
      }, {passive:true});
      el.addEventListener('touchmove',  (e)=>{ if(!isMobile()) return; const t=e.touches[0]; move(t.clientY); }, {passive:true});
      el.addEventListener('touchend',   end);
    })();

    // Input del sheet (robusto iOS/Android)
    $('#customSearch').on('input keyup change search', function(){ applySearch(this.value); });

    // Limpiar: borra filtro y mantiene sheet abierto con foco
    $('#btnClearSearch').on('click', function(){
      lastSearchRaw = '';
      applySearch('');
      const input = document.getElementById('customSearch');
      input.value = '';
      input.focus();
    });
  });
</script>

{{-- ===== HERO (marcado como "blur-on-sheet" para borroso al abrir) ===== --}}
<div class="hero blur-on-sheet fade-slide d-flex align-items-center justify-content-between flex-wrap" style="margin-top:20px;">
  <div class="hero-main d-flex align-items-center gap-3">
    <div class="rounded-circle d-inline-flex align-items-center justify-content-center bg-white border"
         style="width:44px;height:44px;border-color:#dce7ff">
      <i class="bi bi-graph-up-arrow" style="font-size:1.2rem;color:var(--pblue-700)"></i>
    </div>
    <div>
      <h1 class="h4 mb-0">Cotizaciones</h1>
      <div class="small" style="color:var(--muted)">Consulta, exporta y gestiona tus cotizaciones en tiempo real.</div>
    </div>
  </div>

  <div class="d-flex align-items-center gap-2 hero-actions">
    <!-- DESKTOP: buscador inline -->
    <div class="desktop-search">
      <i class="fa-solid fa-magnifying-glass" style="opacity:.7"></i>
      <input id="desktopSearch" type="text" placeholder="Buscar…">
    </div>

    <a href="{{ route('propuestas.create', [], false) }}" class="btn btn-pastel">
      <i class="bi bi-plus-lg"></i> Nueva cotización
    </a>
  </div>
</div>

<!-- FAB Buscar (solo móvil; se oculta al abrir el sheet) -->
<button type="button" class="fab-search js-open-search" aria-label="Buscar">
  <i class="fa-solid fa-magnifying-glass"></i>
</button>

{{-- Sheet (móvil) + máscara (con blur) --}}
<div class="sheet" role="dialog" aria-modal="true" aria-label="Búsqueda de cotizaciones">
  <div class="handle"><span></span></div>
  <div class="body">
    <input id="customSearch" type="search" inputmode="search" enterkeyhint="search"
           class="search-input" placeholder="Escribe para filtrar…">
    <div class="actions">
      <button type="button" class="btn-soft btn-soft-gray" id="btnClearSearch">
        <i class="fa-solid fa-eraser"></i> <span>Limpiar</span>
      </button>
      <button type="button" class="btn-soft btn-soft-blue js-close-sheet">
        <i class="fa-solid fa-check"></i> <span>Aplicar</span>
      </button>
    </div>
  </div>
</div>
<div class="sheet-mask"></div>

{{-- ===== TABLA (también borrosa al abrir el sheet) ===== --}}
<div id="cotizContainer" class="container blur-on-sheet">
  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  <div class="table-responsive">
    <table id="propuestasTable" class="table w-100">
      <thead>
        <tr>
          <th>ID</th>
          <th>Cliente</th>
          <th>Usuario</th>
          <th>Total</th>
          <th>Fecha</th>
          <th>Plan</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
      @forelse($propuestas as $propuesta)
        <tr>
          <td>{{ $propuesta->id }}</td>
          <td>
            {{ isset($propuesta->cliente)
                ? mb_strtoupper(trim(($propuesta->cliente->nombre ?? '').' '.($propuesta->cliente->apellido ?? '')), 'UTF-8')
                : 'N/A' }}
          </td>
          <td>{{ isset($propuesta->usuario) ? mb_strtoupper($propuesta->usuario->name, 'UTF-8') : 'N/A' }}</td>
          <td>${{ number_format($propuesta->total, 2) }}</td>
          <td data-order="{{ $propuesta->created_at->timestamp }}">{{ $propuesta->created_at->format('d/m/Y H:i') }}</td>
          <td>{{ ucfirst($propuesta->plan) }}</td>
          <td>
            <a href="{{ route('propuestas.show', $propuesta->id) }}" class="icon-btn" title="Ver"><i class="fa-solid fa-eye"></i></a>
            <a href="{{ route('propuestas.pdf', $propuesta->id) }}" class="icon-btn" title="Descargar PDF"><i class="fa-solid fa-file-pdf"></i></a>
            <a href="{{ route('propuestas.edit', $propuesta->id) }}" class="icon-btn" title="Editar"><i class="fa-solid fa-pen-to-square"></i></a>
          </td>
        </tr>
      @empty
        <tr><td colspan="7" class="text-center">No hay propuestas registradas.</td></tr>
      @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
