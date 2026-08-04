@extends('layouts.app')

@section('title', 'Gastos')
@section('titulo', 'Gastos')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<style>
  :root{
    --bg:#f5f7fb; --panel:#ffffff; --text:#0f172a; --muted:#667085; --border:#e8eef6;
    /* Pastel primario (azul) y éxito (verde) */
    --pblue:#cfe6ff; --pblue-strong:#5aa9ff; --pblue-dark:#1f7ae6;
    --pgreen:#c9f7df; --pgreen-strong:#34d399; --pgreen-dark:#059669;
    --shadow:0 10px 40px rgba(2,6,23,.08);
  }
  body{ background:var(--bg); color:var(--text); }
  .page-wrap{ max-width:1040px; }

  .hero { background:linear-gradient(135deg, rgba(90,169,255,.12), rgba(52,211,153,.10));
    border:1px solid #d9e9ff; border-radius:18px; padding:16px 18px; box-shadow:var(--shadow); }

  .nav-tabs{ border:0; gap:.4rem }
  .nav-tabs .nav-link{ border:1px solid var(--border); border-radius:12px; font-weight:700; color:#475467; background:#fff;
    transition:transform .15s ease, box-shadow .2s ease, color .2s }
  .nav-tabs .nav-link:hover{ transform:translateY(-1px); box-shadow:0 8px 20px rgba(31,122,230,.15) }
  .nav-tabs .nav-link.active{ color:#0b2a4a; background:linear-gradient(135deg, var(--pblue), #e8f3ff); border-color:#d7e8ff }

  .card{ border:1px solid var(--border); border-radius:16px; box-shadow:var(--shadow) }
  .form-label{ color:var(--muted); font-weight:600 }
  .form-control,.form-select{ border:1px solid var(--border); border-radius:14px; padding:.9rem .95rem;
    transition:border-color .2s, box-shadow .2s, transform .1s }
  .form-control:focus,.form-select:focus{ border-color:#bfdcff; box-shadow:0 0 0 .25rem rgba(90,169,255,.18); transform:translateY(-1px) }
  .input-group-text{ border:1px solid var(--border); background:#f8fbff }

  /* Firma */
  .sig-wrap{ border:1px dashed #d5e4f8; border-radius:14px; padding:12px; background:#fff }
  .sig-canvas{ width:100%; height:190px; display:block; background:#fff; border-radius:10px; touch-action:none; }

  /* Dropzone */
  .dropzone{ border:2px dashed #d5e4f8; border-radius:14px; padding:18px; text-align:center; background:#fbfdff; cursor:pointer }
  .dropzone:hover{ background:#f4faff; border-color:#bdd8ff }
  .file-pill{ display:inline-flex; gap:.5rem; align-items:center; padding:.35rem .6rem; background:#ecfdf5; color:#065f46;
    border:1px solid #a7f3d0; border-radius:999px; margin:.25rem; font-size:.83rem }

  /* Botones pastel */
  .btn-pastel-blue{
    color:#0b2a4a; background:var(--pblue); border:1px solid #b6d8ff; border-radius:14px; font-weight:800;
    box-shadow:0 10px 22px rgba(90,169,255,.25); transition:transform .12s, box-shadow .2s, filter .2s;
  }
  .btn-pastel-blue:hover{ filter:brightness(1.03); box-shadow:0 12px 26px rgba(90,169,255,.32) }
  .btn-pastel-green{
    color:#053824; background:var(--pgreen); border:1px solid #9ff0c9; border-radius:14px; font-weight:800;
    box-shadow:0 10px 22px rgba(34,197,94,.22); transition:transform .12s, box-shadow .2s, filter .2s;
  }
  .btn-pastel-green:hover{ filter:brightness(1.03); box-shadow:0 12px 26px rgba(34,197,94,.3) }
  .btn-outline-modern{ border-radius:12px; }
  .btn[disabled]{ opacity:.75; cursor:not-allowed }

  /* Animaciones */
  .fade-slide{ opacity:0; transform:translateY(8px); animation:fadeSlide .5s ease forwards }
  @keyframes fadeSlide{ to{ opacity:1; transform:none } }

  /* QR panel y éxito */
  #qrPanel .card{ border:1px dashed #bfead1 }
  #qrStatus.badge{ font-size:.85rem }
  #qrSuccess{ text-align:center; }
  .check-wrap{ width:140px; height:140px; margin:4px auto 8px; position:relative }
  .check-circle{ fill:none; stroke:#34d399; stroke-width:8; stroke-linecap:round; opacity:.9;
    stroke-dasharray:440; stroke-dashoffset:440; animation:drawCircle 1s ease forwards }
  @keyframes drawCircle{ to{ stroke-dashoffset:0 } }
  .check-mark{ fill:none; stroke:#34d399; stroke-width:8; stroke-linecap:round; stroke-linejoin:round;
    stroke-dasharray:100; stroke-dashoffset:100; animation:drawCheck .7s .6s ease forwards }
  @keyframes drawCheck{ to{ stroke-dashoffset:0 } }

  /* Micro hover */
  .lift:hover{ transform:translateY(-2px) }
</style>

<div class="container page-wrap" style="margin-top:110px;">
  <div class="hero fade-slide mt-2 mb-3 d-flex align-items-center justify-content-between flex-wrap">
    <div class="d-flex align-items-center gap-3">
      <div class="rounded-circle d-inline-flex align-items-center justify-content-center bg-white border"
           style="width:42px;height:42px;border-color:#d7e8ff!important">
        <i class="bi bi-cash-coin" style="font-size:1.15rem;color:#1f7ae6"></i>
      </div>
      <div>
        <h1 class="h4 mb-0">Nueva operación</h1>
        <div class="small text-muted">Registra entradas, entregas y devoluciones sin salir de esta pantalla.</div>
      </div>
    </div>
    <span class="badge ms-auto mt-2 mt-md-0 bg-light text-dark border">UI pastel • mobile first</span>
  </div>

  {{-- Tabs --}}
  <ul class="nav nav-tabs fade-slide" id="trxTabs" role="tablist">
    <li class="nav-item" role="presentation">
      <button class="nav-link active" id="tab-alloc" data-bs-toggle="tab" data-bs-target="#pane-alloc" type="button" role="tab">
        <i class="bi bi-arrow-down-circle me-1"></i> Entrada (jefa → yo)
      </button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="tab-disb" data-bs-toggle="tab" data-bs-target="#pane-disb" type="button" role="tab">
        <i class="bi bi-arrow-up-right-circle me-1"></i> Entrega (yo → usuario)
      </button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="tab-return" data-bs-toggle="tab" data-bs-target="#pane-return" type="button" role="tab">
        <i class="bi bi-arrow-repeat me-1"></i> Devolución
      </button>
    </li>
  </ul>

  <div class="tab-content mt-3" id="trxTabContent">
    {{-- =================== TAB 1 =================== --}}
    <div class="tab-pane fade show active" id="pane-alloc" role="tabpanel" tabindex="0">
      <form id="formAlloc" class="card fade-slide border-0">
        @csrf
        <div class="card-body">
          <div class="row g-3">
            <div class="col-12 col-md-4">
              <label class="form-label">Encargado (tú)</label>
              <select name="manager_id" class="form-select">
                @foreach($managers as $m)
                  <option value="{{ $m->id }}" @selected(auth()->id()==$m->id)>{{ $m->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-12 col-md-4">
              <label class="form-label">Jefa que entrega</label>
              <select name="counterparty_id" class="form-select">
                @foreach($managers as $m)
                  <option value="{{ $m->id }}">{{ $m->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-12 col-md-4">
              <label class="form-label">Fecha y hora</label>
              <input type="datetime-local" name="performed_at" class="form-control" value="{{ $now }}">
            </div>
            <div class="col-12 col-md-4">
              <label class="form-label">Monto (MXN)</label>
              <div class="input-group">
                <span class="input-group-text">MXN</span>
                <input type="number" step="0.01" min="0.01" name="amount" class="form-control" required>
              </div>
            </div>
            <div class="col-12 col-md-8">
              <label class="form-label">Concepto</label>
              <input type="text" name="purpose" maxlength="255" class="form-control" placeholder="Caja inicial, etc.">
            </div>

            <div class="col-12">
              <label class="form-label">Mi firma</label>
              <div class="sig-wrap">
                <canvas id="allocMgrCanvas" class="sig-canvas"></canvas>
                <div class="d-flex justify-content-between align-items-center mt-2">
                  <small class="text-muted"><i class="bi bi-pencil"></i> Firma con el dedo o mouse</small>
                  <button class="btn btn-sm btn-outline-modern btn-outline-secondary lift" type="button" data-clear="#allocMgrCanvas">
                    <i class="bi bi-eraser"></i> Limpiar
                  </button>
                </div>
                <input type="hidden" name="manager_signature" id="allocMgrSig">
              </div>
            </div>
          </div>
        </div>
        <div class="d-flex justify-content-end gap-2 p-3">
          <a href="{{ route('transactions.index', [], false) }}" class="btn btn-outline-modern btn-outline-secondary lift">
            <i class="bi bi-arrow-left-short"></i> Regresar
          </a>
          <button type="button" class="btn btn-pastel-green lift" id="btnAlloc" data-loading="false">
            <span class="btn-text"><i class="bi bi-check2-circle me-1"></i> Guardar entrada</span>
            <span class="btn-spinner d-none spinner-border spinner-border-sm"></span>
          </button>
        </div>
      </form>
    </div>

    {{-- =================== TAB 2 =================== --}}
    <div class="tab-pane fade" id="pane-disb" role="tabpanel" tabindex="0">
      <div class="card fade-slide border-0">
        <div class="card-body">
          <div class="alert alert-primary border-0 bg-opacity-25" style="background:#eaf4ff">
            <i class="bi bi-lightning-charge-fill me-2"></i>
            Elige <strong>Directo</strong> (firma del usuario + tu NIP) o <strong>con QR</strong> (firma desde el celular).
          </div>

          <div class="row g-3">
            <div class="col-12 col-md-4">
              <label class="form-label">Encargado (admin)</label>
              <select name="manager_id" id="disb_manager_id" class="form-select">
                @foreach($managers as $m)
                  <option value="{{ $m->id }}" @selected(auth()->id()==$m->id)>{{ $m->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-12 col-md-4">
              <label class="form-label">Usuario que recibe</label>
              <select name="counterparty_id" id="disb_counterparty_id" class="form-select">
                @foreach($people as $u)
                  <option value="{{ $u->id }}">{{ $u->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-12 col-md-4">
              <label class="form-label">Fecha y hora</label>
              <input type="datetime-local" id="disb_performed_at" class="form-control" value="{{ $now }}">
            </div>
            <div class="col-12 col-md-4">
              <label class="form-label">Monto (MXN)</label>
              <div class="input-group">
                <span class="input-group-text">MXN</span>
                <input type="number" step="0.01" min="0.01" id="disb_amount" class="form-control">
              </div>
            </div>

            <div class="col-12">
              <div class="btn-group" role="group">
                <input type="radio" class="btn-check" name="disbMode" id="modeDirect" autocomplete="off" checked>
                <label class="btn btn-outline-modern btn-outline-primary lift" for="modeDirect"><i class="bi bi-person-check me-1"></i> Directo</label>
                <input type="radio" class="btn-check" name="disbMode" id="modeQr" autocomplete="off">
                <label class="btn btn-outline-modern btn-outline-primary lift" for="modeQr"><i class="bi bi-qr-code me-1"></i> Con QR</label>
              </div>
            </div>

            {{-- DIRECTO --}}
            <div id="disbDirectBox" class="col-12">
              <div class="row g-3">
                <div class="col-12 col-md-8">
                  <label class="form-label">Concepto</label>
                  <input type="text" id="disb_purpose" class="form-control" placeholder="préstamo / gasto / compra…">
                </div>
                <div class="col-12 col-md-4">
                  <label class="form-label">Tu NIP</label>
                  <input type="password" id="disb_nip" class="form-control" placeholder="NIP 4–8 dígitos" inputmode="numeric" maxlength="8">
                </div>

                <div class="col-12">
                  <label class="form-label">Firma del usuario</label>
                  <div class="sig-wrap">
                    <canvas id="disbUserCanvas" class="sig-canvas"></canvas>
                    <div class="d-flex justify-content-between align-items-center mt-2">
                      <small class="text-muted">Firma del usuario</small>
                      <button class="btn btn-sm btn-outline-modern btn-outline-secondary lift" type="button" data-clear="#disbUserCanvas">
                        <i class="bi bi-eraser"></i> Limpiar
                      </button>
                    </div>
                    <input type="hidden" id="disb_user_sig">
                  </div>
                </div>

                <div class="col-12 text-end">
                  <button class="btn btn-pastel-green lift" id="btnDisbDirect" type="button" data-loading="false">
                    <span class="btn-text"><i class="bi bi-send-check me-1"></i> Guardar entrega</span>
                    <span class="btn-spinner d-none spinner-border spinner-border-sm"></span>
                  </button>
                </div>
              </div>
            </div>

            {{-- CON QR --}}
            <div id="disbQrBox" class="col-12 d-none">
              <div class="row g-3">
                <div class="col-12 col-md-4">
                  <label class="form-label">Tu NIP</label>
                  <input type="password" id="qr_nip" class="form-control" placeholder="NIP 4–8 dígitos" inputmode="numeric" maxlength="8">
                </div>
                <div class="col-12">
                  <button class="btn btn-pastel-blue lift" id="btnStartQr" type="button" data-loading="false">
                    <span class="btn-text"><i class="bi bi-qr-code me-1"></i> Generar QR</span>
                    <span class="btn-spinner d-none spinner-border spinner-border-sm"></span>
                  </button>
                </div>

                {{-- QR visible al iniciar --}}
                <div class="col-12 d-none" id="qrPanel">
                  <div class="card">
                    <div class="card-body d-flex flex-column align-items-center">
                      <div id="qrcode" class="mb-2"></div>
                      <div class="text-center">
                        <div class="small text-muted">Pide al usuario escanear, escribir el motivo y firmar.</div>
                        <div class="mt-2">
                          <span class="badge bg-info d-inline-flex align-items-center gap-1" id="qrStatus">
                            <span class="spinner-border spinner-border-sm"></span> Esperando…
                          </span>
                        </div>
                        <div class="mt-2">
                          <button class="btn btn-sm btn-outline-modern btn-outline-secondary lift" id="btnCopyLink" type="button">
                            <i class="bi bi-link-45deg"></i> Copiar link
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                {{-- Vista de éxito (aparece al autorizar) --}}
                <div class="col-12 d-none" id="qrSuccess">
                  <div class="card border-0" style="background:#f1fff7">
                    <div class="card-body">
                      <div class="check-wrap">
                        <svg viewBox="0 0 200 200" width="140" height="140">
                          <circle class="check-circle" cx="100" cy="100" r="68"></circle>
                          <path class="check-mark" d="M70 105 L95 130 L135 85"></path>
                        </svg>
                      </div>
                      <h5 class="mb-1">Autorizado por el usuario</h5>
                      <div class="text-muted">¡Listo! La firma fue recibida correctamente.</div>
                    </div>
                  </div>
                </div>

                <div class="col-12">
                  <small class="text-muted">Al confirmarse la firma, el QR desaparecerá y verás la confirmación.</small>
                </div>
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>

    {{-- =================== TAB 3 =================== --}}
    <div class="tab-pane fade" id="pane-return" role="tabpanel" tabindex="0">
      <form id="formReturn" class="card fade-slide border-0" enctype="multipart/form-data">
        @csrf
        <div class="card-body">
          <div class="row g-3">
            <div class="col-12 col-md-4">
              <label class="form-label">Encargado (admin)</label>
              <select name="manager_id" class="form-select">
                @foreach($managers as $m)
                  <option value="{{ $m->id }}" @selected(auth()->id()==$m->id)>{{ $m->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-12 col-md-4">
              <label class="form-label">Usuario que devuelve</label>
              <select name="counterparty_id" class="form-select">
                @foreach($people as $u)
                  <option value="{{ $u->id }}">{{ $u->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-12 col-md-4">
              <label class="form-label">Fecha y hora</label>
              <input type="datetime-local" name="performed_at" class="form-control" value="{{ $now }}">
            </div>
            <div class="col-12 col-md-4">
              <label class="form-label">Monto devuelto (MXN)</label>
              <div class="input-group">
                <span class="input-group-text">MXN</span>
                <input type="number" step="0.01" min="0.01" name="amount" class="form-control" required>
              </div>
            </div>
            <div class="col-12 col-md-8">
              <label class="form-label">¿Qué compraron? (concepto)</label>
              <input type="text" name="purpose" class="form-control" maxlength="255" required>
            </div>

            <div class="col-12">
              <label class="form-label">Evidencias (JPG/PNG/PDF)</label>
              <label class="dropzone w-100" id="retDrop">
                <input type="file" id="retEvidence" name="evidence[]" multiple accept="image/*,application/pdf" class="d-none">
                <div class="text-muted"><i class="bi bi-cloud-arrow-up me-1"></i> Arrastra o toca para seleccionar archivos</div>
              </label>
              <div id="retFileList" class="mt-2"></div>
            </div>

            <div class="col-12 col-lg-6">
              <label class="form-label">Firma del usuario</label>
              <div class="sig-wrap">
                <canvas id="retUserCanvas" class="sig-canvas"></canvas>
                <div class="d-flex justify-content-between align-items-center mt-2">
                  <small class="text-muted">Firma del usuario</small>
                  <button class="btn btn-sm btn-outline-modern btn-outline-secondary lift" type="button" data-clear="#retUserCanvas">
                    <i class="bi bi-eraser"></i> Limpiar
                  </button>
                </div>
                <input type="hidden" name="counterparty_signature" id="retUserSig">
              </div>
            </div>

            <div class="col-12 col-lg-6">
              <label class="form-label">Mi firma</label>
              <div class="sig-wrap">
                <canvas id="retMgrCanvas" class="sig-canvas"></canvas>
                <div class="d-flex justify-content-between align-items-center mt-2">
                  <small class="text-muted">Tu firma</small>
                  <button class="btn btn-sm btn-outline-modern btn-outline-secondary lift" type="button" data-clear="#retMgrCanvas">
                    <i class="bi bi-eraser"></i> Limpiar
                  </button>
                </div>
                <input type="hidden" name="manager_signature" id="retMgrSig">
              </div>
            </div>

          </div>
        </div>
        <div class="d-flex justify-content-end gap-2 p-3">
          <button type="button" class="btn btn-pastel-blue lift" id="btnReturn" data-loading="false">
            <span class="btn-text"><i class="bi bi-arrow-repeat me-1"></i> Guardar devolución</span>
            <span class="btn-spinner d-none spinner-border spinner-border-sm"></span>
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- Toasts --}}
<div class="toast-container position-fixed top-0 end-0 p-3">
  <div id="toastOK" class="toast align-items-center text-bg-success border-0" role="alert">
    <div class="d-flex">
      <div class="toast-body"><i class="bi bi-check-circle me-1"></i> Operación exitosa.</div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
    </div>
  </div>
  <div id="toastERR" class="toast align-items-center text-bg-danger border-0" role="alert">
    <div class="d-flex">
      <div class="toast-body"><i class="bi bi-exclamation-octagon me-1"></i> <span id="toastERRMsg">Error</span></div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.2.0/dist/signature_pad.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>

<script>
$(function(){
  // --- Toast helpers
  const toastOK  = new bootstrap.Toast('#toastOK',{delay:2200});
  const toastERR = new bootstrap.Toast('#toastERR',{delay:3200});
  const ok  = (m)=>{ $('#toastOK .toast-body').html('<i class="bi bi-check-circle me-1"></i> '+m); toastOK.show(); }
  const err = (m)=>{ $('#toastERRMsg').text(m); toastERR.show(); }

  // --- Button loading
  function setLoading($btn,on=true){
    $btn.prop('disabled',on);
    $btn.find('.btn-text').toggleClass('d-none',on);
    $btn.find('.btn-spinner').toggleClass('d-none',!on);
  }

  // --- Signature Pads
  const pads = {};
  function fitCanvas(canvas, pad){
    const ratio = Math.max(window.devicePixelRatio||1,1);
    const rect  = canvas.getBoundingClientRect();
    // si el tab está oculto, rect.width puede ser 0. Usa ancho del contenedor como fallback.
    const fallbackW = canvas.parentElement ? canvas.parentElement.clientWidth : 600;
    const w = rect.width>0 ? rect.width : (fallbackW>0?fallbackW:600);
    const h = rect.height>0 ? rect.height : 190;
    canvas.width = w * ratio; canvas.height = h * ratio;
    const ctx = canvas.getContext('2d'); ctx.scale(ratio, ratio);
    pad.clear();
  }
  function initPad(id){
    const c = document.getElementById(id);
    const p = new SignaturePad(c,{backgroundColor:'#fff', penColor:'#0f172a'});
    pads[id]=p; // NO ajustamos aquí; lo hacemos al mostrar el tab
    return p;
  }

  const allocMgrPad = initPad('allocMgrCanvas');
  const disbUserPad = initPad('disbUserCanvas');
  const retUserPad  = initPad('retUserCanvas');
  const retMgrPad   = initPad('retMgrCanvas');

  // Ajuste inicial para los canvas visibles al principio (solo pestaña 1)
  fitCanvas(document.getElementById('allocMgrCanvas'), allocMgrPad);

  // Reajustar SIEMPRE que cambie de pestaña (esto arregla el “no puedo firmar” en tabs ocultos)
  document.querySelectorAll('button[data-bs-toggle="tab"]').forEach(btn=>{
    btn.addEventListener('shown.bs.tab', (e)=>{
      const target = document.querySelector(e.target.getAttribute('data-bs-target'));
      target.querySelectorAll('.sig-canvas').forEach(cv=>{
        const pad = pads[cv.id]; if(pad) fitCanvas(cv, pad);
      });
    });
  });

  // Reajustar al cambiar entre Directo / QR (cuando vuelve a “Directo” el canvas ya tiene ancho)
  $('#modeDirect').on('change',()=>{
    $('#disbDirectBox').removeClass('d-none'); $('#disbQrBox').addClass('d-none');
    setTimeout(()=>{ const c = document.getElementById('disbUserCanvas'); fitCanvas(c, disbUserPad); }, 50);
  });
  $('#modeQr').on('change',()=>{
    $('#disbDirectBox').addClass('d-none'); $('#disbQrBox').removeClass('d-none');
  });

  // Limpiar firmas
  $(document).on('click','[data-clear]',function(){
    const id = $(this).data('clear').slice(1);
    pads[id] && pads[id].clear();
  });

  const toData = (pad)=> pad.isEmpty() ? null : pad.toDataURL('image/png');
  const fdForm = (sel)=> new FormData(document.querySelector(sel));

  // --- TAB 1: ALLOCATION
  $('#btnAlloc').on('click', function(){
    const $btn=$(this);
    const sig = toData(allocMgrPad); if(!sig) return err('Tu firma es obligatoria.');
    $('#allocMgrSig').val(sig);
    const data = fdForm('#formAlloc');
    setLoading($btn,true);
    $.ajax({
      url:"{{ route('transactions.allocation.store', [], false) }}",
      method:'POST', data, processData:false, contentType:false,
      headers:{'X-CSRF-TOKEN': $('input[name=_token]').val()},
    }).done(r=>{ ok(`Entrada registrada (#${r.id})`); window.location="{{ route('transactions.index', [], false) }}"; })
      .fail(x=> err(x.responseJSON?.message || 'Error al guardar entrada'))
      .always(()=> setLoading($btn,false));
  });

  // --- TAB 2: DISBURSEMENT DIRECT
  $('#btnDisbDirect').on('click', function(){
    const $btn=$(this);
    const amount=parseFloat($('#disb_amount').val()||'0'); if(isNaN(amount)||amount<0.01) return err('Monto inválido.');
    const nip=$('#disb_nip').val(); if(!/^\d{4,8}$/.test(nip)) return err('NIP inválido.');
    const sig=toData(disbUserPad); if(!sig) return err('Firma del usuario obligatoria.');

    const fd=new FormData();
    fd.append('manager_id',$('#disb_manager_id').val());
    fd.append('counterparty_id',$('#disb_counterparty_id').val());
    fd.append('performed_at',$('#disb_performed_at').val());
    fd.append('amount',$('#disb_amount').val());
    fd.append('purpose',$('#disb_purpose').val());
    fd.append('nip',nip);
    fd.append('counterparty_signature',sig);

    setLoading($btn,true);
    $.ajax({
      url:"{{ route('transactions.disbursement.direct', [], false) }}",
      method:'POST', data:fd, processData:false, contentType:false,
      headers:{'X-CSRF-TOKEN': $('input[name=_token]').val()},
    }).done(r=>{ ok(`Entrega guardada (#${r.id})`); window.location="{{ route('transactions.index', [], false) }}"; })
      .fail(x=> err(x.responseJSON?.message || 'No se pudo guardar la entrega'))
      .always(()=> setLoading($btn,false));
  });

  // --- TAB 2: DISBURSEMENT QR
  let pollTimer=null, activeToken=null, lastQrUrl='';
  function stopPolling(){ if(pollTimer){ clearInterval(pollTimer); pollTimer=null; } }

  $('#btnStartQr').on('click', function(){
    const $btn=$(this);
    const amount=parseFloat($('#disb_amount').val()||'0'); if(isNaN(amount)||amount<0.01) return err('Monto inválido.');
    const nip=$('#qr_nip').val(); if(!/^\d{4,8}$/.test(nip)) return err('NIP inválido.');

    const fd=new FormData();
    fd.append('manager_id',$('#disb_manager_id').val());
    fd.append('counterparty_id',$('#disb_counterparty_id').val());
    fd.append('performed_at',$('#disb_performed_at').val());
    fd.append('amount',$('#disb_amount').val());
    fd.append('nip',nip);

    setLoading($btn,true);
    $.ajax({
      url:"{{ route('transactions.disbursement.qr.start', [], false) }}",
      method:'POST', data:fd, processData:false, contentType:false,
      headers:{'X-CSRF-TOKEN': $('input[name=_token]').val()},
    }).done(r=>{
      $('#qrPanel').removeClass('d-none');
      $('#qrSuccess').addClass('d-none');
      $('#qrcode').empty();
      new QRCode(document.getElementById("qrcode"), { text:r.url, width:230, height:230 });
      $('#qrStatus').removeClass('bg-danger bg-success').addClass('bg-info').html('<span class="spinner-border spinner-border-sm me-1"></span> Esperando firma del usuario…');
      activeToken=r.token; lastQrUrl=r.url;
      stopPolling();
      pollTimer=setInterval(()=>{
        $.getJSON("{{ url('', [], false) }}/transactions/qr/status/"+activeToken, s=>{
          if(s.expired){
            $('#qrStatus').removeClass('bg-info bg-success').addClass('bg-danger').text('QR expirado');
            stopPolling();
          } else if(s.acknowledged){
            stopPolling();
            // Oculta QR y muestra animación profesional + toast
            $('#qrPanel').addClass('d-none');
            $('#qrSuccess').removeClass('d-none');
            ok('Autorizado por el usuario ✅');
          }
        });
      }, 2200);
      ok('QR generado');
    }).fail(x=> err(x.responseJSON?.message || 'No se pudo generar el QR'))
      .always(()=> setLoading($btn,false));
  });

  $('#btnCopyLink').on('click', async ()=>{
    try{ await navigator.clipboard.writeText(lastQrUrl); ok('Link copiado'); }
    catch(e){ err('No se pudo copiar el link'); }
  });

  // --- TAB 3: RETURN
  $('#retDrop').on('click', ()=>$('#retEvidence').trigger('click'));
  $('#retEvidence').on('change', function(){
    const $list=$('#retFileList').empty();
    [...this.files].forEach(f=> $list.append(`<span class="file-pill"><i class="bi bi-file-earmark-arrow-up"></i> ${f.name}</span>`));
  });

  $('#btnReturn').on('click', function(){
    const $btn=$(this);
    const m=toData(retMgrPad), u=toData(retUserPad);
    if(!u) return err('Falta firma del usuario.');
    if(!m) return err('Falta tu firma.');
    $('#retMgrSig').val(m); $('#retUserSig').val(u);

    const fd=new FormData(document.getElementById('formReturn'));
    setLoading($btn,true);
    $.ajax({
      url:"{{ route('transactions.return.store', [], false) }}",
      method:'POST', data:fd, processData:false, contentType:false,
      headers:{'X-CSRF-TOKEN': $('input[name=_token]').val()},
    }).done(r=>{ ok(`Devolución guardada (#${r.id})`); window.location="{{ route('transactions.index', [], false) }}"; })
      .fail(x=> err(x.responseJSON?.message || 'Error al guardar la devolución'))
      .always(()=> setLoading($btn,false));
  });

  // Recalcular pads si la ventana cambia tamaño
  window.addEventListener('resize', ()=>{
    Object.entries(pads).forEach(([id,p])=>{
      const c=document.getElementById(id);
      // solo canvases visibles (evita que width 0 rompa el trazo)
      if(c.offsetParent !== null) fitCanvas(c,p);
    });
  });
});
</script>
@endsection
