@extends('layouts.app')
@section('title', 'Movimientos')
@section('titulo', 'Salida')

@section('content')
<link rel="stylesheet" href="{{ asset('css/asistencias.css') }}?v={{ time() }}">

<style>
  :root{
    --mv-ink:#0f172a;
    --mv-muted:#6b7280;
    --mv-soft:#f9fafb;
    --mv-border:#e5e7eb;
    --mv-accent:#2563eb;
    --mv-radius:16px;
  }

  body{
    background:#F5FAFF;
  }

  .mv-shell{
    min-height:calc(100vh - 120px);
    display:flex;
    justify-content:center;
    align-items:flex-start;
    padding:1.5rem 1rem 3rem;
  }

  .mv-form{
    width:100%;
    max-width:1120px;
  }

  /* 🔹 Layout doble contenedor */
  .mv-layout{
    display:flex;
    flex-direction:column;
    gap:1rem;
  }

  @media (min-width:992px){
    .mv-layout{
      flex-direction:row;
      align-items:flex-start;
    }
    .mv-col-left{
      flex:1.1;
    }
    .mv-col-right{
      flex:1;
    }
  }

  .mv-card{
    background:#ffffff;
    border-radius:20px;
    border:1px solid rgba(148,163,184,0.35);
    padding:1.6rem 1.4rem 1.7rem;
  }

  @media (min-width:768px){
    .mv-card{
      padding:1.9rem 1.9rem 2rem;
    }
  }

  .mv-header{
    display:flex;
    flex-direction:column;
    gap:.4rem;
    margin-bottom:1.3rem;
  }

  .mv-header-top{
    display:flex;
    flex-wrap:wrap;
    justify-content:space-between;
    gap:.75rem;
    align-items:center;
  }

  .mv-tag{
    font-size:.75rem;
    padding:.18rem .6rem;
    border-radius:999px;
    border:1px solid rgba(148,163,184,0.6);
    color:var(--mv-muted);
    text-transform:uppercase;
    letter-spacing:.08em;
    background:#fff;
  }

  .mv-equipo-id{
    font-size:.85rem;
    color:var(--mv-muted);
  }

  .mv-title{
    font-size:1.25rem;
    font-weight:600;
    color:var(--mv-ink);
  }

  .mv-subtitle{
    font-size:.9rem;
    color:var(--mv-muted);
    max-width:38rem;
  }

  .mv-section{
    margin-bottom:1.3rem;
  }

  .mv-section-header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    gap:.75rem;
    margin-bottom:.6rem;
  }

  .mv-section-title{
    font-size:.95rem;
    font-weight:600;
    color:var(--mv-ink);
  }

  .mv-section-hint{
    font-size:.8rem;
    color:var(--mv-muted);
  }

  .label_nomina{
    font-size:.9rem;
    font-weight:500;
    color:var(--mv-ink);
    display:flex;
    align-items:center;
    gap:.35rem;
  }

  /* 🔹 Checklist */
  .defecto-container{
    display:grid;
    grid-template-columns:repeat(auto-fit, minmax(230px, 1fr));
    gap:.75rem;
  }

  .checkbox-card{
    display:flex;
    align-items:flex-start;
    gap:.55rem;
    padding:.75rem .8rem;
    background-color:#f9fafb;
    border:1px solid var(--mv-border);
    border-radius:var(--mv-radius);
    cursor:pointer;
    transition:background .15s ease, border-color .15s ease;
  }

  .checkbox-card:hover{
    background:#eef2ff;
    border-color:#c7d2fe;
  }

  .checkbox-card input[type="checkbox"]{
    width:1rem;
    height:1rem;
    margin-top:.15rem;
    flex-shrink:0;
    accent-color:var(--mv-accent);
  }

  .checkbox-card span{
    font-size:.88rem;
    color:#374151;
    line-height:1.35;
  }

  /* 🔹 Descripción */
  .mv-textarea{
    border-radius:14px;
    border:1px solid var(--mv-border);
    padding:.7rem .85rem;
    font-size:.9rem;
    resize:vertical;
    min-height:110px;
    background:#f9fafb;
    transition:border-color .15s ease, box-shadow .15s ease, background .15s ease;
  }

  .mv-textarea:focus{
    outline:none;
    border-color:var(--mv-accent);
    box-shadow:0 0 0 1px rgba(37,99,235,0.18);
    background:#ffffff;
  }

  /* 🔹 Fotos */
  .mv-evidence-grid{
    display:grid;
    grid-template-columns:repeat(auto-fit, minmax(140px, 1fr));
    gap:.8rem;
    margin-top:.5rem;
  }

  .image-container{
    position:relative;
    width:100%;
    aspect-ratio:4/3;
    overflow:hidden;
    border-radius:14px;
    border:1px dashed rgba(148,163,184,0.9);
    background:#f9fafb;
    transition:border-color .15s ease, background .15s ease;
  }

  .image-container:hover{
    border-color:var(--mv-accent);
    background:#eef2ff;
  }

  .image-preview{
    display:flex;
    flex-direction:column;
    align-items:center;
    justify-content:center;
    width:100%;
    height:100%;
    padding:.6rem;
    text-align:center;
    cursor:pointer;
  }

  #preview-icon-1,
  #preview-icon-2,
  #preview-icon-3{
    width:38px;
    height:38px;
    object-fit:contain;
    opacity:.9;
    margin-bottom:.4rem;
  }

  #preview-text-1,
  #preview-text-2,
  #preview-text-3{
    font-size:.78rem;
    color:var(--mv-muted);
  }

  /* 🔹 Video */
  .mv-file-row{
    display:flex;
    align-items:center;
    gap:.7rem;
    flex-wrap:wrap;
  }

  .file-input-label{
    border-radius:999px;
    padding:.5rem 1.1rem;
    background:#ffffff;
    border:1px solid var(--mv-border);
    font-size:.85rem;
    font-weight:500;
    cursor:pointer;
    display:inline-flex;
    align-items:center;
    gap:.45rem;
  }

  .file-input-label:hover{
    border-color:var(--mv-accent);
  }

  .file-input{
    display:none;
  }

  .file-input-text{
    font-size:.8rem;
    color:var(--mv-muted);
  }

  .preview-containerrr{
    border-radius:14px;
    border:1px solid var(--mv-border);
    padding:.7rem .8rem .9rem;
    background:#f9fafb;
  }

  .preview-title{
    font-size:.85rem;
    font-weight:600;
    color:var(--mv-ink);
    margin-bottom:.4rem;
  }

  .video-container{
    position:relative;
    display:inline-block;
    border-radius:12px;
    overflow:hidden;
  }

  .video-container video{
    display:block;
    max-width:100%;
  }

  .remove-btn{
    position:absolute;
    top:.3rem;
    right:.3rem;
    width:22px;
    height:22px;
    border-radius:999px;
    border:none;
    background:#111827;
    opacity:.9;
    color:#fff;
    font-size:.95rem;
    line-height:1;
    display:flex;
    align-items:center;
    justify-content:center;
    cursor:pointer;
  }

  .upload-message{
    margin-top:.35rem;
    font-size:.8rem;
    color:var(--mv-muted);
  }

  .upload-message.error{
    color:#b91c1c;
  }

  /* 🔹 Botones */
  .mv-actions{
    display:flex;
    flex-wrap:wrap;
    gap:.7rem;
    margin-top:.8rem;
  }

  .btn-custom{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    gap:.4rem;
    border-radius:999px;
    padding:.55rem 1.3rem;
    font-size:.88rem;
    font-weight:500;
    min-width:130px;
    border:1px solid transparent;
    background:#ffffff;
    color:var(--mv-ink);
  }

  .btn-custom img{
    width:18px;
    height:18px;
  }

  .btn-secondary.btn-custom{
    border-color:var(--mv-border);
  }

  .btn-secondary.btn-custom:hover{
    border-color:#9ca3af;
    background:#f9fafb;
  }

  .btn-primary.btn-custom{
    border-color:var(--mv-accent);
    color:#fff;
    background:var(--mv-accent);
  }

  .btn-primary.btn-custom:hover{
    background:#1d4ed8;
    border-color:#1d4ed8;
  }

  @media (max-width:640px){
    .mv-card{
      border-radius:18px;
      padding:1.4rem 1.05rem 1.6rem;
    }

    .mv-actions{
      flex-direction:column-reverse;
    }

    .btn-custom{
      width:100%;
    }
  }
