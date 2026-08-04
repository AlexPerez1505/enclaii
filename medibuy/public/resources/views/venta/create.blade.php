@extends('layouts.app')
@section('title', 'Remisión')
@section('titulo', 'Remisión')
@section('content')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> 
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="stylesheet" href="{{ asset('css/ventascreate.css') }}?v={{ time() }}">

<style>
:root{
  --ink:#0f172a;
  --muted:#6b7280;
  --line:#e2e8f0;
  --accent:#4f46e5;
  --accent-soft:rgba(79,70,229,0.06);
  --card-border:rgba(209,213,219,0.8);
  --radius:18px;
  --ease:cubic-bezier(.22,1,.36,1);
}

/* 🎨 Fondo degradado */
body{
  background: linear-gradient(90deg, #d3b791,#ffffff);
  color:var(--ink);
  font-family:"Söhne","Circular Std","Poppins",system-ui,-apple-system,
               "Segoe UI","Helvetica Neue",Arial,sans-serif;
  -webkit-font-smoothing:antialiased;
}

/* Tipografía global */
input,
button,
select,
textarea,
.badge,
.alert,
.btn,
.form-control,
.modal,
.modal-title,
.swal2-popup.custom-swal{
  font-family:"Söhne","Circular Std","Poppins",system-ui,-apple-system,
               "Segoe UI","Helvetica Neue",Arial,sans-serif;
}

/* 📦 Cards / contenedores con LIQUID GLASS */
.modern-card{
  position:relative;
  border-radius:var(--radius);
  border:1px solid var(--card-border);
  background-color:rgba(255,255,255,0.47);
  backdrop-filter:blur(18px) saturate(180%);
  -webkit-backdrop-filter:blur(18px) saturate(180%);
  box-shadow:0 22px 50px rgba(15,23,42,0.25);
  overflow:visible;   /* para que el dropdown pueda salir */
  z-index:1;
}

/* Glow interno */
.modern-card::before{
  content:"";
  position:absolute;
  inset:0;
  pointer-events:none;
  border-radius:inherit;
  background:
    radial-gradient(circle at 0 0, rgba(255,255,255,0.70) 0, transparent 60%),
    radial-gradient(circle at 100% 0, rgba(79,70,229,0.25) 0, transparent 65%),
    radial-gradient(circle at 100% 100%, rgba(255,255,255,0.4) 0, transparent 60%);
  opacity:.35;
  mix-blend-mode:soft-light;
}
.modern-card > *{
  position:relative;
  z-index:1;
}

/* Cuando algo dentro tiene foco, sube la card completa */
.modern-card:focus-within{
  z-index:40;
}

/* Header de las cards */
.modern-header{
  border-bottom:1px solid rgba(148,163,184,0.35);
  padding:.65rem .95rem;
  font-weight:600;
  font-size:.95rem;
  color:var(--ink);
  background:
    linear-gradient(120deg, rgba(255,255,255,0.88), rgba(248,250,252,0.96));
}

/* Inputs / selects / textareas dentro de glass */
.modern-input,
.modern-select,
.modern-textarea{
  border-radius:12px;
  border:1px solid rgba(148,163,184,0.75);
  font-size:.95rem;
  padding:.45rem .7rem;
  background:rgba(255,255,255,0.9);
  transition:border-color .18s var(--ease),
             box-shadow .18s var(--ease),
             background-color .18s var(--ease),
             transform .08s;
}
.modern-input:focus,
.modern-select:focus,
.modern-textarea:focus{
  border-color:var(--accent);
  outline:none;
  box-shadow:0 0 0 1px rgba(79,70,229,0.35),
             0 14px 30px rgba(79,70,229,0.25);
  background:#ffffff;
  transform:translateY(-1px);
}

/* Select cerrado con efecto glass */
.modern-select{
  appearance:none;
  -webkit-appearance:none;
  -moz-appearance:none;
  box-shadow:0 18px 40px rgba(15,23,42,0.18);
  background-image:linear-gradient(
      180deg,
      transparent 0,
      transparent 60%,
      rgba(148,163,184,0.16) 100%
  );
  background-repeat:no-repeat;
}
.modern-select::-ms-expand{
  display:none;
}

/* === DROPDOWN TIPO MILK-GLASS (CLIENTE / PRODUCTOS) === */
.modern-dropdown,
.dropdown-menu.modern-dropdown{
  position:absolute;
  border-radius:18px;
  padding:.45rem;
  border:1px solid rgba(255,255,255,0.75);

  /* Fondo lechoso translúcido */
  background:
    linear-gradient(135deg,
      rgba(255,255,255,0.90),
      rgba(248,250,252,0.85));
  backdrop-filter: blur(26px) saturate(190%);
  -webkit-backdrop-filter: blur(26px) saturate(190%);

  box-shadow:0 22px 50px rgba(15,23,42,0.25);
  max-height:320px;
  overflow-y:auto;     /* mantiene el scroll */
  overflow-x:hidden;
  z-index:60 !important;
}

/* Brillo suave */
.modern-dropdown::before,
.dropdown-menu.modern-dropdown::before{
  content:"";
  position:absolute;
  inset:0;
  border-radius:inherit;
  pointer-events:none;
  background:
    radial-gradient(circle at 0 0,
      rgba(255,255,255,0.55) 0, transparent 60%),
    radial-gradient(circle at 100% 0,
      rgba(255,255,255,0.35) 0, transparent 60%);
  opacity:.7;
  mix-blend-mode:soft-light;
}

/* Items del dropdown */
.modern-dropdown-item{
  position:relative;
  z-index:1;
  border-radius:10px;
  padding:.45rem .7rem;
  font-size:.9rem;
  color:#111827;
  display:flex;
  align-items:center;
  gap:.5rem;
  background:transparent;
}

/* Hover con velo suave */
.modern-dropdown-item:hover{
  background:rgba(255,255,255,0.45);
}

.modern-dropdown > *,
.dropdown-menu.modern-dropdown > *{
  position:relative;
  z-index:1;
}

/* Imagen de producto en dropdown */
.modern-product-img{
  border-radius:10px;
  box-shadow:0 10px 24px rgba(15,23,42,0.3);
}
.modern-product-info{ font-size:.82rem; }
.modern-product-price{ font-weight:500; }

/* ============================
   TABLA PRODUCTOS SELECCIONADOS
   ============================ */

.modern-table{
  width:100%;
  border-collapse:separate;
  border-spacing:0;
  font-size:.85rem;
  margin-bottom:0;
}

/* Encabezados */
.modern-table thead th{
  padding:.7rem .9rem;
  border-bottom:1px solid rgba(226,232,240,0.95);
  font-weight:600;
  color:#4b5563;
  background:linear-gradient(
    135deg,
    rgba(255,255,255,0.96),
    rgba(248,250,252,0.96)
  );
  white-space:nowrap;
}

/* esquinas superiores suaves */
.modern-table thead th:first-child{
  border-top-left-radius:14px;
}
.modern-table thead th:last-child{
  border-top-right-radius:14px;
}

/* Celdas */
.modern-table tbody td{
  padding:.55rem .9rem;
  vertical-align:middle;
  border-top:1px solid rgba(226,232,240,0.9);
  color:#111827;
}

/* Filas minimalistas */
.modern-table tbody tr:nth-child(odd){
  background:rgba(255,255,255,0.75);
}
.modern-table tbody tr:nth-child(even){
  background:rgba(248,250,252,0.85);
}
.modern-table tbody tr:hover{
  background:rgba(129,140,248,0.06);
}

/* Imagen de producto en la primera columna */
.modern-table tbody td:first-child img{
  width:54px;
  height:54px;
  border-radius:14px;
  object-fit:cover;
  box-shadow:0 8px 20px rgba(15,23,42,0.20);
}

/* Inputs dentro de la tabla (cantidad, sobreprecio, núm. serie) */
.modern-table input[type="number"],
.modern-table input[type="text"],
.modern-table .form-control{
  border-radius:10px;
  border:1px solid rgba(148,163,184,0.75);
  background:rgba(255,255,255,0.95);
  padding:.32rem .55rem;
  font-size:.82rem;
  height:36px;
  box-shadow:0 6px 14px rgba(15,23,42,0.08);
  transition:border-color .18s var(--ease),
           box-shadow .18s var(--ease),
           transform .06s;
}
.modern-table input[type="number"]:focus,
.modern-table input[type="text"]:focus,
.modern-table .form-control:focus{
  outline:none;
  border-color:var(--accent);
  box-shadow:0 0 0 1px rgba(79,70,229,0.35),
             0 10px 22px rgba(79,70,229,0.20);
  transform:translateY(-1px);
}
.modern-table input::placeholder{
  color:#9ca3af;
}

/* Subtotal con énfasis sutil */
.modern-table .subtotal-cell{
  font-weight:600;
}

/* Botón Eliminar minimal pill */
.modern-table .btn-danger{
  border-radius:999px;
  padding:.35rem .9rem;
  font-size:.78rem;
  font-weight:600;
  border:none;
  background:linear-gradient(135deg,#f97373,#ef4444);
  box-shadow:0 10px 22px rgba(239,68,68,0.30);
  transition:transform .06s ease, box-shadow .18s ease, filter .12s;
}
.modern-table .btn-danger:hover{
  filter:brightness(1.03);
  transform:translateY(-1px);
  box-shadow:0 14px 28px rgba(239,68,68,0.35);
}
.modern-table .btn-danger:active{
  transform:translateY(0);
  box-shadow:0 4px 10px rgba(239,68,68,0.25);
}

/* Badges */
.modern-badge{
  border-radius:999px;
  font-size:.7rem;
  padding:.25rem .55rem;
}

/* Resumen – valores */
.modern-value{
  font-weight:700;
  color:#111827;
}

/* Dropdown carta de garantía */
#dropdown_cartas{
  background-color:rgba(254,250,255,0.96);
  border:1px solid #ddd;
  border-radius:14px;
  box-shadow:0 16px 40px rgba(0,0,0,0.18);
}
.list-option-carta{
  cursor:pointer;
  padding:10px 14px;
  font-size:15px;
  color:#555;
  transition:background-color .18s var(--ease), transform .08s;
}
.list-option-carta:hover{
  background-color:#e1bee7;
  transform:translateY(-1px);
}
.list-option-carta strong{
  font-weight:bold;
  color:#6a1b9a;
}

/* SweetAlert custom */
.swal2-popup.custom-swal{
  border-radius:16px;
  font-size:15px;
  color:#444;
  background-color:#fdfcff;
  box-shadow:0 10px 30px rgba(0,0,0,0.08);
  padding:2rem;
}
.swal2-title.custom-title{
  font-size:22px;
  font-weight:600;
  color:#333;
  display:flex;
  align-items:center;
  gap:10px;
  justify-content:center;
}
.swal2-html-container.custom-html{
  text-align:left;
  line-height:1.8;
  color:#555;
  padding:.5rem 1rem;
}
.swal2-confirm.custom-btn{
  background-color:#a78bfa;
  color:#fff !important;
  font-weight:600;
  padding:.5rem 1.2rem;
  border-radius:12px;
  font-size:15px;
  box-shadow:0 4px 10px rgba(167,139,250,0.3);
  transition:all .2s ease-in-out;
}
.swal2-confirm.custom-btn:hover{
  background-color:#8b5cf6;
}
.swal-img-evidencia{
  width:100%;
  max-height:260px;
  object-fit:contain;
  border-radius:12px;
  margin-top:1rem;
  box-shadow:0 4px 14px rgba(0,0,0,0.1);
}

/* Contenedor preview imagen (modal producto) */
.image-container{
  width:150px;
  height:150px;
  border:2px dashed #ccc;
  border-radius:12px;
  display:flex;
  align-items:center;
  justify-content:center;
  overflow:hidden;
  cursor:pointer;
  background-color:#f9f9f9;
  transition:border-color .25s ease, background-color .18s ease;
}
.image-container:hover{
  border-color:#4a90e2;
  background-color:#f3f4ff;
}
#preview-icon{
  max-width:100%;
  max-height:100%;
  object-fit:contain;
  border-radius:10px;
  transition:transform .3s ease;
}
#preview-text{
  color:#999;
  font-size:.9rem;
  text-align:center;
}

