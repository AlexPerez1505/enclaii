@extends('layouts.app')
@section('title', 'Orden de Servicio')
@section('titulo', 'Pagos OS No ' . $orden->id)

@section('content')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/pagos.css') }}?v={{ time() }}">

<style>
  .page-topbar{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:14px;
    flex-wrap:wrap;
    margin-bottom:22px;
    padding:0 2px;
  }

  .page-topbar-info{
    min-width:0;
  }

  .page-kicker{
    margin:0 0 4px 0;
    font-size:.78rem;
    font-weight:700;
    letter-spacing:.08em;
    text-transform:uppercase;
    color:#64748b;
  }

  .page-heading{
    margin:0;
    font-size:1.55rem;
    font-weight:800;
    letter-spacing:-.02em;
    color:#0f172a;
  }

  .btn-back-minimal{
    display:inline-flex;
    align-items:center;
    gap:8px;
    padding:10px 16px;
    border:1px solid #e2e8f0;
    border-radius:14px;
    background:#ffffff;
    color:#0f172a;
    text-decoration:none;
    font-weight:700;
    font-size:.95rem;
    box-shadow:0 10px 25px rgba(15,23,42,.06);
    transition:all .18s ease;
  }

  .btn-back-minimal i{
    font-size:1.1rem;
    line-height:1;
  }

  .btn-back-minimal:hover{
    color:#0f172a;
    background:#f8fafc;
    border-color:#cbd5e1;
    transform:translateY(-1px);
    box-shadow:0 14px 30px rgba(15,23,42,.09);
  }

  .btn-back-minimal:active{
    transform:translateY(0);
    box-shadow:0 8px 18px rgba(15,23,42,.08);
  }

  @media (max-width: 576px){
    .page-heading{
      font-size:1.2rem;
    }

    .btn-back-minimal{
      width:100%;
      justify-content:center;
    }
  }
</style>

@php
  // =========================
  // Helpers (BLADE) — mismos de tu vista de ventas
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

  // Para OS no hay plan de financiamiento, pero dejamos helper por compatibilidad visual
  if (!function_exists('esDescAutoContraEsperado')) {
    function esDescAutoContraEsperado($desc, $esperado){
      $desc = trim((string)$desc);
      $esperado = trim((string)$esperado);

      if($desc === '') return true;
      if(preg_match('/^pago$/iu', $desc)) return true;
      if(preg_match('/^pago\s*\d+$/iu', $desc)) return true;

      return mb_strtolower($desc) === mb_strtolower($esperado);
    }
  }

  $clienteNombre = trim((optional($orden->cliente)->nombre.' '.optional($orden->cliente)->apellido) ?? '');
  if(!$clienteNombre) $clienteNombre = 'SIN CLIENTE';

  // Para "Próximos pagos" en OS: mostramos pagos NO aprobados como "pendientes por aprobar"
  $pagosPendientes = collect($pagos)->where('aprobado', false)->sortBy('fecha_pago')->values();

  $totalOS          = (float) $total;
  $totalPagadoOS    = (float) $pagado;
  $saldoRestanteOS  = (float) $restante;
  $porcentajePagado = (float) $progreso;

  $tooltipText = "Has pagado $" . number_format($totalPagadoOS, 2) . " de $" . number_format($totalOS, 2);

  $previousUrl = url()->previous();
  $currentUrl  = url()->current();
  $backUrl     = ($previousUrl && $previousUrl !== $currentUrl) ? $previousUrl : url('/ordenes');
@endphp

