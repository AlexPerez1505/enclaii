@extends('layouts.app')
@section('titulo','Orden Servicio')
@section('title','Nuevo Mantenimiento')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@500;600;700&display=swap" rel="stylesheet">
{{-- Se comenta la hoja de estilos original para forzar el nuevo sistema de diseño corporativo --}}
{{-- <link rel="stylesheet" href="{{ asset('css/servicio.css') }}?v={{ time() }}"> --}}

@php
  $usuariosServicio = \App\Models\User::query()
      ->orderBy('name')
      ->get(['id', 'name']);
@endphp

<style>
  :root {
    --bg: #f9fafb;
    --card: #ffffff;
    --ink: #333333;
    --ink-dark: #111111;
    --muted: #888888;
    --line: #ebebeb;
    --blue: #007aff;
    --blue-soft: #e6f0ff;
    --success: #15803d;
    --success-soft: #e6ffe6;
    --danger: #ff4a4a;
    --danger-soft: #ffebeb;
    --font-main: 'Quicksand', sans-serif;
  }

  /* Reset y Base */
  * {
    box-sizing: border-box;
    font-family: var(--font-main);
  }

  body, .page {
    background-color: var(--bg);
    color: var(--ink);
    -webkit-font-smoothing: antialiased;
  }

  .wrap {
    max-width: 1000px;
    margin: 40px auto;
    padding: 0 24px;
  }

  /* Header */
  .head {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    margin-bottom: 32px;
  }

  .hgroup h2 {
    font-size: 28px;
    font-weight: 700;
    color: var(--ink-dark);
    margin: 0 0 8px 0;
  }

  .hgroup p {
    color: var(--muted);
    font-size: 15px;
    margin: 0;
    font-weight: 500;
  }

  .back-link {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: var(--muted);
    text-decoration: none;
    font-weight: 600;
    font-size: 14px;
    transition: color 0.2s;
  }
  .back-link:hover { color: var(--ink-dark); }

  /* Card Premium */
  .panel {
    background: var(--card);
    border: 1px solid var(--line);
    border-radius: 16px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.02);
    padding: 40px;
    position: relative;
  }

  /* Progress Bar UI (Agregado para el JS) */
  .progress-wrap {
    margin-bottom: 32px;
    padding-bottom: 24px;
    border-bottom: 1px solid var(--line);
  }
  .progress-track {
    background: var(--line);
    height: 6px;
    border-radius: 999px;
    margin-bottom: 12px;
    overflow: hidden;
  }
  #bar {
    height: 100%;
    background: var(--blue);
    width: 25%;
    border-radius: 999px;
    transition: width 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  }
  .progress-texts {
    display: flex;
    justify-content: space-between;
    font-size: 13px;
    font-weight: 700;
    color: var(--muted);
    text-transform: uppercase;
    letter-spacing: 0.05em;
  }

  /* Inputs & Formularios */
  .grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 20px;
  }

  .field {
    position: relative;
    display: flex;
    flex-direction: column-reverse;
    margin-bottom: 20px;
  }

  .field label {
    font-size: 13px;
    font-weight: 600;
    color: var(--muted);
    margin-bottom: 6px;
    transition: color 0.2s;
  }

  .field input, .field select, .field textarea {
    background: var(--card);
    border: 1px solid var(--line);
    border-radius: 8px;
    padding: 12px 16px;
    font-size: 15px;
    font-weight: 500;
    color: var(--ink-dark);
    width: 100%;
    transition: all 0.2s;
    font-family: var(--font-main);
  }

  .field input:focus, .field select:focus, .field textarea:focus {
    outline: none;
    border-color: var(--blue);
    box-shadow: 0 0 0 3px var(--blue-soft);
  }

  .field input:focus + label, .field select:focus + label, .field textarea:focus + label {
    color: var(--blue);
  }

  .hint {
    font-size: 12px;
    color: var(--muted);
    margin-top: 6px;
    font-weight: 500;
  }

  /* Combobox Custom */
  .combo { position: relative; }
  .combo-list {
    position: absolute;
    top: calc(100% + 4px);
    left: 0;
    width: 100%;
    background: var(--card);
    border: 1px solid var(--line);
    border-radius: 12px;
    box-shadow: 0 8px 24px rgba(0,0,0,0.08);
    max-height: 250px;
    overflow-y: auto;
    z-index: 100;
    display: none;
    padding: 8px;
  }
  .combo-list.show { display: block; animation: slideDown 0.2s ease; }
  @keyframes slideDown {
    from { opacity: 0; transform: translateY(-5px); }
    to { opacity: 1; transform: translateY(0); }
  }
  .combo-item {
    padding: 10px 14px;
    border-radius: 8px;
    cursor: pointer;
    transition: background 0.2s;
  }
  .combo-item:hover, .combo-item.active {
    background: var(--bg);
  }
  .chipSel {
    display: inline-flex;
    align-items: center;
    background: var(--blue-soft);
    color: var(--blue);
    padding: 6px 14px;
    border-radius: 999px;
    font-size: 13px;
    font-weight: 700;
  }
  .chipSel .x {
    margin-left: 8px;
    cursor: pointer;
    font-size: 16px;
  }

  /* Botones */
  .actions {
    display: flex;
    justify-content: flex-end;
    gap: 12px;
    margin-top: 32px;
  }

  .btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 12px 24px;
    border-radius: 999px; /* Aspecto de píldora */
    font-weight: 700;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.2s;
    border: none;
    font-family: var(--font-main);
  }

  .btn:active { transform: scale(0.98); }

  .btn-primary { background: var(--blue); color: #fff; }
  .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 6px 16px rgba(0, 122, 255, 0.2); }
  
  .btn-ghost { background: transparent; color: var(--ink); border: 1px solid var(--line); }
  .btn-ghost:hover { background: var(--bg); }

  .btn-link { background: none; border: none; color: var(--blue); cursor: pointer; text-decoration: underline; font-family: var(--font-main); font-weight: 600; }

  /* Wizard Steps */
  .step { display: none; }
  .step.active { display: block; animation: fadeIn 0.4s ease; }
  @keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
  }

  /* Uploader Grid */
  .uploader-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-top: 16px;
  }
  .uploader-card {
    border: 1px dashed var(--line);
    border-radius: 12px;
    padding: 12px;
    background: var(--bg);
    text-align: center;
    transition: border-color 0.2s;
  }
  .uploader-card:hover { border-color: var(--blue); }
  .multi-thumb {
    height: 160px;
    border-radius: 8px;
    overflow: hidden;
    margin-bottom: 12px;
    background: #fff;
    border: 1px solid var(--line);
    display: flex;
    align-items: center;
    justify-content: center;
  }
  .multi-thumb img { width: 100%; height: 100%; object-fit: cover; }
  .input-file { display: none; }

  /* UI Listas e IA */
  .list .item {
    background: #fff;
    border: 1px solid var(--line);
    border-radius: 8px;
    padding: 12px 16px;
    margin-bottom: 8px;
    font-size: 14px;
    font-weight: 500;
  }
  .list .item label { font-size: 14px; color: var(--ink-dark); margin:0; }
  .chips { display: flex; flex-wrap: wrap; gap: 8px; }
  .chip {
    background: var(--bg);
    border: 1px solid var(--line);
    padding: 6px 12px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 600;
    color: var(--ink);
  }
  
  .del {
    background: var(--danger-soft);
    color: var(--danger);
    border: none;
    padding: 6px 12px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.2s;
  }
  .del:active { transform: scale(0.95); }

  /* Preview (Paso 4) */
  .pv-card {
    background: var(--bg);
    border: 1px solid var(--line);
    border-radius: 16px;
    padding: 24px;
    margin-bottom: 24px;
  }
  .kv .item {
    display: flex;
    justify-content: space-between;
    padding: 8px 0;
    border-bottom: 1px solid var(--line);
  }
  .kv .k { font-size: 13px; font-weight: 700; color: var(--muted); text-transform: uppercase; }
  .kv .v { font-size: 14px; font-weight: 600; color: var(--ink-dark); text-align: right; }

  /* Partidas */
  .partida-row {
    background: #fff;
    border: 1px solid var(--line);
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 16px;
  }
  .partida-row-top { display: flex; justify-content: space-between; margin-bottom: 16px; align-items: center; }
  .partida-index { font-weight: 700; font-size: 16px; color: var(--ink-dark); }
  .partida-grid-clean {
    display: grid;
    grid-template-columns: 1fr 2fr 0.8fr 0.8fr 1fr;
    gap: 12px;
  }
  .partida-total-clean {
    margin-top: 16px;
    padding-top: 16px;
    border-top: 1px dashed var(--line);
    display: flex;
    justify-content: flex-end;
    align-items: center;
    gap: 12px;
  }
  .partida-total-clean strong { font-size: 18px; color: var(--ink-dark); font-weight: 700; }
  .btn-mini-del {
    background: transparent;
    color: var(--danger);
    border: 1px solid var(--danger);
    padding: 6px 12px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 700;
    cursor: pointer;
  }

  /* Overlay AI */
  .overlay {
    position: fixed;
    top: 0; left: 0; width: 100%; height: 100%;
    background: rgba(255,255,255,0.8);
    backdrop-filter: blur(8px); /* Efecto Premium Apple */
    z-index: 9999;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.3s;
  }
  .overlay.show { opacity: 1; pointer-events: auto; }
  .ai-badge { background: var(--blue-soft); color: var(--blue); padding: 8px 16px; border-radius: 999px; font-weight: 700; margin-bottom: 24px; }
  .ai-stage { width: 200px; height: 200px; }

  /* Sobreescribiendo Modal Bootstrap para look Corporativo */
  .modal-content {
    border-radius: 16px !important;
    border: none !important;
    box-shadow: 0 20px 40px rgba(0,0,0,0.1) !important;
    font-family: var(--font-main) !important;
  }
  .modal-header {
    border-bottom: 1px solid var(--line) !important;
    padding: 24px !important;
  }
  .modal-title { font-weight: 700 !important; color: var(--ink-dark) !important; }
  .modal-subtitle { color: var(--muted) !important; font-size: 14px !important; margin-top: 4px !important; }
  .modal-body { padding: 24px !important; }
  .modal-footer { border-top: 1px solid var(--line) !important; padding: 16px 24px !important; }
  .badge-soft { background: var(--bg) !important; color: var(--ink) !important; border: 1px solid var(--line) !important; padding: 4px 10px !important; border-radius: 999px !important; font-size: 11px !important; font-weight: 600 !important; }
  .form-control { border-radius: 8px !important; border: 1px solid var(--line) !important; padding: 12px 16px !important; font-size: 15px !important; }
  .form-control:focus { border-color: var(--blue) !important; box-shadow: 0 0 0 3px var(--blue-soft) !important; }
  .form-label { font-weight: 600 !important; color: var(--muted) !important; font-size: 13px !important; }

