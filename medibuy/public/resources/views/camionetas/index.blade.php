@extends('layouts.app')

@section('title', 'Automovil')
@section('titulo', 'Automoviles')

@section('content')
<link rel="stylesheet" href="{{ asset('css/camionetas.css') }}?v={{ time() }}">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('js/delete-confirmation.js') }}"></script>

<div class="container py-5">
    <div class="d-flex justify-content-center mb-4">
        <a href="{{ route('camionetas.create') }}" class="btn btn-success btn-custom">+ Agregar</a>
    </div>
    
    <div class="grid">
        @foreach($camionetas as $camioneta)
        <div class="card p-3">
            <div class="card-body text-center">
                <!-- Imagen de la camioneta -->
                <img src="{{ $camioneta->fotos ? Storage::url(json_decode($camioneta->fotos)[0]) : '/images/default-car.jpg' }}" 
                     alt="Foto de {{ $camioneta->marca }} {{ $camioneta->modelo }}" 
                     class="img-fluid rounded mb-3"
                     style="max-height: 200px; object-fit: cover;">

                <h5 class="card-title">{{ $camioneta->marca }} {{ $camioneta->modelo }}</h5>
                <h6 class="card-subtitle text-muted">Año: {{ $camioneta->anio }}</h6>
                <p class="mt-2"><strong>Placa:</strong> {{ $camioneta->placa }}</p>
                <p><strong>VIN:</strong> {{ $camioneta->vin }}</p>
                <p><strong>Color:</strong> {{ $camioneta->color }}</p>
                
                <div class="d-flex justify-content-center gap-2 mt-3 flex-wrap">
                    <a href="{{ route('camionetas.show', $camioneta->id) }}" class="btn btn-info btn-sm btn-custom">Detalles</a>
                    <a href="{{ route('camionetas.edit', $camioneta->id) }}" class="btn btn-primary btn-sm btn-custom">Editar</a>
                    <form action="{{ route('camionetas.destroy', $camioneta->id) }}" method="POST" class="delete-form d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm btn-custom">Eliminar</button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection


<script>
    document.addEventListener("DOMContentLoaded", function () {
        document.querySelectorAll(".delete-form").forEach(form => {
            form.addEventListener("submit", function (event) {
                event.preventDefault();
                const formElement = this;

                Swal.fire({
                    title: '¿Estás seguro?',
                    text: 'Esta acción no se puede deshacer.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Sí, eliminar!',
                    cancelButtonText: 'Cancelar',
                    customClass: {
                        popup: 'border-radius-15',
                        title: 'font-weight-bold text-dark',
                        content: 'font-size-16',
                        confirmButton: 'btn-custom-confirm',
                        cancelButton: 'btn-custom-cancel',
                    },
                    buttonsStyling: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        formElement.submit();
                    }
                });
            });
        });
    });
</script>

