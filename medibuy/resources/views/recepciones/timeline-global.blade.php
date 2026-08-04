@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
/* ==========================================================================
   ESTILOS DEL HERO FIXED
   ========================================================================== */
.hero-sticky-container {
    position: fixed;
    top: 66px; /* <-- Ajusta este valor al alto de tu navbar */
    left: 0;
    right: 0;
    z-index: 1020;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-bottom: 1px solid #ced4da;
    padding: 20px 0;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    border-radius:30px;
}

/* Botones de acción rápidos (Home y Regresar) */
.btn-nav-quick {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 38px;
    height: 38px;
    background: #fff;
    border: 1px solid #ced4da;
    border-radius: 8px;
    color: #495057;
    transition: background 0.2s, color 0.2s;
    text-decoration: none;
}
.btn-nav-quick:hover {
    background: #e9ecef;
    color: #0d6efd;
}

/* Contenedor flexible para los filtros dentro del Hero */
.filtros-wrapper {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    align-items: center;
}

.filtro-card {
    background: #fff;
    border: 1px solid #ced4da;
    border-radius: 8px;
    padding: 8px 15px;
    display: flex;
    align-items: center;
    gap: 12px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.02);
}

.filtro-label {
    white-space: nowrap;
    font-size: 0.8rem;
    font-weight: 700;
    text-transform: uppercase;
    color: #495057;
    margin-bottom: 0;
    display: inline-flex;
    align-items: center;
}

.filtro-card .form-control {
    height: 36px;
    border-color: #ced4da;
}

.filtro-card .btn {
    height: 36px;
    padding: 0 14px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

/* ==========================================================================
   ESTILOS PARA CARDS CON TAMAÑO FIJO Y SCROLL INTERNO
   ========================================================================== */
.recepcion-card {
    border: 1px solid #e3e6f0;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.02);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    background: #fff;
    display: flex;
    flex-direction: column;
    height: 100%;
}

.recepcion-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.05);
}

.recepcion-header {
    border-bottom: 1px solid #e3e6f0;
    padding: 12px 16px;
    border-top-left-radius: 12px;
    border-top-right-radius: 12px;
    flex-shrink: 0;
}

.card-body-scroll {
    flex-grow: 1;
    overflow-y: auto;
    max-height: 280px;
    padding: 16px;
}

/* Barra de scroll estética */
.card-body-scroll::-webkit-scrollbar {
    width: 6px;
}
.card-body-scroll::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}
.card-body-scroll::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 10px;
}

