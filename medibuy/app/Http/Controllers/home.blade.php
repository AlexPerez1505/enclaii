<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Inicio</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

  <style>
    @import url("https://fonts.googleapis.com/css?family=Poppins:100,300,400,500,600,700,800,900&display=swap");
    *{ margin:0; padding:0; box-sizing:border-box; font-family:"Poppins",sans-serif; }
    body{ background-color:transparent; margin:0; padding:0; color:#1a1a1a; }

    /* ================= FONDO ANIMADO: PALETA AZUL/CYAN/SLATE (PASTEL PRO) ================= */
    :root{
      --neo-blue:#2563EB;  /* azul principal */
      --neo-cyan:#0EA5E9;  /* cian fresco */
      --neo-slate:#64748B; /* gris azulado */
    }
    .animated-background{ position:fixed; inset:0; z-index:0; pointer-events:none; overflow:hidden; }
    .wave{
      position:absolute; inset:0;
      /* Fallback sin color-mix */
      background: linear-gradient(180deg,
        rgba(14,165,233,.16) 0%,
        rgba(255,255,255,.55) 50%,
        rgba(37,99,235,.16) 100%
      );
      box-shadow: inset 0 0 60px rgba(0,0,0,.05);
    }
    /* Si hay soporte de color-mix, mezcla más fina y moderna */
    @supports (background: color-mix(in oklab, red 50%, white)){
      .wave{
        background: linear-gradient(180deg,
          color-mix(in oklab, var(--neo-cyan) 18%, transparent) 0%,
          color-mix(in oklab, white 45%, transparent) 50%,
          color-mix(in oklab, var(--neo-blue) 18%, transparent) 100%
        );
      }
    }
    .wave span{
      position:absolute; width:325vh; height:325vh; top:0; left:50%;
      transform:translate(-50%,-75%);
    }
    /* burbujas pastel */
    .wave span:nth-child(1){ border-radius:45%; background:rgba(14,165,233,.28); animation:rotatePulse 9s linear infinite; }
    .wave span:nth-child(2){ border-radius:40%; background:rgba(255,255,255,.50); animation:rotatePulse 16s linear infinite reverse; }
    .wave span:nth-child(3){ border-radius:42.5%; background:rgba(100,116,139,.22); animation:rotatePulse 22s linear infinite; }
    @keyframes rotatePulse{
      0%   { transform:translate(-50%,-75%) rotate(0deg)   scale(1); }
      50%  { transform:translate(-50%,-75%) rotate(180deg) scale(1.03); }
      100% { transform:translate(-50%,-75%) rotate(360deg) scale(1); }
    }
    /* ==================================================================== */

    body > *:not(.animated-background){ position:relative; z-index:1; }

    /* Header (igual) */
    header{
      position:fixed; top:0!important; left:0!important; width:100%!important; z-index:1000!important;
      background:#f2f2f2; padding:16px 20px!important; box-shadow:0 2px 8px rgba(0,0,0,.05);
      display:flex!important; justify-content:space-between!important; align-items:center;
    }
    header img{ height:40px; }

    /* Buscador y grid (tuyos) */
    .buscador{ max-width:500px; margin:90px auto 20px; padding:0 20px; margin-top:15px; }
    .buscador input{ width:100%; padding:10px 14px; border-radius:12px; border:1px solid #ccc; font-size:14px; }

    h3{ margin:20px 20px 10px; color:#fff; text-shadow:1px 1px 2px #000; }

    .menu-grid{
      display:grid; grid-template-columns:repeat(auto-fit, minmax(140px, 1fr));
      gap:16px; padding:0 20px 40px; max-width:1000px; margin:0 auto;
    }

    /* ================= TARJETAS: base + animaciones avanzadas ================= */
    .menu-card{
      background:#fff; border-radius:16px; box-shadow:0 4px 12px rgba(0,0,0,.05);
      padding:20px; text-align:center; text-decoration:none; color:#333; position:relative;
      border:1px solid transparent;
      /* SI NO HAY JS: SIEMPRE VISIBLES */
      opacity:1; transform:none;
      transition:
        transform .35s cubic-bezier(.2,.8,.2,1),
        box-shadow .35s cubic-bezier(.2,.8,.2,1),
        border-color .35s linear,
        filter .35s linear,
        opacity .35s ease;
      /* Flotación sutil desincronizada */
      animation: float 7.5s ease-in-out infinite;
      animation-delay: var(--float-delay, 0ms);
      will-change: transform, box-shadow, filter;
      transform-style: preserve-3d;
      perspective: 800px;
    }
    .menu-card i{ font-size:28px; margin-bottom:10px; color:#2a2a2a; transition: transform .35s ease, filter .35s ease, color .25s; }

    .menu-card::after{
      content:""; position:absolute; inset:-1px; border-radius:16px; pointer-events:none;
      background: linear-gradient(120deg, transparent 20%, rgba(37,99,235,.10) 45%, rgba(37,99,235,.18) 55%, transparent 80%);
      transform: translateX(-120%); opacity:0;
    }
    .menu-card:hover::after{ animation: shine 900ms ease forwards; }
    .menu-card:hover{
      transform:
        translate3d(var(--tx,0), var(--ty,0), 0)
        rotateX(var(--rx, 0deg))
        rotateY(var(--ry, 0deg))
        scale(1.04);
      box-shadow:0 16px 32px rgba(0,0,0,.14), 0 0 16px rgba(37,99,235,.18);
      border-color: rgba(37,99,235,.22);
      background:#f9fbff;
    }
    .menu-card:hover i{
      transform:translateZ(16px) translateY(-2px) scale(1.06);
      color:#2563EB; filter: drop-shadow(0 4px 8px rgba(37,99,235,.25));
    }
    @keyframes shine{
      0%{ transform: translateX(-120%); opacity:0; }
      20%{ opacity:1; }
      100%{ transform: translateX(120%); opacity:0; }
    }
    @keyframes float{
      0%{ transform: translateY(0); }
      50%{ transform: translateY(-4px); }
      100%{ transform: translateY(0); }
    }

    /* Entrada “reveal” (se activa con body.enhanced) */
    body.enhanced .menu-card.reveal{
      opacity:0; transform: translateY(24px) scale(.985);
      animation: revealUp 650ms cubic-bezier(.2,.8,.2,1) both;
      animation-delay: calc(var(--stagger, 0) * 60ms);
    }
    @keyframes revealUp{
      from{ opacity:0; transform: translateY(24px) scale(.985); }
      to  { opacity:1; transform: translateY(0) scale(1); }
    }

    @media (prefers-reduced-motion: reduce){
      .menu-card, .menu-card::after{ animation: none !important; transition: none !important; }
      .menu-card{ opacity:1 !important; transform:none !important; }
    }

    .badge{ background:#e53935; color:#fff; font-size:10px; padding:4px 6px; border-radius:8px; position:absolute; top:10px; right:10px; }

    @media (max-width:600px){
      .logo-responsive{ display:none; }
      .menu-grid{ padding:16px; }
    }

    .welcome-wrapper{ display:flex; justify-content:center; align-items:center; height:20px; overflow:hidden; }
    .masked-text{
      font-size:1rem; font-weight:bold; color:transparent;
      background-image:url('https://images.unsplash.com/photo-1732535725600-f805d8b33c9c?q=80&w=1470&auto=format&fit=crop&ixlib=rb-4.0.3');
      background-size:200%; background-position:0 50%; -webkit-background-clip:text; background-clip:text; -webkit-text-fill-color:transparent;
      animation:animate-background 5s infinite alternate linear;
    }
    @keyframes animate-background{ 0%{background-position:0 50%} 100%{background-position:100% 50%} }

    /* Botón flotante (igual) */
    .registro-btn{ position:fixed; bottom:20px; right:20px; z-index:9999; width:60px; height:60px; border-radius:50%;
      background:#007bff; display:flex; align-items:center; justify-content:center; box-shadow:0 2px 5px rgba(0,0,0,.3);
      animation:breathe 2s ease-in-out infinite; transition:background .2s; text-decoration:none; }
    .registro-btn:hover{ background:#0056b3; }
    .registro-btn i{ color:#fff; font-size:28px; animation:beat 2s ease-in-out infinite; text-decoration:none; }
    @keyframes breathe{0%{box-shadow:0 0 0 0 rgba(0,123,255,.5)}70%{box-shadow:0 0 0 15px rgba(0,123,255,0)}100%{box-shadow:0 0 0 0 rgba(0,0,0,0)}}
    @keyframes beat{0%{transform:scale(1)}50%{transform:scale(1.2)}100%{transform:scale(1)}}
  </style>
</head>
<body>

  <!-- Fondo -->
  <section class="animated-background">
    <div class="wave"><span></span><span></span><span></span></div>
  </section>

  <!-- Header -->
  <header>
    <img src="{{ asset('images/logomedy.png') }}" alt="Logo" class="logo-responsive">
    <div class="welcome-wrapper">
      <div class="masked-text"><strong>Bienvenido, {{ Auth::user()->name }}</strong></div>
    </div>
  </header>

  <!-- Buscador -->
  <div class="buscador">
    <input type="text" id="buscador" placeholder="Buscar..." oninput="filtrarMenu()">
  </div>

  {{-- FIX: evita error si $modulosRecientes no viene del controlador --}}
  @php
    /** @var \Illuminate\Support\Collection|array|null $modulosRecientes */
    $modulosRecientes = $modulosRecientes ?? collect();
    if (!($modulosRecientes instanceof \Illuminate\Support\Collection)) {
        $modulosRecientes = collect($modulosRecientes);
    }
    $rutasRecientes = $modulosRecientes->pluck('ruta')->filter()->map(fn($r)=>url($r))->values()->all();
  @endphp

  @if($modulosRecientes->isNotEmpty())
    <h3>Recientes</h3>
    <div class="menu-grid">
      @foreach($modulosRecientes as $modulo)
        <a href="{{ url($modulo->ruta) }}" class="menu-card">
          <i class="{{ $modulo->icono ?: 'fas fa-star' }}"></i>
          <p>{{ $modulo->nombre }}</p>
        </a>
      @endforeach
    </div>
  @endif

  <h3>Tus accesos</h3>
  <div class="menu-grid" id="menuGrid">

    @if(Auth::id() == 18)

      @if(!in_array(url('/ordenes'), $rutasRecientes))
      <a href="{{ url('/ordenes') }}" class="menu-card"><i class="fas fa-tools"></i><p>Mantenimiento</p></a>
      @endif

      @if(!in_array(url('/perfil'), $rutasRecientes))
      <a href="{{ url('/perfil') }}" class="menu-card"><i class="fas fa-user-circle"></i><p>Perfil</p></a>
      @endif

    @else

      @if(!in_array(url('/publicaciones'), $rutasRecientes))
      <a href="{{ url('/publicaciones') }}" class="menu-card"><i class="fas fa-newspaper"></i><p>Cuentas Bancarias</p></a>
      @endif

      @if(!in_array(url('/inventario'), $rutasRecientes))
      <a href="{{ url('/inventario') }}" class="menu-card"><i class="fas fa-box"></i><p>Inventario</p></a>
      @endif

      @if(Auth::user()->hasRole('admin') || Auth::user()->hasRole('editor'))
        @if(!in_array(url('/bento'), $rutasRecientes))
        <a href="{{ url('/bento') }}" class="menu-card"><i class="fas fa-receipt"></i><p>Menu Contabilidad</p></a>
        @endif
       
       
      
        @if(!in_array(url('/productos/cards'), $rutasRecientes))
        <a href="{{ url('/productos/cards') }}" class="menu-card"><i class="fas fa-cubes"></i><p>Productos</p></a>
        @endif
        @if(!in_array(url('/promos/whatsapp/direct'), $rutasRecientes))
        <a href="{{ url('/promos/whatsapp/direct') }}" class="menu-card"><i class="fas fa-ticket"></i><p>Promocionales</p></a>
        @endif
        @if(!in_array(url('/whatsapp/inbox'), $rutasRecientes))
        <a href="{{ url('/whatsapp/inbox') }}" class="menu-card"><i class="fab fa-whatsapp"></i><p>WhatsApp Help Desk</p></a>
        @endif
      @endif

    

      @if(!in_array(url('/agenda'), $rutasRecientes))
      <a href="{{ url('/agenda') }}" class="menu-card"><i class="fas fa-calendar-alt"></i><p>Agenda</p></a>
      @endif

      @if(!in_array(url('/camionetas'), $rutasRecientes))
      <a href="{{ url('/camionetas') }}" class="menu-card"><i class="fas fa-truck"></i><p>Camionetas</p></a>
      @endif

      @if(!in_array(url('/perfil'), $rutasRecientes))
      <a href="{{ url('/perfil') }}" class="menu-card"><i class="fas fa-user-circle"></i><p>Perfil</p></a>
      @endif

      @if(!in_array(route('fichas.index'), $rutasRecientes))
      <a href="{{ route('fichas.index') }}" class="menu-card"><i class="fas fa-file-alt"></i><p>Fichas Técnicas</p></a>
      @endif

      @if(!in_array(url('/carta-garantia'), $rutasRecientes))
      <a href="{{ url('/carta-garantia') }}" class="menu-card"><i class="fas fa-shield-alt"></i><p>Cartas de Garantía</p></a>
      @endif

      @if(!in_array(url('/servicio'), $rutasRecientes))
      <a href="{{ url('/servicio') }}" class="menu-card"><i class="fas fa-clipboard-list"></i><p>Registro Ext.</p></a>
      @endif
      @if(!in_array(url('/inventario/servicio'), $rutasRecientes))
      <a href="{{ url('/inventario/servicio') }}" class="menu-card"><i class="fas fa-warehouse"></i><p>Inventario Ext.</p></a>
      @endif

      @if(!in_array(url('/envios-gastos'), $rutasRecientes))
      <a href="{{ url('/envios-gastos') }}" class="menu-card"><i class="fas fa-truck-ramp-box"></i><p>Paqueterías</p></a>
      @endif

      @auth
        @if(!in_array(route('entregas.index'), $rutasRecientes))
        <a href="{{ route('entregas.index') }}" class="menu-card"><i class="fas fa-barcode"></i><p>Guías</p></a>
        @endif
      @endauth

      @if(Auth::check())
        @if(!in_array(route('solicitudes.create'), $rutasRecientes))
        <a href="{{ route('solicitudes.create') }}" class="menu-card"><i class="fas fa-box-open"></i><p>Solicitar Material</p></a>
        @endif

        @if(!in_array(route('solicitudes.index'), $rutasRecientes))
        <a href="{{ route('solicitudes.index') }}" class="menu-card"><i class="fas fa-inbox"></i><p>Mis Solicitudes</p></a>
        @endif

        @if(Auth::user()->hasRole('admin'))
          @if(!in_array(route('solicitudes.admin'), $rutasRecientes))
          <a href="{{ route('solicitudes.admin') }}" class="menu-card"><i class="fas fa-list-check"></i><p>Solicitudes Pendientes</p></a>
          @endif
        @endif
      @endif

      @if(Auth::user()->hasRole('admin'))
        @if(!in_array(url('/pedidos'), $rutasRecientes))
        <a href="{{ url('/pedidos') }}" class="menu-card"><i class="fas fa-shopping-cart"></i><p>Pedidos</p></a>
        @endif
      @endif

      @if(!in_array(url('/recepciones'), $rutasRecientes))
      <a href="{{ url('/recepciones') }}" class="menu-card"><i class="fas fa-file-import"></i><p>Mis Recepciones</p></a>
      @endif

      @if(!in_array(url('/recepciones/timeline'), $rutasRecientes))
      <a href="{{ url('/recepciones/timeline') }}" class="menu-card"><i class="fas fa-history"></i><p>Historial Global</p></a>
      @endif

      @if(!in_array(url('/cuentas'), $rutasRecientes))
      <a href="{{ url('/cuentas') }}" class="menu-card"><i class="fas fa-wallet"></i><p>Viáticos</p></a>
      @endif

      @if(!in_array(route('prestamos.index'), $rutasRecientes))
      <a href="{{ route('prestamos.index') }}" class="menu-card"><i class="fas fa-money-check-alt"></i><p>Préstamos</p></a>
      @endif

      @if(Auth::user()->hasRole('admin'))
        @if(!in_array(url('/usuarios'), $rutasRecientes))
        <a href="{{ url('/usuarios') }}" class="menu-card"><i class="fas fa-users-cog"></i><p>Usuarios</p></a>
        @endif

        @if(!in_array(route('asistencias.index'), $rutasRecientes))
        <a href="{{ route('asistencias.index') }}" class="menu-card"><i class="fas fa-user-check"></i><p>Asistencias</p></a>
        @endif

        @if(!in_array(url('/transactions'), $rutasRecientes))
        <a href="{{ url('/transactions') }}" class="menu-card"><i class="fas fa-cash-register"></i><p>Movimientos de caja</p></a>
        @endif

        <!-- Botón flotante -->
        <a href="/inventario/buscar" target="_self" class="registro-btn" title="Ir a registro">
          <i class="bi bi-search"></i>
        </a>
      @endif

    @endif

  </div>

  <script>
    // Filtro manteniendo el grid
    function filtrarMenu(){
      const filtro = document.getElementById("buscador").value.toLowerCase();
      const cards = document.querySelectorAll(".menu-card");
      cards.forEach(card => {
        const texto = card.innerText.toLowerCase();
        card.style.display = texto.includes(filtro) ? "" : "none";
      });
    }

    // Mejora progresiva: animaciones avanzadas sin ocultar contenido si falla JS
    document.addEventListener('DOMContentLoaded', () => {
      document.body.classList.add('enhanced');

      const cards = Array.from(document.querySelectorAll('.menu-card'));
      // Delays de flotación y reveal
      cards.forEach((c, i) => {
        c.style.setProperty('--float-delay', `${(i%7)*120}ms`);
        c.style.setProperty('--stagger', i);
        requestAnimationFrame(() => c.classList.add('reveal'));
      });

      // Tilt 3D + desplazamiento magnético
      const maxTilt = 8, maxMove = 6;
      const setVars = (el, rx, ry, tx, ty) => {
        el.style.setProperty('--rx', rx + 'deg');
        el.style.setProperty('--ry', ry + 'deg');
        el.style.setProperty('--tx', tx + 'px');
        el.style.setProperty('--ty', ty + 'px');
      };
      cards.forEach(card => {
        let rect;
        const updateRect = () => rect = card.getBoundingClientRect();
        updateRect();
        window.addEventListener('resize', updateRect, {passive:true});
        const onMove = (x, y) => {
          const cx = rect.left + rect.width/2, cy = rect.top + rect.height/2;
          const dx = (x - cx) / (rect.width/2), dy = (y - cy) / (rect.height/2);
          setVars(card, (-dy*maxTilt), (dx*maxTilt), (dx*maxMove), (dy*maxMove));
        };
        card.addEventListener('pointermove', e=>onMove(e.clientX,e.clientY));
        card.addEventListener('pointerleave', ()=>setVars(card,0,0,0,0));
        card.addEventListener('touchmove', e=>{ if(e.touches[0]) onMove(e.touches[0].clientX,e.touches[0].clientY); }, {passive:true});
        card.addEventListener('touchend', ()=>setVars(card,0,0,0,0));
      });
    });
  </script>
</body>
</html>