/* Inputs del modal producto */
#formProducto input[type="text"],
#formProducto input[type="number"],
#formProducto input[type="file"],
#formProducto .form-control{
  border:1px solid #ccc;
  border-radius:10px;
  padding:8px 12px;
  font-size:1rem;
  transition:border-color .25s ease,
           box-shadow .25s ease,
           transform .06s;
}
#formProducto input[type="text"]:focus,
#formProducto input[type="number"]:focus,
#formProducto input[type="file"]:focus,
#formProducto .form-control:focus{
  border-color:#4a90e2;
  outline:none;
  box-shadow:0 0 0 1px rgba(74,144,226,0.4);
  transform:translateY(-1px);
}

/* Botón primary modal producto */
#formProducto button.btn-primary{
  background-color:#4a90e2;
  border:none;
  border-radius:999px;
  padding:10px 22px;
  font-weight:600;
  color:#fff;
  font-size:.95rem;
  transition:background-color .2s ease,
             box-shadow .18s ease,
             transform .06s;
}
#formProducto button.btn-primary:hover{
  background-color:#357ABD;
  box-shadow:0 12px 26px rgba(53,122,189,0.35);
  transform:translateY(-1px);
}

/* Botón submit principal */
button.btn.btn-success{
  border-radius:999px;
  padding:.55rem 1.4rem;
  font-weight:600;
}

/* Modal "cliente guardado" */
#cliente_creado .modal-content{
  border-radius:20px;
}
.tradein-pill-btn{
    border-radius: 999px;
    padding: 4px 16px;
    font-size: 0.85rem;
    font-weight: 600;
    border: 1px solid #b4f397;          /* borde verde suave */
    background-color: #e9ffb5;           /* verde muy claro tipo highlight */
    color: #111827;                      /* texto oscuro */
    box-shadow: 0 0 0 1px rgba(180,243,151,0.5);
    transition: background-color .18s ease, 
                box-shadow .18s ease,
                transform .08s ease;
}

.tradein-pill-btn:hover{
    background-color: #dcff94;           /* un poco más intenso */
    box-shadow: 0 6px 14px rgba(148, 220, 120, 0.5);
    transform: translateY(-1px);
}

.tradein-pill-btn:active{
    transform: translateY(0);
    box-shadow: 0 2px 6px rgba(148, 220, 120, 0.4);
}
/* Botón tipo píldora negro */
.primary-pill-btn{
    position: relative;
    border-radius: 999px;
    padding: 0.55rem 1.9rem;
    font-weight: 600;
    font-size: 0.95rem;
    border: none;
    background-color: #000000;
    color: #ffffff;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: .45rem;
    box-shadow: 0 10px 25px rgba(0,0,0,0.25);
    transition: transform .12s ease, box-shadow .12s ease, background-color .12s ease;
}

.primary-pill-btn:hover{
    background-color:#111111;
    transform: translateY(-1px);
    box-shadow:0 14px 32px rgba(0,0,0,0.32);
}

.primary-pill-btn:active{
    transform: translateY(0);
    box-shadow:0 6px 16px rgba(0,0,0,0.28);
}

/* Loader circular */
.primary-pill-btn .btn-loader{
    width: 0;                 /* oculto por defecto */
    height: 0;
    border-radius: 999px;
    border: 2px solid transparent;
    border-top-color:#ffffff;
    border-right-color:#ffffff;
    animation: spin-btn 0.7s linear infinite;
    opacity:0;
    transition: opacity .15s ease, width .15s ease, height .15s ease;
}

/* Estado cargando */
.primary-pill-btn.is-loading{
    cursor: default;
    pointer-events: none;
    opacity: .92;
}

.primary-pill-btn.is-loading .btn-label{
    opacity:.0;
    transform: translateY(2px);
    transition: opacity .12s ease, transform .12s ease;
}

.primary-pill-btn.is-loading .btn-loader{
    width: 16px;
    height: 16px;
    opacity:1;
}

/* Animación giro */
@keyframes spin-btn{
    to{ transform: rotate(360deg); }
}

</style>


<div class="container">
    <form id="form-venta" method="POST" action="{{ route('ventas.store') }}">
        @csrf

        <div class="row">
            {{-- =======================
                 COLUMNA IZQUIERDA
                 ======================= --}}
            <div class="col-md-3 mt-3">

                <!-- Tarjeta de Cliente -->
                <div class="card modern-card mb-3">
                    <div class="card-header modern-header">Cliente</div>
                    <div class="card-body">
                        <div class="dropdown">
                            <input
                                type="text"
                                id="search-client"
                                class="form-control modern-input dropdown-toggle"
                                data-bs-toggle="dropdown"
                                placeholder="Buscar cliente..."
                                autocomplete="off"
                            >
                            <ul class="dropdown-menu modern-dropdown w-100" id="client-list">
                                <li>
                                    <button type="button" class="dropdown-item modern-dropdown-item" onclick='selectClient({
                                        id: 1,
                                        nombre: "PÚBLICO EN GENERAL",
                                        apellido: "",
                                        telefono: "",
                                        email: "",
                                        comentarios: ""
                                    })'>
                                        PÚBLICO EN GENERAL
                                    </button>
                                </li>
                                <li>
                                    <button type="button" class="dropdown-item modern-dropdown-item" onclick="openCreateClientModal()">
                                        + Crear nuevo cliente
                                    </button>
                                </li>
                                <!-- Aquí se insertarán dinámicamente los clientes -->
                            </ul>
                        </div>

                        <!-- Campo oculto para enviar ID del cliente seleccionado -->
                        <input type="hidden" name="cliente_id" id="cliente_id" value="{{ old('cliente_id') }}">
                    </div>

                    <!-- Detalles del cliente -->
                    <div id="client-details" class="mt-3"></div>
                </div>

                <!-- Tarjeta de Lugar de la Cotización -->
                <div class="card modern-card mb-3">
                    <div class="card-header modern-header">Lugar de la Cotización</div>
                    <div class="card-body">
                        <select name="lugar" id="lugarCotizacion" class="form-control modern-select" required>
                            <option value="">Selecciona un lugar...</option>
                            <option value="AMCG ECOS INTERNACIONAL DE CIRUGIA GENERAL">AMCG ECOS INTERNACIONAL DE CIRUGIA GENERAL</option>
                            <option value="AMCG CONGRESO INTERNACIONAL DE CIRUGIA GENERAL">AMCG CONGRESO INTERNACIONAL DE CIRUGIA GENERAL</option>
                            <option value="AMCE CONGRESO INTERNACIONAL DE CIRUGIA ENDOSCOPICA">AMCE CONGRESO INTERNACIONAL DE CIRUGIA ENDOSCOPICA</option>
                            <option value="AMECRA XXIX CONGRESO INTERNACIONAL DE ASOCIACIÓN MEX DE CIRUGIA RECONS, ARTICULAR Y ARTROSCOPICA">
                                AMECRA XXIX CONGRESO INTERNACIONAL DE ASOCIACIÓN MEX DE CIRUGIA RECONS, ARTICULAR Y ARTROSCOPICA
                            </option>
                            <option value="CVDL CONGRESO DE VETERINARIA">CVDL CONGRESO DE VETERINARIA</option>
                            <option value="AMG ECOS INTERNACIONALES DE GASTROENTEROLOGIA">AMG ECOS INTERNACIONALES DE GASTROENTEROLOGIA</option>
                            <option value="AMG SEMANA NACIONAL GASTRO">AMG SEMANA NACIONAL GASTRO</option>
                            <option value="otro">Otro</option>
                        </select>
                    </div>
                </div>

                <!-- Tarjeta de Nota al Cliente -->
                <div class="card modern-card mb-3">
                    <div class="card-header modern-header">Nota al Cliente</div>
                    <div class="card-body">
                        <textarea name="nota" id="notaCliente" class="form-control modern-textarea" rows="4" placeholder="Escribe una nota...">{{ old('nota') }}</textarea>
                    </div>
                </div>

                <!-- Tarjeta de Registrado por -->
                <div class="card modern-card">
                    <div class="card-header modern-header">Registrado por</div>
                    <div class="card-body">
                        @auth
                            <input type="text" name="registrado_por" class="form-control modern-textarea" value="{{ auth()->user()->name }}" readonly>
                        @else
                            <input type="text" name="registrado_por" class="form-control modern-textarea" value="Desconocido" readonly>
                        @endauth
                    </div>
                </div>
            </div>

            {{-- =======================
                 COLUMNA DERECHA
                 ======================= --}}
            <div class="col-md-9">

                <!-- Productos -->
                <div class="card modern-card mt-3">
                    <div class="card-header modern-header">Productos</div>
                    <div class="card-body">
                        <div class="dropdown">
                            <input
                                type="text"
                                id="buscarProducto"
                                class="form-control modern-input dropdown-toggle"
                                data-bs-toggle="dropdown"
                                placeholder="Buscar producto..."
                                autocomplete="off"
                            >

                            <ul class="dropdown-menu modern-dropdown w-100" id="dropdownProductos">
                                <li>
                                    <button type="button" class="dropdown-item modern-dropdown-item" data-bs-toggle="modal" data-bs-target="#modal1">
                                        + Crear Producto
                                    </button>
                                </li>

                                @foreach($productos->sortBy('tipo_equipo') as $producto)
                                    <li>
                                        <button
                                            type="button"
                                            class="dropdown-item modern-dropdown-item d-flex align-items-center"
                                            onclick="agregarProductoDesdeDropdown(
                                                {{ $producto->id }},
                                                @json($producto->tipo_equipo),
                                                @json($producto->modelo),
                                                @json($producto->marca),
                                                {{ $producto->precio }},
                                                @json($producto->imagen)
                                            )"
                                        >
                                            <img src="/storage/{{ $producto->imagen }}" alt="{{ $producto->tipo_equipo }}"
                                                 class="modern-product-img me-2"
                                                 style="width: 40px; height: 40px; object-fit: cover; border-radius: 6px;">

                                            <div class="flex-grow-1 modern-product-info">
                                                <strong>{{ strtoupper($producto->tipo_equipo) }}</strong>
                                                - {{ strtoupper($producto->modelo) }} {{ strtoupper($producto->marca) }}
                                                <br>
                                                <span class="text-muted modern-product-price">
                                                    ${{ number_format($producto->precio, 2) }}
                                                </span>
                                            </div>

                                            <span class="badge bg-secondary modern-badge">
                                                {{ $producto->stock }} unidades
                                            </span>
                                        </button>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Productos seleccionados -->
                <div class="card modern-card mt-3">
                    <div class="card-header modern-header">Productos Seleccionados</div>
                    <div class="card-body">
                        <div class="table-responsive">
                            {{-- ✅ JSON que espera el controlador: productos_json --}}
                            <input type="hidden" name="productos_json" id="productos_json" value="{{ old('productos_json') }}">
                            <table id="tabla-productos" class="table modern-table">
                                <thead>
                                    <tr>
                                        <th>Imagen</th>
                                        <th>Equipo</th>
                                        <th>Modelo</th>
                                        <th>Marca</th>
                                        <th>Cantidad</th>
                                        <th>Subtotal</th>
                                        <th>Sobreprecio</th>
                                        <th>Número de Serie</th>
                                        <th>Acción</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- =========================================
                     ✅ EQUIPOS A CUENTA (TRADE-IN)
                     ========================================= --}}
                <div class="card modern-card mt-3">
                    <div class="card-header modern-header d-flex align-items-center justify-content-between">
                        <span>Equipos a cuenta (Trade-in)</span>
                        <button type="button" class="btn btn-sm tradein-pill-btn" id="btn-add-tradein">
    + Agregar equipo
