@extends('layouts.app') 

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500&display=swap');

    body {
        background-color: #eef1fb;
        font-family: 'Roboto', sans-serif;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        margin: 0;
        overflow: hidden;
    }

    .upload-card {
        background-color: #fff;
        width: 360px;
        padding: 40px 20px;
        text-align: center;
        border-radius: 15px;
        box-shadow: 0 10px 15px rgba(0,0,0,0.1);
        transition: transform 1s ease, opacity 1s ease;
        z-index: 1;
    }

    .custom-input {
        background-color: #ffe0d9;
        border-radius: 30px;
        border: none;
        padding: 15px;
        width: 100%;
        margin-bottom: 15px;
        font-size: 0.95rem;
        text-align: center;
        color: #555;
        outline: none;
        box-shadow: 0 2px 6px rgba(0,0,0,0.05);
    }

    .upload-btn {
        background: linear-gradient(145deg, #ff8a65, #ff7043);
        color: white;
        border: none;
        padding: 12px 20px;
        border-radius: 12px;
        font-weight: 500;
        cursor: pointer;
        transition: transform 0.3s;
        box-shadow: 0 8px 16px rgba(255,112,67,0.4);
        margin-top: 10px;
    }

    .upload-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(255,112,67,0.5);
    }

    /* Parallax Toast vertical suave */
    .toast-parallax {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        display: none;
        justify-content: center;
        align-items: center;
        background: rgba(255,255,255,0.95);
        z-index: 2;
        animation: fadeIn 1s ease forwards;
    }

    .toast-parallax h3 {
        font-size: 1.5rem;
        color: #555;
        font-weight: 300;
        animation: slideUp 1s ease;
    }

    @keyframes fadeIn {
        from {opacity: 0;}
        to {opacity: 1;}
    }

    @keyframes slideUp {
        from {transform: translateY(50px); opacity: 0;}
        to {transform: translateY(0); opacity: 1;}
    }
    
</style>

<div class="upload-card" id="upload-card">
    <h2 style="color: #8a8f9a; font-weight:300;"> Buscar equipo</h2>
    <form id="search-form" action="{{ route('inventario.buscar.submit') }}" method="GET" autocomplete="off">
        <input type="text" id="scanner-input" name="serie" class="custom-input" placeholder="Escanea o escribe número de serie..." required>
        <button type="submit" class="upload-btn">BUSCAR</button>
    </form>

    <div style="margin-top:25px; color:#bbb; font-weight:300;">O busca por otra referencia:</div>

    <form action="{{ route('inventario.buscar.submit') }}" method="GET">
        <input type="text" name="query" class="custom-input" placeholder="Marca, modelo, etc...">
    </form>
</div>

<div class="toast-parallax" id="toast-parallax">
    <h3>✅ ¡Equipo encontrado!</h3>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const form = document.getElementById('search-form');
        const toast = document.getElementById('toast-parallax');
        const uploadCard = document.getElementById('upload-card');
        const scannerInput = document.getElementById('scanner-input');

        // Auto focus
        scannerInput.focus();
        scannerInput.addEventListener('blur', () => {
            setTimeout(() => scannerInput.focus(), 100);
        });

        form.addEventListener('submit', (e) => {
            e.preventDefault();

            // Muestra el toast con animación suave
            toast.style.display = 'flex';

            // Oculta suavemente el formulario
            uploadCard.style.transform = 'translateY(-20px)';
            uploadCard.style.opacity = '0';

            // Redirige después de 2 segundos
            setTimeout(() => {
                form.submit();
            }, 2000);
        });
    });
</script>
@endsection
