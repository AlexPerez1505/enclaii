@once
<style>
    :root {
        --submenu-desktop-top: 90px;
        --submenu-tablet-top: 85px;

        /*
         * En móvil este valor baja el submenu para que NO tape el header global.
         * Si tu header global es más alto, sube este valor a 76px u 82px.
         */
        --submenu-mobile-top: 64px;
        --submenu-mobile-height: 64px;
        --submenu-mobile-space: 148px;
    }

    .app-submenu {
        position: fixed;
        top: var(--submenu-desktop-top);
        left: 24px;
        width: 80px;
        min-height: 680px;

        background: rgba(255, 255, 255, 0.72);
        backdrop-filter: blur(16px) saturate(135%);
        -webkit-backdrop-filter: blur(16px) saturate(135%);

        border: 1px solid rgba(255, 255, 255, 0.82);
        border-radius: 28px;

        display: flex;
        flex-direction: column;
        align-items: center;

        padding: 24px 0;
        gap: 16px;

        box-shadow:
            0 10px 40px -10px rgba(0, 0, 0, 0.12),
            inset 0 1px 0 rgba(255, 255, 255, 1);

        z-index: 980;

        transition:
            transform 0.35s cubic-bezier(0.16, 1, 0.3, 1),
            opacity 0.25s ease,
            top 0.25s ease;

        isolation: isolate;
        will-change: transform;
        transform: translateZ(0);
        -webkit-transform: translateZ(0);
        backface-visibility: hidden;
        -webkit-backface-visibility: hidden;
    }

    .app-submenu-spacer {
        display: none;
        width: 100%;
        height: 0;
        pointer-events: none;
    }

    .submenu-item {
        width: 48px;
        height: 48px;
        border-radius: 14px;

        display: flex;
        align-items: center;
        justify-content: center;

        text-decoration: none;
        color: #64748b;
        background: transparent;

        font-size: 20px;
        position: relative;

        border: 0;
        outline: 0;

        flex: 0 0 auto;

        transition:
            background 0.25s ease,
            color 0.25s ease,
            box-shadow 0.25s ease,
            transform 0.25s ease;
    }

    .submenu-item i {
        line-height: 1;
        display: inline-block;
        transition: transform 0.25s cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    .submenu-item:hover {
        background: rgba(0, 0, 0, 0.06);
        color: #171717;
    }

    .submenu-item:hover i {
        transform: scale(1.22);
    }

    .submenu-item.active {
        background: #171717;
        color: #ffffff;
        box-shadow: 0 8px 20px -6px rgba(0, 0, 0, 0.4);
    }

    .submenu-item.active:hover i {
        transform: scale(1);
    }

    .submenu-item::after {
        content: attr(title);
        position: absolute;

        left: calc(100% + 16px);
        top: 50%;

        transform: translateY(-50%) translateX(-10px);

        background: #171717;
        color: #ffffff;

        font-size: 13px;
        font-weight: 500;

        padding: 6px 14px;
        border-radius: 8px;

        white-space: nowrap;

        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);

        opacity: 0;
        visibility: hidden;
        pointer-events: none;

        transition:
            opacity 0.18s ease,
            visibility 0.18s ease,
            transform 0.18s ease;

        letter-spacing: 0.2px;
        z-index: 990;
    }

    .submenu-item:hover::after {
        opacity: 1;
        visibility: visible;
        transform: translateY(-50%) translateX(0);
    }

    .submenu-separator {
        width: 24px;
        height: 1px;
        background: rgba(0, 0, 0, 0.08);
        border-radius: 2px;
        margin: 4px 0;
        flex: 0 0 auto;
    }

    @supports not ((backdrop-filter: blur(16px)) or (-webkit-backdrop-filter: blur(16px))) {
        .app-submenu {
            background: rgba(255, 255, 255, 0.96);
        }
    }

    @media (max-width: 991.98px) and (min-width: 768px) {
        .app-submenu {
            top: var(--submenu-tablet-top);
            left: 16px;

            width: 70px;
            min-height: 600px;

            border-radius: 24px;
            padding: 20px 0;
            gap: 12px;
            z-index: 980;
        }

        .submenu-item {
            width: 44px;
            height: 44px;
            font-size: 18px;
            border-radius: 12px;
        }

        .submenu-item::after {
            left: calc(100% + 12px);
            font-size: 12px;
            padding: 5px 12px;
        }
    }

    @media (max-width: 767.98px) {
        .app-submenu-spacer {
            display: block;
            height: calc(var(--submenu-mobile-space) + env(safe-area-inset-top, 0px));
            min-height: 148px;
        }

        .app-submenu {
            position: fixed;

            top: calc(var(--submenu-mobile-top) + env(safe-area-inset-top, 0px));
            left: 50%;
            right: auto;

            width: calc(100vw - 18px);
            width: calc(100dvw - 18px);
            max-width: 560px;

            height: var(--submenu-mobile-height);
            min-height: var(--submenu-mobile-height);
            max-height: var(--submenu-mobile-height);

            display: flex;
            flex-direction: row;
            align-items: center;
            justify-content: flex-start;

            padding: 0 14px;
            gap: 8px;

            border-radius: 999px;

            overflow-x: auto;
            overflow-y: hidden;

            white-space: nowrap;

            /*
             * Menor que el header global para que el header quede encima.
             * Si tu header global usa z-index 1000 o más, este 980 queda perfecto.
             */
            z-index: 980;

            scrollbar-width: none;
            -ms-overflow-style: none;

            transform: translateX(-50%) translateZ(0);
            -webkit-transform: translateX(-50%) translateZ(0);

            -webkit-overflow-scrolling: touch;
        }

        .app-submenu::-webkit-scrollbar {
            display: none;
            width: 0;
            height: 0;
        }

        .app-submenu.is-hidden {
            transform: translateX(-50%) translateY(-160px) translateZ(0);
            -webkit-transform: translateX(-50%) translateY(-160px) translateZ(0);
            opacity: 0;
            pointer-events: none;
        }

        .submenu-item {
            width: 42px;
            height: 42px;
            min-width: 42px;
            max-width: 42px;

            font-size: 18px;
            border-radius: 12px;

            flex: 0 0 42px;
        }

        .submenu-item:hover i {
            transform: none;
        }

        .submenu-separator {
            width: 1px;
            min-width: 1px;
            height: 24px;

            margin: 0 4px;

            flex: 0 0 1px;
        }

        .submenu-item::after {
            display: none;
        }
    }

    @supports (-webkit-touch-callout: none) {
        @media (max-width: 767.98px) {
            .app-submenu-spacer {
                height: calc(156px + env(safe-area-inset-top, 0px));
                min-height: 156px;
            }

            .app-submenu {
                top: calc(68px + env(safe-area-inset-top, 0px));
                transform: translate3d(-50%, 0, 0);
                -webkit-transform: translate3d(-50%, 0, 0);
            }

            .app-submenu.is-hidden {
                transform: translate3d(-50%, -160px, 0);
                -webkit-transform: translate3d(-50%, -160px, 0);
            }
        }
    }
