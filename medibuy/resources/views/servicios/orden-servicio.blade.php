@extends('layouts.app')
@section('title', 'Validar Orden de Servicio')
@section('titulo', 'Validar Orden de Servicio')

@section('content')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@500;600;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

<style>
:root {
  /* PALETA CORPORATIVA ESTRICTA */
  --bg: #f9fafb; 
  --card: #ffffff; 
  --title: #111111;
  --ink: #333333; 
  --muted: #888888; 
  --line: #ebebeb; 
  --blue: #007aff; 
  --blue-soft: #e6f0ff; 
  --success: #15803d; 
  --success-soft: #e6ffe6;  
  --danger: #ff4a4a; 
  --danger-soft: #ffebeb;

  /* VARIABLES DE DISEÑO MÍNIMO */
  --radius-sm: 8px;
  --radius-md: 12px;
  --radius-lg: 16px;
  --shadow-base: 0 4px 12px rgba(0,0,0,0.02);
  --shadow-hover: 0 8px 24px rgba(0,0,0,0.06);
  --shadow-focus: 0 0 0 3px var(--blue-soft);
  --transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
  --font-family: 'Quicksand', sans-serif;
}

/* RESET & BASE */
* { box-sizing: border-box; margin: 0; padding: 0; }
body {
  background-color: var(--bg);
  font-family: var(--font-family);
  color: var(--ink);
  -webkit-font-smoothing: antialiased;
}
h1, h2, h3, h4, h5, h6 { color: var(--title); font-weight: 700; }
a { text-decoration: none; }

/* LAYOUT PRINCIPAL */
.form-wrapper {
  max-width: 1000px;
  margin: 0 auto;
  padding: 48px 24px;
}

/* ANIMACIONES */
@keyframes fadeUp {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}
.animate-enter { animation: fadeUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards; opacity: 0; }
.delay-1 { animation-delay: 0.1s; }
.delay-2 { animation-delay: 0.2s; }

/* CABECERA */
.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 32px;
  flex-wrap: wrap;
  gap: 24px;
}
.page-title-block h1 {
  font-size: 2rem;
  letter-spacing: -0.02em;
  margin-bottom: 8px;
}
.page-subtitle {
  color: var(--muted);
  font-size: 0.95rem;
  font-weight: 600;
  display: flex;
  align-items: center;
  gap: 12px;
}

/* ALERTAS */
.alert-box {
  padding: 16px 24px;
  border-radius: var(--radius-md);
  margin-bottom: 24px;
  display: flex;
  align-items: flex-start;
  gap: 12px;
  font-weight: 600;
  font-size: 0.95rem;
  line-height: 1.5;
  box-shadow: var(--shadow-base);
}
.alert-box i { font-size: 1.25rem; }
.alert-box.success { background: var(--success-soft); color: var(--success); border: 1px solid rgba(21, 128, 61, 0.2); }
.alert-box.danger { background: var(--danger-soft); color: var(--danger); border: 1px solid rgba(255, 74, 74, 0.2); }
.alert-box.info { background: var(--blue-soft); color: var(--blue); border: 1px solid rgba(0, 122, 255, 0.2); }

/* GRID DE CONTENIDO */
.content-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
  gap: 24px;
}

/* TARJETAS */
.card {
  background: var(--card);
  border: 1px solid var(--line);
  border-radius: var(--radius-lg);
  box-shadow: var(--shadow-base);
  display: flex;
  flex-direction: column;
  transition: var(--transition);
  overflow: hidden;
  height: 100%;
}
.card:hover {
  transform: translateY(-2px);
  box-shadow: var(--shadow-hover);
}
.card-header {
  padding: 20px 24px;
  border-bottom: 1px solid var(--line);
  font-weight: 700;
  color: var(--title);
  font-size: 1.05rem;
  display: flex;
  align-items: center;
  justify-content: space-between;
}
.card-header i { color: var(--muted); margin-right: 8px; }
.card-body {
  padding: 24px;
  flex-grow: 1;
  display: flex;
  flex-direction: column;
}

/* BADGES */
.badge {
  padding: 4px 12px;
  border-radius: 20px;
  font-weight: 700;
  font-size: 0.75rem;
  letter-spacing: 0.05em;
  display: inline-flex;
  align-items: center;
}
.badge.primary { background: var(--blue-soft); color: var(--blue); }
.badge.info { background: var(--bg); color: var(--muted); border: 1px solid var(--line); }