{{-- ✅ TOAST ÚNICO (igual) --}}
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

  {{-- ✅ ENCABEZADO + BOTÓN REGRESAR --}}
  <div class="page-topbar">
    <div class="page-topbar-info">
      <div class="page-kicker">Gestión de pagos</div>
      <h1 class="page-heading">Pagos OS No {{ $orden->id }}</h1>
    </div>

    <a href="{{ $backUrl }}" class="btn-back-minimal">
      <i class="bi bi-arrow-left"></i>
      <span>Regresar</span>
    </a>
  </div>

  <div class="row g-4">

    {{-- ================= INFO ORDEN ================= --}}
    <div class="col-lg-5">
      <div class="card shadow-sm">
        <div class="card-body">
          <h5 class="card-title mb-3">{{ $clienteNombre }}</h5>

          <p><strong>Orden:</strong> OS-{{ $orden->id }}</p>
          <p><strong>Equipo:</strong> {{ $orden->equipo ?? '—' }}</p>
          @if(!empty($orden->marca) || !empty($orden->modelo))
            <p><strong>Marca / Modelo:</strong> {{ trim(($orden->marca ?? '').' '.($orden->modelo ?? '')) }}</p>
          @endif

          <p><strong>Total:</strong> ${{ number_format($totalOS, 2) }}</p>
          <p><strong>Total pagado:</strong> ${{ number_format($totalPagadoOS, 2) }}</p>
          <p><strong>Saldo restante:</strong> ${{ number_format($saldoRestanteOS, 2) }}</p>

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

          <div class="mt-3 d-flex gap-2 flex-wrap">
            {{-- Si ya tienes PDF de remisión OS --}}
            @php
              $router = app('router');
              $pdfUrl = $router->has('ordenes.remision.pdf')
                ? route('ordenes.remision.pdf', $orden->id)
                : url('/ordenes/'.$orden->id.'/remision-pdf');
            @endphp

            <a href="{{ $pdfUrl }}"
               target="_blank"
               class="btn btn-soft btn-indigo btn-soft-sm">
              <i class="bi bi-file-earmark-pdf me-1"></i>
              Descargar remisión (PDF)
            </a>
          </div>
        </div>
      </div>

      {{-- ================= PRÓXIMOS PAGOS (en OS = pagos pendientes por aprobar) ================= --}}
      <div class="mt-4">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <h5 class="mb-0">Pagos pendientes</h5>

          {{-- En OS NO editas plan, solo registras pago --}}
          <button class="btn btn-soft btn-indigo btn-soft-sm"
                  data-bs-toggle="modal"
                  data-bs-target="#pagoModalOS"
                  data-action="{{ route('ordenes.pagos.store', $orden->id) }}">
            Registrar pago
          </button>
        </div>

        @if($pagosPendientes->isEmpty())
          <div class="alert alert-secondary">No hay pagos pendientes.</div>
        @else
          <ul class="list-group small">
            @foreach($pagosPendientes as $p)
              @php
                $fechaISO = $p->fecha_pago ? \Carbon\Carbon::parse($p->fecha_pago)->format('Y-m-d') : now()->format('Y-m-d');
                $descMostrada = $p->es_anticipo ? 'Anticipo' : 'Pago';
              @endphp

              <li class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                  <strong>{{ $p->fecha_pago ? \Carbon\Carbon::parse($p->fecha_pago)->format('d/m/Y') : '—' }}</strong><br>
                  <small>{{ $descMostrada }}</small>
                </div>
                <div class="text-end">
                  <span class="fw-bold d-block">${{ number_format((float)$p->monto, 2) }}</span>

                  {{-- Botón “Registrar” aquí lo usamos como "aprobar" (igual flujo de PIN) --}}
                  <button type="button"
                          class="btn btn-soft btn-amber btn-soft-sm mt-1 aprobar-os-btn"
                          data-form-id="form-os-aprobar-{{ $p->id }}">
                    Aprobar
                  </button>

                  <form id="form-os-aprobar-{{ $p->id }}"
                        action="{{ route('ordenes.pagos.aprobar', $p->id) }}"
                        method="POST" class="d-none">
                    @csrf
                    <input type="hidden" name="pin" class="pin-input">
                  </form>
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
                @forelse($pagos as $pago)
                  <tr>
                    <td>{{ $pago->fecha_pago ? \Carbon\Carbon::parse($pago->fecha_pago)->format('d/m/Y') : '—' }}</td>
                    <td>${{ number_format((float)$pago->monto, 2) }}</td>
                    <td>{{ $pago->metodo_pago ?? '—' }}</td>

                    {{-- Recibo: si aún no manejas recibo en OS, dejamos "—" --}}
                    <td>
                      @if($pago->aprobado && $pago->id)
                        <span class="text-muted small">—</span>
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
                      @if($pago->es_anticipo)
                        <span class="badge bg-light text-dark border ms-2">Anticipo</span>
                      @endif
                    </td>

                    <td class="d-flex gap-2 flex-wrap">
                      @if(!$pago->aprobado)
                        {{-- ✅ Aprobar con PIN --}}
                        <button type="button"
                                class="btn btn-soft btn-amber btn-soft-sm aprobar-os-btn"
                                data-form-id="form-os-aprobar-{{ $pago->id }}">
                          Aprobar
                        </button>

                        <form id="form-os-aprobar-{{ $pago->id }}"
                              action="{{ route('ordenes.pagos.aprobar', $pago->id) }}"
                              method="POST" class="d-none">
                          @csrf
                          <input type="hidden" name="pin" class="pin-input">
                        </form>

                        {{-- ✅ Eliminar (sin PIN) --}}
                        <button type="button"
                                class="btn btn-soft btn-rose btn-soft-sm eliminar-os-btn"
                                data-form-id="del-os-{{ $pago->id }}">
                          Eliminar
                        </button>

                        <form id="del-os-{{ $pago->id }}"
                              action="{{ route('ordenes.pagos.destroy', $pago->id) }}"
                              method="POST" class="d-none">
                          @csrf
                          @method('DELETE')
                        </form>

                      @else
                        {{-- ✅ Revertir con PIN --}}
                        <button type="button"
                                class="btn btn-soft btn-slate btn-soft-sm revertir-os-btn"
                                data-form-id="rev-os-{{ $pago->id }}">
                          Revertir
                        </button>

                        <form id="rev-os-{{ $pago->id }}"
                              action="{{ route('ordenes.pagos.revertir', $pago->id) }}"
                              method="POST" class="d-none">
                          @csrf
                          <input type="hidden" name="pin" class="pin-input">
                        </form>
                      @endif
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="6" class="text-center text-muted py-4">No hay pagos aún.</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>

        </div>
      </div>
    </div>

  </div>
