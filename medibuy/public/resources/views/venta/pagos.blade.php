@extends('layouts.app')
@section('title', 'Remisión')
@section('titulo', 'Remisión No' . $venta->id)

@section('content')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/pagos.css') }}?v={{ time() }}">

@php
  // =========================
  // Helpers (BLADE)
  // =========================
  if (!function_exists('ordinalES')) {
    function ordinalES($n){
      $ord = [
        'Primer','Segundo','Tercer','Cuarto','Quinto','Sexto','Séptimo','Octavo','Noveno','Décimo',
        'Onceavo','Doceavo','Treceavo','Catorceavo','Quinceavo','Dieciseisavo','Diecisieteavo','Dieciochoavo','Diecinueveavo','Veinteavo'
      ];
      return ($n >= 1 && $n <= count($ord)) ? $ord[$n-1] : "Pago {$n}";
    }
  }

  // ✅ SOLO consideramos auto lo genérico ("Pago", "Pago 4") o exactamente igual al esperado.
  if (!function_exists('esDescAutoContraEsperado')) {
    function esDescAutoContraEsperado($desc, $esperado){
      $desc = trim((string)$desc);
      $esperado = trim((string)$esperado);

      if($desc === '') return true;
      if(preg_match('/^pago$/iu', $desc)) return true;
      if(preg_match('/^pago\s*\d+$/iu', $desc)) return true;

      // ✅ SOLO si es EXACTAMENTE el esperado (no cualquier "Séptimo pago")
      return mb_strtolower($desc) === mb_strtolower($esperado);
    }
  }

  // ✅ IDs de cuotas ya pagadas aunque el flag "pagado" en pagos_financiamiento no esté actualizado
  $finIdsPagados = collect($pagos)
      ->where('aprobado', true)
      ->pluck('financiamiento_id')
      ->filter()
      ->unique()
      ->values();

  // ✅ Conteo de cuotas pagadas
  $paidInstallments = $finIdsPagados->count();

  // ✅ Próximos pagos: NO mostrar cuotas que ya tienen pago aprobado ligado
  $pagosPendientes = $venta->pagosFinanciamiento
      ->where('pagado', false)
      ->reject(fn($pf) => $finIdsPagados->contains($pf->id))
      ->sortBy('fecha_pago')
      ->values();

  // ✅ Editables: lo mismo (solo pendientes reales)
  $pagosEditable = $pagosPendientes;
@endphp

<style>
/* ==================== TOKENS (SIN TOCAR TIPOGRAFÍA GLOBAL) ==================== */
:root{
  --ink:#0b2a47;
  --muted:#6b7b83;
  --border:#e7eef6;
  --shadow: 0 18px 44px rgba(9,30,66,.14);
  --shadow-sm: 0 10px 24px rgba(9,30,66,.10);

  --indigo-bg:#eef2ff; --indigo:#4f46e5;
  --teal-bg:#e6fffb;   --teal:#0f766e;
  --rose-bg:#fff1f2;   --rose:#e11d48;
  --amber-bg:#fffbeb;  --amber:#b45309;
  --slate-bg:#f1f5f9;  --slate:#334155;

  --white:#ffffff;
}

/* ==================== BOTONES PASTEL ==================== */
.btn-soft{
  border:0 !important;
  border-radius:12px !important;
  padding:.52rem .9rem !important;
  background: var(--slate-bg);
  color: var(--slate) !important;
  box-shadow: 0 1px 0 rgba(15,23,42,.05);
  transition: transform .12s ease, box-shadow .18s ease, background .18s ease, color .18s ease;
}
.btn-soft:hover{
  background: var(--white) !important;
  color:#111827 !important;
  box-shadow: var(--shadow-sm);
  transform: translateY(-1px);
}
.btn-soft:active{ transform: translateY(0) scale(.99); }
.btn-soft:focus{ outline:none !important; box-shadow: 0 0 0 .2rem rgba(99,102,241,.16); }
.btn-soft-sm{ padding:.40rem .72rem !important; border-radius:11px !important; }

.btn-indigo{ background:var(--indigo-bg) !important; color:var(--indigo) !important; }
.btn-teal{   background:var(--teal-bg)   !important; color:var(--teal)   !important; }
.btn-rose{   background:var(--rose-bg)   !important; color:var(--rose)   !important; }
.btn-amber{  background:var(--amber-bg)  !important; color:var(--amber)  !important; }
.btn-slate{  background:var(--slate-bg)  !important; color:var(--slate)  !important; }