/* LISTA DE DATOS */
.data-list {
  display: flex;
  flex-direction: column;
  gap: 16px;
  margin-bottom: 24px;
}
.data-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-bottom: 16px;
  border-bottom: 1px dashed var(--line);
}
.data-row:last-child { border-bottom: none; padding-bottom: 0; }
.data-label {
  font-size: 0.8rem;
  font-weight: 700;
  color: var(--muted);
  text-transform: uppercase;
  letter-spacing: 0.05em;
}
.data-value {
  font-size: 0.95rem;
  font-weight: 700;
  color: var(--title);
  text-align: right;
}
.data-value.highlight { color: var(--blue); }

/* ÁREA DE ACCIONES SECUNDARIAS */
.related-actions {
  margin-top: auto;
  padding-top: 20px;
  border-top: 1px solid var(--line);
}
.related-actions-title {
  display: block;
  font-size: 0.8rem;
  font-weight: 700;
  color: var(--muted);
  text-transform: uppercase;
  margin-bottom: 12px;
}
.related-actions-flex { display: flex; gap: 12px; flex-wrap: wrap; }

/* FORMULARIOS E INPUTS */
.form-group { margin-bottom: 24px; }
.form-label {
  display: block;
  font-weight: 700;
  color: var(--title);
  margin-bottom: 8px;
  font-size: 0.95rem;
}
.form-label .required { color: var(--danger); }

.premium-input {
  width: 100%;
  padding: 14px 16px;
  background: var(--card);
  border: 1px solid var(--line);
  border-radius: var(--radius-sm);
  font-family: var(--font-family);
  font-size: 1rem;
  font-weight: 600;
  color: var(--ink);
  transition: var(--transition);
}
.premium-input:focus {
  outline: none;
  border-color: var(--blue);
  box-shadow: var(--shadow-focus);
}
.premium-input::placeholder { color: #d1d5db; font-weight: 500; }

.input-hint {
  margin-top: 12px;
  font-size: 0.85rem;
  color: var(--muted);
  display: flex;
  align-items: flex-start;
  gap: 8px;
  line-height: 1.5;
}

.error-text {
  color: var(--danger);
  font-size: 0.85rem;
  font-weight: 700;
  margin-top: 8px;
  display: flex;
  align-items: center;
  gap: 6px;
}

/* OS VINCULADA RESUMEN */
.os-summary {
  background: var(--success-soft);
  border: 1px solid rgba(21, 128, 61, 0.2);
  border-radius: var(--radius-sm);
  padding: 16px;
  margin-bottom: 24px;
  margin-top: auto;
}
.os-summary-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px; }
.os-summary-row:last-child { margin-bottom: 0; }
.os-summary-label { font-weight: 700; color: var(--success); font-size: 0.9rem; }
.os-summary-value { font-weight: 700; color: var(--success); font-size: 0.9rem; }
.os-summary-sub { font-size: 0.8rem; color: #166534; font-weight: 600; }

/* BOTONES */
.btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  padding: 12px 24px;
  border-radius: var(--radius-sm);
  font-family: var(--font-family);
  font-weight: 700;
  font-size: 0.95rem;
  transition: var(--transition);
  cursor: pointer;
  border: none;
  width: 100%;
}
.btn-primary { background: var(--blue); color: var(--card); }
.btn-primary:hover { transform: translateY(-1px); box-shadow: 0 6px 16px rgba(0, 122, 255, 0.2); }
.btn-primary:active { transform: scale(0.98); }

.btn-ghost {
  background: transparent;
  color: #555555;
  width: auto;
}
.btn-ghost:hover { background: var(--bg); color: var(--title); }

.btn-outline {
  background: var(--card);
  border: 1px solid var(--blue);
  color: var(--blue);
}
.btn-outline:hover { background: var(--blue-soft); transform: translateY(-1px); }

/* ESTILOS DEL FORMULARIO FLEXIBLE */
.auth-form {
  display: flex;
  flex-direction: column;
  height: 100%;
}
.form-actions { margin-top: auto; }

/* RESPONSIVO */
@media (max-width: 768px) {
  .page-header { flex-direction: column; align-items: flex-start; }
  .content-grid { grid-template-columns: 1fr; }
  .btn-ghost { width: 100%; }
}
</style>

