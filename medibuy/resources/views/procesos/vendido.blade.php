@extends('layouts.app')
@section('title', 'Inventario')
@section('titulo', 'Vendido')
@section('content')

<link rel="stylesheet" href="{{ asset('css/asistencias.css') }}?v={{ time() }}">

<style>
    body{
        background: #F5FAFF;
    }
    .btn-secondary, .btn-primary {
    display: flex;
    align-items: center;   /* Centra verticalmente */
    justify-content: center; /* Centra horizontalmente */
    border: none;
    border-radius: 8px;
    font-weight: bold;
    width: 130px;
    height: 45px;
    transition: background-color 0.3s;
}

.btn-secondary {
    background-color: #dc3545;
}

.btn-secondary:hover {
    background-color: #c82333;
    transform: translateY(-2px);
}

.btn-primary {
    background-color: #1E6BB8;
}

.btn-primary:hover {
    background-color: #0056b3;
    transform: translateY(-2px);
}

.btn-secondary img, .btn-primary img {
    margin-right: 5px;  /* Espacio entre la imagen y el texto */
}

</style>
<body>
    
<div class="formulario-contenedor">
    <h5 class="card-title">Equipo ID: {{ $id }}</h5>
    <p class="card-text">Aquí puedes gestionar el proceso de Vendido del equipo con ID {{ $id }}.</p>
    <form id="formulario-stock" action="{{ route('procesos.guardar', $id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="tipo_proceso" value="vendido">
    <div class="form-group">
    <label for="descripcion" class="label_nomina">Descripción</label>
    <textarea class="form-control select" name="descripcion_proceso"placeholder="Escribe aquí una descripción detallada de lo que se realizo." style="height: 130px;" required></textarea>
</div>
<div class="form-group">
        <label  class="label_nomina">Fotos de evidencia
            <div class="info-icon">?
                <div class="info-tooltip text-center">Máximo 3 (una por campo).</div>
            </div>
            </div>
        </label>

        <!-- Input para Imagen 1 -->
        <div class="d-flex align-items-center input-containerr">
            <label for="evidencia1" class="file-input-label d-flex align-items-center">
                <div class="icon-containerr">
                    <img src="{{ asset('images/adjunto-archivo.png') }}" alt="Icono de pin" class="icon-pin">
                </div>
                <span>Seleccione archivo 1</span>
            </label>
            <input type="file" id="evidencia1" name="evidencia1" class="file-input" onchange="updatePreview(this, 'preview1')" accept="image/*">
            <div id="file-input-text-images1" class="file-input-text">Sin selección</div>
        </div>

        <!-- Input para Imagen 2 -->
        <div class="d-flex align-items-center input-containerr mt-2">
            <label for="evidencia2" class="file-input-label d-flex align-items-center">
                <div class="icon-containerr">
                    <img src="{{ asset('images/adjunto-archivo.png') }}" alt="Icono de pin" class="icon-pin">
                </div>
                <span>Seleccione archivo 2</span>
            </label>
            <input type="file" id="evidencia2" name="evidencia2" class="file-input" onchange="updatePreview(this, 'preview2')" accept="image/*">
            <div id="file-input-text-images2" class="file-input-text">Sin selección</div>
        </div>

        <!-- Input para Imagen 3 -->
        <div class="d-flex align-items-center input-containerr mt-2">
            <label for="evidencia3" class="file-input-label d-flex align-items-center">
                <div class="icon-containerr">
                    <img src="{{ asset('images/adjunto-archivo.png') }}" alt="Icono de pin" class="icon-pin">
                </div>
                <span>Seleccione archivo 3</span>
            </label>
            <input type="file" id="evidencia3" name="evidencia3" class="file-input" onchange="updatePreview(this, 'preview3')" accept="image/*">
            <div id="file-input-text-images3" class="file-input-text">Sin selección</div>
        </div>

 <!-- Contenedor para previsualizar las imágenes -->
<div id="preview-containerr" class="preview-containerr mt-3">
    <h5 class="preview-title">Previsualización de las Imágenes:</h5>
    <div class="image-preview-containerr">
        <div id="preview1" class="image-previeww"></div>
        <div id="preview2" class="image-previeww"></div>
        <div id="preview3" class="image-previeww"></div>
    </div>
</div>

   <br>
