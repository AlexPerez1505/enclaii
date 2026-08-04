@extends('layouts.app')
@section('title', 'Publicaciones')
@section('titulo', 'Publicaciones')
@section('content')


<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="{{ asset('css/publicaciones.css') }}?v={{ time() }}">

<div id="contenedor-publicaciones">
    @include('partials.publicaciones-list', ['publicaciones' => $publicaciones])
</div>

<script>
    let ultimaActualizacion = "{{ $publicaciones->max('updated_at')?->toISOString() }}";

    function verificarActualizacion() {
        fetch("{{ route('publicaciones.ultimaActualizacion') }}")
            .then(res => res.json())
            .then(data => {
                if (data.ultima_actualizacion !== ultimaActualizacion) {
                    ultimaActualizacion = data.ultima_actualizacion;
                    cargarPublicaciones();
                }
            })
            .catch(err => console.error('Error verificando actualizaciones:', err));
    }

    function cargarPublicaciones() {
        fetch("{{ route('publicaciones.fetch') }}")
            .then(response => response.text())
            .then(html => {
                const contenedor = document.getElementById("contenedor-publicaciones");
                contenedor.innerHTML = html;

                const cards = contenedor.querySelectorAll('.card');
                cards.forEach((card, i) => {
                    card.style.opacity = '0';
                    setTimeout(() => {
                        card.classList.add('visible');
                    }, i * 100); // efecto cascada
                });

                observerizarCards();
            })
            .catch(error => console.error('Error al cargar publicaciones:', error));
    }

    // Animación al hacer scroll
    function observerizarCards() {
        const cards = document.querySelectorAll('.card');
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    observer.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.1
        });

        cards.forEach(card => observer.observe(card));
    }

    document.addEventListener("DOMContentLoaded", () => {
        observerizarCards(); // inicial en el primer render
    });

    // Verificar cada 5 segundos si hay cambios
    setInterval(verificarActualizacion, 5000);
</script>
@endsection