</style>

<div class="mv-shell">
  <form action="{{ route('movimientos.guardar', ['id' => $servicio->id]) }}" method="POST" enctype="multipart/form-data" class="mv-form">
    @csrf
    <input type="hidden" name="tipo_movimiento" value="salida_mantenimiento">

    <div class="mv-layout" style="margin-top:90px;">
      {{-- 🔹 CONTENEDOR IZQUIERDO: SOLO CHECKLIST --}}
      <div class="mv-col-left">
        <div class="mv-card">
          <div class="mv-header">
            <div class="mv-header-top">
              <span class="mv-tag">Movimiento · Salida</span>
              <span class="mv-equipo-id">Equipo ID: <strong>{{ $id }}</strong></span>
            </div>
            <h2 class="mv-title">Checklist antes de salida</h2>
            <p class="mv-subtitle">
              Selecciona los puntos revisados antes de enviar el equipo a mantenimiento externo.
            </p>
          </div>

          <div class="mv-section">
            <div class="defecto-container">
              <label class="checkbox-card" for="check-1">
                <input type="checkbox" id="check-1" name="checklist[]" value="Registro de fallas detectadas">
                <span>Registro de fallas detectadas</span>
              </label>

              <label class="checkbox-card" for="check-2">
                <input type="checkbox" id="check-2" name="checklist[]" value="Accesorios entregados junto con el equipo">
                <span>Accesorios entregados junto con el equipo</span>
              </label>

              <label class="checkbox-card" for="check-3">
                <input type="checkbox" id="check-3" name="checklist[]" value="Etiqueta con número de serie visible">
                <span>Etiqueta con número de serie visible</span>
              </label>

              <label class="checkbox-card" for="check-4">
                <input type="checkbox" id="check-4" name="checklist[]" value="Prueba de imagen">
                <span>Prueba de imagen</span>
              </label>

              <label class="checkbox-card" for="check-5">
                <input type="checkbox" id="check-5" name="checklist[]" value="Prueba de fugas">
                <span>Prueba de fugas</span>
              </label>

              <label class="checkbox-card" for="check-6">
                <input type="checkbox" id="check-6" name="checklist[]" value="Prueba de angulación">
                <span>Prueba de angulación</span>
              </label>

              <label class="checkbox-card" for="check-7">
                <input type="checkbox" id="check-7" name="checklist[]" value="Prueba de botones">
                <span>Prueba de botones</span>
              </label>

              <label class="checkbox-card" for="check-8">
                <input type="checkbox" id="check-8" name="checklist[]" value="Inspección de tubo de inserción universal">
                <span>Inspección de tubo de inserción universal</span>
              </label>

              <label class="checkbox-card" for="check-9">
                <input type="checkbox" id="check-9" name="checklist[]" value="Inspección de lente de objetivo">
                <span>Inspección de lente de objetivo</span>
              </label>

              <label class="checkbox-card" for="check-10">
                <input type="checkbox" id="check-10" name="checklist[]" value="Inspección de rubber">
                <span>Inspección de rubber</span>
              </label>

              <label class="checkbox-card" for="check-11">
                <input type="checkbox" id="check-11" name="checklist[]" value="Revisión de conectores y cables">
                <span>Revisión de conectores y cables</span>
              </label>

              <label class="checkbox-card" for="check-12">
                <input type="checkbox" id="check-12" name="checklist[]" value="Verificación de encendido/apagado">
                <span>Verificación de encendido/apagado</span>
              </label>

              <label class="checkbox-card" for="check-13">
                <input type="checkbox" id="check-13" name="checklist[]" value="Prueba de luz o fuente de iluminación">
                <span>Prueba de luz o fuente de iluminación</span>
              </label>

              <label class="checkbox-card" for="check-14">
                <input type="checkbox" id="check-14" name="checklist[]" value="Chequeo de integridad de carcasa">
                <span>Chequeo de integridad de carcasa</span>
              </label>

              <label class="checkbox-card" for="check-15">
                <input type="checkbox" id="check-15" name="checklist[]" value="Revisión de canales de trabajo/irrigación">
                <span>Revisión de canales de trabajo / irrigación</span>
              </label>

              <label class="checkbox-card" for="check-16">
                <input type="checkbox" id="check-16" name="checklist[]" value="Chequeo de puerto de salida de video">
                <span>Chequeo de puerto de salida de video</span>
              </label>

              <label class="checkbox-card" for="check-18">
                <input type="checkbox" id="check-18" name="checklist[]" value="Checar batería o fuente de poder">
                <span>Checar batería o fuente de poder</span>
              </label>
            </div>
          </div>
        </div>
      </div>

      {{-- 🔹 CONTENEDOR DERECHO: DESCRIPCIÓN + EVIDENCIAS + VIDEO + BOTONES --}}
      <div class="mv-col-right">
        <div class="mv-card">
          {{-- Descripción --}}
          <div class="mv-section">
            <label for="descripcion" class="label_nomina">Descripción del estado del equipo</label>
            <textarea
              id="descripcion"
              class="form-control mv-textarea"
              name="descripcion"
              placeholder="Describe el estado del equipo antes de la salida (daños, observaciones, accesorios, etc.)."
              required
            ></textarea>
          </div>

          {{-- Fotos --}}
          <div class="mv-section">
            <div class="mv-section-header">
              <span class="label_nomina">
                Fotos de evidencia
                <div class="info-icon">?
                  <div class="info-tooltip text-center">Máximo 3 (una por campo).</div>
                </div>
              </span>
            </div>

            <div class="mv-evidence-grid">
              <div class="image-container">
                <label for="image-upload-1" class="image-preview">
                  <img id="preview-icon-1" src="https://cdn-icons-png.flaticon.com/512/1829/1829586.png" alt="Añadir imagen">
                  <span id="preview-text-1">Añadir imagen</span>
                </label>
                <input type="file" id="image-upload-1" name="evidencia1" accept="image/*" hidden>
              </div>

              <div class="image-container">
                <label for="image-upload-2" class="image-preview">
                  <img id="preview-icon-2" src="https://cdn-icons-png.flaticon.com/512/1829/1829586.png" alt="Añadir imagen">
                  <span id="preview-text-2">Añadir imagen</span>
                </label>
                <input type="file" id="image-upload-2" name="evidencia2" accept="image/*" hidden>
              </div>

              <div class="image-container">
                <label for="image-upload-3" class="image-preview">
                  <img id="preview-icon-3" src="https://cdn-icons-png.flaticon.com/512/1829/1829586.png" alt="Añadir imagen">
                  <span id="preview-text-3">Añadir imagen</span>
                </label>
                <input type="file" id="image-upload-3" name="evidencia3" accept="image/*" hidden>
              </div>
            </div>
          </div>

          {{-- Video --}}
          <div class="mv-section">
            <div class="mv-section-header">
              <span class="label_nomina">
                Video de evidencia
                <div class="info-icon">?
                  <div class="info-tooltip text-center">Máximo 1.</div>
                </div>
              </span>
            </div>

            <div class="mv-file-row">
              <label for="video" class="file-input-label">
                <div class="icon-containerr">
                  <img src="{{ asset('images/adjunto-archivo.png') }}" alt="Icono de video" class="icon-pin">
                </div>
                <span>Seleccionar archivo</span>
              </label>

              <input
                type="file"
                id="video"
                name="video"
                class="file-input"
                onchange="updateVideoPreview(this, 'file-input-text-video')"
                accept="video/mp4,video/avi,video/mpeg,video/webm,video/quicktime"
              >

              <div id="file-input-text-video" class="file-input-text">Sin selección</div>
            </div>

            <div id="video-preview-container" class="preview-containerrr mt-3" style="display:none;">
              <h5 class="preview-title">Previsualización del video</h5>
              <div id="video-preview" class="video-preview"></div>
              <div id="video-message" class="upload-message"></div>
            </div>
          </div>

          {{-- Botones --}}
          <div class="mv-actions">
            <a href="/inventario/servicio" class="btn btn-secondary btn-custom">
              <img src="/images/regresa.png" alt="Icono Volver">
              Volver
            </a>

            <button type="submit" class="btn btn-primary btn-custom">
              <img src="/images/like.png" alt="Icono Finalizar">
              Finalizar
            </button>
          </div>
        </div>
      </div>
    </div>
  </form>
