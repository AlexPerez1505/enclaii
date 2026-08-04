@extends('layouts.app')
@section('title','Editar Orden de Servicio')
@section('titulo','Editar Orden')

@section('content')

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
body{
  background:#f8fafc;
  font-family:'Inter',system-ui,-apple-system,sans-serif;
  color:#0f172a;
  -webkit-font-smoothing:antialiased;
}
.card-clean{
  border-radius:16px;
  border:1px solid #e2e8f0;
  box-shadow:0 4px 6px -1px rgba(0,0,0,.05),0 2px 4px -2px rgba(0,0,0,.05);
  background:#fff;
  overflow:hidden;
}
.header-clean{
  padding:24px;
  border-bottom:1px solid #e2e8f0;
  display:flex; justify-content:space-between; align-items:center;
  flex-wrap:wrap; gap:16px;
  background:#fff;
}
.header-clean h4{ margin:0; font-weight:700; font-size:1.15rem; color:#0f172a; }
.header-sub{ font-size:.875rem; color:#64748b; margin-top:4px; }
.icon-box{
  width:48px; height:48px;
  background:#eff6ff; color:#2563eb;
  border-radius:12px; display:flex; align-items:center; justify-content:center;
  font-size:1.25rem;
}
.section-title{
  font-size:.8rem; letter-spacing:.05em; font-weight:600;
  color:#64748b; text-transform:uppercase; margin-bottom:4px;
}
.section-sub{
  font-size:.875rem; color:#64748b; margin-bottom:24px;
}
.form-label{ font-size:.875rem; font-weight:500; color:#334155; margin-bottom:8px; }
.form-control,.form-select{
  border-radius:10px!important;
  border:1px solid #cbd5e1!important;
  background:#f8fafc!important;
  padding:10px 14px!important;
  font-size:.95rem!important;
  color:#0f172a!important;
  min-height:44px!important;
  box-shadow:none!important;
  transition:all .2s ease!important;
}
.form-control:focus,.form-select:focus{
  background:#fff!important;
  border-color:#2563eb!important;
  box-shadow:0 0 0 3px rgba(37,99,235,.15)!important;
}
textarea.form-control{ min-height:100px; }
.invalid-feedback{ font-size:.8rem; font-weight:500; }
.btn{ display:inline-flex; align-items:center; gap:8px; padding:10px 18px; font-size:.9rem; font-weight:500; border-radius:10px; transition:all .2s ease; }
.btn:active{ transform:scale(.98); }
.btn-soft{ background:#fff; border:1px solid #e2e8f0; color:#334155; }
.btn-soft:hover{ background:#f8fafc; border-color:#cbd5e1; color:#0f172a; }
.btn-primary-clean{ background:#0f172a; color:#fff; border:none; box-shadow:0 1px 2px rgba(0,0,0,.05); }
.btn-primary-clean:hover{ background:#1e293b; color:#fff; }
.sidebar-wrapper{ position:sticky; top:24px; }
.kpi-item{ background:#f8fafc; padding:14px 16px; border-radius:10px; margin-bottom:12px; }
.kpi-label{ font-size:.75rem; text-transform:uppercase; letter-spacing:.05em; color:#64748b; font-weight:600; margin-bottom:4px; }
.kpi-value{ font-weight:600; font-size:.95rem; color:#0f172a; }
.client-picker{ position:relative; }
.client-results{
  position:absolute;
  left:0; right:0;
  top: calc(44px + 10px);
  background:#fff;
  border:1px solid #e2e8f0;
  border-radius:14px;
  box-shadow:0 18px 60px rgba(2,6,23,.14);
  overflow:hidden;
  z-index: 9999;
  display:none;
}
.client-results.show{ display:block; }
.client-results .list{ max-height: 320px; overflow:auto; }
.client-row{
  padding:14px 14px;
  cursor:pointer;
  display:flex;
  justify-content:space-between;
  gap:12px;
  border-top:1px solid #f1f5f9;
}
.client-row:first-child{ border-top:none; }
.client-row:hover{ background:#f8fafc; }
.client-name{ font-weight:800; letter-spacing:.01em; }
.client-meta{ font-size:.85rem; color:#64748b; margin-top:4px; }
.client-id{ font-size:.85rem; color:#94a3b8; white-space:nowrap; }
.client-empty{ padding:14px; color:#64748b; font-size:.9rem; }
.client-hint{ font-size:.82rem; color:#64748b; margin-top:6px; }
.photo-wrap{ display:flex; gap:14px; align-items:flex-start; flex-wrap:wrap; }
.photo-thumb{
  width:92px; height:92px;
  border-radius:14px;
  border:1px solid #e2e8f0;
  background:#f8fafc;
  overflow:hidden;
  display:grid; place-items:center;
  flex-shrink:0;
}
.photo-thumb img{ width:100%; height:100%; object-fit:cover; }
.photo-actions{ flex:1; min-width:220px; }
.partida-row{
  background:#f8fafc;
  border:1px solid #e2e8f0;
  border-radius:12px;
  padding:16px;
  margin-bottom:12px;
}
.checklist-row{
  background:#f8fafc;
  border:1px solid #e2e8f0;
  border-radius:10px;
  padding:12px 14px;
  margin-bottom:8px;
  display:flex;
  align-items:flex-start;
  gap:10px;
}
.badge-tip{
  display:inline-block;
  background:#eff6ff;
  color:#2563eb;
  font-size:.75rem;
  font-weight:600;
  border-radius:6px;
  padding:2px 8px;
  margin-bottom:8px;
}
</style>

@php
  $clienteFullName = function($c){
      $nom = trim((string)($c->nombre ?? ''));
      $ape = trim((string)($c->apellido ?? ''));
      $full = trim($nom.' '.$ape);
      return $full !== '' ? $full : ($c->nombre ?? 'Cliente');
  };

  $currentCliente = $clientes->firstWhere('id', (int)old('cliente_id', $orden->cliente_id));
  $currentClienteName = $currentCliente ? $clienteFullName($currentCliente) : '';

  $tipoMantenimientoActual = old(
      'tipo_mantenimiento',
      $orden->tipo_mantenimiento
      ?? $orden->tipo_servicio
      ?? $orden->servicio
      ?? 'preventivo'
  );

  $mtoPreventivo = old('mto_preventivo', $orden->mto_preventivo ?? []);
  $mtoRealizado  = old('mto_realizado',  $orden->mto_realizado  ?? []);

  $partidas = old('remision_partidas', $orden->remision_partidas ?? []);
  if(is_string($partidas)){
      $decoded = json_decode($partidas, true);
      $partidas = (json_last_error() === JSON_ERROR_NONE) ? $decoded : [];
  }
  if(empty($partidas)) $partidas = [];
@endphp

<div class="container py-4">
  <div class="row g-4">

    {{-- COLUMNA PRINCIPAL --}}
    <div class="col-lg-8">
      <div class="card-clean mb-4">

        <div class="header-clean">
          <div class="d-flex align-items-center gap-3">
            <div class="icon-box"><i class="bi bi-pencil-square"></i></div>
            <div>
              <h4>Editar Orden #{{ str_pad($orden->id, 5, '0', STR_PAD_LEFT) }}</h4>
              <div class="header-sub">Actualiza datos, remisión y checklist del equipo.</div>
            </div>
          </div>

          <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('ordenes.index') }}" class="btn btn-soft">
              <i class="bi bi-arrow-left"></i> Volver
            </a>
            @if(Route::has('ordenes.pdf'))
              <a href="{{ route('ordenes.pdf', $orden) }}" class="btn btn-soft text-primary" style="border-color:rgba(37,99,235,.2);background:#eff6ff;">
                <i class="bi bi-filetype-pdf"></i> PDF OS
              </a>
            @endif
            @if(Route::has('ordenes.remision-pdf'))
              <a href="{{ route('ordenes.remision-pdf', $orden) }}" class="btn btn-soft text-success" style="border-color:rgba(22,163,74,.2);background:#f0fdf4;">
                <i class="bi bi-receipt"></i> Remisión PDF
              </a>
            @endif
          </div>
        </div>

        <div class="p-4 p-md-5">
          <form method="POST" action="{{ route('ordenes.update', $orden) }}" enctype="multipart/form-data" id="ordenEditForm">
            @csrf
            @method('PUT')

            {{-- ─── DATOS GENERALES ─── --}}
            <div class="mb-5">
              <div class="section-title">Datos generales</div>
              <div class="section-sub">Información principal del cliente y el equipo.</div>

              <div class="row g-4">

                {{-- Cliente --}}
                <div class="col-md-6">
                  <label class="form-label">Cliente</label>
                  <input type="hidden" name="cliente_id" id="cliente_id"
                         value="{{ old('cliente_id', $orden->cliente_id) }}">
                  <div class="client-picker">
                    <input type="text" id="clienteSearch"
                           class="form-control @error('cliente_id') is-invalid @enderror"
                           placeholder="Nombre, apellido, email…"
                           autocomplete="off"
                           value="{{ old('cliente_search', $currentClienteName) }}">
                    <div class="client-results" id="clienteResults">
                      <div class="list" id="clienteList">
                        @foreach($clientes as $c)
                          @php
                            $full    = $clienteFullName($c);
                            $tel     = trim((string)($c->telefono ?? ''));
                            $email   = trim((string)($c->email ?? ''));
                            $empresa = trim((string)($c->empresa ?? ''));
                            $meta    = trim(implode(' • ', array_filter([$empresa ?: null, $email ?: null, $tel ?: null])));
                            $blob    = mb_strtolower($full.' '.$empresa.' '.$email.' '.$tel.' #'.$c->id, 'UTF-8');
                          @endphp
                          <div class="client-row" role="button" tabindex="0"
                               data-id="{{ $c->id }}"
                               data-name="{{ $full }}"
                               data-search="{{ e($blob) }}">
                            <div>
                              <div class="client-name">{{ mb_strtoupper($full,'UTF-8') }}</div>
                              @if($meta)<div class="client-meta">{{ $meta }}</div>@endif
                            </div>
                            <div class="client-id">#{{ $c->id }}</div>
                          </div>
                        @endforeach
                      </div>
                      <div class="client-empty d-none" id="clienteEmpty">Sin coincidencias.</div>
                    </div>
                  </div>
                  @error('cliente_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                  <div class="client-hint">Escribe para filtrar y haz click para seleccionar.</div>
                </div>

                {{-- Fecha entrada --}}
                <div class="col-md-3">
                  <label class="form-label">Fecha entrada</label>
                  <input type="date" name="fecha_entrada"
                         class="form-control @error('fecha_entrada') is-invalid @enderror"
                         value="{{ old('fecha_entrada', optional($orden->fecha_entrada)->format('Y-m-d')) }}">
                  @error('fecha_entrada')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Fecha mtto --}}
                <div class="col-md-3">
                  <label class="form-label">Fecha mtto.</label>
                  <input type="date" name="fecha_mantenimiento"
                         class="form-control @error('fecha_mantenimiento') is-invalid @enderror"
                         value="{{ old('fecha_mantenimiento', optional($orden->fecha_mantenimiento)->format('Y-m-d')) }}">
                  @error('fecha_mantenimiento')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Técnico --}}
                <div class="col-md-4">
                  <label class="form-label">Técnico</label>
                  <select name="tecnico_id" class="form-select @error('tecnico_id') is-invalid @enderror">
                    <option value="">Selecciona</option>
                    @foreach($usuariosServicio as $usuario)
                      <option value="{{ $usuario->id }}"
                        @selected((string)old('tecnico_id', $orden->tecnico_id) === (string)$usuario->id)>
                        {{ $usuario->name }}
                      </option>
                    @endforeach
                  </select>
                  @error('tecnico_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Tipo mantenimiento --}}
                <div class="col-md-4">
                  <label class="form-label">Tipo de mantenimiento</label>
                  <select name="tipo_mantenimiento" id="tipo_mantenimiento"
                          class="form-select @error('tipo_mantenimiento') is-invalid @enderror" required>
                    <option value="preventivo" @selected($tipoMantenimientoActual==='preventivo')>Preventivo</option>
                    <option value="correctivo" @selected($tipoMantenimientoActual==='correctivo')>Correctivo</option>
                    <option value="mixto"      @selected($tipoMantenimientoActual==='mixto')>Mixto</option>
                  </select>
                  @error('tipo_mantenimiento')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Próximo mtto --}}
                <div class="col-md-4">
                  <label class="form-label">Próximo mtto.</label>
                  <select name="proximo_mantenimiento" class="form-select @error('proximo_mantenimiento') is-invalid @enderror">
                    @foreach([3,6,12] as $m)
                      <option value="{{ $m }}"
                        @selected((int)old('proximo_mantenimiento',$orden->proximo_mantenimiento)==(int)$m)>
                        {{ $m }} meses
                      </option>
                    @endforeach
                  </select>
                  @error('proximo_mantenimiento')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Equipo --}}
                <div class="col-md-4">
                  <label class="form-label">Equipo</label>
                  <input type="text" name="equipo"
                         class="form-control @error('equipo') is-invalid @enderror"
                         value="{{ old('equipo', $orden->equipo) }}">
                  @error('equipo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Marca --}}
                <div class="col-md-4">
                  <label class="form-label">Marca</label>
                  <input type="text" name="marca"
                         class="form-control @error('marca') is-invalid @enderror"
                         value="{{ old('marca', $orden->marca) }}">
                  @error('marca')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Modelo --}}
                <div class="col-md-4">
                  <label class="form-label">Modelo</label>
                  <input type="text" name="modelo"
                         class="form-control @error('modelo') is-invalid @enderror"
                         value="{{ old('modelo', $orden->modelo) }}">
                  @error('modelo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- No. Serie --}}
                <div class="col-md-4">
                  <label class="form-label">Número de serie</label>
                  <input type="text" name="numero_serie"
                         class="form-control @error('numero_serie') is-invalid @enderror"
                         value="{{ old('numero_serie', $orden->numero_serie) }}">
                  @error('numero_serie')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Asistente IA --}}
                <div class="col-md-4">
                  <label class="form-label">Asistente IA</label>
                  <select name="usar_ia" class="form-select @error('usar_ia') is-invalid @enderror">
                    <option value="0" @selected((int)old('usar_ia',$orden->usar_ia??0)===0)>Inactivo</option>
                    <option value="1" @selected((int)old('usar_ia',$orden->usar_ia??0)===1)>Activo</option>
                  </select>
                  @error('usar_ia')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Observaciones --}}
                <div class="col-12">
                  <label class="form-label">Observaciones</label>
                  <textarea name="observaciones"
                            class="form-control @error('observaciones') is-invalid @enderror"
                            placeholder="Notas adicionales...">{{ old('observaciones', $orden->observaciones) }}</textarea>
                  @error('observaciones')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

              </div>
            </div>

            {{-- ─── FOTOS ─── --}}
            <div class="mb-5">
              <hr style="border-color:#e2e8f0;margin:0 0 28px 0;">
              <div class="section-title">Imágenes del equipo</div>
              <div class="section-sub">Puedes subir hasta 3 fotos del equipo.</div>

              @foreach([
                ['foto_equipo',   'quitar_foto',   $foto_url   ?? null, 'Foto 1 (principal)'],
                ['foto_equipo_2', 'quitar_foto_2', $foto_url_2 ?? null, 'Foto 2'],
                ['foto_equipo_3', 'quitar_foto_3', $foto_url_3 ?? null, 'Foto 3'],
              ] as [$field, $quitarField, $url, $label])
              <div class="photo-wrap mb-4">
                <div class="photo-thumb" id="thumb_{{ $field }}">
                  @if(!empty($url))
                    <img id="preview_{{ $field }}" src="{{ $url }}" alt="foto">
                  @else
                    <i class="bi bi-camera" style="font-size:22px;color:#64748b"></i>
                    <img id="preview_{{ $field }}" src="" alt="foto" style="display:none;">
                  @endif
                </div>
                <div class="photo-actions">
                  <label class="form-label">{{ $label }}</label>
                  <input type="file" name="{{ $field }}" id="{{ $field }}"
                         class="form-control @error($field) is-invalid @enderror"
                         accept="image/*"
                         data-preview="preview_{{ $field }}"
                         data-thumb="thumb_{{ $field }}">
                  @error($field)<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                  <div class="client-hint">JPG / PNG / WebP · Máx 5MB</div>
                  @if(!empty($url))
                    <div class="form-check mt-2">
                      <input class="form-check-input" type="checkbox" value="1"
                             id="{{ $quitarField }}" name="{{ $quitarField }}">
                      <label class="form-check-label" for="{{ $quitarField }}">Eliminar imagen actual</label>
                    </div>
                  @endif
                </div>
              </div>
              @endforeach
            </div>

            {{-- ─── REMISIÓN / PARTIDAS ─── --}}
            <div class="mb-5">
              <hr style="border-color:#e2e8f0;margin:0 0 28px 0;">
              <div class="section-title">Remisión · Partidas</div>
              <div class="section-sub">Agrega o edita las partidas del servicio. Los totales se recalculan al guardar.</div>

              <div id="partidasContainer">
                @forelse($partidas as $idx => $partida)
                <div class="partida-row" id="partida_{{ $idx }}">
                  <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="badge-tip">Partida #{{ $idx + 1 }}</span>
                    <button type="button" class="btn btn-soft btn-sm py-1 px-2 text-danger"
                            onclick="removePartida({{ $idx }})">
                      <i class="bi bi-trash3"></i>
                    </button>
                  </div>
                  <div class="row g-3">
                    <div class="col-md-4">
                      <label class="form-label">Nombre / ítem</label>
                      <input type="text" name="remision_partidas[{{ $idx }}][item]"
                             class="form-control"
                             value="{{ $partida['item'] ?? '' }}"
                             placeholder="Ej: Mantenimiento preventivo">
                    </div>
                    <div class="col-md-2">
                      <label class="form-label">Unidad</label>
                      <input type="text" name="remision_partidas[{{ $idx }}][unidad]"
                             class="form-control"
                             value="{{ $partida['unidad'] ?? 'SERVICIO' }}">
                    </div>
                    <div class="col-md-2">
                      <label class="form-label">Cantidad</label>
                      <input type="number" min="1" step="1"
                             name="remision_partidas[{{ $idx }}][cantidad]"
                             class="form-control partida-cantidad"
                             value="{{ $partida['cantidad'] ?? 1 }}">
                    </div>
                    <div class="col-md-2">
                      <label class="form-label">P. Unitario</label>
                      <input type="number" min="0" step="0.01"
                             name="remision_partidas[{{ $idx }}][precio_unitario]"
                             class="form-control partida-precio"
                             value="{{ $partida['precio_unitario'] ?? 0 }}">
                    </div>
                    <div class="col-md-2">
                      <label class="form-label">Importe</label>
                      <input type="number" min="0" step="0.01"
                             name="remision_partidas[{{ $idx }}][importe]"
                             class="form-control partida-importe"
                             value="{{ $partida['importe'] ?? 0 }}" readonly>
                    </div>
                    <div class="col-12">
                      <label class="form-label">Descripción completa <span style="color:#2563eb;font-size:.78rem;">(aparece en el PDF)</span></label>
                      <textarea name="remision_partidas[{{ $idx }}][descripcion]"
                                class="form-control"
                                rows="3"
                                placeholder="Detalle del servicio, acciones realizadas, número de serie...">{{ $partida['descripcion'] ?? '' }}</textarea>
                    </div>
                  </div>
                </div>
                @empty
                  {{-- sin partidas --}}
                @endforelse
              </div>

              <button type="button" class="btn btn-soft mt-2" onclick="addPartida()">
                <i class="bi bi-plus-circle"></i> Agregar partida
              </button>
            </div>

            {{-- ─── REMISIÓN / TOTALES ─── --}}
            <div class="mb-5">
              <hr style="border-color:#e2e8f0;margin:0 0 28px 0;">
              <div class="section-title">Remisión · Cargos adicionales y totales</div>
              <div class="section-sub">Envío, anticipo e IVA se suman a las partidas al guardar.</div>

              <div class="row g-4">
                <div class="col-md-3">
                  <label class="form-label">Envío</label>
                  <input type="number" min="0" step="0.01" name="remision_envio"
                         class="form-control @error('remision_envio') is-invalid @enderror"
                         value="{{ old('remision_envio', $orden->remision_envio ?? 0) }}">
                  @error('remision_envio')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-3">
                  <label class="form-label">Anticipo</label>
                  <input type="number" min="0" step="0.01" name="remision_anticipo"
                         class="form-control @error('remision_anticipo') is-invalid @enderror"
                         value="{{ old('remision_anticipo', $orden->remision_anticipo ?? 0) }}">
                  @error('remision_anticipo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-3">
                  <label class="form-label">¿Aplica IVA?</label>
                  <select name="remision_requiere_iva"
                          class="form-select @error('remision_requiere_iva') is-invalid @enderror">
                    <option value="0" @selected(!(bool)old('remision_requiere_iva',$orden->remision_requiere_iva??false))>Sin IVA</option>
                    <option value="1" @selected((bool)old('remision_requiere_iva',$orden->remision_requiere_iva??false))>Con IVA (16%)</option>
                  </select>
                  @error('remision_requiere_iva')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-3">
                  <label class="form-label">Unidad (global)</label>
                  <input type="text" name="remision_unidad"
                         class="form-control @error('remision_unidad') is-invalid @enderror"
                         value="{{ old('remision_unidad', $orden->remision_unidad ?? 'SERVICIO') }}">
                  @error('remision_unidad')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12">
                  <label class="form-label">Descripción general de remisión <span style="color:#2563eb;font-size:.78rem;">(respaldo si las partidas no tienen descripción)</span></label>
                  <textarea name="remision_descripcion"
                            class="form-control @error('remision_descripcion') is-invalid @enderror"
                            rows="4"
                            placeholder="Descripción completa del servicio realizado, acciones, número de serie...">{{ old('remision_descripcion', $orden->remision_descripcion) }}</textarea>
                  @error('remision_descripcion')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
              </div>
            </div>

            {{-- ─── CHECKLIST PREVENTIVO ─── --}}
            <div class="mb-5">
              <hr style="border-color:#e2e8f0;margin:0 0 28px 0;">
              <div class="section-title">Checklist preventivo</div>
              <div class="section-sub">Ítems del mantenimiento preventivo realizado.</div>

              <div id="mtoPreventContainer">
                @foreach($mtoPreventivo as $pi => $pItem)
                <div class="checklist-row" id="mprow_{{ $pi }}">
                  <div class="row g-2 flex-grow-1">
                    <div class="col-md-4">
                      <input type="text" name="mto_preventivo[{{ $pi }}][seccion]"
                             class="form-control"
                             placeholder="Sección"
                             value="{{ $pItem['seccion'] ?? '' }}">
                    </div>
                    <div class="col-md-5">
                      <input type="text" name="mto_preventivo[{{ $pi }}][item]"
                             class="form-control"
                             placeholder="Ítem / actividad"
                             value="{{ $pItem['item'] ?? '' }}">
                    </div>
                    <div class="col-md-3">
                      <input type="text" name="mto_preventivo[{{ $pi }}][estatus]"
                             class="form-control"
                             placeholder="Estatus (OK, N/A…)"
                             value="{{ $pItem['estatus'] ?? '' }}">
                    </div>
                  </div>
                  <button type="button" class="btn btn-soft btn-sm py-1 px-2 text-danger ms-2"
                          onclick="this.closest('.checklist-row').remove()">
                    <i class="bi bi-x-lg"></i>
                  </button>
                </div>
                @endforeach
              </div>

              <button type="button" class="btn btn-soft mt-2" onclick="addChecklistItem()">
                <i class="bi bi-plus-circle"></i> Agregar ítem checklist
              </button>
            </div>

            {{-- ─── ACCIONES REALIZADAS ─── --}}
            <div class="mb-5">
              <hr style="border-color:#e2e8f0;margin:0 0 28px 0;">
              <div class="section-title">Acciones realizadas</div>
              <div class="section-sub">Listado de trabajos efectuados durante el servicio.</div>

              <div id="mtoRealizadoContainer">
                @foreach($mtoRealizado as $ri => $rItem)
                <div class="d-flex gap-2 mb-2 realizado-row" id="mrrow_{{ $ri }}">
                  <input type="text" name="mto_realizado[{{ $ri }}]"
                         class="form-control"
                         placeholder="Ej: Cambio de filtro HEPA"
                         value="{{ $rItem }}">
                  <button type="button" class="btn btn-soft btn-sm text-danger px-2"
                          onclick="this.closest('.realizado-row').remove()">
                    <i class="bi bi-x-lg"></i>
                  </button>
                </div>
                @endforeach
              </div>

              <button type="button" class="btn btn-soft mt-2" onclick="addRealizadoItem()">
                <i class="bi bi-plus-circle"></i> Agregar acción
              </button>
            </div>

            {{-- ─── BOTONES ─── --}}
            <div class="d-flex justify-content-end gap-3 mt-5 pt-3 border-top">
              <a href="{{ route('ordenes.index') }}" class="btn btn-soft">Cancelar</a>
              <button type="submit" class="btn btn-primary-clean">
                <i class="bi bi-floppy2"></i> Guardar cambios
              </button>
            </div>

          </form>
        </div>
      </div>
    </div>

    {{-- ─── SIDEBAR ─── --}}
    <div class="col-lg-4">
      <div class="sidebar-wrapper">
        <div class="card-clean p-4">
          <h5 class="mb-4" style="font-weight:600;font-size:1rem;">Resumen de la Orden</h5>

          <div class="kpi-item">
            <div class="kpi-label">No. Orden</div>
            <div class="kpi-value">#{{ str_pad($orden->id, 5, '0', STR_PAD_LEFT) }}</div>
          </div>

          <div class="kpi-item">
            <div class="kpi-label">Cliente</div>
            <div class="kpi-value" id="sideClienteName">
              {{ $currentClienteName ?: trim((optional($orden->cliente)->nombre??'').' '.(optional($orden->cliente)->apellido??'')) ?: 'Sin asignar' }}
            </div>
          </div>

          <div class="kpi-item">
            <div class="kpi-label">Tipo de mantenimiento</div>
            <div class="kpi-value" id="sideTipoMtto">{{ ucfirst($tipoMantenimientoActual) }}</div>
          </div>

          <div class="kpi-item">
            <div class="kpi-label">Equipo</div>
            <div class="kpi-value">
              {{ $orden->equipo ?? 'No especificado' }}<br>
              <span style="font-weight:400;font-size:.85rem;color:#64748b;">{{ $orden->marca }} {{ $orden->modelo }}</span>
            </div>
          </div>

          <div class="kpi-item">
            <div class="kpi-label">No. Serie</div>
            <div class="kpi-value" style="font-family:monospace;font-size:.9rem;">{{ $orden->numero_serie ?: 'N/A' }}</div>
          </div>

          <div class="kpi-item">
            <div class="kpi-label">Subtotal remisión</div>
            <div class="kpi-value">${{ number_format($orden->remision_subtotal ?? 0, 2) }}</div>
          </div>

          <div class="kpi-item">
            <div class="kpi-label">Total a pagar</div>
            <div class="kpi-value" style="color:#2563eb;">${{ number_format($orden->remision_total_pagar ?? 0, 2) }}</div>
          </div>

          <div class="kpi-item mb-0">
            <div class="kpi-label">Código validación</div>
            <div class="kpi-value" style="font-family:monospace;font-size:.85rem;letter-spacing:.05em;">
              {{ $orden->codigo_validacion_servicio ?: 'N/A' }}
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {

  const swalConfig = {
    customClass:{ confirmButton:'btn btn-primary-clean', popup:'card-clean' },
    buttonsStyling:false
  };

  @if(session('error'))
    Swal.fire({...swalConfig,icon:'error',title:'No se pudo completar',text:@json(session('error')),confirmButtonText:'Entendido'});
  @endif
  @if(session('ok'))
    Swal.fire({...swalConfig,icon:'success',title:@json(session('ok')),confirmButtonText:'Aceptar'});
  @endif

  // ── Cliente picker ──
  function norm(s){
    return (s||'').toString().toLowerCase()
      .normalize('NFD').replace(/[\u0300-\u036f]/g,'').trim();
  }

  const input     = document.getElementById('clienteSearch');
  const results   = document.getElementById('clienteResults');
  const list      = document.getElementById('clienteList');
  const empty     = document.getElementById('clienteEmpty');
  const hidden    = document.getElementById('cliente_id');
  const sideName  = document.getElementById('sideClienteName');
  const tipoMtto  = document.getElementById('tipo_mantenimiento');
  const sideTipo  = document.getElementById('sideTipoMtto');

  function filterClients(){
    const q = norm(input.value);
    let any = false;
    list.querySelectorAll('.client-row').forEach(row => {
      const blob = norm(row.dataset.search || row.textContent);
      const show = (!q || blob.includes(q));
      row.style.display = show ? '' : 'none';
      if(show) any = true;
    });
    empty.classList.toggle('d-none', any);
  }

  input.addEventListener('focus', () => { results.classList.add('show'); filterClients(); });
  input.addEventListener('input', () => { results.classList.add('show'); filterClients(); });
  document.addEventListener('click', e => { if(!e.target.closest('.client-picker')) results.classList.remove('show'); });

  list.querySelectorAll('.client-row').forEach(row => {
    row.addEventListener('click', () => {
      hidden.value = row.dataset.id;
      input.value  = row.dataset.name;
      if(sideName) sideName.textContent = row.dataset.name;
      results.classList.remove('show');
    });
    row.addEventListener('keydown', e => { if(e.key==='Enter'){ e.preventDefault(); row.click(); } });
  });

  tipoMtto?.addEventListener('change', () => {
    if(sideTipo) sideTipo.textContent = tipoMtto.options[tipoMtto.selectedIndex]?.text || '—';
  });

  // ── Previews de fotos ──
  document.querySelectorAll('input[type=file][data-preview]').forEach(inp => {
    inp.addEventListener('change', () => {
      const f = inp.files && inp.files[0];
      if(!f || !f.type.startsWith('image/')) return;
      const img   = document.getElementById(inp.dataset.preview);
      const thumb = document.getElementById(inp.dataset.thumb);
      const url   = URL.createObjectURL(f);
      img.src = url; img.style.display = '';
      const icon = thumb?.querySelector('i.bi');
      if(icon) icon.style.display = 'none';
      img.onload = () => URL.revokeObjectURL(url);
    });
  });

  // ── Cálculo automático de importes de partidas ──
  document.getElementById('partidasContainer').addEventListener('input', e => {
    const row = e.target.closest('.partida-row');
    if(!row) return;
    const cant   = parseFloat(row.querySelector('.partida-cantidad')?.value) || 0;
    const precio = parseFloat(row.querySelector('.partida-precio')?.value)   || 0;
    const imp    = row.querySelector('.partida-importe');
    if(imp) imp.value = (cant * precio).toFixed(2);
  });

  // ── Validación submit ──
  document.getElementById('ordenEditForm')?.addEventListener('submit', e => {
    const cid = document.getElementById('cliente_id')?.value?.trim();
    if(!cid){
      e.preventDefault();
      Swal.fire({...swalConfig,icon:'warning',title:'Falta cliente',
        text:'Debes seleccionar un cliente válido antes de guardar.',confirmButtonText:'Entendido'});
      document.getElementById('clienteSearch')?.focus();
    }
  });
});

// ── Partidas ──
let partidaIdx = {{ count($partidas) }};

function addPartida(){
  const idx = partidaIdx++;
  const container = document.getElementById('partidasContainer');
  const div = document.createElement('div');
  div.className = 'partida-row';
  div.id = 'partida_'+idx;
  div.innerHTML = `
    <div class="d-flex justify-content-between align-items-center mb-2">
      <span class="badge-tip">Partida #${idx+1}</span>
      <button type="button" class="btn btn-soft btn-sm py-1 px-2 text-danger" onclick="removePartida(${idx})">
        <i class="bi bi-trash3"></i>
      </button>
    </div>
    <div class="row g-3">
      <div class="col-md-4">
        <label class="form-label">Nombre / ítem</label>
        <input type="text" name="remision_partidas[${idx}][item]" class="form-control" placeholder="Ej: Mantenimiento preventivo">
      </div>
      <div class="col-md-2">
        <label class="form-label">Unidad</label>
        <input type="text" name="remision_partidas[${idx}][unidad]" class="form-control" value="SERVICIO">
      </div>
      <div class="col-md-2">
        <label class="form-label">Cantidad</label>
        <input type="number" min="1" step="1" name="remision_partidas[${idx}][cantidad]" class="form-control partida-cantidad" value="1">
      </div>
      <div class="col-md-2">
        <label class="form-label">P. Unitario</label>
        <input type="number" min="0" step="0.01" name="remision_partidas[${idx}][precio_unitario]" class="form-control partida-precio" value="0">
      </div>
      <div class="col-md-2">
        <label class="form-label">Importe</label>
        <input type="number" min="0" step="0.01" name="remision_partidas[${idx}][importe]" class="form-control partida-importe" value="0" readonly>
      </div>
      <div class="col-12">
        <label class="form-label">Descripción completa <span style="color:#2563eb;font-size:.78rem;">(aparece en el PDF)</span></label>
        <textarea name="remision_partidas[${idx}][descripcion]" class="form-control" rows="3"
                  placeholder="Detalle del servicio, acciones realizadas, número de serie..."></textarea>
      </div>
    </div>`;
  container.appendChild(div);
}

function removePartida(idx){
  document.getElementById('partida_'+idx)?.remove();
}

// ── Checklist preventivo ──
let mpIdx = {{ count($mtoPreventivo) }};

function addChecklistItem(){
  const idx = mpIdx++;
  const container = document.getElementById('mtoPreventContainer');
  const div = document.createElement('div');
  div.className = 'checklist-row';
  div.id = 'mprow_'+idx;
  div.innerHTML = `
    <div class="row g-2 flex-grow-1">
      <div class="col-md-4">
        <input type="text" name="mto_preventivo[${idx}][seccion]" class="form-control" placeholder="Sección">
      </div>
      <div class="col-md-5">
        <input type="text" name="mto_preventivo[${idx}][item]" class="form-control" placeholder="Ítem / actividad">
      </div>
      <div class="col-md-3">
        <input type="text" name="mto_preventivo[${idx}][estatus]" class="form-control" placeholder="Estatus (OK, N/A…)">
      </div>
    </div>
    <button type="button" class="btn btn-soft btn-sm py-1 px-2 text-danger ms-2"
            onclick="this.closest('.checklist-row').remove()">
      <i class="bi bi-x-lg"></i>
    </button>`;
  container.appendChild(div);
}

// ── Acciones realizadas ──
let mrIdx = {{ count($mtoRealizado) }};

function addRealizadoItem(){
  const idx = mrIdx++;
  const container = document.getElementById('mtoRealizadoContainer');
  const div = document.createElement('div');
  div.className = 'd-flex gap-2 mb-2 realizado-row';
  div.id = 'mrrow_'+idx;
  div.innerHTML = `
    <input type="text" name="mto_realizado[${idx}]" class="form-control" placeholder="Ej: Cambio de filtro HEPA">
    <button type="button" class="btn btn-soft btn-sm text-danger px-2"
            onclick="this.closest('.realizado-row').remove()">
      <i class="bi bi-x-lg"></i>
    </button>`;
  container.appendChild(div);
}
</script>

@endsection