/* ==================== TOAST ==================== */
.toast-clean{
  border:1px solid var(--border);
  border-radius:16px;
  box-shadow: var(--shadow);
  overflow:hidden;
  background:#fff;
}
.toast-clean .toast-header{ border:0; background:#fff; }
.toast-dot{
  width:10px;height:10px;border-radius:50%;
  box-shadow: 0 8px 16px rgba(9,30,66,.12);
}
.toast-dot.ok{ background:#10b981; }
.toast-dot.bad{ background:#ef4444; }

/* ==================== MODALES ==================== */
.modal-pro .modal-dialog{ max-width: 760px; }
.modal-pro .modal-content{
  border-radius: 22px;
  border:1px solid var(--border);
  box-shadow: var(--shadow);
  overflow:hidden;
  background:#fff;
}
.modal-pro .modal-header{
  border:0;
  padding:18px 20px;
  background: radial-gradient(120% 140% at 0% 0%, #f3f7ff 0%, #f7fbff 38%, #ffffff 100%);
}
.modal-pro .modal-title{ color:var(--ink); letter-spacing:.2px; }
.modal-pro .modal-body{ padding: 16px 20px 18px; }
.modal-pro .modal-footer{
  border:0;
  padding:14px 20px 18px;
  background: linear-gradient(180deg,#ffffff 0%, #f9fbff 100%);
}

/* Botón X */
.btn-x{
  width:36px;height:36px;
  display:grid;place-items:center;
  border:0;background:transparent;
  border-radius:10px;
  color:#64748b;
  transition: background .15s ease, color .15s ease, box-shadow .15s ease;
}
.btn-x:hover{
  background:#f1f5f9;
  color:#0f172a;
  box-shadow: 0 10px 22px rgba(9,30,66,.10);
}

/* ==================== MODAL REGISTRAR PAGO ==================== */
#pagoModal .modal-dialog{ max-width: 640px; }
#pagoModal .modal-footer{ display:flex; justify-content:flex-end; gap:10px; }

.bank-card{
  background:#fbfdff;
  border:1px solid var(--border);
  border-radius:16px;
  padding:14px;
  box-shadow: 0 10px 24px rgba(9,30,66,.06);
  margin-bottom: 14px;
  position:relative;
  overflow:hidden;
}
.bank-card::after{
  content:"";
  position:absolute; inset:0;
  background: radial-gradient(80% 70% at 120% -10%, rgba(111,181,255,.16), transparent 60%);
  pointer-events:none;
}
.bank-k{ font-size:.82rem; color:var(--muted); }
.bank-v{ color:var(--ink); }
.bank-amount{ font-variant-numeric: tabular-nums; color:var(--ink); font-size:1.65rem; }
.bank-input, .bank-select{
  border-radius:12px;
  border:1px solid #d8e3ec;
  background:#f9fbfd;
  padding:.8rem .95rem;
  transition:.2s border-color, .2s box-shadow;
}
.bank-input:focus, .bank-select:focus{
  border-color:#9dd1ff;
  box-shadow:0 0 0 .22rem rgba(100,181,255,.18);
}
.input-group-text{ background:#f1f6fb; border-color:#d8e3ec; color:#3b5b71; }

.match-wrap{ margin-top:8px }
.match-bar{
  height:10px; border-radius:99px; background:#eef3f8; position:relative; overflow:hidden;
  border:1px solid #dde8f3;
}
.match-fill{
  position:absolute; inset:0 100% 0 0;
  background: linear-gradient(90deg,#b9e5ff,#7cc7ff);
  border-radius:99px; transition: inset .35s cubic-bezier(.2,.8,.2,1);
}
.match-label{ font-size:.82rem; color:var(--muted); margin-top:6px }
.inline-warning{
  display:none;
  margin-top:10px;
  border:1px solid #ffe2a6;
  background:#fffbeb;
  color:#8a5a00;
  border-radius:14px;
  padding:10px 12px;
}

/* ==================== MODAL EDITAR PAGOS (LISTA + SCROLL + MOBILE DELETE FIX) ==================== */
#modalEditarPagos .modal-dialog{ max-width: 860px; }
#modalEditarPagos .modal-header{
  background: radial-gradient(140% 170% at 0% 0%, #f2f6ff 0%, #ffffff 58%);
}
#modalEditarPagos .modal-body{
  padding: 12px 16px 14px;
  max-height: 520px;      /* ✅ scroll interno */
  overflow: auto;
}
#modalEditarPagos .modal-footer{ padding: 10px 16px 14px; }

.pf-top{
  display:flex; align-items:center; justify-content:space-between; gap:12px;
  padding:10px 12px;
  border:1px solid var(--border);
  background:#fbfdff;
  border-radius:14px;
}
.pf-top .k{ font-size:.74rem; color:var(--muted); letter-spacing:.2px; }
.pf-top .v{ font-weight:900; color:var(--ink); font-variant-numeric: tabular-nums; font-size:1.18rem; line-height:1.05; }
.pf-chip{
  display:inline-flex; align-items:center; gap:8px;
  border:1px solid var(--border);
  background:#fff;
  border-radius:999px;
  padding:6px 10px;
  font-size:.82rem;
  color:var(--muted);
}
.pf-chip strong{ color:var(--ink); font-variant-numeric: tabular-nums; }

.pf-alert{
  display:none;
  margin-top:10px;
  border:1px solid #fee2e2;
  background:#fff1f2;
  color:#be123c;
  border-radius:14px;
  padding:10px 12px;
}
.pf-alert.show{ display:block; }

.pf-list{ margin-top:10px; display:flex; flex-direction:column; gap:10px; }
.pf-row{
  border:1px solid var(--border);
  background:#fff;
  border-radius:14px;
  padding:10px;
  box-shadow: 0 10px 22px rgba(9,30,66,.06);
}
.pf-grid{
  display:grid;
  grid-template-columns: 1.45fr .95fr 1fr 44px;
  gap:10px;
  align-items:end;
}
.pf-label{ font-size:.70rem; color:var(--muted); margin:0 0 6px 2px; }

/* inputs: evita borde negro de iOS */
#modalEditarPagos input.pf-input{
  -webkit-appearance: none !important;
  appearance: none !important;
  border-radius: 12px !important;
  border: 1px solid #d8e3ec !important;
  background: #f9fbfd !important;
  padding: .62rem .78rem !important;
  font-size: .95rem !important;
  color: #0f172a !important;
  outline: none !important;
  box-shadow: none !important;
}
#modalEditarPagos input.pf-input:focus{
  border-color:#a5b4fc !important;
  box-shadow:0 0 0 .22rem rgba(99,102,241,.14) !important;
}

.pf-money{
  display:flex !important;
  align-items:center !important;
  gap:8px !important;
  border: 1px solid #d8e3ec !important;
  background: #f9fbfd !important;
  border-radius: 12px !important;
  padding: 0 .75rem !important;
  height: 42px;
}
.pf-money span{ color:#3b5b71 !important; font-weight:700; }
.pf-money input{
  -webkit-appearance:none !important;
  appearance:none !important;
  border:0 !important;
  outline:0 !important;
  background:transparent !important;
  padding: .62rem 0 !important;
  margin:0 !important;
  width:100% !important;
  font-size: .95rem !important;
  color:#0f172a !important;
  box-shadow:none !important;
}

/* botón eliminar */
.pf-trash{ display:flex; justify-content:flex-end; }
.pf-trash .btn{
  width:40px; height:40px;
  display:grid; place-items:center;
  padding:0 !important;
  border-radius:12px !important;
}

/* acciones */
.pf-actions{ display:flex; align-items:center; justify-content:space-between; gap:10px; margin-top:10px; }
.pf-actions .text-muted{ font-size:.82rem; }

/* Tablet */
@media (max-width: 992px){
  #modalEditarPagos .modal-body{ max-height: 540px; }
  .pf-grid{ grid-template-columns: 1fr 1fr; }
  .col-monto{ grid-column: 1 / 2; }
  .col-trash{ grid-column: 2 / 3; display:flex; justify-content:flex-end; align-items:end; }
}

/* ✅ Mobile: ELIMINAR SIEMPRE VISIBLE (FIX) */
@media (max-width: 576px){
  #modalEditarPagos .modal-content{ border-radius: 0; }
  #modalEditarPagos .modal-body{ max-height: calc(100vh - 170px); }

  .pf-top{ flex-direction:column; align-items:flex-start; }

  .pf-row{
    position:relative;
    padding-right: 58px;
  }
  .pf-grid{ grid-template-columns: 1fr; }

  .pf-trash{
    position:absolute;
    right:10px;
    top:10px;
    justify-content:flex-end;
  }
  .pf-trash .btn{
    width:44px; height:44px;
    border-radius:14px !important;
  }

  .pf-actions{ flex-direction:column; align-items:stretch; }
  .pf-money{ height: 44px; }
}
</style>

{{-- ✅ TOAST ÚNICO --}}
<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999;">
  @php
    $toastType = null;
    $toastMsg  = null;
    if(session('error')) { $toastType = 'bad'; $toastMsg = session('error'); }
    elseif(session('success')) { $toastType = 'ok'; $toastMsg = session('success'); }
  @endphp

  @if($toastType && $toastMsg)
    <div class="toast toast-clean align-items-center"
         role="alert" aria-live="assertive" aria-atomic="true"
         data-bs-delay="3200" id="oneToast">
      <div class="toast-header">
        <span class="toast-dot {{ $toastType }} me-2"></span>
        <div class="me-auto text-truncate" style="max-width:320px;">
          {{ $toastMsg }}
        </div>
        <button type="button" class="btn-close ms-2 mb-1" data-bs-dismiss="toast" aria-label="Close"></button>
      </div>
    </div>
  @endif
</div>

<div class="container my-5">
  <div class="row g-4">

    {{-- ================= INFO VENTA ================= --}}
    <div class="col-lg-5">
      <div class="card shadow-sm">
        <div class="card-body">
          <h5 class="card-title mb-3">{{ $venta->cliente->nombre }} {{ $venta->cliente->apellido }}</h5>

          @php
            $totalVentaView    = (float) $totalVenta;
            $totalPagadoView   = (float) $totalPagado;
            $saldoRestanteView = (float) $saldoRestante;
            $porcentajePagado  = (float) $progreso;
            $tooltipText = "Has pagado $" . number_format($totalPagadoView, 2) . " de $" . number_format($totalVentaView, 2);
          @endphp

          <p><strong>Plan:</strong> {{ $venta->plan }}</p>
          <p><strong>Total:</strong> ${{ number_format($totalVentaView, 2) }}</p>
          <p><strong>Total pagado:</strong> ${{ number_format($totalPagadoView, 2) }}</p>
          <p><strong>Saldo restante:</strong> ${{ number_format($saldoRestanteView, 2) }}</p>

          <div class="mt-4">
            <label class="form-label">Progreso del pago: {{ $porcentajePagado }}%</label>
            <div class="progress position-relative" style="height:20px;border-radius:12px;">
              <div class="progress-bar
                @if($porcentajePagado < 50) bg-danger
                @elseif($porcentajePagado < 100) bg-warning
                @else bg-success @endif"
                role="progressbar"
                style="width: {{ $porcentajePagado }}%; transition: width .6s ease;"
                data-bs-toggle="tooltip" data-bs-placement="top"
                title="{{ $tooltipText }}">
              </div>
            </div>
          </div>

          <div class="mt-3">
            <a href="{{ route('venta.pagos-global.pdf', $venta->id) }}"
               target="_blank"
               class="btn btn-soft btn-indigo btn-soft-sm">
              <i class="bi bi-file-earmark-pdf me-1"></i>
              Descargar plan de pagos global (PDF)
            </a>
          </div>
        </div>
      </div>

      {{-- ================= PRÓXIMOS PAGOS (NO PISAR TEXTO GUARDADO) ================= --}}
      <div class="mt-4">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <h5 class="mb-0">Próximos pagos</h5>
          <button class="btn btn-soft btn-indigo btn-soft-sm" data-bs-toggle="modal" data-bs-target="#modalEditarPagos">
            Editar pagos
          </button>
        </div>

        @if($pagosPendientes->isEmpty())
          <div class="alert alert-secondary">No hay pagos pendientes.</div>
        @else
          <ul class="list-group small">
            @foreach($pagosPendientes as $p)
              @php
                $n = $paidInstallments + $loop->iteration;
                $labelAuto = ordinalES($n) . ' pago';

                // ✅ Solo reemplaza si el texto es vacío / "Pago" / "Pago 4" / o EXACTAMENTE el esperado.
                $isAuto = esDescAutoContraEsperado($p->descripcion, $labelAuto);
                $descMostrada = $isAuto ? $labelAuto : $p->descripcion;

                $fechaISO = \Carbon\Carbon::parse($p->fecha_pago)->format('Y-m-d');
              @endphp

              <li class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                  <strong>{{ \Carbon\Carbon::parse($p->fecha_pago)->format('d/m/Y') }}</strong><br>
                  <small>{{ $descMostrada }}</small>
                </div>
                <div class="text-end">
                  <span class="fw-bold d-block">${{ number_format($p->monto, 2) }}</span>

                  <button type="button"
                          class="btn btn-soft btn-teal btn-soft-sm mt-1"
                          data-bs-toggle="modal"
                          data-bs-target="#pagoModal"
                          data-fin-id="{{ $p->id }}"
                          data-fecha="{{ $fechaISO }}"
                          data-monto="{{ number_format((float)$p->monto, 2, '.', '') }}"
                          data-descripcion="{{ $descMostrada }}"
                          data-action="{{ route('ventas.pagos.store', $p->venta_id) }}">
                    Registrar
                  </button>
                </div>
              </li>
            @endforeach
          </ul>
        @endif
      </div>
    </div>

    {{-- ================= PAGOS REALIZADOS ================= --}}
    <div class="col-lg-7">
      <div class="card shadow-sm">
        <div class="card-body">
          <h5 class="card-title mb-3">Pagos realizados</h5>

          <div class="table-responsive">
            <table class="table table-hover align-middle">
              <thead class="table-light">
                <tr>
                  <th>Fecha</th>
                  <th>Monto</th>
                  <th>Método</th>
                  <th>Recibo</th>
                  <th>Estado</th>
                  <th>Acción</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($pagos as $pago)
                  <tr>
                    <td>{{ \Carbon\Carbon::parse($pago->fecha_pago)->format('d/m/Y') }}</td>
                    <td>${{ number_format($pago->monto, 2) }}</td>
                    <td>{{ $pago->metodo_pago }}</td>
                    <td>
                      @if($pago->aprobado)
                        <a href="{{ route('pagos.recibo', $pago->id) }}" class="btn btn-soft btn-indigo btn-soft-sm" target="_blank">Ver</a>
                      @else
                        <span class="text-muted small">—</span>
                      @endif
                    </td>
                    <td>
                      @if($pago->aprobado)
                        <span class="badge bg-success">Pagado</span>
                      @else
                        <span class="badge bg-warning text-dark">Pendiente</span>
                      @endif
                    </td>
                    <td>
                      @if(!$pago->aprobado && !$pago->financiamiento_id)
                        <button type="button" class="btn btn-soft btn-rose btn-soft-sm eliminar-pago-btn" data-form-id="del-{{ $pago->id }}">
                          Eliminar
                        </button>
                        <form id="del-{{ $pago->id }}" action="{{ route('pagos.destroy', $pago->id) }}" method="POST" class="d-none">
                          @csrf @method('DELETE')
                        </form>

                      @elseif(!$pago->aprobado && $pago->financiamiento_id)
                        <button type="button" class="btn btn-soft btn-amber btn-soft-sm aprobar-pago-btn" data-form-id="form-{{ $pago->id }}">
                          Aprobar
                        </button>
                        <form id="form-{{ $pago->id }}" action="{{ route('pagos.marcarPagado', $pago->financiamiento_id) }}" method="POST" class="d-none">
                          @csrf @method('PUT')
                          <input type="hidden" name="pin" class="pin-input">
                        </form>
                      @else
                        <button class="btn btn-soft btn-slate btn-soft-sm" disabled>Pagado</button>
                      @endif
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>

        </div>
      </div>
    </div>

  </div>
</div>

{{-- ========================= MODAL EDITAR PAGOS ========================= --}}
<div class="modal fade modal-pro" id="modalEditarPagos" tabindex="-1" aria-labelledby="modalEditarPagosLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-fullscreen-sm-down">
    <form id="formEditarPagos"
          action="{{ route('ventas.pagosFinanciamiento.update', $venta->id) }}"
          method="POST"
          onsubmit="return validarTotalPagos(event)">
      @csrf @method('PUT')

      <div class="modal-content">
        <div class="modal-header">
          <div class="w-100 d-flex align-items-center justify-content-between">
            <div>
              <h6 class="modal-title mb-0" id="modalEditarPagosLabel">Editar pagos</h6>
              <div class="text-muted small">Descripción, fecha y monto.</div>
            </div>
            <button type="button" class="btn-x" data-bs-dismiss="modal" aria-label="Cerrar">
              <i class="bi bi-x-lg"></i>
            </button>
          </div>
        </div>

        <div class="modal-body">
          <div class="pf-top">
            <div>
              <div class="k">SALDO A PROGRAMAR</div>
              <div class="v">${{ number_format((float)$saldoRestante, 2) }}</div>
            </div>
            <div class="pf-chip">
              Total: <strong>$<span id="pfTotal">0.00</span></strong>
            </div>
          </div>

          <div class="pf-alert" id="pfAlert"></div>

          <div class="pf-list" id="pfList" data-paid-offset="{{ $paidInstallments }}">
            @forelse($pagosEditable as $p)
              @php
                $fechaISO = \Carbon\Carbon::parse($p->fecha_pago)->format('Y-m-d');
                $n = $paidInstallments + $loop->iteration;
                $labelAuto = ordinalES($n) . ' pago';
                $isAuto = esDescAutoContraEsperado($p->descripcion, $labelAuto);
                $descMostrada = $isAuto ? $labelAuto : $p->descripcion;
              @endphp

              <div class="pf-row" data-row>
                <div class="pf-grid">
                  <div class="col-desc">
                    <div class="pf-label">Descripción</div>
                    <input type="text"
                           name="pagos_financiamiento[{{ $p->id }}][descripcion]"
                           class="pf-input w-100"
                           value="{{ $descMostrada }}"
                           data-auto="{{ $isAuto ? 1 : 0 }}"
                           data-manual="0"
                           oninput="this.dataset.manual='1'; this.dataset.auto='0';"
                           required>
                  </div>

                  <div class="col-fecha">
                    <div class="pf-label">Fecha</div>
                    <input type="date"
                           name="pagos_financiamiento[{{ $p->id }}][fecha_pago]"
                           class="pf-input w-100"
                           value="{{ $fechaISO }}"
                           required>
                  </div>

                  <div class="col-monto">
                    <div class="pf-label">Monto</div>
                    <div class="pf-money">
                      <span>$</span>
                      <input type="number"
                             name="pagos_financiamiento[{{ $p->id }}][monto]"
                             class="monto-pago"
                             min="0"
                             step="0.01"
                             value="{{ number_format((float)$p->monto, 2, '.', '') }}"
                             required>
                    </div>
                  </div>

                  <div class="pf-trash col-trash">
                    <button type="button"
                            class="btn btn-soft btn-rose btn-soft-sm"
                            title="Eliminar"
                            onclick="eliminarPagoExistente(this)">
                      <i class="bi bi-trash"></i>
                    </button>
                    <input type="hidden" name="pagos_financiamiento[{{ $p->id }}][eliminar]" value="0" class="campo-eliminar">
                  </div>
                </div>
              </div>
            @empty
              <div class="text-center text-muted small py-3">No hay pagos pendientes para editar.</div>
            @endforelse
          </div>

          <div class="pf-actions">
            <button type="button" class="btn btn-soft btn-slate btn-soft-sm" onclick="agregarFilaPago()">
              <i class="bi bi-plus-lg me-1"></i> Agregar pago
            </button>
            <div class="text-muted small">El botón se habilita cuando el total coincide.</div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-soft btn-indigo btn-soft-sm" id="btnGuardarPagos" disabled>
            Guardar
          </button>
        </div>
      </div>
    </form>
  </div>
</div>

{{-- ==================== MODAL REGISTRAR PAGO ==================== --}}
<div class="modal fade modal-pro" id="pagoModal" tabindex="-1" aria-labelledby="pagoModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form id="formPagoModal" method="POST" enctype="multipart/form-data">
      @csrf
      <input type="hidden" name="financiamiento_id" id="pm-fin-id">

      <div class="modal-content">
        <div class="modal-header">
          <div class="w-100 d-flex align-items-center justify-content-between">
            <h6 class="modal-title mb-0" id="pagoModalLabel">Registrar pago</h6>
            <button type="button" class="btn-x" data-bs-dismiss="modal" aria-label="Cerrar">
              <i class="bi bi-x-lg"></i>
            </button>
          </div>
        </div>

        <div class="modal-body">
          <div class="bank-card">
            <div class="d-flex justify-content-between gap-3">
              <div>
                <div class="bank-k">Fecha</div>
                <div class="bank-v" id="pm-fecha-text">—</div>
                <div class="bank-k mt-1" id="pm-desc-text"></div>
              </div>
              <div class="text-end">
                <div class="bank-k">Monto esperado</div>
                <div class="bank-amount" id="pm-esperado-text">$0.00</div>
              </div>
            </div>
          </div>

          <input type="hidden" id="pm-expected-amount" value="0">

          <div class="mb-3">
            <label class="form-label">Monto recibido</label>
            <div class="input-group">
              <span class="input-group-text">$</span>
              <input type="number" step="0.01" min="0" class="form-control bank-input" name="monto" id="modalMonto" required>
            </div>

            <div class="match-wrap">
              <div class="match-bar"><div class="match-fill" id="matchFill"></div></div>
              <div class="match-label"><span id="matchText">0% coincide con el esperado</span></div>
            </div>

            <div class="inline-warning" id="pm-mismatch">
              El monto ingresado no coincide con el monto programado.
              Si necesitas cambiarlo, edita primero el plan en <b>Editar pagos</b>.
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label">Fecha de pago</label>
            <input type="date" class="form-control bank-input" name="fecha_pago" id="modalFecha" required>
          </div>

          <div class="mb-2">
            <label class="form-label">Método de pago</label>
            <select class="form-select bank-select" name="metodo_pago" id="modalMetodo" required>
              <option value="">Selecciona…</option>
              <option value="Efectivo">Efectivo</option>
              <option value="Transferencia bancaria">Transferencia bancaria</option>
              <option value="Tarjeta">Tarjeta</option>
              <option value="Cheque">Cheque</option>
            </select>
          </div>

          <div class="mb-2">
            <label class="form-label">Adjuntar recibo (opcional)</label>
            <input type="file" class="form-control bank-input" name="recibo">
          </div>
        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-soft btn-teal btn-soft-sm">Guardar pago</button>
        </div>
      </div>
    </form>
  </div>
</div>

{{-- ==================== MODAL PIN ==================== --}}
<div class="modal fade modal-pro" id="pinModal" tabindex="-1" aria-labelledby="pinModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" style="max-width:520px;">
    <div class="modal-content">
      <div class="modal-header" style="background:#fff;">
        <div class="w-100 d-flex align-items-center justify-content-between">
          <h6 class="modal-title mb-0" id="pinModalLabel">Aprobar pago</h6>
          <button type="button" class="btn-x" data-bs-dismiss="modal" aria-label="Cerrar">
            <i class="bi bi-x-lg"></i>
          </button>
        </div>
      </div>
      <div class="modal-body">
        <div style="border:1px solid var(--border); background:#fbfdff; border-radius:14px; padding:12px;">
          <div class="text-muted small">Ingresa tu PIN de 6 dígitos.</div>
          <div class="d-flex gap-2 justify-content-center mt-2" aria-label="PIN">
            @for($i=0;$i<6;$i++)
              <input class="pin-box" inputmode="numeric" maxlength="1" autocomplete="one-time-code">
            @endfor
          </div>
          <div class="pin-error" id="pinError">PIN inválido.</div>
        </div>
        <input type="hidden" id="pinTargetFormId">
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/locale/es.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function(){
  // Tooltips
  document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => new bootstrap.Tooltip(el));

  // Toast único
  const oneToast = document.getElementById('oneToast');
  if(oneToast){
    bootstrap.Toast.getOrCreateInstance(oneToast).show();
    oneToast.addEventListener('hidden.bs.toast', ()=> oneToast.remove());
  }

  // =========================
  // MODAL REGISTRAR PAGO
  // =========================
  const pagoModalEl = document.getElementById('pagoModal');
  const formPago    = document.getElementById('formPagoModal');

  const pmFinId     = document.getElementById('pm-fin-id');
  const montoInput  = document.getElementById('modalMonto');
  const fechaInput  = document.getElementById('modalFecha');
  const metodoInput = document.getElementById('modalMetodo');

  const expectedEl  = document.getElementById('pm-expected-amount');
  const esperadoTxt = document.getElementById('pm-esperado-text');
  const fechaTxt    = document.getElementById('pm-fecha-text');
  const descTxt     = document.getElementById('pm-desc-text');

  const mismatch    = document.getElementById('pm-mismatch');
  const matchFill   = document.getElementById('matchFill');
  const matchText   = document.getElementById('matchText');

  const fmtMoney = (n)=> Number(n||0).toLocaleString('es-MX',{minimumFractionDigits:2, maximumFractionDigits:2});
  const formatDateES = (iso)=>{
    if(!iso) return '—';
    const d = new Date(String(iso).slice(0,10) + 'T00:00:00');
    return d.toLocaleDateString('es-MX');
  };
  const toISODate = (v)=> v ? String(v).slice(0,10) : '';

  function updateMatch(){
    const esperado = parseFloat(expectedEl.value || 0);
    const ingresado = parseFloat(montoInput.value || 0);

    let pct = 0;
    if(esperado > 0) pct = Math.max(0, Math.min(100, (ingresado/esperado)*100));

    matchFill.style.inset = `0 ${Math.max(0, 100 - pct)}% 0 0`;
    matchText.textContent = `${Math.round(pct)}% coincide con el esperado`;

    const iguales = Math.abs(esperado - ingresado) < 0.005;
    mismatch.style.display = iguales ? 'none' : 'block';
  }

  pagoModalEl.addEventListener('show.bs.modal', function (event) {
    const btn = event.relatedTarget;
    if(!btn) return;

    const finId       = btn.getAttribute('data-fin-id') || '';
    const fecha       = toISODate(btn.getAttribute('data-fecha') || '');
    const esperado    = parseFloat(btn.getAttribute('data-monto') || 0);
    const descripcion = btn.getAttribute('data-descripcion') || '';
    const actionUrl   = btn.getAttribute('data-action') || '';

    formPago.action = actionUrl;
    pmFinId.value   = finId;

    fechaTxt.textContent = formatDateES(fecha);
    descTxt.textContent  = descripcion;
    expectedEl.value     = esperado.toFixed(2);
    esperadoTxt.textContent = '$' + fmtMoney(esperado);

    fechaInput.value  = fecha;
    montoInput.value  = esperado.toFixed(2);
    metodoInput.value = '';

    mismatch.style.display = 'none';
    updateMatch();
  });

  montoInput.addEventListener('input', updateMatch);
  montoInput.addEventListener('blur', ()=>{ if(montoInput.value){ montoInput.value = (parseFloat(montoInput.value)||0).toFixed(2); } updateMatch(); });

  // =========================
  // PIN (AUTO SUBMIT)
  // =========================
  const pinModalEl = document.getElementById('pinModal');
  const pinModal   = bootstrap.Modal.getOrCreateInstance(pinModalEl);
  const pinBoxes   = Array.from(pinModalEl.querySelectorAll('.pin-box'));
  const pinTarget  = document.getElementById('pinTargetFormId');
  const pinError   = document.getElementById('pinError');

  function clearPin(){
    pinBoxes.forEach(i => i.value = '');
    pinError.style.display = 'none';
    pinError.textContent = 'PIN inválido.';
  }
  function getPin(){ return pinBoxes.map(i => (i.value || '')).join(''); }
  function onlyDigit(inp){ inp.value = (inp.value || '').replace(/\D/g,'').slice(0,1); }

  function tryAutoSubmit(){
    const pin = getPin();
    if(pin.length !== 6) return;

    if(!/^\d{6}$/.test(pin)){
      pinError.style.display = 'block';
      return;
    }

    const formId = pinTarget.value;
    const form = document.getElementById(formId);
    if(!form){
      pinError.textContent = 'No se encontró el formulario.';
      pinError.style.display = 'block';
      return;
    }

    const pinInput = form.querySelector('.pin-input');
    if(pinInput) pinInput.value = pin;

    pinModal.hide();
    setTimeout(()=> form.submit(), 120);
  }

  pinModalEl.addEventListener('shown.bs.modal', () => {
    clearPin();
    setTimeout(()=> pinBoxes[0].focus(), 80);
  });
  pinModalEl.addEventListener('hidden.bs.modal', () => {
    clearPin();
    pinTarget.value = '';
  });

  pinBoxes.forEach((inp, idx) => {
    inp.addEventListener('input', () => {
      onlyDigit(inp);
      if(inp.value && idx < pinBoxes.length - 1) pinBoxes[idx+1].focus();
      tryAutoSubmit();
    });
    inp.addEventListener('keydown', (e) => {
      if(e.key === 'Backspace' && !inp.value && idx > 0) pinBoxes[idx-1].focus();
    });
    inp.addEventListener('paste', (e) => {
      e.preventDefault();
      const text = (e.clipboardData.getData('text') || '').replace(/\D/g,'').slice(0,6);
      if(!text) return;
      text.split('').forEach((ch, i) => { if(pinBoxes[i]) pinBoxes[i].value = ch; });
      tryAutoSubmit();
    });
  });

  // Acciones aprobar/eliminar (pagos realizados)
  document.addEventListener('click', function(e){
    const aprobarBtn = e.target.closest('.aprobar-pago-btn');
    if(aprobarBtn){
      e.preventDefault();
      const formId = aprobarBtn.dataset.formId;
      if(!document.getElementById(formId)) return;
      pinTarget.value = formId;
      pinModal.show();
      return;
    }

    const eliminarBtn = e.target.closest('.eliminar-pago-btn');
    if(eliminarBtn){
      e.preventDefault();
      const formId = eliminarBtn.dataset.formId;
      const form = document.getElementById(formId);
      if(!form) return;
      form.submit();
      return;
    }
  });
});
</script>

{{-- ==================== LÓGICA EDITAR PAGOS (NO PISA LO MANUAL + ERROR CLARO) ==================== --}}
<script>
(() => {
  let contadorNuevoPago = 1;

  const objetivoCents = Math.round(Number({{ (float)$saldoRestante }}) * 100);

  const $list  = () => document.getElementById('pfList');
  const $alert = () => document.getElementById('pfAlert');
  const $btn   = () => document.getElementById('btnGuardarPagos');
  const $total = () => document.getElementById('pfTotal');

  const paidOffset = Math.max(0, Number($list()?.dataset?.paidOffset || 0));

  const toCents  = (v)=> Math.round((parseFloat(v || 0) || 0) * 100);
  const fmtMoney = (n)=> Number(n||0).toLocaleString('es-MX',{minimumFractionDigits:2, maximumFractionDigits:2});

  const ordinalESjs = (n)=>{
    const ord = [
      'Primer','Segundo','Tercer','Cuarto','Quinto','Sexto','Séptimo','Octavo','Noveno','Décimo',
      'Onceavo','Doceavo','Treceavo','Catorceavo','Quinceavo','Dieciseisavo','Diecisieteavo','Dieciochoavo','Diecinueveavo','Veinteavo'
    ];
    return (n >= 1 && n <= ord.length) ? ord[n-1] : `Pago ${n}`;
  };

  function rowsVisible(){
    return Array.from($list().querySelectorAll('[data-row]')).filter(el => el.style.display !== 'none');
  }

  function sumCents(){
    return rowsVisible().reduce((acc, row)=>{
      const inp = row.querySelector('.monto-pago');
      return acc + toCents(inp ? inp.value : 0);
    }, 0);
  }

  function setAlert(msg){
    const a = $alert();
    if(!msg){
      a.classList.remove('show');
      a.innerHTML = '';
      return;
    }
    a.classList.add('show');
    a.innerHTML = msg;
  }

  function getDescInput(row){
    return row.querySelector('input[name*="[descripcion]"]');
  }

  function esAutoGenerico(v){
    const s = (v || '').trim();
    if(!s) return true;
    if(/^pago$/iu.test(s)) return true;
    if(/^pago\s*\d+$/iu.test(s)) return true;
    return false;
  }

  // ✅ Solo autollenamos si:
  // - no es manual
  // - y (está vacío) o (es genérico) o (es EXACTAMENTE el esperado)
  function renumerarSoloAuto(){
    const visibles = rowsVisible();

    visibles.forEach((row, idx)=>{
      const inp = getDescInput(row);
      if(!inp) return;

      if(inp.dataset.manual === '1') return;

      const n = paidOffset + idx + 1;
      const esperado = `${ordinalESjs(n)} pago`;
      const val = (inp.value || '').trim();

      // inicializa auto si no existe
      if(!inp.dataset.auto){
        inp.dataset.auto = (esAutoGenerico(val) || val === '' || val.toLowerCase() === esperado.toLowerCase()) ? '1' : '0';
      }

      if(val === '' || esAutoGenerico(val) || val.toLowerCase() === esperado.toLowerCase()){
        inp.value = esperado;
        inp.dataset.auto = '1';
      } else {
        // si tiene algo distinto, es manual aunque no lo haya tecleado aquí
        inp.dataset.auto = '0';
      }
    });
  }

  function actualizarTotalPagos(){
    const total = sumCents();
    const diff  = objetivoCents - total;

    $total().textContent = fmtMoney(total/100);

    if(rowsVisible().length === 0){
      setAlert('No hay pagos para guardar.');
      $btn().disabled = true;
      return;
    }

    for(const row of rowsVisible()){
      const desc = getDescInput(row);
      const date = row.querySelector('input[type="date"]');
      const mon  = row.querySelector('.monto-pago');

      if(desc && !desc.value.trim()){
        setAlert('Falta una descripción.');
        $btn().disabled = true; return;
      }
      if(date && !date.value){
        setAlert('Falta una fecha.');
        $btn().disabled = true; return;
      }
      if(mon && (parseFloat(mon.value || 0) <= 0)){
        setAlert('Hay un monto en 0.00.');
        $btn().disabled = true; return;
      }
    }

    if(diff !== 0){
      setAlert(diff > 0
        ? `El total está <b>por debajo</b> por <b>$${fmtMoney(diff/100)}</b>.`
        : `El total está <b>por encima</b> por <b>$${fmtMoney(Math.abs(diff)/100)}</b>.`
      );
      $btn().disabled = true;
      return;
    }

    setAlert('');
    $btn().disabled = false;
  }

  // ✅ NO renumeramos en submit
  window.validarTotalPagos = function(e){
    actualizarTotalPagos();
    if($btn().disabled){
      e.preventDefault();
      return false;
    }
    return true;
  };

  function bindRow(row){
    const mon = row.querySelector('.monto-pago');
    if(mon){
      mon.addEventListener('input', actualizarTotalPagos);
      mon.addEventListener('blur', ()=>{
        if(mon.value) mon.value = (parseFloat(mon.value)||0).toFixed(2);
        actualizarTotalPagos();
      });
    }

    const date = row.querySelector('input[type="date"]');
    if(date) date.addEventListener('change', actualizarTotalPagos);

    const desc = getDescInput(row);
    if(desc){
      // ✅ reforzado: si el user cambia, ya es manual
      desc.addEventListener('input', ()=> { desc.dataset.manual='1'; desc.dataset.auto='0'; });
      desc.addEventListener('change', ()=> { desc.dataset.manual='1'; desc.dataset.auto='0'; });
    }
  }

  function redistribuirEquitativo(){
    const filas = rowsVisible();
    const n = filas.length;
    if(n === 0){ actualizarTotalPagos(); return; }

    const base = Math.floor(objetivoCents / n);
    let acum = 0;

    filas.forEach((row, i)=>{
      const cents = (i === n-1) ? (objetivoCents - acum) : base;
      const inp = row.querySelector('.monto-pago');
      if(inp) inp.value = (cents/100).toFixed(2);
      acum += cents;
    });

    renumerarSoloAuto();
    actualizarTotalPagos();
  }

  window.agregarFilaPago = function(){
    const idx = 'nuevo_' + (contadorNuevoPago++);

    const visibles = rowsVisible();
    let lastDate = '';
    visibles.forEach(r=>{
      const d = r.querySelector('input[type="date"]');
      if(d && d.value) lastDate = d.value;
    });

    const fechaNueva = lastDate
      ? moment(lastDate).add(1,'months').format('YYYY-MM-DD')
      : moment().format('YYYY-MM-DD');

    const div = document.createElement('div');
    div.className = 'pf-row';
    div.setAttribute('data-row','1');
    div.innerHTML = `
      <div class="pf-grid">
        <div class="col-desc">
          <div class="pf-label">Descripción</div>
          <input type="text" name="pagos_financiamiento[${idx}][descripcion]" class="pf-input w-100" placeholder="Pago" data-auto="1" data-manual="0"
                 oninput="this.dataset.manual='1'; this.dataset.auto='0';" required>
        </div>

        <div class="col-fecha">
          <div class="pf-label">Fecha</div>
          <input type="date" name="pagos_financiamiento[${idx}][fecha_pago]" class="pf-input w-100" value="${fechaNueva}" required>
        </div>

        <div class="col-monto">
          <div class="pf-label">Monto</div>
          <div class="pf-money">
            <span>$</span>
            <input type="number" name="pagos_financiamiento[${idx}][monto]" class="monto-pago" min="0" step="0.01" placeholder="0.00" required>
          </div>
        </div>

        <div class="pf-trash col-trash">
          <button type="button" class="btn btn-soft btn-rose btn-soft-sm" title="Eliminar" onclick="eliminarPagoNuevo(this)">
            <i class="bi bi-trash"></i>
          </button>
        </div>
      </div>
    `;

    $list().appendChild(div);
    bindRow(div);

    renumerarSoloAuto();
    redistribuirEquitativo();
  };

  window.eliminarPagoNuevo = function(btn){
    const row = btn.closest('[data-row]');
    if(row) row.remove();
    redistribuirEquitativo();
  };

  window.eliminarPagoExistente = function(btn){
    const row = btn.closest('[data-row]');
    if(!row) return;
    const hidden = row.querySelector('.campo-eliminar');
    if(hidden) hidden.value = '1';
    row.style.display = 'none';
    redistribuirEquitativo();
  };

  document.addEventListener('DOMContentLoaded', function(){
    rowsVisible().forEach(bindRow);
    renumerarSoloAuto();
    setTimeout(actualizarTotalPagos, 120);

    const modalEl = document.getElementById('modalEditarPagos');
    if(modalEl){
      modalEl.addEventListener('show.bs.modal', function(){
        setTimeout(()=>{
          renumerarSoloAuto();
          actualizarTotalPagos();
        }, 80);
      });
    }
  });
})();
</script>

@endsection