</div>

{{-- ==================== MODAL REGISTRAR PAGO (OS) ==================== --}}
<div class="modal fade modal-pro" id="pagoModalOS" tabindex="-1" aria-labelledby="pagoModalOSLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form id="formPagoModalOS" method="POST" enctype="multipart/form-data" action="{{ route('ordenes.pagos.store', $orden->id) }}">
      @csrf

      <div class="modal-content">
        <div class="modal-header">
          <div class="w-100 d-flex align-items-center justify-content-between">
            <h6 class="modal-title mb-0" id="pagoModalOSLabel">Registrar pago</h6>
            <button type="button" class="btn-x" data-bs-dismiss="modal" aria-label="Cerrar">
              <i class="bi bi-x-lg"></i>
            </button>
          </div>
        </div>

        <div class="modal-body">
          <div class="bank-card">
            <div class="d-flex justify-content-between gap-3">
              <div>
                <div class="bank-k">Orden</div>
                <div class="bank-v">OS-{{ $orden->id }}</div>
                <div class="bank-k mt-1">{{ $clienteNombre }}</div>
              </div>
              <div class="text-end">
                <div class="bank-k">Saldo restante</div>
                <div class="bank-amount">${{ number_format((float)$saldoRestanteOS, 2) }}</div>
              </div>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label">Monto recibido</label>
            <div class="input-group">
              <span class="input-group-text">$</span>
              <input type="number" step="0.01" min="0" class="form-control bank-input" name="monto" required>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label">Fecha de pago</label>
            <input type="date" class="form-control bank-input" name="fecha_pago" value="{{ now()->format('Y-m-d') }}" required>
          </div>

          <div class="mb-2">
            <label class="form-label">Método de pago</label>
            <select class="form-select bank-select" name="metodo_pago" required>
              <option value="">Selecciona…</option>
              <option value="Efectivo">Efectivo</option>
              <option value="Transferencia bancaria">Transferencia bancaria</option>
              <option value="Tarjeta">Tarjeta</option>
              <option value="Cheque">Cheque</option>
            </select>
          </div>

          <div class="mb-2">
            <label class="form-label">¿Es anticipo?</label>
            <select class="form-select bank-select" name="es_anticipo">
              <option value="0" selected>No</option>
              <option value="1">Sí, es anticipo</option>
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

{{-- ==================== MODAL PIN (MISMO) ==================== --}}
<div class="modal fade modal-pro" id="pinModal" tabindex="-1" aria-labelledby="pinModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" style="max-width:520px;">
    <div class="modal-content">
      <div class="modal-header" style="background:#fff;">
        <div class="w-100 d-flex align-items-center justify-content-between">
          <h6 class="modal-title mb-0" id="pinModalLabel">Aprobar / Revertir</h6>
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

<script>
document.addEventListener('DOMContentLoaded', function(){
  document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => new bootstrap.Tooltip(el));

  const oneToast = document.getElementById('oneToast');
  if(oneToast){
    bootstrap.Toast.getOrCreateInstance(oneToast).show();
    oneToast.addEventListener('hidden.bs.toast', ()=> oneToast.remove());
  }

  // =========================
  // PIN (AUTO SUBMIT) — igual que ventas
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

  // Acciones aprobar / revertir / eliminar (OS)
  document.addEventListener('click', function(e){
    const aprobarBtn = e.target.closest('.aprobar-os-btn');
    if(aprobarBtn){
      e.preventDefault();
      const formId = aprobarBtn.dataset.formId;
      if(!document.getElementById(formId)) return;
      pinTarget.value = formId;
      pinModal.show();
      return;
    }

    const revertirBtn = e.target.closest('.revertir-os-btn');
    if(revertirBtn){
      e.preventDefault();
      const formId = revertirBtn.dataset.formId;
      if(!document.getElementById(formId)) return;
      pinTarget.value = formId;
      pinModal.show();
      return;
    }

    const eliminarBtn = e.target.closest('.eliminar-os-btn');
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

@endsection