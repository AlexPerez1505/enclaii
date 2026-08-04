@extends('layouts.app')

@section('title', 'Cartas de Garantía')
@section('titulo', 'Cartas de Garantía')

@section('content')

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;800;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/cartas.css') }}?v={{ time() }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"
        integrity="sha512-2lWgwjqkA1mESVq+unfFvB6vVqP20cgw2quQkiH7DRl+MtjJFt8h7xkFsjv1b9Cx8Q4xEYF52TtqDPX6C3v6mA=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<style>
    :root {
        --ft-primary: #2563eb;
        --ft-accent:  #22c55e;
        --ft-danger:  #ef4444;
        --ft-text-main:  #0f172a;
        --ft-text-muted: #64748b;
        --ft-border: #e8edf6;
        --ft-surface: #ffffff;
        --ft-surface-alt: #f8fafc;
        --ft-radius-lg: 24px;
        --ft-shadow: 0 20px 48px rgba(2,6,23,.08);
    }

    *{ box-sizing: border-box; }

    .ft-page {
        font-family: 'Outfit', system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
        max-width: 1140px;
        margin: 0 auto;
        padding: 16px 12px 40px;
        overflow: visible;
    }

    .btn-ui {
        border: 0;
        border-radius: 999px;
        height: 44px;
        padding: 0 14px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-weight: 500;
        font-size: 13px;
        text-decoration: none;
        cursor: pointer;
        transition: transform .14s ease, box-shadow .14s ease, background .14s ease, color .14s ease;
        user-select: none;
        white-space: nowrap;
        line-height: 1;
    }

    .btn-ui--square {
        width: 44px;
        height: 44px;
        padding: 0;
        justify-content: center;
    }

    .btn-ghost {
        background: #ffffff;
        color: var(--ft-text-main);
        border: 1px solid var(--ft-border);
        box-shadow: 0 6px 20px rgba(15, 23, 42, 0.05);
    }

    .btn-ghost:hover {
        background: #f1f5f9;
        color: var(--ft-primary);
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(37, 99, 235, 0.08);
    }

    .ft-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1.5rem;
        flex-wrap: wrap;
        position: sticky;
        top: 60px; /* ajusta al alto de tu navbar */
        z-index: 100;
        background: transparent;
        backdrop-filter: none;
        -webkit-backdrop-filter: none;
        padding: 20px 0;
        margin-bottom: 1.5rem;
        border-bottom: 1px solid transparent;
    }

    .ft-heading {
        display: flex;
        flex-direction: column;
        gap: .25rem;
    }

    .ft-title-text {
        font-size: 2rem;
        font-weight: 900;
        letter-spacing: -.4px;
        color: var(--ft-text-main);
        line-height: 1.1;
    }

    .ft-subtitle-text {
        font-size: .9rem;
        color: var(--ft-text-muted);
        font-weight: 400;
    }

    .ft-actions {
        display: flex;
        align-items: center;
        gap: .6rem;
        flex-wrap: wrap;
    }

    @media (max-width: 768px) {
        .ft-toolbar {
            align-items: flex-start;
            background: #ffffff;
            padding: 14px 0;
        }
        .ft-actions {
            width: 100%;
            justify-content: flex-start;
            gap: 10px;
        }
    }

    .smart-search {
        display: flex;
        align-items: center;
        padding: 0 14px;
        width: 44px;
        height: 44px;
        border-radius: 999px;
        background: #ffffff;
        border: 1px solid var(--ft-border);
        box-shadow: 0 6px 20px rgba(15, 23, 42, 0.05);
        overflow: hidden;
        cursor: text;
        transition: width .28s ease, box-shadow .28s ease, border-color .28s ease;
    }

    .smart-search:hover,
    .smart-search:focus-within {
        width: 280px;
        border-color: rgba(37, 99, 235, 0.4);
        box-shadow: 0 10px 28px rgba(37, 99, 235, .12);
    }

    .smart-search-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .smart-search-icon svg {
        width: 18px;
        height: 18px;
        fill: #94a3b8;
        transition: fill .2s ease;
    }

    .smart-search:focus-within .smart-search-icon svg {
        fill: var(--ft-primary);
    }

    .smart-search-input {
        border: none;
        outline: none;
        background: transparent;
        color: var(--ft-text-main);
        margin-left: .6rem;
        font-size: .88rem;
        width: 0;
        opacity: 0;
        transform: translateX(-4px);
        transition: width .28s ease, opacity .18s ease, transform .28s ease;
        font-family: inherit;
    }

    .smart-search:hover .smart-search-input,
    .smart-search:focus-within .smart-search-input {
        width: 100%;
        opacity: 1;
        transform: translateX(0);
    }

    .smart-search-input::placeholder { color: #94a3b8; }

    @media (max-width: 640px) {
        .smart-search { width: 100% !important; }
        .smart-search-input { width: 100% !important; opacity: 1 !important; transform: translateX(0) !important; }
    }

    .add-carta-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 44px;
        height: 44px;
        border: none;
        border-radius: 999px;
        cursor: pointer;
        position: relative;
        overflow: hidden;
        transition: width .3s cubic-bezier(0.4, 0, 0.2, 1), transform .15s ease, background-color .2s ease;
        background-color: var(--ft-text-main);
        text-decoration: none;
        color: #ffffff;
        box-shadow: 0 8px 20px rgba(15, 23, 42, 0.15);
    }

    .add-carta-btn .sign {
        font-size: 1.4rem;
        font-weight: 300;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: transform .3s ease;
    }

    .add-carta-btn .text {
        position: absolute;
        right: 18px;
        opacity: 0;
        color: white;
        font-size: 0.88rem;
        font-weight: 600;
        transition: opacity .2s ease;
        white-space: nowrap;
        letter-spacing: .2px;
    }

    .add-carta-btn:hover { width: 130px; background-color: #1e293b; }
    .add-carta-btn:hover .sign { transform: translateX(-24px); }
    .add-carta-btn:hover .text { opacity: 1; transition-delay: .05s; }
    .add-carta-btn:active { transform: scale(0.96); }

    .add-carta-btn.ft-add-compact { width: 44px !important; }
    .add-carta-btn.ft-add-compact .sign { transform: translateX(0) !important; }
    .add-carta-btn.ft-add-compact .text { opacity: 0 !important; }

    @media (max-width: 640px) {
        .add-carta-btn {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 56px !important;
            height: 56px;
            border-radius: 999px;
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.25);
            z-index: 1010;
        }
        .add-carta-btn .text { display: none !important; }
        .add-carta-btn .sign { font-size: 1.8rem; transform: none !important; }
    }

    /* ===== TABLA CON SCROLL INTERNO ===== */
    .ft-table-card {
        margin-top: 1rem;
        background: var(--ft-surface);
        border-radius: var(--ft-radius-lg);
        padding: 8px 16px;
        border: 1px solid var(--ft-border);
        box-shadow: var(--ft-shadow);
        max-height: calc(100vh - 220px);
        overflow-y: auto;
    }

    .ft-table-responsive { width: 100%; overflow-x: auto; }

    .ft-table { width: 100%; border-collapse: collapse; }

    .ft-table th,
    .ft-table td {
        padding: 14px 18px;
        font-size: .9rem;
        border-bottom: 1px solid var(--ft-border);
        color: var(--ft-text-main);
    }

    .ft-table th {
        text-align: left;
        text-transform: uppercase;
        font-size: .72rem;
        font-weight: 600;
        letter-spacing: .08em;
        color: #64748b;
        background: var(--ft-surface);
        padding-top: 20px;
        padding-bottom: 20px;
        /* Encabezado fijo dentro del scroll */
        position: sticky;
        top: 0;
        z-index: 10;
    }

    .ft-table tbody tr:last-child td { border-bottom: none; }
    .ft-table tbody tr { transition: background-color .15s ease; }
    .ft-table tbody tr:hover { background: #f8fafc; }

    .ft-name-cell {
        text-transform: uppercase;
        font-weight: 600;
        letter-spacing: .2px;
    }

    .ft-actions-cell {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 12px;
    }

    .btn-download-modern,
    .btn-delete-modern {
        width: 40px;
        height: 40px;
        border: none;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: transform .2s ease, box-shadow .2s ease, background .2s ease;
    }

    .btn-download-modern {
        background: rgba(37, 99, 235, 0.08);
        color: var(--ft-primary);
        text-decoration: none;
    }

    .btn-download-modern:hover {
        background: var(--ft-primary);
        color: #ffffff;
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(37, 99, 235, 0.25);
    }

    .btn-download-modern svg path,
    .btn-delete-modern svg path { stroke: currentColor; transition: stroke .2s ease; }

    .btn-delete-modern {
        background: rgba(239, 68, 68, 0.08);
        color: var(--ft-danger);
    }

    .btn-delete-modern:hover {
        background: var(--ft-danger);
        color: #ffffff;
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(239, 68, 68, 0.25);
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

    @media (max-width: 640px) {
        .ft-table-card { background: transparent; border: none; box-shadow: none; padding: 0; max-height: none; overflow-y: visible; }
        .ft-table thead { display: none; }
        .ft-table, .ft-table tbody, .ft-table tr, .ft-table td { display: block; width: 100%; }
        .ft-table tr {
            margin-bottom: 1rem;
            border-radius: 20px;
            background: #ffffff;
            border: 1px solid var(--ft-border);
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.04);
            overflow: hidden;
            padding: 8px 0;
        }
        .ft-table td {
            padding: 12px 16px;
            font-size: .85rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid #f1f5f9;
        }
        .ft-table td:last-child { border-bottom: none; }
        .ft-table td::before {
            content: attr(data-label);
            font-weight: 600;
            color: #94a3b8;
            text-transform: uppercase;
            font-size: .7rem;
            letter-spacing: .5px;
        }
        .ft-actions-cell { justify-content: flex-end; gap: 8px; }
    }

    /* ===== ESTILOS PIN OTP ===== */
    .swal2-pin-popup {
        border-radius: 20px !important;
        padding: 1.75rem 2rem !important;
        width: 480px !important;
        font-family: 'Outfit', system-ui, -apple-system, BlinkMacSystemFont, sans-serif;
    }
    .swal2-pin-header {
        display: flex; align-items: center; gap: 0.85rem;
        width: 100%; border-bottom: none !important; padding: 0 !important;
    }
    .swal2-pin-icon-box {
        background: #eff6ff; border-radius: 50%;
        width: 42px; height: 42px;
        display: flex; align-items: center; justify-content: center;
        color: #2563eb; flex-shrink: 0;
    }
    .swal2-pin-titles { display: flex; flex-direction: column; align-items: flex-start; text-align: left; }
    .swal2-pin-main-title { font-size: 1.15rem; font-weight: 700; color: #1e293b; margin: 0; }
    .swal2-pin-subtitle { font-size: 0.88rem; color: #64748b; margin: 0; }
    .swal2-pin-info-banner {
        background: #f1f5f9; border-radius: 10px; padding: 0.75rem 1rem;
        display: flex; align-items: center; gap: 0.6rem;
        margin: 1.25rem 0 1.5rem; width: 100%; box-sizing: border-box;
    }
    .swal2-pin-radio-dot {
        width: 14px; height: 14px; border-radius: 50%;
        background: #2563eb; border: 3px solid #ffffff;
        box-shadow: 0 0 0 1px #2563eb; flex-shrink: 0;
    }
    .swal2-pin-banner-text { font-size: 0.85rem; color: #334155; text-align: left; margin: 0; }
    .swal2-pin-banner-text strong { font-weight: 600; color: #0f172a; }
    .otp-container { display: flex; justify-content: space-between; gap: 0.5rem; width: 100%; margin-bottom: 0.75rem; }
    .otp-input {
        width: 52px; height: 54px;
        border: 1.5px solid #e2e8f0; border-radius: 12px;
        text-align: center; font-size: 1.5rem; font-weight: 600;
        color: #1e293b; background: #ffffff; outline: none;
        transition: all 0.2s ease;
    }
    .otp-input:focus { border-color: #2563eb; box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15); }
    .swal2-pin-footer-text { font-size: 0.82rem; color: #64748b; margin-top: 0.5rem; width: 100%; text-align: center; }
    .swal2-pin-footer-text span { font-weight: 500; color: #475569; }
</style>

<div class="ft-page">
    <div class="ft-toolbar">
        <div class="ft-heading">
            <div class="ft-title-text">Cartas de garantía</div>
            <p class="ft-subtitle-text">
                Busca, descarga y administra tus cartas de garantía.
            </p>
        </div>

        <div class="ft-actions">
            <a href="{{ url('/home') }}" class="btn-ui btn-ghost btn-ui--square" title="Ir al inicio">
                <i class="bi bi-house-door-fill fs-4" style="font-size: 18px; line-height: 1;"></i>
            </a>

            <a href="javascript:void(0);" onclick="volverInteligente();" class="btn-ui btn-ghost btn-ui--square" title="Volver atrás">
                <i class="bi bi-arrow-left-short fs-2" style="font-size: 18px; line-height: 1;"></i>
            </a>

            <div class="smart-search">
                <div class="smart-search-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <path d="M18.9,16.776A10.539,10.539,0,1,0,16.776,18.9l5.1,5.1L24,21.88ZM10.5,18A7.5,7.5,0,1,1,18,10.5,7.507,7.507,0,0,1,10.5,18Z"></path>
                    </svg>
                </div>
                <input id="cartasSearch" type="text" class="smart-search-input" placeholder="Buscar documento..." />
            </div>

            <a href="{{ route('carta.create') }}" class="add-carta-btn" id="addCartaBtn">
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
                        <th>Nombre del documento</th>
                        <th style="text-align: right; padding-right: 24px;">Acciones</th>
                    </tr>
                </thead>
                <tbody id="cartasTableBody">
                    @forelse($cartas as $carta)
                        <tr class="ft-row">
                            <td class="ft-name-cell" data-label="Documento">
                                {{ $carta->nombre }}
                            </td>
                            <td class="ft-actions-cell" data-label="Acciones">
                                <a href="{{ route('carta.descargar', $carta->id) }}" class="btn-download-modern" title="Descargar documento">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                                        <path d="M12 4V15" stroke-width="2" stroke-linecap="round"/>
                                        <path d="M7 11L12 16L17 11" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M5 20H19" stroke-width="2" stroke-linecap="round"/>
                                    </svg>
                                </a>

                                <form action="{{ route('carta.destroy', $carta->id) }}" method="POST" class="delete-form m-0" style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-delete-modern" title="Eliminar documento">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                                            <path d="M5 7H19" stroke-width="2" stroke-linecap="round"/>
                                            <path d="M19 7L18.2 19.2C18.1 19.6 17.7 20 17.3 20H6.7C6.3 20 5.9 19.6 5.8 19.2L5 7" stroke-width="2" stroke-linecap="round"/>
                                            <path d="M9 7V4H15V7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" style="padding: 2rem; text-align: center; color: var(--ft-text-muted);">
                                No hay cartas registradas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    (function () {
        const MODULE_PREFIX = '/carta';
        const STORAGE_KEY   = 'cartas_volver_target';
        const NAV_KEY       = 'navHistory_cartas';
        const FALLBACK_URL  = "{{ url('/home') }}";

        const current = window.location.href;
        let nav = [];
        try { nav = JSON.parse(sessionStorage.getItem(NAV_KEY) || '[]'); } catch(e) {}

        if (nav[nav.length - 1] !== current) {
            nav.push(current);
            if (nav.length > 30) nav.shift();
            sessionStorage.setItem(NAV_KEY, JSON.stringify(nav));
        }

        const referrer = document.referrer;
        let referrerIsInternalModule = false;

        if (referrer) {
            try {
                const ref = new URL(referrer);
                referrerIsInternalModule =
                    ref.origin === window.location.origin &&
                    ref.pathname.startsWith(MODULE_PREFIX);
            } catch(e) {}
        }

        if (referrer && !referrerIsInternalModule) {
            sessionStorage.setItem(STORAGE_KEY, referrer);
        } else if (!sessionStorage.getItem(STORAGE_KEY)) {
            sessionStorage.setItem(STORAGE_KEY, FALLBACK_URL);
        }

        window.volverInteligente = function () {
            let nav = [];
            try { nav = JSON.parse(sessionStorage.getItem(NAV_KEY) || '[]'); } catch(e) {}

            for (let i = nav.length - 1; i >= 0; i--) {
                try {
                    const u = new URL(nav[i]);
                    if (!u.pathname.startsWith(MODULE_PREFIX)) {
                        nav.splice(i, 1);
                        sessionStorage.setItem(NAV_KEY, JSON.stringify(nav));
                        sessionStorage.removeItem(STORAGE_KEY);
                        window.location.href = u.href;
                        return;
                    }
                } catch(e) {}
            }

            const target = sessionStorage.getItem(STORAGE_KEY) || FALLBACK_URL;
            sessionStorage.removeItem(STORAGE_KEY);
            window.location.href = target;
        };
    })();

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

        /* ===== CONFIRMACIÓN ELIMINAR CON DISEÑO OTP ===== */
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
                            <p class="swal2-pin-banner-text">
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
                    customClass: { popup: 'swal2-pin-popup' },
                    didOpen: () => {
                        const inputs = document.querySelectorAll('.otp-input');
                        if (inputs[0]) inputs[0].focus();

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
                                    inputs.forEach((inp, idx) => { inp.value = pasteData[idx]; });
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

        /* ===== BUSCADOR EN TIEMPO REAL ===== */
        const searchInput = document.getElementById('cartasSearch');
        const rows = Array.from(document.querySelectorAll('#cartasTableBody .ft-row'));

        if (searchInput) {
            searchInput.addEventListener('input', function () {
                const query = this.value.toLowerCase().trim();
                rows.forEach(row => {
                    const text = row.querySelector('.ft-name-cell').textContent.toLowerCase();
                    row.style.display = text.includes(query) ? '' : 'none';
                });
            });
        }

        /* ===== COMPACTACIÓN DEL BOTÓN AL SCROLL ===== */
        const addBtn = document.getElementById('addCartaBtn');

        function toggleAddBtnOnScroll() {
            if (!addBtn) return;
            if (window.innerWidth <= 640) { addBtn.classList.remove('ft-add-compact'); return; }
            addBtn.classList.toggle('ft-add-compact', window.scrollY > 40);
        }

        window.addEventListener('scroll', toggleAddBtnOnScroll, { passive: true });
        window.addEventListener('resize', toggleAddBtnOnScroll);
        toggleAddBtnOnScroll();
    });
</script>
@endsection