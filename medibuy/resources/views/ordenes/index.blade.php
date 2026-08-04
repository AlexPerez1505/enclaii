{{-- resources/views/ordenes/index.blade.php --}}
@extends('layouts.app')

@section('title','Órdenes de Servicio')
@section('titulo','Órdenes')

@section('content')
@if(Auth::id() != 19)
    @include('partials.submenu-cotizaciones')
@endif

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@500;600;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
:root {
  --bg: #f9fafb; 
  --card: #ffffff; 
  --ink: #333333; 
  --muted: #888888; 
  --line: #ebebeb; 
  --blue: #007aff; 
  --blue-soft: #e6f0ff; 
  --success: #15803d; 
  --success-soft: #e6ffe6; 
  --danger: #ff4a4a; 
  --danger-soft: #ffebeb;
  
  /* Curva de animación suave */
  --ease: cubic-bezier(0.2, 0.8, 0.2, 1);
}

*, *::before, *::after { box-sizing: border-box; }

body {
  background: var(--bg);
  color: var(--ink);
  font-family: 'Quicksand', system-ui, -apple-system, sans-serif;
  margin: 0;
  padding: 0;
  -webkit-font-smoothing: antialiased;
}

/* --- Keyframes Animaciones --- */
@keyframes fadeInUp {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}

@keyframes scaleIn {
  from { opacity: 0; transform: scale(0.9); }
  to { opacity: 1; transform: scale(1); }
}

/* --- Estructura de Entrada --- */
.animate-entry { animation: fadeInUp 0.8s var(--ease) both; }
.delay-1 { animation-delay: 0.1s; }
.delay-2 { animation-delay: 0.2s; }

.page-wrap {
  max-width: 1160px;
  margin: 0 auto;
  padding: 24px 16px 60px;
  overflow-x: hidden;
}

h1, h2, h3, h4, h5, h6 {
  color: #111111;
  margin: 0;
  font-weight: 700;
}

/* --- Cards Corporativas --- */
.card-ui {
  background: var(--card);
  border: 1px solid var(--line);
  border-radius: 16px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.01);
  transition: all 0.4s var(--ease);
}
.card-ui:hover {
  transform: translateY(-4px);
  box-shadow: 0 12px 30px rgba(0,0,0,0.04);
}

/* --- HERO --- */
.hero {
  padding: 24px 32px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 24px;
  flex-wrap: wrap;
  margin-bottom: 24px;
}
.hero-icon {
  width: 56px;
  height: 56px;
  border-radius: 14px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  background: var(--blue-soft);
  color: var(--blue);
  font-size: 26px;
  transition: transform 0.5s var(--ease);
}
.hero:hover .hero-icon { transform: rotate(5deg) scale(1.1); }
.hero h1 { font-size: 22px; letter-spacing: -0.02em; }

.hero-actions {
  display: flex;
  align-items: center;
  gap: 12px;
  flex-wrap: wrap;
  flex: 1;
  justify-content: flex-end;
}

/* --- Inputs & Search --- */
.search {
  position: relative;
  flex: 1 1 300px;
  max-width: 420px;
}
.search input {
  width: 100%;
  background: var(--bg);
  border: 1px solid var(--line);
  border-radius: 12px;
  padding: 12px 14px 12px 42px;
  color: var(--ink);
  font-weight: 600;
  font-family: 'Quicksand', sans-serif;
  transition: all 0.3s var(--ease);
  outline: none;
}
.search input::placeholder { color: var(--muted); font-weight: 500; }
.search input:focus {
  background: #fff;
  border-color: var(--blue);
  box-shadow: 0 0 0 4px var(--blue-soft);
  transform: scale(1.01);
}
.search .ico {
  position: absolute;
  left: 14px;
  top: 50%;
  transform: translateY(-50%);
  font-size: 18px;
  color: var(--muted);
  transition: color 0.3s ease;
}
.search input:focus + .ico { color: var(--blue); }