<div class="form-group">
   
        <label for="video" class="label_nomina">Video de Evidencia
            <div class="info-icon">?
                <div class="info-tooltip text-center">Máximo 1.</div>
            </div>
            </div>
        </label>
        <div class="d-flex align-items-center input-containerr">
            <label for="video" class="file-input-label d-flex align-items-center">
                <div class="icon-containerr">
                    <img src="{{ asset('images/adjunto-archivo.png') }}" alt="Icono de video" class="icon-pin">
                </div>
                <span>Seleccione archivos</span>
            </label>
            <input type="file" id="video" name="video" class="file-input"
                onchange="updateVideoPreview(this, 'file-input-text-video')" 
                accept="video/mp4,video/avi,video/mpeg,video/webm,video/quicktime">
            <div id="file-input-text-video" class="file-input-text">Sin selección</div>
        </div>
        <!-- Área de previsualización -->
        <div id="video-preview-container" class="preview-containerrr mt-3" style="display: none;">
            <h5 class="preview-title">Previsualización del Video:</h5>
            <div id="video-preview" class="video-preview"></div>
            <div id="video-message" class="upload-message"></div>
        </div>


<br>


<div style="display: flex;">
  <a href="/inventario" class="btn btn-secondary btn-custom" style="margin-right: 10px;">
    <img src="/images/regresa.png" alt="Icono Volver" style="width: 20px; height: 20px;">
    Volver
  </a>
  <button type="submit" class="btn btn-primary btn-custom" style="margin-left: 10px;">
    <img src="/images/like.png" alt="Icono Finalizar" style="width: 20px; height: 20px; ">
    Finalizar
  </button>
</div>
    
</form>
</body>
<script>
function updatePreview(input, previewId) {
    const file = input.files[0];
    const previewContainer = document.getElementById(previewId);
    const fileInputText = document.getElementById('file-input-text-images' + previewId.charAt(previewId.length - 1)); // Obtener el texto de estado

    // Limpiar contenido anterior
    previewContainer.innerHTML = '';

    if (file && file.type.startsWith('image/')) {
        const imgContainer = document.createElement('div');
        imgContainer.classList.add('image-preview-container');
        imgContainer.style.position = 'relative';
        imgContainer.style.maxWidth = '100%';  // Asegura que no se expanda más allá de su contenedor

        const img = document.createElement('img');
        const fileURL = URL.createObjectURL(file);
        img.src = fileURL;
        img.style.maxWidth = '100px';  // Limita el tamaño de la imagen
        img.style.margin = '5px';
        img.onload = () => URL.revokeObjectURL(fileURL);

        const removeBtn = document.createElement('button');
        removeBtn.innerHTML = '&times;';
        removeBtn.className = 'remove-btn';
        removeBtn.style.position = 'absolute';
        removeBtn.style.top = '0';
        removeBtn.style.right = '0';
        removeBtn.style.background = 'red';
        removeBtn.style.color = 'white';
        removeBtn.style.border = 'none';
        removeBtn.style.borderRadius = '50%';
        removeBtn.style.cursor = 'pointer';

        // Eliminar imagen
        removeBtn.onclick = function () {
            input.value = ''; // Elimina el archivo seleccionado
            previewContainer.innerHTML = ''; // Limpia la previsualización
            fileInputText.textContent = 'Sin selección'; // Vuelve a mostrar "Sin selección"
        };

        imgContainer.appendChild(img);
        imgContainer.appendChild(removeBtn);
        previewContainer.appendChild(imgContainer);

        // Cambia el texto a "Imagen seleccionada"
        fileInputText.textContent = 'Imagen seleccionada';
    } else {
        // Si no es una imagen válida, muestra "Sin selección"
        fileInputText.textContent = 'Sin selección';
    }
}
</script>