</button>

                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-2" style="font-size:13px;">
                            Agrega equipos que te dejan como parte de pago. Su valor se descuenta del total.
                        </p>

                        <!-- ✅ Input oculto que se enviará al backend -->
                        <input type="hidden" name="tradeins_json" id="tradeins_json" value='{{ old('tradeins_json', '[]') }}'>

                        <div class="table-responsive">
                            <table id="tabla-tradeins" class="table modern-table">
                                <thead>
                                    <tr>
                                        <th style="width:16%;">Tipo de equipo</th>
                                        <th style="width:12%;">Marca</th>
                                        <th style="width:12%;">Modelo</th>
                                        <th style="width:12%;">Núm. Serie</th>
                                        <th style="width:12%;">Valor a cuenta</th>
                                        <th style="width:4%;">Acción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- filas dinámicas por JS --}}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="d-flex flex-column flex-md-row">

                    <!-- Resumen -->
                    <div class="card modern-card mt-3 w-100 w-md-50">
                        <div class="card-header modern-header">Resumen</div>
                        <div class="card-body">

                            <p>Subtotal: $<span id="subtotal" class="modern-value">0.00</span></p>
                            <input type="hidden" name="subtotal" id="subtotal_input" value="{{ old('subtotal', 0) }}">

                            <div class="form-group">
                                <label>Descuento</label>
                                <input type="number" name="descuento" id="descuento" value="{{ old('descuento', 0) }}"
                                       class="form-control modern-input w-25 d-inline-block" step="0.01">
                            </div>

                            <br>

                            <div class="form-group">
                                <label>Envío</label>
                                <input type="number" name="envio" id="envio" value="{{ old('envio', 0) }}"
                                       class="form-control modern-input w-25 d-inline-block" step="0.01">
                            </div>

                            <div class="form-check mt-2">
                                <input type="checkbox" class="form-check-input" id="aplica_iva">
                                <label class="form-check-label">Aplicar IVA (16%)</label>
                            </div>

                            <input type="hidden" name="iva" id="iva_input" value="{{ old('iva', 0) }}">

                            <p>IVA: $<span id="iva">0.00</span></p>
                            
                            {{-- ✅ ANTICIPO REAL (se guarda como Pago aprobado y es_anticipo = true) --}}
  
                            <div class="form-group mt-3">
                                <label style="font-weight:700;">Anticipo</label>
                                <div class="d-flex gap-2 flex-wrap mt-1">
                                    <input type="number" step="0.01" min="0"
                                           id="anticipo_monto" name="anticipo_monto"
                                           class="form-control modern-input" style="max-width:160px;"
                                           placeholder="Monto"
                                           value="{{ old('anticipo_monto') }}">
                                    <input type="date"
                                           id="anticipo_fecha" name="anticipo_fecha"
                                           class="form-control modern-input" style="max-width:180px;"
                                           value="{{ old('anticipo_fecha') }}">
                                    <select id="anticipo_metodo" name="anticipo_metodo"
                                            class="form-control modern-input" style="max-width:220px;">
                                        <option value="">Método de pago</option>
                                        <option value="efectivo"          {{ old('anticipo_metodo') === 'efectivo' ? 'selected' : '' }}>Efectivo</option>
                                        <option value="transferencia"     {{ old('anticipo_metodo') === 'transferencia' ? 'selected' : '' }}>Transferencia</option>
                                        <option value="tarjeta_credito"   {{ old('anticipo_metodo') === 'tarjeta_credito' ? 'selected' : '' }}>Tarjeta crédito</option>
                                        <option value="tarjeta_debito"    {{ old('anticipo_metodo') === 'tarjeta_debito' ? 'selected' : '' }}>Tarjeta débito</option>
                                        <option value="cheque"            {{ old('anticipo_metodo') === 'cheque' ? 'selected' : '' }}>Cheque</option>
                                        <option value="otro"              {{ old('anticipo_metodo') === 'otro' ? 'selected' : '' }}>Otro</option>
                                    </select>
                                </div>
                                <small class="text-muted">
                                    Si capturas anticipo se registra como pago real aprobado y aplicado al saldo de esta venta.
                                </small>
                            </div>
                            <hr>
                            {{-- Total ORIGINAL (productos/iva/envío/descuento) --}}
                            <p><strong>Total: $<span id="total">0.00</span></strong></p>

                            {{-- ✅ hidden total ORIGINAL que usa el controlador --}}
                            <input type="hidden" name="total" id="total_input" value="{{ old('total', 0) }}">
                            <input type="hidden" name="total_original" id="total_original_input" value="{{ old('total_original', 0) }}">

                            {{-- ✅ TOTAL TRADE-IN Y NETO (solo informativo pero se mandan también) --}}
                            <p>
                                Valor a cuenta: $<span id="tradein_total_ui" class="modern-value">0.00</span>
                            </p>
                            <p style="font-weight:800;">
                                Total neto: $<span id="total_neto_ui" class="modern-value">0.00</span>
                            </p>
                            <input type="hidden" name="tradein_total" id="tradein_total_input" value="{{ old('tradein_total', 0) }}">
                            <input type="hidden" name="total_neto" id="total_neto_input" value="{{ old('total_neto', 0) }}">

                            <hr>

                            <div class="form-group">
                                <label for="tipoPago">Selecciona Plan:</label>
                                <select id="tipoPago" name="plan" class="form-control modern-input w-50" required>
                                    <option value="" disabled {{ old('plan') ? '' : 'selected' }}>Selecciona un plan</option>
                                    <option value="contado"      {{ old('plan') === 'contado' ? 'selected' : '' }}>Pago de Contado</option>
                                    <option value="personalizado"{{ old('plan') === 'personalizado' ? 'selected' : '' }}>Plan Personalizado</option>
                                    <option value="estatico"     {{ old('plan') === 'estatico' ? 'selected' : '' }}>Plan Fijo</option>
                                    <option value="dinamico"     {{ old('plan') === 'dinamico' ? 'selected' : '' }}>Plan Flexible</option>
                                    <option value="credito"      {{ old('plan') === 'credito' ? 'selected' : '' }}>Plan a Crédito</option>
                                </select>
                            </div>

                            <div id="opcionesDinamicas" style="display: none; margin-top: 1rem;">
                                <label for="pagoInicial">Pago Inicial:</label>
                                <input type="number" id="pagoInicial" class="form-control modern-input w-50" min="0" step="0.01">
                            </div>

                            <div id="opcionesCredito" style="display: none; margin-top: 1rem;">
                                <label for="pagoCreditoInicial">Pago Inicial:</label>
                                <input type="number" id="pagoCreditoInicial" class="form-control modern-input w-50" min="0" step="0.01">
                                <label for="plazoCredito" style="margin-top: 0.5rem;">Plazo (meses):</label>
                                <input type="number" id="plazoCredito" class="form-control modern-input w-50" value="6" min="1" step="1">
                            </div>

                            <div id="opcionesPersonalizado" style="display: none; margin-top: 1rem;">
                                <label for="mesesPersonalizado">Selecciona el número de meses:</label>
                                <input type="number" id="mesesPersonalizado" class="form-control modern-input w-50" min="1" step="1" value="1">
                                <div id="listaPagosPersonalizados" class="mt-3"></div>
                            </div>

                            {{-- ✅ JSON con el calendario de pagos que espera el controlador: pagos_json --}}
                            <input type="hidden" id="pagosJsonInput" name="pagos_json" value="{{ old('pagos_json') }}">

                            {{-- Meses de garantía --}}
                            <div class="form-group mt-4">
                                <label for="meses_garantia">Meses de Garantía:</label>
                                <select name="meses_garantia" id="meses_garantia" class="form-control modern-input w-50" required>
                                    <option value="" disabled {{ old('meses_garantia') ? '' : 'selected' }}>Selecciona meses de garantía</option>
                                    <option value="6"  {{ old('meses_garantia') == 6  ? 'selected' : '' }}>6 meses</option>
                                    <option value="9"  {{ old('meses_garantia') == 9  ? 'selected' : '' }}>9 meses</option>
                                    <option value="12" {{ old('meses_garantia') == 12 ? 'selected' : '' }}>12 meses</option>
                                    <option value="15" {{ old('meses_garantia') == 15 ? 'selected' : '' }}>15 meses</option>
                                    <option value="18" {{ old('meses_garantia') == 18 ? 'selected' : '' }}>18 meses</option>
                                </select>
                            </div>

                            {{-- Carta de garantía buscador --}}
                            <br>
                            <div class="form-group position-relative mt-4" style="max-width: 900px; width: 100%;">
                                <label for="carta_garantia_search">Carta de Garantía a incluir en el PDF:</label>

                                <input type="text" id="carta_garantia_search" class="form-control" placeholder="Escribe para buscar..." autocomplete="off">
                                <input type="hidden" name="carta_garantia_id" id="carta_garantia_id" value="{{ old('carta_garantia_id') }}">

                                <ul id="dropdown_cartas"
                                    class="list-group position-absolute w-100 mt-1 shadow-sm"
                                    style="z-index:1000;display:none;max-height:250px;overflow-y:auto;">
                                    @foreach ($cartas->sortBy('nombre') as $carta)
                                        <li class="list-group-item list-option-carta" data-id="{{ $carta->id }}">
                                            {{ strtoupper($carta->nombre) }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>

                            <br>

                            <button type="submit" class="btn btn-success primary-pill-btn" id="btn-guardar">
    <span class="btn-label">Guardar</span>
    <span class="btn-loader" aria-hidden="true"></span>
</button>

                        </div>
                    </div>

                    <!-- Detalles del financiamiento -->
                    <div class="card modern-card mt-3 w-100 w-md-50 ms-md-3">
                        <div class="card-header modern-header">Detalles del Financiamiento</div>
                        <div class="card-body" id="plan-pagos"></div>
                    </div>

                </div>
            </div>
        </div>
    </form>
</div>

{{-- ============================================================
     MODAL: CREAR CLIENTE
     ============================================================ --}}
<form id="form-cliente" method="POST" action="{{ route('clientes.store') }}">
  @csrf

  <div class="modal fade" id="modal_formulario" tabindex="-1" role="dialog" aria-labelledby="FormularioLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">

        <div class="modal-header">
          <h5 class="modal-title" id="createClientModalLabel">Registrar Cliente</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>

        <div class="modal-body">
          <div class="row mb-3">
            <div class="col-md-6">
              <label for="nombre" class="form-label">Nombre</label>
              <input type="text" class="form-control text-uppercase" id="nombre" name="nombre" placeholder="Ingresar nombre" required>
              @error('nombre') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
            <div class="col-md-6">
              <label for="apellido" class="form-label">Apellido</label>
              <input type="text" class="form-control text-uppercase" id="apellido" name="apellido" placeholder="Ingresar apellido" required>
              @error('apellido') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-6">
              <label for="telefono" class="form-label">Teléfono</label>
              <input
                  type="tel" class="form-control" id="telefono" name="telefono"
                  placeholder="10 dígitos" required inputmode="numeric"
                  pattern="^\d{10}$" maxlength="10" minlength="10"
              >
              <small id="help-telefono" class="text-muted">Debe contener exactamente 10 números.</small><br>
              <span id="error-telefono" class="text-danger" style="display:none;">El teléfono ya está registrado.</span>
              @error('telefono') <small class="text-danger d-block">{{ $message }}</small> @enderror
            </div>

            <div class="col-md-6">
              <label for="email" class="form-label">Email</label>
              <input type="email" class="form-control" id="email" name="email" placeholder="Ingresar email">
              <span id="error-email" class="text-danger" style="display:none;">El correo ya está registrado.</span>
              @error('email') <small class="text-danger d-block">{{ $message }}</small> @enderror
            </div>
          </div>

          <div class="mb-3">
            <label for="comentarios" class="form-label">Dirección</label>
            <textarea id="comentarios" name="comentarios" class="form-control text-uppercase" placeholder="Agrega información de tu cliente"></textarea>
            @error('comentarios') <small class="text-danger d-block">{{ $message }}</small> @enderror
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button id="btn-submit" type="submit" class="btn btn-primary">Agregar</button>
        </div>

      </div>
    </div>
  </div>
</form>

{{-- Modal Cliente guardado --}}
<div class="modal fade" id="cliente_creado" tabindex="-1" role="dialog" aria-labelledby="ClienteCreadoLabel"
     aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header encabezado_modal text-center">
                <h5 class="modal-title titulo_modal">¡Cliente guardado exitosamente!</h5>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <img src="{{ asset('images/confirmar.jpeg') }}" alt="Logo de encabezado" class="logo-modal">
                </div>
                <p class="text-center mensaje-modal">
                    El cliente se ha registrado correctamente en el sistema.
                    Puedes proceder a cerrar este mensaje.
                    <b>Grupo MediBuy</b>.
                </p>
            </div>
            <div class="modal-footer d-flex justify-content-center">
                <button class="btn btn-listo" onclick="cerrarModal()">Listo</button>
            </div>
        </div>
    </div>
</div>

{{-- ============================================================
     MODAL: CREAR PRODUCTO
     ============================================================ --}}
<form id="formProducto" method="POST" action="{{ route('productos.store') }}" enctype="multipart/form-data">
    @csrf
    <div class="modal fade" id="modal1" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Registrar Equipo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text" name="tipo_equipo" class="form-control" placeholder="Ej: Monitor" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Modelo</label>
                            <input type="text" name="modelo" class="form-control" placeholder="Ej: Vision Pro" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Marca</label>
                            <input type="text" name="marca" class="form-control" placeholder="Ej: Stryker" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Existencias</label>
                            <input type="number" name="stock" class="form-control" value="1" required min="1">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Precio</label>
                            <input type="number" name="precio" class="form-control" value="0.00" required min="0" step="0.01">
                        </div>
                    </div>

                    <div class="mb-3 text-center">
                        <label class="form-label d-block">Imagen</label>
                        <div class="image-container">
                            <label for="image-upload" class="image-preview">
                                <img id="preview-icon" src="https://cdn-icons-png.flaticon.com/512/1829/1829586.png" alt="Añadir imagen">
                                <span id="preview-text">Añadir imagen</span>
                            </label>
                            <input type="file" id="image-upload" name="imagen" accept="image/*" hidden>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Agregar</button>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
// =========================
// Crear producto vía modal
// =========================
document.addEventListener('DOMContentLoaded', function () {
    const formProducto = document.getElementById("formProducto");
    if (!formProducto) return;

    formProducto.addEventListener("submit", function (event) {
        event.preventDefault(); // Evita recarga

        const form = this;
        var formData = new FormData(form);

        fetch("{{ route('productos.store') }}", {
            method: "POST",
            body: formData,
            headers: {
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                "Accept": "application/json" // para que Laravel retorne JSON
            }
        })
        .then(function (response) {
            return response.json()
                .catch(function () { return {}; })
                .then(function (data) {
                    if (!response.ok) {
                        throw data;
                    }
                    return data;
                });
        })
        .then(function (data) {
            if (data.message) {
                Swal.fire({
                    toast: true,
                    icon: 'success',
                    title: data.message,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    didOpen: function (toast) {
                        toast.addEventListener('mouseenter', Swal.stopTimer);
                        toast.addEventListener('mouseleave', Swal.resumeTimer);
                    }
                });

                // Limpiar formulario
                form.reset();

                // Resetear preview de imagen
                const previewIcon = document.getElementById('preview-icon');
                const previewText = document.getElementById('preview-text');
                if (previewIcon && previewText) {
                    previewIcon.src = "https://cdn-icons-png.flaticon.com/512/1829/1829586.png";
                    previewIcon.style.width = '';
                    previewIcon.style.height = '';
                    previewText.style.display = 'inline';
                }

                // Cerrar modal
                const modalEl = document.getElementById('modal1');
                if (modalEl && window.bootstrap && bootstrap.Modal) {
                    const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                    modal.hide();
                }
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.message || 'No se pudo crear el producto.',
                });
            }
        })
        .catch(function (error) {
            console.error("Error:", error);
            Swal.fire({
                icon: 'error',
                title: 'Error inesperado',
                text: 'Ocurrió un error al enviar el formulario.',
            });
        });
    });
});
</script>

