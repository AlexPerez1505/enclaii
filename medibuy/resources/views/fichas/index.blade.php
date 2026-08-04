@extends('layouts.app')

@section('title', 'FichaTecnica')
@section('titulo', 'Ficha Técnica')

@section('content')
<link rel="stylesheet" href="{{ asset('css/fichas.css') }}?v={{ time() }}">

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
{{-- GSAP para la animación del botón de descarga --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"
        integrity="sha512-2lWgwjqkA1mESVq+unfFvB6vVqP20cgw2quQkiH7DRl+MtjJFt8h7xkFsjv1b9Cx8Q4xEYF52TtqDPX6C3v6mA=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<style>
    :root {
        --ft-primary: #2563eb;
        --ft-accent:  #22c55e;
        --ft-danger:  #ef4444;
        --ft-text-main:  #0f172a;
        --ft-text-muted: #6b7280;
        --ft-border: #e5e7eb;
        --ft-surface: #ffffff;
        --ft-surface-alt: #f9fafb;
        --ft-radius-lg: 1rem;
    }

    /* ===== TIPOGRAFÍA GLOBAL (SÖHNE STYLE) ===== */
    .ft-page {
        font-family: "Söhne", "Circular Std", "Poppins", system-ui, -apple-system,
            BlinkMacSystemFont, "Segoe UI", "Helvetica Neue", Arial, sans-serif;
        max-width: 1100px;
        margin: 0 auto;
        padding: 1.75rem 1.25rem 2.75rem;
    }

    /* ===== TOOLBAR SUPERIOR ===== */
    .ft-toolbar {
    position: sticky;
    top: 70px;
    z-index: 100;
    background: transparent;
    padding: 1rem 0;
    border-bottom: 1px solid transparent;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1rem;
    flex-wrap: wrap;
}

    .ft-heading {
        display: flex;
        flex-direction: column;
        gap: .25rem;
    }

    .ft-title-text {
        font-size: 1.5rem;
        font-weight: 700;
        letter-spacing: .02em;
        color: var(--ft-text-main);
    }

    .ft-subtitle-text {
        font-size: .9rem;
        color: var(--ft-text-muted);
    }

    .ft-actions {
        display: flex;
        align-items: center;
        gap: .7rem;
        flex-wrap: wrap;
    }

    @media (max-width: 768px) {
        .ft-toolbar {
            flex-direction: column;
            align-items: flex-start;
        }
        .ft-actions {
            width: 100%;
            justify-content: flex-start;
        }
    }

    /* ===== BUSCADOR PILL BLANCO ===== */
    .smart-search {
        display: flex;
        align-items: center;
        padding: 0 1rem;
        width: 56px;
        height: 44px;
        border-radius: 999px;
        background: #ffffff;
        border: 1px solid #e5e7eb;
        box-shadow: 0 6px 20px rgba(15, 23, 42, 0.08);
        overflow: hidden;
        cursor: text;
        transition:
            width .28s ease,
            box-shadow .28s ease,
            border-color .28s ease,
            background .28s ease;
    }

    .smart-search:hover,
    .smart-search:focus-within {
        width: 240px;
        border-color: var(--ft-primary);
        box-shadow: 0 10px 28px rgba(37, 99, 235, .15);
        background: #ffffff;
    }

    .smart-search-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .smart-search-icon svg {
        width: 20px;
        height: 20px;
        fill: var(--ft-primary);
    }

    .smart-search-input {
        border: none;
        outline: none;
        background: transparent;
        color: var(--ft-text-main);
        margin-left: .6rem;
        font-size: .9rem;
        width: 0;
        opacity: 0;
        transform: translateX(-4px);
        transition:
            width .28s ease,
            opacity .18s ease,
            transform .28s ease;
    }

    .smart-search:hover .smart-search-input,
    .smart-search:focus-within .smart-search-input {
        width: 100%;
        opacity: 1;
        transform: translateX(0);
    }

    .smart-search-input::placeholder {
        color: #9ca3af;
    }

    /* === MODO CELULAR: SIN ANIMACIÓN, SIEMPRE ABIERTO === */
    @media (max-width: 640px) {
        .smart-search {
            width: 100%;
            transition: none;
        }

        .smart-search:hover,
        .smart-search:focus-within {
            width: 100%;
            box-shadow: 0 6px 20px rgba(15, 23, 42, 0.08);
            border-color: #e5e7eb;
        }

        .smart-search-input {
            width: 100%;
            opacity: 1;
            transform: translateX(0);
        }
    }

    /* ===== BOTÓN "AGREGAR FICHA" ===== */
    .add-ficha-btn {
        display: flex;
        align-items: center;
        justify-content: flex-start;
        width: 56px;
        height: 44px;
        border: none;
        border-radius: 999px;
        cursor: pointer;
        position: relative;
        overflow: hidden;
        transition-duration: .3s;
        box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.199);
        background-color: #000000;
        text-decoration: none;
        color: #ffffff;
        padding: 0;
        z-index: 10;
    }

    .add-ficha-btn .sign {
        width: 100%;
        font-size: 1.6rem;
        color: white;
        transition-duration: .3s;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .add-ficha-btn .text {
        position: absolute;
        right: 0%;
        width: 0%;
        opacity: 0;
        color: white;
        font-size: 0.95rem;
        font-weight: 500;
        transition-duration: .3s;
        white-space: nowrap;
    }

    .add-ficha-btn:hover {
        width: 135px;
        border-radius: 999px;
        transition-duration: .3s;
    }

    .add-ficha-btn:hover .sign {
        width: 35%;
        transition-duration: .3s;
        padding-left: 14px;
    }

    .add-ficha-btn:hover .text {
        opacity: 1;
        width: 65%;
        transition-duration: .3s;
        padding-right: 18px;
    }

    .add-ficha-btn:active {
        transform: translate(2px ,2px);
    }

    .add-ficha-btn.ft-add-compact {
        width: 44px !important;
        height: 44px;
        border-radius: 999px;
    }

    .add-ficha-btn.ft-add-compact .sign {
        width: 100% !important;
        padding-left: 0 !important;
    }

    .add-ficha-btn.ft-add-compact .text {
        opacity: 0 !important;
        width: 0 !important;
        padding-right: 0 !important;
    }

    /* ===== BOTONES DE NAVEGACIÓN (VOLVER / INICIO) ===== */
    .ft-btn-nav {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 44px;
        height: 44px;
        border-radius: 999px;
        background: #ffffff;
        border: 1px solid #e5e7eb;
        color: var(--ft-text-main);
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.05);
        cursor: pointer;
        transition: all 0.25s ease;
        text-decoration: none;
    }

    .ft-btn-nav:hover {
        background: #f1f5f9;
        border-color: #cbd5e1;
        transform: translateY(-1px);
        color: var(--ft-primary);
    }

    .ft-btn-nav svg {
        width: 20px;
        height: 20px;
    }

    /* === MODO CELULAR: BOTÓN FLOTANTE INFERIOR DERECHA === */
    @media (max-width: 640px) {
        .add-ficha-btn {
            position: fixed;
            bottom: 18px;
            right: 18px;
            left: auto;
            width: 56px;
            height: 56px;
            border-radius: 999px;
            box-shadow: 0 14px 30px rgba(0, 0, 0, 0.4);
            justify-content: center;
        }

        .add-ficha-btn .text {
            display: none;
        }

        .add-ficha-btn .sign {
            width: 100%;
            padding-left: 0;
            font-size: 2rem;
        }

        .add-ficha-btn.ft-add-compact {
            width: 56px !important;
            height: 56px;
        }
    }

    /* ===== ALERTAS ===== */
    .ft-alert {
        display: flex;
        align-items: center;
        gap: .6rem;
        padding: .75rem 1.1rem;
        border-radius: 12px;
        font-size: .88rem;
        font-weight: 500;
        margin-bottom: 1rem;
        animation: slideDown .3s ease;
    }

    .ft-alert--success {
        background: #f0fdf4;
        color: #166534;
        border: 1px solid #bbf7d0;
    }

    .ft-alert--error {
        background: #fef2f2;
        color: #991b1b;
        border: 1px solid #fecaca;
    }

    @keyframes slideDown {
        from { opacity: 0; transform: translateY(-8px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    /* ===== CARD CONTENEDOR Y TABLA ===== */
    .ft-table-card {
    margin-top: 1.25rem;
    background: var(--ft-surface);
    border-radius: var(--ft-radius-lg);
    padding: 1.25rem 1.4rem;
    border: 1px solid var(--ft-border);
    box-shadow: 0 18px 45px rgba(15, 23, 42, .12);
    max-height: calc(100vh - 220px);
    overflow-y: auto;
}
    .ft-table thead th {
    position: sticky;
    top: 0;
    z-index: 10;
    background: var(--ft-surface-alt);
}

    .ft-table-responsive {
        width: 100%;
    }

    .ft-table {
        width: 100%;
        border-collapse: collapse;
    }

    .ft-table th,
    .ft-table td {
        padding: .75rem .9rem;
        font-size: .88rem;
        border-bottom: 1px solid #e5e7eb;
        color: var(--ft-text-main);
        vertical-align: middle;
    }

    .ft-table th {
        text-align: left;
        text-transform: uppercase;
        font-size: .75rem;
        letter-spacing: .09em;
        color: #6b7280;
        background: var(--ft-surface-alt);
    }

    .ft-table tbody tr:hover {
        background: #eef2ff;
    }

    .ft-name-cell {
        text-transform: uppercase;
        font-weight: 600;
    }

    .ft-actions-cell {
        display: flex;
        align-items: center;
        gap: .6rem;
    }

    /* ===== DISEÑO RESPONSIVE MÓVIL ===== */
    @media (max-width: 640px) {
        .ft-table thead {
            display: none;
        }

        .ft-table,
        .ft-table tbody,
        .ft-table tr,
        .ft-table td {
            display: block;
            width: 100%;
        }

        .ft-table tr {
            margin-bottom: .9rem;
            border-radius: .9rem;
            background: #ffffff;
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.10);
            overflow: hidden;
            border: 1px solid var(--ft-border);
        }

        .ft-table td {
            padding: .55rem .9rem;
            font-size: .82rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid #e5e7eb;
        }

        .ft-table td:last-child {
            border-bottom: none;
        }

        .ft-table td::before {
            content: attr(data-label);
            font-weight: 600;
            color: var(--ft-text-muted);
            margin-right: .75rem;
            text-transform: uppercase;
            font-size: .7rem;
            flex-shrink: 0;
        }

        .ft-actions-cell {
            justify-content: flex-end;
        }
    }

    /* ===== BOTONES DE ACCIÓN (DESCARGAR / ELIMINAR) ===== */
    .icon-btn-download {
        width: 40px;
        height: 40px;
        border: none;
        border-radius: 12px;
        background: #eaf3ff;
        color: #3b82f6;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: .25s;
        cursor: pointer;
        text-decoration: none;
    }

    .icon-btn-download:hover {
        transform: translateY(-2px);
        background: #dbeafe;
    }

    .icon-btn-download svg {
        width: 22px;
        height: 22px;
    }

    .icon-btn-delete {
        width: 40px;
        height: 40px;
        border: none;
        border-radius: 12px;
        background: #fff5f5;
        color: #ff4d4f;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: .25s;
    }

    .icon-btn-delete:hover {
        background: #ff4d4f;
        color: white;
        transform: translateY(-2px);
    }

    .icon-btn-delete svg {
        width: 20px;
        height: 20px;
    }

    /* ===== ESTILOS CUSTOM PARA EL MODAL DE PIN (UI DE LA IMAGEN) ===== */
    .swal2-pin-popup {
        border-radius: 20px !important;
        padding: 1.75rem 2rem !important;
        width: 480px !important;
        font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    }
    .swal2-pin-header {
        display: flex;
        align-items: center;
        gap: 0.85rem;
        width: 100%;
        border-bottom: none !important;
        padding: 0 !important;
    }
    .swal2-pin-icon-box {
        background: #eff6ff;
        border-radius: 50%;
        width: 42px;
        height: 42px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #2563eb;
        flex-shrink: 0;
    }
    .swal2-pin-titles {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        text-align: left;
    }
    .swal2-pin-main-title {
        font-size: 1.15rem;
        font-weight: 700;
        color: #1e293b;
        margin: 0;
    }
    .swal2-pin-subtitle {
        font-size: 0.88rem;
        color: #64748b;
        margin: 0;
    }
    .swal2-pin-info-banner {
        background: #f1f5f9;
        border-radius: 10px;
        padding: 0.75rem 1rem;
        display: flex;
        align-items: center;
        gap: 0.6rem;
        margin: 1.25rem 0 1.5rem;
        width: 100%;
        box-sizing: border-box;
    }
    .swal2-pin-radio-dot {
        width: 14px;
        height: 14px;
        border-radius: 50%;
        background: #2563eb;
        border: 3px solid #ffffff;
        box-shadow: 0 0 0 1px #2563eb;
        flex-shrink: 0;
    }
    .swal2-pin-banner-text {
        font-size: 0.85rem;
        color: #334155;
        text-align: left;
        margin: 0;
    }
    .swal2-pin-banner-text strong {
        font-weight: 600;
        color: #0f172a;
    }
    .otp-container {
        display: flex;
        justify-content: space-between;
        gap: 0.5rem;
        width: 100%;
        margin-bottom: 0.75rem;
    }
    .otp-input {
        width: 52px;
        height: 54px;
        border: 1.5px solid #e2e8f0;
        border-radius: 12px;
        text-align: center;
        font-size: 1.5rem;
        font-weight: 600;
        color: #1e293b;
        background: #ffffff;
        outline: none;
        transition: all 0.2s ease;
    }
    .otp-input:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
    }
    .swal2-pin-footer-text {
        font-size: 0.82rem;
        color: #64748b;
        margin-top: 0.5rem;
        width: 100%;
        text-align: center;
    }
    .swal2-pin-footer-text span {
        font-weight: 500;
        color: #475569;
    }
</style>

<div class="ft-page">
    <div class="ft-toolbar">
        <div class="ft-heading">
            <div class="ft-title-text">Fichas técnicas</div>
            <p class="ft-subtitle-text">
                Busca, descarga y administra las fichas técnicas de tus productos.
            </p>
        </div>

        <div class="ft-actions">
            {{-- BOTÓN VOLVER INICIO --}}
            <a href="{{ url('/home') }}" class="ft-btn-nav" title="Volver al inicio">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                </svg>
            </a>

            {{-- BOTÓN VOLVER PÁGINA ANTERIOR (HISTORY BACK) --}}
            <a href="javascript:history.back()" class="ft-btn-nav" title="Volver atrás">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
            </a>

            {{-- BUSCADOR PILL BLANCO --}}
            <div class="smart-search">
                <div class="smart-search-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <path d="M18.9,16.776A10.539,10.539,0,1,0,16.776,18.9l5.1,5.1L24,21.88ZM10.5,18A7.5,7.5,0,1,1,18,10.5,7.507,7.507,0,0,1,10.5,18Z"></path>
                    </svg>
                </div>
                <input
                    id="fichasSearch"
                    type="text"
                    class="smart-search-input"
                    placeholder="Buscar por nombre..."
                />
            </div>

            {{-- BOTÓN AGREGAR FICHA --}}
            <a href="{{ route('fichas.create') }}" class="add-ficha-btn" id="addFichaBtn">
                <span class="sign">+</span>
                <span class="text">Agregar</span>
            </a>
        </div>
    </div>

    {{-- ALERTAS DE SESIÓN --}}
    @if (session('success'))
        <div class="ft-alert ft-alert--success">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M20 6L9 17l-5-5"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="ft-alert ft-alert--error">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M18 6L6 18M6 6l12 12"/>
            </svg>
            {{ session('error') }}
        </div>
    @endif

    <div class="ft-table-card">
        <div class="ft-table-responsive">
            <table class="ft-table">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="fichasTableBody">
                    @forelse ($fichas as $ficha)
                        <tr class="ft-row">
                            <td class="ft-name-cell" data-label="Nombre">
                                {{ $ficha->nombre }}
                            </td>
                            <td class="ft-actions-cell" data-label="Acciones">
                                {{-- BOTÓN DESCARGAR --}}
                                <a href="{{ route('fichas.download', $ficha) }}" class="icon-btn-download" title="Descargar">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 16V4m0 12l-4-4m4 4l4-4M4 20h16"/>
                                    </svg>
                                </a>

                                {{-- FORMULARIO ELIMINAR --}}
                                <form action="{{ route('fichas.destroy', $ficha) }}" method="POST" class="delete-form m-0">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="icon-btn-delete" title="Eliminar">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7L18.133 19.142A2 2 0 0116.138 21H7.862A2 2 0 015.867 19.142L5 7m5-3h4m-7 3h10"/>
                                        </svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" style="padding: 2rem; text-align: center; color: var(--ft-text-muted);">
                                No hay fichas registradas aún.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const pinAprobacionCorrecto = "{{ env('APROBACION_PIN') }}";

        /* ===== AUTO-OCULTAR ALERTAS ===== */
        document.querySelectorAll('.ft-alert').forEach(alert => {
            setTimeout(() => {
                alert.style.transition = 'opacity .4s ease, transform .4s ease';
                alert.style.opacity = '0';
                alert.style.transform = 'translateY(-6px)';
                setTimeout(() => alert.remove(), 400);
            }, 4000);
        });

        /* ===== CONFIRMACIÓN ELIMINAR CON DISEÑO OTP INDEPENDIENTE ===== */
        document.querySelectorAll(".delete-form").forEach(form => {
            form.addEventListener("submit", function (event) {
                event.preventDefault();
                const formElement = this;

                Swal.fire({
                    html: `
                        <div class="swal2-pin-header">
                            <div class="swal2-pin-icon-box">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                                </svg>
                            </div>
                            <div class="swal2-pin-titles">
                                <h2 class="swal2-pin-main-title">Confirmación segura</h2>
                                <p class="swal2-pin-subtitle">Escribe el PIN de 6 dígitos</p>
                            </div>
                        </div>

                        <div class="swal2-pin-info-banner">
                            <div class="swal2-pin-radio-dot"></div>
                            <p class="swal2-pin-text swal2-pin-banner-text">
                                Al completar los <strong>6 dígitos</strong>, se confirma automáticamente.
                            </p>
                        </div>

                        <div class="otp-container">
                            <input type="text" class="otp-input" maxlength="1" pattern="[0-9]*" inputmode="numeric">
                            <input type="text" class="otp-input" maxlength="1" pattern="[0-9]*" inputmode="numeric">
                            <input type="text" class="otp-input" maxlength="1" pattern="[0-9]*" inputmode="numeric">
                            <input type="text" class="otp-input" maxlength="1" pattern="[0-9]*" inputmode="numeric">
                            <input type="text" class="otp-input" maxlength="1" pattern="[0-9]*" inputmode="numeric">
                            <input type="text" class="otp-input" maxlength="1" pattern="[0-9]*" inputmode="numeric">
                        </div>

                        <p class="swal2-pin-footer-text">Puedes <span>pegar</span> el PIN completo.</p>
                    `,
                    showConfirmButton: false,
                    showCloseButton: true,
                    customClass: {
                        popup: 'swal2-pin-popup'
                    },
                    didOpen: () => {
                        const inputs = document.querySelectorAll('.otp-input');

                        if(inputs[0]) inputs[0].focus();

                        inputs.forEach((input, index) => {
                            input.addEventListener('input', (e) => {
                                const val = e.target.value;
                                if (val.length === 1 && index < inputs.length - 1) {
                                    inputs[index + 1].focus();
                                }
                                checkAndSubmitPin();
                            });

                            input.addEventListener('keydown', (e) => {
                                if (e.key === 'Backspace' && !e.target.value && index > 0) {
                                    inputs[index - 1].focus();
                                }
                            });

                            input.addEventListener('paste', (e) => {
                                e.preventDefault();
                                const pasteData = (e.clipboardData || window.clipboardData).getData('text').trim();
                                if (pasteData.length === 6 && /^\d+$/.test(pasteData)) {
                                    inputs.forEach((inp, idx) => {
                                        inp.value = pasteData[idx];
                                    });
                                    inputs[inputs.length - 1].focus();
                                    checkAndSubmitPin();
                                }
                            });
                        });

                        function checkAndSubmitPin() {
                            const fullPin = Array.from(inputs).map(inp => inp.value).join('');

                            if (fullPin.length === 6) {
                                if (fullPin === pinAprobacionCorrecto) {
                                    Swal.close();
                                    formElement.submit();
                                } else {
                                    inputs.forEach(inp => {
                                        inp.style.borderColor = '#ef4444';
                                        inp.style.boxShadow = '0 0 0 3px rgba(239, 68, 68, 0.15)';
                                    });

                                    setTimeout(() => {
                                        inputs.forEach(inp => {
                                            inp.value = '';
                                            inp.style.borderColor = '#e2e8f0';
                                            inp.style.boxShadow = 'none';
                                        });
                                        inputs[0].focus();
                                    }, 800);
                                }
                            }
                        }
                    }
                });
            });
        });

        /* ===== FILTRO DE BÚSQUEDA ===== */
        const searchInput = document.getElementById('fichasSearch');
        const rows = Array.from(document.querySelectorAll('#fichasTableBody .ft-row'));

        if (searchInput) {
            searchInput.addEventListener('input', function () {
                const query = this.value.toLowerCase().trim();
                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    row.style.setProperty('display', text.includes(query) ? '' : 'none', 'important');
                });
            });
        }

        /* ===== BOTÓN AGREGAR: SCROLL COMPACT (DESKTOP) ===== */
        const addBtn = document.getElementById('addFichaBtn');

        function toggleAddBtnOnScroll() {
            if (!addBtn) return;

            if (window.innerWidth <= 640) {
                addBtn.classList.remove('ft-add-compact');
                return;
            }

            const shouldCompact = window.scrollY > 80;
            addBtn.classList.toggle('ft-add-compact', shouldCompact);
        }

        window.addEventListener('scroll', toggleAddBtnOnScroll, { passive: true });
        window.addEventListener('resize', toggleAddBtnOnScroll);
        toggleAddBtnOnScroll();
    });
</script>
@endsection