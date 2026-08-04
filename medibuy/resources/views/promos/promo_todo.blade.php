{{-- resources/views/promociones/whatsapp-direct.blade.php --}}
@extends('layouts.app')
<meta name="csrf-token" content="{{ csrf_token() }}">
@section('titulo', 'Promocionales')
@section('content')

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<style>
  :root {
    --bg: #f5f9ff; 
    --card: #ffffff; 
    --line: #dbeafe;
    --brand: #cfe7ff; 
    --brand-ink: #0b4a8f;
    --ink: #123b66; 
    --muted: #5b728a;
    --ok: #c7f9cc; 
    --ok-ink: #166534;
    --warn: #ffedd5; 
    --warn-ink: #9a3412;
    --err: #ffeeef; 
    --err-ink: #991b1b;
    --radius: 16px; 
    --shadow: 0 12px 32px rgba(8, 32, 67, .08);
  }

  body { 
    background: var(--bg); 
  }

  .wrap {
    max-width: 1400px;
    margin: 18px auto;
    padding: 0 16px;
  }

  /* Tarjeta Contenedora Principal */
  .card {
    background: var(--card);
    border: 1px solid var(--line);
    border-radius: var(--radius);
    box-shadow: var(--shadow);
    overflow: hidden;
    margin-bottom: 24px;
  }

  /* Encabezado Principal */
  .card-head {
    padding: 20px 24px;
    border-bottom: 1px solid var(--line);
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
  }

  .hero {
    background: radial-gradient(1200px 160px at 10% -20%, #e8f3ff 0%, transparent 60%),
                radial-gradient(1200px 160px at 90% -20%, #e6f5ff 0%, transparent 60%),
                linear-gradient(180deg, #ffffff 0%, #ffffff 100%);
  }

  .card-body {
    padding: 24px;
  }

  .dot {
    width: 10px;
    height: 10px;
    border-radius: 999px;
    background: #2563eb;
    animation: pulse 1.6s infinite;
  }

  @keyframes pulse {
    0% { transform: scale(.9); opacity: .8; }
    50% { transform: scale(1); opacity: 1; }
    100% { transform: scale(.9); opacity: .8; }
  }

  h3 {
    margin: 0;
    font-size: 22px;
    font-weight: 700;
    letter-spacing: -.3px;
    color: #1e293b;
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .tag {
    background: #eff7ff;
    border: 1px solid #dbeafe;
    color: #1e3a8a;
    padding: 4px 10px;
    border-radius: 10px;
    font-weight: 700;
    font-size: 12px;
  }

  /* Botones de Navegación Superiores */
  .header-nav-actions {
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .ft-btn-nav {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    border-radius: 999px;
    background: #ffffff;
    border: 1px solid #dbe5f0;
    color: #1e293b;
    box-shadow: 0 4px 12px rgba(15, 23, 42, 0.04);
    cursor: pointer;
    transition: all 0.2s ease;
    text-decoration: none;
  }

  .ft-btn-nav:hover {
    background: #f1f5f9;
    border-color: #cbd5e1;
    transform: translateY(-1px);
    color: #2563eb;
  }

  /* Formularios y Estructura */
  .grid {
    display: grid;
    gap: 20px;
  }

  .form-group {
    display: flex;
    flex-direction: column;
    gap: 6px;
  }

  @media (min-width: 1000px) { 
    .grid-2 { grid-template-columns: 1.1fr 0.9fr; } 
  }

  label {
    font-size: 13px;
    font-weight: 700;
    color: var(--ink);
  }

  .inp, textarea, select {
    width: 100%;
    border: 1px solid #dbe5f0;
    background: #fff;
    border-radius: 14px;
    padding: 12px 16px;
    font-size: 14px;
    color: #1e293b;
    outline: none;
    transition: all 0.15s ease;
  }

  .inp:focus, textarea:focus, select:focus {
    box-shadow: 0 0 0 4px rgba(191, 227, 255, .4);
    border-color: #93c5fd;
  }

  textarea {
    height: 120px;
    resize: none;
    overflow-y: auto;
  }

  /* Botones del Sistema */
  .btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    border: 0;
    border-radius: 14px;
    padding: 12px 22px;
    font-weight: 700;
    font-size: 14px;
    cursor: pointer;
    background: var(--brand);
    color: var(--brand-ink);
    box-shadow: 0 4px 12px rgba(8, 32, 67, 0.05);
    transition: all 0.2s ease;
    text-decoration: none;
  }

  .btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 16px rgba(8, 32, 67, 0.12);
    filter: brightness(0.97);
  }

  .btn-ghost {
    background: #fff;
    border: 1px solid #dbe5f0;
    color: var(--ink);
    border-radius: 14px;
    padding: 10px 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.15s ease;
  }

  .btn-ghost:hover {
    background: #f8fafc;
    border-color: #cbd5e1;
  }

  .btn-lite {
    background: #eff4ff;
    border: 1px solid #cfe0ff;
    color: #2563eb;
    border-radius: 12px;
    padding: 8px 14px;
    font-weight: 700;
    font-size: 13px;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.15s ease;
  }

  .btn-lite:hover {
    background: #e0ecff;
  }

  /* Alertas */
  .alert {
    padding: 14px 16px;
    border-radius: 14px;
    border: 1px solid;
    font-size: 14px;
    line-height: 1.4;
  }
  .alert.ok { background: var(--ok); border-color: #bbf7d0; color: var(--ok-ink); }
  .alert.warn { background: var(--warn); border-color: #fed7aa; color: var(--warn-ink); }
  .alert.err { background: var(--err); border-color: #fecaca; color: var(--err-ink); }

  /* Dropzone de Imagen */
  .dropzone {
    position: relative;
    border: 2px dashed #cfe0ff;
    border-radius: 16px;
    background: linear-gradient(180deg, #fcfeff, #f7fbff);
    padding: 16px;
    display: grid;
    grid-template-columns: 120px 1fr;
    gap: 16px;
    align-items: center;
    transition: all 0.2s ease;
  }

  .dropzone.dragover {
    border-color: #2563eb;
    background: #f0f7ff;
    transform: scale(1.005);
  }

  .dz-thumb {
    width: 120px;
    height: 120px;
    border-radius: 12px;
    border: 1px solid #dbe5f0;
    background: #f1f5f9;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    position: relative;
    cursor: pointer;
    gap: 4px;
  }

  .dz-thumb img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    animation: fadein .2s ease;
  }

  .dz-thumb i {
    font-size: 24px;
    color: #94a3b8;
  }

  @keyframes fadein {
    from { opacity: .6; transform: scale(.98); }
    to { opacity: 1; transform: scale(1); }
  }

  .dz-actions {
    display: flex;
    gap: 8px;
    margin-top: 8px;
  }

  .dz-remove {
    background: #fff;
    border: 1px solid #e2e8f0;
    color: #ef4444;
    font-weight: 600;
    border-radius: 10px;
    padding: 6px 12px;
    cursor: pointer;
    font-size: 12px;
    transition: all 0.15s ease;
  }

  .dz-remove:hover {
    background: #fef2f2;
    border-color: #fca5a5;
  }

  /* Tabla de Destinatarios */
  .table-wrap {
    border: 1px solid var(--line);
    border-radius: 14px;
    max-height: 380px;
    overflow-y: auto;
    overflow-x: auto;
    background: #fff;
    box-shadow: inset 0 2px 4px rgba(0,0,0,0.02);
  }

  .table-wrap thead th {
    position: sticky;
    top: 0;
    z-index: 10;
    background: #f8fafc;
    border-bottom: 2px solid var(--line);
  }

  table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
  }

  th, td {
    padding: 12px 14px;
    border-bottom: 1px solid var(--line);
    font-size: 14px;
    color: #334155;
  }

  th {
    font-weight: 700;
    color: var(--ink);
    text-align: left;
  }

  tr:hover td {
    background: #f8fafc;
  }

  tr.picked td {
    background: #eff6ff;
  }

  .rowcheck {
    transform: scale(1.15);
    cursor: pointer;
  }

  /* Tarjeta Lateral de Control (Sticky) */
  .side-card {
    border: 1px solid var(--line);
    border-radius: var(--radius);
    background: #fff;
    box-shadow: var(--shadow);
    align-self: start;
  }

  .side-head {
    padding: 18px 20px;
    border-bottom: 1px solid var(--line);
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  .side-body {
    padding: 20px;
  }

  @media (min-width: 1000px) { 
    .side-sticky { position: sticky; top: 24px; } 
  }

  /* Listado de Chips */
  .chips {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    margin-top: 12px;
    max-height: 260px;
    overflow-y: auto;
    padding-right: 4px;
  }

  .chip {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #eff6ff;
    border: 1px solid #cfe0ff;
    color: #1e40af;
    border-radius: 999px;
    padding: 5px 12px;
    font-size: 12px;
    font-weight: 600;
  }

  .chip .x {
    cursor: pointer;
    opacity: .6;
    font-weight: 700;
    transition: opacity 0.15s;
    margin-left: 2px;
  }

  .chip .x:hover { opacity: 1; color: #ef4444; }

  .muted { color: var(--muted); font-size: 13px; }
  .mini { font-size: 12px; color: var(--muted); }
  .mono { font-family: ui-monospace, SFMono-Regular, Consolas, monospace; }
  /* PREVISUALIZACION WHATSAPP */

.wa-preview{
  border:1px solid #dbeafe;
  border-radius:16px;
  overflow:hidden;
  margin-bottom:18px;
  background:#fff;
}

.wa-header{
  background:#075e54;
  color:#fff;
  padding:10px 14px;
  font-weight:600;
  display:flex;
  align-items:center;
  gap:8px;
}

.wa-chat-bg{
  background:#ece5dd;
  padding:18px;
  min-height:260px;
}

.wa-message{
  background:#dcf8c6;
  border-radius:10px;
  max-width:280px;
  margin-left:auto;
  overflow:hidden;
  box-shadow:0 2px 6px rgba(0,0,0,.08);
}

.wa-image{
  width:100%;
  display:block;
  object-fit:cover;
  max-height:220px;
}

.wa-text{
  padding:10px 12px 4px;
  white-space:pre-wrap;
  word-break:break-word;
  font-size:14px;
  color:#111827;
}

.wa-time{
  text-align:right;
  padding:0 10px 8px;
  font-size:11px;
  color:#6b7280;
}
</style>

<div class="wrap">
  <div class="card">
    
    <div class="card-head hero">
      <div style="display:flex; align-items:center; gap:14px">
        <div class="header-nav-actions">
          <a href="javascript:history.back()" class="ft-btn-nav" title="Volver atrás">
            <i class="bi bi-arrow-left"></i>
          </a>
          <a href="{{ url('/home') }}" class="ft-btn-nav" title="Ir al inicio">
            <i class="bi bi-house-door-fill"></i>
          </a>
        </div>
        <span class="dot"></span>
        <h3>Promocionales WhatsApp <span class="tag">Imagen + variables</span></h3>
      </div>
    </div>

    <div class="card-body grid grid-2">
      
      {{-- COLUMNA IZQUIERDA: FORMULARIO Y TABLA --}}
      <div class="grid">

        {{-- Estado y Mensajes de Respuesta --}}
        @if(session('wa_success'))
          <div class="alert ok"><i class="bi bi-check-circle-fill"></i> {{ session('wa_success') }}</div>
        @endif

        @if(session('wa_info'))
          <div class="alert warn"><i class="bi bi-exclamation-triangle-fill"></i> {{ session('wa_info') }}</div>
          @if(session('wa_fail'))
            <details style="margin-top:-6px">
              <summary class="mini" style="cursor:pointer">Ver detalles técnicos</summary>
              <pre class="mono" style="white-space:pre-wrap; background:#f8fafc; border:1px dashed var(--line); border-radius:12px; padding:12px; font-size:12px">{{ json_encode(session('wa_fail'), JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}</pre>
            </details>
          @endif
        @endif

        @if ($errors->any())
          <div class="alert err">
            <div style="font-weight:700; margin-bottom:6px"><i class="bi bi-x-circle-fill"></i> Errores del servidor</div>
            <ul style="margin:0; padding-left:18px">
              @foreach($errors->all() as $e)
                <li>{{ $e }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <div id="liveError" class="alert err" style="display:none"></div>

        {{-- FORMULARIO PRINCIPAL --}}
        <form method="POST" action="{{ route('promos.whatsapp.direct.send') }}" enctype="multipart/form-data" id="sendForm" class="grid">
          @csrf

          {{-- SECCIÓN: COMPONENTE DE IMAGEN --}}
          <div class="form-group">
            <label>Imagen de encabezado</label>
            <div class="dropzone" id="dropzone">
              <div class="dz-thumb" id="dzThumb" title="Click para seleccionar imagen">
                <i class="bi bi-image-fill" id="dzIcon"></i>
                <span class="muted" id="dzPlaceholder">Examinar</span>
              </div>
              <div>
                <div style="font-weight:700; color:#1e293b; margin-bottom:4px">Sube una imagen.</div>
                <div class="muted" style="display:flex; gap:6px; align-items:center; flex-wrap:wrap">
                  <span>Arrastra un archivo multimedia o</span>
                  <button type="button" class="btn-lite" id="btnBrowse"><i class="bi bi-folder2-open"></i> Buscar archivo</button>
                </div>
                <input
    type="file"
    name="imagen_file"
    id="headerInput"
    accept="image/*"
    style="display:none"
>
                @error('imagen_file')<div class="mini" style="color:var(--err-ink); margin-top:6px">{{ $message }}</div>@enderror
                
                <div class="dz-actions">
                  <button class="dz-remove" type="button" id="btnRemoveImg" style="display:none"><i class="bi bi-trash"></i> Quitar imagen</button>
                </div>
                <div class="mini" id="fileMeta" style="margin-top:8px; font-weight: 500;"></div>
              </div>
            </div>
          </div>

          <div class="form-group">
    <label>Producto</label>

    <select id="productoSelect" class="inp">
        <option value="">Selecciona un producto</option>

        @foreach($productos as $producto)
            <option
    value="{{ $producto->id }}"
    data-tipo="{{ $producto->tipo_equipo }}"
    data-marca="{{ $producto->marca }}"
    data-modelo="{{ $producto->modelo }}"
    data-precio="{{ $producto->precio }}"
    data-imagen="{{ $producto->imagen }}"
>
    {{ strtoupper($producto->tipo_equipo) }}
    -
    {{ strtoupper($producto->modelo) }}
    {{ strtoupper($producto->marca) }}
</option>
        @endforeach
    </select>
    <input type="hidden" name="producto_id" id="producto_id">
</div>

          {{-- SECCIÓN: ASISTENTE IA Y REDACCIÓN --}}
          <div class="card" style="padding:16px; background:#fcfdfe; border-color:#e2e8f0; gap:12px; display:grid;">
            <div class="form-group">
  <label><i class="bi bi-magic"></i> Palabras clave para el Redactor IA</label>

  <div style="display:flex; gap:10px; align-items:center;">
    
    <input
      type="text"
      id="keywords"
      class="inp"
      placeholder="Ej. Colonoscopio, 25% descuento, Mayo 2026"
      style="flex:1"
    >

    <button type="button" id="btnVoice" class="btn-lite" style="height:46px">
      <i class="bi bi-mic-fill"></i>
      Dictar
    </button>

    <button type="button" class="btn-lite" id="btnGenerateAI" style="height:46px">
      ✨ Generar texto
    </button>

  </div>
</div>

            <div class="form-group">
              <div style="display:flex; justify-content:space-between; align-items:center;">
  <label>Mensaje o Frase a Enviar</label>

</div>
              <textarea class="inp" name="producto" id="phrase" maxlength="3000" placeholder="Escribe el mensaje o genéralo con el asistente de arriba..." required></textarea>
              @error('producto')<div class="mini" style="color:var(--err-ink)">{{ $message }}</div>@enderror
            </div>
          </div>

          {{-- SECCIÓN: BÚSQUEDA Y FILTRADO --}}
          <div class="form-group" style="margin-top:8px;">
            <label>Destinatarios</label>
            <div style="display:flex; gap:10px; flex-wrap:wrap; margin-bottom:4px">
              <input class="inp" id="quickSearch" placeholder="🔍 Buscar por nombre, teléfono..." style="flex:1; min-width:250px">
              <select class="inp" id="categoryFilter" style="max-width:250px">
                <option value="">Todas las categorías</option>
                @foreach($categorias as $cat)
                  <option value="{{ strtolower($cat->nombre) }}">{{ $cat->nombre }}</option>
                @endforeach
              </select>
            </div>
          </div>

          {{-- HERRAMIENTAS DE SELECCIÓN EN LÍNEA --}}
          <div style="display:flex; gap:8px; align-items:center; flex-wrap:wrap">
            <span class="mini" style="font-weight:600; margin-right:auto">Selecciona destinatarios (se guarda en tu navegador).</span>
            <button class="btn-lite" type="button" id="checkPage"><i class="bi bi-check2-square"></i> Seleccionar todos</button>
            <button class="btn-lite" type="button" id="uncheckPage"><i class="bi bi-square"></i> Desmarcar todos</button>
            <button class="btn-lite" type="button" id="invertPage"><i class="bi bi-arrow-left-right"></i> Invertir</button>
          </div>

          {{-- TABLA DE CLIENTES --}}
          <div class="table-wrap">
            <table id="clientsTable">
              <thead>
                <tr>
                  <th style="width:44px; text-align:center;"><input type="checkbox" id="checkAllPage" class="rowcheck"></th>
                  <th>Nombre Completo</th>
                  <th>Teléfono</th>
                  <th>Categoría</th>
                </tr>
              </thead>
              <tbody>
                @forelse ($clientes as $c)
                  @php $nombre = trim(($c->nombre ?? '').' '.($c->apellido ?? '')); @endphp
                  <tr data-name="{{ $nombre }}" data-phone="{{ $c->telefono ?? '' }}" data-category="{{ strtolower($c->categoria->nombre ?? '') }}">
                    <td style="text-align:center;"><input type="checkbox" value="{{ $c->id }}" class="rowcheck"></td>
                    <td style="font-weight:600; color:#1e293b">{{ $nombre ?: '—' }}</td>
                    <td class="mono">{{ $c->telefono ?? '—' }}</td>
                    <td><span class="tag" style="background:#f1f5f9; color:#475569; border:0">{{ $c->categoria->nombre ?? 'Sin categoría' }}</span></td>
                  </tr>
                @empty
                  <tr><td colspan="4" class="muted" style="text-align:center; padding:24px;">No se encontraron registros de clientes disponibles.</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </form>
      </div>

      {{-- COLUMNA DERECHA: PANEL PERSISTENTE DE ENVÍO --}}
      <div class="side-card side-sticky">
        <div class="side-head">
          <div style="display:flex; gap:8px; align-items:center">
            <strong style="color:#1e293b">Seleccionados</strong>
            <span class="tag" id="selCounterBadgeSide" style="background:#2563eb; color:#fff; border:0; padding:2px 8px; border-radius:8px">0</span>
          </div>
          <button class="btn-ghost" type="button" id="clearAll" style="padding:6px 12px; font-size:12px; color:#ef4444"><i class="bi bi-x-circle"></i> Limpiar todo</button>
        </div>
        <div class="side-body">
          <div class="muted" style="margin-bottom:8px; font-weight:600">Acciones rápidas en lote:</div>
          <div style="display:flex; gap:6px; flex-wrap:wrap; margin-bottom:12px">
            <button class="btn-lite" type="button" id="sideCheckPage">Seleccionar</button>
            <button class="btn-lite" type="button" id="sideUncheckPage">Quitar</button>
            <button class="btn-lite" type="button" id="sideInvertPage">Invertir</button>
          </div>
          
          <div class="wa-preview">
  <div class="wa-header">
    <i class="bi bi-whatsapp"></i>
    Previsualización del mensaje
  </div>

  <div class="wa-chat-bg">
    <div class="wa-message">

      <div id="previewUserImageWrap" style="display:none">
    <div style="padding:6px;font-size:12px;font-weight:bold;background:#f3f4f6">
        Imagen cargada
    </div>

    <img id="previewUserImage" class="wa-image" src="">
</div>

<div id="previewProductImageWrap" style="display:none">
    <div style="padding:6px;font-size:12px;font-weight:bold;background:#f3f4f6">
        Imagen del producto
    </div>

    <img id="previewProductImage" class="wa-image" src="">
</div>

      <div id="previewText" class="wa-text">
        Aquí aparecerá tu mensaje promocional...
      </div>

      <div class="wa-time">
        {{ now()->format('H:i') }}
      </div>

    </div>
  </div>
</div>

          <label style="margin-top:14px; display:block">Previsualización de Destinos:</label>
          <div class="chips" id="chipsSide"></div>
          
          <div style="margin-top:20px; border-top:1px solid var(--line); padding-top:16px;">
            <button class="btn" type="button" id="sideSend" style="width:100%; background:#16a34a; color:#fff; padding:14px">
              <i class="bi bi-whatsapp"></i> Enviar
            </button>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>

<script>
(() => {
  /* Helpers UI de errores en vivo */
  const liveError = document.getElementById('liveError');
  const showLiveError = (html) => {
    liveError.innerHTML = html;
    liveError.style.display = 'block';
    liveError.scrollIntoView({behavior:'smooth', block:'center'});
  };
  const clearLiveError = () => {
    liveError.innerHTML = '';
    liveError.style.display = 'none';
  };

  /* LocalStorage Persistencia */
  const STORAGE_KEY = 'promoSelectedIds_v4';
  let selected = new Map();
  const saveSel = () => localStorage.setItem(STORAGE_KEY, JSON.stringify([...selected.values()]));
  const loadSel = () => {
    try { selected = new Map((JSON.parse(localStorage.getItem(STORAGE_KEY) || '[]')).map(o => [String(o.id), o])); }
    catch { selected = new Map(); }
  };

  /* Selectores e Inputs */
  const table = document.getElementById('clientsTable');
  const quickSearch = document.getElementById('quickSearch');
  const categoryFilter = document.getElementById('categoryFilter');
  const checkAll = document.getElementById('checkAllPage');
  const chipsSide = document.getElementById('chipsSide');
  const selBadgeSide = document.getElementById('selCounterBadgeSide');
  const sideSend = document.getElementById('sideSend');
  const form = document.getElementById('sendForm');
  const phrase = document.getElementById('phrase');
  const previewText = document.getElementById('previewText');
  console.log('previewText:', previewText);
  const previewUserImage =
    document.getElementById('previewUserImage');

const previewUserImageWrap =
    document.getElementById('previewUserImageWrap');

const previewProductImage =
    document.getElementById('previewProductImage');

const previewProductImageWrap =
    document.getElementById('previewProductImageWrap');
  const fraseCounter = document.getElementById('fraseCounter');

  /* Botones de control de filas */
  const actions = ['checkPage', 'uncheckPage', 'invertPage', 'sideCheckPage', 'sideUncheckPage', 'sideInvertPage'];
  const [checkPage, uncheckPage, invertPage, sideCheckPage, sideUncheckPage, sideInvertPage] = actions.map(id => document.getElementById(id));
  const clearAllBtn = document.getElementById('clearAll');

  const checks = () => [...table.querySelectorAll('.rowcheck')];
  const rowInfo = (tr) => ({ id: String(tr.querySelector('.rowcheck').value), name: tr.dataset.name || 'Cliente', phone: tr.dataset.phone || '' });

  function renderChips(container, max) {
    container.innerHTML = '';
    const arr = [...selected.values()];
    arr.slice(0, max).forEach(o => {
      const el = document.createElement('span');
      el.className = 'chip';
      el.innerHTML = `${o.name} <span class="muted" style="font-size:11px">• ${o.phone || 's/tel'}</span> <span class="x" data-id="${o.id}">×</span>`;
      el.querySelector('.x').onclick = () => { selected.delete(String(o.id)); saveSel(); refresh(); };
      container.appendChild(el);
    });
    if (arr.length > max) {
      const more = document.createElement('span');
      more.className = 'chip';
      more.style.background = '#e2e8f0';
      more.style.color = '#334155';
      more.textContent = `+${arr.length - max} más`;
      container.appendChild(more);
    }
  }

  const visibleRows = () => {
    const q = (quickSearch.value || '').trim().toLowerCase();
    const cat = categoryFilter.value;
    return [...table.querySelectorAll('tbody tr')].filter(tr => {
      if (tr.cells.length === 1) return false; // descarta fila "sin resultados"
      const txt = ((tr.dataset.name || '') + ' ' + (tr.dataset.phone || '')).toLowerCase();
      const rowCat = (tr.dataset.category || '').toLowerCase();
      return (!q || txt.includes(q)) && (!cat || rowCat === cat);
    });
  };

  function refresh() {
    checks().forEach(cb => {
      const on = selected.has(String(cb.value));
      cb.checked = on;
      cb.closest('tr').classList.toggle('picked', on);
    });
    if (selBadgeSide) selBadgeSide.textContent = selected.size;
    renderChips(chipsSide, 40);
    const vis = visibleRows();
    checkAll.checked = vis.length && vis.every(tr => selected.has(rowInfo(tr).id));
  }

  function selectVis() { visibleRows().forEach(tr => selected.set(rowInfo(tr).id, rowInfo(tr))); saveSel(); refresh(); }
  function unselectVis() { visibleRows().forEach(tr => selected.delete(rowInfo(tr).id)); saveSel(); refresh(); }
  function invertVis() { visibleRows().forEach(tr => { const { id } = rowInfo(tr); selected.has(id) ? selected.delete(id) : selected.set(id, rowInfo(tr)); }); saveSel(); refresh(); }

  /* Eventos y Vinculación */
  loadSel(); 
  refresh();

  table.addEventListener('change', e => {
    const cb = e.target.closest('.rowcheck'); if (!cb) return;
    const info = rowInfo(cb.closest('tr'));
    cb.checked ? selected.set(info.id, info) : selected.delete(info.id);
    saveSel(); refresh();
  });

  checkAll.addEventListener('change', e => { e.target.checked ? selectVis() : unselectVis(); });
  checkPage.addEventListener('click', selectVis);
  uncheckPage.addEventListener('click', unselectVis);
  invertPage.addEventListener('click', invertVis);
  sideCheckPage.addEventListener('click', selectVis);
  sideUncheckPage.addEventListener('click', unselectVis);
  sideInvertPage.addEventListener('click', invertVis);
  
  clearAllBtn.addEventListener('click', () => {
    selected.clear();
    saveSel();
    refresh();
  });

  function filterClients() {
    const q = quickSearch.value.trim().toLowerCase();
    const cat = categoryFilter.value;
    const rows = [...table.querySelectorAll('tbody tr')];
    
    rows.forEach(tr => {
      if (tr.cells.length === 1) return;
      const txt = ((tr.dataset.name || '') + ' ' + (tr.dataset.phone || '')).toLowerCase();
      const rowCat = (tr.dataset.category || '').toLowerCase();
      tr.style.display = (!q || txt.includes(q)) && (!cat || rowCat === cat) ? '' : 'none';
    });
    refresh();
  }

  quickSearch.addEventListener('input', filterClients);
  categoryFilter.addEventListener('change', filterClients);

  window.addEventListener('keydown', e => {
    if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
      e.preventDefault(); quickSearch.focus();
    }
  });

  /* Dropzone Lógica Visual */
  const dz = document.getElementById('dropzone');
  const dzThumb = document.getElementById('dzThumb');
  const dzIcon = document.getElementById('dzIcon');
  const dzPlaceholder = document.getElementById('dzPlaceholder');
  const inputFile = document.getElementById('headerInput');
  const btnRemoveImg = document.getElementById('btnRemoveImg');
  const btnBrowse = document.getElementById('btnBrowse');
  const fileMeta = document.getElementById('fileMeta');

  function setThumb(file) {
    const reader = new FileReader();
    reader.onload = e => {
      dzThumb.innerHTML = `<img src="${e.target.result}" alt="preview">`;
      btnRemoveImg.style.display = 'inline-block';
    };
    reader.readAsDataURL(file);
    const mb = (file.size / 1024 / 1024).toFixed(2);
    fileMeta.textContent = `Archivo: ${file.name} • ${mb} MB`;
    clearLiveError();

    if (!String(file.type || '').startsWith('image/')) {
      showLiveError('El archivo seleccionado no es válido. Debe ser una imagen.');
    }
    if (parseFloat(mb) > 24) {
      showLiveError(`Alerta: La imagen pesa ${mb} MB. Si excede la cuota de PHP o WhatsApp, el servidor rechazará la petición.`);
    }
    const previewReader = new FileReader();

previewReader.onload = e => {
  previewUserImage.src = e.target.result;
previewUserImageWrap.style.display = 'block';
};

previewReader.readAsDataURL(file);
  }

  function clearThumb() {
    dzThumb.innerHTML = '';
    dzThumb.appendChild(dzIcon);
    dzThumb.appendChild(dzPlaceholder);
    inputFile.value = '';
    btnRemoveImg.style.display = 'none';
    fileMeta.textContent = '';
    previewUserImage.src = '';
    previewUserImageWrap.style.display = 'none';
  }

  dzThumb.addEventListener('click', (e) => { e.preventDefault(); inputFile.click(); });
  btnBrowse.addEventListener('click', (e) => { e.preventDefault(); inputFile.click(); });
  dz.addEventListener('dragover', e => { e.preventDefault(); dz.classList.add('dragover'); });
  dz.addEventListener('dragleave', () => dz.classList.remove('dragover'));
  dz.addEventListener('drop', e => {
    e.preventDefault(); dz.classList.remove('dragover');
    const f = e.dataTransfer.files?.[0]; if (!f) return;
    if (!String(f.type || '').startsWith('image/')) return;
    inputFile.files = e.dataTransfer.files;
    setThumb(f);
  });

  inputFile.addEventListener('change', () => {

    const f = inputFile.files?.[0];

    if(f){
        setThumb(f);
    }

    

});
  btnRemoveImg.addEventListener('click', (e) => { e.preventDefault(); clearThumb(); });

  /* Contador de caracteres y vista previa */
phrase.addEventListener('input', () => {
  if (fraseCounter) {
    fraseCounter.textContent = phrase.value.length;
}

  previewText.textContent =
    phrase.value.trim() ||
    'Aquí aparecerá tu mensaje promocional...';
});

  /* Submit e Inyección de IDS masivos */
  function injectIds() {
    form.querySelectorAll('input[name="clientes_ids[]"]').forEach(el => el.remove());
    for (const { id } of selected.values()) {
      const h = document.createElement('input');
      h.type = 'hidden'; h.name = 'clientes_ids[]'; h.value = id; form.appendChild(h);
    }
  }

  function validateBeforeSubmit() {
    const errs = [];
    const f = inputFile.files?.[0];
    if (!f) errs.push('Falta seleccionar la imagen promocional.');
    if (!phrase.value.trim()) errs.push('El campo del mensaje está vacío.');
    if (selected.size === 0) errs.push('Selecciona al menos un destinatario de la lista.');

    if (errs.length) {
      showLiveError('<div style="font-weight:700;margin-bottom:4px">Verifica los siguientes puntos:</div><ul style="margin:0;padding-left:16px">' + errs.map(e => `<li>${e}</li>`).join('') + '</ul>');
      return false;
    }
    clearLiveError();
    return true;
  }

  sideSend.addEventListener('click', (e) => {
    e.preventDefault();
    injectIds();
    if (!validateBeforeSubmit()) return;
    form.submit();
  });
  const productoSelect = document.getElementById('productoSelect');
const productoIdInput = document.getElementById('producto_id');

productoSelect.addEventListener('change', function(){

    const option = this.options[this.selectedIndex];

    if(!option.value){
        return;
    }

    const id = option.value;

const tipo = option.dataset.tipo || '';
const marca = option.dataset.marca || '';
const modelo = option.dataset.modelo || '';
const precio = option.dataset.precio || '';
const imagen = option.dataset.imagen || '';

productoIdInput.value = id;

keywordsInput.value =
`${tipo}
${marca} ${modelo }
Precio: $${precio}`;


    // Mostrar imagen del producto
    if(imagen){

    previewProductImage.src = imagen;
    previewProductImageWrap.style.display = 'block';

}

});
})();

/* Generador de Promociones con Inteligencia Artificial */
const btnGenerateAI = document.getElementById('btnGenerateAI');
const keywordsInput = document.getElementById('keywords');
const phraseInput = document.getElementById('phrase');

btnGenerateAI.addEventListener('click', async () => {
  const keywords = keywordsInput.value.trim();
  if (!keywords) {
    alert('Por favor ingresa palabras clave primero.');
    return;
  }

  btnGenerateAI.disabled = true;
  btnGenerateAI.innerHTML = 'Generando...';

  try {
    const response = await fetch('/ia/generar-promocion', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
      },
      body: JSON.stringify({ keywords })
    });

    const data = await response.json();

console.log(data);

phraseInput.value = data.texto;

const contador = document.getElementById('fraseCounter');
const preview = document.getElementById('previewText');

console.log('contador:', contador);
console.log('preview:', preview);

if (contador) {
    contador.textContent = data.texto.length;
}

if (preview) {
    preview.textContent = data.texto;
}
  } catch (error) {
    
    console.error(error);
  } finally {
    btnGenerateAI.disabled = false;
    btnGenerateAI.innerHTML = '✨ Generar texto';
  }
});
/* Dictado por voz */
const btnVoice = document.getElementById('btnVoice');

if ('webkitSpeechRecognition' in window || 'SpeechRecognition' in window) {

  const SpeechRecognition =
    window.SpeechRecognition ||
    window.webkitSpeechRecognition;

  const recognition = new SpeechRecognition();

  recognition.lang = 'es-ES';
  recognition.continuous = true;
  recognition.interimResults = true;

  let listening = false;

  btnVoice.addEventListener('click', () => {

    if (!listening) {
      recognition.start();
    } else {
      recognition.stop();
    }

  });

  recognition.onstart = () => {
    listening = true;
    btnVoice.innerHTML =
      '<i class="bi bi-mic-mute-fill"></i> Detener';
  };

  recognition.onend = () => {
    listening = false;
    btnVoice.innerHTML =
      '<i class="bi bi-mic-fill"></i> Dictar';
  };

  let textoFinal = '';

recognition.onresult = (event) => {

  let textoTemporal = '';

  for (let i = event.resultIndex; i < event.results.length; i++) {

    const texto = event.results[i][0].transcript;

    if (event.results[i].isFinal) {
      textoFinal += texto + ' ';
    } else {
      textoTemporal += texto;
    }
  }

  keywordsInput.value = textoFinal + textoTemporal;

  fraseCounter.textContent =
    phraseInput.value.length;

  previewText.textContent =
    phraseInput.value || 'Aquí aparecerá tu mensaje promocional...';
};

} else {

  btnVoice.style.display = 'none';

  console.warn(
    'El navegador no soporta reconocimiento de voz.'
  );
}
</script>
@endsection