<script>
// =========================
// Preview imagen producto
// =========================
document.addEventListener('DOMContentLoaded', function () {
    const inputImg = document.getElementById('image-upload');
    if (!inputImg) return;

    inputImg.addEventListener('change', function (event) {
        const file = event.target.files[0];
        if (file && file.type && file.type.indexOf('image/') === 0) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const previewIcon = document.getElementById('preview-icon');
                const previewText = document.getElementById('preview-text');

                if (!previewIcon || !previewText) return;

                previewIcon.src = e.target.result;
                previewIcon.style.width = '100%';
                previewIcon.style.height = '100%';
                previewText.style.display = 'none';
            };
            reader.readAsDataURL(file);
        }
    });
});
</script>

<script>
// =========================
// Buscador carta garantía
// =========================
document.addEventListener("DOMContentLoaded", function () {
    const input       = document.getElementById('carta_garantia_search');
    const hiddenInput = document.getElementById('carta_garantia_id');
    const dropdown    = document.getElementById('dropdown_cartas');

    if (!input || !hiddenInput || !dropdown) return;

    const originalOptions = Array.prototype.slice.call(
        dropdown.querySelectorAll('.list-option-carta')
    );

    function normalize(str) {
        return (str || '')
            .normalize("NFD")
            .replace(/[\u0300-\u036f]/g, "")
            .toLowerCase();
    }
    function escapeReg(str) {
        return (str || '').replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    }

    input.addEventListener('input', function () {
        const term = input.value.trim();
        const normalizedValue = normalize(term);
        dropdown.innerHTML = '';
        let matches = 0;

        if (!normalizedValue) {
            dropdown.style.display = 'none';
            hiddenInput.value = '';
            return;
        }

        originalOptions.forEach(function (option) {
            const textOriginal   = option.textContent || '';
            const textNormalized = normalize(textOriginal);

            if (textNormalized.indexOf(normalizedValue) !== -1) {
                matches++;

                // Resaltado seguro
                const regex = new RegExp(escapeReg(term), 'i');
                const highlighted = textOriginal.replace(regex, function (match) {
                    return '<strong style="color:#4a148c">' + match + '</strong>';
                });

                const li = document.createElement('li');
                li.classList.add('list-group-item', 'list-option-carta');
                li.setAttribute('data-id', option.getAttribute('data-id'));
                li.innerHTML = highlighted;
                li.addEventListener('click', function () {
                    input.value = textOriginal;
                    hiddenInput.value = option.getAttribute('data-id');
                    dropdown.style.display = 'none';
                });
                dropdown.appendChild(li);
            }
        });

        dropdown.style.display = matches ? 'block' : 'none';
    });

    document.addEventListener('click', function (e) {
        if (!dropdown.contains(e.target) && e.target !== input) {
            dropdown.style.display = 'none';
        }
    });
});
</script>