</style>

<div id="mto" class="page">
  <div class="wrap">
    <div class="panel">
      
      <div class="head">
        <div class="hgroup">
          <h2>Orden de Servicio</h2>
          <p>Captura los datos, genera el checklist y confirma.</p>
        </div>
        <a href="{{ route('productos.cards') }}" class="back-link" title="Volver">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"></polyline></svg>
          Volver
        </a>
      </div>

      <div class="progress-wrap">
        <div class="progress-track">
          <div id="bar"></div>
        </div>
        <div class="progress-texts">
          <span id="progress-sub">Paso 1 de 4</span>
          <span id="ringLabel">25%</span>
        </div>
      </div>

      <div class="form">
        <div class="layout layout-single">
          <form id="wizard-form" action="{{ route('ordenes.store') }}" method="POST" novalidate enctype="multipart/form-data">
            @csrf

            <input type="hidden" name="remision_cantidad" id="remision_cantidad" value="1">
            <input type="hidden" name="remision_precio" id="remision_precio" value="0">

            {{-- STEP 1 --}}
            <div class="step active" data-step="1">
              <div class="field combo" id="cliente-combo" aria-haspopup="listbox" aria-expanded="false">
                <label for="cliente_search">Cliente (nombre, empresa, email…)</label>
                <input type="text" id="cliente_search" placeholder="Buscar..." autocomplete="off" role="combobox" aria-autocomplete="list" aria-controls="cliente_list">
                <div class="combo-list" id="cliente_list" role="listbox"></div>
                
                <input type="hidden" name="cliente_id" id="cliente_id" required>
                <div id="cliente_chip" style="margin-top:12px"></div>
                <div class="hint">
                  Escribe al menos 2 caracteres o 
                  <button type="button" class="btn-link p-0 align-baseline" onclick="openCreateClientModal()">
                    crea un nuevo cliente
                  </button>.
                </div>
                @error('cliente_id')<div style="color: var(--danger); font-size: 13px; font-weight: 600; margin-top: 4px;">{{ $message }}</div>@enderror
              </div>

              <div class="grid">
                <div class="field">
                  <label for="fecha_entrada">Fecha de entrada</label>
                  <input type="date" name="fecha_entrada" id="fecha_entrada" required>
                  @error('fecha_entrada')<div style="color: var(--danger); font-size: 13px; font-weight: 600; margin-top: 4px;">{{ $message }}</div>@enderror
                </div>

                <div class="field">
                  <label for="fecha_mantenimiento">Fecha de mantenimiento</label>
                  <input type="date" name="fecha_mantenimiento" id="fecha_mantenimiento" value="{{ now()->format('Y-m-d') }}" required>
                </div>

                <div class="field">
                  <label for="tecnico_id">Quién realizará el servicio</label>
                  <select name="tecnico_id" id="tecnico_id" required>
                    <option value="" hidden>Selecciona un técnico...</option>
                    @foreach($usuariosServicio as $usuario)
                      <option value="{{ $usuario->id }}" {{ (string) old('tecnico_id', auth()->id()) === (string) $usuario->id ? 'selected' : '' }}>
                        {{ $usuario->name }}
                      </option>
                    @endforeach
                  </select>
                  @error('tecnico_id')<div style="color: var(--danger); font-size: 13px; font-weight: 600; margin-top: 4px;">{{ $message }}</div>@enderror
                </div>
              </div>

              <div class="actions">
                <button type="button" class="btn btn-primary next">Siguiente →</button>
              </div>
            </div>

            {{-- STEP 2 --}}
            <div class="step" data-step="2">
              <div class="grid">
                <div class="field">
                  <label for="proximo_mantenimiento">Próximo mantenimiento (meses)</label>
                  <select name="proximo_mantenimiento" id="proximo_mantenimiento" required>
                    <option value="" hidden>Seleccionar...</option>
                    <option value="3" {{ old('proximo_mantenimiento') == '3' ? 'selected' : '' }}>3 meses</option>
                    <option value="6" {{ old('proximo_mantenimiento') == '6' ? 'selected' : '' }}>6 meses</option>
                    <option value="12" {{ old('proximo_mantenimiento') == '12' ? 'selected' : '' }}>12 meses</option>
                  </select>
                </div>

                <div class="field">
                  <label for="tipo_mantenimiento">Tipo de mantenimiento</label>
                  <select name="tipo_mantenimiento" id="tipo_mantenimiento" required>
                    <option value="" hidden>Seleccionar...</option>
                    <option value="preventivo" {{ old('tipo_mantenimiento', 'preventivo') === 'preventivo' ? 'selected' : '' }}>Preventivo</option>
                    <option value="correctivo" {{ old('tipo_mantenimiento') === 'correctivo' ? 'selected' : '' }}>Correctivo</option>
                    <option value="mixto" {{ old('tipo_mantenimiento') === 'mixto' ? 'selected' : '' }}>Mixto</option>
                  </select>
                </div>

                <div class="field">
                  <label for="equipo">Equipo</label>
                  <input type="text" name="equipo" id="equipo" placeholder="Ej. Insufflator" required value="{{ old('equipo') }}">
                </div>

                <div class="field">
                  <label for="marca">Marca</label>
                  <input type="text" name="marca" id="marca" placeholder="Ej. Stryker" value="{{ old('marca') }}">
                </div>

                <div class="field">
                  <label for="modelo">Modelo</label>
                  <input type="text" name="modelo" id="modelo" placeholder="Ej. L9000" value="{{ old('modelo') }}">
                </div>

                <div class="field">
                  <label for="numero_serie">Número de serie</label>
                  <input type="text" name="numero_serie" id="numero_serie" placeholder="Ej. SN-12345" value="{{ old('numero_serie') }}">
                </div>
              </div>

              <div class="field" style="margin-top:12px">
                <label for="observaciones">Observaciones Iniciales</label>
                <textarea name="observaciones" id="observaciones" rows="3" placeholder="Detalles de recepción...">{{ old('observaciones') }}</textarea>
              </div>

              <div style="margin-top:24px">
                <h4 style="font-size: 15px; font-weight: 700; color: var(--ink-dark); margin-bottom: 8px;">Registro Fotográfico</h4>
                <div class="uploader-grid">
                  <div class="uploader-card">
                    <div class="thumb multi-thumb">
                      <img id="foto_preview_1" src="https://via.placeholder.com/280x220.png?text=Foto+1" alt="Previsualización 1">
                    </div>
                    <div>
                      <label class="btn btn-ghost" for="foto_equipo" style="width: 100%; font-size: 12px; padding: 8px;">
                        <i class="bi bi-camera" style="margin-right: 6px;"></i> Subir foto 1
                      </label>
                      <input id="foto_equipo" class="input-file" type="file" name="foto_equipo" accept="image/*">
                      <div class="hint mt-2">Principal</div>
                    </div>
                  </div>

                  <div class="uploader-card">
                    <div class="thumb multi-thumb">
                      <img id="foto_preview_2" src="https://via.placeholder.com/280x220.png?text=Foto+2" alt="Previsualización 2">
                    </div>
                    <div>
                      <label class="btn btn-ghost" for="foto_equipo_2" style="width: 100%; font-size: 12px; padding: 8px;">
                        <i class="bi bi-camera" style="margin-right: 6px;"></i> Subir foto 2
                      </label>
                      <input id="foto_equipo_2" class="input-file" type="file" name="foto_equipo_2" accept="image/*">
                      <div class="hint mt-2">Lateral / detalle</div>
                    </div>
                  </div>

                  <div class="uploader-card">
                    <div class="thumb multi-thumb">
                      <img id="foto_preview_3" src="https://via.placeholder.com/280x220.png?text=Foto+3" alt="Previsualización 3">
                    </div>
                    <div>
                      <label class="btn btn-ghost" for="foto_equipo_3" style="width: 100%; font-size: 12px; padding: 8px;">
                        <i class="bi bi-camera" style="margin-right: 6px;"></i> Subir foto 3
                      </label>
                      <input id="foto_equipo_3" class="input-file" type="file" name="foto_equipo_3" accept="image/*">
                      <div class="hint mt-2">Serie / componente</div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="actions">
                <button type="button" class="btn btn-ghost prev">← Anterior</button>
                <button type="button" class="btn btn-primary next">Siguiente →</button>
              </div>
            </div>

            {{-- STEP 3 --}}
            <div class="step" data-step="3">
              <div class="grid">
                <div class="field">
                  <label for="nombre_equipo_libre">Equipo (Contexto IA)</label>
                  <input type="text" id="nombre_equipo_libre" placeholder="Opcional para mejorar precisión">
                </div>
                <div class="field">
                  <label for="servicio">Tipo de servicio</label>
                  <select id="servicio">
                    <option value="preventivo">Preventivo</option>
                    <option value="correctivo">Correctivo</option>
                    <option value="mixto">Mixto</option>
                  </select>
                </div>
                <div class="field">
                  <label for="sintomas">Síntomas / Observaciones adicionales</label>
                  <input type="text" id="sintomas" placeholder="Fallas reportadas...">
                </div>
              </div>

              <div class="actions" style="justify-content:flex-start;margin-top:16px;margin-bottom:32px;">
                <button type="button" class="btn btn-primary" id="btn-sugerir-ia">
                  <i class="bi bi-stars" style="margin-right: 8px;"></i>
                  <span class="btn-text">Generar Checklist con IA</span>
                </button>
              </div>

              <input type="hidden" name="template_slug" value="ia-dynamic">

              <div style="margin-bottom: 32px;">
                <h4 style="font-size: 16px; font-weight: 700; color: var(--ink-dark); margin-bottom: 12px;">Checklist sugerido</h4>
                <div id="preventivo-box" class="list">
                  <div class="hint" style="padding: 24px; background: var(--bg); border: 1px dashed var(--line); border-radius: 12px; text-align: center;">Pulsa el botón de IA para generar parámetros de inspección.</div>
                </div>
              </div>

              <div style="margin-bottom: 32px;">
                <h4 style="font-size: 16px; font-weight: 700; color: var(--ink-dark); margin-bottom: 12px;">Mantenimiento realizado</h4>
                <div id="realizado-box" class="list"></div>
                <div class="field" style="max-width:500px; margin-top: 16px;">
                  <input type="text" id="accion_libre" placeholder="Escribe y presiona Enter...">
                  <label for="accion_libre">Agregar acción manual</label>
                </div>
              </div>

              <div class="grid" style="grid-template-columns: 1fr 1fr; gap: 24px;">
                <div>
                  <h4 style="font-size: 16px; font-weight: 700; color: var(--ink-dark); margin-bottom: 12px;">Diagnóstico preliminar (IA)</h4>
                  <div id="diag-box" class="list">
                    <div class="hint" style="padding: 16px; background: var(--bg); border-radius: 8px;">Esperando análisis de IA...</div>
                  </div>
                </div>
                <div>
                  <h4 style="font-size: 16px; font-weight: 700; color: var(--ink-dark); margin-bottom: 12px;">Plan de Ingeniería</h4>
                  <div id="eng-box" class="list">
                    <div class="hint" style="padding: 16px; background: var(--bg); border-radius: 8px;">Esperando plan sugerido...</div>
                  </div>
                </div>
              </div>

              <div class="actions">
                <button type="button" class="btn btn-ghost prev">← Anterior</button>
                <button type="button" class="btn btn-primary next">Siguiente →</button>
              </div>
            </div>

            {{-- STEP 4 --}}
            <div class="step" data-step="4">
              <div class="preview">
                
                <section class="pv-card">
                  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                    <h3 style="font-size: 18px; font-weight: 700; margin: 0;">Revisión Final</h3>
                    <span class="chipSel" id="pvBadgeCliente">Cliente</span>
                  </div>

                  <div class="kv">
                    <div class="item"><div class="k">Cliente</div><div class="v" id="c_cliente"></div></div>
                    <div class="item"><div class="k">Equipo</div><div class="v" id="c_equipo"></div></div>
                    <div class="item"><div class="k">Fechas</div><div class="v"><span id="c_fentrada"></span> / <span id="c_fmanto"></span></div></div>
                    <div class="item"><div class="k">Técnico</div><div class="v" id="c_tecnico"></div></div>
                    <div class="item"><div class="k">Identificación</div><div class="v"><span id="c_marca"></span> | <span id="c_modelo"></span> | SN: <span id="c_serie"></span></div></div>
                    <div class="item"><div class="k">Próx. Mto.</div><div class="v" id="c_prox"></div></div>
                  </div>

                  <div style="margin-top:32px">
                    <h4 style="font-size: 16px; font-weight: 700; margin-bottom: 16px; border-bottom: 1px solid var(--line); padding-bottom: 8px;">Checklist Procesado</h4>
                    <table style="width: 100%; border-collapse: collapse; text-align: left;" id="pvChecklist">
                      <thead>
                        <tr>
                          <th style="padding: 8px; border-bottom: 1px solid var(--line); color: var(--muted); font-size: 12px; text-transform: uppercase;">Ítem</th>
                          <th style="padding: 8px; border-bottom: 1px solid var(--line); color: var(--muted); font-size: 12px; text-transform: uppercase;">Sección</th>
                          <th style="padding: 8px; border-bottom: 1px solid var(--line); color: var(--muted); font-size: 12px; text-transform: uppercase;">Estatus</th>
                        </tr>
                      </thead>
                      <tbody></tbody>
                    </table>
                  </div>
                  
                  <div id="pvDiag" style="display: none;"></div>
                  <div id="pvDiagHallazgos" style="display: none;"></div>
                  <div id="pvDiagPrioridad" style="display: none;"></div>
                  <div id="pvDiagRiesgo" style="display: none;"></div>
                  <div id="pvDiagHipotesis" style="display: none;"></div>
                  <div id="pvDiagCausa" style="display: none;"></div>
                  <div id="pvDiagPruebas" style="display: none;"></div>
                  <div id="pvDiagPiezas" style="display: none;"></div>
                  <div id="pvDiagRecomendacion" style="display: none;"></div>
                  <div id="pvDiagLiberacion" style="display: none;"></div>
                  <div id="pvDiagPasos" style="display: none;"></div>
                  <div id="pvDiagPruebasDetalladas" style="display: none;"></div>

                  <div style="margin-top:32px">
                    <h4 style="font-size: 16px; font-weight: 700; margin-bottom: 16px;">Acciones Realizadas</h4>
                    <div class="chips" id="pvAcciones"></div>
                  </div>
                </section>

                <aside class="pv-card" style="background: var(--bg);">
                  <div class="pv-photos-grid">
                    <img id="pv_foto_1" alt="Foto 1" class="pv-photo" style="display:none;">
                    <img id="pv_foto_2" alt="Foto 2" class="pv-photo" style="display:none;">
                    <img id="pv_foto_3" alt="Foto 3" class="pv-photo" style="display:none;">
                  </div>

                  <div style="display: flex; gap: 8px; margin-bottom: 24px;">
                    <span class="chip">Ítems: <b id="pvTotalItems">0</b></span>
                    <span class="chip" style="background: var(--success-soft); color: var(--success); border-color: var(--success-soft);">OK: <b id="pvOkCount">0</b></span>
                  </div>

                  <div style="background: #ffffff; border: 1px solid var(--line); border-radius: 12px; padding: 24px;">
                    <h4 style="font-size: 16px; font-weight: 700; margin-top: 0;">Partidas para remisión</h4>
                    <p style="font-size: 13px; color: var(--muted); margin-bottom: 24px;">Configuración final de cobro.</p>

                    <input type="hidden" name="remision_partidas" id="remision_partidas">
                    <input type="hidden" name="remision_subtotal" id="remision_subtotal">
                    <input type="hidden" name="remision_iva" id="remision_iva">
                    <input type="hidden" name="remision_total" id="remision_total">
                    <input type="hidden" name="remision_total_pagar" id="remision_total_pagar">
                    <input type="hidden" name="remision_unidad" id="remision_unidad" value="SERVICIO">

                    <div id="partidas-box"></div>

                    <div style="display: flex; gap: 8px; margin-top: 16px;">
                      <button type="button" class="btn btn-ghost" id="btn-add-partida" style="font-size: 12px; padding: 8px 16px;">+ Agregar</button>
                      <button type="button" class="btn btn-ghost" id="btn-rebuild-partidas" style="font-size: 12px; padding: 8px 16px;"><i class="bi bi-arrow-clockwise"></i> Regenerar</button>
                    </div>

                    <div class="grid" style="margin-top: 24px; gap: 12px; grid-template-columns: 1fr 1fr;">
                      <div class="field" style="margin: 0;">
                        <label for="remision_envio" style="font-size: 11px;">Envío</label>
                        <input type="number" min="0" step="0.01" name="remision_envio" id="remision_envio" placeholder="0.00" style="padding: 8px;">
                      </div>
                      <div class="field" style="margin: 0;">
                        <label for="remision_anticipo" style="font-size: 11px;">Anticipo</label>
                        <input type="number" min="0" step="0.01" name="remision_anticipo" id="remision_anticipo" placeholder="0.00" style="padding: 8px;">
                      </div>
                    </div>

                    <div style="margin-top: 16px; padding: 12px; background: var(--bg); border-radius: 8px; display: flex; justify-content: space-between; align-items: center;">
                      <span style="font-size: 13px; font-weight: 700; color: var(--ink-dark);">Requiere IVA (16%)</span>
                      <input type="checkbox" name="remision_requiere_iva" id="remision_requiere_iva" value="1" style="width: 18px; height: 18px;">
                    </div>

                    <div style="margin-top: 24px; border-top: 1px solid var(--line); padding-top: 16px;">
                      <div style="display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 14px; font-weight: 500;">
                        <span>Subtotal</span> <strong id="remision_subtotal_lbl">$0.00</strong>
                      </div>
                      <div style="display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 14px; font-weight: 500;">
                        <span>IVA</span> <strong id="remision_iva_lbl">$0.00</strong>
                      </div>
                      <div style="display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 14px; font-weight: 500;">
                        <span>Total</span> <strong id="remision_total_lbl">$0.00</strong>
                      </div>
                      <div style="display: flex; justify-content: space-between; margin-bottom: 16px; font-size: 14px; font-weight: 500; color: var(--danger);">
                        <span>Anticipo</span> <strong id="remision_anticipo_lbl">$0.00</strong>
                      </div>
                      <div style="display: flex; justify-content: space-between; font-size: 18px; font-weight: 700; color: var(--ink-dark); background: var(--bg); padding: 12px; border-radius: 8px;">
                        <span>Pagar</span> <strong id="remision_pagar_lbl">$0.00</strong>
                      </div>
                    </div>

                    <div class="field" style="margin-top: 24px;">
                      <label for="remision_descripcion">Descripción general remisión</label>
                      <textarea name="remision_descripcion" id="remision_descripcion" rows="3"></textarea>
                    </div>

                  </div>

                  <div class="actions" style="margin-top: 24px;">
                    <button type="button" class="btn btn-ghost prev">← Editar</button>
                    <button type="submit" class="btn btn-primary" style="flex: 1;">Generar PDF</button>
                  </div>
                </aside>

              </div>
            </div>

          </form>
        </div>
      </div>
    </div>
  </div>

  <div class="overlay" id="ai-overlay" aria-hidden="true" role="dialog" aria-modal="true">
    <div class="ai-badge">Generando checklist con IA...</div>
    <div class="ai-stage" aria-hidden="true">
      <svg id="mtoSVG" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 800 600">
        <defs>
          <linearGradient id="mtoGrad" x1="513.98" y1="290" x2="479.72" y2="320" gradientUnits="userSpaceOnUse">
            <stop offset="0" stop-color="#000" stop-opacity="0"/>
            <stop offset=".15" stop-color="#007aff"/>
            <stop offset=".4"  stop-color="#3b82f6"/>
            <stop offset=".6"  stop-color="#60a5fa"/>
            <stop offset=".78" stop-color="#93c5fd"/>
            <stop offset="1" stop-color="#000" stop-opacity="0"/>
          </linearGradient>
        </defs>
        @for($i=0;$i<30;$i++)
          <ellipse class="ell" cx="400" cy="300" rx="80" ry="80"/>
        @endfor
        <path id="ai" opacity="0" d="m417.17,323.85h-34.34c-3.69,0-6.67-2.99-6.67-6.67v-34.34c0-3.69,2.99-6.67,6.67-6.67h34.34c3.69,0,6.67,2.99,6.67,6.67v34.34c0,3.69-2.99,3.69-6.67,6.67Zm-5.25-12.92v-21.85c0-.55-.45-1-1-1h-21.85c-.55,0-1,.45-1,1v21.85c0,.55.45,1,1,1h21.85c.55,0,1-.45,1-1Zm23.08-16.29h-11.15m-47.69,0h-11.15m70,10.73h-11.15m-47.69,0h-11.15m40.37,29.63v-11.15m0-47.69v-11.15m-10.73,70v-11.15m0-47.69v-11.15" stroke="url(#mtoGrad)" stroke-linecap="round" stroke-miterlimit="10" stroke-width="2"/>
      </svg>
    </div>
  </div>
