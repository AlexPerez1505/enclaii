@extends('layouts.app')

@section('title', 'Cartas de Garantía')
@section('titulo', 'Cartas de Garantía')

@section('content')

<link rel="stylesheet" href="{{ asset('css/cartas.css') }}?v={{ time() }}">

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
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1.5rem;
        flex-wrap: wrap;
        margin-bottom: 1.5rem;
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
        gap: .9rem;
        flex-wrap: wrap;
    }

    @media (max-width: 768px) {
        .ft-toolbar {
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
        width: 260px;
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
        .ft-actions {
            width: 100%;
        }

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

    /* ===== BOTÓN “AGREGAR” ESTILO Btn ===== */
    .add-carta-btn {
        display: flex;
        align-items: center;
        justify-content: flex-start;
        width: 56px;
        height: 44px;          /* mismo alto que buscador */
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

    .add-carta-btn .sign {
        width: 100%;
        font-size: 1.6rem;
        color: white;
        transition-duration: .3s;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .add-carta-btn .text {
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

    /* hover: se alarga y aparece el texto (desktop) */
    .add-carta-btn:hover {
        width: 145px;
        border-radius: 999px;
        transition-duration: .3s;
    }

    .add-carta-btn:hover .sign {
        width: 35%;
        transition-duration: .3s;
        padding-left: 14px;
    }

    .add-carta-btn:hover .text {
        opacity: 1;
        width: 65%;
        transition-duration: .3s;
        padding-right: 18px;
    }

    .add-carta-btn:active {
        transform: translate(2px ,2px);
    }

    /* Estado compacto con scroll en desktop */
    .add-carta-btn.ft-add-compact {
        width: 44px !important;
        height: 44px;
        border-radius: 999px;
    }

    .add-carta-btn.ft-add-compact .sign {
        width: 100% !important;
        padding-left: 0 !important;
    }

    .add-carta-btn.ft-add-compact .text {
        opacity: 0 !important;
        width: 0 !important;
        padding-right: 0 !important;
    }

    /* === MODO CELULAR: BOTÓN FLOTANTE INFERIOR DERECHA === */
    @media (max-width: 640px) {
        .add-carta-btn {
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

        .add-carta-btn .text {
            display: none; /* solo se ve el + en móvil */
        }

        .add-carta-btn .sign {
            width: 100%;
            padding-left: 0;
            font-size: 2rem;
        }

        .add-carta-btn.ft-add-compact {
            width: 56px !important;
            height: 56px;
        }
    }

    /* ===== CARD CONTENEDOR ===== */
    .ft-table-card {
        margin-top: 1.25rem;
        background: var(--ft-surface);
        border-radius: var(--ft-radius-lg);
        padding: 1.25rem 1.4rem;
        border: 1px solid var(--ft-border);
        box-shadow: 0 18px 45px rgba(15, 23, 42, .12);
    }

    .ft-table-responsive {
        width: 100%;
    }

    /* ===== TABLA ESCRITORIO ===== */
    .ft-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 100%;
    }

    .ft-table th,
    .ft-table td {
        padding: .75rem .9rem;
        font-size: .88rem;
        border-bottom: 1px solid #e5e7eb;
        color: var(--ft-text-main);
    }

    .ft-table th {
        text-align: left;
        text-transform: uppercase;
        font-size: .75rem;
        letter-spacing: .09em;
        color: #6b7280;
        background: var(--ft-surface-alt);
    }

    .ft-table tbody tr:nth-child(odd) {
        background: #ffffff;
    }

    .ft-table tbody tr:nth-child(even) {
        background: #f9fafb;
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
        flex-wrap: wrap;
    }

    .ft-download-cell {
        text-align: left;
    }

    /* ===== TABLA RESPONSIVE COMO CARDS EN CELULAR ===== */
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
            gap: .4rem;
        }

        .ft-download-cell {
            justify-content: flex-end;
        }
    }

    /* ===== ALERT SUCCESS ===== */
    .ft-alert {
        margin-bottom: 1rem;
        padding: .7rem 1rem;
        border-radius: .75rem;
        background: #ecfdf5;
        color: #166534;
        font-size: .85rem;
        border: 1px solid #bbf7d0;
    }

    /* ===== BOTÓN ELIMINAR ===== */
    .btn-custom-cancel {
        border-radius: .6rem;
        padding: .35rem .85rem;
        font-size: .78rem;
        background: var(--ft-danger);
        border: none;
        color: #fef2f2;
        font-weight: 600;
        transition:
            background .18s ease,
            transform .18s ease,
            box-shadow .18s ease;
        box-shadow: 0 10px 20px rgba(239, 68, 68, .55);
    }

    .btn-custom-cancel:hover {
        background: #b91c1c;
        transform: translateY(-1px);
        box-shadow: 0 12px 26px rgba(185, 28, 28, .7);
    }

    .btn-custom-cancel:active {
        transform: translateY(0) scale(.97);
        box-shadow: 0 6px 16px rgba(185, 28, 28, .7);
    }

    /* ===== BOTÓN DOWNLOAD NEGRO ICON-ONLY (DRIBBBLE) ===== */
    .button {
        --background: #111827;
        --rectangle: #020617;
        --success: #4BC793;
        --text: #fff;
        --arrow: #fff;
        --checkmark: #fff;
        --shadow: rgba(15, 23, 42, .6);
        display: inline-flex;
        justify-content: center;
        align-items: center;
        overflow: hidden;
        text-decoration: none;
        -webkit-mask-image: -webkit-radial-gradient(white, black);
        background: var(--background);
        border-radius: 10px;
        box-shadow: 0 6px 18px -1px var(--shadow);
        transition: transform .2s ease, box-shadow .2s ease;
        width: 44px;
        height: 44px;
    }
    .button:active {
        transform: scale(.96);
        box-shadow: 0 3px 10px -1px var(--shadow);
    }
    .button > div {
        position: relative;
        width: 44px;
        height: 44px;
        background: var(--rectangle);
    }
    .button > div:before,
    .button > div:after {
        content: '';
        display: block;
        position: absolute;
    }
    .button > div:before {
        border-radius: 1px;
        width: 2px;
        top: 50%;
        left: 50%;
        height: 17px;
        margin: -9px 0 0 -1px;
        background: var(--arrow);
    }
    .button > div:after {
        width: 44px;
        height: 44px;
        transform-origin: 50% 0;
        border-radius: 0 0 80% 80%;
        background: var(--success);
        top: 0;
        left: 0;
        transform: scaleY(0);
    }
    .button > div svg {
        display: block;
        position: absolute;
        width: 20px;
        height: 20px;
        left: 50%;
        top: 50%;
        margin: -10px 0 0 -10px;
        fill: none;
        z-index: 1;
        stroke-width: 2px;
        stroke: var(--arrow);
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .button.loading > div:before {
        animation: line calc(var(--duration) * 1ms) linear forwards calc(var(--duration) * .065ms);
    }
    .button.loading > div:after {
        animation: background calc(var(--duration) * 1ms) linear forwards calc(var(--duration) * .065ms);
    }
    .button.loading > div svg {
        animation: svg calc(var(--duration) * 1ms) linear forwards calc(var(--duration) * .065ms);
    }

    @keyframes line {
        5%, 10% { transform: translateY(-30px); }
        40%     { transform: translateY(-20px); }
        65%     { transform: translateY(0); }
        75%,100%{ transform: translateY(30px); }
    }

    @keyframes svg {
        0%,20% {
            stroke-dasharray: 0;
            stroke-dashoffset: 0;
        }
        21%,89% {
            stroke-dasharray: 26px;
            stroke-dashoffset: 26px;
            stroke-width: 3px;
            margin: -10px 0 0 -10px;
            stroke: var(--checkmark);
        }
        100% {
            stroke-dasharray: 26px;
            stroke-dashoffset: 0;
            margin: -10px 0 0 -10px;
            stroke: var(--checkmark);
        }
        12% {
            opacity: 1;
        }
        20%,89% {
            opacity: 0;
        }
        90%,100% {
            opacity: 1;
        }
    }

    @keyframes background {
        10%  { transform: scaleY(0); }
        40%  { transform: scaleY(.15); }
        65%  {
            transform: scaleY(.5);
            border-radius: 0 0 50% 50%;
        }
        75%  { border-radius: 0 0 50% 50%; }
        90%,100% {
            border-radius: 0;
        }
        75%,100% {
            transform: scaleY(1);
        }
    }

    .sr-only {
        position: absolute;
        width: 1px;
        height: 1px;
        padding: 0;
        margin: -1px;
        overflow: hidden;
        clip: rect(0, 0, 0, 0);
        white-space: nowrap;
        border: 0;
    }
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
            {{-- BUSCADOR --}}
            <div class="smart-search">
                <div class="smart-search-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <path
                            d="M18.9,16.776A10.539,10.539,0,1,0,16.776,18.9l5.1,5.1L24,21.88ZM10.5,18A7.5,7.5,0,1,1,18,10.5,7.507,7.507,0,0,1,10.5,18Z">
                        </path>
                    </svg>
                </div>
                <input
                    id="cartasSearch"
                    type="text"
                    class="smart-search-input"
                    placeholder="Buscar por nombre de documento..."
                />
            </div>

            {{-- BOTÓN AGREGAR CARTA --}}
            <a href="{{ route('carta.create') }}" class="add-carta-btn" id="addCartaBtn">
                <span class="sign">+</span>
                <span class="text">Agregar</span>
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="ft-alert">
            {{ session('success') }}
        </div>
    @endif

    <div class="ft-table-card">
        <div class="ft-table-responsive">
            <table class="ft-table">
                <thead>
                    <tr>
                        <th>Nombre del documento</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="cartasTableBody">
                    @forelse($cartas as $carta)
                        <tr class="ft-row">
                            <td class="ft-name-cell" data-label="Documento">
                                {{ $carta->nombre }}
                            </td>
                            <td class="ft-actions-cell" data-label="Acciones">
                                {{-- Descarga con botón negro animado --}}
                                <a href="{{ route('carta.descargar', $carta->id) }}"
                                   class="button"
                                   title="Descargar carta">
                                    <span class="sr-only">Descargar carta</span>
                                    <div>
                                        <svg viewBox="0 0 24 24"></svg>
                                    </div>
                                </a>

                                <form action="{{ route('carta.destroy', $carta->id) }}"
                                      method="POST"
                                      class="delete-form m-0"
                                      style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-custom-cancel">
                                        Eliminar
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" style="padding: 1rem; text-align: center; color: var(--ft-text-muted);">
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
    document.addEventListener("DOMContentLoaded", function () {
        /* ===== CONFIRMACIÓN ELIMINAR ===== */
        document.querySelectorAll(".delete-form").forEach(form => {
            form.addEventListener("submit", function (event) {
                event.preventDefault();
                const formElement = this;

                Swal.fire({
                    title: '¿Estás seguro?',
                    text: 'Esta acción no se puede deshacer.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Sí, eliminar',
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

        /* ===== BUSCADOR ===== */
        const searchInput = document.getElementById('cartasSearch');
        const rows = Array.from(document.querySelectorAll('#cartasTableBody .ft-row'));

        if (searchInput) {
            searchInput.addEventListener('input', function () {
                const query = this.value.toLowerCase().trim();
                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(query) ? '' : 'none';
                });
            });
        }

        /* ===== BOTÓN AGREGAR: SOLO + EN SCROLL (DESKTOP) ===== */
        const addBtn = document.getElementById('addCartaBtn');

        function toggleAddBtnOnScroll() {
            if (!addBtn) return;

            // Solo aplicar comportamiento compacto si no es móvil (ancho > 640)
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

<script>
    // Animación del botón de descarga (visual)
    document.querySelectorAll('.button').forEach(button => {

        let duration = 3000,
            svg = button.querySelector('svg'),
            svgPath = new Proxy({
                y: null,
                smoothing: null
            }, {
                set(target, key, value) {
                    target[key] = value;
                    if (target.y !== null && target.smoothing !== null) {
                        svg.innerHTML = getPath(target.y, target.smoothing, null);
                    }
                    return true;
                },
                get(target, key) {
                    return target[key];
                }
            });

        button.style.setProperty('--duration', duration);

        svgPath.y = 20;
        svgPath.smoothing = 0;

        button.addEventListener('click', e => {
            if (button.classList.contains('loading')) return;

            button.classList.add('loading');

            gsap.to(svgPath, {
                smoothing: .3,
                duration: duration * .065 / 1000
            });

            gsap.to(svgPath, {
                y: 12,
                duration: duration * .265 / 1000,
                delay: duration * .065 / 1000,
                ease: Elastic.easeOut.config(1.12, .4)
            });

            setTimeout(() => {
                svg.innerHTML = getPath(0, 0, [
                    [3, 14],
                    [8, 19],
                    [21, 6]
                ]);
            }, duration / 2);
        });
    });

    function getPoint(point, i, a, smoothing) {
        let cp = (current, previous, next, reverse) => {
                let p = previous || current,
                    n = next || current,
                    o = {
                        length: Math.sqrt(Math.pow(n[0] - p[0], 2) + Math.pow(n[1] - p[1], 2)),
                        angle: Math.atan2(n[1] - p[1], n[0] - p[0])
                    },
                    angle = o.angle + (reverse ? Math.PI : 0),
                    length = o.length * smoothing;
                return [current[0] + Math.cos(angle) * length, current[1] + Math.sin(angle) * length];
            },
            cps = cp(a[i - 1], a[i - 2], point, false),
            cpe = cp(point, a[i - 1], a[i + 1], true);
        return `C ${cps[0]},${cps[1]} ${cpe[0]},${cpe[1]} ${point[0]},${point[1]}`;
    }

    function getPath(update, smoothing, pointsNew) {
        let points = pointsNew ? pointsNew : [
                [4, 12],
                [12, update],
                [20, 12]
            ],
            d = points.reduce((acc, point, i, a) =>
                i === 0 ? `M ${point[0]},${point[1]}` : `${acc} ${getPoint(point, i, a, smoothing)}`, '');
        return `<path d="${d}" />`;
    }
</script>

@endsection