<script>
// =====================================================
// PLAN DE PAGOS (estático, dinámico, crédito, personalizado, contado)
// Genera window.pagos y rellena #pagosJsonInput
// ✅ AHORA RESTA EL ANTICIPO (anticipo_monto) AL TOTAL NETO
// =====================================================
document.addEventListener('DOMContentLoaded', function () {
    const tipoPago              = document.getElementById('tipoPago');
    const opcionesDinamicas     = document.getElementById('opcionesDinamicas');
    const opcionesCredito       = document.getElementById('opcionesCredito');
    const opcionesPersonalizado = document.getElementById('opcionesPersonalizado');
    const listaPagosPersonalizados = document.getElementById('listaPagosPersonalizados');
    const planPagosDiv          = document.getElementById('plan-pagos');
    const pagoInicial           = document.getElementById('pagoInicial');
    const pagoCreditoInicial    = document.getElementById('pagoCreditoInicial');
    const plazoCredito          = document.getElementById('plazoCredito');
    const mesesPersonalizado    = document.getElementById('mesesPersonalizado');
    const anticipoMontoInput    = document.getElementById('anticipo_monto'); // 🔥 nuevo

    if (!tipoPago || !planPagosDiv) return;

    // Estado global de pagos a enviar al backend
    window.pagos = [];

    // ===== Utilidades =====
    function obtenerTotalBase() {
        // Usar primero TOTAL NETO si existe (total_neto_ui), si no, el total original
        var netNode = document.getElementById('total_neto_ui');
        if (netNode && netNode.textContent && netNode.textContent.trim() !== '') {
            var netVal = parseFloat(netNode.textContent.replace(/[$,]/g, '')) || 0;
            if (netVal > 0) return netVal;
        }

        var totalEl = document.getElementById('total');
        var totalTexto = totalEl && totalEl.textContent ? totalEl.textContent : "0";
        return parseFloat(totalTexto.replace(/[$,]/g, '')) || 0;
    }

    // 🔥 TOTAL A FINANCIAR = total_neto - anticipo
    function obtenerTotal() {
        var base = obtenerTotalBase();
        var anticipo = 0;

        if (anticipoMontoInput && anticipoMontoInput.value !== '') {
            anticipo = parseFloat(String(anticipoMontoInput.value).replace(/[$,]/g, '')) || 0;
        }

        var totalFinanciar = base - anticipo;
        if (totalFinanciar < 0) totalFinanciar = 0;
        return totalFinanciar;
    }

    function formatear(moneda) {
        return moneda.toLocaleString('es-MX', { style: 'currency', currency: 'MXN' });
    }
    function formatoFechaISO(date) {
        return date.toISOString().split('T')[0]; // yyyy-mm-dd
    }
    function formatoMoneda2(n) {
        return (parseFloat(n) || 0).toFixed(2);
    }
    function debounce(fn, delay) {
        var t;
        return function () {
            var args = arguments;
            clearTimeout(t);
            t = setTimeout(function () {
                fn.apply(null, args);
            }, delay);
        };
    }

    var nombresPagos = ["Primer", "Segundo", "Tercer", "Cuarto", "Quinto", "Sexto", "Séptimo", "Octavo", "Noveno", "Décimo", "Undécimo", "Duodécimo"];

    // ===== Núcleo: recalcular según tipo =====
    function recalcularSegunTipo() {
        var totalActual = obtenerTotal(); // ✅ YA VIENE CON ANTICIPO DESCONTADO
        var tipo = tipoPago.value;
        if (tipo === 'personalizado' && listaPagosPersonalizados.querySelectorAll('input[type="number"]').length > 0) {
            // No regenerar inputs: solo ajusta las cuotas no bloqueadas para que sumen al nuevo total
            recalcularPagosPersonalizados();
        } else {
            // Para otros tipos, se recomputa la fórmula
            actualizarPlanPagos(totalActual);
        }
    }

    // ===== Generador general de plan =====
    function actualizarPlanPagos(total) {
        planPagosDiv.innerHTML = '';
        window.pagos = [];

        var tipo = tipoPago.value;
        var fechaPago = new Date();

        if (!tipo) {
            planPagosDiv.innerHTML = '<p style="color:#666;">Selecciona un plan para ver el detalle de pagos.</p>';
            return;
        }

        if (total <= 0) {
            planPagosDiv.innerHTML = '<p style="color:red;">Total a financiar inválido o cero</p>';
            return;
        }

        function agregarPago(cuota, fecha, descripcion) {
            var fechaCopia = new Date(fecha);
            var fechaStr   = fechaCopia.toLocaleDateString('es-MX', { day: '2-digit', month: 'long', year: 'numeric' });
            var fechaISO   = formatoFechaISO(fechaCopia);

            window.pagos.push({ cuota: formatoMoneda2(cuota), mes: fechaISO, descripcion: descripcion });

            var p = document.createElement('p');
            p.innerHTML = '<strong>' + descripcion + ' - ' + fechaStr + ':</strong> ' + formatear(parseFloat(cuota));
            planPagosDiv.appendChild(p);
        }

        if (tipo === 'estatico') {
            if (total < 500000) {
                var fechaIni = new Date(fechaPago);
                agregarPago(total * 0.5, fechaIni, 'Pago inicial');

                var fecha1 = new Date(fechaPago);
                fecha1.setMonth(fecha1.getMonth() + 1);
                agregarPago(total * 0.25, fecha1, 'Primer pago');

                var fecha2 = new Date(fechaPago);
                fecha2.setMonth(fecha2.getMonth() + 2);
                agregarPago(total * 0.25, fecha2, 'Segundo pago');
            } else {
                var fechaIni2 = new Date(fechaPago);
                var primerPago = total * 0.4;
                agregarPago(primerPago, fechaIni2, 'Pago inicial');

                var restante = total - primerPago;
                var numPagos = Math.min(6, Math.max(4, Math.ceil(restante / 50000)));
                var cuotaRestante = restante / numPagos;

                for (var i = 0; i < numPagos; i++) {
                    var fecha = new Date(fechaPago);
                    fecha.setMonth(fecha.getMonth() + i + 1);
                    agregarPago(cuotaRestante, fecha, (nombresPagos[i] || ((i + 1) + 'º')) + ' pago');
                }
            }

        } else if (tipo === 'dinamico') {
            var pagoIni = parseFloat(pagoInicial && pagoInicial.value ? pagoInicial.value : 0) || 0;
            if (pagoIni <= 0 || pagoIni >= total) {
                planPagosDiv.innerHTML = '<p style="color:red;">Pago inicial inválido</p>';
                return;
            }
            var restante2   = total - pagoIni;
            var numPagos2   = (total < 150000) ? 2 : (total < 400000) ? 4 : 6;
            var cuotaRestante2 = restante2 / numPagos2;

            agregarPago(pagoIni, fechaPago, 'Pago inicial');
            for (var j = 0; j < numPagos2; j++) {
                fechaPago.setMonth(fechaPago.getMonth() + 1);
                agregarPago(cuotaRestante2, fechaPago, (nombresPagos[j] || ((j + 1) + 'º')) + ' pago');
            }

        } else if (tipo === 'credito') {
            var pagoIniC = parseFloat(pagoCreditoInicial && pagoCreditoInicial.value ? pagoCreditoInicial.value : 0) || 0;
            var plazo   = parseInt(plazoCredito && plazoCredito.value ? plazoCredito.value : 6, 10) || 6;
            if (pagoIniC < 0 || pagoIniC >= total) {
                planPagosDiv.innerHTML = '<p style="color:red;">Pago inicial de crédito inválido</p>';
                return;
            }
            if (plazo <= 0) {
                planPagosDiv.innerHTML = '<p style="color:red;">Plazo inválido</p>';
                return;
            }

            var tasaInteres  = 0.05;
            var montoCredito = total - pagoIniC;
            var totalCredito = montoCredito + (montoCredito * tasaInteres * plazo);
            var cuotaMensual = totalCredito / plazo;

            agregarPago(pagoIniC, fechaPago, 'Pago inicial');

            var totalCreditoP = document.createElement('p');
            totalCreditoP.innerHTML = '<strong>Total a pagar con crédito:</strong> ' + formatear(totalCredito);
            planPagosDiv.appendChild(totalCreditoP);

            for (var k = 0; k < plazo; k++) {
                fechaPago.setMonth(fechaPago.getMonth() + 1);
                agregarPago(cuotaMensual, fechaPago, (nombresPagos[k] || ((k + 1) + 'º')) + ' pago');
            }

        } else if (tipo === 'personalizado') {
            generarPagosPersonalizados(total);

        } else if (tipo === 'contado') {
            var fechaPagoUnico = new Date();
            agregarPago(total, fechaPagoUnico, 'Pago único');
        }

        // Mostrar resumen final
        var sumaFinal = window.pagos.reduce(function (acc, p) {
            return acc + parseFloat(p.cuota);
        }, 0);

        var totalPagosP = document.createElement('p');
        totalPagosP.style.fontWeight = "bold";
        totalPagosP.textContent = Math.abs(sumaFinal - total) < 0.01
            ? 'Total de pagos: ' + formatear(sumaFinal) + ' ✅ (Coincide)'
            : 'Total de pagos: ' + formatear(sumaFinal) + ' ⚠️ (No coincide)';
        planPagosDiv.appendChild(totalPagosP);
    }

    // ===== Personalizado: generar entradas con switch "Bloquear" =====
    function generarPagosPersonalizados(total) {
        var meses = parseInt(mesesPersonalizado && mesesPersonalizado.value ? mesesPersonalizado.value : 1, 10) || 1;
        listaPagosPersonalizados.innerHTML = "";
        planPagosDiv.innerHTML = "";
        window.pagos = [];

        var pagoSugerido = total / (meses + 1);
        var fechaActual  = new Date();

        for (var i = 0; i <= meses; i++) {
            var div = document.createElement('div');
            div.classList.add('mb-2');

            var mesPago = new Date(fechaActual);
            mesPago.setMonth(mesPago.getMonth() + i);
            var fechaStr = mesPago.toLocaleDateString('es-MX', { day: '2-digit', month: 'long', year: 'numeric' });

            var label = document.createElement('label');
            label.innerHTML = (i === 0)
                ? '<strong>Pago inicial - ' + fechaStr + ':</strong>'
                : '<strong>' + (nombresPagos[i - 1] || ((i + 1) + 'º')) + ' pago - ' + fechaStr + ':</strong>';

            var inputDiv = document.createElement('div');
            inputDiv.classList.add('d-flex', 'align-items-center', 'gap-2');

            var input = document.createElement('input');
            input.type = 'number';
            input.classList.add('form-control', 'modern-input', 'w-50');
            input.setAttribute('data-mes', i);
            input.value = pagoSugerido.toFixed(2);

            // Switch de bloqueo
            var lockWrap = document.createElement('label');
            lockWrap.className = 'd-flex align-items-center gap-1';
            lockWrap.style.cursor = 'pointer';
            lockWrap.innerHTML = '' +
                '<input type="checkbox" class="form-check-input lock-switch" />' +
                '<span style="user-select:none">Bloquear</span>';

            // Cualquier edición manual marca como "modificado"
            input.addEventListener('input', function () {
                this.dataset.modificado = "true";
                recalcularPagosPersonalizados();
            });

            var lockSwitch = lockWrap.querySelector('.lock-switch');
            if (lockSwitch) {
                lockSwitch.addEventListener('change', function () {
                    var parentInput = this.closest('.d-flex').querySelector('input[type="number"]');
                    if (parentInput) {
                        parentInput.dataset.modificado = this.checked ? "true" : "";
                    }
                    recalcularPagosPersonalizados();
                });
            }

            inputDiv.appendChild(input);
            inputDiv.appendChild(lockWrap);

            div.appendChild(label);
            div.appendChild(inputDiv);
            listaPagosPersonalizados.appendChild(div);
        }

        recalcularPagosPersonalizados();
    }

    // ===== Personalizado: ajustar para que la suma coincida con el total actual =====
    function recalcularPagosPersonalizados() {
        var total        = obtenerTotal(); // ✅ también respeta anticipo
        var listaInputs  = listaPagosPersonalizados.querySelectorAll('input[type="number"]');

        // Suma actual y detecta cuáles NO deben tocarse
        var sumaPagos = 0;
        var siAjustar = [];

        Array.prototype.forEach.call(listaInputs, function (input) {
            var cont = input.closest('.mb-2');
            var lock = cont ? cont.querySelector('.lock-switch') : null;
            var bloqueado = lock ? !!lock.checked : false;
            var esMod     = !!input.dataset.modificado || bloqueado;
            var val       = parseFloat(input.value) || 0;
            sumaPagos += val;
            if (!esMod) {
                siAjustar.push(input);
            }
        });

        // Si hay diferencia, la repartimos SOLO entre las cuotas "siAjustar"
        var diferencia = total - sumaPagos;
        if (Math.abs(diferencia) > 0.01 && siAjustar.length > 0) {
            var ajuste = diferencia / siAjustar.length;
            siAjustar.forEach(function (input) {
                var actual = parseFloat(input.value) || 0;
                var nuevo = Math.max(0, actual + ajuste); // evitar negativos
                input.value = nuevo.toFixed(2);
            });
        }

        // Render resumen + window.pagos
        planPagosDiv.innerHTML = "";
        window.pagos = [];

        var fechaBase = new Date();
        Array.prototype.forEach.call(listaInputs, function (input, index) {
            var monto     = parseFloat(input.value) || 0;
            var fechaPago = new Date(fechaBase);
            fechaPago.setMonth(fechaPago.getMonth() + index);
            var fechaStr  = fechaPago.toLocaleDateString('es-MX', { day: '2-digit', month: 'long', year: 'numeric' });
            var fechaISO  = formatoFechaISO(fechaPago);
            var descripcion = (index === 0) ? "Pago inicial" : (nombresPagos[index - 1] || ((index + 1) + 'º')) + " pago";

            window.pagos.push({
                cuota: monto.toFixed(2),
                descripcion: descripcion,
                mes: fechaISO
            });

            var p = document.createElement('p');
            p.innerHTML = '<strong>' + descripcion + ' - ' + fechaStr + ':</strong> ' + formatear(monto);
            planPagosDiv.appendChild(p);
        });

        var sumaFinal = 0;
        Array.prototype.forEach.call(listaInputs, function (input) {
            sumaFinal += parseFloat(input.value) || 0;
        });

        var totalPagosP = document.createElement('p');
        totalPagosP.style.fontWeight = "bold";
        if (Math.abs(sumaFinal - total) < 0.01) {
            totalPagosP.textContent = 'Total de pagos: ' + formatear(sumaFinal) + ' ✅ (Coincide)';
        } else if (siAjustar.length === 0) {
            totalPagosP.textContent = 'Total de pagos: ' + formatear(sumaFinal) + ' ⚠️ (No coincide, todo bloqueado)';
        } else {
            totalPagosP.textContent = 'Total de pagos: ' + formatear(sumaFinal) + ' ⚠️ (No coincide)';
        }
        planPagosDiv.appendChild(totalPagosP);
    }

    // ===== Listeners de UI propios =====
    tipoPago.addEventListener('change', function () {
        if (opcionesDinamicas) {
            opcionesDinamicas.style.display = tipoPago.value === 'dinamico' ? 'block' : 'none';
        }
        if (opcionesCredito) {
            opcionesCredito.style.display = tipoPago.value === 'credito' ? 'block' : 'none';
        }
        if (opcionesPersonalizado) {
            opcionesPersonalizado.style.display = tipoPago.value === 'personalizado' ? 'block' : 'none';
        }
        actualizarPlanPagos(obtenerTotal());
    });

    [pagoInicial, pagoCreditoInicial, plazoCredito, mesesPersonalizado].forEach(function (input) {
        if (input) {
            input.addEventListener('input', function () {
                actualizarPlanPagos(obtenerTotal());
            });
        }
    });

    // 🔥 Recalcular cuando cambie el anticipo
    if (anticipoMontoInput) {
        anticipoMontoInput.addEventListener('input', function () {
            recalcularSegunTipo();
        });
        anticipoMontoInput.addEventListener('change', function () {
            recalcularSegunTipo();
        });
    }

    // ===== Integración: escuchar cambios del TOTAL (original o neto) =====
    var onTotalEvent = function () {
        recalcularSegunTipo();
    };
    var debouncedOnTotalEvent = debounce(onTotalEvent, 80);
    window.addEventListener('total:changed', debouncedOnTotalEvent);

    // Fallback: observar cambios visuales en #total o #total_neto_ui
    var totalNode     = document.getElementById('total');
    var totalNetoNode = document.getElementById('total_neto_ui');
    if (typeof MutationObserver !== 'undefined') {
        if (totalNode) {
            var obs = new MutationObserver(function () {
                debouncedOnTotalEvent();
            });
            obs.observe(totalNode, { childList: true, characterData: true, subtree: true });
        }
        if (totalNetoNode) {
            var obs2 = new MutationObserver(function () {
                debouncedOnTotalEvent();
            });
            obs2.observe(totalNetoNode, { childList: true, characterData: true, subtree: true });
        }
    }

    // Render inicial
    actualizarPlanPagos(obtenerTotal());
});
</script>