</div>

<div class="modal fade" id="modal_formulario" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <div>
          <h5 class="modal-title">Nuevo Cliente</h5>
          <p class="modal-subtitle">Guarda los datos básicos para reutilizarlo.</p>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <div class="row g-3">
          <div class="col-12 col-md-6">
            <label class="form-label">Nombre</label>
            <input type="text" class="form-control" id="mc_nombre" placeholder="Ej. Ana">
          </div>
          <div class="col-12 col-md-6">
            <label class="form-label">Apellido</label>
            <input type="text" class="form-control" id="mc_apellido" placeholder="Ej. López">
          </div>
          <div class="col-12 col-md-6">
            <label class="form-label">Teléfono</label>
            <input type="text" class="form-control" id="mc_telefono" placeholder="Ej. 55 1234 5678">
          </div>
          <div class="col-12 col-md-6">
            <label class="form-label">Correo electrónico</label>
            <input type="email" class="form-control" id="mc_email" placeholder="correo@dominio.com">
          </div>
          <div class="col-12">
            <label class="form-label">Dirección / Comentarios</label>
            <textarea class="form-control" id="mc_comentarios" rows="3"></textarea>
          </div>
          <div class="col-12">
            <div id="mc_error" class="alert alert-danger d-none mb-0" style="border-radius: 8px;"></div>
            <div id="mc_ok" class="alert alert-success d-none mb-0" style="border-radius: 8px; background: var(--success-soft); color: var(--success); border: none;"></div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-ghost" data-bs-dismiss="modal">Cerrar</button>
        <button type="button" class="btn btn-primary" id="btn_guardar_cliente">Guardar cliente</button>
      </div>
    </div>
  </div>