<div class="form-wrapper">

  <div class="page-header animate-enter">
    <div class="page-title-block">
      <h1>Validación de Servicio</h1>
      <div class="page-subtitle">
        Control de Mantenimiento <span class="badge primary">INTERNO</span>
      </div>
    </div>
    <a href="{{ url('/inventario/servicio') }}" class="btn btn-ghost">
      <i class="bi bi-arrow-left"></i> Regresar al Inventario
    </a>
  </div>

  @if(session('error'))
    <div class="alert-box danger animate-enter">
      <i class="bi bi-exclamation-octagon-fill"></i>
      <div>{{ session('error') }}</div>
    </div>
  @endif
  @if(session('ok'))
    <div class="alert-box success animate-enter">
      <i class="bi bi-check-circle-fill"></i>
      <div>{{ session('ok') }}</div>
    </div>
  @endif

  <div class="alert-box info animate-enter" style="margin-bottom: 32px;">
    <i class="bi bi-info-circle-fill"></i>
    <div>
      <strong>Flujo requerido:</strong> Para dar continuidad al proceso de mantenimiento interno, es obligatorio vincular el código o folio de la Orden de Servicio correspondiente.
    </div>
  </div>

  <div class="content-grid">
    
    <div class="card animate-enter delay-1">
      <div class="card-header">
        <span><i class="bi bi-hdd-network"></i> Detalles del Equipo</span>
        <span class="badge info">Servicio #{{ $servicio->id }}</span>
      </div>
      <div class="card-body">
        
        <div class="data-list">
          <div class="data-row">
            <span class="data-label">Tipo</span>
            <span class="data-value">{{ strtoupper($servicio->tipo_equipo ?? 'N/A') }}</span>
          </div>
          <div class="data-row">
            <span class="data-label">Subtipo</span>
            <span class="data-value">{{ strtoupper($servicio->subtipo_equipo ?? 'N/A') }}</span>
          </div>
          <div class="data-row">
            <span class="data-label">Marca / Modelo</span>
            <span class="data-value">{{ trim(($servicio->marca ?? '').' '.($servicio->modelo ?? '')) ?: 'N/A' }}</span>
          </div>
          <div class="data-row">
            <span class="data-label">Número de Serie</span>
            <span class="data-value">{{ $servicio->numero_serie ?? 'N/A' }}</span>
          </div>
          <div class="data-row" style="border: none;">
            <span class="data-label">Estado Actual</span>
            <span class="data-value highlight">{{ strtoupper($servicio->estado_proceso ?? 'N/A') }}</span>
          </div>
        </div>

        <div class="related-actions">
          <span class="related-actions-title">Acciones relacionadas</span>
          <div class="related-actions-flex">
            <a class="btn btn-ghost" style="padding: 8px 16px; font-size: 0.85rem;" target="_blank" href="{{ url('/ordenes/create') }}">
              <i class="bi bi-plus-circle"></i> Nueva OS
            </a>
            <a class="btn btn-ghost" style="padding: 8px 16px; font-size: 0.85rem;" target="_blank" href="{{ url('/ordenes') }}">
              <i class="bi bi-list-task"></i> Consultar Órdenes
            </a>
          </div>
        </div>

      </div>
    </div>

    <div class="card animate-enter delay-2">
      <div class="card-header">
        <span><i class="bi bi-shield-lock"></i> Autenticación de OS</span>
      </div>
      <div class="card-body">
        
        <form method="POST" action="{{ route('servicio.os.validar', $servicio->id) }}" class="auth-form">
          @csrf
          
          <div class="form-group">
            <label class="form-label">
              Folio o ID de la Orden de Servicio <span class="required">*</span>
            </label>
            <input
              type="text"
              name="orden_id"
              class="premium-input"
              value="{{ old('orden_id', $servicio->orden_id) }}"
              placeholder="Ej. OS-125A o 125"
              autocomplete="off"
              required>
            
            @error('orden_id')
              <div class="error-text">
                <i class="bi bi-exclamation-circle"></i> {{ $message }}
              </div>
            @enderror
            
            <div class="input-hint">
              <i class="bi bi-lightbulb" style="color: #f59e0b; font-size: 1rem;"></i> 
              Puede ingresar el folio alfanumérico o el ID interno que visualiza en el PDF de la orden.
            </div>
          </div>

          @if(!empty($servicio->orden_validada_at))
            <div class="os-summary">
              <div class="os-summary-row">
                <span class="os-summary-label">OS Vinculada:</span>
                <span class="os-summary-value">#{{ $servicio->orden_id ?? 'N/D' }}</span>
              </div>
              <div class="os-summary-row mt-1">
                <span class="os-summary-sub">Fecha de validación:</span>
                <span class="os-summary-sub">{{ \Carbon\Carbon::parse($servicio->orden_validada_at)->format('d M, Y - H:i') }}</span>
              </div>
            </div>
          @else
            <div style="flex-grow: 1;"></div>
          @endif

          <div class="form-actions">
            <button type="submit" class="btn btn-primary">
              <i class="bi bi-link-45deg fs-5"></i> Procesar y Vincular OS
            </button>
            
            @if(!empty($servicio->orden_validada_at) && !empty($servicio->orden_id))
              <a href="{{ route('servicio.show', $servicio->id) }}" class="btn btn-outline" style="margin-top: 12px;">
                Proceder al Servicio <i class="bi bi-arrow-right"></i>
              </a>
            @endif
          </div>

        </form>

      </div>
    </div>

  </div>
</div>
@endsection