/* --- Botones --- */
.btn-ui {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 12px 20px;
  border-radius: 12px;
  font-family: 'Quicksand', sans-serif;
  font-weight: 700;
  font-size: 14px;
  text-decoration: none;
  cursor: pointer;
  line-height: 1;
  transition: all 0.3s var(--ease);
  border: none;
}
.btn-ui:active { transform: scale(0.96); }

.btn-primary {
  background: var(--blue);
  color: #ffffff;
}
.btn-primary:hover {
  background: #006ce4;
  box-shadow: 0 8px 20px rgba(0, 122, 255, 0.25);
  transform: translateY(-1px);
}
.btn-ghost {
  background: transparent;
  color: #555555;
}
.btn-ghost:hover {
  background: var(--bg);
  color: var(--ink);
}

/* --- Badges --- */
.badge-ui {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 6px 12px;
  border-radius: 8px;
  font-weight: 700;
  font-size: 12px;
}
.badge-info { background: var(--blue-soft); color: var(--blue); }

/* --- TABLA --- */
.table-wrap {
  padding: 0;
  overflow: hidden;
}
.table-scroll {
  overflow-x: auto;
  width: 100%;
}
.inv-table {
  width: 100%;
  min-width: 980px;
  border-collapse: collapse;
  text-align: left;
}
.inv-table th {
  padding: 20px 24px;
  color: var(--muted);
  font-weight: 700;
  font-size: 12px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  border-bottom: 1px solid var(--line);
  background: #fff;
}
.inv-table td {
  padding: 18px 24px;
  border-bottom: 1px solid var(--line);
  vertical-align: middle;
  background: #fff;
  transition: background 0.2s ease;
}
.inv-table tr:last-child td { border-bottom: none; }
.inv-table tr { opacity: 0; animation: fadeInUp 0.5s var(--ease) forwards; }

/* Animación escalonada para las primeras filas */
.inv-table tr:nth-child(1) { animation-delay: 0.2s; }
.inv-table tr:nth-child(2) { animation-delay: 0.25s; }
.inv-table tr:nth-child(3) { animation-delay: 0.3s; }
.inv-table tr:nth-child(4) { animation-delay: 0.35s; }
.inv-table tr:nth-child(5) { animation-delay: 0.4s; }

