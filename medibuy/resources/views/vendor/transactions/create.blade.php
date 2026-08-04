{{-- resources/views/transactions/create.blade.php --}}
@extends('layouts.app')

@section('content')
<style>
  :root{
    --bg: #f7f8fb;
    --panel: #ffffff;
    --text: #0f172a;
    --muted: #667085;
    --primary: #0d6efd;
    --border: #e7eaf0;
    --success:#16a34a;
    --danger:#dc2626;
  }
  body{ background: var(--bg); }
  .page-wrap{ max-width: 960px; }

  .card{
    background: var(--panel);
    border: 1px solid var(--border);
    border-radius: 14px;
    box-shadow: 0 6px 22px rgba(16,24,40,.06);
    color: var(--text);
  }
  .card .card-header{ background:#fff; border-bottom:1px solid var(--border); color:var(--muted); font-weight:600; }

  /* Inputs */
  .form-label{ color: var(--muted); font-size:.9rem; }
  .form-control, .form-select{
    border:1px solid var(--border);
    border-radius: 10px;
    transition: border-color .2s, box-shadow .2s, transform .15s;
  }
  .form-control:focus, .form-select:focus{
    border-color:#c9d2e0; box-shadow:0 0 0 4px rgba(13,110,253,.12); transform: translateY(-1px);
  }

  .input-hint{ color:#98a2b3; font-size:.85rem; }

  /* Signatures */
  .sig-wrap{ background:#fff; border:1px dashed #d9dee8; border-radius:12px; padding:10px; }
  .sig-canvas{ width:100%; height:180px; display:block; background:#fff; border-radius:10px; }
  .sig-actions .btn{ border-radius: 999px; }

  /* Evidence drop area */
  .dropzone{
    background:#fff; border:2px dashed #cfd6e3; border-radius:12px; padding:16px; text-align:center; cursor:pointer;
    transition: background .2s, border-color .2s;
  }
  .dropzone:hover{ background:#f4f6fb; border-color:#b9c4da; }
  .dropzone input{ display:none; }
  .file-pill{
    display:inline-flex; align-items:center; gap:.4rem; background:#eef2f8; border:1px solid #dbe3f2; color:#334155;
    padding:.35rem .6rem; border-radius:999px; font-size:.85rem; margin:.25rem;
  }
  .file-pill .remove{ color:#64748b; cursor:pointer; }
  .required:after{ content:" *"; color: var(--danger); }

  /* Sticky action bar (mobile friendly) */
  .sticky-actions{
    position: sticky; bottom: 0; z-index: 20; background: #fff;
    border-top:1px solid var(--border); padding:12px; display:flex; gap:12px; align-items:center; justify-content:space-between;
    border-bottom-left-radius:14px; border-bottom-right-radius:14px;
  }

  /* Toasts */
  .toast-container{ z-index:1080; }
</style>

<div class="container page-wrap">
  <div class="d-flex align-items-center justify-content-between mb-3">
    <h1 class="mb-0">Nueva operación</h1>
    <a class="btn btn-outline-secondary" href="{{ route('transactions.index') }}">Regresar</a>
  </div>

  <form id="trxForm" class="card" enctype="multipart/form-data" novalidate>
    @csrf
    <div class="card-body">
      <div class="row g-3">
        <div class="col-12 col-md-4">
          <label class="form-label required">Tipo</label>
          <select name="type" class="form-select" id="type">
            <option value="allocation">Entrada de jefas</option>
            <option value="disbursement">Entrega a usuario</option>
            <option value="return">Regreso de cambio</option>
          </select>
          <div class="input-hint mt-1" id="typeHint">Selecciona el flujo.</div>
        </div>

        <div class="col-12 col-md-4">
          <label class="form-label required">Encargado</label>
          <select name="manager_id" class="form-select">
            @foreach($managers as $m)
              <option value="{{ $m->id }}">{{ $m->name }}</option>
            @endforeach
          </select>
        </div>

        <div class="col-12 col-md-4">
          <label class="form-label required">Contraparte</label>
          <select name="counterparty_id" class="form-select">
            @foreach($people as $u)
              <option value="{{ $u->id }}">{{ $u->name }}</option>
            @endforeach
          </select>
        </div>

        <div class="col-12 col-md-4">
          <label class="form-label required">Monto</label>
          <div class="input-group">
            <span class="input-group-text">MXN</span>
            <input type="number" step="0.01" name="amount" class="form-control" placeholder="0.00" required min="0.01">
          </div>
          <div class="input-hint mt-1">Mínimo $0.01</div>
        </div>

        <div class="col-12 col-md-8">
          <label class="form-label">Concepto</label>
          <input type="text" maxlength="255" name="purpose" class="form-control" placeholder="préstamo / gasto / compra...">
        </div>
      </div>

      <hr class="my-4">

      <div class="row g-3">
        <div class="col-12 col-lg-6">
          <label class="form-label required">Firma Encargado</label>
          <div class="sig-wrap">
            <canvas id="sigManager" class="sig-canvas"></canvas>
            <div class="d-flex justify-content-between align-items-center mt-2 sig-actions">
              <small class="text-muted">Firma con el dedo o mouse.</small>
              <button type="button" id="clearMgr" class="btn btn-sm btn-outline-secondary">Limpiar</button>
            </div>
            <input type="hidden" name="manager_signature" id="manager_signature">
          </div>
        </div>

        <div class="col-12 col-lg-6">
          <label class="form-label required">Firma Contraparte</label>
          <div class="sig-wrap">
            <canvas id="sigCounter" class="sig-canvas"></canvas>
            <div class="d-flex justify-content-between align-items-center mt-2 sig-actions">
              <small class="text-muted">Firma con el dedo o mouse.</small>
              <button type="button" id="clearCpt" class="btn btn-sm btn-outline-secondary">Limpiar</button>
            </div>
            <input type="hidden" name="counterparty_signature" id="counterparty_signature">
          </div>
        </div>
      </div>

      <div id="evidenceWrap" class="mt-4 d-none">
        <label class="form-label required">Evidencias (obligatorio en devolución)</label>
        <label class="dropzone w-100" id="dropArea">
          <input id="evidenceInput" type="file" name="evidence[]" multiple accept="image/*,application/pdf">
          <div class="d-flex flex-column align-items-center">
            <div class="mb-2">
              <svg width="30" height="30" viewBox="0 0 24 24" fill="none">
                <path d="M7 15l5-5 5 5" stroke="#6b7280" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M12 10v9" stroke="#6b7280" stroke-width="2" stroke-linecap="round"/>
                <path d="M20 21H4" stroke="#6b7280" stroke-width="2" stroke-linecap="round"/>
              </svg>
            </div>
            <div class="text-muted">Toca para seleccionar o arrastra aquí</div>
            <small class="text-muted">JPG/PNG/PDF (máx 5 MB c/u)</small>
          </div>
        </label>
        <div id="fileList" class="mt-2"></div>
      </div>
    </div>

    <div class="sticky-actions">
      <div class="small text-muted d-none d-md-block">
        Al guardar se generará un PDF con firmas.
      </div>
      <div class="d-flex align-items-center gap-2">
        <button type="button" id="submitBtn" class="btn btn-primary px-4">
          Guardar operación
        </button>
      </div>
    </div>
  </form>
</div>

{{-- Modal NIP --}}
<div class="modal fade" id="nipModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-sm">
    <div class="modal-content">
      <div class="modal-header"><h5 class="modal-title">Autorizar con NIP</h5></div>
      <div class="modal-body">
        <input type="password" class="form-control text-center fs-5" id="nipInput"
               placeholder="••••" inputmode="numeric" pattern="\d{4,8}" maxlength="8" autocomplete="one-time-code">
        <div class="row g-2 text-center mt-3">
          @for($i=1; $i<=9; $i++)
            <div class="col-4"><button class="btn btn-light w-100 py-2 num" data-n="{{$i}}">{{$i}}</button></div>
          @endfor
          <div class="col-4"><button class="btn btn-light w-100 py-2 num" data-n="0">0</button></div>
          <div class="col-4"><button class="btn btn-outline-secondary w-100 py-2" id="nipBack">⌫</button></div>
          <div class="col-4"><button class="btn btn-outline-secondary w-100 py-2" id="nipClear">Limpiar</button></div>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button id="nipOk" class="btn btn-primary">Autorizar</button>
      </div>
    </div>
  </div>
</div>

{{-- Toasts --}}
<div class="toast-container position-fixed top-0 end-0 p-3">
  <div id="toast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="d-flex">
      <div class="toast-body" id="toastMsg">Guardado correctamente.</div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
  </div>
</div>

{{-- Dependencias (sin @push) --}}
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.2.0/dist/signature_pad.umd.min.js"></script>
<script>
$(function(){
  // ---------- SignaturePad con escala retina ----------
  const mgrCanvas = document.getElementById('sigManager');
  const cptCanvas = document.getElementById('sigCounter');
  const sigMgr = new SignaturePad(mgrCanvas, {backgroundColor:'#fff', penColor:'#0f172a'});
  const sigCpt = new SignaturePad(cptCanvas, {backgroundColor:'#fff', penColor:'#0f172a'});

  function fitCanvas(canvas, pad){
    const ratio = Math.max(window.devicePixelRatio || 1, 1);
    const rect = canvas.getBoundingClientRect();
    canvas.width  = rect.width  * ratio;
    canvas.height = rect.height * ratio;
    const ctx = canvas.getContext('2d');
    ctx.scale(ratio, ratio);
    pad.clear(); // se limpia pero mantiene tamaño nítido
  }
  function resizeAll(){ fitCanvas(mgrCanvas, sigMgr); fitCanvas(cptCanvas, sigCpt); }
  resizeAll();
  window.addEventListener('resize', resizeAll);

  $('#clearMgr').on('click', ()=>sigMgr.clear());
  $('#clearCpt').on('click', ()=>sigCpt.clear());

  // ---------- Evidencias ----------
  const $wrap = $('#evidenceWrap');
  const $drop = $('#dropArea');
  const $list = $('#fileList');
  const $input = $('#evidenceInput');

  function updateTypeUI(){
    const t = $('#type').val();
    if(t === 'return'){
      $wrap.removeClass('d-none');
      $('#typeHint').text('Devolución: evidencia obligatoria.');
    }else if(t === 'disbursement'){
      $wrap.addClass('d-none');
      $('#typeHint').text('Entrega: solicitará NIP para autorizar.');
    }else{
      $wrap.addClass('d-none');
      $('#typeHint').text('Entrada de jefas.');
    }
  }
  $('#type').on('change', updateTypeUI);
  updateTypeUI();

  // Drop/Select
  $drop.on('click', ()=> $input.trigger('click'));
  $drop.on('dragover', function(e){ e.preventDefault(); $(this).addClass('border-primary'); });
  $drop.on('dragleave drop', function(e){ e.preventDefault(); $(this).removeClass('border-primary'); });
  $drop.on('drop', function(e){
    e.preventDefault();
    $input.prop('files', e.originalEvent.dataTransfer.files);
    renderFiles();
  });
  $input.on('change', renderFiles);

  function renderFiles(){
    $list.empty();
    const files = $input[0].files;
    if(!files || !files.length){ return; }
    [...files].forEach((f, idx)=>{
      const pill = $(`<span class="file-pill">${f.name} <span class="remove" data-i="${idx}">×</span></span>`);
      $list.append(pill);
    });
  }
  // eliminar archivo de la lista (reconstruyendo FileList)
  $list.on('click', '.remove', function(){
    const idx = Number($(this).data('i'));
    const dt = new DataTransfer();
    const files = $input[0].files;
    [...files].forEach((f, i)=>{ if(i!==idx) dt.items.add(f); });
    $input[0].files = dt.files;
    renderFiles();
  });

  // ---------- Modal NIP ----------
  const nipModal = new bootstrap.Modal('#nipModal');
  $('#nipClear').on('click', function(e){ e.preventDefault(); $('#nipInput').val(''); });
  $('#nipBack').on('click', function(e){
    e.preventDefault();
    const v = $('#nipInput').val(); $('#nipInput').val(v.slice(0, -1));
  });
  $('.num').on('click', function(e){
    e.preventDefault();
    const v = $('#nipInput').val();
    if(v.length < 8) $('#nipInput').val(v + $(this).data('n'));
  });

  // ---------- Submit ----------
  let saving = false;
  $('#submitBtn').on('click', function(){
    if(saving) return;

    // Validaciones mínimas
    const amount = parseFloat($('input[name=amount]').val() || '0');
    if(isNaN(amount) || amount < 0.01) return toast('Monto inválido', true);
    if (sigMgr.isEmpty() || sigCpt.isEmpty()) return toast('Las firmas son obligatorias.', true);

    // Inyectar dataURL
    $('#manager_signature').val(sigMgr.toDataURL('image/png'));
    $('#counterparty_signature').val(sigCpt.toDataURL('image/png'));

    if ($('#type').val()==='disbursement') {
      nipModal.show();
    } else {
      submitForm();
    }
  });

  $('#nipOk').on('click', function(){
    const nip = $('#nipInput').val();
    if (!/^\d{4,8}$/.test(nip)) return toast('NIP inválido (4-8 dígitos)', true);
    $('<input>').attr({type:'hidden', name:'nip', value:nip}).appendTo('#trxForm');
    nipModal.hide();
    submitForm();
  });

  function submitForm(){
    const fd = new FormData(document.getElementById('trxForm'));
    saving = true;
    $('#submitBtn').prop('disabled', true).text('Guardando…');

    $.ajax({
      url: "{{ route('transactions.store') }}",
      method: "POST",
      data: fd,
      processData: false,
      contentType: false,
      headers: {'X-CSRF-TOKEN': $('input[name=_token]').val()},
      success: (res)=>{
        toast('Operación guardada. ID: '+res.id, false);
        setTimeout(()=> window.location = "{{ route('transactions.index') }}", 700);
      },
      error: (xhr)=>{
        const msg = xhr.responseJSON?.message ?? 'Revisa los campos';
        toast('Error: ' + msg, true);
      },
      complete: ()=>{
        saving = false;
        $('#submitBtn').prop('disabled', false).text('Guardar operación');
      }
    });
  }

  // ---------- Toast helper ----------
  function toast(message, isError=false){
    const t = document.getElementById('toast');
    const body = document.getElementById('toastMsg');
    body.textContent = message;
    t.classList.toggle('text-bg-success', !isError);
    t.classList.toggle('text-bg-danger', isError);
    (new bootstrap.Toast(t, {delay: 2000})).show();
  }
});
</script>
@endsection