</div>

<div id="cliente-toast" class="toast-cliente" role="status" aria-live="polite" style="position:fixed;right:24px;bottom:24px;background:#ffffff;border:1px solid var(--line);border-radius:12px;padding:16px 20px;box-shadow:0 10px 30px rgba(0,0,0,0.1);z-index:9999;opacity:0;transform:translateY(20px);transition:all 0.3s ease;pointer-events:none;">
  <div style="display:flex;align-items:center;gap:12px;">
    <div style="width:32px;height:32px;border-radius:50%;background:var(--success-soft);color:var(--success);display:flex;align-items:center;justify-content:center;font-weight:700;"><i class="bi bi-check"></i></div>
    <div>
      <div style="font-weight:700;font-size:14px;color:var(--ink-dark);">Éxito</div>
      <div id="cliente-toast-text" style="font-size:13px;color:var(--muted);">Cliente registrado.</div>
    </div>
  </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js" defer></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/CustomEase.min.js" defer></script>

<script>
function showClienteToast(message){
  const toast = document.getElementById('cliente-toast');
  const text  = document.getElementById('cliente-toast-text');
  if(!toast || !text) return;
  text.textContent = message || 'Cliente creado correctamente.';
  toast.style.opacity = '1';
  toast.style.transform = 'translateY(0)';
  clearTimeout(window.__clienteToastTimer);
  window.__clienteToastTimer = setTimeout(()=>{ 
    toast.style.opacity = '0'; 
    toast.style.transform = 'translateY(20px)'; 
  }, 3000);
}
function hideClienteToast(){
  const toast = document.getElementById('cliente-toast');
  if(toast) { toast.style.opacity = '0'; toast.style.transform = 'translateY(20px)'; }
}
</script>