<script>
// =====================================================
// Form submit: adjuntar window.pagos a pagos_json
// =====================================================
document.addEventListener('DOMContentLoaded', function () {
    const form           = document.getElementById('form-venta');
    const inputPagosJson = document.getElementById('pagosJsonInput');
    const selectPlan     = document.getElementById('tipoPago');

    if (!form || !inputPagosJson || !selectPlan) return;

    form.addEventListener('submit', function (e) {
        console.log('Form submit capturado (pagos_json)');
        console.log('window.pagos:', window.pagos);

        const planSeleccionado = selectPlan.value;

        if (!planSeleccionado) {
            alert('Selecciona un plan de pagos antes de continuar.');
            e.preventDefault();
            return;
        }

        // Debe haber al menos un pago
        if (!window.pagos || window.pagos.length === 0) {
            alert('No hay pagos definidos, por favor selecciona o genera un plan de pagos.');
            e.preventDefault();
            return;
        }

        // Formatear los pagos y pasarlos al input oculto como JSON
        const pagosFormateados = window.pagos.map(function (pago) {
            return {
                cuota: pago.cuota,
                descripcion: pago.descripcion,
                mes: pago.mes // 'YYYY-MM-DD'
            };
        });

        inputPagosJson.value = JSON.stringify(pagosFormateados);
        console.log('Pagos a enviar:', inputPagosJson.value);
    });
});
</script>