.inv-table tr:hover td { background: #fafafa; }

/* Imagen de equipo */
.tile-mini {
  width: 48px;
  height: 48px;
  border: 1px solid var(--line);
  border-radius: 12px;
  overflow: hidden;
  background: var(--bg);
  display: grid;
  place-items: center;
  flex: 0 0 auto;
}
.tile-mini img { width: 100%; height: 100%; object-fit: cover; }
.tile-mini i { font-size: 20px; color: var(--muted); }

/* Acciones en Tabla */
.cell-actions {
  display: flex;
  gap: 8px;
  justify-content: flex-end;
  align-items: center;
}
.cell-actions form { margin: 0; }
.btn-icon {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 38px;
  height: 38px;
  border-radius: 10px;
  border: 1px solid transparent;
  background: transparent;
  color: var(--muted);
  cursor: pointer;
  text-decoration: none;
  transition: all 0.2s var(--ease);
  font-size: 16px;
}
.btn-icon:hover {
  transform: translateY(-2px) scale(1.1);
  background: #fff;
  box-shadow: 0 4px 10px rgba(0,0,0,0.05);
  border-color: var(--line);
  color: var(--ink);
}
.btn-icon--view:hover { background: var(--blue-soft); color: var(--blue); border-color: transparent; }
.btn-icon--edit:hover { background: var(--blue-soft); color: var(--blue); border-color: transparent; }
.btn-icon--danger:hover { background: var(--danger-soft); color: var(--danger); border-color: transparent; }

/* --- Responsive Layout --- */
.show-desktop-only { display: inline-flex; }

@media (min-width: 577px) {
  .inv-table col:nth-child(1) { width: 28%; }
  .inv-table col:nth-child(2) { width: 14%; }
  .inv-table col:nth-child(3) { width: 18%; }
  .inv-table col:nth-child(4) { width: 18%; }
  .inv-table col:nth-child(6) { width: 210px; }
  /* Ocultar columna Próximo en Desktop según tu código original */
  .inv-table th:nth-child(5),
  .inv-table td:nth-child(5),
  .inv-table colgroup col:nth-child(5) {
    display: none !important;
    width: 0 !important;
  }
}

@media (max-width: 576px) {
  .hero { padding: 20px; justify-content: center; text-align: center; }
  .hero-actions { justify-content: center; }
  .show-desktop-only { display: none !important; }
  
  .table-scroll { overflow: visible; }
  .inv-table { min-width: 0; display: block; }
  .inv-table thead { display: none; }
  .inv-table tbody, .inv-table tr, .inv-table td { display: block; width: 100%; }
  .inv-table tr {
    margin-bottom: 16px;
    border: 1px solid var(--line);
    border-radius: 16px;
    background: #fff;
    box-shadow: 0 2px 8px rgba(0,0,0,0.02);
  }
  .inv-table td {
    padding: 12px 16px;
    display: flex;
    flex-direction: column;
    gap: 4px;
    border-bottom: 1px solid var(--bg);
  }
  .inv-table td::before {
    content: attr(data-label);
    font-size: 11px;
    text-transform: uppercase;
    font-weight: 700;
    color: var(--muted);
  }
  .inv-table td[data-label="Acciones"] { flex-direction: row; justify-content: space-between; }
  .cell-actions { justify-content: flex-start; width: 100%; flex-wrap: wrap; }
}

/* --- FAB Button (Mobile) --- */
.fab {
  position: fixed;
  right: 20px;
  bottom: 20px;
  z-index: 100;
  display: none;
  place-items: center;
  width: 56px;
  height: 56px;
  border-radius: 999px;
  background: var(--blue);
  color: #fff;
  box-shadow: 0 10px 24px rgba(0, 122, 255, 0.3);
  text-decoration: none;
  font-size: 24px;
  animation: scaleIn 0.5s var(--ease) both;
  transition: transform 0.3s ease;
}
.fab:hover { transform: scale(1.1) rotate(90deg); }
@media (max-width: 576px) { .fab { display: grid; } }

/* --- SweetAlert2 Ajustes Corporativos --- */
.sw-wow-container { font-family: 'Quicksand', sans-serif !important; }
.sw-wow-popup {
  border-radius: 20px !important;
  background: var(--card) !important;
  border: 1px solid var(--line) !important;
  box-shadow: 0 20px 60px rgba(0,0,0,0.08) !important;
  padding: 30px !important;
}
.sw-wow-popup .swal2-title {
  color: #111 !important;
  font-weight: 700 !important;
  font-size: 22px !important;
}
.sw-wow-popup .swal2-html-container { color: var(--muted) !important; font-weight: 500 !important; }
.sw-wow-confirm {
  background: var(--blue) !important;
  color: #fff !important;
  border-radius: 8px !important;
  font-weight: 700 !important;
  padding: 12px 24px !important;
}
.sw-wow-cancel {
  background: var(--bg) !important;
  color: var(--ink) !important;
  border-radius: 8px !important;
  font-weight: 700 !important;
  padding: 12px 24px !important;
}
.sw-wow-danger { background: var(--danger) !important; color: #fff !important; }
.sw-wow-input {
  border-radius: 8px !important;
  border: 1px solid var(--line) !important;
  font-family: 'Quicksand', sans-serif !important;
}
.sw-wow-input:focus {
  border-color: var(--blue) !important;
  box-shadow: 0 0 0 3px var(--blue-soft) !important;
}
@media (min-width: 768px) {
    .page-wrap {
        margin-left: calc(88px + 32px);
        max-width: calc(100% - 88px - 48px);
    }
}
</style>

<div class="page-wrap" x-data="OrdenesUI()">

  {{-- HERO --}}
  <div class="card-ui hero animate-entry">
    <div style="display:flex; align-items:center; gap:16px;">
      <div class="hero-icon">
        <i class="bi bi-clipboard-data"></i>
      </div>
      <div>
        <h1>Órdenes de Servicio</h1>
        <div style="color:var(--muted); font-size:13px; margin-top:4px; font-weight:500;">Consulta, descarga PDFs y administra tus OS.</div>
      </div>
    </div>

    <div class="hero-actions">
      <div class="search">
        <i class="ico bi bi-search"></i>
        <input type="search" placeholder="Buscar: cliente, equipo, serie…" x-model="$store.os.q">
      </div>

      @if (Route::has('ordenes.export'))
        <a class="btn-ui btn-ghost" href="{{ route('ordenes.export', ['q'=>request('q')]) }}">
          <i class="bi bi-download"></i> Exportar
        </a>
      @endif

      @if (Route::has('ordenes.create'))
        <a class="btn-ui btn-primary show-desktop-only" href="{{ route('ordenes.create') }}">
          <i class="bi bi-plus-circle"></i> Nueva OS
        </a>
      @endif
    </div>
  </div>

  {{-- TABLA --}}
  <div class="card-ui table-wrap animate-entry delay-1">
    <div class="table-scroll">
      <table class="inv-table">
        <colgroup>
          <col>
          <col>
          <col>
          <col>
          <col>
          <col>
        </colgroup>

        <thead>
          <tr>
            <th>Equipo</th>
            <th>Serie</th>
            <th>Cliente</th>
            <th>Fechas</th>
            <th>Próx. mantto</th>
            <th style="text-align:right;">Acciones</th>
          </tr>
        </thead>

        <tbody>
          @forelse($ordenes as $o)
            @php
              $img = null;
              $rel = $o->foto_equipo ? ltrim((string)$o->foto_equipo, '/') : null;

              if (!empty($o->foto_url ?? null)) {
                $img = $o->foto_url;
              } elseif (!empty($rel)) {
                try {
                  if (\Illuminate\Support\Facades\Storage::disk('public')->exists($rel)) {
                    $img = \Illuminate\Support\Facades\Storage::disk('public')->url($rel);
                  } elseif (is_file(public_path('storage/'.$rel))) {
                    $img = asset('storage/'.$rel);
                  }
                } catch (\Throwable $e) {
                  $img = null;
                }
              }

              $mtoDate = $o->fecha_mantenimiento ? \Illuminate\Support\Carbon::parse($o->fecha_mantenimiento) : null;
              $fEntrada = $o->fecha_entrada ? \Illuminate\Support\Carbon::parse($o->fecha_entrada)->format('d/m/Y') : '—';
              $fMto = $mtoDate ? $mtoDate->format('d/m/Y') : '—';

              $proxRaw = $o->getRawOriginal('proximo_mantenimiento');
              $proxFecha = null;
              $proxMeses = null;
              $proxTexto = '—';

              if($proxRaw !== null && $proxRaw !== ''){
                if (is_numeric($proxRaw)) {
                  $proxMeses = (int)$proxRaw;
                  if ($mtoDate) {
                    $proxFecha = (clone $mtoDate)->addMonths($proxMeses);
                  }
                } else {
                  try {
                    $proxFecha = \Illuminate\Support\Carbon::parse($proxRaw);
                  } catch (\Throwable $e) {
                    $proxFecha = null;
                  }

                  if ($proxFecha && $mtoDate) {
                    $proxMeses = $mtoDate->diffInMonths($proxFecha);
                  }
                }

                if ($proxFecha instanceof \Illuminate\Support\Carbon) {
                  $proxTexto = $proxFecha->format('d/m/Y') . ($proxMeses ? " ({$proxMeses} meses)" : '');
                } elseif (is_numeric($proxRaw) && $mtoDate) {
                  $proxTexto = "{$proxMeses} meses";
                }
              }

              $rowBlob = strtolower(trim(implode(' ', [
                $o->equipo,
                $o->marca,
                $o->modelo,
                $o->numero_serie,
                optional($o->cliente)->nombre,
                optional($o->cliente)->telefono
              ])));
            @endphp

            <tr x-show="filtra({ blob: @js($rowBlob) }, $store.os.q)"
                :style="filtra({ blob: @js($rowBlob) }, $store.os.q) ? '' : 'display:none !important'">

              {{-- Equipo --}}
              <td data-label="Equipo">
                <div style="display:flex; align-items:center; gap:12px;">
                  <div class="tile-mini">
                    @if($img)
                      <img src="{{ $img }}" alt="equipo #{{ $o->id }}"
                           onerror="this.style.display='none'; this.closest('.tile-mini').innerHTML='<i class=&quot;bi bi-image text-muted&quot;></i>';">
                    @else
                      <i class="bi bi-image"></i>
                    @endif
                  </div>

                  <div>
                    <div style="font-weight:700; text-transform:uppercase; font-size:12px; letter-spacing:0.5px;">{{ $o->equipo }}</div>
                    <div style="color:var(--muted); font-size:13px; margin-top:4px;">{{ $o->marca }} {{ $o->modelo }}</div>
                  </div>
                </div>
              </td>

              {{-- Serie --}}
              <td data-label="Serie">
                <span style="font-weight:600;">{{ $o->numero_serie ?: '—' }}</span>
              </td>

              {{-- Cliente --}}
              <td data-label="Cliente">
                <div style="font-weight:600;">{{ optional($o->cliente)->nombre ?: '—' }}</div>
                <div style="color:var(--muted); font-size:13px; margin-top:4px;">{{ optional($o->cliente)->telefono }}</div>
              </td>

              {{-- Fechas --}}
              <td data-label="Fechas">
                <div style="color:var(--muted); font-size:12px;">Entrada</div>
                <div style="font-weight:600;">{{ $fEntrada }}</div>
                <div style="color:var(--muted); font-size:12px; margin-top:4px;">Mantenimiento</div>
                <div style="font-weight:600;">{{ $fMto }}</div>
              </td>

              {{-- Próximo --}}
              <td data-label="Próx. mantto">
                @if($proxTexto !== '—')
                  <span class="badge-ui badge-info">
                    <i class="bi bi-calendar2-event"></i> {{ $proxTexto }}
                  </span>
                @else
                  <span style="color:var(--muted);">—</span>
                @endif
              </td>

              {{-- Acciones --}}
              <td data-label="Acciones">
                <div class="cell-actions">

                  @if (Route::has('ordenes.show'))
                    <a href="{{ route('ordenes.show', $o) }}" class="btn-icon btn-icon--view" title="Ver OS">
                      <i class="bi bi-eye"></i>
                    </a>
                  @endif

                  @if (Route::has('ordenes.edit'))
                    <a href="{{ route('ordenes.edit', $o) }}" class="btn-icon btn-icon--edit" title="Editar OS">
                      <i class="bi bi-pencil-square"></i>
                    </a>
                  @endif

                  @if (Route::has('ordenes.pdf'))
                    <a href="{{ route('ordenes.pdf', $o) }}"
                       class="btn-icon js-pdf"
                       data-href="{{ route('ordenes.pdf', $o) }}"
                       data-id="{{ $o->id }}"
                       data-fname="OS_{{ $o->id }}.pdf"
                       title="Descargar PDF OS">
                      <i class="bi bi-filetype-pdf"></i>
                    </a>
                  @endif

                  @if (Route::has('ordenes.remision.pdf'))
                    <a href="{{ route('ordenes.remision.pdf', $o) }}"
                       class="btn-icon js-pdf"
                       data-href="{{ route('ordenes.remision.pdf', $o) }}"
                       data-id="{{ $o->id }}"
                       data-fname="Remision_Mantenimiento_OS_{{ $o->id }}.pdf"
                       title="Descargar PDF Remisión">
                      <i class="bi bi-receipt"></i>
                    </a>
                  @endif

                  @if (Route::has('ordenes.destroy'))
                    <form method="POST" action="{{ route('ordenes.destroy', $o) }}" class="js-delete" data-id="{{ (int)$o->id }}">
                      @csrf
                      @method('DELETE')
                      <input type="hidden" name="orden_id" value="{{ (int)$o->id }}">
                      <input type="hidden" name="pin" value="">
                      <button class="btn-icon btn-icon--danger" type="submit" title="Eliminar OS">
                        <i class="bi bi-trash3"></i>
                      </button>
                    </form>
                  @endif

                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" style="text-align: center; padding: 60px; color: var(--muted); font-weight:600;">No hay órdenes registradas.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

</div>

@if (Route::has('ordenes.create'))
  <a href="{{ route('ordenes.create') }}" class="fab" aria-label="Nueva OS" title="Nueva OS">
    <i class="bi bi-plus-lg"></i>
  </a>
@endif

<script>
document.addEventListener('alpine:init', () => {
  Alpine.store('os', { q:'' });
  Alpine.data('OrdenesUI', OrdenesUI);
});

function OrdenesUI(){
  return {
    isMobile: window.matchMedia('(max-width: 576px)').matches,
    filtra(row, q){
      q = (q || '').toLowerCase().trim();
      if(!q) return true;
      return (row.blob || '').includes(q);
    },
  };
}

const SW = Swal.mixin({
  customClass: {
    container: 'sw-wow-container',
    popup: 'sw-wow-popup',
    confirmButton: 'sw-wow-confirm',
    cancelButton: 'sw-wow-cancel',
    input: 'sw-wow-input',
    validationMessage: 'sw-wow-validation',
  },
  buttonsStyling: false,
  reverseButtons: true,
  allowOutsideClick: true
});

/* Alertas de sesión */
document.addEventListener('DOMContentLoaded', () => {
  @if (session('error'))
    SW.fire({
      icon: 'error',
      title: 'No se pudo completar',
      text: @json(session('error')),
      confirmButtonText: 'Entendido'
    });
  @endif
});

document.addEventListener('DOMContentLoaded', () => {
  @if (session('ok'))
    let pdfUrl = @json(session('pdf_url', null));
    const ordenId = @json(session('orden_id', session('id', null)));

    @if (Route::has('ordenes.pdf'))
      if (!pdfUrl && ordenId) {
        try {
          const base = @json(route('ordenes.pdf', ['orden' => '__ID__']));
          pdfUrl = base.replace('__ID__', ordenId);
        } catch (e) {}
      }
    @endif

    SW.fire({
      icon: 'success',
      title: @json(session('ok')),
      text: pdfUrl ? 'Tu PDF está listo.' : 'Acción realizada correctamente.',
      showCancelButton: !!pdfUrl,
      confirmButtonText: pdfUrl ? 'Descargar PDF' : 'Aceptar',
      cancelButtonText: 'Cerrar'
    }).then((result) => {
      if (result.isConfirmed && pdfUrl) {
        descargaPdfConLoader(pdfUrl, 'OS_' + (ordenId || 'archivo') + '.pdf');
      }
    });
  @endif
});

/* Eliminar con PIN */
document.addEventListener('click', async function(e){
  const form = e.target.closest('form.js-delete');
  if (!form) return;

  e.preventDefault();

  const hiddenId = form.querySelector('input[name="orden_id"]')?.value;
  const id = (hiddenId && String(hiddenId).trim()) ? String(hiddenId).trim() : (form.dataset.id || '');

  const step1 = await SW.fire({
    icon: 'warning',
    title: 'Eliminar OS #' + id,
    html: `
      <div style="margin-top:6px">
        <div style="font-weight:700;color:var(--ink)">Acción irreversible</div>
        <div style="margin-top:4px;color:var(--muted)">
          Se eliminará la orden y sus pagos asociados.
        </div>
      </div>
    `,
    showCancelButton: true,
    confirmButtonText: 'Continuar',
    cancelButtonText: 'Cancelar',
    didOpen: () => {
      const confirm = Swal.getConfirmButton();
      if (confirm) {
          confirm.classList.remove('sw-wow-confirm');
          confirm.classList.add('sw-wow-danger');
      }
    }
  });

  if (!step1.isConfirmed) return;

  const step2 = await SW.fire({
    icon: 'info',
    title: 'PIN de aprobación',
    html: `<div>Confirma con tu PIN de 6 dígitos.</div>`,
    input: 'password',
    inputPlaceholder: '••••••',
    inputAttributes: {
      inputmode: 'numeric',
      autocomplete: 'one-time-code',
      maxlength: 6
    },
    showCancelButton: true,
    confirmButtonText: 'Eliminar',
    cancelButtonText: 'Cancelar',
    focusConfirm: false,
    didOpen: () => {
      const confirm = Swal.getConfirmButton();
      if (confirm) {
          confirm.classList.remove('sw-wow-confirm');
          confirm.classList.add('sw-wow-danger');
      }

      const input = Swal.getInput();
      if (input) {
        input.addEventListener('input', () => {
          input.value = input.value.replace(/\D/g,'').slice(0,6);
        });
      }
    },
    preConfirm: (val) => {
      const pin = String(val || '').trim();
      if (!/^[0-9]{6}$/.test(pin)) {
        Swal.showValidationMessage('El PIN debe tener 6 dígitos.');
        return false;
      }
      return pin;
    }
  });

  if (!step2.isConfirmed) return;

  const pinInput = form.querySelector('input[name="pin"]');
  if (pinInput) pinInput.value = step2.value;

  SW.fire({
    title: 'Eliminando…',
    html: '<div style="color:var(--muted)">Validando PIN y aplicando cambios</div>',
    didOpen: () => Swal.showLoading(),
    allowOutsideClick: false,
    allowEscapeKey: false,
    showConfirmButton: false
  });

  form.submit();
});

/* Descargar PDF con loader */
document.addEventListener('click', function(e){
  const a = e.target.closest('a.js-pdf');
  if(!a) return;

  e.preventDefault();

  const url = a.dataset.href || a.getAttribute('href') || '#';
  const id = a.dataset.id || 'archivo';
  const fname = a.dataset.fname || ('OS_' + id + '.pdf');

  descargaPdfConLoader(url, fname);
});

function abrirLoader(titulo = 'Descargando PDF…') {
  SW.fire({
    title: titulo,
    html: '<div style="color:var(--muted)">Generando y preparando el archivo</div>',
    didOpen: () => Swal.showLoading(),
    allowOutsideClick: false,
    allowEscapeKey: false,
    showConfirmButton: false
  });
}

async function descargaPdfConLoader(url, filename){
  try{
    abrirLoader();

    const res = await fetch(url, {
      method: 'GET',
      credentials: 'same-origin',
      headers: { 'Accept': 'application/pdf' },
      redirect: 'follow'
    });

    if(!res.ok){
      throw new Error('No se pudo generar el PDF (' + res.status + ')');
    }

    const ct = (res.headers.get('Content-Type') || '').toLowerCase();

    if (!ct.includes('pdf')) {
      Swal.close();
      window.location.href = url;
      return;
    }

    let suggested = filename;
    const cd = res.headers.get('Content-Disposition') || res.headers.get('content-disposition');

    if (cd) {
      const m = /filename\*=UTF-8''([^;]+)|filename="?([^"]+)"?/i.exec(cd);
      const raw = decodeURIComponent(m?.[1] || m?.[2] || '').trim();
      if (raw) suggested = raw;
    }

    const blob = await res.blob();
    const href = URL.createObjectURL(blob);
    const a = document.createElement('a');

    a.href = href;
    a.download = suggested;
    document.body.appendChild(a);

    try {
      a.click();
    } catch(_) {
      window.location = href;
    }

    a.remove();
    setTimeout(() => URL.revokeObjectURL(href), 1200);

    Swal.close();
  }catch(err){
    console.error(err);

    SW.fire({
      icon: 'error',
      title: 'Error',
      text: err?.message || 'No se pudo descargar el PDF.',
      confirmButtonText: 'Entendido'
    });
  }
}
</script>
@endsection