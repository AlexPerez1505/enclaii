@extends('layouts.app')
@section('title', 'Enviar promoción (promo_todo)')
@section('titulo', 'Enviar promoción')
@section('content')

<style>
  :root{
    --bg:#f7f9fb; --card:#fff; --line:#e6ebf1;
    --brand:#bfe3ff; --brand-ink:#0a0f1f;
    --ink:#0f172a; --muted:#64748b;
    --ok:#16a34a; --err:#dc2626;
  }
  .wrap{max-width:1200px;margin:24px auto;padding:0 16px;}
  .card{
    background:var(--card); border:1px solid var(--line); border-radius:18px;
    box-shadow:0 12px 32px rgba(2,8,23,.06); overflow:hidden;
  }
  .card-head{padding:18px 22px;border-bottom:1px solid var(--line);display:flex;align-items:center;gap:12px}
  .dot{width:10px;height:10px;border-radius:999px;background:var(--brand);animation:pulse 1.6s infinite}
  @keyframes pulse{0%{transform:scale(.9);opacity:.8}50%{transform:scale(1);opacity:1}100%{transform:scale(.9);opacity:.8}}
  h3{margin:0;font-size:20px;letter-spacing:-.3px;color:var(--ink)}
  .card-body{padding:22px}
  label{font-weight:600;color:var(--ink);font-size:14px}
  .muted{color:var(--muted);font-size:13px}
  .grid{display:grid;gap:16px}
  @media (min-width:1000px){
    .grid-2{grid-template-columns:1.1fr .9fr}
  }

  /* Hero desvanecido superior */
  .hero{
    display:flex; align-items:center; justify-content:space-between; gap:12px;
    padding:18px 22px; border-bottom:1px solid var(--line);
    background: radial-gradient(1200px 160px at 10% -20%, #e8f3ff 0%, transparent 60%),
                radial-gradient(1200px 160px at 90% -20%, #e6f5ff 0%, transparent 60%),
                linear-gradient(180deg, #ffffff 0%, #ffffff 100%);
  }
  .hero-title{display:flex;align-items:center;gap:12px}
  .hero .tag{background:#eff7ff;border:1px solid #dbeafe;color:#1e3a8a;
    padding:6px 10px;border-radius:10px;font-weight:700;font-size:12px;text-decoration:none}

  /* Inputs & botones */
  .inp, textarea, input[type=file], select{
    width:100%;border:1px solid var(--line);background:#fff;border-radius:12px;
    padding:12px 14px;font-size:14px;outline:none; transition:.15s border, .15s box-shadow;
  }
  .inp:focus, textarea:focus, select:focus{box-shadow:0 0 0 4px rgba(191,227,255,.35);border-color:#cfe9ff}
  .btn{
    display:inline-flex;align-items:center;gap:10px;border:0;border-radius:14px;
    padding:12px 18px;font-weight:700;cursor:pointer;background:var(--brand);color:var(--brand-ink);
    transition:transform .05s ease, box-shadow .15s ease;
  }
  .btn:hover{transform:translateY(-1px); box-shadow:0 6px 14px rgba(2,8,23,.08)}
  .btn-ghost{
    background:transparent;border:1px solid var(--line);color:var(--ink);
    border-radius:14px;padding:10px 14px;font-weight:600;
  }
  .btn-lite{
    background:#eff7ff;border:1px solid #dbeafe;color:#1e3a8a;border-radius:12px;padding:8px 12px;font-weight:600;font-size:13px;
  }
  .alert{margin:14px 0;padding:12px 14px;border-radius:12px;background:#f7fffb;border:1px solid #d1fae5;color:#065f46}

  /* Chips seleccionados */
  .chips{display:flex;gap:8px;flex-wrap:wrap;margin-top:10px}
  .chip{
    display:inline-flex;align-items:center;gap:8px;background:#eef6ff;border:1px solid #dbeafe;color:#1e3a8a;
    border-radius:999px;padding:6px 10px;font-size:12px;font-weight:700;animation:pop .15s ease;
  }
  .chip .x{cursor:pointer;opacity:.7}
  @keyframes pop{from{transform:scale(.95);opacity:.6}to{transform:scale(1);opacity:1}}

  /* Tabla */
  .table-wrap{overflow:auto;border:1px solid var(--line);border-radius:14px}
  table{width:100%;border-collapse:separate;border-spacing:0}
  th,td{padding:10px 12px;border-bottom:1px solid var(--line);font-size:14px}
  th{font-weight:700;color:var(--ink);text-align:left;background:#fbfcff}
  tr:hover td{background:#fafcff}
  tr.picked td{background:#f4f9ff}
  .rowcheck{transform:scale(1.1)}

  .badge{display:inline-block;padding:4px 8px;border-radius:999px;font-size:12px;font-weight:700}
  .badge.ok{background:#ecfdf5;color:#065f46}
  .badge.err{background:#fef2f2;color:#991b1b}

  /* Panel lateral derecho (card) */
  .side-card{border:1px solid var(--line); border-radius:18px; background:#fff; box-shadow:0 12px 32px rgba(2,8,23,.06);}
  .side-head{padding:16px 18px;border-bottom:1px solid var(--line);display:flex;justify-content:space-between;align-items:center}
  .side-body{padding:16px 18px}
  @media (min-width:1000px){ .side-sticky{position:sticky; top:18px} }

  /* Drawer flotante (opcional) */
  .drawer{
    position:fixed; right:16px; bottom:16px; width:clamp(280px, 36vw, 420px);
    background:var(--card); border:1px solid var(--line); border-radius:18px; box-shadow:0 20px 60px rgba(2,8,23,.16);
    transform:translateY(calc(100% + 16px)); transition:transform .25s ease; z-index:1200;
  }
  .drawer.open{ transform:translateY(0) }
  .drawer-head{display:flex;align-items:center;justify-content:space-between;padding:12px 14px;border-bottom:1px solid var(--line)}
  .drawer-body{padding:12px 14px; max-height:40vh; overflow:auto}
  .drawer-foot{padding:12px 14px;border-top:1px solid var(--line);display:flex;gap:8px;justify-content:flex-end}

  /* Sticky actions (debajo de la tabla) */
  .sticky-actions{
    position:sticky;bottom:-1px;background:linear-gradient(180deg, transparent, #fff 20%);
    padding-top:8px;margin-top:8px;display:flex;justify-content:space-between;gap:8px;flex-wrap:wrap;
  }

  /* Dropzone imagen */
  .dropzone{
    position:relative;border:2px dashed #dbeafe;border-radius:14px;background:linear-gradient(180deg,#fcfeff, #f7fbff);
    padding:14px; display:grid; grid-template-columns:120px 1fr; gap:14px; align-items:center;
    transition:border-color .15s ease, transform .15s ease, box-shadow .2s ease;
  }
  .dropzone.dragover{ border-color:#93c5fd; transform:scale(1.01); box-shadow:0 8px 28px rgba(2,8,23,.06) }
  .dz-thumb{
    width:120px;height:120px;border-radius:12px;border:1px solid var(--line);background:#f1f5f9;overflow:hidden;
    display:flex;align-items:center;justify-content:center; position:relative;
  }
  .dz-thumb img{width:100%;height:100%;object-fit:cover; animation:fadein .2s ease}
  .dz-info .title{font-weight:700;color:#0b1220}
  .dz-actions{display:flex;gap:8px;margin-top:8px}
  .dz-remove{background:#fff;border:1px solid #e5e7eb;border-radius:10px;padding:6px 10px;cursor:pointer}
  @keyframes fadein{from{opacity:.6;transform:scale(.98)}to{opacity:1;transform:scale(1)}}

  .preview-phrase{font-size:13px;color:var(--muted);margin-top:6px}
  .kbd{background:#eef2ff;border:1px solid #e5e7eb;padding:2px 6px;border-radius:6px;font-size:12px}

  /* Paginación Laravel fix (tailwind view sin Tailwind) */
  .pagination-fx nav[aria-label="Pagination Navigation"]{display:block;margin:6px 0;font-size:14px}
  .pagination-fx nav[aria-label="Pagination Navigation"] > div:first-child{display:none}
  .pagination-fx nav[aria-label="Pagination Navigation"] > div:last-child{display:flex;align-items:center;justify-content:space-between;gap:10px;flex-wrap:wrap}
  .pagination-fx nav a, .pagination-fx nav span{
    display:inline-flex;align-items:center;gap:6px;padding:6px 10px;border:1px solid #e6ebf1;border-radius:10px;
    text-decoration:none;color:#0f172a;background:#fff;margin:2px
  }
  .pagination-fx nav span[aria-current="page"]{background:#eff7ff;border-color:#dbeafe;font-weight:700}
  .pagination-fx nav a:hover{background:#fbfcff;box-shadow:0 2px 8px rgba(2,8,23,.06)}
  .pagination-fx nav svg{width:18px!important;height:18px!important;display:inline-block;vertical-align:middle}
</style>

<div class="wrap" style="margin-top:100px;">
  <div class="card">
    <div class="hero">
      <div class="hero-title">
        <span class="dot"></span>
        <h3 style="margin:0">Plantilla WhatsApp:</h3>
        <a class="tag">promo_todo (es_MX)</a>
      </div>
      <div class="muted" style="font-weight:600">MediBuy</div>
    </div>

    <div class="card-body grid grid-2">
      {{-- IZQUIERDA --}}
      <div class="grid">
        @if ($errors->any())
          <div class="alert"><strong>Corrige:</strong><ul style="margin:6px 0 0 16px;">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
        @endif
        @if (session('status'))
          <div class="alert">{{ session('status') }}</div>
        @endif

        {{-- Filtro rápido --}}
        <div>
          <label>Buscar (rápido)</label>
          <input class="inp" id="quickSearch" placeholder="Filtra por nombre o teléfono sin recargar">
          <div class="muted">Tip: <b>Ctrl/Cmd + K</b> para enfocar.</div>
        </div>

        {{-- FORM --}}
        <form method="POST" action="{{ route('promos.promo_todo.send') }}" enctype="multipart/form-data" id="sendForm" class="grid">
          @csrf

          {{-- Dropzone + frase --}}
          <div class="dropzone" id="dropzone">
            <div class="dz-thumb" id="dzThumb">
              <span class="muted" id="dzPlaceholder">IMG</span>
            </div>
            <div class="dz-info">
              <div class="title">Imagen de encabezado</div>
              <div class="muted">Arrastra una imagen aquí o <label for="headerInput" style="text-decoration:underline;cursor:pointer">búscala</label>.</div>
              <input type="file" name="header_image" id="headerInput" accept="image/png,image/jpeg" style="position:absolute;opacity:0;pointer-events:none;width:1px;height:1px" required>
              <div class="dz-actions">
                <button type="button" class="dz-remove" id="btnRemoveImg" style="display:none">Quitar imagen</button>
              </div>

              <div style="margin-top:10px">
                <label>Texto {{ '{' }}{2}{{ '}' }} (frase)</label>
                <input class="inp" name="frase" maxlength="500" placeholder="En promoción videocolonoscopio fujinon" required id="phrase">
                <div class="preview-phrase"><span id="fraseCounter">0</span>/500 • <span id="phrasePreview">Tu frase aparecerá aquí…</span></div>
              </div>
            </div>
          </div>

          {{-- Tabla --}}
          <div class="table-wrap">
            <table id="clientsTable">
              <thead>
                <tr>
                  <th style="width:42px"><input type="checkbox" id="checkAllPage"></th>
                  <th>Nombre</th>
                  <th>Teléfono</th>
                </tr>
              </thead>
              <tbody>
                @forelse ($clientes as $c)
                  @php $nombreCompleto = trim(($c->nombre ?? '').' '.($c->apellido ?? '')); @endphp
                  <tr data-name="{{ $nombreCompleto }}" data-phone="{{ $c->telefono ?? '' }}">
                    <td><input type="checkbox" value="{{ $c->id }}" class="rowcheck"></td>
                    <td>{{ $nombreCompleto ?: '—' }}</td>
                    <td>{{ $c->telefono ?? '—' }}</td>
                  </tr>
                @empty
                  <tr><td colspan="3" class="muted">Sin resultados</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>

          {{-- Acciones bajo tabla --}}
          <div class="sticky-actions">
            <div style="display:flex;gap:8px;flex-wrap:wrap">
              <button class="btn-lite" type="button" id="checkPage">Seleccionar visibles</button>
              <button class="btn-lite" type="button" id="uncheckPage">Quitar visibles</button>
              <button class="btn-lite" type="button" id="invertPage">Invertir visibles</button>
            </div>
            <div style="display:flex;gap:8px;align-items:center">
              <input type="hidden" name="mode" id="mode" value="selected">
              <button class="btn" type="submit" id="btnSelected">
                📣 Enviar a seleccionados (<span id="selCountBtn">0</span>)
              </button>
            </div>
          </div>

          {{-- Paginación --}}
          <div class="pagination-fx">{{ $clientes->links() }}</div>

          {{-- Chips inline --}}
          <div class="chips" id="chipsInline"></div>
        </form>

        {{-- Resultados de envío --}}
        @if (session('results'))
          <div style="margin-top:16px">
            <table>
              <thead><tr><th>Teléfono</th><th>Nombre</th><th>Estado</th><th>WAMID</th></tr></thead>
              <tbody>
              @foreach (session('results') as $r)
                <tr>
                  <td>{{ $r['to'] }}</td>
                  <td>{{ $r['nombre'] }}</td>
                  <td>@if ($r['ok']) <span class="badge ok">OK {{ $r['status'] }}</span> @else <span class="badge err">ERR {{ $r['status'] }}</span> @endif</td>
                  <td>{{ $r['wamid'] ?? '-' }}</td>
                </tr>
              @endforeach
              </tbody>
            </table>
          </div>
        @endif
      </div>

      {{-- DERECHA: panel lateral --}}
      <div class="side-card side-sticky">
        <div class="side-head">
          <div style="display:flex;align-items:center;gap:10px">
            <span class="dot"></span>
            <strong>Seleccionados</strong>
            <span class="badge ok" id="selCounterBadge">0</span>
          </div>
          <button class="btn-ghost" type="button" id="toggleDrawer">Panel flotante</button>
        </div>
        <div class="side-body">
          <div class="muted" style="margin-bottom:8px">Se enviará a quienes marques en la tabla.</div>
          <div class="chips" id="chipsSide"></div>

          <div style="margin-top:12px;display:flex;gap:8px;flex-wrap:wrap">
            <button class="btn-lite" type="button" id="sideCheckPage">Seleccionar visibles</button>
            <button class="btn-lite" type="button" id="sideUncheckPage">Quitar visibles</button>
            <button class="btn-lite" type="button" id="sideInvertPage">Invertir visibles</button>
            <button class="btn-ghost" type="button" id="clearAll">Limpiar</button>
            <button class="btn" type="button" id="sideSend">Enviar seleccionados</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- Drawer flotante (opcional) --}}
<div class="drawer" id="drawer">
  <div class="drawer-head">
    <strong>Seleccionados (<span id="selCounterDrawer">0</span>)</strong>
    <button class="btn-ghost" id="closeDrawer" type="button">Cerrar</button>
  </div>
  <div class="drawer-body">
    <div class="chips" id="chipsDrawer"></div>
  </div>
  <div class="drawer-foot">
    <button class="btn-ghost" id="drawerClear" type="button">Limpiar</button>
    <button class="btn" id="drawerSend" type="button">Enviar a seleccionados</button>
  </div>
</div>

<script>
(() => {
  /* ===== Selección persistente ===== */
  const STORAGE_KEY = 'promoSelectedIds_v3';
  let selected = new Map();
  function saveSel(){ localStorage.setItem(STORAGE_KEY, JSON.stringify(Array.from(selected.values()))); }
  function loadSel(){ try{ selected = new Map((JSON.parse(localStorage.getItem(STORAGE_KEY)||'[]')).map(o=>[String(o.id),o])); }catch{ selected = new Map(); } }

  /* DOM refs */
  const table = document.getElementById('clientsTable');
  const checkAll = document.getElementById('checkAllPage');
  const quickSearch = document.getElementById('quickSearch');

  const chipsInline = document.getElementById('chipsInline');
  const chipsSide   = document.getElementById('chipsSide');
  const chipsDrawer = document.getElementById('chipsDrawer');

  const selCountBtn = document.getElementById('selCountBtn');
  const selBadge    = document.getElementById('selCounterBadge');
  const selDrawer   = document.getElementById('selCounterDrawer');

  const btnSelected = document.getElementById('btnSelected');
  const sideSend    = document.getElementById('sideSend');
  const mode        = document.getElementById('mode');
  const form        = document.getElementById('sendForm');

  const checkPage   = document.getElementById('checkPage');
  const uncheckPage = document.getElementById('uncheckPage');
  const invertPage  = document.getElementById('invertPage');

  const sideCheckPage   = document.getElementById('sideCheckPage');
  const sideUncheckPage = document.getElementById('sideUncheckPage');
  const sideInvertPage  = document.getElementById('sideInvertPage');

  const drawer      = document.getElementById('drawer');
  const toggleDrawer= document.getElementById('toggleDrawer');
  const closeDrawer = document.getElementById('closeDrawer');
  const drawerClear = document.getElementById('drawerClear');
  const drawerSend  = document.getElementById('drawerSend');
  const clearAllBtn = document.getElementById('clearAll');

  /* Utilidades */
  function checks(){ return Array.from(table.querySelectorAll('.rowcheck')); }
  function rowInfo(tr){ return { id:String(tr.querySelector('.rowcheck').value), name:tr.getAttribute('data-name')||'Cliente', phone:tr.getAttribute('data-phone')||'' }; }
  function visibleRows(){
    const q = (quickSearch?.value || '').trim().toLowerCase();
    const rows = Array.from(table.querySelectorAll('tbody tr'));
    if(!q) return rows;
    return rows.filter(tr => {
      const txt = (tr.getAttribute('data-name') + ' ' + tr.getAttribute('data-phone')).toLowerCase();
      return txt.includes(q);
    });
  }
  function addSel(o){ selected.set(o.id, o); saveSel(); refresh(); }
  function delSel(id){ selected.delete(String(id)); saveSel(); refresh(); }
  function isSel(id){ return selected.has(String(id)); }

  function renderChips(container, maxItems){
    container.innerHTML = '';
    const arr = Array.from(selected.values());
    arr.slice(0, maxItems).forEach(o => {
      const chip = document.createElement('span');
      chip.className = 'chip';
      chip.innerHTML = `<span>👤 ${o.name}</span><span class="muted">• ${o.phone || 's/tel'}</span><span class="x" data-id="${o.id}">✕</span>`;
      chip.querySelector('.x').addEventListener('click', () => delSel(o.id));
      container.appendChild(chip);
    });
    if(arr.length > maxItems){
      const more = document.createElement('span');
      more.className = 'chip'; more.textContent = `+${arr.length - maxItems} más…`;
      container.appendChild(more);
    }
  }

  function refresh(){
    checks().forEach(cb => { cb.checked = isSel(cb.value); cb.closest('tr').classList.toggle('picked', cb.checked); });
    const n = selected.size;
    [selCountBtn, selBadge, selDrawer].forEach(el => el && (el.textContent = n));
    renderChips(chipsInline, 20); renderChips(chipsSide, 40); renderChips(chipsDrawer, 999);
    const vis = visibleRows(); checkAll.checked = vis.length && vis.every(tr => isSel(rowInfo(tr).id));
    if(n > 0 && !drawer.classList.contains('open')) drawer.classList.add('open');
  }

  /* Delegación: checks siempre funcionan */
  table.addEventListener('change', (e) => {
    const cb = e.target.closest('.rowcheck'); if(!cb) return;
    const info = rowInfo(cb.closest('tr')); cb.checked ? addSel(info) : delSel(info.id);
  });

  /* Masivas */
  function selectVis(){ visibleRows().forEach(tr => selected.set(rowInfo(tr).id, rowInfo(tr))); saveSel(); refresh(); }
  function unselectVis(){ visibleRows().forEach(tr => selected.delete(rowInfo(tr).id)); saveSel(); refresh(); }
  function invertVis(){ visibleRows().forEach(tr => { const id = rowInfo(tr).id; if(isSel(id)) selected.delete(id); else selected.set(id, rowInfo(tr)); }); saveSel(); refresh(); }

  checkAll.addEventListener('change', e => { e.target.checked ? selectVis() : unselectVis(); });
  checkPage.addEventListener('click', selectVis);
  uncheckPage.addEventListener('click', unselectVis);
  invertPage.addEventListener('click', invertVis);

  sideCheckPage.addEventListener('click', selectVis);
  sideUncheckPage.addEventListener('click', unselectVis);
  sideInvertPage.addEventListener('click', invertVis);

  /* Filtro rápido */
  quickSearch.addEventListener('input', () => {
    const q = quickSearch.value.trim().toLowerCase();
    const rows = Array.from(table.querySelectorAll('tbody tr'));
    rows.forEach(tr => {
      const txt = (tr.getAttribute('data-name') + ' ' + tr.getAttribute('data-phone')).toLowerCase();
      tr.style.display = !q || txt.includes(q) ? '' : 'none';
    });
    refresh();
  });
  window.addEventListener('keydown', (e)=>{ if((e.ctrlKey||e.metaKey)&&e.key.toLowerCase()==='k'){ e.preventDefault(); quickSearch.focus(); } });

  /* Envío: inyecta ids[] */
  function injectAndSubmit(){
    if(selected.size === 0){ alert('Selecciona al menos un cliente.'); return; }
    mode.value = 'selected';
    form.querySelectorAll('input[name="ids[]"]').forEach(el => el.remove());
    for(const {id} of selected.values()){
      const inp = document.createElement('input'); inp.type='hidden'; inp.name='ids[]'; inp.value=id; form.appendChild(inp);
    }
    form.submit();
  }
  btnSelected.addEventListener('click', (e)=>{ e.preventDefault(); injectAndSubmit(); });
  sideSend.addEventListener('click', injectAndSubmit);

  /* Drawer */
  toggleDrawer.addEventListener('click', ()=> drawer.classList.toggle('open'));
  closeDrawer .addEventListener('click', ()=> drawer.classList.remove('open'));
  drawerClear .addEventListener('click', ()=> { selected.clear(); saveSel(); refresh(); });
  drawerSend  .addEventListener('click', injectAndSubmit);
  clearAllBtn ?.addEventListener('click', ()=> { selected.clear(); saveSel(); refresh(); });

  /* Dropzone */
  const dz = document.getElementById('dropzone');
  const dzThumb = document.getElementById('dzThumb');
  const dzPlaceholder = document.getElementById('dzPlaceholder');
  const inputFile = document.getElementById('headerInput');
  const btnRemoveImg = document.getElementById('btnRemoveImg');
  function setThumb(file){
    const reader = new FileReader();
    reader.onload = e => { dzThumb.innerHTML = `<img src="${e.target.result}" alt="preview">`; btnRemoveImg.style.display='inline-block'; };
    reader.readAsDataURL(file);
  }
  function clearThumb(){ dzThumb.innerHTML = `<span class="muted" id="dzPlaceholder">IMG</span>`; inputFile.value=''; btnRemoveImg.style.display='none'; }
  dz.addEventListener('dragover', e => { e.preventDefault(); dz.classList.add('dragover'); });
  dz.addEventListener('dragleave', ()=> dz.classList.remove('dragover'));
  dz.addEventListener('drop', e => {
    e.preventDefault(); dz.classList.remove('dragover');
    const f = e.dataTransfer.files?.[0]; if(!f) return;
    if(!/image\/(png|jpeg)/.test(f.type)){ alert('Solo JPG o PNG'); return; }
    inputFile.files = e.dataTransfer.files; setThumb(f);
  });
  inputFile.addEventListener('change', ()=>{ const f = inputFile.files?.[0]; if(f) setThumb(f); });
  btnRemoveImg.addEventListener('click', clearThumb);

  /* Frase */
  const phrase = document.getElementById('phrase');
  const phrasePreview = document.getElementById('phrasePreview');
  const fraseCounter = document.getElementById('fraseCounter');
  phrase.addEventListener('input', ()=>{ fraseCounter.textContent = phrase.value.length; phrasePreview.textContent = phrase.value || 'Tu frase aparecerá aquí…'; });

  /* Init */
  loadSel(); quickSearch.dispatchEvent(new Event('input')); refresh();
})();
</script>
@endsection