.card-realizada .recepcion-header { background-color: #f4f6f9; border-left: 5px solid #4e73df; }
.card-parcial .recepcion-header { background-color: #fff9db; border-left: 5px solid #f59f00; }
.card-pendiente .recepcion-header { background-color: #f8f9fa; border-left: 5px solid #6c757d; }

.badge-status {
    font-size: 0.75rem;
    padding: 5px 10px;
    border-radius: 30px;
    font-weight: 600;
}

.componentes-lista {
    list-style: none;
    padding-left: 0;
    margin-bottom: 0;
}

.componente-item {
    padding: 8px 0;
    border-bottom: 1px dashed #eaecf1;
}

.componente-item:last-child {
    border-bottom: none;
}
</style>

{{-- ESPACIADOR: altura del navbar + altura del hero --}}
<div style="height: 170px;"></div>

{{-- BLOQUE HERO FIXED --}}
<div class="hero-sticky-container">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-4 mb-3 mb-lg-0">
                <div class="d-flex align-items-center gap-2">
                    {{-- Botón Home --}}
                    <a href="{{ url('/home') }}" class="btn-nav-quick" title="Ir al Inicio">
                        <i class="fas fa-home"></i>
                    </a>
                    {{-- Botón Regresar --}}
                    <button type="button" class="btn-nav-quick" onclick="if(window.history.length > 1) window.history.back(); else window.location.href='{{ url('/') }}';" title="Regresar">
                        <i class="fas fa-arrow-left"></i>
                    </button>

                    <h2 class="fw-bold text-dark m-0 ms-1"><i class="fas fa-boxes text-primary me-2"></i>Historial</h2>
                </div>
                <small class="text-muted d-block mt-1" style="padding-left: 92px;">Monitoreo global de componentes y pedidos</small>
            </div>
            
            <div class="col-lg-8">
                <div class="filtros-wrapper justify-content-lg-end">
                    {{-- FILTROS DE LA VISTA --}}
                    <form action="{{ route('recepciones.timeline.global') }}" method="GET" class="filtro-card">
                        <span class="filtro-label">
                            <i class="fas fa-filter text-primary me-1"></i> Filtrar:
                        </span>

                        <div class="d-flex align-items-center gap-1">
                            <select name="pedido_id_vista" id="pedido_id_vista" class="form-select form-select-sm" style="width:130px;">
                                <option value="">Todos los Pedidos</option>
                                @foreach ($pedidosDisponibles as $pedido)
                                    <option value="{{ $pedido->id }}" {{ request('pedido_id_vista') == $pedido->id ? 'selected' : '' }}>
                                        Pedido #{{ $pedido->id }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="d-flex align-items-center gap-1">
                            <select name="orden" id="orden" class="form-select form-select-sm" style="width:170px;">
                                <option value="desc" {{ request('orden','desc')=='desc' ? 'selected' : '' }}>
                                    Recientes primero
                                </option>
                                <option value="asc" {{ request('orden')=='asc' ? 'selected' : '' }}>
                                    Antiguos primero
                                </option>
                            </select>
                        </div>
                    </form>

                    {{-- EXPORTAR PDF --}}
                    <form action="{{ route('recepciones.timeline.pdf') }}" method="GET" class="filtro-card">
                        <span class="filtro-label">
                            <i class="fas fa-file-pdf text-danger me-1"></i> PDF:
                        </span>

                        <div class="d-flex align-items-center gap-1">
                            <select name="pedido_id" id="pedido_id" class="form-select form-select-sm" style="width:130px;">
                                <option value="">Todos</option>
                                @foreach ($pedidosDisponibles as $pedido)
                                    <option value="{{ $pedido->id }}">
                                        Pedido #{{ $pedido->id }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <button type="submit" class="btn btn-danger btn-sm text-white" title="Descargar PDF">
                            <i class="fas fa-download"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- CUERPO PRINCIPAL DE LAS CARDS --}}
<div class="container">
    @if ( $recepciones->isEmpty() && count($componentesPendientes) == 0 && count($componentesParciales) == 0)
        <div class="alert alert-light text-center border p-4">
            <i class="fas fa-box-open text-muted fa-2xl mb-3 d-block"></i>
            <span class="text-muted">No hay recepciones ni componentes registrados con los filtros seleccionados.</span>
        </div>
    @else
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 mb-5">

            {{-- 1. RECEPCIONES REALIZADAS --}}
            @foreach ($recepciones as $recepcion)
                <div class="col">
                    <div class="card recepcion-card card-realizada">
                        <div class="recepcion-header d-flex justify-content-between align-items-center">
                            <div>
                                <span class="fw-bold text-primary">Recepción #{{ $recepcion->id }}</span>
                                @if($recepcion->pedido)
                                    <span class="text-muted small ms-1">| #{{ $recepcion->pedido->id }}</span>
                                @endif
                            </div>
                            <span class="badge bg-soft-primary text-primary border border-primary-subtle badge-status">
                                {{ \Carbon\Carbon::parse($recepcion->fecha)->format('d M Y') }}
                            </span>
                        </div>
                        <div class="card-body-scroll">
                            <div class="mb-2 small text-muted">
                                <i class="fas fa-user me-1"></i> <strong>Por:</strong> {{ $recepcion->recibido_por }}
                            </div>
                            
                            @if($recepcion->observaciones)
                                <div class="bg-light p-2 rounded mb-3 small">
                                    <strong>Obs:</strong> {{ $recepcion->observaciones }}
                                </div>
                            @endif

                            <h6 class="fw-bold text-secondary border-bottom pb-1 small text-uppercase">Componentes</h6>
                            <ul class="componentes-lista">
                                @foreach ($recepcion->componentes as $componente)
                                    <li class="componente-item small">
                                        <div class="d-flex justify-content-between">
                                            <span><strong>{{ $componente->nombre_componente }}</strong></span>
                                            <span class="badge bg-secondary">x{{ $componente->cantidad_recibida }}</span>
                                        </div>
                                        <div class="text-muted text-truncate">
                                            <small>Eq: {{ $componente->nombre_equipo }}</small>
                                        </div>
                                        @if ($componente->observaciones)
                                            <div class="text-danger mt-1"><small><em>Obs: {{ $componente->observaciones }}</em></small></div>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endforeach

            {{-- 2. COMPONENTES PARCIALES --}}
            @foreach ($componentesParciales as $componente)
                <div class="col">
                    <div class="card recepcion-card card-parcial">
                        <div class="recepcion-header d-flex justify-content-between align-items-center">
                            <div>
                                <span class="fw-bold text-warning text-truncate d-inline-block" style="max-width: 140px;">{{ $componente->nombre }}</span>
                                @if($componente->pedido)
                                    <span class="text-muted small ms-1">| #{{ $componente->pedido->id }}</span>
                                @endif
                            </div>
                            <span class="badge bg-warning text-dark badge-status">
                                Parcial
                            </span>
                        </div>
                        <div class="card-body-scroll d-flex flex-column justify-content-between">
                            <div>
                                <p class="mb-2 small"><strong>Equipo ID:</strong> {{ $componente->equipo_id }}</p>
                                <div class="row text-center bg-light rounded g-0 p-2 mb-2">
                                    <div class="col-6 border-end">
                                        <small class="text-muted d-block">Esperado</small>
                                        <span class="fw-bold text-secondary">{{ $componente->cantidad_esperada }}</span>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted d-block">Recibido</small>
                                        <span class="fw-bold text-warning">{{ $componente->cantidad_recibida }}</span>
                                    </div>
                                </div>
                            </div>
                            <span class="text-muted small mt-auto"><i class="fas fa-info-circle me-1"></i> Recibido parcialmente.</span>
                        </div>
                    </div>
                </div>
            @endforeach

            {{-- 3. COMPONENTES PENDIENTES --}}
            @foreach ($componentesPendientes as $componente)
                <div class="col">
                    <div class="card recepcion-card card-pendiente">
                        <div class="recepcion-header d-flex justify-content-between align-items-center">
                            <div>
                                <span class="fw-bold text-dark text-truncate d-inline-block" style="max-width: 140px;">{{ $componente->nombre }}</span>
                                @if($componente->pedido)
                                    <span class="text-muted small ms-1">| #{{ $componente->pedido->id }}</span>
                                @endif
                            </div>
                            <span class="badge bg-secondary text-white badge-status">
                                Pendiente
                            </span>
                        </div>
                        <div class="card-body-scroll d-flex flex-column justify-content-between">
                            <div>
                                <p class="mb-1 small"><strong>Equipo ID:</strong> {{ $componente->equipo_id }}</p>
                                <p class="mb-2 small"><strong>Cantidad Esperada:</strong> <span class="badge bg-light text-dark border">{{ $componente->cantidad_esperada }}</span></p>
                            </div>
                            <span class="text-muted small mt-auto"><i class="fas fa-info-circle me-1"></i> Sin entregas.</span>
                        </div>
                    </div>
                </div>
            @endforeach

        </div>
    @endif
</div>
@endsection

<script>
document.addEventListener('DOMContentLoaded', function () {
    const formVista = document.querySelector('form[action="{{ route("recepciones.timeline.global") }}"]');

    document.getElementById('pedido_id_vista').addEventListener('change', function () {
        formVista.submit();
    });

    document.getElementById('orden').addEventListener('change', function () {
        formVista.submit();
    });
});
</script>