@extends('layouts.app')
@section('title', $publicacion->titulo)
@section('titulo', 'Publicaciones')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="{{ asset('css/publicaciones.css') }}">
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.6.15/dist/sweetalert2.min.css" rel="stylesheet">
<style>
    .swal2-popup {
        font-family: 'Arial', sans-serif;
        background-color: #f8f9fa;
        border-radius: 15px;
    }
    .swal2-title { color: #343a40; }
    .swal2-content { color: #495057; }

    .swal2-confirm {
        background-color: #4CAF50 !important;
        color: white !important;
        border-radius: 10px !important;
        padding: 12px 25px !important;
        border: none !important;
        margin-right: 10px !important;
        transition: background-color 0.3s ease !important;
        box-shadow: none !important;
        font-weight: bold !important;
    }
    .swal2-confirm:hover { background-color: #45a049 !important; }

    /* ── Botones de acción ── */
    .acciones-publicacion {
        display: flex;
        gap: 12px;
        margin-top: 18px;
        flex-wrap: wrap;
    }

    .btn-accion {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 22px;
        border-radius: 10px;
        font-weight: 700;
        font-size: 14px;
        cursor: pointer;
        border: none;
        transition: background .2s ease, transform .1s ease;
        text-decoration: none;
    }
    .btn-accion:active { transform: scale(0.97); }

    .btn-subir {
        background-color: #3b82f6;
        color: #ffffff;
    }
    .btn-subir:hover { background-color: #2563eb; }

    .btn-eliminar {
        background-color: #ef4444;
        color: #ffffff;
    }
    .btn-eliminar:hover { background-color: #dc2626; }

    /* Modal subir archivo */
    .modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,.5);
        z-index: 9999;
        align-items: center;
        justify-content: center;
    }
    .modal-overlay.activo { display: flex; }

    .modal-caja {
        background: #ffffff;
        border-radius: 16px;
        padding: 32px 28px;
        width: 100%;
        max-width: 440px;
        box-shadow: 0 8px 32px rgba(0,0,0,.2);
        display: flex;
        flex-direction: column;
        gap: 16px;
    }
    .modal-caja h2 {
        margin: 0;
        font-size: 20px;
        color: #1e293b;
    }
    .modal-caja input[type="file"] {
        border: 2px dashed #94a3b8;
        border-radius: 10px;
        padding: 14px;
        width: 100%;
        cursor: pointer;
        font-size: 14px;
        box-sizing: border-box;
    }
    .modal-botones {
        display: flex;
        gap: 10px;
        justify-content: flex-end;
    }
    .btn-cancelar-modal {
        background: #e2e8f0;
        color: #475569;
        border: none;
        border-radius: 10px;
        padding: 10px 20px;
        font-weight: 600;
        cursor: pointer;
        transition: background .2s;
    }
    .btn-cancelar-modal:hover { background: #cbd5e1; }

    .btn-confirmar-subir {
        background: #3b82f6;
        color: white;
        border: none;
        border-radius: 10px;
        padding: 10px 20px;
        font-weight: 700;
        cursor: pointer;
        transition: background .2s;
    }
    .btn-confirmar-subir:hover { background: #2563eb; }
</style>


<div class="detalle-publicacion" style="margin-top:110px;">
    <div class="detalle-media">
        @if(Str::endsWith($publicacion->archivo, ['jpg', 'jpeg', 'png', 'webp']))
            <img src="{{ Storage::url($publicacion->archivo) }}" alt="{{ $publicacion->titulo }}" class="media-ajustada">
        @elseif(Str::endsWith($publicacion->archivo, ['mp4', 'mov']))
            <video controls class="media-ajustada">
                <source src="{{ Storage::url($publicacion->archivo) }}" type="video/mp4">
                Tu navegador no soporta la reproducción de video.
            </video>
        @else
            <div class="no-preview">Archivo no visualizable</div>
        @endif
    </div>

    <div class="detalle-info">
        <h1>{{ $publicacion->titulo }}</h1>
        <p class="time">{{ $publicacion->created_at->diffForHumans() }}</p>
        <p class="descripcion">{!! nl2br(e($publicacion->descripcion)) !!}</p>

        @php
            $promedioFormateado = number_format($promedio, 1);
            $cantidadVotos = $publicacion->valoraciones->count();
        @endphp

        <div class="promedio-estrellas">
            <p>Calificación promedio: <strong>{{ $promedioFormateado }}/5</strong>
               (basado en {{ $cantidadVotos }} {{ Str::plural('voto', $cantidadVotos) }})</p>

            <div class="estrellas-promedio">
                @for ($i = 1; $i <= 5; $i++)
                    @if ($i <= floor($promedio))
                        <i class="fas fa-star estrella llena"></i>
                    @elseif ($i - $promedio < 1)
                        <i class="fas fa-star-half-alt estrella llena"></i>
                    @else
                        <i class="far fa-star estrella vacia"></i>
                    @endif
                @endfor
            </div>
        </div>

        <div class="calificacion">
            <span>¿Te fue útil?</span>
            <div class="estrellas">
                <input type="radio" name="rating" id="star5" value="5"><label for="star5">&#9733;</label>
                <input type="radio" name="rating" id="star4" value="4"><label for="star4">&#9733;</label>
                <input type="radio" name="rating" id="star3" value="3"><label for="star3">&#9733;</label>
                <input type="radio" name="rating" id="star2" value="2"><label for="star2">&#9733;</label>
                <input type="radio" name="rating" id="star1" value="1"><label for="star1">&#9733;</label>
            </div>
        </div>

        <!-- ── BOTONES DE ACCIÓN ── -->
        <div class="acciones-publicacion">
            <button class="btn-accion btn-subir" onclick="abrirModalSubir()">
                <i class="fas fa-upload"></i> Subir Archivo
            </button>
            <button class="btn-accion btn-eliminar" onclick="confirmarEliminar()">
                <i class="fas fa-trash-alt"></i> Eliminar Publicación
            </button>
        </div>

        <div class="detalle-tipo">
            <strong>Tipo:</strong> {{ ucfirst($publicacion->tipo) }}
        </div>
    </div>
</div>


<!-- ── MODAL: SUBIR ARCHIVO ── -->
<div class="modal-overlay" id="modalSubir">
    <div class="modal-caja">
        <h2><i class="fas fa-upload" style="color:#3b82f6;"></i> Subir nuevo archivo</h2>
        <p style="margin:0; color:#64748b; font-size:14px;">
            Selecciona la imagen o video que reemplazará el archivo actual.
        </p>
        <form id="formSubirArchivo"
              action="{{ route('publicaciones.subirArchivo', $publicacion->id) }}"
              method="POST"
              enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <input type="file" name="archivo" id="inputArchivo"
                   accept="image/jpeg,image/png,image/webp,video/mp4,video/quicktime"
                   required>
            <div class="modal-botones" style="margin-top:8px;">
                <button type="button" class="btn-cancelar-modal" onclick="cerrarModalSubir()">
                    Cancelar
                </button>
                <button type="submit" class="btn-confirmar-subir">
                    <i class="fas fa-cloud-upload-alt"></i> Subir
                </button>
            </div>
        </form>
    </div>
</div>
@endsection


@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.6.15/dist/sweetalert2.min.js"></script>
<script>
/* ── Valoración por estrellas ── */
document.querySelectorAll('.estrellas input').forEach(star => {
    star.addEventListener('change', function () {
        const valor = this.value;
        const publicacionId = {{ $publicacion->id }};

        fetch("{{ route('valorar') }}", {
            method: "POST",
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
            body: JSON.stringify({ publicacion_id: publicacionId, valor: valor })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Gracias por tu valoración!',
                    text: 'Tu opinión es muy importante para nosotros.',
                    confirmButtonText: 'Listo',
                    background: '#f0f8ff',
                    color: '#333',
                    showCloseButton: true,
                    customClass: { confirmButton: 'swal2-confirm' }
                }).then(() => location.reload());

                const promedio = data.promedio;
                const estrellasContenedor = document.querySelector('.estrellas-promedio');
                estrellasContenedor.innerHTML = '';
                for (let i = 1; i <= 5; i++) {
                    if (i <= Math.floor(promedio))
                        estrellasContenedor.innerHTML += '<i class="fas fa-star estrella llena"></i>';
                    else if (i - promedio < 1)
                        estrellasContenedor.innerHTML += '<i class="fas fa-star-half-alt estrella llena"></i>';
                    else
                        estrellasContenedor.innerHTML += '<i class="far fa-star estrella vacia"></i>';
                }
                document.querySelector('.promedio-estrellas p').innerHTML =
                    `Calificación promedio: <strong>${parseFloat(promedio).toFixed(1)}/5</strong>
                     (basado en ${data.total} ${data.total === 1 ? 'voto' : 'votos'})`;
            }
        })
        .catch(error => console.error("Error al enviar la valoración:", error));
    });
});

/* ── Modal subir archivo ── */
function abrirModalSubir() {
    document.getElementById('modalSubir').classList.add('activo');
}
function cerrarModalSubir() {
    document.getElementById('modalSubir').classList.remove('activo');
    document.getElementById('inputArchivo').value = '';
}
// Cerrar modal al hacer clic fuera
document.getElementById('modalSubir').addEventListener('click', function (e) {
    if (e.target === this) cerrarModalSubir();
});

/* ── Eliminar publicación ── */
function confirmarEliminar() {
    Swal.fire({
        icon: 'warning',
        title: '¿Eliminar publicación?',
        text: 'Esta acción no se puede deshacer.',
        showCancelButton: true,
        confirmButtonText: '<i class="fas fa-trash-alt"></i> Sí, eliminar',
        cancelButtonText: 'Cancelar',
        background: '#fff8f8',
        color: '#333',
        customClass: {
            confirmButton: 'swal2-confirm',
            cancelButton: 'btn-custom-cancel'
        },
        buttonsStyling: false
    }).then(result => {
        if (result.isConfirmed) {
            fetch("{{ route('publicaciones.destroy', $publicacion->id) }}", {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Eliminada!',
                        text: 'La publicación fue eliminada correctamente.',
                        confirmButtonText: 'Aceptar',
                        customClass: { confirmButton: 'swal2-confirm' }
                    }).then(() => {
                        window.location.href = "{{ route('publicaciones.index') }}";
                    });
                } else {
                    Swal.fire('Error', data.message ?? 'No se pudo eliminar.', 'error');
                }
            })
            .catch(() => Swal.fire('Error', 'Ocurrió un problema al eliminar.', 'error'));
        }
    });
}
</script>
@endsection
