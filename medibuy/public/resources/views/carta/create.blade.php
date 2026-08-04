@extends('layouts.app')

@section('title', 'Cartas de Garantía')
@section('titulo', 'Cartas de Garantía')

@section('content')
<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    body {
        margin: 0;
        min-height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        font-family: 'Inter', sans-serif;
        background: linear-gradient(-45deg, #e0f7ff, #f5fcff, #d0eaff, #ffffff);
        background-size: 400% 400%;
        animation: gradientBG 16s ease infinite;
    }

    @keyframes gradientBG {
        0% {background-position: 0% 50%;}
        50% {background-position: 100% 50%;}
        100% {background-position: 0% 50%;}
    }

    .form-container {
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(10px);
        border-radius: 16px;
        padding: 2.5rem;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
        width: 100%;
        max-width: 400px;
    }

    .form-container h2 {
        text-align: center;
        font-size: 1.5rem;
        margin-bottom: 1.5rem;
        color: #2c3e50;
    }

    label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 500;
        color: #34495e;
    }

    input[type="text"],
    input[type="file"] {
        width: 100%;
        padding: 0.6rem 0.75rem;
        margin-bottom: 1.2rem;
        border-radius: 8px;
        border: 1px solid #ccddee;
        background-color: #ffffff;
        transition: border-color 0.3s ease;
        font-size: 0.95rem;
    }

    input:focus {
        border-color: #90caf9;
        outline: none;
        box-shadow: 0 0 0 3px rgba(144, 202, 249, 0.3);
    }

    button[type="submit"] {
        width: 100%;
        padding: 0.75rem;
        border: none;
        border-radius: 8px;
        background-color: #64b5f6;
        color: white;
        font-weight: 600;
        font-size: 1rem;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    button:hover {
        background-color: #42a5f5;
    }

    .success-message {
        color: #2e7d32;
        font-size: 0.9rem;
        text-align: center;
        margin-bottom: 1rem;
    }
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

<div class="form-container">


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
            e.target.submit(); // Continúa con el envío
        }, 1000); // Simula un pequeño delay UX
    });
</script>
@endsection
