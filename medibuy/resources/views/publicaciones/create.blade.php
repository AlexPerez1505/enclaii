@extends('layouts.app')
@section('title', 'Publicaciones')
@section('titulo', 'Publicaciones')
@section('content')
<link rel="stylesheet" href="{{ asset('css/publicacion.css') }}?v={{ time() }}">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<div class="container py-4">
    <div class="card-form mx-auto" style="max-width: 600px; margin-top:110px;">
 

        <form id="form-publicacion" method="POST" enctype="multipart/form-data" action="{{ route('publicaciones.store') }}">
            @csrf

            <div class="mb-4">
                <label for="titulo" class="form-label">Título</label>
                <input type="text" name="titulo" id="titulo" class="form-control" required>
            </div>

            <div class="mb-4">
                <label for="descripcion" class="form-label">Descripción</label>
                <textarea name="descripcion" id="descripcion" class="form-control" rows="3"></textarea>
            </div>

            <div class="mb-4">
                <label for="archivo" class="form-label">Archivo (imagen, video o documento)</label>
                <input type="file" name="archivo" id="archivo" class="form-control" required>
            </div>

            <button type="submit" class="btn-submit">Subir Publicación</button>
        </form>
    </div>
</div>

</div>

<!-- SweetAlert + Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    document.getElementById('form-publicacion')?.addEventListener('submit', function (e) {
        e.preventDefault();

        const form = e.target;
        const formData = new FormData(form);

        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) throw new Error("Error al publicar");
            return response.json();
        })
        .then(data => {
            form.reset();
            if (typeof cargarPublicaciones === 'function') {
                cargarPublicaciones();
            }

            Swal.fire({
                icon: 'success',
                title: '¡Publicado!',
                text: data.message,
                timer: 2500,
                showConfirmButton: false
            });
        })
        .catch(error => {
            console.error('Error al enviar publicación:', error);

            Swal.fire({
                icon: 'error',
                title: 'Ups...',
                text: 'No se pudo subir la publicación.',
            });
        });
    });
</script>
@endsection
