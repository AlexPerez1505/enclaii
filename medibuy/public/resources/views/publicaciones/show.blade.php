@extends('layouts.app')
@section('title', $publicacion->titulo)
@section('titulo', 'Publicaciones')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="{{ asset('css/publicaciones.css') }}">
<!-- SweetAlert2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.6.15/dist/sweetalert2.min.css" rel="stylesheet">
<style>
    .swal2-popup {
    font-family: 'Arial', sans-serif;
    background-color: #f8f9fa;
    border-radius: 15px;
}
.swal2-title {
    color: #343a40;
}
.swal2-content {
    color: #495057;
}
.btn-custom-confirm {
    background-color: #4CAF50; /* Verde suave */
    color: white;
    border-radius: 10px;
    padding: 12px 25px;
    border: none; /* Eliminar borde */
    margin-right: 10px; /* Separar botones */
    transition: background-color 0.3s ease;
}
.btn-custom-confirm:hover {
    background-color: #45a049; /* Verde un poco más oscuro */
}
/* Estilo para el botón de confirmación en SweetAlert2 */
/* Fuerza estilos personalizados en el botón de confirmación */
.swal2-confirm {
    background-color: #4CAF50 !important;  /* Verde suave */
    color: white !important;
    border-radius: 10px !important;
    padding: 12px 25px !important;
    border: none !important;
    margin-right: 10px !important;
    transition: background-color 0.3s ease !important;
    box-shadow: none !important;
    font-weight: bold !important;
}

.swal2-confirm:hover {
    background-color: #45a049 !important;  /* Verde un poco más oscuro */
}


.swal2-popup {
    font-family: 'Arial', sans-serif;
    font-size: 16px;
}

.swal2-title {
    font-weight: bold;
}

.btn-custom-cancel {
    background-color: #DC3545; /* Gris suave */
    color: white;
    border-radius: 10px;
    padding: 12px 25px;
    border: none; /* Eliminar borde */
    margin-left: 10px; /* Separar botones */
    transition: background-color 0.3s ease;
}
.btn-custom-cancel:hover {
    background-color: #C82333; /* Gris un poco más oscuro */
}
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
    $promedioFormateado = number_format($promedio, 1); // 1 decimal
    $cantidadVotos = $publicacion->valoraciones->count(); // número de personas que votaron
@endphp

<div class="promedio-estrellas">
    <p>Calificación promedio: <strong>{{ $promedioFormateado }}/5</strong> (basado en {{ $cantidadVotos }} {{ Str::plural('voto', $cantidadVotos) }})</p>


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

     

        <div class="detalle-tipo">
            <strong>Tipo:</strong> {{ ucfirst($publicacion->tipo) }}
        </div>
    </div>
</div>
@endsection


@section('scripts')
<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.6.15/dist/sweetalert2.min.js"></script>
<script>
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
            body: JSON.stringify({
                publicacion_id: publicacionId,
                valor: valor
            })
        })
        .then(res => res.json())
        .then(data => {
            console.log(data); // Verifica qué devuelve el servidor

            if (data.success) {
                // Muestra el SweetAlert
                Swal.fire({
                    icon: 'success',
                    title: '¡Gracias por tu valoración!',
                    text: 'Tu opinión es muy importante para nosotros.',
                    confirmButtonText: 'Listo',
                    background: '#f0f8ff',
                    color: '#333',
                    showCloseButton: true,
                    customClass: {
                        confirmButton: 'swal2-confirm'  // Aplica la clase personalizada al botón
                    }
                }).then(() => {
                    // Recarga la página al cerrar el modal
                    location.reload();
                });

                // Actualiza las estrellas y el promedio
                const promedio = data.promedio;
                const estrellasContenedor = document.querySelector('.estrellas-promedio');
                estrellasContenedor.innerHTML = '';

                for (let i = 1; i <= 5; i++) {
                    if (i <= Math.floor(promedio)) {
                        estrellasContenedor.innerHTML += '<i class="fas fa-star estrella llena"></i>';
                    } else if (i - promedio < 1) {
                        estrellasContenedor.innerHTML += '<i class="fas fa-star-half-alt estrella llena"></i>';
                    } else {
                        estrellasContenedor.innerHTML += '<i class="far fa-star estrella vacia"></i>';
                    }
                }

                document.querySelector('.promedio-estrellas p').innerHTML = `Calificación promedio: <strong>${parseFloat(promedio).toFixed(1)}/5</strong> (basado en ${data.total} ${data.total === 1 ? 'voto' : 'votos'})`;
            }
        })
        .catch(error => {
            console.error("Error al enviar la valoración:", error);
        });
    });
});
</script>



@endsection