</div>
@endsection

@section('scripts')
  {{-- SweetAlert --}}
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script>
    document.addEventListener("DOMContentLoaded", function () {
      @if(session('success'))
        Swal.fire({
          title: 'Guardando',
          text: 'Por favor espera mientras se guarda el proceso.',
          showConfirmButton: false,
          allowOutsideClick: false,
          didOpen: () => {
            Swal.showLoading();
            setTimeout(() => {
              Swal.fire({
                title: 'Proceso guardado',
                text: '{{ session('success') }}',
                icon: 'success',
                confirmButtonText: 'OK'
              }).then(() => {
                window.location.href = "{{ route('inventarioservicio') }}";
              });
            }, 2000);
          }
        });
      @elseif(session('error'))
        Swal.fire({
          title: 'Error',
          text: '{{ session('error') }}',
          icon: 'error',
          confirmButtonText: 'OK'
        }).then(() => {
          window.location.href = "{{ route('inventarioservicio') }}";
        });
      @endif
    });
  </script>

  <script>
    document.getElementById("image-upload-1").addEventListener("change", function(event) {
      previewImage(event, "1");
    });
    document.getElementById("image-upload-2").addEventListener("change", function(event) {
      previewImage(event, "2");
    });
    document.getElementById("image-upload-3").addEventListener("change", function(event) {
      previewImage(event, "3");
    });

    function previewImage(event, index) {
      const file = event.target.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
          document.getElementById("preview-icon-" + index).src = e.target.result;
          document.getElementById("preview-text-" + index).style.display = 'none';
        };
        reader.readAsDataURL(file);
      }
    }
  </script>

  <script>
    function updateVideoPreview(input) {
      const file = input.files[0];
      const videoPreview = document.getElementById('video-preview');
      const message = document.getElementById('video-message');
      const previewContainer = document.getElementById('video-preview-container');
      const fileInputText = document.getElementById('file-input-text-video');

      videoPreview.innerHTML = '';
      message.textContent = '';

      if (file) {
        if (file.type.startsWith('video/')) {
          fileInputText.textContent = 'Video seleccionado';

          const video = document.createElement('video');
          video.controls = true;
          video.width = 320;
          video.src = URL.createObjectURL(file);

          video.onloadeddata = () => {
            video.onended = () => {
              URL.revokeObjectURL(video.src);
            };
          };

          const removeBtn = document.createElement('button');
          removeBtn.className = 'remove-btn';
          removeBtn.innerHTML = '&times;';
          removeBtn.onclick = function () {
            videoPreview.innerHTML = '';
            input.value = '';
            previewContainer.style.display = 'none';
            fileInputText.textContent = 'Sin selección';
          };

          const videoContainer = document.createElement('div');
          videoContainer.className = 'video-container';
          videoContainer.appendChild(video);
          videoContainer.appendChild(removeBtn);

          videoPreview.appendChild(videoContainer);

          message.textContent = 'Se ha seleccionado un video.';
          message.className = 'upload-message';

          previewContainer.style.display = 'block';
        } else {
          message.textContent = 'El archivo seleccionado no es un video válido.';
          message.className = 'upload-message error';
          previewContainer.style.display = 'none';
          input.value = '';
          fileInputText.textContent = 'Sin selección';
        }
      } else {
        previewContainer.style.display = 'none';
        fileInputText.textContent = 'Sin selección';
      }
    }
  </script>
@endsection
