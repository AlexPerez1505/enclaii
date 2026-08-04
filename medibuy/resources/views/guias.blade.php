@extends('layouts.app')
@section('title', 'Guías')
@section('titulo', 'Guías')

@section('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
@endsection

@section('content')
<style>
  /* ══════════════════════════════════════════════════════════
   VARIABLES LOCALES
══════════════════════════════════════════════════════════ */
  .g-wrap {
    /* Modo claro — heredado de cotz */
    --g-bg: #f8fafc;
--g-panel: #ffffff;
--g-panel-2: #f8fafc;
--g-text: #0f172a;
--g-muted: #64748b;
--g-border: #e2e8f0;
--g-input-bg: #ffffff;
--g-input-brd: #d1d5db;
    --g-shadow:       0 4px 6px -1px rgba(0,0,0,.05), 0 2px 4px -1px rgba(0,0,0,.03);
    --g-radius:       16px;

    /* Acento esmeralda */
    --g-mint:         #10b981;
    --g-mint-dark:    #059669;
    --g-mint-glow:    rgba(16,185,129,.18);
    --g-mint-bg:      rgba(16,185,129,.08);
    --g-mint-brd:     rgba(16,185,129,.30);
    --g-mint-txt:     #047857;

    /* Peligro */
    --g-danger:       #ef4444;
    --g-danger-bg:    #fef2f2;
    --g-danger-txt:   #991b1b;
  }


  /* ── Reset ─────────────────────────────────────────────── */
  *, *::before, *::after { box-sizing: border-box; }

  /* ── Wrapper ────────────────────────────────────────────── */
  .g-wrap {
    max-width: 1200px;
    margin: 36px auto;
    padding: 0 24px;
    background: transparent;
    color: var(--g-text);
    transition: color .25s ease;
  }

  /* ── Encabezado ─────────────────────────────────────────── */
  .g-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 16px;
    margin-bottom: 28px;
    padding-bottom: 20px;
    border-bottom: 1px solid var(--g-border);
    flex-wrap: wrap;
    transition: border-color .25s ease;
  }
  .g-header h2 {
    margin: 0;
    font-size: 1.9rem; font-weight: 800;
    color: var(--g-text);
    letter-spacing: -.5px;
    transition: color .25s ease;
  }
  .g-header p {
    margin: 6px 0 0;
    color: var(--g-muted);
    font-size: .93rem;
    transition: color .25s ease;
  }
  .g-header-actions { display: flex; align-items: center; gap: 10px; }
  .g-btn-round {
    display: inline-flex; align-items: center; justify-content: center;
    width: 42px; height: 42px;
    border-radius: 50%;
    border: 1px solid var(--g-border);
    background: var(--g-panel);
    color: var(--g-text);
    text-decoration: none; font-size: 1.2rem;
    transition: all .2s ease;
  }
  .g-btn-round:hover {
    border-color: var(--g-mint);
    color: var(--g-mint);
    transform: translateY(-2px);
    background: var(--g-mint-bg);
  }

  /* ── Grid de paneles ────────────────────────────────────── */
  .g-grid { display: grid; gap: 24px; grid-template-columns: 1fr; }
  @media (min-width: 980px) { .g-grid { grid-template-columns: 1fr 1.3fr; } }

  /* ── Panel ──────────────────────────────────────────────── */
  .g-panel {
    background: var(--g-panel);
    border-radius: var(--g-radius);
    border: 1px solid var(--g-border);
    box-shadow: var(--g-shadow);
    overflow: hidden;
    transition: background .25s ease, border-color .25s ease, box-shadow .25s ease;
  }
  @media (min-width: 980px) { .g-panel--sticky { align-self: start; position: sticky; top: 72px; } }

  .g-panel-head {
    padding: 18px 22px;
    border-bottom: 1px solid var(--g-border);
    display: flex; justify-content: space-between; align-items: center; gap: 14px;
    background: var(--g-panel-2);
    flex-wrap: wrap;
    transition: background .25s ease, border-color .25s ease;
  }
  .g-panel-head h3 {
    margin: 0; font-size: 1.1rem; font-weight: 700;
    color: var(--g-text);
    display: flex; align-items: center; gap: 10px;
    transition: color .25s ease;
  }
  .g-section { padding: 22px; }

  /* ── Badge ──────────────────────────────────────────────── */
  .g-badge {
    background: var(--g-mint-bg);
    color: var(--g-mint-txt);
    border: 1px solid var(--g-mint-brd);
    padding: 4px 12px; border-radius: 8px;
    font-weight: 700; font-size: .78rem;
    transition: background .25s ease, color .25s ease, border-color .25s ease;
  }

  /* ── Inputs flotantes ───────────────────────────────────── */
  .g-field {
    position: relative;
    background: var(--g-input-bg);
    border: 1px solid var(--g-input-brd);
    border-radius: 12px;
    padding: 22px 16px 6px;
    transition: border-color .25s ease, box-shadow .25s ease, background .25s ease;
  }
  .g-field:focus-within {
    border-color: var(--g-mint);
    box-shadow: 0 0 0 3px var(--g-mint-glow);
    background: var(--g-panel);
  }
  .g-field input {
    width: 100%; border: 0; outline: 0;
    background: transparent;
    font-size: 15px; font-family: inherit;
    color: var(--g-text);
    padding: 2px 0 0;
    transition: color .25s ease;
  }
  .g-field input::placeholder { color: transparent; }
  .g-field label {
    position: absolute; left: 16px; top: 16px;
    color: var(--g-muted); font-size: 14px;
    pointer-events: none;
    transition: top .2s ease, font-size .2s ease, color .2s ease;
  }
  .g-field input:focus + label,
  .g-field input:not(:placeholder-shown) + label {
    top: 6px; font-size: 11px;
    color: var(--g-mint); font-weight: 600;
  }
  .g-field.is-invalid { border-color: var(--g-danger) !important; background: var(--g-danger-bg) !important; }
  .g-error { color: var(--g-danger); font-size: 13px; margin-top: 6px; font-weight: 600; }

  /* ── Grid de inputs ─────────────────────────────────────── */
  .g-inputs { display: grid; gap: 18px; }

  /* ── Botones ────────────────────────────────────────────── */
  .g-btn {
    border: 0; border-radius: 12px;
    padding: 13px 20px;
    font-weight: 700; font-size: .93rem; font-family: inherit;
    cursor: pointer;
    display: inline-flex; align-items: center; justify-content: center; gap: 8px;
    transition: all .2s ease;
    text-decoration: none;
  }
  .g-btn:active { transform: scale(.97); }
  .g-btn-primary {
    background: linear-gradient(135deg, var(--g-mint), var(--g-mint-dark));
    color: #fff;
    box-shadow: 0 4px 14px var(--g-mint-glow);
  }
  .g-btn-primary:hover { filter: brightness(1.06); box-shadow: 0 6px 20px rgba(16,185,129,.35); }
  .g-btn-ghost {
    background: var(--g-panel-2);
    color: var(--g-text);
    border: 1px solid var(--g-border);
    transition: background .15s ease, border-color .25s ease, color .25s ease;
  }
  .g-btn-ghost:hover { background: var(--g-border); }
  .g-btn-outline {
    background: var(--g-panel);
    color: var(--g-mint-txt);
    border: 1px solid var(--g-mint-brd);
    padding: 9px 15px; font-size: .86rem;
    transition: background .15s ease, border-color .25s ease, color .25s ease;
  }
  .g-btn-outline:hover { background: var(--g-mint-bg); border-color: var(--g-mint); }
  .g-form-actions { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-top: 8px; }

  /* ── Buscador ───────────────────────────────────────────── */
  .g-toolbar { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; margin-left: auto; }
  .g-search { position: relative; flex: 1 1 220px; min-width: 220px; }
  .g-search input {
    width: 100%;
    background: var(--g-input-bg);
    border: 1px solid var(--g-input-brd);
    color: var(--g-text);
    border-radius: 12px;
    padding: 11px 40px 11px 40px;
    font-size: .93rem; font-family: inherit;
    transition: border-color .25s ease, box-shadow .25s ease, background .25s ease, color .25s ease;
  }
  .g-search input:focus {
    outline: none;
    border-color: var(--g-mint);
    box-shadow: 0 0 0 3px var(--g-mint-glow);
    background: var(--g-panel);
  }
  .g-search input::placeholder { color: var(--g-muted); }
  .g-search .g-ico-l {
    position: absolute; left: 13px; top: 50%; transform: translateY(-50%);
    color: var(--g-muted); pointer-events: none;
    transition: color .25s ease;
  }
  .g-search .g-ico-r {
    position: absolute; right: 13px; top: 50%; transform: translateY(-50%);
    color: var(--g-muted); cursor: pointer;
    transition: color .25s ease;
  }

  /* ── Lista de guías ─────────────────────────────────────── */
  .g-list {
    display: grid; gap: 10px;
    max-height: 500px; overflow-y: auto;
    padding-right: 4px;
  }
  .g-list::-webkit-scrollbar       { width: 6px; }
  .g-list::-webkit-scrollbar-track { background: var(--g-panel); }
  .g-list::-webkit-scrollbar-thumb { background: var(--g-border); border-radius: 999px; }

  .g-item {
    display: flex; align-items: center;
    justify-content: space-between; gap: 14px;
    background: var(--g-panel-2);
    border: 1px solid var(--g-border);
    border-radius: 12px; padding: 14px 18px;
    transition: all .2s ease;
  }
  .g-item:hover {
    border-color: var(--g-mint-brd);
    background: var(--g-panel);
    transform: scale(1.005);
    box-shadow: 0 4px 12px rgba(0,0,0,.06);
  }
  .g-item-id {
    font-weight: 700; font-size: 1.05rem;
    font-family: monospace; color: var(--g-text);
    transition: color .25s ease;
  }
  .g-item-meta {
    color: var(--g-muted); font-size: .88rem; margin-top: 3px;
    transition: color .25s ease;
  }
  .g-item-meta b { color: var(--g-mint); }

  /* ── Pills resumen ──────────────────────────────────────── */
  .g-pills { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 16px; }
  .g-pill {
    background: var(--g-panel);
    border: 1px solid var(--g-border);
    color: var(--g-mint-txt);
    padding: 5px 12px; border-radius: 8px;
    font-weight: 600; font-size: .78rem;
    transition: background .25s ease, border-color .25s ease, color .25s ease;
  }

  /* ── Skeleton ───────────────────────────────────────────── */
  .g-skeleton { display: grid; gap: 10px; }
  .g-shimmer {
    height: 62px; border-radius: 12px;
    background: linear-gradient(90deg, var(--g-panel-2) 25%, var(--g-border) 37%, var(--g-panel-2) 63%);
    background-size: 400% 100%;
    animation: g-shimmer 1.4s infinite linear;
    border: 1px solid var(--g-border);
  }
  @keyframes g-shimmer {
    0%   { background-position:  200% 0; }
    100% { background-position: -200% 0; }
  }

  /* ── Toast propio ───────────────────────────────────────── */
  .g-toast {
    position: fixed; top: 72px; right: 24px; z-index: 1060;
    padding: 13px 22px; border-radius: 12px;
    font-weight: 700; font-size: .9rem;
    box-shadow: 0 20px 25px rgba(0,0,0,.12);
    display: none; border: 1px solid transparent;
    font-family: inherit;
    transition: background .25s ease, color .25s ease, border-color .25s ease;
  }

  /* ── Responsivo ─────────────────────────────────────────── */
  @media (max-width: 720px) {
    .g-wrap          { padding: 0 14px; margin: 16px auto; }
    .g-header        { flex-direction: column; align-items: flex-start; }
    .g-header-actions{ width: 100%; }
    .g-panel-head    { flex-direction: column; align-items: flex-start; gap: 12px; padding: 18px; }
    .g-toolbar       { width: 100%; }
    .g-search        { flex: 1 1 100%; min-width: 100%; }
    .g-form-actions  { grid-template-columns: 1fr; }
  }