<script>
(function(){
  const $ = s => document.querySelector(s);
  const $$ = s => [...document.querySelectorAll(s)];

  const steps = $$('#mto .step');
  const total = steps.length;
  let current = 0;
  const names = ['Datos y Fechas','Especificaciones','IA & Diagnóstico','Resumen y Cierre'];
  const bar = $('#mto #bar');
  const pctLabel = $('#mto #ringLabel');
  const pSub = $('#mto #progress-sub');
  
  // Elementos falsos para que el JS no lance error si espera ".stepdot"
  const dots = []; 

  let fotoDataURL1 = null;
  let fotoDataURL2 = null;
  let fotoDataURL3 = null;
  let lastAIData = null;
  let userTouchedPartidas = false;

  function updateProgress(){
    const pct = Math.round(((current+1)/total)*100);
    if (pctLabel) pctLabel.textContent = pct + '%';
    if (bar) bar.style.width = pct + '%';
    if (pSub) pSub.textContent = `Paso ${current+1} de ${total} · ${names[current]}`;
  }

  function show(i){
    steps.forEach((s,idx)=> s.classList.toggle('active', idx===i));
    current = i;
    if(i===3) fillConfirm();
    updateProgress();
    updateLive();
  }

  function validate(i){
    let ok = true;
    steps[i].querySelectorAll('[required]').forEach(x=>{ if(!x.value){ ok=false; }});
    if(i===0 && !$('#mto #cliente_id').value){ ok = false; }
    if(i===1 && !$('#mto #equipo').value.trim()){ ok = false; }
    if(i===1 && !$('#mto #tipo_mantenimiento').value){ ok = false; }
    return ok;
  }

  $$('#mto .next').forEach(b=> b.addEventListener('click', ()=>{
    if(!validate(current)) return;
    show(Math.min(total-1,current+1));
  }));

  $$('#mto .prev').forEach(b=> b.addEventListener('click', ()=> show(Math.max(0,current-1))));

  function bindImagePreview(inputSelector, imgSelector, setter){
    const inp = $(inputSelector);
    const img = $(imgSelector);
    inp?.addEventListener('change', e=>{
      const f = e.target.files?.[0];
      if(!f) return;
      const reader = new FileReader();
      reader.onload = ev => {
        setter(ev.target.result);
        img.src = ev.target.result;
      };
      reader.readAsDataURL(f);
    });
  }

  bindImagePreview('#mto #foto_equipo', '#mto #foto_preview_1', v => fotoDataURL1 = v);
  bindImagePreview('#mto #foto_equipo_2', '#mto #foto_preview_2', v => fotoDataURL2 = v);
  bindImagePreview('#mto #foto_equipo_3', '#mto #foto_preview_3', v => fotoDataURL3 = v);

  const combo = $('#mto #cliente-combo');
  const list  = $('#mto #cliente_list');
  const chip  = $('#mto #cliente_chip');
  const fCliente = $('#mto #cliente_search');
  const fClienteId = $('#mto #cliente_id');
  let itemsCache = [], activeIndex=-1, debounceId=null, ctrl=null;

  function setBusy(b){ combo.classList.toggle('busy', b); }
  function openList(){ list.classList.add('show'); combo.setAttribute('aria-expanded','true'); }
  function closeList(){ list.classList.remove('show'); combo.setAttribute('aria-expanded','false'); activeIndex=-1; fCliente.setAttribute('aria-activedescendant',''); }

  function normalizeApiData(data){
    const rows = Array.isArray(data) ? data : (Array.isArray(data?.items) ? data.items : []);
    return rows.map(r=>{
      const id = r.id ?? r.value ?? r.uuid ?? r.ID ?? r.pk ?? '';
      const nombre   = (r.nombre   || r.name   || r.label || '').trim();
      const apellido = (r.apellido || r.apellidos || '').trim();
      const nombreCompleto = [nombre, apellido].filter(Boolean).join(' ');
      const label = nombreCompleto || r.razon_social || r.email || r.label || '—';
      const desc = r.empresa ?? r.desc ?? r.email ?? r.telefono ?? '';
      return { id, label, desc };
    }).filter(x => x.id !== '' && x.label !== '—');
  }

  function renderList(items){
    list.innerHTML=''; itemsCache = items||[]; activeIndex=-1;
    if(!itemsCache.length){
      const el=document.createElement('div');
      el.className='combo-item';
      el.style.opacity=.75;
      el.textContent='Sin resultados';
      el.setAttribute('role','option');
      el.id='opt-empty';
      list.appendChild(el);
    }else{
      itemsCache.forEach((it,i)=>{
        const el=document.createElement('div');
        el.className='combo-item';
        el.setAttribute('role','option');
        el.id='opt-'+i;
        el.innerHTML=`<div style="font-weight:700;color:var(--ink-dark);">${it.label}</div><div style="color:var(--muted);font-size:12px">${it.desc||''}</div>`;
        el.addEventListener('mouseenter',()=> setActive(i));
        el.addEventListener('mousedown', ev=>{ ev.preventDefault(); pick(i); });
        list.appendChild(el);
      });
    }
    openList();
  }

  function setActive(i){
    const items=[...list.querySelectorAll('.combo-item')];
    items.forEach((el,idx)=> el.classList.toggle('active', idx===i));
    activeIndex=i;
    if(items[i]){
      fCliente.setAttribute('aria-activedescendant', items[i].id);
      items[i].scrollIntoView({block:'nearest'});
    }
  }

  function pick(i){
    const it = itemsCache[i];
    if(!it) return;
    fClienteId.value = it.id;
    fCliente.value = it.label;
    closeList();
    chip.innerHTML = `<span class="chipSel">${it.label} <span class="x" title="Quitar">×</span></span>`;
    const x = chip.querySelector('.x');
    if (x) {
      x.addEventListener('click', ()=>{
        fClienteId.value=''; fCliente.value=''; chip.innerHTML=''; fCliente.focus(); updateLive();
      });
    }
    updateLive();
  }

  function doSearch(q){
    if(ctrl) ctrl.abort();
    ctrl = new AbortController();
    setBusy(true);
    const url = new URL(`{{ route('clientes.encontrar') }}`);
    url.searchParams.set('search', q);
    fetch(url.toString(),{
      headers:{ 'X-Requested-With':'XMLHttpRequest','Accept':'application/json' },
      signal:ctrl.signal
    })
      .then(r=> r.ok ? r.json() : Promise.reject(new Error('HTTP '+r.status)))
      .then(json=> renderList(normalizeApiData(json)))
      .catch(e=>{ if(e?.name!=='AbortError'){ renderList([]); }})
      .finally(()=> setBusy(false));
  }

  fCliente.addEventListener('input', ()=>{
    const q = fCliente.value.trim();
    fClienteId.value='';
    chip.innerHTML='';
    if(debounceId) clearTimeout(debounceId);
    if(q.length < 2){ closeList(); updateLive(); return; }
    debounceId = setTimeout(()=> doSearch(q), 250);
  });

  fCliente.addEventListener('keydown', (e)=>{
    if(!list.classList.contains('show')){
      if(e.key==='ArrowDown'){ openList(); }
      return;
    }
    const count = itemsCache.length;
    if(e.key==='ArrowDown'){
      e.preventDefault(); if(count===0) return;
      setActive( (activeIndex+1) % count );
    }else if(e.key==='ArrowUp'){
      e.preventDefault(); if(count===0) return;
      setActive( (activeIndex-1+count) % count );
    }else if(e.key==='Enter'){
      if(activeIndex>=0){ e.preventDefault(); pick(activeIndex); }
    }else if(e.key==='Escape'){
      closeList();
    }
  });

  document.addEventListener('click', (e)=>{ if(!combo.contains(e.target)) closeList(); });

  window.setClienteDesdeModal = function (cliente) {
    if (!cliente) return;
    const nombre   = (cliente.nombre   || '').trim().toUpperCase();
    const apellido = (cliente.apellido || '').trim().toUpperCase();
    const label = [nombre, apellido].filter(Boolean).join(' ') || 'SIN NOMBRE';
    fCliente.value = label;
    fClienteId.value = cliente.id ?? '';
    chip.innerHTML = `<span class="chipSel">${label} <span class="x" title="Quitar">×</span></span>`;
    const x = chip.querySelector('.x');
    if (x) {
      x.addEventListener('click', ()=>{
        fClienteId.value=''; fCliente.value=''; chip.innerHTML=''; fCliente.focus(); updateLive();
      });
    }
    closeList();
    updateLive();
  };

  const preventivoBox = $('#mto #preventivo-box');
  const realizadoBox  = $('#mto #realizado-box');
  const btnIA         = $('#mto #btn-sugerir-ia');
  const overlay       = $('#mto #ai-overlay');
  const btnText       = btnIA?.querySelector('.btn-text');
  const partidasBox   = $('#mto #partidas-box');

  function setOverlay(on){ overlay.classList.toggle('show', on); }

  function setBtnLoading(on){
    if(!btnIA) return;
    if(on){
      btnIA.setAttribute('disabled','disabled');
      btnText.textContent = 'Generando...';
    } else {
      btnIA.removeAttribute('disabled');
      btnText.textContent = 'Generar Checklist con IA';
    }
  }

  function showSkeleton(){
    preventivoBox.innerHTML='';
    for(let i=0;i<6;i++){
      const sk=document.createElement('div');
      sk.className='item';
      sk.innerHTML = 'Cargando análisis...';
      sk.style.color = 'var(--muted)';
      preventivoBox.appendChild(sk);
    }
    realizadoBox.innerHTML='';
  }

  function hideSkeleton(){ }

  function renderPreventivo(secciones){
    preventivoBox.innerHTML = '';
    let idx=0;
    (secciones||[]).forEach(sec=>{
      const head=document.createElement('div');
      head.className='item';
      head.style.fontWeight='700';
      head.style.background='var(--bg)';
      head.textContent=sec.titulo||'Sección';
      preventivoBox.appendChild(head);

      (sec.items||[]).forEach(it=>{
        const row=document.createElement('div');
        row.className='item';
        row.innerHTML = `
          <div style="display:flex;justify-content:space-between;gap:8px;align-items:center;flex-wrap:wrap">
            <div style="font-weight: 600;">${it.nombre}</div>
            <div style="display:flex;align-items:center;gap:8px">
              <select name="mto_preventivo[${idx}][estatus]" style="border:1px solid var(--line);border-radius:8px;padding:6px 12px;font-family:var(--font-main);">
                <option ${it.resultado_sugerido==='Bueno y Funcional'?'selected':''}>Bueno y Funcional</option>
                <option ${it.resultado_sugerido==='Revisado'?'selected':''}>Revisado</option>
                <option ${it.resultado_sugerido==='Ajustado'?'selected':''}>Ajustado</option>
                <option ${it.resultado_sugerido==='Reparado'?'selected':''}>Reparado</option>
                <option ${it.resultado_sugerido==='Reemplazado'?'selected':''}>Reemplazado</option>
                <option ${it.resultado_sugerido==='Realizado'?'selected':''}>Realizado</option>
                <option ${it.resultado_sugerido==='No aplica'?'selected':''}>No aplica</option>
              </select>
              <button type="button" class="del del-item" title="Eliminar ítem">X</button>
            </div>
          </div>
          <input type="hidden" name="mto_preventivo[${idx}][seccion]" value="${(sec.titulo||'').replaceAll('"','&quot;')}">
          <input type="hidden" name="mto_preventivo[${idx}][item]" value="${(it.nombre||'').replaceAll('"','&quot;')}">`;
        preventivoBox.appendChild(row);
        idx++;
      });
    });
    if(!preventivoBox.children.length){
      preventivoBox.innerHTML = '<div class="hint">Sin ítems</div>';
    }
    updateLive();
  }

  function renderRealizado(list){
    realizadoBox.innerHTML = '';
    (list||[]).forEach(txt=>{
      const el=document.createElement('div');
      el.className='item';
      el.innerHTML = `<label style="display:flex;align-items:center;gap:10px;justify-content:space-between; width: 100%;">
        <span><input type="checkbox" name="mto_realizado[]" value="${(txt||'').replaceAll('"','&quot;')}" checked> ${txt}</span>
        <button type="button" class="del del-act" title="Eliminar acción">X</button></label>`;
      realizadoBox.appendChild(el);
    });
    updateLive();
  }

  function renderDiagnosis(diag, riesgos, notas, resumenIngenieria){
    const diagBox = $('#mto #diag-box');
    const engBox  = $('#mto #eng-box');

    diagBox.innerHTML='';

    const hall = (diag?.hallazgos_probables||[]).join(' · ') || '—';
    const pruebas = (diag?.pruebas_sugeridas||[]).join(' · ') || '—';
    const piezas = (diag?.piezas_posibles||[]).join(' · ') || '—';
    const pasos = Array.isArray(diag?.pasos_a_seguir) ? diag.pasos_a_seguir : [];
    const liberacion = Array.isArray(diag?.criterio_liberacion) ? diag.criterio_liberacion : [];
    const pruebasDetalladas = Array.isArray(diag?.pruebas_detalladas) ? diag.pruebas_detalladas : [];

    const block=document.createElement('div');
    block.className='item';
    block.innerHTML=`
      <div style="display:flex;flex-direction:column;gap:8px">
        <div><strong>Hipótesis:</strong> ${diag?.hipotesis || '—'}</div>
        <div><strong>Causa probable:</strong> ${diag?.causa_raiz_probable || '—'}</div>
        <div><strong>Hallazgos:</strong> ${hall}</div>
        <div style="display:flex;gap:8px;flex-wrap:wrap;margin-top:8px;">
          <span class="chip" style="background:var(--blue-soft);color:var(--blue);border:none;">Prioridad: <b>${diag?.prioridad || '—'}</b></span>
          <span class="chip" style="background:var(--danger-soft);color:var(--danger);border:none;">Riesgo: <b>${diag?.nivel_riesgo || '—'}</b></span>
        </div>
      </div>`;
    diagBox.appendChild(block);

    engBox.innerHTML='';
    const plan = [
      ...(Array.isArray(diag?.pasos_a_seguir) ? diag.pasos_a_seguir : []),
      ...(Array.isArray(resumenIngenieria) ? resumenIngenieria : [])
    ];

    const uniquePlan = [...new Set(plan.filter(Boolean))];

    if(uniquePlan.length){
      const wrap=document.createElement('div');
      wrap.className='item';
      const ol=document.createElement('ol');
      ol.style.margin='0 0 0 16px';
      uniquePlan.forEach(p=>{
        const li=document.createElement('li');
        li.textContent=p;
        ol.appendChild(li);
      });
      wrap.appendChild(ol);
      engBox.appendChild(wrap);
    } else {
      engBox.innerHTML = '<div class="hint" style="padding: 16px; background: var(--bg); border-radius: 8px;">Sin plan sugerido.</div>';
    }
  }

  function money(n){
    try{
      return new Intl.NumberFormat('es-MX',{style:'currency',currency:'MXN'}).format(n || 0);
    }catch(_){
      return '$' + (n || 0).toFixed(2);
    }
  }

  function num(el){
    if(!el) return 0;
    const v = (el.value || '').toString().replace(',','.');
    const n = parseFloat(v);
    return Number.isFinite(n) ? n : 0;
  }

  function esc(v){
    return String(v ?? '')
      .replaceAll('&', '&amp;')
      .replaceAll('"', '&quot;')
      .replaceAll('<', '&lt;')
      .replaceAll('>', '&gt;');
  }

  function getCheckedActions(){
    return [...document.querySelectorAll('#mto input[name="mto_realizado[]"]:checked')]
      .map(x => (x.value || '').trim())
      .filter(Boolean);
  }

  function getTipoServicioActual(){
    return ($('#mto #tipo_mantenimiento')?.value || $('#mto #servicio')?.value || 'preventivo').toLowerCase();
  }

  function buildPartidasFromActions(useAiBase = true){
    const tipo = getTipoServicioActual();
    const equipo = ($('#mto #equipo')?.value || '').trim();
    const marca = ($('#mto #marca')?.value || '').trim();
    const modelo = ($('#mto #modelo')?.value || '').trim();
    const acciones = getCheckedActions();

    if(useAiBase && Array.isArray(lastAIData?.remision_partidas) && lastAIData.remision_partidas.length){
      const aiRows = lastAIData.remision_partidas.map((p, i) => ({
        item: p.item || `Partida ${i+1}`,
        descripcion: p.descripcion || '',
        unidad: p.unidad || 'SERVICIO',
        cantidad: Number(p.cantidad || 1),
        precio_unitario: Number(p.precio_unitario || 0)
      }));

      if(acciones.length){
        const joined = acciones.join(', ');
        if(aiRows[0]){
          aiRows[0].descripcion = aiRows[0].descripcion
            ? `${aiRows[0].descripcion}. Acciones realizadas: ${joined}`
            : `Acciones realizadas: ${joined}`;
        }
      }

      return aiRows;
    }

    const resumenEquipo = [equipo, marca, modelo].filter(Boolean).join(' ').trim();
    const partidas = [];

    if(tipo === 'preventivo'){
      partidas.push({ item: 'Partida 1', descripcion: `Mantenimiento preventivo general${resumenEquipo ? ' de ' + resumenEquipo : ''}`, unidad: 'SERVICIO', cantidad: 1, precio_unitario: 0 });
      partidas.push({ item: 'Partida 2', descripcion: acciones.length ? `Limpieza, inspección, ajuste y revisión realizados: ${acciones.join(', ')}` : 'Limpieza, inspección, ajuste y verificación de componentes', unidad: 'SERVICIO', cantidad: 1, precio_unitario: 0 });
    }
    else if(tipo === 'correctivo'){
      partidas.push({ item: 'Partida 1', descripcion: `Diagnóstico y revisión correctiva${resumenEquipo ? ' de ' + resumenEquipo : ''}`, unidad: 'SERVICIO', cantidad: 1, precio_unitario: 0 });
      partidas.push({ item: 'Partida 2', descripcion: acciones.length ? `Corrección, reparación o ajuste realizado: ${acciones.join(', ')}` : 'Corrección, reparación o ajuste de la falla detectada', unidad: 'SERVICIO', cantidad: 1, precio_unitario: 0 });
    }
    else {
      partidas.push({ item: 'Partida 1', descripcion: `Mantenimiento preventivo / correctivo${resumenEquipo ? ' de ' + resumenEquipo : ''}`, unidad: 'SERVICIO', cantidad: 1, precio_unitario: 0 });
    }

    return partidas;
  }

  function createPartidaRow(data = {}){
    const idx = partidasBox.querySelectorAll('.partida-row').length + 1;
    const row = document.createElement('div');
    row.className = 'partida-row';
    row.innerHTML = `
      <div class="partida-row-top">
        <div class="partida-index">Partida ${idx}</div>
        <button type="button" class="btn-mini-del btn-del-partida">Eliminar</button>
      </div>

      <div class="partida-grid-clean">
        <div class="field" style="margin:0;">
          <label>Ítem</label>
          <input type="text" class="partida-item" placeholder=" " value="${esc(data.item || `Partida ${idx}`)}">
        </div>
        <div class="field" style="margin:0;">
          <label>Descripción</label>
          <input type="text" class="partida-descripcion" placeholder=" " value="${esc(data.descripcion || '')}">
        </div>
        <div class="field" style="margin:0;">
          <label>Unidad</label>
          <input type="text" class="partida-unidad" placeholder=" " value="${esc(data.unidad || 'SERVICIO')}">
        </div>
        <div class="field" style="margin:0;">
          <label>Cantidad</label>
          <input type="number" min="0.01" step="0.01" class="partida-cantidad" placeholder=" " value="${esc(data.cantidad ?? 1)}">
        </div>
        <div class="field" style="margin:0;">
          <label>Precio unitario</label>
          <input type="number" min="0" step="0.01" class="partida-precio" placeholder=" " value="${esc(data.precio_unitario ?? 0)}">
        </div>
      </div>

      <div class="partida-total-clean">
        <span>Importe</span>
        <strong class="partida-importe">$0.00</strong>
      </div>`;
    partidasBox.appendChild(row);
    updatePartidaRow(row);
    updatePartidasIndexes();
    updateRemisionTotals();
  }

  function updatePartidasIndexes(){
    [...partidasBox.querySelectorAll('.partida-row')].forEach((row, i)=>{
      const indexEl = row.querySelector('.partida-index');
      const itemEl = row.querySelector('.partida-item');
      if(indexEl) indexEl.textContent = `Partida ${i+1}`;
      if(itemEl && !itemEl.value.trim()) itemEl.value = `Partida ${i+1}`;
    });
  }

  function updatePartidaRow(row){
    const cantidad = Math.max(0, parseFloat(row.querySelector('.partida-cantidad')?.value || 0) || 0);
    const precio = Math.max(0, parseFloat(row.querySelector('.partida-precio')?.value || 0) || 0);
    const importe = cantidad * precio;
    const lbl = row.querySelector('.partida-importe');
    if(lbl) lbl.textContent = money(importe);
  }

  function getPartidasData(){
    return [...partidasBox.querySelectorAll('.partida-row')].map((row, i)=>{
      const item = row.querySelector('.partida-item')?.value?.trim() || `Partida ${i+1}`;
      const descripcion = row.querySelector('.partida-descripcion')?.value?.trim() || '';
      const unidad = row.querySelector('.partida-unidad')?.value?.trim() || 'SERVICIO';
      const cantidad = Math.max(0.01, parseFloat(row.querySelector('.partida-cantidad')?.value || 1) || 1);
      const precio_unitario = Math.max(0, parseFloat(row.querySelector('.partida-precio')?.value || 0) || 0);
      return { item, descripcion, unidad, cantidad, precio_unitario };
    }).filter(x => x.item || x.descripcion);
  }

  function setPartidasData(partidas){
    partidasBox.innerHTML = '';
    if(Array.isArray(partidas) && partidas.length){
      partidas.forEach(p => createPartidaRow(p));
    }else{
      createPartidaRow();
    }
    updateRemisionTotals();
  }

  function rebuildPartidas(forceAiBase = true){
    const nuevas = buildPartidasFromActions(forceAiBase);
    setPartidasData(nuevas);
    userTouchedPartidas = false;
  }

  function buildRemisionDescripcion(){
    const getVal = s => $(s)?.value?.trim() || '';
    const servicioVal = ($('#mto #tipo_mantenimiento')?.value || $('#mto #servicio')?.value || 'preventivo').toLowerCase();
    let base;
    if(servicioVal === 'preventivo') base = 'MANTENIMIENTO PREVENTIVO';
    else if(servicioVal === 'mixto') base = 'MANTENIMIENTO PREVENTIVO / CORRECTIVO';
    else base = 'MANTENIMIENTO CORRECTIVO';

    const eq     = getVal('#mto #equipo').toUpperCase();
    const marca  = getVal('#mto #marca').toUpperCase();
    const modelo = getVal('#mto #modelo').toUpperCase();
    const serie  = getVal('#mto #numero_serie').toUpperCase();

    let frase = base;
    if(eq) frase += ' A ' + eq;
    const mm = [marca, modelo].filter(Boolean).join(' ');
    if(mm) frase += ' ' + mm;
    if(serie) frase += ' CON NÚMERO DE SERIE ' + serie;
    frase = frase.trim();
    if(!frase.endsWith('.')) frase += '.';

    const acts = getCheckedActions()
      .map(t=>{
        t = t.replace(/^[\-•]+/, '').trim();
        t = t.replace(/\.+$/, '');
        return t.charAt(0).toUpperCase() + t.slice(1);
      });

    let detalle = '';
    if(acts.length === 1) detalle = acts[0] + '.';
    else if(acts.length > 1){
      detalle = acts.slice(0,-1).join(', ') + ' y ' + acts.slice(-1)[0] + '.';
    }

    return (frase + (detalle ? ' ' + detalle : '')).trim();
  }

  let __lastAutoDesc = '';
  function refreshRemisionDescripcion(){
    const txt = $('#mto #remision_descripcion');
    if(!txt) return;

    const auto = buildRemisionDescripcion();
    const currentVal = (txt.value || '').trim();

    if(!currentVal || currentVal === __lastAutoDesc){
      txt.value = auto;
      __lastAutoDesc = auto;
    } else {
      __lastAutoDesc = auto;
    }
  }

  function updateRemisionTotals(){
    const envio = Math.max(0, num($('#mto #remision_envio')));
    const anticipo = Math.max(0, num($('#mto #remision_anticipo')));
    const requiereIVA = !!($('#mto #remision_requiere_iva')?.checked);

    const partidas = getPartidasData();
    let subtotalPartidas = 0;
    let cantidadTotal = 0;

    [...partidasBox.querySelectorAll('.partida-row')].forEach((row)=>{
      updatePartidaRow(row);
    });

    partidas.forEach(p=>{
      subtotalPartidas += (p.cantidad * p.precio_unitario);
      cantidadTotal += p.cantidad;
    });

    const subtotal = subtotalPartidas + envio;
    const iva = requiereIVA ? (subtotal * 0.16) : 0;
    const total = subtotal + iva;
    const pagar = Math.max(0, total - anticipo);

    $('#mto #remision_subtotal_lbl').textContent = money(subtotal);
    $('#mto #remision_iva_lbl').textContent = money(iva);
    $('#mto #remision_total_lbl').textContent = money(total);
    $('#mto #remision_anticipo_lbl').textContent = money(anticipo);
    $('#mto #remision_pagar_lbl').textContent = money(pagar);

    $('#mto #remision_subtotal').value = subtotal.toFixed(2);
    $('#mto #remision_iva').value = iva.toFixed(2);
    $('#mto #remision_total').value = total.toFixed(2);
    $('#mto #remision_total_pagar').value = pagar.toFixed(2);
    $('#mto #remision_partidas').value = JSON.stringify(partidas);

    const cantidadHidden = $('#mto #remision_cantidad');
    const precioHidden = $('#mto #remision_precio');

    if(cantidadHidden) cantidadHidden.value = Math.max(1, Math.round(cantidadTotal || 1));
    if(precioHidden) precioHidden.value = subtotalPartidas.toFixed(2);
  }

  function renderDiagnosisData(data){
    renderDiagnosis(
      data.diagnostico || {},
      data.riesgos_seguridad || [],
      data.notas || null,
      data.resumen_ingenieria || []
    );
  }

  btnIA?.addEventListener('click', async ()=>{
    const equipo = $('#mto #equipo').value.trim();
    const libre  = $('#mto #nombre_equipo_libre').value.trim();
    if(!equipo && !libre){
      alert('Escribe el Equipo (Paso 2) o un texto opcional.');
      return;
    }

    const tipoMantenimiento = $('#mto #tipo_mantenimiento').value || 'preventivo';
    const servicioSel = $('#mto #servicio');
    if(servicioSel && servicioSel.value !== tipoMantenimiento){
      servicioSel.value = tipoMantenimiento;
    }

    const servicio = $('#mto #servicio').value;
    const sintomas = $('#mto #sintomas').value.trim();
    const marca    = $('#mto #marca').value.trim();
    const modelo   = $('#mto #modelo').value.trim();
    const nserie   = $('#mto #numero_serie').value.trim();
    const obs      = $('#mto #observaciones').value.trim();

    setBtnLoading(true);
    setOverlay(true);
    showSkeleton();

    const ctrl = new AbortController();
    const t = setTimeout(()=>ctrl.abort(), 45000);

    try{
      const body = libre
        ? { nombre_equipo: libre, servicio, sintomas }
        : { equipo, marca, modelo, numero_serie:nserie, observaciones:obs, servicio, sintomas };

      const res = await fetch(`{{ route('ai.checklist') }}`,{
        method:'POST',
        headers:{ 'X-CSRF-TOKEN':'{{ csrf_token() }}','Content-Type':'application/json' },
        body:JSON.stringify(body),
        signal:ctrl.signal
      });

      if(!res.ok) throw new Error('Network');
      const data = await res.json();
      lastAIData = data || null;

      hideSkeleton();
      renderPreventivo(data.secciones||[]);
      renderRealizado(data.acciones_sugeridas||[]);
      renderDiagnosisData(data);

      if(!userTouchedPartidas){
        rebuildPartidas(true);
      }
    }catch(err){
      console.error(err);
      hideSkeleton();
      preventivoBox.innerHTML = `<div class="item" style="border-color:var(--danger);background:var(--danger-soft);color:var(--danger)">Error generando el checklist con la IA. Intenta nuevamente.</div>`;
    }finally{
      clearTimeout(t);
      setBtnLoading(false);
      setOverlay(false);
      updateRemisionTotals();
      refreshRemisionDescripcion();
    }
  });

  $('#mto #btn-add-partida')?.addEventListener('click', ()=>{
    userTouchedPartidas = true;
    createPartidaRow();
    updateRemisionTotals();
  });

  $('#mto #btn-rebuild-partidas')?.addEventListener('click', ()=>{
    rebuildPartidas(true);
  });

  partidasBox?.addEventListener('input', (e)=>{
    const row = e.target.closest('.partida-row');
    if(row) updatePartidaRow(row);
    userTouchedPartidas = true;
    updateRemisionTotals();
  });

  partidasBox?.addEventListener('click', (e)=>{
    const btn = e.target.closest('.btn-del-partida');
    if(!btn) return;
    const rows = partidasBox.querySelectorAll('.partida-row');
    if(rows.length <= 1){
      alert('Debe existir al menos una partida.');
      return;
    }
    userTouchedPartidas = true;
    btn.closest('.partida-row')?.remove();
    updatePartidasIndexes();
    updateRemisionTotals();
  });

  $('#mto #accion_libre').addEventListener('keydown', e=>{
    if(e.key==='Enter'){
      e.preventDefault();
      const txt=e.target.value.trim();
      if(!txt) return;
      const wrap=document.createElement('div');
      wrap.className='item';
      wrap.innerHTML=`<label style="display:flex;align-items:center;gap:10px;justify-content:space-between;width:100%">
        <span><input type="checkbox" name="mto_realizado[]" value="${txt.replaceAll('"','&quot;')}" checked> ${txt}</span>
        <button type="button" class="del del-act" title="Eliminar acción">X</button></label>`;
      $('#mto #realizado-box').appendChild(wrap);
      e.target.value='';
      if(!userTouchedPartidas) rebuildPartidas(false);
      updateLive();
    }
  });

  $('#mto #realizado-box').addEventListener('click', e=>{
    const btn = e.target.closest('.del-act');
    if(!btn) return;
    btn.closest('.item')?.remove();
    if(!userTouchedPartidas) rebuildPartidas(false);
    updateLive();
    if(current===3) fillConfirm();
  });

  $('#mto #realizado-box').addEventListener('change', e=>{
    if(e.target.matches('input[name="mto_realizado[]"]') && !userTouchedPartidas){
      rebuildPartidas(false);
    }
    updateLive();
  });

  $('#mto #preventivo-box').addEventListener('click', e=>{
    const btn = e.target.closest('.del-item');
    if(!btn) return;
    const row = btn.closest('.item');
    if(!row) return;
    const hasHidden = row.querySelector('input[name^="mto_preventivo"][name$="[item]"]');
    if(!hasHidden) return;
    row.remove();

    const rows = [...document.querySelectorAll('#mto #preventivo-box .item')];
    let idx=0;
    rows.forEach(el=>{
      const it = el.querySelector('input[name^="mto_preventivo"][name$="[item]"]');
      const sc = el.querySelector('input[name^="mto_preventivo"][name$="[seccion]"]');
      const st = el.querySelector('select[name^="mto_preventivo"][name$="[estatus]"]');
      if(it && sc && st){
        it.name = `mto_preventivo[${idx}][item]`;
        sc.name = `mto_preventivo[${idx}][seccion]`;
        st.name = `mto_preventivo[${idx}][estatus]`;
        idx++;
      }
    });

    updateLive();
    if(current===3) fillConfirm();
  });

  function cls(st){
    const s=(st||'').toLowerCase();
    if(s.includes('bueno')) return 'color: var(--success); background: var(--success-soft); border:none;';
    if(s.includes('revisado')) return 'color: #0284c7; background: #e0f2fe; border:none;';
    if(s.includes('ajust')) return 'color: #d97706; background: #fef3c7; border:none;';
    if(s.includes('repar')) return 'color: var(--danger); background: var(--danger-soft); border:none;';
    if(s.includes('reempl')) return 'color: #dc2626; background: #fee2e2; border:none;';
    if(s.includes('realizado')) return 'color: #16a34a; background: #dcfce7; border:none;';
    if(s.includes('no aplica')) return 'color: var(--muted); background: var(--line); border:none;';
    return '';
  }

  function fillConfirm(){
    const val = s => $(s)?.value?.trim() || '';
    const cliente = $('#mto #cliente_search')?.value?.trim() || '';
    const tecnicoText = $('#mto #tecnico_id option:checked')?.textContent?.trim() || '—';

    $('#mto #pvBadgeCliente').textContent = cliente || 'Cliente';
    $('#mto #c_cliente').textContent      = cliente || '—';
    $('#mto #c_fentrada').textContent     = val('#mto #fecha_entrada') || '—';
    $('#mto #c_fmanto').textContent       = val('#mto #fecha_mantenimiento') || '—';
    $('#mto #c_tecnico').textContent      = tecnicoText;
    $('#mto #c_equipo').textContent       = val('#mto #equipo') || '(no especificado)';
    $('#mto #c_marca').textContent        = val('#mto #marca') || '—';
    $('#mto #c_modelo').textContent       = val('#mto #modelo') || '—';
    $('#mto #c_serie').textContent        = val('#mto #numero_serie') || '—';
    $('#mto #c_prox').textContent         = $('#mto #proximo_mantenimiento option:checked')?.textContent || '—';

    const pv1 = $('#mto #pv_foto_1');
    const pv2 = $('#mto #pv_foto_2');
    const pv3 = $('#mto #pv_foto_3');

    if(pv1 && fotoDataURL1){ pv1.src = fotoDataURL1; pv1.style.display='block'; } else if(pv1){ pv1.style.display='none'; }
    if(pv2 && fotoDataURL2){ pv2.src = fotoDataURL2; pv2.style.display='block'; } else if(pv2){ pv2.style.display='none'; }
    if(pv3 && fotoDataURL3){ pv3.src = fotoDataURL3; pv3.style.display='block'; } else if(pv3){ pv3.style.display='none'; }

    const tbody = $('#mto #pvChecklist tbody');
    tbody.innerHTML='';
    const items = [...document.querySelectorAll('[name^="mto_preventivo"][name$="[item]"]')];
    const get = (i,k)=> document.querySelector(`[name="mto_preventivo[${i}][${k}]"]`)?.value || '';
    let okCount=0;

    items.forEach((it,i)=>{
      const tr=document.createElement('tr');
      const est = get(i,'estatus');
      const style = cls(est);
      if(est.toLowerCase().includes('bueno')) okCount++;
      tr.innerHTML=`<td style="padding: 12px 8px; border-bottom: 1px solid var(--line); font-weight: 600; color: var(--ink-dark);">${it.value}</td>
                    <td style="padding: 12px 8px; border-bottom: 1px solid var(--line); color: var(--ink);">${get(i,'seccion')}</td>
                    <td style="padding: 12px 8px; border-bottom: 1px solid var(--line);"><span style="padding: 4px 10px; border-radius: 999px; font-size: 11px; font-weight: 700; ${style}">${est}</span></td>`;
      tbody.appendChild(tr);
    });

    $('#mto #pvTotalItems').textContent = items.length;
    $('#mto #pvOkCount').textContent   = okCount;

    const actsWrap = $('#mto #pvAcciones');
    actsWrap.innerHTML='';
    const acts = [...document.querySelectorAll('#mto input[name="mto_realizado[]"]:checked')].map(x=>x.value);
    if(acts.length){
      acts.forEach(t=>{
        const ch=document.createElement('span');
        ch.className='chip';
        ch.textContent=t;
        actsWrap.appendChild(ch);
      });
    } else {
      actsWrap.innerHTML='<span class="chip" style="opacity:.7; border: 1px dashed var(--muted);">Sin acciones seleccionadas</span>';
    }
  }

  function updateLive(){
    const servicioSel = $('#mto #servicio');
    const tipoSel = $('#mto #tipo_mantenimiento');
    if(servicioSel && tipoSel && servicioSel.value !== tipoSel.value){
      servicioSel.value = tipoSel.value || 'preventivo';
    }

    refreshRemisionDescripcion();
    updateRemisionTotals();
  }

  ['#cliente_search','#fecha_entrada','#fecha_mantenimiento','#tecnico_id','#proximo_mantenimiento','#tipo_mantenimiento','#equipo','#marca','#modelo','#numero_serie','#observaciones','#servicio','#sintomas'].forEach(sel=>{
    const el=$(sel);
    el?.addEventListener('input',updateLive);
    el?.addEventListener('change',updateLive);
  });

  ['#remision_envio','#remision_anticipo'].forEach(sel=>{
    const el=$(sel);
    el?.addEventListener('input',updateRemisionTotals);
  });

  $('#mto #tipo_mantenimiento')?.addEventListener('change', ()=>{
    if(!userTouchedPartidas) rebuildPartidas(false);
  });

  $('#mto #equipo')?.addEventListener('input', ()=>{
    if(!userTouchedPartidas) rebuildPartidas(false);
  });

  $('#mto #marca')?.addEventListener('input', ()=>{
    if(!userTouchedPartidas) rebuildPartidas(false);
  });

  $('#mto #modelo')?.addEventListener('input', ()=>{
    if(!userTouchedPartidas) rebuildPartidas(false);
  });

  $('#mto #remision_requiere_iva')?.addEventListener('change', updateRemisionTotals);

  $('#mto #wizard-form').addEventListener('submit', function(e){
    const partidas = getPartidasData();
    const tipoMantenimiento = document.querySelector('#mto #tipo_mantenimiento');

    if(!tipoMantenimiento?.value){
      e.preventDefault();
      alert('Debes seleccionar el tipo de mantenimiento.');
      tipoMantenimiento?.focus();
      return;
    }

    if(!partidas.length){
      e.preventDefault();
      alert('Debes agregar al menos una partida para la remisión.');
      return;
    }

    const invalidPartida = partidas.find(p => !p.descripcion || p.cantidad <= 0 || p.precio_unitario < 0);
    if(invalidPartida){
      e.preventDefault();
      alert('Revisa las partidas: cada una debe tener descripción, cantidad válida y precio.');
      return;
    }

    $('#mto #remision_partidas').value = JSON.stringify(partidas);
    updateRemisionTotals();
  });

  rebuildPartidas(false);
  show(0);
})();
</script>

