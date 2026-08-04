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

  .wrap{ max-width:1100px; margin:28px auto; padding:0 16px; }
  .panel{
    background:var(--card); border:1px solid var(--line);
    border-radius:var(--radius); box-shadow:var(--shadow-lg);
    /* CRÍTICO: visible para que los dropdowns floten */
    overflow:visible;
  }
  .panel-head{
    padding:20px 22px; border-bottom:1px solid var(--line);
    display:flex; align-items:center; justify-content:space-between; gap:12px;
    border-radius:var(--radius) var(--radius) 0 0; overflow:hidden;
  }
  .panel-head .title{ margin:0; font-weight:800; color:var(--ink); }
  .panel-head .sub{ margin:4px 0 0; color:var(--muted); font-size:14px }
  .section{ padding:26px; }

  .actions{ display:flex; gap:10px; align-items:center; }
  .btn{ border:0; border-radius:12px; padding:12px 16px; font-weight:700; cursor:pointer; transition:transform .05s, background .2s; }
  .btn:active{ transform:translateY(1px) }
  .btn-ghost{ background:#fff; color:var(--ink); border:1px solid var(--line) }
  .btn-ghost:hover{ border-color:#dfe3e8 }
  .btn-ok{ background:var(--ok); color:#fff }
  .btn-back{ display:inline-flex; align-items:center; gap:8px; }
  .btn-back svg{ width:18px; height:18px; }

  /* Grid */
  .grid{ display:grid; gap:18px; grid-template-columns:1fr; }
  @media(min-width:900px){ .grid-2{ grid-template-columns:1fr 1fr; } .grid-3{ grid-template-columns:1fr 1fr 1fr; } }

  /* ── CAMPO CON LABEL FLOTANTE ── */
  .field{
    position:relative; background:#fff; border:1px solid var(--line);
    border-radius:12px; padding:18px 14px 10px;
    transition:box-shadow .2s, border-color .2s;
  }
  .field:focus-within{ border-color:#d8dee6; box-shadow:0 8px 24px rgba(18,38,63,.08) }
  .field input,.field textarea,.field select{
    width:100%; border:0; outline:0; background:transparent;
    font-size:15px; color:var(--ink); padding-top:8px;
  }
  .field input::placeholder,.field textarea::placeholder{ color:transparent }
  .field label{
    position:absolute; left:14px; top:16px; color:var(--muted);
    font-size:13px; pointer-events:none;
    transition:transform .15s, color .15s, font-size .15s, top .15s;
  }
  .field input:focus + label,
  .field input:not(:placeholder-shown) + label,
  .field textarea:focus + label,
  .field textarea:not(:placeholder-shown) + label,
  .field select:focus + label,
  .field.filled label{
    top:6px; transform:translateY(0); font-size:11px; color:var(--mint-dark);
  }
  .field .icon{
    position:absolute; left:12px; top:50%; transform:translateY(-50%);
    width:18px; height:18px; color:#8aa0b3; opacity:.85;
  }
  .field.has-icon input, .field.has-icon textarea{ padding-left:30px }

  /* Ajuste específico para centrar el icono en textareas */
  .field.has-icon textarea + label + .icon,
  .field.has-icon .icon:first-child {
    top: 24px;
    transform: none;
  }

  /* ── Campo readonly (autorellenado) ── */
  .field input[readonly]{ background:transparent; cursor:default; color:var(--ink); }
  .field textarea[readonly]{ background:transparent; cursor:default; color:var(--ink); }
  .field:has(input[readonly]),
  .field:has(textarea[readonly]){ background:#f8fffe; border-color:rgba(72,207,173,.3); }

  /* ── DROPDOWN GENÉRICO ── */
  .dd-wrap{ position:relative; }
  .dd-list{
    position:absolute; left:0; right:0; top:calc(100% + 6px);
    background:#fff; border:1px solid var(--line); border-radius:12px;
    box-shadow:var(--shadow-lg); list-style:none; padding:6px; margin:0;
    max-height:240px; overflow-y:auto; z-index:9999; display:none;
  }
  .dd-list.open{ display:block; }
  .dd-item{
    border:none; background:transparent; width:100%; text-align:left;
    padding:10px 12px; border-radius:10px; cursor:pointer; font-size:14px;
    color:var(--ink); display:block;
  }
  .dd-item:hover,.dd-item.active{ background:#f3fffb; color:var(--mint-dark); }
  .dd-empty{ padding:10px 12px; color:var(--muted); font-size:13px; }

  /* badge de serie en dropdown */
  .dd-serie-badge{
    display:inline-block; background:#e0f2fe; color:#0369a1;
    border-radius:6px; padding:1px 7px; font-size:11px; font-weight:700;
    margin-right:6px; letter-spacing:.03em;
  }

  /* aviso de "no está en cartera, se guardará manual" */
  .dd-manual-hint{
    padding:10px 12px; color:#7a7f87; font-size:12px; border-top:1px dashed var(--line);
    margin-top:4px;
  }

  /* ── CHIPS DE SERIES SELECCIONADAS (multi-selección) ── */
  .serie-chips{ display:flex; flex-wrap:wrap; gap:8px; margin-top:10px; }
  .serie-chip{
    display:flex; align-items:center; gap:8px; background:#f3fffb;
    border:1px solid rgba(72,207,173,.45); border-radius:10px;
    padding:6px 8px 6px 10px; font-size:13px; color:var(--ink);
    max-width:100%;
  }
  .serie-chip .serie-chip-serie{
    font-weight:800; color:var(--mint-dark); white-space:nowrap;
  }
  .serie-chip .serie-chip-contenido{
    color:var(--muted); max-width:200px; overflow:hidden;
    text-overflow:ellipsis; white-space:nowrap;
  }
  .serie-chip button{
    border:none; background:transparent; color:#b91c1c; font-weight:800;
    cursor:pointer; font-size:16px; line-height:1; padding:0 2px;
  }
  .serie-chip button:hover{ color:#7f1d1d; }
  .serie-empty-hint{ font-size:12px; color:var(--muted); margin-top:8px; }
  .serie-count-badge{
    display:inline-block; background:var(--mint); color:#fff;
    border-radius:999px; padding:1px 9px; font-size:11px; font-weight:800;
    margin-left:8px;
  }

  /* ── UPLOADER ── */
  .image-wrap{ border:1px dashed #dfe3e8; border-radius:14px; padding:14px; background:#fafbfc; }
  .image-preview{
    display:grid; place-items:center; border-radius:12px;
    min-height:220px; max-height:320px; overflow:hidden;
    background:#fff; border:1px solid #edf0f3; cursor:pointer; padding:8px;
  }
  .image-preview img{ width:60px; height:60px; opacity:.65 }
  .image-preview span{ color:#7a7f87; margin-top:8px; font-weight:600 }
  .image-preview.is-image img{ width:100%; height:100%; object-fit:contain; opacity:1; border-radius:10px; }

  /* ── FIRMA ── */
  .sig-wrap{
    position:relative; border:1px dashed #dfe3e8; border-radius:12px;
    overflow:hidden; height:200px;
    background:
      radial-gradient(1000px 200px at 10% -20%, rgba(72,207,173,.08), transparent 60%),
      repeating-linear-gradient(0deg, #f9fafb 0 14px, #f3f4f6 14px 15px),
      repeating-linear-gradient(90deg, transparent 0 14px, rgba(2,6,23,.03) 14px 15px);
  }
  .sig-wrap canvas{ display:block; width:100%; height:100%; background:transparent; cursor:crosshair; }
  .sig-placeholder{ position:absolute; inset:0; display:grid; place-items:center; pointer-events:none; font-weight:800; letter-spacing:.5px; color:#7a7f87; opacity:.55; }
  .sig-toolbar{ position:absolute; right:10px; bottom:10px; display:flex; gap:8px; z-index:2; background:rgba(255,255,255,.85); border:1px solid #e7ebf0; border-radius:999px; padding:6px 8px; box-shadow:0 8px 24px rgba(18,38,63,.10); }
  .chip{ border:none; border-radius:999px; padding:8px 12px; font-weight:700; font-size:.9rem; background:#fff; color:#1f2937; border:1px solid #e5e7eb; cursor:pointer; }
  .chip-danger{ background:var(--warn-100); color:#7f1d1d; border-color:#fecaca; }

  /* ── FAB / SHEET ── */
  .fab{ position:fixed; right:16px; bottom:16px; z-index:60; display:none; }
  .fab button{ border:none; border-radius:999px; padding:14px 18px; font-weight:800; cursor:pointer; background:var(--mint); color:#fff; box-shadow:0 10px 30px rgba(18,38,63,.18); }
  .sheet-backdrop{ position:fixed; inset:0; background:rgba(2,6,23,.45); display:none; z-index:70; }
  .sheet{ position:fixed; left:0; right:0; bottom:-100%; z-index:80; background:#fff; border-radius:18px 18px 0 0; box-shadow:0 -20px 40px rgba(2,6,23,.16); padding:16px; transition:bottom .28s ease; }
  .sheet .grab{ width:60px; height:6px; background:#e5e7eb; border-radius:999px; margin:6px auto 12px; }
  .sheet .sgrid{ display:grid; gap:12px; }
  .sheet .link{ display:flex; align-items:center; gap:10px; padding:14px; border:1px dashed rgba(72,207,173,.5); border-radius:14px; background:#f5fffc; color:#0b3b26; font-weight:700; text-decoration:none; }

  .error-text{ color:#b91c1c; font-size:.85rem; margin-top:6px }

  /* ── VALIDACIÓN DE CAMPOS FALTANTES ── */
  #form-error{
    display:none; background:var(--warn-100); color:#7f1d1d;
    border:1px solid #fecaca; border-radius:12px; padding:12px 14px;
    font-weight:700; margin-bottom:16px;
  }

  .field.field-error,
  .image-wrap.field-error,
  .sig-wrap.field-error{
    border-color:var(--warn) !important;
    box-shadow:0 0 0 3px rgba(239,68,68,.15);
  }

  @media(max-width:860px){
    .section{ padding:20px; }
    .grid{ gap:18px; }
    .image-preview{ min-height:180px; max-height:240px; }
    .fab{ display:block; }
  }
</style>

<div class="wrap" style="margin-top:90px">
  <div class="panel">
    <div class="panel-head">
      <div>
        <h3 class="title">Entrega de Guía</h3>
        <p class="sub">Busca la guía, completa la información y captura la firma.</p>
      </div>
      <div class="actions">
        <button type="button" class="btn btn-ghost btn-back" onclick="handleBack()">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none">
            <path d="M15 19l-7-7 7-7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
          Regresar
        </button>
      </div>
    </div>

    <div class="section">
      @if(session('success'))
        <div style="background:var(--ok-100);color:#0b3b26;border:1px solid rgba(16,185,129,.35);border-radius:12px;padding:10px 12px;font-weight:700;margin-bottom:12px;">
          {{ session('success') }}
        </div>
      @endif

      <form id="entregaForm" action="{{ route('entregas.store') }}" method="POST" enctype="multipart/form-data" novalidate>
        @csrf

        <div id="form-error"></div>

        {{-- ── 1. BUSCAR GUÍA ── --}}
        <div class="dd-wrap" id="ddGuia">
          <div class="field has-icon">
            <svg class="icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <circle cx="11" cy="11" r="8" stroke-width="2"/><line x1="21" y1="21" x2="16.65" y2="16.65" stroke-width="2"/>
            </svg>
            <input id="search-guia" type="text" placeholder=" " autocomplete="off">
            <label for="search-guia">Buscar Guía (número de rastreo)</label>
            <input type="hidden" id="guia_id" name="guia_id">
          </div>
          <ul class="dd-list" id="guia-list"></ul>
        </div>

        {{-- ── Peso + Fecha ── --}}
        <div class="grid grid-2" style="margin-top:14px">
          <div>
            <div class="field has-icon">
              <svg class="icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path fill="currentColor" d="M7 20h10v-2H7v2Zm5-18A7 7 0 0 0 5 9v4.5A2.5 2.5 0 0 0 7.5 16H9v-6H7a5 5 0 0 1 10 0h-2v6h1.5a2.5 2.5 0 0 0 2.5-2.5V9a7 7 0 0 0-7-7Z"/></svg>
              <input id="peso" name="peso" type="text" placeholder=" " readonly required>
              <label for="peso">Peso total (kg)</label>
            </div>
          </div>
          <div>
            <div class="field">
              <input id="fecha" name="fecha" type="date" placeholder=" " value="{{ \Carbon\Carbon::today()->toDateString() }}" required>
              <label for="fecha" style="top:6px;font-size:11px;color:var(--mint-dark)">Fecha</label>
            </div>
          </div>
        </div>

        {{-- ── 2. NÚMERO DE SERIE (buscador, selección MÚLTIPLE) → rellena CONTENIDO ── --}}
        <div class="grid grid-2" style="margin-top:14px; align-items:start;">

          {{-- IZQUIERDA: Número de serie — buscador en inventario, permite varios productos --}}
          <div>
            <div class="dd-wrap" id="ddSerie">
              <div class="field has-icon">
                <svg class="icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <circle cx="11" cy="11" r="8" stroke-width="2"/>
                  <line x1="21" y1="21" x2="16.65" y2="16.65" stroke-width="2"/>
                </svg>
                <input id="search-serie" type="text" placeholder=" " autocomplete="off">
                <label for="search-serie">
                  Buscar número de serie
                  <span class="serie-count-badge" id="serie-count-badge" style="display:none">0</span>
                </label>
              </div>
              <ul class="dd-list" id="serie-list"></ul>
            </div>

            {{-- Chips con las series ya agregadas --}}
            <div class="serie-chips" id="serie-chips"></div>
            <div class="serie-empty-hint" id="serie-empty-hint">
              Busca y selecciona uno o varios productos para esta guía.
            </div>

            {{-- Aquí se generan dinámicamente los <input type="hidden" name="numero_serie[]"> --}}
            <div id="serie-hidden-inputs"></div>

            @error('numero_serie')<div class="error-text">{{ $message }}</div>@enderror
          </div>

          {{-- DERECHA: Contenido — resumen automático de todos los productos seleccionados --}}
          <div>
            <div class="field has-icon">
              <svg class="icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                <path fill="currentColor" d="M7 3h10v2H7zM5 7h14v2H5zm2 4h10v2H7zm-2 4h14v2H5zm2 4h10v2H7z"/>
              </svg>
              <textarea id="contenido" name="contenido" rows="1" placeholder=" " readonly style="resize:none;"></textarea>
              <label for="contenido">Contenido del paquete</label>
            </div>

            {{-- Aquí se generan dinámicamente los <input type="hidden" name="contenido_items[]"> (uno por producto) --}}
            <div id="contenido-hidden-inputs"></div>

            @error('contenido')<div class="error-text">{{ $message }}</div>@enderror
          </div>

        </div>

        {{-- ── 3. DESTINATARIO + ENTREGADO POR ── --}}
        <div class="grid grid-2" style="margin-top:14px">
          <div>
            <div class="dd-wrap" id="ddDestinatario">
              <div class="field has-icon">
                <svg class="icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path fill="currentColor" d="M12 12a5 5 0 1 0-5-5a5 5 0 0 0 5 5Zm0 2c-4 0-8 2-8 6v2h16v-2c0-4-4-6-8-6Z"/></svg>
                <input id="search-dest" type="text" placeholder=" " autocomplete="off">
                <label for="search-dest">Destinatario (cartera de clientes o escribe manualmente)</label>
                <input type="hidden" id="destinatario" name="destinatario" required>
              </div>
              <ul class="dd-list" id="dest-list"></ul>
            </div>
            @error('destinatario')<div class="error-text">{{ $message }}</div>@enderror
          </div>

          <div>
            <div class="dd-wrap" id="ddEntregador">
              <div class="field has-icon">
                <svg class="icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path fill="currentColor" d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>
                <input id="search-entregador" type="text" placeholder=" " autocomplete="off">
                <label for="search-entregador">Entregado por</label>
                <input type="hidden" id="entregado_por" name="entregado_por" required>
              </div>
              <ul class="dd-list" id="entregador-list"></ul>
            </div>
          </div>
        </div>

        {{-- ── CAMPO DE OBSERVACIONES ── --}}
        <div style="margin-top:14px">
          <div class="field has-icon">
            <svg class="icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
            </svg>
            <textarea id="observaciones" name="observaciones" rows="2" placeholder=" " style="resize: none;"></textarea>
            <label for="observaciones">Observaciones adicionales</label>
          </div>
          @error('observaciones')<div class="error-text">{{ $message }}</div>@enderror
        </div>

        {{-- ── 4. IMAGEN + FIRMA ── --}}
        <div class="grid grid-2" style="margin-top:14px">
          <div>
            <div class="image-wrap">
              <label class="image-preview" for="image-upload">
                <div style="display:grid;place-items:center">
                  <img id="preview-icon" src="https://cdn-icons-png.flaticon.com/512/1829/1829586.png" alt="Añadir imagen">
                  <span id="preview-text">Añadir imagen</span>
                </div>
              </label>
              <input id="image-upload" type="file" name="imagen" accept="image/*" hidden required>
            </div>
          </div>

          <div>
            <div style="font-weight:700;color:var(--ink);margin:0 0 8px">Firma digital</div>
            <div class="sig-wrap">
              <div class="sig-placeholder" id="sigPlaceholder">FIRME AQUÍ ✍️</div>
              <div class="sig-toolbar">
                <button id="limpiarFirma" type="button" class="chip chip-danger">Limpiar</button>
              </div>
              <canvas id="firmaCanvas"></canvas>
            </div>
            <input type="hidden" id="firmaInput" name="firmaDigital">
            <div class="error-text" id="firma-error" style="display:none">La firma es obligatoria.</div>
          </div>
        </div>

        @auth
          <input type="hidden" id="user_name" name="user_name" value="{{ Auth::user()->name }}">
        @endauth

        <div id="form-error" style="display:none;background:var(--warn-100);color:#7f1d1d;border:1px solid #fecaca;border-radius:12px;padding:10px 12px;font-weight:700;margin-top:16px;"></div>

        <div class="actions" style="justify-content:flex-end;margin-top:20px">
          <button type="submit" class="btn btn-ok" id="btnRegistrar">Registrar entrega</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="fab"><button id="openSheet">Menú</button></div>
<div class="sheet-backdrop" id="sheetBackdrop"></div>
<div class="sheet" id="sheet">
  <div class="grab"></div>
  <div style="text-align:center;font-weight:800;color:var(--ink);margin-bottom:6px;">Navegación rápida</div>
  <div class="sgrid">
    <a class="link" href="{{ route('guias.create') }}">📦 Guías</a>
    <a class="link" href="{{ route('entregas.index') }}">🧾 Entregas</a>
  </div>
</div>

<script>
function handleBack(){
  if(window.history.length > 1) window.history.back();
  else window.location.href = "{{ route('entregas.index') }}";
}

/* ── Endpoints ── */
const ROUTES = {
  guias:       "{{ route('guias.search') }}",
  buscarSerie: "{{ route('registros.buscar-serie') }}",
  clientes:    "{{ route('clientes.search') }}",
  usuarios:    "{{ route('agenda.users') }}",
};

document.addEventListener("DOMContentLoaded", function(){

  /* ════════════════════════════════════════════════════════════
     UTILIDAD: dropdown genérico reutilizable
     ════════════════════════════════════════════════════════════ */
  function makeDropdown({ inputEl, listEl, hiddenEl, endpoint, paramName,
                          labelFn, descFn, onSelect, minChars = 1, renderItem,
                          emptyHint = null, closeOnSelect = true }){
    let timer = null;

    function render(items){
      listEl.innerHTML = '';
      if(!items.length){
        listEl.innerHTML = `<li class="dd-empty">Sin resultados</li>`;
        if(emptyHint){
          const hint = document.createElement('li');
          hint.className = 'dd-manual-hint';
          hint.textContent = emptyHint;
          listEl.appendChild(hint);
        }
        listEl.classList.add('open');
        return;
      }
      items.slice(0,12).forEach(item => {
        const li  = document.createElement('li');
        const btn = document.createElement('button');
        btn.type      = 'button';
        btn.className = 'dd-item';

        if(renderItem){
          /* render personalizado */
          btn.innerHTML = renderItem(item);
        } else {
          const spanLabel = document.createElement('span');
          spanLabel.style.cssText = 'display:block;font-weight:600';
          spanLabel.textContent = labelFn(item);
          btn.appendChild(spanLabel);

          if(descFn){
            const d = descFn(item);
            if(d){
              const spanDesc = document.createElement('span');
              spanDesc.style.cssText = 'display:block;font-size:11px;color:#7a7f87;margin-top:1px';
              spanDesc.textContent = d;
              btn.appendChild(spanDesc);
            }
          }
        }

        btn.addEventListener('click', () => {
          if(!hiddenEl){
            // Cuando no hay un hidden único (p.ej. selección múltiple),
            // dejamos que "onSelect" decida qué hacer con el input.
          } else {
            inputEl.value = labelFn(item);
            hiddenEl.value = item.id ?? labelFn(item);
          }
          if(closeOnSelect) listEl.classList.remove('open');
          onSelect && onSelect(item);
        });
        li.appendChild(btn);
        listEl.appendChild(li);
      });
      listEl.classList.add('open');
    }

    async function doFetch(q){
      try{
        const url = new URL(endpoint, window.location.origin);
        url.searchParams.set(paramName, q);
        const res  = await fetch(url, { headers:{ 'X-Requested-With':'XMLHttpRequest' }});
        const json = await res.json();
        const list = Array.isArray(json) ? json : (json.items ?? json.data ?? []);
        render(list);
      }catch(e){
        listEl.innerHTML = `<li class="dd-empty">Error al buscar</li>`;
        listEl.classList.add('open');
      }
    }

    inputEl.addEventListener('input', function(){
      clearTimeout(timer);
      const val = this.value.trim();
      if(val.length < minChars){
        listEl.classList.remove('open');
        return;
      }
      timer = setTimeout(() => doFetch(val), 260);
    });

    inputEl.addEventListener('keydown', ev => {
      const items = [...listEl.querySelectorAll('.dd-item')];
      if(!items.length) return;
      let idx = items.findIndex(i => i.classList.contains('active'));
      if(ev.key === 'ArrowDown'){ ev.preventDefault(); idx = idx < items.length-1 ? idx+1 : 0; }
      else if(ev.key === 'ArrowUp'){ ev.preventDefault(); idx = idx > 0 ? idx-1 : items.length-1; }
      else if(ev.key === 'Enter' && idx >= 0){ ev.preventDefault(); items[idx].click(); return; }
      else return;
      items.forEach(i => i.classList.remove('active'));
      items[idx].classList.add('active');
    });

    document.addEventListener('click', ev => {
      if(!inputEl.closest('.dd-wrap').contains(ev.target))
        listEl.classList.remove('open');
    });
  }

  /* ════════════════════════════════════════════════════════════
     1. BUSCAR GUÍA
     ════════════════════════════════════════════════════════════ */
  const fmtRastreo = v => String(v).replace(/\D/g,'').replace(/(.{4})/g,'$1 ').trim();
  const inputSearch = document.getElementById('search-guia');

  inputSearch.addEventListener('input', function(){
    this.value = this.value.replace(/\D/g,'').replace(/(.{4})/g,'$1 ').trim();
  });

  makeDropdown({
    inputEl:  inputSearch,
    listEl:   document.getElementById('guia-list'),
    hiddenEl: document.getElementById('guia_id'),
    endpoint: ROUTES.guias,
    paramName:'search',
    labelFn:  g => fmtRastreo(g.numero_rastreo),
    descFn:   null,
    onSelect: g => {
      document.getElementById('peso').value = g.peso
        ? (parseFloat(g.peso).toFixed(2)+' kg') : '';
      document.getElementById('search-serie').focus();
    }
  });

  /* Autorellenar desde querystring */
  const qs = new URLSearchParams(window.location.search);
  if(qs.get('guia_id')){
    document.getElementById('guia_id').value = qs.get('guia_id');
    inputSearch.value = fmtRastreo(qs.get('rastreo') || '');
    document.getElementById('peso').value = qs.get('peso')
      ? parseFloat(qs.get('peso')).toFixed(2)+' kg' : '';
  }

  /* ════════════════════════════════════════════════════════════
     2. NÚMERO DE SERIE → SELECCIÓN MÚLTIPLE → rellena CONTENIDO
     ════════════════════════════════════════════════════════════
     - El usuario puede buscar y agregar TANTOS productos como
       necesite; cada uno se muestra como un "chip" removible.
     - Por cada producto agregado se genera un input oculto
       name="numero_serie[]" y otro name="contenido_items[]",
       para que el backend reciba arreglos y pueda guardar varios
       productos asociados a la misma guía/entrega.
     - El textarea "contenido" (name="contenido") sigue existiendo
       como resumen legible de todo lo seleccionado, por si el
       backend todavía espera ese campo como texto único.
     ════════════════════════════════════════════════════════════ */
  const searchSerieInput    = document.getElementById('search-serie');
  const serieListEl         = document.getElementById('serie-list');
  const chipsContainer      = document.getElementById('serie-chips');
  const emptyHintEl         = document.getElementById('serie-empty-hint');
  const countBadge          = document.getElementById('serie-count-badge');
  const serieHiddenWrap     = document.getElementById('serie-hidden-inputs');
  const contenidoHiddenWrap = document.getElementById('contenido-hidden-inputs');
  const contenidoEl         = document.getElementById('contenido');

  // Lista en memoria de los productos seleccionados: [{ numero_serie, contenido }]
  let selectedSeries = [];

  function escapeHtml(str){
    return String(str ?? '')
      .replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
  }

  function renderSerieChips(){
    // 1) Chips visuales
    chipsContainer.innerHTML = '';
    selectedSeries.forEach((item, idx) => {
      const chip = document.createElement('div');
      chip.className = 'serie-chip';
      chip.innerHTML = `
        <span class="serie-chip-serie">${escapeHtml(item.numero_serie)}</span>
        <span class="serie-chip-contenido">${escapeHtml(item.contenido)}</span>
        <button type="button" data-idx="${idx}" title="Quitar producto" aria-label="Quitar">&times;</button>
      `;
      chip.querySelector('button').addEventListener('click', () => {
        selectedSeries.splice(idx, 1);
        renderSerieChips();
      });
      chipsContainer.appendChild(chip);
    });

    // 2) Mensaje / badge de conteo
    emptyHintEl.style.display = selectedSeries.length ? 'none' : 'block';
    if(selectedSeries.length){
      countBadge.style.display = 'inline-block';
      countBadge.textContent = selectedSeries.length;
    } else {
      countBadge.style.display = 'none';
    }

    // 3) Inputs ocultos para el backend (arreglos)
    serieHiddenWrap.innerHTML = selectedSeries
      .map(item => `<input type="hidden" name="numero_serie[]" value="${escapeHtml(item.numero_serie)}">`)
      .join('');

    contenidoHiddenWrap.innerHTML = selectedSeries
      .map(item => `<input type="hidden" name="contenido_items[]" value="${escapeHtml(item.contenido)}">`)
      .join('');

    // 4) Resumen legible en el textarea "contenido"
    contenidoEl.value = selectedSeries
      .map(i => `${i.numero_serie}: ${i.contenido || 'Sin descripción'}`)
      .join('\n');

    marcarCampo(searchSerieInput, false);
  }

  /* Mayúsculas en tiempo real mientras se busca */
  searchSerieInput.addEventListener('input', function(){
    const pos = this.selectionStart;
    this.value = this.value.toUpperCase();
    this.setSelectionRange(pos, pos);
  });

  makeDropdown({
    inputEl:  searchSerieInput,
    listEl:   serieListEl,
    hiddenEl: null, // ya no hay un único hidden: manejamos todo en onSelect
    endpoint: ROUTES.buscarSerie,
    paramName:'q',
    labelFn:  r => r.numero_serie || '',
    closeOnSelect: false, // dejamos el dropdown abierto para seguir agregando
    renderItem: r => {
      const serie      = (r.numero_serie || '').toUpperCase();
      const contenido  = (r.contenido    || '').toUpperCase();
      const yaAgregado = selectedSeries.some(i => i.numero_serie === serie);
      return `
        <span style="display:block;font-weight:700; ${yaAgregado ? 'opacity:.45' : ''}">
          <span class="dd-serie-badge">${escapeHtml(serie)}</span>
          ${escapeHtml(contenido)}
          ${yaAgregado ? ' <em style="font-weight:400;font-size:11px">(ya agregado)</em>' : ''}
        </span>`;
    },
    onSelect: r => {
      const serie = (r.numero_serie || '').toUpperCase();

      // Evitar duplicados
      if(selectedSeries.some(i => i.numero_serie === serie)){
        searchSerieInput.value = '';
        return;
      }

      selectedSeries.push({
        numero_serie: serie,
        contenido: (r.contenido || '').toUpperCase()
      });

      renderSerieChips();

      // Limpiamos el buscador para permitir agregar otro producto
      searchSerieInput.value = '';
      searchSerieInput.focus();
    }
  });

  // Estado inicial de los chips (vacío)
  renderSerieChips();

  /* ════════════════════════════════════════════════════════════
     3. DESTINATARIO (cartera de clientes + fallback manual)
     ════════════════════════════════════════════════════════════
     - Si el cliente existe en la cartera (ClienteController::search),
       aparece en el dropdown y al seleccionarlo se guarda su nombre.
     - Si NO está registrado, el usuario puede escribir el nombre
       manualmente: el hidden #destinatario se sincroniza en cada
       tecleo, así el formulario igual se puede enviar.
     ════════════════════════════════════════════════════════════ */
  const searchDestInput    = document.getElementById('search-dest');
  const destinatarioHidden = document.getElementById('destinatario');

  makeDropdown({
    inputEl:  searchDestInput,
    listEl:   document.getElementById('dest-list'),
    hiddenEl: destinatarioHidden,
    endpoint: ROUTES.clientes,
    paramName:'q',
    minChars: 2,
    labelFn:  c => c.label ?? c.nombre ?? c.name ?? '',
    descFn:   c => c.desc  ?? c.telefono ?? c.email ?? null,
    emptyHint:'No está en la cartera de clientes. Puedes escribir el nombre y continuar.',
    onSelect: c => {
      const nombre = c.label ?? c.nombre ?? '';
      searchDestInput.value    = nombre;
      destinatarioHidden.value = nombre;
      document.getElementById('search-entregador').focus();
    }
  });

  searchDestInput.addEventListener('input', function(){
    const pos = this.selectionStart;
    this.value = this.value.toUpperCase();
    this.setSelectionRange(pos, pos);

    // Mantiene el hidden sincronizado con lo escrito, aunque el
    // usuario no seleccione ninguna sugerencia del dropdown.
    destinatarioHidden.value = this.value.trim();
  });

  searchDestInput.addEventListener('blur', function(){
    // Por si el usuario escribe y sale del campo sin disparar 'input'
    // (ej. autocompletado del navegador).
    if(this.value.trim()) destinatarioHidden.value = this.value.trim();
  });

  /* ════════════════════════════════════════════════════════════
     4. ENTREGADO POR
     ════════════════════════════════════════════════════════════ */
  const entregadorInput  = document.getElementById('search-entregador');
  const entregadorList   = document.getElementById('entregador-list');
  const entregadorHidden = document.getElementById('entregado_por');

  makeDropdown({
    inputEl:  entregadorInput,
    listEl:   entregadorList,
    hiddenEl: entregadorHidden,
    endpoint: ROUTES.usuarios,
    paramName:'q',
    labelFn:  u => u.name ?? u.nombre ?? u.nombre_completo ?? '',
    descFn:   null,
    minChars: 0,
    onSelect: () => {}
  });

  entregadorInput.addEventListener('focus', function(){
    if(!this.value.trim()) this.dispatchEvent(new Event('input'));
  });

  /* ════════════════════════════════════════════════════════════
     5. PREVIEW IMAGEN
     ════════════════════════════════════════════════════════════ */
  document.getElementById('image-upload').addEventListener('change', ev => {
    const file = ev.target.files[0];
    if(file && file.type.startsWith('image/')){
      const reader = new FileReader();
      reader.onload = e => {
        const img = document.getElementById('preview-icon');
        const txt = document.getElementById('preview-text');
        const box = document.querySelector('.image-preview');
        img.src = e.target.result;
        box.classList.add('is-image');
        if(txt) txt.style.display = 'none';
      };
      reader.readAsDataURL(file);
    }
  });

  /* ════════════════════════════════════════════════════════════
     6. FIRMA DIGITAL
     ════════════════════════════════════════════════════════════ */
  const canvas      = document.getElementById('firmaCanvas');
  const ctx         = canvas.getContext('2d');
  const firmaInput  = document.getElementById('firmaInput');
  const placeholder = document.getElementById('sigPlaceholder');
  let drawing = false, last = null;
  const MIN_W = 1.2, MAX_W = 3.8;

  function fitCanvas(){
    const rect  = canvas.getBoundingClientRect();
    const ratio = window.devicePixelRatio || 1;
    canvas.width  = rect.width  * ratio;
    canvas.height = rect.height * ratio;
    ctx.setTransform(ratio,0,0,ratio,0,0);
    ctx.lineCap = 'round'; ctx.lineJoin = 'round';
    ctx.strokeStyle = '#111827'; ctx.imageSmoothingEnabled = true;
  }
  fitCanvas();
  window.addEventListener('resize', fitCanvas);

  function pos(ev){
    const r = canvas.getBoundingClientRect();
    const e = ev.touches ? ev.touches[0] : ev;
    return { x: e.clientX - r.left, y: e.clientY - r.top, t: Date.now() };
  }
  function strokeWidth(p1,p2){
    const v = Math.hypot(p2.x-p1.x, p2.y-p1.y) / Math.max(1, p2.t-p1.t);
    return Math.min(MAX_W, Math.max(MIN_W, MAX_W - v*2.8));
  }

  canvas.addEventListener('mousedown',  ev => { drawing=true; last=pos(ev); placeholder.style.display='none'; ev.preventDefault(); });
  canvas.addEventListener('mousemove',  ev => { if(!drawing) return; const p=pos(ev); ctx.beginPath(); ctx.moveTo(last.x,last.y); ctx.lineTo(p.x,p.y); ctx.lineWidth=strokeWidth(last,p); ctx.stroke(); last=p; ev.preventDefault(); });
  window.addEventListener('mouseup',    ()  => { if(!drawing) return; drawing=false; last=null; firmaInput.value=canvas.toDataURL('image/png'); });
  canvas.addEventListener('touchstart', ev => { drawing=true; last=pos(ev); placeholder.style.display='none'; ev.preventDefault(); }, {passive:false});
  canvas.addEventListener('touchmove',  ev => { if(!drawing) return; const p=pos(ev); ctx.beginPath(); ctx.moveTo(last.x,last.y); ctx.lineWidth=strokeWidth(last,p); ctx.stroke(); last=p; ev.preventDefault(); }, {passive:false});
  window.addEventListener('touchend',   ()  => { if(!drawing) return; drawing=false; last=null; firmaInput.value=canvas.toDataURL('image/png'); });

  document.getElementById('limpiarFirma').addEventListener('click', () => {
    const r = canvas.getBoundingClientRect();
    ctx.clearRect(0,0,r.width,r.height);
    firmaInput.value = '';
    placeholder.style.display = 'grid';
  });

  /* ════════════════════════════════════════════════════════════
     7. BOTTOM SHEET (móvil)
     ════════════════════════════════════════════════════════════ */
  const sheet    = document.getElementById('sheet');
  const backdrop = document.getElementById('sheetBackdrop');
  function showSheet(v){ backdrop.style.display = v?'block':'none'; sheet.style.bottom = v?'0':'-100%'; }
  document.getElementById('openSheet').addEventListener('click', ()=> showSheet(true));
  backdrop.addEventListener('click', ()=> showSheet(false));

  /* ════════════════════════════════════════════════════════════
     8. VALIDACIÓN COMPLETA ANTES DE ENVIAR
     ════════════════════════════════════════════════════════════
     Todos los campos son obligatorios EXCEPTO "Observaciones".
     El número de serie ahora se valida contra el arreglo
     "selectedSeries" (debe tener al menos un producto), en vez de
     un único input hidden.
     ════════════════════════════════════════════════════════════ */
  const form         = document.querySelector('form[action="{{ route('entregas.store') }}"]');
  const formErrorBox = document.getElementById('form-error');
  const firmaError   = document.getElementById('firma-error');

  function marcarCampo(el, invalido){
    const field = el.closest('.field') || el.closest('.dd-wrap');
    if(!field) return;
    field.style.borderColor = invalido ? 'var(--warn)' : '';
    if(field.classList.contains('field')) field.style.boxShadow = invalido ? '0 0 0 3px rgba(239,68,68,.12)' : '';
  }

  form.addEventListener('submit', function(ev){
    const errores = [];

    const campos = [
      { el: document.getElementById('guia_id'), nombre: 'Guía (número de rastreo)', target: inputSearch },
      { el: document.getElementById('peso'),    nombre: 'Peso total',               target: document.getElementById('peso') },
      { el: document.getElementById('fecha'),   nombre: 'Fecha',                    target: document.getElementById('fecha') },
      { el: destinatarioHidden,                  nombre: 'Destinatario',              target: searchDestInput },
      { el: entregadorHidden,                    nombre: 'Entregado por',             target: entregadorInput },
    ];

    campos.forEach(c => {
      const vacio = !c.el || !String(c.el.value || '').trim();
      marcarCampo(c.target, vacio);
      if(vacio) errores.push(c.nombre);
    });

    // Número de serie: debe haber al menos un producto seleccionado
    if(!selectedSeries.length){
      marcarCampo(searchSerieInput, true);
      errores.push('Número de serie (agrega al menos un producto)');
    } else {
      marcarCampo(searchSerieInput, false);
    }

    // Imagen (input file real, sí lo valida el navegador, pero lo
    // reforzamos aquí para incluirlo en el mensaje agrupado)
    const imagenInput = document.getElementById('image-upload');
    const sinImagen = !imagenInput.files || imagenInput.files.length === 0;
    if(sinImagen) errores.push('Imagen');

    // Firma digital
    const sinFirma = !firmaInput.value;
    firmaError.style.display = sinFirma ? 'block' : 'none';
    if(sinFirma) errores.push('Firma digital');

    if(errores.length){
      ev.preventDefault();
      formErrorBox.style.display = 'block';
      formErrorBox.textContent = 'Faltan campos obligatorios: ' + errores.join(', ') + '.';
      formErrorBox.scrollIntoView({ behavior:'smooth', block:'center' });
      return false;
    }

    formErrorBox.style.display = 'none';
  });

  // Quita el resaltado de error en cuanto el usuario corrige el campo
  [inputSearch, document.getElementById('peso'), document.getElementById('fecha'),
   searchSerieInput, searchDestInput, entregadorInput]
   .forEach(el => el && el.addEventListener('input', () => marcarCampo(el, false)));
});
</script>
@endsection