</style>

<div class="g-wrap">

  {{-- ── Encabezado ── --}}
  <div class="g-header">
    <div>
      <h2>Guías y Entregas</h2>
      <p>Crea guías y entrégalas en tiempo real.</p>
    </div>
    <div class="g-header-actions">
      <a href="javascript:void(0);" onclick="window.history.back()" class="g-btn-round" title="Volver">
        <i class="bi bi-arrow-left"></i>
      </a>
      <a href="{{ url('/home') }}" class="g-btn-round" title="Inicio">
        <i class="bi bi-house-fill"></i>
      </a>
    </div>
  </div>

  {{-- ── Grid ── --}}
  <div class="g-grid">

    {{-- Panel captura --}}
    <div class="g-panel">
      <div class="g-panel-head"><h3>Nueva Guía</h3></div>
      <div class="g-section">
        <div class="g-inputs">

          <div>
            <div class="g-field @error('numero_rastreo') is-invalid @enderror">
              <input type="text" id="f-rastreo" placeholder=" " inputmode="numeric" autocomplete="off">
              <label for="f-rastreo">Número de rastreo</label>
            </div>
            @error('numero_rastreo')<div class="g-error">{{ $message }}</div>@enderror
          </div>

          <div>
            <div class="g-field @error('peso') is-invalid @enderror">
              <input type="number" step="0.01" id="f-peso" placeholder=" ">
              <label for="f-peso">Peso (kg)</label>
            </div>
            @error('peso')<div class="g-error">{{ $message }}</div>@enderror
          </div>

          <div>
            <div class="g-field @error('fecha_recepcion') is-invalid @enderror">
              <input type="date" id="f-fecha" placeholder=" ">
              <label for="f-fecha">Fecha de recepción</label>
            </div>
            @error('fecha_recepcion')<div class="g-error">{{ $message }}</div>@enderror
          </div>

          <div class="g-form-actions">
            <button class="g-btn g-btn-primary" id="btnCrearGuia">
              <span id="spinCrear" style="display:none;width:14px;height:14px;border:2px solid rgba(255,255,255,.4);border-top-color:#fff;border-radius:50%;animation:g-spin .7s linear infinite;"></span>
              Registrar
            </button>
            <button class="g-btn g-btn-ghost" type="button" id="btnReset">Limpiar</button>
          </div>

        </div>
      </div>
    </div>

    {{-- Panel monitoreo --}}
    <div class="g-panel g-panel--sticky">
      <div class="g-panel-head">
        <h3>
          Guías sin entregar
          <span id="totalBadge" class="g-badge">0 pendientes</span>
        </h3>
        <div class="g-toolbar">
          <div class="g-search">
            <i class="bi bi-search g-ico-l" style="font-size:15px;"></i>
            <input id="searchTxt" placeholder="Filtrar guías…">
            <i class="bi bi-x g-ico-r" id="clearSearch" style="display:none;font-size:18px;"></i>
          </div>
          <button class="g-btn g-btn-ghost" id="btnRefresh" style="padding:11px 16px;">
            <i class="bi bi-arrow-clockwise"></i>
          </button>
        </div>
      </div>

      <div class="g-section">
        <div id="resumeKilos" class="g-pills"></div>

        <div id="skeleton" class="g-skeleton" style="display:none">
          <div class="g-shimmer"></div>
          <div class="g-shimmer"></div>
          <div class="g-shimmer"></div>
        </div>

        <div class="g-list" id="listGuias"></div>
        <div id="emptyState" class="g-item-meta"
             style="text-align:center;display:none;padding:24px 0;">
          Cero guías pendientes.
        </div>

        <div style="display:flex;justify-content:center;margin-top:18px;">
          <button id="btnMore" class="g-btn g-btn-outline" style="width:100%;max-width:200px;">
            Ver más registros
          </button>
        </div>
      </div>
    </div>

  </div>{{-- /g-grid --}}
