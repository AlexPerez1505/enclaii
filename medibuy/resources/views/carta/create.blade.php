@extends('layouts.app')

@section('title', 'Cartas de Garantía')
@section('titulo', 'Cartas de Garantía')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    @keyframes gradientBG {
        0% {background-position: 0% 50%;}
        50% {background-position: 100% 50%;}
        100% {background-position: 0% 50%;}
    }

    .page-wrapper {
        min-height: calc(100vh - 56px);
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 30px;
        background: linear-gradient(-45deg, #e0f7ff, #f5fcff, #d0eaff, #ffffff);
        background-size: 400% 400%;
        animation: gradientBG 16s ease infinite;
    }

    .form-container {
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(10px);
        border-radius: 16px;
        padding: 2.5rem;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
        width: 100%;
        max-width: 500px;
    }

    /* Estilos para los botones de navegación */
    .nav-buttons {
        display: flex;
        justify-content: space-between;
        margin-bottom: 1.5rem;
    }

    .btn-nav {
        background: #e9ecef;
        border: none;
        padding: 8px 15px;
        border-radius: 8px;
        color: #495057;
        transition: all 0.3s;
        text-decoration: none;
        font-size: 1.2rem;
    }

    .btn-nav:hover {
        background: #dee2e6;
        color: #212529;
    }

    .form-container h2 {
        text-align: center;
        font-size: 1.5rem;
        margin-bottom: 1.5rem;
        color: #2c3e50;
    }

    label { display: block; margin-bottom: 0.5rem; font-weight: 500; color: #34495e; }

    input[type="text"], input[type="file"] {
        width: 100%;
        padding: 0.6rem 0.75rem;
        margin-bottom: 1.2rem;
        border-radius: 8px;
        border: 1px solid #ccddee;
        background-color: #ffffff;
        transition: border-color 0.3s ease;
    }

    button[type="submit"] {
        width: 100%;
        padding: 0.75rem;
        border: none;
        border-radius: 8px;
        background-color: #64b5f6;
        color: white;
        font-weight: 600;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    button:hover { background-color: #42a5f5; }
    .success-message { color: #2e7d32; font-size: 0.9rem; text-align: center; margin-bottom: 1rem; }
</style>

<div class="page-wrapper">
    <div class="form-container">
        
        <div class="nav-buttons">
            <a href="javascript:history.back()" class="btn-nav" title="Volver atrás">
                <i class="fas fa-arrow-left"></i>
            </a>
            <a href="{{ url('/home') }}" class="btn-nav" title="Ir al inicio">
                <i class="fas fa-home"></i>
            </a>
        </div>

        @if(session('success'))
            <p class="success-message">{{ session('success') }}</p>
        @endif

        <form id="uploadForm" action="{{ route('carta.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <label for="nombre">Nombre del documento</label>
            <input type="text" name="nombre" id="nombre" required>

            <label for="archivo">Archivo PDF</label>
            <input type="file" name="archivo" id="archivo" accept="application/pdf" required>

            <button type="submit">Subir</button>
        </form>
    </div>
</div>

<script>
    document.getElementById('uploadForm').addEventListener('submit', function (e) {
        e.preventDefault();

        Swal.fire({
            title: 'Subiendo archivo...',
            html: 'Por favor espera un momento',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        setTimeout(() => {
            e.target.submit();
        }, 1000);
    });
</script>
@endsection