<script>
// =====================================================
// Buscador de productos + paquetes
// =====================================================
document.addEventListener("DOMContentLoaded", function () {
    const buscarProducto    = document.getElementById("buscarProducto");
    const dropdownProductos = document.getElementById("dropdownProductos");
    if (!buscarProducto || !dropdownProductos) return;

    let controladorAbort = new AbortController();

    function realizarBusqueda(searchQuery) {
        controladorAbort.abort();
        controladorAbort = new AbortController();

        if (searchQuery.length > 1) {
            Promise.all([
                fetch("{{ route('productos.search') }}?search=" + encodeURIComponent(searchQuery), { signal: controladorAbort.signal }).then(function (res) { return res.json(); }),
                fetch("{{ route('paquetes.search') }}?search=" + encodeURIComponent(searchQuery), { signal: controladorAbort.signal }).then(function (res) { return res.json(); })
            ])
            .then(function (results) {
                var productos = results[0];
                var paquetes  = results[1];
                mostrarResultados(paquetes, productos);
            })
            .catch(function (error) {
                if (error.name !== "AbortError") {
                    console.error("Error en la búsqueda:", error);
                }
            });
        } else {
            restaurarListaOriginal();
        }
    }

    function mostrarResultados(paquetes, productos) {
        dropdownProductos.innerHTML = '' +
            '<li>' +
            '   <button type="button" class="dropdown-item modern-dropdown-item" data-bs-toggle="modal" data-bs-target="#modal1">' +
            '       + Crear Producto' +
            '   </button>' +
            '</li>';

        if (paquetes && paquetes.length > 0) {
            paquetes.forEach(function (paquete) {
                var li = document.createElement("li");
                li.innerHTML = '' +
                    '<button type="button" class="dropdown-item modern-dropdown-item"' +
                    '        data-id="' + paquete.id + '"' +
                    '        data-productos=\'' + JSON.stringify(paquete.productos || []) + '\'' +
                    '        onclick="agregarPaqueteDesdeData(this)">' +
                    '   📦 ' + String(paquete.nombre || '').toUpperCase() + ' - Paquete' +
                    '</button>';
                dropdownProductos.appendChild(li);
            });
        }

        if (productos && productos.length > 0) {
            productos.sort(function (a, b) {
                var ta = (a.tipo_equipo || '').toLowerCase();
                var tb = (b.tipo_equipo || '').toLowerCase();
                if (ta < tb) return -1;
                if (ta > tb) return 1;
                return 0;
            });

            productos.forEach(function (producto) {
                var li = document.createElement("li");
                var tipo   = String(producto.tipo_equipo || '');
                var modelo = String(producto.modelo || '');
                var marca  = String(producto.marca || '');
                var img    = String(producto.imagen || '');
                var precio = Number(producto.precio || 0);

                li.innerHTML = '' +
                    '<button type="button" class="dropdown-item modern-dropdown-item d-flex align-items-center"' +
                    '        onclick="agregarProductoDesdeDropdown(' +
                               producto.id + ', ' +
                               '\'' + tipo.replace(/'/g, "\\'") + '\', ' +
                               '\'' + modelo.replace(/'/g, "\\'") + '\', ' +
                               '\'' + marca.replace(/'/g, "\\'") + '\', ' +
                               precio + ', ' +
                               '\'' + img.replace(/'/g, "\\'") + '\'' +
                    ')">' +
                    '   <img src="/storage/' + img + '" alt="' + tipo + '" class="modern-product-img me-2" ' +
                    '        style="width: 40px; height: 40px; object-fit: cover; border-radius: 6px;">' +
                    '   <div class="flex-grow-1 modern-product-info">' +
                    '       <strong>' + tipo.toUpperCase() + '</strong> - ' + modelo.toUpperCase() + ' ' + marca.toUpperCase() +
                    '       <br>' +
                    '       <span class="text-muted modern-product-price">$' + precio.toFixed(2) + '</span>' +
                    '   </div>' +
                    '   <span class="badge modern-badge">' + (producto.stock || 0) + ' unidades</span>' +
                    '</button>';
                dropdownProductos.appendChild(li);
            });
        }

        if ((!paquetes || paquetes.length === 0) && (!productos || productos.length === 0)) {
            dropdownProductos.innerHTML += '<li><button type="button" class="dropdown-item text-muted" disabled>No se encontraron resultados</button></li>';
        }
    }

    function restaurarListaOriginal() {
        Promise.all([
            fetch("{{ route('productos.search') }}?search=").then(function (res) { return res.json(); }),
            fetch("{{ route('paquetes.search') }}?search=").then(function (res) { return res.json(); })
        ])
        .then(function (results) {
            var productos = results[0];
            var paquetes  = results[1];
            mostrarResultados(paquetes, productos);
        })
        .catch(function (error) {
            console.error("Error al restaurar lista:", error);
        });
    }

    buscarProducto.addEventListener("input", function () {
        realizarBusqueda(this.value.trim());
    });

    buscarProducto.addEventListener("keydown", function (event) {
        if (event.key === "Enter") {
            event.preventDefault();
            realizarBusqueda(this.value.trim());
        }
    });

    buscarProducto.addEventListener("focus", function () {
        if (this.value.trim() === "") {
            restaurarListaOriginal();
        }
    });

    // Mostrar todos al iniciar
    restaurarListaOriginal();

    // Función global para insertar paquetes
    window.agregarPaqueteDesdeData = function (element) {
        var productos = [];
        try {
            productos = JSON.parse(element.getAttribute("data-productos") || '[]');
        } catch (e) {
            productos = [];
        }
        agregarPaquete(productos);
    };

    function agregarPaquete(productos) {
        (productos || []).forEach(function (producto) {
            agregarProductoDesdeDropdown(
                producto.id,
                producto.tipo_equipo,
                producto.modelo,
                producto.marca,
                parseFloat(producto.precio || 0),
                producto.imagen
            );
        });
    }
});
</script>

<script>
// =====================================================
// Productos: tabla, totales, IVA, series
// =====================================================
$(document).ready(function () {
    // Al enviar, preparamos productos_json
    $('#form-venta').on('submit', function () {
        prepararProductosJSON();

        var productos = JSON.parse($('#productos_json').val() || '[]');
        console.log('🧪 Enviando productos_json:', productos);

        // Validación opcional de número de serie
        var algunoSinSerie = productos.some(function (p) {
            return !Array.isArray(p.registro_id) ||
                   p.registro_id.length !== p.cantidad ||
                   p.registro_id.indexOf(null) !== -1;
        });
        // Si quieres forzar series completas, descomenta:
        /*
        if (algunoSinSerie) {
            e.preventDefault();
            alert('Faltan números de serie en uno o más productos.');
            return;
        }
        */
    });

    // Actualizar totales en tiempo real si cambian descuento, envío o IVA
    $(document).on('input change', '#aplica_iva, #envio, #descuento', function () {
        actualizarTotal();
    });
});

// Agregar producto a la tabla
function agregarProductoDesdeDropdown(id, nombre, modelo, marca, precio, imagen) {
    if (!id || !nombre) return;
    if ($('#tabla-productos tbody tr[data-id="' + id + '"]').length) {
        alert('Este producto ya ha sido agregado.');
        return;
    }
    var p = parseFloat(precio) || 0;
    var fila = '' +
      '<tr data-id="' + id + '" data-precio="' + p + '">' +
      '  <td><img src="/storage/' + imagen + '" width="50" alt="Imagen producto"></td>' +
      '  <td class="equipo">' + nombre + '</td>' +
      '  <td>' + modelo + '</td>' +
      '  <td>' + marca + '</td>' +
      '  <td><input type="number" class="form-control cantidad" value="1" min="1" onchange="actualizarSubtotal(this)"></td>' +
      '  <td class="subtotal">' + p.toFixed(2) + '</td>' +
      '  <td><input type="number" class="form-control sobreprecio" value="0" min="0" onchange="actualizarSubtotal(this)"></td>' +
      '  <td><div class="serie-container"></div></td>' +
      '  <td class="text-center"><button type="button" class="btn btn-sm btn-danger eliminar-fila btn-icon"><i class="fas fa-trash-alt"></i></button></td>' +
      '</tr>';
    $('#tabla-productos tbody').append(fila);
    var nueva = $('#tabla-productos tbody tr').last();
    generarSelects(nueva, 1);
    actualizarTotal();
}

// Eliminar fila
$(document).on('click', '.eliminar-fila', function () {
    $(this).closest('tr').remove();
    actualizarTotal();
});

// Actualizar subtotal + selects cuando cambia cantidad o sobreprecio
$(document).on('input change', '.cantidad, .sobreprecio', function () {
    actualizarSubtotal(this);
});

function actualizarSubtotal(el) {
    var tr   = $(el).closest('tr');
    var qty  = parseInt(tr.find('.cantidad').val(), 10) || 1;
    var base = parseFloat(tr.data('precio')) || 0;
    var extra= parseFloat(tr.find('.sobreprecio').val()) || 0;
    var sub  = (base + extra) * qty;
    tr.find('.subtotal').text(sub.toFixed(2));
    generarSelects(tr, qty);
    actualizarTotal();
}

// Recalcula totales (IVA incluye envío)
function actualizarTotal() {
    var subtotal = 0;
    $('#tabla-productos tbody tr').each(function () {
        subtotal += parseFloat($(this).find('.subtotal').text()) || 0;
    });

    var desc  = parseFloat($('#descuento').val()) || 0;
    var envio = parseFloat($('#envio').val()) || 0;

    // IVA ahora contempla el envío
    var ivaBase = Math.max(0, subtotal - desc + envio);
    var iva     = $('#aplica_iva').is(':checked') ? (ivaBase * 0.16) : 0;

    var total   = ivaBase + iva;

    // Mostrar/mandar valores
    $('#subtotal').text(subtotal.toFixed(2));
    $('#subtotal_input').val(subtotal.toFixed(2));

    $('#iva').text(iva.toFixed(2));
    $('#iva_input').val(iva.toFixed(2));

    $('#total').text(total.toFixed(2));
    $('#total_input').val(total.toFixed(2));

    // Evento para plan de pagos / trade-in
    if (typeof CustomEvent !== 'undefined') {
        var evt = new CustomEvent('total:changed', { detail: { total: total } });
        window.dispatchEvent(evt);
    }

    return total;
}

// Genera inputs con buscador dinámico para número de serie
function generarSelects(tr, n) {
    var cont = tr.find('.serie-container').empty();
    for (var i = 0; i < n; i++) {
        cont.append('' +
          '<div class="dropdown-search d-flex align-items-center gap-1 mb-1" style="position: relative; width: 220px;">' +
          '  <input type="text" class="form-control search-input" placeholder="Buscar número de serie..." autocomplete="off" />' +
          '  <input type="hidden" class="registro-id-hidden" />' +
          '  <div class="dropdown-list" style="position: absolute; top: 100%; left: 0; right: 0; background: white; border: 1px solid #ccc; max-height: 150px; overflow-y: auto; display: none; z-index: 1000; border-radius: 4px;"></div>' +
          '  <button type="button" class="btn btn-outline-info btn-sm ver-registro" title="Ver detalles" style="height: 38px; margin-left: 5px;">' +
          '    <i class="bi bi-eye"></i>' +
          '  </button>' +
          '</div>'
        );
    }
}

// Buscar registros disponibles
$(document).on('input', '.search-input', function () {
    var input       = $(this);
    var query       = (input.val() || '').toLowerCase();
    var dropdown    = input.siblings('.dropdown-list');
    var hiddenInput = input.siblings('.registro-id-hidden');

    if (query.length < 1) {
        dropdown.hide();
        hiddenInput.val('');
        return;
    }

    $.get('/registros-disponibles', function (data) {
        var filtered = (data || []).filter(function (item) {
            return ((item.numero_serie || '').toLowerCase().indexOf(query) !== -1);
        });
        dropdown.empty();
        if (filtered.length === 0) {
            dropdown.html('<div style="padding: 5px;">No hay resultados</div>').show();
        } else {
            filtered.forEach(function (item) {
                dropdown.append('<div data-id="' + item.id + '" style="padding: 5px; cursor: pointer;">' + item.numero_serie + '</div>');
            });
            dropdown.show();
        }
    });
});

// Seleccionar registro
$(document).on('click', '.dropdown-list div', function () {
    var div       = $(this);
    var id        = div.data('id');
    var val       = div.text();
    var container = div.closest('.dropdown-search');
    container.find('.search-input').val(val);
    container.find('.registro-id-hidden').val(id);
    container.find('.dropdown-list').hide();
});

// Cierra dropdown si clic fuera
$(document).on('click', function (e) {
    if (!$(e.target).closest('.dropdown-search').length) {
        $('.dropdown-list').hide();
    }
});

// Mostrar detalles con SweetAlert
$(document).on('click', '.ver-registro', function () {
    var container  = $(this).closest('.dropdown-search');
    var registroId = container.find('.registro-id-hidden').val();

    if (!registroId) {
        Swal.fire('Atención', 'Primero elige un número de serie.', 'warning');
        return;
    }

    $.get('/registro-info/' + registroId, function (data) {
        var evidenciaUrl = data.evidencia1 ? data.evidencia1.replace(/^,/, '') : null;

        Swal.fire({
            title: 'Detalles del Registro',
            html:
              '<p><strong>Equipo:</strong> ' + (data.tipo_equipo || '—') + '</p>' +
              '<p><strong>Subtipo:</strong> ' + (data.subtipo_equipo || '—') + '</p>' +
              '<p><strong>Modelo:</strong> ' + (data.modelo || '—') + '</p>' +
              '<p><strong>Marca:</strong> ' + (data.marca || '—') + '</p>' +
              '<p><strong>Serie:</strong> ' + (data.numero_serie || '') + '</p>' +
              '<p><strong>Estado:</strong> ' + (data.estado_proceso || '') + '</p>' +
              (evidenciaUrl
                ? '<img src="' + evidenciaUrl + '" style="width:100%; max-height:200px; object-fit:contain; border-radius:15px; margin-top:8px;">'
                : '<em style="color:#999;">Sin evidencia</em>'),
            confirmButtonText: 'Cerrar',
            customClass: {
                popup: 'rounded-4 shadow',
                confirmButton: 'btn btn-primary'
            },
            buttonsStyling: false,
            width: 500
        });
    }).fail(function () {
        Swal.fire('Error', 'No se pudo cargar la información.', 'error');
    });
});

// Prepara JSON final para enviar al backend
function prepararProductosJSON() {
    var arr = [];
    $('#tabla-productos tbody tr').each(function (i) {
        var tr  = $(this);
        var pid = tr.data('id');
        var qty = parseInt(tr.find('.cantidad').val(), 10) || 1;
        var pu  = parseFloat(tr.data('precio')) || 0;
        var sp  = parseFloat(tr.find('.sobreprecio').val()) || 0;
        var st  = parseFloat(tr.find('.subtotal').text()) || 0;

        var regs = tr.find('.registro-id-hidden').map(function () {
            return $(this).val() || null;
        }).get();

        arr.push({
            producto_id:     pid,
            cantidad:        qty,
            precio_unitario: pu,
            sobreprecio:     sp,
            subtotal:        st,
            registro_id:     regs
        });
        console.log('🧾 Producto ' + (i + 1) + ':', arr[i]);
    });
    $('#productos_json').val(JSON.stringify(arr));
    console.log('🧪 JSON generado:', arr);
}
</script>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// =====================================================
// Búsqueda / selección de cliente + creación rápida
// =====================================================

// Cerrar modal de éxito de cliente (botón "Listo")
function cerrarModal() {
    const modalEl = document.getElementById("cliente_creado");
    if (!modalEl) return;

    if (window.bootstrap && bootstrap.Modal) {
        const modalInstance = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
        modalInstance.hide();
    }
}

document.addEventListener("DOMContentLoaded", function () {
    const searchInput   = document.getElementById("search-client");
    const clientList    = document.getElementById("client-list");
    const clienteIdInput= document.getElementById("cliente_id");
    const clientDetails = document.getElementById("client-details");
    const formVenta     = document.getElementById("form-venta");

    if (!searchInput || !clientList || !clienteIdInput || !clientDetails || !formVenta) return;

    // Validación antes de enviar el formulario
    formVenta.addEventListener("submit", function (e) {
        console.log("cliente_id al enviar:", clienteIdInput.value);
        if (!clienteIdInput.value) {
            e.preventDefault();
            alert("Por favor selecciona un cliente antes de continuar.");
        }
    });

    // Función para cargar clientes dinámicamente desde el backend
    function loadClients(search) {
        if (typeof search === 'undefined') search = "";

        fetch('/buscar-clientes?search=' + encodeURIComponent(search), {
            method: "GET",
            headers: {
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                "Accept": "application/json"
            }
        })
        .then(function (response) {
            return response.json();
        })
        .then(function (clients) {
            clientList.innerHTML =
                '<li>' +
                '  <button type="button" class="dropdown-item modern-dropdown-item" onclick=\'selectClientFromEncoded("' +
                    encodeURIComponent(JSON.stringify({ id: 1, nombre: "Público en General", apellido: "", telefono: "", email: "", comentarios: "" })) +
                '")\'>' +
                '      Público en General' +
                '  </button>' +
                '</li>' +
                '<li>' +
                '  <button type="button" class="dropdown-item modern-dropdown-item" onclick="openCreateClientModal()">' +
                '      + Crear nuevo cliente' +
                '  </button>' +
                '</li>';

            if ((!clients || clients.length === 0) && search !== "") {
                clientList.innerHTML +=
                    '<li><button type="button" class="dropdown-item disabled">No se encontraron resultados</button></li>';
            } else {
                (clients || []).forEach(function (client) {
                    const clientFullName = ((client.nombre || '').toUpperCase() + ' ' + (client.apellido || '').toUpperCase()).trim();
                    const encodedClient  = encodeURIComponent(JSON.stringify(client));
                    const clientItem     = document.createElement("li");
                    clientItem.innerHTML =
                        '<button type="button" class="dropdown-item modern-dropdown-item" onclick=\'selectClientFromEncoded("' + encodedClient + '")\'>' +
                        (clientFullName || '(SIN NOMBRE)') +
                        '</button>';
                    clientList.appendChild(clientItem);
                });
            }
        })
        .catch(function (error) {
            console.error("Error al cargar clientes:", error);
        });
    }

    // Función para decodificar el cliente
    window.selectClientFromEncoded = function (encoded) {
        const client = JSON.parse(decodeURIComponent(encoded));
        selectClient(client);
    };

    // Función para seleccionar cliente
    window.selectClient = function (client) {
        console.log("Seleccionado:", client);
        console.log("ID del cliente:", client.id);

        const nombre = (client.nombre || '').toUpperCase();
        const apellido = (client.apellido || '').toUpperCase();

        searchInput.value = (nombre + ' ' + apellido).trim();
        clienteIdInput.value = client.id != null ? client.id : "";

        clientDetails.innerHTML =
              '<p><strong>Nombre:</strong> ' + nombre + ' ' + apellido + '</p>'
            + '<p><strong>Teléfono:</strong> ' + (client.telefono || "No registrado") + '</p>'
            + '<p><strong>Email:</strong> ' + (client.email || "No registrado") + '</p>'
            + '<p><strong>Dirección:</strong> ' + (client.comentarios || "No registrado") + '</p>';
        clientDetails.style.padding = "15px";
        clientList.classList.remove("show");
    };

    // Mostrar modal crear cliente
    window.openCreateClientModal = function () {
        const modalFormularioEl = document.getElementById("modal_formulario");
        if (modalFormularioEl && window.bootstrap && bootstrap.Modal) {
            const modalFormulario = new bootstrap.Modal(modalFormularioEl);
            modalFormulario.show();
        }
    };

    // Eventos de búsqueda
    searchInput.addEventListener("input", function () {
        const search = searchInput.value.trim();
        loadClients(search);
        clientList.classList.add("show");
    });

    searchInput.addEventListener("keydown", function (e) {
        if (e.key === "Enter") {
            e.preventDefault();
            const search = searchInput.value.trim();
            loadClients(search);
            clientList.classList.add("show");
        }
    });

    loadClients(); // Carga inicial

    // ------------------------ LÓGICA DE CREACIÓN DE CLIENTE ------------------------
    const formCliente            = document.getElementById("form-cliente");
    const modalFormularioElement = document.getElementById("modal_formulario");
    const modalExitoElement      = document.getElementById("cliente_creado");
    if (!formCliente || !modalFormularioElement || !modalExitoElement) return;

    const modalFormulario = (window.bootstrap && bootstrap.Modal) ? new bootstrap.Modal(modalFormularioElement) : null;
    const modalExito      = (window.bootstrap && bootstrap.Modal) ? new bootstrap.Modal(modalExitoElement) : null;

    formCliente.addEventListener("submit", function (event) {
        event.preventDefault();

        // Limpiar errores
        const errorTelefono = document.getElementById("error-telefono");
        const errorEmail    = document.getElementById("error-email");
        if (errorTelefono) { errorTelefono.style.display = "none"; errorTelefono.textContent = ""; }
        if (errorEmail)    { errorEmail.style.display    = "none"; errorEmail.textContent    = ""; }

        const nombre      = document.getElementById("nombre").value.trim();
        const apellido    = document.getElementById("apellido").value.trim();
        const telefono    = document.getElementById("telefono").value.trim();
        const email       = document.getElementById("email").value.trim();
        const comentarios = document.getElementById("comentarios").value.trim();

        if (!nombre || !apellido || !telefono) {
            alert("Nombre, apellido y teléfono son obligatorios.");
            return;
        }

        // Validar duplicados
        fetch("{{ route('clientes.check_unique') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                "Accept": "application/json"
            },
            body: JSON.stringify({ telefono: telefono, email: email })
        })
        .then(function (res) { return res.json(); })
        .then(function (data) {
            if (data.success) {
                // Guardar nuevo cliente
                fetch("{{ route('clientes.store') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "Accept": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        nombre: nombre,
                        apellido: apellido,
                        telefono: telefono,
                        email: email,
                        comentarios: comentarios
                    })
                })
                .then(function (res) { return res.json(); })
                .then(function (data) {
                    if (data.success && data.cliente) {
                        if (modalFormulario) {
                            modalFormulario.hide();
                        }

                        modalFormularioElement.addEventListener("hidden.bs.modal", function () {
                            if (modalExito) {
                                modalExito.show();
                            }
                            loadClients(); // Recargar lista sin recargar página
                        }, { once: true });

                        formCliente.reset();

                        // Seleccionar automáticamente al cliente recién creado
                        selectClient(data.cliente);
                    } else {
                        alert(data.message || "Ocurrió un error al guardar el cliente.");
                    }
                })
                .catch(function (error) {
                    console.error("Error:", error);
                    alert("Error al guardar el cliente.");
                });
            } else {
                if (data.error_telefono && errorTelefono) {
                    errorTelefono.textContent = data.error_telefono;
                    errorTelefono.style.display = "block";
                }
                if (data.error_email && errorEmail) {
                    errorEmail.textContent = data.error_email;
                    errorEmail.style.display = "block";
                }
            }
        })
        .catch(function (error) {
            console.error("Error:", error);
            alert("Error al verificar la existencia del teléfono o correo.");
        });
    });

    modalExitoElement.addEventListener("hidden.bs.modal", function () {
        // Si quisieras, puedes recargar la página
        // location.reload();
    });
});
</script>