</div>{{-- /g-wrap --}}

{{-- Toast --}}
<div id="g-toast" class="g-toast"></div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
@keyframes g-spin{to{transform:rotate(360deg)}}
</script>
<style>@keyframes g-spin{to{transform:rotate(360deg)}}</style>
<script>
(function () {
  /* ── Rutas ───────────────────────────────────────────── */
  const routes = {
    crearGuia:    '{{ route('guias.store') }}',
    search:       '{{ route('guias.search') }}',
    resumen:      '{{ route('guias.resumen') }}'
  };
  const entregaBase = '{{ route('entrega.create') }}';
  const csrf = '{{ csrf_token() }}';
function makeSwal() {
  return Swal.mixin({
    background: '#ffffff',
    color: '#0f172a',
    customClass: {
      confirmButton: 'g-btn g-btn-primary',
      cancelButton: 'g-btn g-btn-ghost'
    },
    buttonsStyling: false
  });
}

  /* ── Toast ───────────────────────────────────────────── */
  const toastEl = document.getElementById('g-toast');
  let toastTimer = null;
  function showToast(msg, ok = true) {
    toastEl.textContent = msg;
    toastEl.style.display      = 'block';
    toastEl.style.background   = ok ? 'var(--g-mint-bg)'   : 'var(--g-danger-bg)';
    toastEl.style.borderColor  = ok ? 'var(--g-mint-brd)'  : 'var(--g-danger)';
    toastEl.style.color        = ok ? 'var(--g-mint-txt)'  : 'var(--g-danger-txt)';
    clearTimeout(toastTimer);
    toastTimer = setTimeout(() => { toastEl.style.display = 'none'; }, 2600);
  }

  /* ── Helpers ─────────────────────────────────────────── */
  const MAX_DIGITS = 12;
  function formatDMY(s) {
    if (!s) return '—';
    const [y, m, d] = String(s).split('-');
    return (y && m && d) ? `${d.padStart(2,'0')}/${m.padStart(2,'0')}/${y}` : '—';
  }
  function prettyRastreo(n) { return String(n).replace(/(\d{4})(?=\d)/g, '$1 '); }
  function esc(s) {
    return String(s ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;')
      .replace(/>/g,'&gt;').replace(/"/g,'&quot;');
  }

  /* ── Máscara rastreo ─────────────────────────────────── */
  const rastreoEl = document.getElementById('f-rastreo');
  const maskR = v => v.replace(/\D/g,'').slice(0, MAX_DIGITS).replace(/(.{4})/g,'$1 ').trim();
  rastreoEl.maxLength = MAX_DIGITS + Math.floor((MAX_DIGITS - 1) / 4);
  rastreoEl.addEventListener('input',   e => { e.target.value = maskR(e.target.value); });
  rastreoEl.addEventListener('paste',   e => { e.preventDefault(); rastreoEl.value = maskR((e.clipboardData || window.clipboardData).getData('text')); });
  rastreoEl.addEventListener('keydown', e => {
    const sel = e.target.selectionEnd - e.target.selectionStart > 0;
    if (!sel && e.target.value.replace(/\D/g,'').length >= MAX_DIGITS && /[0-9]/.test(e.key)) e.preventDefault();
  });

  /* ── Skeleton ────────────────────────────────────────── */
  const skelEl  = document.getElementById('skeleton');
  const listEl  = document.getElementById('listGuias');
  const emptyEl = document.getElementById('emptyState');
  function setLoading(on) { skelEl.style.display = on ? 'grid' : 'none'; }

  /* ── Render guía ─────────────────────────────────────── */
  function renderItem(g) {
    const rec  = g.fecha_recepcion ? formatDMY(g.fecha_recepcion)
               : (g.created_at    ? formatDMY(String(g.created_at).slice(0,10)) : '—');
    const peso = (g.peso != null && g.peso !== '') ? Number(g.peso).toFixed(2) : '0.00';
    const el   = document.createElement('div');
    el.className = 'g-item';
    el.innerHTML = `
      <div>
        <div class="g-item-id">${prettyRastreo(esc(g.numero_rastreo))}</div>
        <div class="g-item-meta">Masa: <b>${esc(peso)} kg</b> · Registro: ${esc(rec)}</div>
      </div>
      <a class="g-btn g-btn-outline"
         href="${esc(entregaBase)}?guia_id=${esc(g.id)}&rastreo=${encodeURIComponent(g.numero_rastreo)}&peso=${encodeURIComponent(g.peso ?? '')}">
        Entregar
      </a>`;
    return el;
  }

  /* ── Carga paginada ──────────────────────────────────── */
  let page = 1, hasNext = true, lastSearch = '';
  async function fetchGuias({ reset = false } = {}) {
    try {
      if (reset) { listEl.innerHTML = ''; page = 1; hasNext = true; emptyEl.style.display = 'none'; }
      if (!hasNext) return;
      setLoading(true);

      const url = new URL(routes.search, window.location.origin);
      url.searchParams.set('page', page);
      if (lastSearch.trim()) url.searchParams.set('search', lastSearch.trim());

      const res  = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
      const data = await res.json();

      if (reset && (!data.data || data.data.length === 0)) emptyEl.style.display = 'block';
      (data.data || []).forEach(g => listEl.appendChild(renderItem(g)));

      hasNext = !!data.next_page_url;
      if (hasNext) page++;
      document.getElementById('btnMore').style.display = hasNext ? 'inline-flex' : 'none';
    } catch (e) { console.error(e); showToast('Error al cargar guías', false); }
    finally { setLoading(false); }
  }

  /* ── Resumen kg ──────────────────────────────────────── */
  async function fetchResumen() {
    const cont = document.getElementById('resumeKilos');
    try {
      const res              = await fetch(routes.resumen, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
      const { total = 0, byWeight = [] } = await res.json();

      document.getElementById('totalBadge').textContent = `${total} pendientes`;

      if (!Array.isArray(byWeight) || byWeight.length === 0) { cont.style.display = 'none'; return; }
      cont.style.display = 'flex';
      cont.innerHTML = byWeight.map(({ kg, c }) => {
        const v = parseFloat(kg);
        return `<span class="g-pill">${c} de ${Number.isInteger(v) ? v : v.toFixed(2)} kg</span>`;
      }).join('');
    } catch (e) { console.error(e); cont.style.display = 'none'; }
  }

  /* ── Búsqueda ────────────────────────────────────────── */
  let debounce = null;
  const searchInput = document.getElementById('searchTxt');
  const clearBtn    = document.getElementById('clearSearch');
  searchInput.addEventListener('input', e => {
    clearBtn.style.display = e.target.value ? 'block' : 'none';
    clearTimeout(debounce);
    debounce = setTimeout(() => { lastSearch = e.target.value; fetchGuias({ reset: true }); }, 320);
  });
  clearBtn.addEventListener('click', () => {
    searchInput.value = ''; lastSearch = ''; clearBtn.style.display = 'none';
    fetchGuias({ reset: true });
  });

  /* ── Crear guía ──────────────────────────────────────── */
  const btnCrear  = document.getElementById('btnCrearGuia');
  const spinCrear = document.getElementById('spinCrear');
  const saving    = on => { btnCrear.disabled = on; spinCrear.style.display = on ? 'inline-block' : 'none'; };

  document.getElementById('btnReset').addEventListener('click', () => {
    rastreoEl.value = '';
    document.getElementById('f-peso').value  = '';
    document.getElementById('f-fecha').value = '';
  });

  btnCrear.addEventListener('click', async () => {
    const rawRastreo = rastreoEl.value.replace(/\s+/g, '');
    if (rawRastreo.length !== MAX_DIGITS) {
      makeSwal().fire({ icon: 'warning', title: 'Formato inválido', text: `Deben ser ${MAX_DIGITS} dígitos.` });
      return;
    }
    const fd = new FormData();
    fd.append('numero_rastreo', rawRastreo);
    fd.append('peso',            document.getElementById('f-peso').value);
    fd.append('fecha_recepcion', document.getElementById('f-fecha').value);
    try {
      saving(true);
      const res  = await fetch(routes.crearGuia, { method: 'POST', headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' }, body: fd });
      const data = await res.json();
      if (data.ok) {
        document.getElementById('btnReset').click();
        showToast(data.message || 'Guía registrada correctamente');
        fetchGuias({ reset: true });
        fetchResumen();
      } else {
        makeSwal().fire({ icon: 'warning', title: 'Atención', text: data.message || 'Parámetros incorrectos' });
      }
    } catch (e) {
      makeSwal().fire({ icon: 'error', title: 'Fallo en el servidor', text: 'No se completó el registro' });
    } finally { saving(false); }
  });

  /* ── Acciones de botones ─────────────────────────────── */
  document.getElementById('btnRefresh').addEventListener('click', () => { fetchGuias({ reset: true }); fetchResumen(); });
  document.getElementById('btnMore').addEventListener('click',    () => fetchGuias());

  /* ── Carga inicial ───────────────────────────────────── */
  fetchGuias({ reset: true });
  fetchResumen();

})();
</script>
@endsection