</style>
@endonce

<div class="app-submenu">
    <a href="{{ route('dashboard') }}"
       class="submenu-item {{ request()->routeIs('dashboard') ? 'active' : '' }}"
       title="Cotizaciones Rentas">
        <i class="bi bi-layers"></i>
    </a>

    <div class="submenu-separator"></div>

    <a href="{{ route('propuestas.index') }}"
       class="submenu-item {{ request()->routeIs('propuestas.*') ? 'active' : '' }}"
       title="Cotizaciones Ventas">
        <i class="bi bi-tag"></i>
    </a>

    <div class="submenu-separator"></div>

    <a href="{{ route('ordenes.index') }}"
       class="submenu-item {{ request()->routeIs('ordenes.*') ? 'active' : '' }}"
       title="Órdenes de Servicio">
        <i class="bi bi-clipboard-data"></i>
    </a>

    <a href="{{ route('ventas.index') }}"
       class="submenu-item {{ request()->routeIs('ventas.index') ? 'active' : '' }}"
       title="Ventas Realizadas">
        <i class="bi bi-cart3"></i>
    </a>

    <a href="{{ route('ventas.deudores') }}"
       class="submenu-item {{ request()->routeIs('ventas.deudores') ? 'active' : '' }}"
       title="Control de Deudores">
        <i class="bi bi-cash-stack"></i>
    </a>

    <div class="submenu-separator"></div>

    <a href="{{ route('clientes.index') }}"
       class="submenu-item {{ request()->routeIs('clientes.*') ? 'active' : '' }}"
       title="Cartera de Clientes">
        <i class="bi bi-person-vcard"></i>
    </a>
</div>

<div class="app-submenu-spacer" aria-hidden="true"></div>

@once
<script>
document.addEventListener('DOMContentLoaded', () => {
    const submenu = document.querySelector('.app-submenu');

    if (!submenu) return;

    let lastScrollTop = window.pageYOffset || document.documentElement.scrollTop;
    let ticking = false;

    const threshold = 15;
    const headerOffset = 120;

    function isMobile() {
        return window.matchMedia('(max-width: 767.98px)').matches;
    }

    function updateSubmenu() {
        if (!isMobile()) {
            submenu.classList.remove('is-hidden');
            ticking = false;
            return;
        }

        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;

        if (scrollTop < 0) {
            ticking = false;
            return;
        }

        if (Math.abs(lastScrollTop - scrollTop) <= threshold) {
            ticking = false;
            return;
        }

        if (scrollTop > lastScrollTop && scrollTop > headerOffset) {
            submenu.classList.add('is-hidden');
        } else {
            submenu.classList.remove('is-hidden');
        }

        lastScrollTop = scrollTop <= 0 ? 0 : scrollTop;
        ticking = false;
    }

    window.addEventListener('scroll', () => {
        if (!ticking) {
            window.requestAnimationFrame(updateSubmenu);
            ticking = true;
        }
    }, { passive: true });

    window.addEventListener('resize', () => {
        if (!isMobile()) {
            submenu.classList.remove('is-hidden');
        }
    }, { passive: true });
});
</script>
@endonce