<script>
window.addEventListener('load', ()=>{
  if(!window.gsap) return;
  try{
    const svg = document.querySelector('#mto #mtoSVG');
    if(!svg) return;
    gsap.set(svg, {visibility:'visible'});

    const rings = gsap.utils.toArray('#mto .ell');
    const easeA = CustomEase.create("ea","M0,0 C0.2,0 0.432,0.147 0.507,0.374 0.59,0.629 0.822,1 1,1 ");
    const easeB = CustomEase.create("eb","M0,0 C0.266,0.412 0.297,0.582 0.453,0.775 0.53,0.87 0.78,1 1,1 ");
    const easeC = CustomEase.create("ec","M0,0 C0.594,0.062 0.79,0.698 1,1 ");
    const interp = gsap.utils.interpolate(["#007aff","#3b82f6","#60a5fa","#93c5fd"]);

    function ringAnim(el, i){
      gsap.set(el,{opacity:1-(i/rings.length), stroke:interp(i/rings.length)});
      const tl = gsap.timeline({defaults:{ease:easeA}, repeat:-1}).timeScale(.5);
      tl.to(el,{attr:{ry:`-=${(i+1)*2.3}`, rx:`+=${(i+1)*1.4}`}, ease:easeC})
        .to(el,{attr:{ry:`+=${(i+1)*2.3}`, rx:`-=${(i+1)*1.4}`}, ease:easeB})
        .to(el,{duration:1, rotation:-180, transformOrigin:"50% 50%"}, 0);
    }
    rings.forEach((el, i)=>{ gsap.delayedCall(i/(rings.length-1), ringAnim, [el, i]); });

    gsap.to('#mto #mtoGrad',{duration:4,delay:.75,attr:{x1:"-=300",x2:"-=300"},scale:1.2,transformOrigin:"50% 50%",repeat:-1,ease:"none"});
    gsap.to('#mto #ai',{duration:1,scale:1.08,transformOrigin:"50% 50%",repeat:-1,yoyo:true,ease:easeA, opacity:1});
  }catch(err){ console.error('GSAP init error', err); }
});

