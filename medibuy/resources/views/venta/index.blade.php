@extends('layouts.app')
@section('title', 'Historial Remisión')
@section('titulo', 'Historial Remisión')

@section('content')
@include('partials.submenu-cotizaciones')

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="{{ asset('css/remision.css') }}?v={{ time() }}">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">

<style>
  :root{
    --pblue-50:#eef6ff; --pblue-75:#f3f8ff; --pblue-100:#e3efff; --pblue-700:#1f4bb8;
    --ink:#0f172a; --muted:#6b7280; --line:#e7ebf0; --card:#ffffff;

    --soft-blue-bg:#edf4ff;  --soft-blue-fg:#1f4bb8;  --soft-blue-br:#dbe7ff;
    --soft-gray-bg:#f4f7fb;  --soft-gray-fg:#475569;  --soft-gray-br:#e5eaf5;

    --m-bg:#ffffff;
    --m-line:#e7ebf2;
    --m-line2:#dde6f6;
    --m-shadow:0 26px 70px rgba(15,23,42,.18);
    --m-glow:0 0 0 10px rgba(31,75,184,.07);
    --m-blue:#1f4bb8;
    --m-danger:#dc2626;
  }

  .hero{
    position: sticky;
    top: 70px;
    z-index: 1020;
    width: var(--hero-w, min(1320px, calc(100% - 24px)));
    margin:90px auto 10px;
    padding:18px 20px;
    background:
      radial-gradient(120% 120% at 0% 0%, var(--pblue-50) 0%, #fff 55%),
      radial-gradient(120% 120% at 100% 0%, var(--pblue-75) 0%, #fff 55%);
    border:1px solid #dce7ff;
    border-radius:18px;
    box-shadow:
      0 8px 24px rgba(31,75,184,.08),
      0 10px 30px rgba(15,23,42,.05);
    backdrop-filter: blur(14px);
    -webkit-backdrop-filter: blur(14px);
  }

  #remiContainer{
    margin-top: 18px;
    padding-bottom: 30px;
  }

  @media (max-width: 767.98px){
    .hero{
      top: 62px;
      margin-top: 12px !important;
      border-radius: 16px;
      padding: 14px;
    }
  }

  .fade-slide{animation:fadeSlide .45s ease both}
  @keyframes fadeSlide{ from{opacity:0; transform:translateY(6px)} to{opacity:1; transform:translateY(0)} }

  .desktop-search{
    display:inline-flex;
    align-items:center;
    gap:.5rem;
    padding:.45rem .75rem;
    border:1px solid #dbe7ff;
    border-radius:999px;
    background:#fff;
    box-shadow:0 8px 22px rgba(31,75,184,.06);
    flex:1;
    min-width:520px;
    max-width:820px;
  }
  .desktop-search input{
    border:0;
    outline:0;
    width:100%;
    font-weight:500;
    font-size:15px;
  }

  .btn-pastel{
    display:inline-flex; align-items:center; gap:.5rem;
    background:var(--soft-blue-bg); color:var(--soft-blue-fg);
    border:1px solid var(--soft-blue-br); border-radius:14px;
    padding:.6rem 1rem; font-weight:700; text-decoration:none;
    box-shadow:0 6px 16px rgba(31,75,184,.08); transition:.15s ease;
  }
  .btn-pastel:hover{ filter:brightness(1.02); transform:translateY(-1px) }

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

  .icon-btn{
    display:inline-flex; align-items:center; justify-content:center;
    width:34px; height:34px; border-radius:8px; background:#f1f5f9; color:#111827;
    margin-right:.25rem; text-decoration:none; transition:.15s ease;
    border:0; cursor:pointer;
  }
  .icon-btn:hover{ background:#e2e8f0; }
  .dataTables_wrapper .dt-buttons .dt-button{
    background:#fff !important;
    border:1px solid #e7ebf0 !important;
    border-radius:12px !important;
    padding:.55rem .9rem !important;
    margin-right:.45rem !important;
    font-weight:700 !important;
    color:#334155 !important;
    box-shadow:0 4px 12px rgba(15,23,42,.05);
    transition:.15s ease;
  }
  .dataTables_wrapper .dt-buttons .dt-button:hover{
    background:#edf4ff !important;
    border-color:#dbe7ff !important;
    color:#1f4bb8 !important;
  }

  @media (max-width: 767.98px){
    .hero{ padding:14px 14px; }
    .hero-actions{ width:100%; gap:.6rem; flex-wrap:wrap }
    .desktop-search{ display:none !important; }
    .fab-search{ display:flex }
  }
  @media (min-width: 768px){
    .desktop-search{ display:inline-flex }
  }

  .bank-mask{
    position:fixed; inset:0;
    background:rgba(15,23,42,.22);
    opacity:0; pointer-events:none;
    transition:.18s ease;
    z-index:5000;
    backdrop-filter: blur(10px) saturate(120%);
    -webkit-backdrop-filter: blur(10px) saturate(120%);
  }
  .bank-mask.open{ opacity:1; pointer-events:auto; }

  .bank-modal{
    position:fixed; left:50%; top:50%;
    transform:translate(-50%, -46%) scale(.985);
    width:min(520px, calc(100% - 28px));
    opacity:0; pointer-events:none;
    transition:.18s ease;
    z-index:5001;
  }
  .bank-modal.open{
    opacity:1; pointer-events:auto;
    transform:translate(-50%, -50%) scale(1);
  }

  .bank-card{
    background:rgba(255,255,255,.92);
    border:1px solid var(--m-line2);
    border-radius:18px;
    box-shadow: var(--m-shadow), var(--m-glow);
    overflow:hidden;
  }

  .bank-top{
    padding:14px 16px;
    display:flex; align-items:flex-start; justify-content:space-between; gap:10px;
    border-bottom:1px solid var(--m-line);
    background:
      radial-gradient(120% 120% at 0% 0%, rgba(31,75,184,.08) 0%, rgba(255,255,255,.0) 55%),
      linear-gradient(180deg, rgba(255,255,255,.75) 0%, rgba(255,255,255,.35) 100%);
  }

  .bank-brand{ display:flex; gap:12px; align-items:center; }
  .bank-badge{
    width:44px; height:44px; border-radius:14px;
    display:grid; place-items:center;
    background: rgba(31,75,184,.08);
    border:1px solid rgba(31,75,184,.14);
  }
  .bank-badge i{ color:var(--m-blue); font-size:1.15rem; }

  .bank-title{
    font-weight:900; color:var(--ink);
    letter-spacing:.2px; line-height:1.1;
  }
  .bank-sub{ margin-top:2px; font-size:.88rem; color:var(--muted); }
  .bank-close{
    border:1px solid var(--m-line);
    background:#fff; color:#475569;
    width:36px; height:36px; border-radius:12px;
    display:grid; place-items:center; transition:.12s ease;
  }
  .bank-close:hover{ background:#f8fafc; }

  .bank-body{ padding:14px 16px 16px; }

  .bank-alert{
    display:flex; align-items:flex-start; gap:10px; padding:10px 12px;
    border-radius:14px; border:1px solid var(--m-line);
    background: rgba(31,75,184,.045); color:#334155;
    font-size:.92rem; line-height:1.35;
  }
  .bank-alert .dot{
    width:10px; height:10px; border-radius:999px;
    background: rgba(31,75,184,.75);
    box-shadow: 0 0 0 6px rgba(31,75,184,.10);
    margin-top:4px; flex:0 0 auto;
  }
  .bank-alert b{ color:var(--ink); }

  .otp-row{ margin-top:12px; display:flex; gap:10px; justify-content:center; }
  .otp{
    width:54px; height:58px; text-align:center; font-weight:900; font-size:1.15rem;
    color:var(--ink); background:#fff; border:1px solid var(--m-line2);
    border-radius:14px; outline:0; box-shadow: 0 1px 0 rgba(15,23,42,.02) inset;
    transition:.12s ease;
  }
  .otp:focus{
    border-color: rgba(31,75,184,.35);
    box-shadow: 0 0 0 6px rgba(31,75,184,.12);
  }
  .otp.error{
    border-color: rgba(220,38,38,.40);
    box-shadow: 0 0 0 6px rgba(220,38,38,.12);
  }
  .otp.success{
    border-color: rgba(21,128,61,.40);
    box-shadow: 0 0 0 6px rgba(21,128,61,.12);
    background: #f0fdf4;
  }
  @media (max-width:420px){
    .otp{ width:44px; height:54px; border-radius:12px; }
    .otp-row{ gap:8px; }
  }

  .bank-note{ margin-top:10px; text-align:center; font-size:.85rem; color:#64748b; }

  .loading-dots{
    margin-top:10px; display:none; justify-content:center; gap:6px;
  }
  .loading-dots span{
    width:7px; height:7px; border-radius:999px;
    background: rgba(31,75,184,.55); opacity:.6;
    animation: dotPulse .9s infinite ease-in-out;
  }
  .loading-dots span:nth-child(2){ animation-delay:.12s; }
  .loading-dots span:nth-child(3){ animation-delay:.24s; }
  @keyframes dotPulse{
    0%,100%{ transform:translateY(0); opacity:.45; }
    50%{ transform:translateY(-4px); opacity:1; }
  }

  @keyframes shake {
    0%,100%{ transform: translateX(0); }
    20%{ transform: translateX(-5px); }
    40%{ transform: translateX(5px); }
    60%{ transform: translateX(-4px); }
    80%{ transform: translateX(4px); }
  }
  .shake { animation: shake .28s ease; }

  @media (min-width: 768px) {
    .hero, #remiContainer, .alert-flotante {
        margin-left: calc(88px + 32px) !important;
        margin-right: auto !important;
        max-width: calc(100% - 88px - 48px) !important;
        width: auto !important;
    }
  }

  .desktop-filter{
    display:inline-flex; align-items:center; gap:.5rem; padding:.45rem .75rem;
    border:1px solid #dbe7ff; border-radius:999px; background:#fff;
    box-shadow:0 8px 22px rgba(31,75,184,.06);
  }
  .desktop-filter select{
    border:0; outline:0; background:transparent; font-weight:500;
    color:#0f172a; min-width:140px; cursor:pointer;
  }

  #ventasTable{ border-collapse:separate !important; border-spacing:0 10px !important; }
  #ventasTable thead th{
    border:0 !important; font-size:.83rem; text-transform:uppercase;
    letter-spacing:.4px; color:#64748b; padding-bottom:12px !important;
  }
  #ventasTable tbody tr{
    background:#fff; box-shadow:0 4px 14px rgba(15,23,42,.04); transition:.15s ease;
  }
  #ventasTable tbody tr:hover{ transform:translateY(-1px); box-shadow:0 8px 22px rgba(15,23,42,.06); }
  #ventasTable tbody td{ vertical-align:middle; border-top:1px solid #eef2f7 !important; border-bottom:1px solid #eef2f7 !important; padding:14px 12px !important; }
  #ventasTable tbody td:first-child{ border-left:1px solid #eef2f7 !important; border-radius:14px 0 0 14px; font-weight:700; }
  #ventasTable tbody td:last-child{ border-right:1px solid #eef2f7 !important; border-radius:0 14px 14px 0; }

  .icon-btn{ width:36px; height:36px; border-radius:10px; background:#f8fafc; border:1px solid #eef2f7; }
  .icon-btn:hover{ background:#edf4ff; color:#1f4bb8; }

  /* ── Alert flotante ── */
  .alert-flotante{
    border-radius:14px;
    padding:14px 18px;
    font-weight:600;
    display:flex;
    align-items:center;
    gap:10px;
    margin-top:12px;
    margin-bottom:0;
    transition: opacity .5s ease;
  }
  .alert-flotante.alert-success{ background:#f0fdf4; border:1px solid #bbf7d0; color:#15803d; }
  .alert-flotante.alert-danger{  background:#fef2f2; border:1px solid #fecaca; color:#dc2626; }
</style>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>

<script>
  $(function () {

    function syncHeroWidth(){
      const cont = document.getElementById('remiContainer');
      const hero = document.querySelector('.hero');
      if(!cont || !hero) return;
      const w = Math.round(cont.getBoundingClientRect().width);
      hero.style.setProperty('--hero-w', w + 'px');
    }
    syncHeroWidth();
    let _t = null;
    window.addEventListener('resize', () => { clearTimeout(_t); _t = setTimeout(syncHeroWidth, 120); });

    jQuery.fn.dataTable.ext.type.search.string = function (data) {
      if (data === null) return '';
      return data.toString().normalize('NFD').replace(/[\u0300-\u036f]/g, '');
    };

    const tabla = $('#ventasTable').DataTable({
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

    const colAgente = 2;
    const colPlan   = 5;
    const agentesSet = new Set();

    tabla.rows().data().each(function (row, i) {
      const celda = tabla.cell(i, colAgente).node();
      if (celda) agentesSet.add(celda.textContent.trim());
    });

    const $sel = document.getElementById('filtroAgente');
    [...agentesSet].sort().forEach(nombre => {
      const opt = document.createElement('option');
      opt.value = nombre; opt.textContent = nombre;
      $sel.appendChild(opt);
    });

    $('#filtroAgente').on('change', function () {
      const val = this.value;
      if (!val) { tabla.column(colAgente).search('', true, false).draw(); return; }
      const normalize = s => s.normalize('NFD').replace(/[\u0300-\u036f]/g, '');
      const escaped = normalize(val).replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
      tabla.column(colAgente).search(`^${escaped}$`, true, false).draw();
    });

    const planesSet = new Set();
    tabla.rows().data().each(function (row, i) {
      const celda = tabla.cell(i, colPlan).node();
      if (celda) planesSet.add(celda.textContent.trim());
    });

    const $plan = document.getElementById('filtroPlan');
    [...planesSet].sort().forEach(plan => {
      const opt = document.createElement('option');
      opt.value = plan; opt.textContent = plan;
      $plan.appendChild(opt);
    });

    $('#filtroPlan').on('change', function () {
      const val = this.value;
      if (!val) { tabla.column(colPlan).search('', true, false).draw(); return; }
      const normalize = s => s.normalize('NFD').replace(/[\u0300-\u036f]/g, '');
      const escaped = normalize(val).replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
      tabla.column(colPlan).search(`^${escaped}$`, true, false).draw();
    });

    let lastSearchRaw = '';
    const normalize = s => (s || '').toString().normalize('NFD').replace(/[\u0300-\u036f]/g, '');
    function debounce(fn, wait){ let t; return function(){ clearTimeout(t); const a = arguments; t = setTimeout(() => fn.apply(this, a), wait); }; }

    const applySearch = debounce((raw) => {
      lastSearchRaw = raw;
      tabla.search(normalize(raw)).draw();
      const d = document.getElementById('desktopSearchVentas');
      if (d && d !== document.activeElement) d.value = raw;
    }, 80);

    $('#desktopSearchVentas').on('input keyup change search', function(){ applySearch(this.value); });

    const $sheet = $('.sheet');
    const $mask  = $('.sheet-mask');
    const $fab   = $('.fab-search');
    const root   = document.documentElement;
    const bodyEl = document.body;
    const isMobile = () => window.matchMedia('(max-width: 767.98px)').matches;

    (function attachVisualViewport(){
      const vv = window.visualViewport;
      if (!vv) return;
      const updateKB = () => {
        const kb = Math.max(0, window.innerHeight - vv.height - vv.offsetTop);
        root.style.setProperty('--kb', kb + 'px');
      };
      vv.addEventListener('resize', updateKB);
      vv.addEventListener('scroll', updateKB);
      updateKB();
    })();

    function openSheet(){
      if (!isMobile()) return;
      $sheet.addClass('open'); $mask.addClass('open'); $fab.addClass('fab-hide');
      bodyEl.classList.add('sheet-open');
      const input = document.getElementById('customSearchVentas');
      input.value = lastSearchRaw;
      setTimeout(() => { input.focus({ preventScroll: true }); input.scrollIntoView({ block: 'center' }); }, 50);
    }

    function closeSheet(preserve = true){
      $sheet.removeClass('open dragging').css('transform', '');
      $mask.removeClass('open'); $fab.removeClass('fab-hide');
      bodyEl.classList.remove('sheet-open');
      if (!preserve){ lastSearchRaw = ''; applySearch(''); }
      root.style.setProperty('--kb', '0px');
    }

    $(document).on('click', '.js-open-search', openSheet);
    $(document).on('click', '.js-close-sheet', function(){ closeSheet(true); });
    $mask.on('click', function(){ closeSheet(true); });

    (function(){
      const el = document.querySelector('.sheet');
      if (!el) return;
      let startY = null, delta = 0;
      const start = (y) => { startY = y; delta = 0; el.classList.add('dragging'); };
      const move  = (y) => { if (startY === null) return; delta = Math.max(0, y - startY); el.style.transform = `translateY(${delta}px)`; };
      const end   = () => {
        if (startY === null) return;
        const shouldClose = delta > 90;
        if (shouldClose) closeSheet(true);
        else { el.classList.remove('dragging'); el.style.transform = ''; }
        startY = null; delta = 0;
      };
      el.addEventListener('touchstart', (e) => {
        if (!isMobile()) return;
        const t = e.touches[0]; const rect = el.getBoundingClientRect();
        if (t.clientY < rect.top + 80) start(t.clientY);
      }, { passive: true });
      el.addEventListener('touchmove', (e) => { if (!isMobile()) return; const t = e.touches[0]; move(t.clientY); }, { passive: true });
      el.addEventListener('touchend', end);
    })();

    $('#customSearchVentas').on('input keyup change search', function(){ applySearch(this.value); });
    $('#btnClearSearchVentas').on('click', function(){
      lastSearchRaw = ''; applySearch('');
      const input = document.getElementById('customSearchVentas');
      input.value = ''; input.focus();
    });

    /* ======================================
       MODAL OTP 6 DÍGITOS
       ====================================== */
    const PIN_CORRECTO = '{{ env("APROBACION_PIN") }}';

    const bankMask  = document.getElementById('bankMask');
    const bankModal = document.getElementById('bankModal');
    const bankClose = document.getElementById('bankClose');
    const bankTitle = document.getElementById('bankModalTitle');
    const otpInputs = Array.from(document.querySelectorAll('.otp'));
    const bankCard  = document.querySelector('.bank-card');
    const loading   = document.getElementById('otpLoading');
    const bankNote  = document.getElementById('bankNote');

    let activeForm     = null;
    let pendingEditUrl = null;
    let submitting     = false;

    function onlyDigits(s){ return (s || '').toString().replace(/\D+/g, ''); }
    function getOTP(){ return otpInputs.map(i => i.value || '').join(''); }

    function setHidden(pin){
      if (!activeForm) return;
      const hidden = activeForm.querySelector('.js-pin-hidden');
      if (hidden) hidden.value = pin;
    }

    function clearOTP(){
      otpInputs.forEach(i => {
        i.value = '';
        i.classList.remove('error', 'success');
        i.disabled = false;
      });
      submitting = false;
      if (loading) loading.style.display = 'none';
      if (bankNote) bankNote.innerHTML = 'Puedes <b>pegar</b> el PIN completo.';
    }

    function focusFirst(){ setTimeout(() => otpInputs[0]?.focus(), 60); }

    function openModal(form, editUrl){
      activeForm     = form    || null;
      pendingEditUrl = editUrl || null;
      clearOTP();
      if (bankTitle){
        bankTitle.textContent = editUrl ? 'Autorización de edición' : 'Confirmar eliminación';
      }
      bankMask?.classList.add('open');
      bankModal?.classList.add('open');
      focusFirst();
    }

    function closeModal(){
      bankMask?.classList.remove('open');
      bankModal?.classList.remove('open');
      activeForm     = null;
      pendingEditUrl = null;
      clearOTP();
    }

    function shake(){
      if (!bankCard) return;
      bankCard.classList.remove('shake');
      void bankCard.offsetWidth;
      bankCard.classList.add('shake');
    }

    function flashError(){
      otpInputs.forEach(i => { i.classList.add('error'); i.classList.remove('success'); });
      if (bankNote) bankNote.innerHTML = '<span style="color:#dc2626;font-weight:700;"><i class="fa-solid fa-xmark" style="margin-right:4px;"></i>PIN incorrecto. Intenta de nuevo.</span>';
      shake();
      setTimeout(() => {
        otpInputs.forEach(i => { i.value = ''; i.classList.remove('error'); i.disabled = false; });
        if (bankNote) bankNote.innerHTML = 'Puedes <b>pegar</b> el PIN completo.';
        submitting = false;
        if (loading) loading.style.display = 'none';
        focusFirst();
      }, 1300);
    }

    function autoSubmitIfReady(){
      if (submitting) return;
      const pin = getOTP();
      if (pin.length < 6 || otpInputs.some(i => !i.value)) return;

      if (pin !== String(PIN_CORRECTO)){
        submitting = true;
        flashError();
        return;
      }

      /* ── PIN CORRECTO ── */
      submitting = true;
      otpInputs.forEach(i => { i.disabled = true; i.classList.add('success'); i.classList.remove('error'); });
      if (bankNote) bankNote.innerHTML = '<span style="color:#15803d;font-weight:700;"><i class="fa-solid fa-check" style="margin-right:4px;"></i>Autorizado</span>';
      if (loading) loading.style.display = 'flex';

      setTimeout(() => {
        /* SI ES EDICIÓN */
        if (pendingEditUrl){
          const url = pendingEditUrl;
          closeModal();
          window.location.href = url;
          return;
        }
        /* SI ES ELIMINACIÓN */
        if (activeForm){
          setHidden(pin);
          const form = activeForm;
          activeForm = null;
          pendingEditUrl = null;
          bankMask?.classList.remove('open');
          bankModal?.classList.remove('open');
          clearOTP();
          form.submit();
        }
      }, 450);
    }

    $(document).on('click', '.js-open-delete', function(){
      const form = this.closest('form');
      if (!form) return;
      openModal(form, null);
    });

    $(document).on('click', '.js-open-edit', function(){
      const editUrl = this.getAttribute('data-edit-url');
      if (!editUrl) return;
      openModal(null, editUrl);
    });

    bankMask?.addEventListener('click', closeModal);
    bankClose?.addEventListener('click', closeModal);
    document.addEventListener('keydown', function(e){
      if (e.key === 'Escape') closeModal();
    });

    otpInputs.forEach((input, idx) => {
      input.addEventListener('input', () => {
        const v = onlyDigits(input.value).slice(0, 1);
        input.value = v;
        if (v && otpInputs[idx + 1]) otpInputs[idx + 1].focus();
        autoSubmitIfReady();
      });

      input.addEventListener('keydown', (e) => {
        if (e.key === 'Backspace' && !input.value && otpInputs[idx - 1]){
          otpInputs[idx - 1].focus();
          otpInputs[idx - 1].value = '';
        }
        if (e.key === 'Enter'){
          e.preventDefault();
          if (getOTP().length !== 6 || otpInputs.some(i => !i.value)){
            shake(); focusFirst();
          }
        }
        if (e.key === 'Escape') closeModal();
      });

      input.addEventListener('paste', (e) => {
        e.preventDefault();
        const paste = onlyDigits((e.clipboardData || window.clipboardData).getData('text')).slice(0, 6);
        if (!paste) return;
        clearOTP();
        paste.split('').forEach((ch, i) => { if (otpInputs[i]) otpInputs[i].value = ch; });
        otpInputs[Math.min(paste.length, 6) - 1]?.focus();
        autoSubmitIfReady();
      });
    });

    /* ── Auto-ocultar alerts después de 4 segundos ── */
    setTimeout(() => {
      document.querySelectorAll('.alert-flotante').forEach(el => {
        el.style.opacity = '0';
        setTimeout(() => el.remove(), 500);
      });
    }, 4000);

  });
</script>

{{-- ===== HERO ===== --}}
<div class="hero blur-on-sheet fade-slide d-flex align-items-center justify-content-between flex-wrap" style="margin-top:20px;">
  <div class="hero-main d-flex align-items-center gap-3">
    <div class="d-flex gap-2">
      <a href="javascript:void(0);" onclick="window.history.back();" class="icon-btn border shadow-sm" title="Volver atrás">
        <i class="fa-solid fa-arrow-left" style="color: var(--pblue-700)"></i>
      </a>
      <a href="{{ url('/home') }}" class="icon-btn border shadow-sm" title="Ir al Inicio">
        <i class="fa-solid fa-house" style="color: var(--muted)"></i>
      </a>
    </div>

    <div class="rounded-circle d-inline-flex align-items-center justify-content-center bg-white border"
         style="width:44px;height:44px;border-color:#dce7ff">
      <i class="fa-solid fa-receipt" style="font-size:1.1rem;color:var(--pblue-700)"></i>
    </div>
    <div>
      <h1 class="h4 mb-0 fw-bold">Historial de Remisiones</h1>
      <div class="small" style="color:var(--muted)">Consulta, exporta y gestiona tus remisiones en tiempo real.</div>
    </div>
  </div>

  <div class="d-flex align-items-center gap-3 hero-actions flex-wrap w-100 justify-content-end">
    <div class="desktop-search">
      <i class="fa-solid fa-magnifying-glass" style="opacity:.7"></i>
      <input id="desktopSearchVentas" type="text" placeholder="Buscar…">
    </div>
    <div class="desktop-filter">
      <select id="filtroAgente" style="width:180px;border-radius:12px;">
        <option value="">Todos los asesores</option>
      </select>
    </div>
    <div class="desktop-filter">
      <select id="filtroPlan">
        <option value="">Todos los planes</option>
      </select>
    </div>
    <a href="{{ url('ventas/crear') }}" class="btn btn-pastel">
      <i class="fa-solid fa-plus"></i> Nueva Venta
    </a>
  </div>
</div>

{{-- ===== ALERTS (fuera de la tabla, se auto-ocultan) ===== --}}
@if(session('success'))
  <div class="alert-flotante alert-success blur-on-sheet">
    <i class="fa-solid fa-circle-check"></i>
    {{ session('success') }}
  </div>
@endif

@if(session('error'))
  <div class="alert-flotante alert-danger blur-on-sheet">
    <i class="fa-solid fa-circle-xmark"></i>
    {{ session('error') }}
  </div>
@endif

@if($errors->any())
  <div class="alert-flotante alert-danger blur-on-sheet">
    <i class="fa-solid fa-circle-xmark"></i>
    <ul class="mb-0 ps-2">
      @foreach($errors->all() as $e)
        <li>{{ $e }}</li>
      @endforeach
    </ul>
  </div>
@endif

<button type="button" class="fab-search js-open-search" aria-label="Buscar">
  <i class="fa-solid fa-magnifying-glass"></i>
</button>

<div class="sheet" role="dialog" aria-modal="true" aria-label="Búsqueda de remisiones">
  <div class="handle"><span></span></div>
  <div class="body">
    <input id="customSearchVentas" type="search" inputmode="search" enterkeyhint="search"
           class="search-input" placeholder="Escribe para filtrar…">
    <div class="actions">
      <button type="button" class="btn-soft btn-soft-gray" id="btnClearSearchVentas">
        <i class="fa-solid fa-eraser"></i> <span>Limpiar</span>
      </button>
      <button type="button" class="btn-soft btn-soft-blue js-close-sheet">
        <i class="fa-solid fa-check"></i> <span>Aplicar</span>
      </button>
    </div>
  </div>
</div>
<div class="sheet-mask"></div>

{{-- ===== TABLA ===== --}}
<div id="remiContainer" class="container blur-on-sheet">
  <div class="table-responsive">
    <table id="ventasTable" class="table w-100">
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
        @forelse($ventas as $venta)
          <tr>
            <td>{{ $venta->id }}</td>
            <td>{{ isset($venta->cliente) ? mb_strtoupper(($venta->cliente->nombre ?? '').' '.($venta->cliente->apellido ?? ''), 'UTF-8') : 'N/A' }}</td>
            <td>{{ isset($venta->usuario) ? mb_strtoupper($venta->usuario->name, 'UTF-8') : 'N/A' }}</td>
            <td>${{ number_format($venta->total, 2) }}</td>
            <td data-order="{{ $venta->created_at->format('Y-m-d H:i:s') }}">
              {{ $venta->created_at->format('d/m/Y H:i') }}
            </td>
            <td>{{ ucfirst($venta->plan) }}</td>
            <td class="text-nowrap">

              {{-- Ver --}}
              <a href="{{ url('ventas/'.$venta->id) }}" class="icon-btn" title="Ver">
                <i class="fa-solid fa-eye"></i>
              </a>

              {{-- Editar con PIN --}}
              <button type="button"
                class="icon-btn js-open-edit"
                data-edit-url="{{ url('ventas/'.$venta->id.'/edit') }}"
                title="Editar">
                <i class="fa-solid fa-pen-to-square"></i>
              </button>

              {{-- Descargar PDF --}}
              <a href="{{ url('ventas/'.$venta->id.'/pdf') }}" class="icon-btn" title="Descargar PDF">
                <i class="bi bi-file-earmark-pdf-fill"></i>
              </a>

              {{-- Eliminar con PIN --}}
              <form action="{{ route('ventas.destroy', $venta) }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <input type="hidden" name="aprobacion_pin" class="js-pin-hidden" value="">
                <button type="button" class="icon-btn text-danger js-open-delete" title="Eliminar">
                  <i class="fa-solid fa-trash"></i>
                </button>
              </form>

            </td>
          </tr>
        @empty
          <tr><td colspan="7" class="text-center">No hay ventas registradas.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

{{-- ===== MODAL OTP ===== --}}
<div id="bankMask" class="bank-mask" aria-hidden="true"></div>

<div id="bankModal" class="bank-modal" role="dialog" aria-modal="true" aria-label="Aprobación por PIN">
  <div class="bank-card">
    <div class="bank-top">
      <div class="bank-brand">
        <div class="bank-badge"><i class="fa-solid fa-shield-halved"></i></div>
        <div>
          <div id="bankModalTitle" class="bank-title">Confirmación segura</div>
          <div class="bank-sub">Escribe el PIN de 6 dígitos</div>
        </div>
      </div>
      <button type="button" id="bankClose" class="bank-close" aria-label="Cerrar">
        <i class="fa-solid fa-xmark"></i>
      </button>
    </div>

    <div class="bank-body">
      <div class="bank-alert">
        <div class="dot"></div>
        <div>Al completar los <b>6 dígitos</b>, se confirma automáticamente.</div>
      </div>

      <div class="otp-row" aria-label="PIN de 6 dígitos">
        <input class="otp" inputmode="numeric" autocomplete="one-time-code" maxlength="1" aria-label="Dígito 1">
        <input class="otp" inputmode="numeric" maxlength="1" aria-label="Dígito 2">
        <input class="otp" inputmode="numeric" maxlength="1" aria-label="Dígito 3">
        <input class="otp" inputmode="numeric" maxlength="1" aria-label="Dígito 4">
        <input class="otp" inputmode="numeric" maxlength="1" aria-label="Dígito 5">
        <input class="otp" inputmode="numeric" maxlength="1" aria-label="Dígito 6">
      </div>

      <div class="bank-note" id="bankNote">
        Puedes <b>pegar</b> el PIN completo.
      </div>

      <div id="otpLoading" class="loading-dots" aria-hidden="true">
        <span></span><span></span><span></span>
      </div>
    </div>
  </div>
</div>

@endsection