<script>
/**
 * =========================================================
 * ✅ TRADE-IN + TOTAL NETO
 * - Genera tradeins_json
 * - Calcula tradein_total y total_neto para mostrar
 *
 * Requiere:
 *   #tabla-tradeins (con <tbody>)
 *   #btn-add-tradein
 *   #tradeins_json (hidden)
 *   #total_input (hidden total) o #total (texto)
 *
 * Opcionales:
 *   #tradein_total_input, #total_neto_input
 *   #tradein_total_ui,    #total_neto_ui
 * =========================================================
 */
(function () {
  window.tradeins = window.tradeins || [];

  function q(sel){ return document.querySelector(sel); }
  function qa(sel){ return document.querySelectorAll(sel); }

  function num(v){
    var n = parseFloat(String(v == null ? '' : v).replace(/[$,\s]/g,'')); 
    return isNaN(n) ? 0 : n;
  }

  function getTotalOriginal() {
    var hidden = q('#total_input');
    if (hidden) return num(hidden.value);

    var totalNode = q('#total');
    var txt = totalNode && totalNode.textContent ? totalNode.textContent : "0";
    return num(txt);
  }

  // 1) Agregar fila Trade-in
  function addTradeinRow(initial) {
    if (typeof initial === 'undefined') initial = {};
    var tbody = q('#tabla-tradeins tbody');
    if (!tbody) return;

    var tr = document.createElement('tr');

    // Soportar inicial desde back: valor_a_cuenta o valor
    var initValor = num(initial.valor_a_cuenta != null ? initial.valor_a_cuenta : initial.valor);

    tr.innerHTML =
      '<td>' +
      '  <input type="text" class="form-control ti-tipo"' +
      '         placeholder="Tipo de equipo (Ej: Laptop)"' +
      '         value="' + (initial.tipo_equipo || '') + '">' +
      '</td>' +
      '<td><input type="text" class="form-control ti-marca" placeholder="Marca"  value="' + (initial.marca || '') + '"></td>' +
      '<td><input type="text" class="form-control ti-modelo"placeholder="Modelo" value="' + (initial.modelo || '') + '"></td>' +
      '<td><input type="text" class="form-control ti-serie" placeholder="Serie"  value="' + (initial.numero_serie || '') + '"></td>' +
      '<td>' +
      '  <input type="number" step="0.01" min="0" class="form-control ti-valor"' +
      '         placeholder="0.00" value="' + (initValor || '') + '">' +
      '</td>' +
      '<td>' +
      '  <button type="button" class="btn btn-sm btn-danger ti-remove">Eliminar</button>' +
      '</td>';

    tbody.appendChild(tr);

    var inputs = tr.querySelectorAll('input');
    Array.prototype.forEach.call(inputs, function (inp) {
      inp.addEventListener('input', function () {
        prepararTradeinsJSON();
        recalcularTradeinsYTotalNeto();
      });
    });

    var btnRemove = tr.querySelector('.ti-remove');
    if (btnRemove) {
      btnRemove.addEventListener('click', function () {
        tr.remove();
        prepararTradeinsJSON();
        recalcularTradeinsYTotalNeto();
      });
    }

    prepararTradeinsJSON();
    recalcularTradeinsYTotalNeto();
  }

  // 2) Generar JSON trade-ins
  function prepararTradeinsJSON() {
    var arr = [];

    qa('#tabla-tradeins tbody tr').forEach(function (tr) {
      var valor_a_cuenta = num(tr.querySelector('.ti-valor') ? tr.querySelector('.ti-valor').value : 0);

      arr.push({
        tipo_equipo:   tr.querySelector('.ti-tipo')  ? tr.querySelector('.ti-tipo').value  || '' : '',
        descripcion:   tr.querySelector('.ti-desc')  ? tr.querySelector('.ti-desc').value  || '' : '',
        marca:         tr.querySelector('.ti-marca') ? tr.querySelector('.ti-marca').value || '' : '',
        modelo:        tr.querySelector('.ti-modelo')? tr.querySelector('.ti-modelo').value|| '' : '',
        numero_serie:  tr.querySelector('.ti-serie') ? tr.querySelector('.ti-serie').value || '' : '',
        valor_a_cuenta: valor_a_cuenta,          // campo real para BD
        valor:          valor_a_cuenta,          // alias compat
        observaciones: tr.querySelector('.ti-obs') ? tr.querySelector('.ti-obs').value || '' : ''
      });
    });

    window.tradeins = arr;

    var h = q('#tradeins_json');
    if (h) h.value = JSON.stringify(arr);

    return arr;
  }

  // 3) Recalcular total neto
  function recalcularTradeinsYTotalNeto() {
    var totalOriginal = getTotalOriginal();

    var tradeTotal = (window.tradeins || []).reduce(function (acc, t) {
      return acc + num(t.valor_a_cuenta != null ? t.valor_a_cuenta : t.valor);
    }, 0);

    var neto = Math.max(0, totalOriginal - tradeTotal);

    var uiTrade = q('#tradein_total_ui');
    if (uiTrade) uiTrade.textContent = tradeTotal.toFixed(2);
    var uiNeto = q('#total_neto_ui');
    if (uiNeto) uiNeto.textContent = neto.toFixed(2);

    var inTrade = q('#tradein_total_input');
    if (inTrade) inTrade.value = tradeTotal.toFixed(2);
    var inNeto = q('#total_neto_input');
    if (inNeto) inNeto.value = neto.toFixed(2);

    return { totalOriginal: totalOriginal, tradeTotal: tradeTotal, neto: neto };
  }

  // 4) Enganche suave a tu total actual (envolvemos actualizarTotal una sola vez)
  if (typeof window.actualizarTotal === 'function' && !window.__tradeinWrapped) {
    var originalActualizarTotal = window.actualizarTotal;
    window.actualizarTotal = function () {
      var r = originalActualizarTotal.apply(this, arguments);
      prepararTradeinsJSON();
      recalcularTradeinsYTotalNeto();
      return r;
    };
    window.__tradeinWrapped = true;
  }

  document.addEventListener('DOMContentLoaded', function () {
    var btnAdd = q('#btn-add-tradein');
    if (btnAdd) {
      btnAdd.addEventListener('click', function () {
        addTradeinRow();
      });
    }

    var totalNode = q('#total');
    if (totalNode && typeof MutationObserver !== 'undefined') {
      var obs = new MutationObserver(function () {
        prepararTradeinsJSON();
        recalcularTradeinsYTotalNeto();
      });
      obs.observe(totalNode, { childList: true, characterData: true, subtree: true });
    }

    prepararTradeinsJSON();
    recalcularTradeinsYTotalNeto();
  });

  document.addEventListener('DOMContentLoaded', function () {
    var formVenta = q('#form-venta');
    if (!formVenta) return;

    formVenta.addEventListener('submit', function () {
      prepararTradeinsJSON();
      recalcularTradeinsYTotalNeto();
    });
  });

  window.addTradeinRow                = addTradeinRow;
  window.prepararTradeinsJSON         = prepararTradeinsJSON;
  window.recalcularTradeinsYTotalNeto = recalcularTradeinsYTotalNeto;

})();
</script>




@endsection