(function(){
  const preventivoBox = document.querySelector('#mto #preventivo-box');
  const realizadoBox  = document.querySelector('#mto #realizado-box');
  if(!preventivoBox || !realizadoBox) return;

  const animate = (nodes)=>{
    nodes.forEach(node=>{
      if(node.nodeType===1 && node.classList && node.classList.contains('item')){
        node.style.opacity = '0';
        node.style.transform = 'translateY(10px)';
        node.style.transition = 'all 0.3s ease';
        setTimeout(()=> {
          node.style.opacity = '1';
          node.style.transform = 'translateY(0)';
        }, 20);
      }
    });
  };
  const opt = {childList:true};
  new MutationObserver(muts=> muts.forEach(m=> m.addedNodes?.length && animate(m.addedNodes))).observe(preventivoBox, opt);
  new MutationObserver(muts=> muts.forEach(m=> m.addedNodes?.length && animate(m.addedNodes))).observe(realizadoBox, opt);
})();
</script>

<script>
window.openCreateClientModal = function () {
  const el = document.getElementById("modal_formulario");
  if (!el) { alert("Error: No se encontró el modal de crear cliente."); return; }
  if (typeof bootstrap === "undefined" || !bootstrap.Modal) { alert("Error: Bootstrap no está cargado."); return; }
  const instance = bootstrap.Modal.getOrCreateInstance(el, { backdrop: 'static', keyboard: true });
  instance.show();
};