<script>
function updateVideoPreview(input) {
    const file = input.files[0]; // Solo se permite un archivo
    const videoPreview = document.getElementById('video-preview');
    const message = document.getElementById('video-message');
    const previewContainer = document.getElementById('video-preview-container');
    const fileInputText = document.getElementById('file-input-text-video'); // Elemento para mostrar el nombre del archivo

    // Reiniciar vista previa
    videoPreview.innerHTML = '';
    message.textContent = '';

    // Validar si se seleccionó un archivo
    if (file) {
        // Comprobar si el archivo es un video
        if (file.type.startsWith('video/')) {
            // Mostrar mensaje minimalista en lugar del nombre del archivo
            fileInputText.textContent = 'Video seleccionado';

            // Crear el elemento <video>
            const video = document.createElement('video');
            video.controls = true; // Habilitar controles del video
            video.width = 300; // Ancho del video
            video.src = URL.createObjectURL(file); // Crear URL para el video

            // Liberar memoria una vez que se haya cargado el video completamente
            video.onloadeddata = () => {
                video.onended = () => {
                    URL.revokeObjectURL(video.src);
                };
            };

            // Crear botón de eliminar
            const removeBtn = document.createElement('button');
            removeBtn.className = 'remove-btn';
            removeBtn.innerHTML = '&times;'; // Tache (X)
            removeBtn.onclick = function () {
                // Eliminar el video y el botón de eliminar
                videoPreview.innerHTML = '';
                input.value = ''; // Limpiar el input
                previewContainer.style.display = 'none'; // Ocultar el contenedor de previsualización
                fileInputText.textContent = 'Sin selección';
            };

            // Crear contenedor para el video y el botón de eliminar
            const videoContainer = document.createElement('div');
            videoContainer.className = 'video-container';
            videoContainer.style.position = 'relative'; // Asegura que el botón se posicione sobre el video
            videoContainer.appendChild(video);
            videoContainer.appendChild(removeBtn);

            // Agregar el video y el botón de eliminar al contenedor de previsualización
            videoPreview.appendChild(videoContainer);

            message.textContent = 'Se ha seleccionado un video.';
            message.className = 'upload-message';

            // Mostrar el contenedor de previsualización
            previewContainer.style.display = 'block';
        } else {
            // Mostrar mensaje de error si el archivo no es un video
            message.textContent = 'El archivo seleccionado no es un video válido.';
            message.className = 'upload-message error';
            previewContainer.style.display = 'none';
            input.value = ''; // Limpiar el input
            fileInputText.textContent = 'Sin selección';
        }
    } else {
        // Restablecer si no hay archivo seleccionado
        previewContainer.style.display = 'none';
        fileInputText.textContent = 'Sin selección';
    }
}

</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('formulario-stock');

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        Swal.fire({
            title: 'Subiendo evidencia...',
            html: `
                <div style="margin-top: 10px;">
                    <div id="progress-bar-container" style="width: 100%; background: #eee; border-radius: 4px;">
                        <div id="progress-bar" style="width: 0%; height: 20px; background: #3085d6; border-radius: 4px;"></div>
                    </div>
                    <div style="margin-top: 8px; font-weight: bold;">Progreso: <span id="upload-progress">0%</span></div>
                </div>
            `,
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();

                const xhr = new XMLHttpRequest();
                const formData = new FormData(form);

                xhr.open('POST', form.getAttribute('action'), true);
                xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');

                xhr.upload.addEventListener('progress', (event) => {
                    if (event.lengthComputable) {
                        const percent = Math.round((event.loaded / event.total) * 100);
                        document.getElementById('upload-progress').textContent = percent + '%';
                        document.getElementById('progress-bar').style.width = percent + '%';
                    }
                });

                xhr.onload = () => {
                    if (xhr.status === 200) {
                        Swal.fire({
                            title: '¡Éxito!',
                            text: 'Proceso guardado correctamente.',
                            icon: 'success',
                            confirmButtonText: 'Aceptar'
                        }).then(() => {
                            window.location.href = "{{ route('inventario') }}";
                        });
                    } else {
                        let response = {};
                        try {
                            response = JSON.parse(xhr.responseText);
                        } catch (e) {
                            response.message = 'Error inesperado.';
                        }

                        // Extraer errores en forma de lista
                        let errorHtml = '';
                        if (response.message) {
                            errorHtml = `<li>${response.message}</li>`;
                        }

                        Swal.fire({
                            title: 'Error',
                            html: `<ul style="text-align:left;">${errorHtml}</ul>`,
                            icon: 'error'
                        });
                    }
                };

                xhr.onerror = () => {
                    Swal.fire({
                        title: 'Error',
                        text: 'No se pudo conectar con el servidor.',
                        icon: 'error'
                    });
                };

                xhr.send(formData);
            }
        });
    });
});

</script>


@endsection
