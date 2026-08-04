@extends('layouts.app')
@section('title', 'Guías')
@section('titulo', 'Entrega de Guía')

@section('content')
<style>
  :root{
    --mint:#48cfad; --mint-dark:#34c29e;
    --ink:#2a2e35; --muted:#7a7f87; --line:#e9ecef; --card:#ffffff;
    --ok:#10b981; --ok-100:#eafaf4;
    --warn:#ef4444; --warn-100:#fff1f2;
    --shadow-lg:0 16px 40px rgba(18,38,63,.12);
    --radius:18px;
  }
  *{box-sizing:border-box}
  body{font-family:"Open Sans",sans-serif;background:#eaebec}

  /* Shell */
  .wrap{ max-width:1100px; margin:28px auto; padding:0 16px; }
  .panel{ background:var(--card); border:1px solid var(--line); border-radius:var(--radius); box-shadow:var(--shadow-lg); overflow:hidden; }
  .panel-head{ padding:20px 22px; border-bottom:1px solid var(--line); display:flex; align-items:center; justify-content:space-between; gap:12px; }
  .panel-head .title{ margin:0; font-weight:800; color:var(--ink); }
  .panel-head .sub{ margin:4px 0 0; color:var(--muted); font-size:14px }
  .section{ padding:22px; }

  .actions{ display:flex; gap:10px; align-items:center; }
  .btn{ border:0; border-radius:12px; padding:12px 16px; font-weight:700; cursor:pointer; transition:transform .05s, background .2s, color .2s, box-shadow .2s; }
  .btn:active{ transform:translateY(1px) }
  .btn-primary{ background:var(--mint); color:#fff; box-shadow:0 12px 22px rgba(72,207,173,.26) }
  .btn-primary:hover{ background:var(--mint-dark) }
  .btn-ghost{ background:#fff; color:var(--ink); border:1px solid var(--line) }
  .btn-ghost:hover{ border-color:#dfe3e8 }
  .btn-ok{ background:var(--ok); color:#fff }
  .btn-danger{ background:var(--warn); color:#fff }
  .btn-back{ display:inline-flex; align-items:center; gap:8px; }
  .btn-back svg{ width:18px; height:18px; }

  /* Grid */
  .grid{ display:grid; gap:18px; grid-template-columns:1fr; }
  @media(min-width:900px){ .grid-2{ grid-template-columns:1fr 1fr; } .grid-3{ grid-template-columns:1fr 1fr 1fr; } }

  /* Field con label flotante + icono */
  .field{
    position:relative; background:#fff; border:1px solid var(--line);
    border-radius:12px; padding:16px 14px 10px; transition:box-shadow .2s, border-color .2s;
  }
  .field:focus-within{ border-color:#d8dee6; box-shadow:0 8px 24px rgba(18,38,63,.08) }
  .field input, .field textarea, .field select{
    width:100%; border:0; outline:0; background:transparent; font-size:15px; color:var(--ink); padding-top:10px;
  }
  .field input::placeholder{ color:transparent }
  .field label{
    position:absolute; left:14px; top:14px; color:var(--muted); font-size:13px; pointer-events:none;
    transition:transform .15s, color .15s, font-size .15s, top .15s;
  }
  .field input:focus + label,
  .field input:not(:placeholder-shown) + label,
  .field textarea:focus + label,
  .field textarea:not(:placeholder-shown) + label{
    top:8px; transform:translateY(-10px); font-size:11px; color:var(--mint-dark);
  }
  .field .icon{
    position:absolute; left:12px; top:50%; transform:translateY(-50%); width:18px; height:18px; color:#8aa0b3; opacity:.85;
  }
  .field.has-icon input{ padding-left:44px }

  /* Dropdown resultados */
  .dropdown{ position:relative }
  .dropdown-menu{
    position:relative; background:#fff; border:1px solid var(--line); border-radius:12px; box-shadow:var(--shadow-lg);
    margin-top:6px; list-style:none; padding:6px; max-height:240px; overflow:auto;
  }
  .dropdown-item{ border:none; background:transparent; width:100%; text-align:left; padding:10px; border-radius:10px; cursor:pointer; }
  .dropdown-item:hover, .dropdown-item.active{ background:#f3fffb; }

  /* Uploader */
  .image-wrap{ border:1px dashed #dfe3e8; border-radius:14px; padding:12px; background:#fafbfc; }
  .image-preview{ display:grid; place-items:center; border-radius:12px; min-height:170px; background:#fff; border:1px solid #edf0f3; cursor:pointer; }
  .image-preview img{ width:60px; height:60px; opacity:.65 }
  .image-preview span{ color:#7a7f87; margin-top:8px; font-weight:600 }

  /* Firma */
  .sig-wrap{
    position:relative;
    border:1px dashed #dfe3e8; border-radius:12px; overflow:hidden;
    background:
      radial-gradient(1000px 200px at 10% -20%, rgba(72,207,173,.08), transparent 60%),
      repeating-linear-gradient(0deg, #f9fafb 0 14px, #f3f4f6 14px 15px),
      repeating-linear-gradient(90deg, transparent 0 14px, rgba(2,6,23,.03) 14px 15px);
    height:200px;
    box-shadow: inset 0 1px 0 rgba(0,0,0,.03);
  }
  .sig-wrap:hover{ box-shadow: inset 0 0 0 2px rgba(72,207,173,.12); }
  .sig-wrap canvas{ display:block; width:100%; height:100%; background:transparent; cursor:crosshair; }
  .sig-placeholder{
    position:absolute; inset:0; display:grid; place-items:center; pointer-events:none;
    font-weight:800; letter-spacing:.5px; color:#7a7f87; opacity:.55;
  }
  .sig-toolbar{
    position:absolute; right:10px; bottom:10px; display:flex; gap:8px; z-index:2;
    background:rgba(255,255,255,.85); border:1px solid #e7ebf0; border-radius:999px; padding:6px 8px;
    box-shadow:0 8px 24px rgba(18,38,63,.10);
  }
  .chip{
    border:none; border-radius:999px; padding:8px 12px; font-weight:700; font-size:.9rem;
    background:#fff; color:#1f2937; border:1px solid #e5e7eb; cursor:pointer;
  }
  .chip-danger{ background:var(--warn-100); color:#7f1d1d; border-color:#fecaca; }

  /* Móvil */
  .fab{ position:fixed; right:16px; bottom:16px; z-index:60; display:none; }
  .fab button{ border:none; border-radius:999px; padding:14px 18px; font-weight:800; cursor:pointer; background:var(--mint); color:#fff; box-shadow:0 10px 30px rgba(18,38,63,.18); }
  .sheet-backdrop{ position:fixed; inset:0; background:rgba(2,6,23,.45); display:none; z-index:70; }
  .sheet{ position:fixed; left:0; right:0; bottom:-100%; z-index:80; background:#fff; border-radius:18px 18px 0 0; box-shadow:0 -20px 40px rgba(2,6,23,.16); padding:16px; transition:bottom .28s ease; }
  .sheet .grab{ width:60px; height:6px; background:#e5e7eb; border-radius:999px; margin:6px auto 12px; }
  .sheet .grid{ display:grid; gap:12px; }
  .sheet .link{ display:flex; align-items:center; gap:10px; padding:14px; border:1px dashed rgba(72,207,173,.5); border-radius:14px; background:#f5fffc; color:#0b3b26; font-weight:700; text-decoration:none; }

  .error-text{ color:#b91c1c; font-size:.85rem; margin-top:6px }
  .grid{ gap:22px; }
  .section{ padding:26px; }
  .field{ padding:18px 16px 12px; }
  .image-wrap{ padding:14px; }
  .image-preview{ position:relative; min-height:220px; max-height:320px; overflow:hidden; padding:8px; }
  .image-preview.is-image img{ width:100%; height:100%; object-fit:contain; opacity:1; border-radius:10px; }
  @media(max-width:860px){
    .section{ padding:20px; }
    .grid{ gap:18px; }
    .image-preview{ min-height:180px; max-height:240px; }
    .fab{ display:block; }
  }
</style>

<div class="wrap">
  <div class="panel">
    <div class="panel-head">
      <div>
        <h3 class="title">Entrega de Guía</h3>
        <p class="sub">Busca la guía, completa la información y captura la firma. Todo en un flujo limpio.</p>
      </div>
      <div class="actions">
        <button type="button" class="btn btn-ghost btn-back" onclick="handleBack()" aria-label="Regresar">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none">
            <path d="M15 19l-7-7 7-7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
          Regresar
        </button>
      </div>
    </div>

    <div class="section">
      @if(session('success'))
        <div style="background:var(--ok-100); color:#0b3b26; border:1px solid rgba(16,185,129,.35); border-radius:12px; padding:10px 12px; font-weight:700; margin-bottom:12px;">
          {{ session('success') }}
        </div>
      @endif

      <form action="{{ route('entregas.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- Buscar guía --}}
        <div class="dropdown">
          <div class="field has-icon">
            <svg class="icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
              <circle cx="11" cy="11" r="8" stroke-width="2"></circle>
              <line x1="21" y1="21" x2="16.65" y2="16.65" stroke-width="2"></line>
            </svg>
            <input id="search-guia" type="text" placeholder=" " autocomplete="off" aria-autocomplete="list">
            <label for="search-guia">Buscar Guía (número de rastreo)</label>
            <input type="hidden" id="guia_id" name="guia_id" required>
          </div>
          <ul class="dropdown-menu w-100" id="guia-list" style="display:none;"></ul>
        </div>

        <div class="grid grid-2" style="margin-top:10px">
          <div>
            <div class="field has-icon">
              <svg class="icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path fill="currentColor" d="M7 20h10v-2H7v2Zm5-18A7 7 0 0 0 5 9v4.5A2.5 2.5 0 0 0 7.5 16H9v-6H7a5 5 0 0 1 10 0h-2v6h1.5a2.5 2.5 0 0 0 2.5-2.5V9a7 7 0 0 0-7-7Z"/></svg>
              <input id="peso" name="peso" type="text" placeholder=" " readonly>
              <label for="peso">Peso total (kg)</label>
            </div>
          </div>
          <div>
            <div class="field">
              <input id="fecha" name="fecha" type="date" placeholder=" " value="{{ \Carbon\Carbon::today()->toDateString() }}" required>
              <label for="fecha">Fecha</label>
            </div>
          </div>
        </div>

        <div class="grid grid-3">
          <div>
            <div class="field has-icon">
              <svg class="icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path fill="currentColor" d="M20 8h-3V4H7v4H4l8 8l8-8Zm-9 2V6h2v4h2.5L12 14.5L8.5 10H11Z"/><path fill="currentColor" d="M4 18h16v2H4z"/></svg>
              <input id="contenido" name="contenido" type="text" placeholder=" " required>
              <label for="contenido">Contenido del paquete</label>
            </div>
            @error('contenido')<div class="error-text">{{ $message }}</div>@enderror
          </div>

          <div>
            <div class="field has-icon">
              <svg class="icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path fill="currentColor" d="M7 3h10v2H7zM5 7h14v2H5zm2 4h10v2H7zm-2 4h14v2H5zm2 4h10v2H7z"/></svg>
              <input id="numero_serie" name="numero_serie" type="text" placeholder=" " required>
              <label for="numero_serie">Número de serie</label>
            </div>
            @error('numero_serie')<div class="error-text">{{ $message }}</div>@enderror
          </div>

          <div>
            <div class="field has-icon">
              <svg class="icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path fill="currentColor" d="M12 12a5 5 0 1 0-5-5a5 5 0 0 0 5 5Zm0 2c-4 0-8 2-8 6v2h16v-2c0-4-4-6-8-6Z"/></svg>
              <input id="destinatario" name="destinatario" type="text" placeholder=" " required>
              <label for="destinatario">Destinatario</label>
            </div>
            @error('destinatario')<div class="error-text">{{ $message }}</div>@enderror
          </div>
        </div>

        <div class="grid grid-2">
          <div>
            <div class="image-wrap">
              <label class="image-preview" for="image-upload">
                <div style="display:grid; place-items:center">
                  <img id="preview-icon" src="https://cdn-icons-png.flaticon.com/512/1829/1829586.png" alt="Añadir imagen">
                  <span id="preview-text">Añadir imagen</span>
                </div>
              </label>
              <input id="image-upload" type="file" name="imagen" accept="image/*" hidden>
            </div>
          </div>

          <div>
            <div class="field">
              <textarea id="observaciones" name="observaciones" placeholder=" "></textarea>
              <label for="observaciones">Observaciones</label>
            </div>
          </div>
        </div>

        @auth
          <input type="hidden" id="user_name" name="user_name" value="{{ Auth::user()->name }}">
        @endauth

        {{-- Firma digital --}}
        <div class="grid" style="margin-top:4px">
          <div>
            <div style="font-weight:700; color:var(--ink); margin:4px 0 8px">Firma digital</div>

            <div class="sig-wrap">
              <div class="sig-placeholder" id="sigPlaceholder">FIRME AQUÍ ✍️</div>

              <div class="sig-toolbar">
                <button id="limpiarFirma" type="button" class="chip chip-danger" title="Limpiar firma">Limpiar</button>
              </div>

              <canvas id="firmaCanvas"></canvas>
            </div>

            <input type="hidden" id="firmaInput" name="firmaDigital">
          </div>
        </div>

        <div class="actions" style="justify-content:flex-end; margin-top:8px">
          <button type="submit" class="btn btn-ok">Registrar entrega</button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- FAB + Bottom Sheet (móvil) --}}
<div class="fab"><button id="openSheet">Menú</button></div>
<div class="sheet-backdrop" id="sheetBackdrop"></div>
<div class="sheet" id="sheet">
  <div class="grab"></div>
  <div style="text-align:center; font-weight:800; color:var(--ink); margin-bottom:6px;">Navegación rápida</div>
  <div class="grid">
    <a class="link" href="{{ route('guias.create') }}">📦 Guías</a>
    <a class="link" href="{{ route('entregas.index') }}">🧾 Entregas</a>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function handleBack(){
  if (window.history.length > 1) window.history.back();
  else window.location.href = "{{ route('entregas.index') }}";
}

document.addEventListener("DOMContentLoaded", function(){

  /* ====== Autorrelleno desde querystring ====== */
  const qs = new URLSearchParams(window.location.search);
  const guiaIdQS = qs.get('guia_id');
  const rastreoQS = qs.get('rastreo') || '';
  const pesoQS = qs.get('peso') || '';

  const inputGuia   = document.getElementById('guia_id');
  const inputSearch = document.getElementById('search-guia');
  const inputPeso   = document.getElementById('peso');

  const fmtRastreo = v => String(v).replace(/\D/g,'').replace(/(.{4})/g,'$1 ').trim();

  if (guiaIdQS) {
    inputGuia.value   = guiaIdQS;
    inputSearch.value = fmtRastreo(rastreoQS);
    inputPeso.value   = pesoQS ? (parseFloat(pesoQS).toFixed(2) + ' kg') : '';
    // Opcional: enfocar el primer campo de captura
    setTimeout(()=> document.getElementById('contenido')?.focus(), 50);
  }

  /* ====== Buscador AJAX (remoto) — usa ?search= tal como espera tu controlador ====== */
  const endpoint    = "{{ route('guias.search') }}";
  const guiaList    = document.getElementById("guia-list");
  let debounceT = null;

  function pintar(list){
    guiaList.innerHTML = "";
    if(!list.length){
      guiaList.innerHTML = `<li><button class="dropdown-item" disabled>Sin registros disponibles</button></li>`;
      guiaList.style.display = "block";
      return;
    }
    list.slice(0,6).forEach(g=>{
      const li = document.createElement('li');
      li.innerHTML = `<button class="dropdown-item" data-id="${g.id}" data-peso="${g.peso ?? ''}">${g.numero_rastreo}</button>`;
      li.querySelector('button').addEventListener('click', ()=>{
        inputSearch.value = fmtRastreo(g.numero_rastreo);
        inputGuia.value   = g.id;
        inputPeso.value   = g.peso ? (Math.round(parseFloat(g.peso)*100)/100) + " kg" : "";
        guiaList.style.display = "none";
        document.getElementById('contenido')?.focus();
      });
      guiaList.appendChild(li);
    });
    guiaList.style.display = "block";
  }

  async function buscarRemoto(q){
    const url = new URL(endpoint, window.location.origin);
    url.searchParams.set('search', q); // <- CORRECTO: tu controlador usa "search"
    try{
      const res  = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
      const json = await res.json();
      const items = Array.isArray(json) ? json : (json.data || []);
      pintar(items);
    }catch(err){
      guiaList.innerHTML = `<li><button class="dropdown-item" disabled>Error al buscar</button></li>`;
      guiaList.style.display = "block";
    }
  }

  if(inputSearch){
    inputSearch.addEventListener('input', function(){
      clearTimeout(debounceT);
      let val = this.value.replace(/\D/g,"");
      val = val.replace(/(.{4})/g, "$1 ").trim();
      this.value = val;

      const clean = val.replace(/\s+/g,"");
      if(!clean){ guiaList.style.display='none'; inputGuia.value=''; inputPeso.value=''; return; }

      debounceT = setTimeout(()=> buscarRemoto(clean), 250);
    });

    // Cerrar si se hace click fuera
    document.addEventListener('click', (ev)=>{
      if(!inputSearch.contains(ev.target) && !guiaList.contains(ev.target)){
        guiaList.style.display = "none";
      }
    });

    // Navegación con flechas
    inputSearch.addEventListener('keydown', (ev)=>{
      const items = guiaList.querySelectorAll(".dropdown-item:not([disabled])");
      if(!items.length) return;

      let active = Array.from(items).findIndex(i=> i.classList.contains('active'));
      if(ev.key==='ArrowDown'){ ev.preventDefault(); active = active < items.length-1 ? active+1 : 0; }
      if(ev.key==='ArrowUp'){   ev.preventDefault(); active = active > 0 ? active-1 : items.length-1; }
      if(ev.key==='Enter' && active !== -1){ ev.preventDefault(); items[active].click(); return; }

      items.forEach(i=> i.classList.remove('active'));
      if(active !== -1) items[active].classList.add('active');
    });
  }

  /* Mayúsculas */
  ["contenido","numero_serie","destinatario","observaciones"].forEach(id=>{
    const el = document.getElementById(id);
    if(el) el.addEventListener('input', e=> e.target.value = e.target.value.toUpperCase());
  });

  /* Preview de imagen */
  const imageInput = document.getElementById('image-upload');
  if(imageInput){
    imageInput.addEventListener('change', ev=>{
      const file = ev.target.files[0];
      if(file && file.type.startsWith('image/')){
        const reader = new FileReader();
        reader.onload = e=>{
          const img = document.getElementById('preview-icon');
          const txt = document.getElementById('preview-text');
          const box = document.querySelector('.image-preview');
          img.src = e.target.result;
          box.classList.add('is-image');
          if(txt) txt.style.display='none';
        };
        reader.readAsDataURL(file);
      }
    });
  }

  /* Firma digital */
  const canvas = document.getElementById('firmaCanvas');
  const ctx = canvas.getContext('2d');
  const firmaInput = document.getElementById('firmaInput');
  const limpiarFirma = document.getElementById('limpiarFirma');
  const placeholder = document.getElementById('sigPlaceholder');

  let drawing = false;
  let last = null;  // {x,y,t}
  const MIN_W = 1.2, MAX_W = 3.8;

  function dpr(){ return window.devicePixelRatio || 1; }
  function fitCanvas(){
    const rect = canvas.getBoundingClientRect();
    const ratio = dpr();
    canvas.width = rect.width * ratio;
    canvas.height = rect.height * ratio;
    ctx.setTransform(ratio,0,0,ratio,0,0);
    ctx.lineCap = 'round';
    ctx.lineJoin = 'round';
    ctx.strokeStyle = '#111827';
    ctx.imageSmoothingEnabled = true;
  }
  fitCanvas();
  window.addEventListener('resize', fitCanvas);

  function pos(ev){
    const r = canvas.getBoundingClientRect();
    const e = ev.touches ? ev.touches[0] : ev;
    return { x: e.clientX - r.left, y: e.clientY - r.top, t: Date.now() };
  }
  function widthFrom(p1, p2){
    const dt = Math.max(1, p2.t - p1.t);
    const dist = Math.hypot(p2.x - p1.x, p2.y - p1.y);
    const v = dist / dt;
    let w = MAX_W - v * 2.8;
    if (w < MIN_W) w = MIN_W;
    if (w > MAX_W) w = MAX_W;
    return w;
  }
  function start(ev){
    drawing = true; last = pos(ev);
    placeholder.style.display = 'none';
    ev.preventDefault();
  }
  function move(ev){
    if(!drawing) return;
    const p = pos(ev);
    ctx.beginPath();
    ctx.moveTo(last.x, last.y);
    ctx.lineTo(p.x, p.y);
    ctx.lineWidth = widthFrom(last, p);
    ctx.stroke();
    last = p;
    ev.preventDefault();
  }
  function end(){
    if(!drawing) return;
    drawing = false; last = null;
    firmaInput.value = canvas.toDataURL('image/png');
  }

  canvas.addEventListener('mousedown', start);
  canvas.addEventListener('mousemove', move);
  window.addEventListener('mouseup', end);
  canvas.addEventListener('touchstart', start, {passive:false});
  canvas.addEventListener('touchmove',  move,  {passive:false});
  window.addEventListener('touchend',   end);

  limpiarFirma.addEventListener('click', ()=>{
    const r = canvas.getBoundingClientRect();
    ctx.clearRect(0,0,r.width,r.height);
    firmaInput.value = '';
    placeholder.style.display = 'grid';
  });

  /* Bottom sheet */
  const sheet = document.getElementById('sheet');
  const backdrop = document.getElementById('sheetBackdrop');
  const openSheet = document.getElementById('openSheet');
  function showSheet(show){ backdrop.style.display = show ? 'block' : 'none'; sheet.style.bottom = show ? '0' : '-100%'; }
  if(openSheet) openSheet.addEventListener('click', ()=> showSheet(true));
  if(backdrop)  backdrop.addEventListener('click', ()=> showSheet(false));
});
</script>
@endsection