document.addEventListener('DOMContentLoaded', () => {
  const btn = document.getElementById('btn_guardar_cliente');
  if (!btn) return;

  const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
  const urlStore = @json( route('clientes.store') );

  const $err = document.getElementById('mc_error');
  const $ok  = document.getElementById('mc_ok');

  function showErr(msg){
    $ok.classList.add('d-none');
    $err.textContent = msg;
    $err.classList.remove('d-none');
  }
  function showOk(msg){
    $err.classList.add('d-none');
    $ok.textContent = msg;
    $ok.classList.remove('d-none');
  }

  btn.addEventListener('click', async () => {
    btn.disabled = true;
    btn.textContent = 'Guardando...';
    $err.classList.add('d-none');
    $ok.classList.add('d-none');

    const payload = {
      nombre: (document.getElementById('mc_nombre')?.value || '').trim(),
      apellido: (document.getElementById('mc_apellido')?.value || '').trim(),
      telefono: (document.getElementById('mc_telefono')?.value || '').trim(),
      email: (document.getElementById('mc_email')?.value || '').trim(),
      comentarios: (document.getElementById('mc_comentarios')?.value || '').trim(),
    };

    if (!payload.nombre) {
      showErr('El nombre es obligatorio.');
      btn.disabled = false;
      btn.textContent = 'Guardar cliente';
      return;
    }

    try {
      const res = await fetch(urlStore, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrf,
          'Accept': 'application/json'
        },
        body: JSON.stringify(payload)
      });

      const data = await res.json().catch(() => ({}));

      if (!res.ok) {
        const msg = data?.message || (data?.errors ? Object.values(data.errors).flat().join(' ') : 'No se pudo crear el cliente.');
        showErr(msg);
      } else {
        showOk('Cliente creado correctamente.');
        showClienteToast('Cliente creado correctamente.');

        if (window.setClienteDesdeModal) {
          window.setClienteDesdeModal(data.cliente || data);
        }

        const el = document.getElementById("modal_formulario");
        if (typeof bootstrap !== 'undefined' && bootstrap.Modal && el) {
          const instance = bootstrap.Modal.getOrCreateInstance(el);
          setTimeout(() => instance.hide(), 600);
        }
      }
    } catch (e) {
      console.error(e);
      showErr('Error de red al crear el cliente.');
    } finally {
      btn.disabled = false;
      btn.textContent = 'Guardar cliente';
    }
  });
});
</script>
@endsection