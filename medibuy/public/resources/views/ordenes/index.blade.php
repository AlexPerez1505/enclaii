{{-- resources/views/ordenes/index.blade.php --}}
@extends('layouts.app')
@section('title','Órdenes de Servicio')
@section('titulo','Órdenes')

@section('content')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
:root{
  --bg:#eaebec; --panel:#ffffff; --text:#0f172a; --muted:#667085; --border:#e7eaf0;
  --pblue:#dbeafe; --pblue-700:#1d4ed8; --pgreen:#dcfce7; --pgreen-700:#046c4e;
  --shadow:0 10px 30px rgba(2,6,23,.06); --radius:22px;

  /* Paleta SweetAlert (pastel) */
  --sw-confirm:#34c29e;    /* verde pastel */
  --sw-cancel:#e5e7eb;     /* gris claro */
  --sw-deny:#fda4af;       /* rojo suave */
  --sw-text:#0f172a;
  --sw-popup-bg:#ffffff;
}
*,*::before,*::after{ box-sizing:border-box; }
body{ background:var(--bg); color:var(--text); font-family:Inter, system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, sans-serif; }
.page-wrap{ max-width:1160px; margin:0 auto; padding:0 16px; overflow-x:hidden; }

/* SweetAlert2 custom */
.sw-rounded .swal2-popup{
  border-radius:18px !important;
  background:var(--sw-popup-bg) !important;
}
.sw-btn{
  font-weight:800 !important;
  border-radius:12px !important;
  padding:10px 14px !important;
  border:1px solid rgba(2,6,23,.08) !important;
  box-shadow:0 8px 24px rgba(2,6,23,.08) !important;
}
.sw-confirm{ background:var(--sw-confirm) !important; color:#ffffff !important; }
.sw-cancel{ background:var(--sw-cancel) !important; color:#0f172a !important; }
.sw-deny{ background:var(--sw-deny) !important; color:#111827 !important; }

/* HERO */
.hero{
  background:
    radial-gradient(1200px 150px at 0% 0%, rgba(96,165,250,.18), transparent 40%),
    radial-gradient(1200px 150px at 100% 0%, rgba(14,165,233,.14), transparent 40%),
    #fff;
  border:1px solid var(--border); border-radius:18px; padding:16px 18px;
  box-shadow:var(--shadow);
  display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap;
  margin:18px 0; overflow:hidden;
}
.hero .chip{ width:56px; height:56px; border-radius:16px; display:inline-flex; align-items:center; justify-content:center; background:#fff; border:1px solid #dce7ff; }
.hero h1{ margin:0; font-weight:800; letter-spacing:-.02em; }
.subtle{ color:var(--muted); }
.hero-actions{ display:flex; align-items:center; gap:10px; flex-wrap:wrap; width:100%; }
.search{ position:relative; flex:1 1 420px; min-width:0; background:#fff; border:1px solid var(--border); border-radius:14px; padding-left:42px; }
.search input{ border:none; outline:none; background:transparent; padding:12px 14px; width:100%; color:#111827; }
.search .ico{ position:absolute; left:12px; top:50%; transform:translateY(-50%); font-size:18px; color:#1d4ed8; opacity:.9; }

.btn{ display:inline-flex; align-items:center; gap:.45rem; padding:12px 14px; border-radius:14px; border:1px solid var(--border); background:#fff; color:#334155; font-weight:800; text-decoration:none; cursor:pointer; transition:transform .04s ease, box-shadow .2s ease, background .2s ease; line-height:1; }
.btn:active{ transform:translateY(1px) }
.btn-utility{ box-shadow:0 4px 10px rgba(2,6,23,.04); }
.btn-blue{ background:var(--pblue); color:#0b2a4a; border-color:rgba(96,165,250,.45); }
.btn-green{ background:var(--pgreen); color:#064e3b; border-color:rgba(52,211,153,.45); }

/* Icon-only uniform buttons */
.btn-icon{
  width:44px; height:44px; padding:0 !important; border-radius:14px;
  display:inline-grid; place-items:center; white-space:nowrap;
}
.btn-icon i{ font-size:18px; }

/* TABLA */
.table-wrap{ background:#fff; border:1px solid var(--border); border-radius:var(--radius); box-shadow:var(--shadow); overflow:hidden; }
.table-scroll{ overflow:auto; max-width:100%; }
.inv-table{ width:100%; border-collapse:separate; border-spacing:0; table-layout:fixed; }
.inv-table .th,.inv-table .td{ padding:16px 18px; text-align:left; font-size:14px; }
.inv-table .th{ color:#6b7280; font-weight:700; background:#eef3ff; border-bottom:1px solid var(--border); white-space:nowrap; }
.inv-table tr.trow{ display:table-row; border-bottom:1px solid var(--border); background:#fff; }
.inv-table tr.trow:hover{ background:#fcfcff; }
.inv-table td.td{ display:table-cell; vertical-align:middle; }
.cell-actions{ display:flex; gap:10px; justify-content:flex-end; flex-wrap:nowrap; }
.cell-actions form{ display:contents; }
.no-results{ color:#667085; }

.tile-mini{ width:52px; height:52px; border:1px solid var(--border); border-radius:10px; overflow:hidden; background:#f8fafc; display:grid; place-items:center }
.tile-mini img{ width:100%; height:100%; object-fit:cover }

/* Badges */
.badge-next{ display:inline-flex; align-items:center; gap:.35rem; padding:6px 10px; border-radius:999px; font-weight:800; font-size:12px; background:#dcfce7; color:#065f46; border:1px solid #bbf7d0; }

/* Col widths (base) */
.inv-table col:nth-child(1){ width:30%; }  /* Equipo */
inv-table col:nth-child(2){ width:16%; }  /* Serie  */
.inv-table col:nth-child(3){ width:20%; }  /* Cliente*/
.inv-table col:nth-child(4){ width:20%; }  /* Fechas */
.inv-table col:nth-child(5){ width:12%; }  /* Próx.  */
.inv-table col:nth-child(6){ width:10%; }  /* Acciones */

/* Acciones compactas */
.td[data-label="Acciones"]{ min-width: 180px; white-space: nowrap; }

/* ====== OCULTAR “Próx. mantto” SOLO EN DESKTOP (≥577px) ====== */
@media (min-width: 577px){
  .inv-table thead th:nth-child(5),
  .inv-table tbody td:nth-child(5),
  .inv-table colgroup col:nth-child(5){
    display:none !important;
    width:0 !important;
  }
  .inv-table col:nth-child(1){ width:34% !important; }
  .inv-table col:nth-child(2){ width:16% !important; }
  .inv-table col:nth-child(3){ width:20% !important; }
  .inv-table col:nth-child(4){ width:20% !important; }
  .inv-table col:nth-child(6){ width:10% !important; }
}

/* ====== RESPONSIVE (MÓVIL) ====== */
@media (max-width:576px){
  .inv-table.is-stacked thead{ display:none; }
  .inv-table.is-stacked, .inv-table.is-stacked tbody, .inv-table.is-stacked tr.trow, .inv-table.is-stacked td.td{ display:block; width:100%; }
  .inv-table.is-stacked tr.trow{ padding:12px 14px; }
  .inv-table.is-stacked tr.trow + tr.trow{ border-top:1px solid var(--border); }
  .inv-table.is-stacked td.td{
    border:none; padding:10px 0;
    display:grid; grid-template-columns:minmax(96px,40%) 1fr; gap:8px; align-items:flex-start;
    word-wrap:break-word;
  }
  .inv-table.is-stacked td.td::before{ content:attr(data-label); font-weight:700; color:#6b7280; }
  .inv-table.is-stacked td.td[data-label="Acciones"]{ grid-template-columns:1fr; }
  .inv-table.is-stacked .cell-actions{ justify-content:flex-start; }

  /* Ocultar botón del hero en móvil y usar FAB */
  .show-desktop-only{ display:none !important; }
}

/* ====== FAB (solo móvil, circular icon-only) ====== */
.fab{
  position:fixed; right:18px; bottom:18px; z-index:1000;
  display:none; place-items:center;
  width:56px; height:56px; border-radius:50%;
  border:1px solid rgba(96,165,250,.45);
  background:var(--pblue); color:#0b2a4a;
  box-shadow:0 14px 40px rgba(2,6,23,.18);
}
.fab i{ font-size:22px; line-height:1; }
@media (max-width:576px){
  .fab{ display:grid; }
}
</style>

<div class="page-wrap" x-data="OrdenesUI()">
  {{-- HERO --}}
  <div class="hero">
    <div class="d-flex align-items-center gap-3">
      <div class="chip"><i class="bi bi-clipboard-data" style="font-size:1.25rem;color:#1d4ed8"></i></div>
      <div>
        <h1 class="h4 mb-0">Órdenes de Servicio</h1>
        <div class="small subtle">Consulta y gestiona las OS.</div>
      </div>
    </div>

    <div class="hero-actions">
      <div class="search">
        <i class="ico bi bi-search"></i>
        <input type="search" placeholder="Buscar: cliente, equipo, serie…" x-model="$store.os.q">
      </div>

      @if (Route::has('ordenes.export'))
        <a class="btn btn-utility" href="{{ route('ordenes.export', ['q'=>request('q')]) }}"><i class="bi bi-download"></i> Descargar</a>
      @endif

      {{-- NUEVA ORDEN: solo desktop aquí; en móvil se oculta y aparece FAB --}}
      @if (Route::has('ordenes.create'))
        <a class="btn btn-green btn-utility show-desktop-only" href="{{ route('ordenes.create') }}">
          <i class="bi bi-plus-circle"></i> Nueva orden
        </a>
      @endif
    </div>
  </div>

  {{-- TABLA --}}
  <div class="table-wrap">
    <div class="table-scroll">
      <table class="inv-table" :class="{'is-stacked': isMobile}">
        <colgroup><col><col><col><col><col><col></colgroup>
        <thead>
          <tr>
            <th class="th">Equipo</th>
            <th class="th">Serie</th>
            <th class="th">Cliente</th>
            <th class="th">Fechas</th>
            <th class="th">Próx. mantto</th>
            <th class="th text-end">Acciones</th>
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
            $fMto     = $mtoDate ? $mtoDate->format('d/m/Y') : '—';

            $proxRaw   = $o->getRawOriginal('proximo_mantenimiento');
            $proxFecha = null; $proxMeses = null; $proxTexto = '—';
            if($proxRaw !== null && $proxRaw !== ''){
              if (is_numeric($proxRaw)) {
                $proxMeses = (int)$proxRaw;
                if ($mtoDate) $proxFecha = (clone $mtoDate)->addMonths($proxMeses);
              } else {
                try { $proxFecha = \Illuminate\Support\Carbon::parse($proxRaw); } catch (\Throwable $e) { $proxFecha = null; }
                if ($proxFecha && $mtoDate) $proxMeses = $mtoDate->diffInMonths($proxFecha);
              }
              if ($proxFecha instanceof \Illuminate\Support\Carbon) {
                $proxTexto = $proxFecha->format('d/m/Y') . ($proxMeses ? " ({$proxMeses} meses)" : '');
              } elseif (is_numeric($proxRaw) && $mtoDate) {
                $proxTexto = "{$proxMeses} meses";
              }
            }
            $rowBlob = strtolower(trim(implode(' ', [
              $o->equipo, $o->marca, $o->modelo, $o->numero_serie,
              optional($o->cliente)->nombre, optional($o->cliente)->telefono
            ])));
          @endphp
          <tr class="trow"
              x-show="filtra({ blob: @js($rowBlob) }, $store.os.q)"
              :style="filtra({ blob: @js($rowBlob) }, $store.os.q) ? '' : 'display:none !important'">
            {{-- Equipo --}}
            <td class="td" data-label="Equipo">
              <div class="d-flex align-items-center gap-3">
                <div class="tile-mini">
                  @if($img)
                    <img src="{{ $img }}" alt="equipo #{{ $o->id }}"
                         onerror="this.style.display='none'; this.closest('.tile-mini').innerHTML='<i class=&quot;bi bi-image text-muted&quot;></i>'; ">
                  @else
                    <i class="bi bi-image text-muted"></i>
                  @endif
                </div>
                <div>
                  <div class="fw-bold text-uppercase" style="letter-spacing:.02em">{{ $o->equipo }}</div>
                  <div class="text-muted small">{{ $o->marca }} {{ $o->modelo }}</div>
                </div>
              </div>
            </td>
            {{-- Serie --}}
            <td class="td" data-label="Serie"><span class="fw-semibold">{{ $o->numero_serie ?: '—' }}</span></td>
            {{-- Cliente --}}
            <td class="td" data-label="Cliente">
              <div class="fw-semibold">{{ optional($o->cliente)->nombre ?: '—' }}</div>
              <div class="text-muted small">{{ optional($o->cliente)->telefono }}</div>
            </td>
            {{-- Fechas --}}
            <td class="td" data-label="Fechas">
              <div class="text-muted small">Entrada</div>
              <div class="fw-semibold">{{ $fEntrada }}</div>
              <div class="text-muted small mt-1">Mantenimiento</div>
              <div class="fw-semibold">{{ $fMto }}</div>
            </td>
            {{-- Próximo (visible en móvil; oculto en desktop por media query) --}}
            <td class="td" data-label="Próx. mantto">
              @if($proxTexto !== '—')
                <span class="badge-next"><i class="bi bi-calendar2-event"></i> {{ $proxTexto }}</span>
              @else
                —
              @endif
            </td>
            {{-- Acciones --}}
            <td class="td" data-label="Acciones">
              <div class="cell-actions">
                @if (Route::has('ordenes.pdf'))
                  {{-- PDF via fetch + loader --}}
                  <a  href="{{ route('ordenes.pdf', $o) }}"
                      class="btn btn-blue btn-utility btn-icon js-pdf"
                      data-href="{{ route('ordenes.pdf', $o) }}"
                      data-id="{{ $o->id }}"
                      title="Descargar PDF">
                    <i class="bi bi-filetype-pdf"></i>
                  </a>
                @endif

                @if (Route::has('ordenes.destroy'))
                  <form method="POST" action="{{ route('ordenes.destroy', $o) }}" class="js-delete" data-id="{{ $o->id }}">
                    @csrf @method('DELETE')
                    <button class="btn btn-utility btn-icon" type="submit" title="Eliminar">
                      <i class="bi bi-trash3"></i>
                    </button>
                  </form>
                @endif
              </div>
            </td>
          </tr>
        @empty
          <tr class="trow"><td class="td no-results" colspan="6">No hay órdenes registradas.</td></tr>
        @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

{{-- FAB: solo móvil, circular con icono "+" --}}
@if (Route::has('ordenes.create'))
  <a href="{{ route('ordenes.create') }}" class="fab" aria-label="Nueva orden">
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

/* -------------------------------
   SWEETALERT: GUARDADO / ÉXITO
-------------------------------- */
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

    Swal.fire({
      icon: 'success',
      title: @json(session('ok')),
      text: pdfUrl ? 'Tu PDF está listo para descargar.' : 'Acción realizada correctamente.',
      showCancelButton: !!pdfUrl,
      confirmButtonText: pdfUrl ? 'Descargar PDF' : 'Aceptar',
      cancelButtonText: 'Cerrar',
      customClass: {
        popup: 'sw-rounded',
        confirmButton: 'sw-btn sw-confirm',
        cancelButton: 'sw-btn sw-cancel'
      },
      buttonsStyling: false,
      backdrop: true,
      allowOutsideClick: true
    }).then((result) => {
      if (result.isConfirmed && pdfUrl) {
        descargaPdfConLoader(pdfUrl, 'OS_' + (ordenId || 'archivo') + '.pdf');
      }
    });
  @endif
});

/* -------------------------------
   SWEETALERT: ELIMINAR
   - Confirmación bonita
   - Loader mientras se envía
-------------------------------- */
document.addEventListener('click', function(e){
  const form = e.target.closest('form.js-delete');
  if (!form) return;

  e.preventDefault();
  const id = form.dataset.id || '';

  Swal.fire({
    icon: 'warning',
    title: 'Eliminar orden #' + id,
    text: 'Esta acción no se puede deshacer.',
    showCancelButton: true,
    confirmButtonText: 'Eliminar',
    cancelButtonText: 'Cancelar',
    customClass: {
      popup: 'sw-rounded',
      confirmButton: 'sw-btn sw-deny',  // rojo suave
      cancelButton: 'sw-btn sw-cancel'
    },
    buttonsStyling: false,
    reverseButtons: true
  }).then((res)=>{
    if(res.isConfirmed){
      Swal.fire({
        title: 'Eliminando…',
        didOpen: () => { Swal.showLoading(); },
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        customClass: { popup: 'sw-rounded' }
      });
      form.submit();
    }
  });
});

/* -------------------------------
   DESCARGA PDF CON LOADER
   - Intercepta clic en .js-pdf
   - fetch() para obtener el blob
   - descarga y cierra loader
-------------------------------- */
document.addEventListener('click', function(e){
  const a = e.target.closest('a.js-pdf');
  if(!a) return;

  e.preventDefault();
  const url = a.dataset.href || a.getAttribute('href') || '#';
  const id  = a.dataset.id || 'archivo';

  descargaPdfConLoader(url, 'OS_' + id + '.pdf');
});

/* Loader redondeado (no bloqueante) */
function abrirLoader(titulo = 'Descargando PDF…') {
  Swal.fire({
    title: titulo,
    html: '<div class="small text-muted">Generando y preparando el archivo</div>',
    didOpen: () => { Swal.showLoading(); },
    allowOutsideClick: false,
    allowEscapeKey: false,
    showConfirmButton: false,
    customClass: { popup: 'sw-rounded' },
    buttonsStyling: false
  });
}

async function descargaPdfConLoader(url, filename){
  try{
    // MOSTRAR loader SIN await (no bloquea la función)
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

    // Si el servidor no envía PDF (p.ej. HTML de login), redirigir
    const ct = (res.headers.get('Content-Type') || '').toLowerCase();
    if (!ct.includes('pdf')) {
      Swal.close();
      window.location.href = url;
      return;
    }

    // Nombre desde Content-Disposition (si existe)
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

    try { a.click(); } catch(_) { window.location = href; } // Safari fallback

    a.remove();
    setTimeout(()=> URL.revokeObjectURL(href), 1000);

    Swal.close();
  }catch(err){
    console.error(err);
    Swal.fire({
      icon: 'error',
      title: 'Error',
      text: err?.message || 'No se pudo descargar el PDF.',
      customClass: {
        popup: 'sw-rounded',
        confirmButton: 'sw-btn sw-confirm'
      },
      buttonsStyling: false
    });
  }
}
</script>
@endsection
