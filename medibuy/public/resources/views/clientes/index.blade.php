@extends('layouts.app')
@section('title', 'Clientes')
@section('titulo', 'Clientes')
@section('content')
<style>
    body {
        background-color: #f0f4f8;
        font-family: 'Inter', sans-serif;
    }

    .container {
        max-width: 900px;
        margin: 3rem auto;
        background-color: #ffffff;
        padding: 2.5rem;
        border-radius: 1rem;
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.03);
        animation: fadeIn 0.6s ease-in-out both;
    }

    @keyframes fadeIn {
        0% { opacity: 0; transform: translateY(20px); }
        100% { opacity: 1; transform: translateY(0); }
    }

    h1 {
        font-size: 2rem;
        color: #5a5f73;
        font-weight: 700;
        margin-bottom: 1rem;
        text-align: center;
    }

    .total-clientes {
        text-align: center;
        color: #6c757d;
        margin-bottom: 2rem;
    }

   /* Contenedor general del buscador + botón */
.search-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1rem;
    margin-bottom: 2rem;
    flex-wrap: wrap;
}

/* Contenedor del input con ícono */
.search-wrapper {
    position: relative;
    flex: 1;
    min-width: 250px;
}

/* Input */
.search-wrapper input {
    width: 100%;
    padding: 0.65rem 1rem 0.65rem 2.5rem; /* espacio para el ícono */
    border-radius: 0.6rem;
    border: 1px solid #dee2e6;
    font-size: 0.95rem;
    transition: border-color 0.3s ease;
    background-color: #ffffff;
    margin: 0; /* elimina separación innecesaria */
    line-height: 1.25rem;
}

/* Foco */
.search-wrapper input:focus {
    outline: none;
    border-color: #a5d8ff;
    box-shadow: 0 0 0 3px rgba(165, 216, 255, 0.3);
}

/* Ícono de lupa */
.search-icon {
    position: absolute;
    top: 50%;
    left: 0.9rem;
    transform: translateY(-50%);
    color: #adb5bd;
    pointer-events: none;
    width: 18px;
    height: 18px;
}

/* Botón personalizado */
.btn-primary {
    background: linear-gradient(90deg, #a5d8ff, #c5f6fa);
    color: #2c3e50;
    font-weight: 600;
    padding: 0.65rem 1.4rem;
    font-size: 0.95rem;
    border: none;
    border-radius: 0.6rem;
    transition: background 0.3s ease, transform 0.2s ease;
    display: inline-block;
    box-shadow: 0 4px 12px rgba(165, 216, 255, 0.3);
    text-decoration: none;
    line-height: 1.25rem;
    white-space: nowrap;
}

/* Hover */
.btn-primary:hover {
    background: linear-gradient(90deg, #99e9f2, #b2f2bb);
    transform: translateY(-1px);
}

.alertas-list {
    list-style: none;
    padding: 0;
}

.alertas-list li {
    background-color: #e3fafc;
    padding: 1rem 1.25rem;
    margin-bottom: 1rem;
    border-left: 5px solid #63e6be;
    border-radius: 0.6rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    transition: transform 0.2s ease;
}

.alertas-list li:hover {
    transform: scale(1.01);
    background-color: #f1f3f5;
}

.alertas-list li span {
    font-weight: 600;
    color: #343a40;
}

.alertas-list li .acciones a {
    margin-left: 10px;
    color: #748ffc;
    text-decoration: none;
    transition: color 0.2s ease;
}

.alertas-list li .acciones a:hover {
    color: #5c7cfa;
}

    /* Botón flotante en móvil */
.btn-float {
    position: fixed;
    bottom: 20px;
    right: 20px;
    border-radius: 999px; /* Forma de píldora */
    padding: 0.65rem 1.4rem;
    font-size: 1rem;
    font-weight: 600;
    background: linear-gradient(90deg, #a5d8ff, #c5f6fa);
    color: #2c3e50;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
    display: none;
    border: none;
    transition: background 0.3s ease, transform 0.2s ease;
}

    @media (max-width: 600px) {
        .container {
            padding: 1.5rem;
        }

        ul li {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.5rem;
        }

        .btn-float {
        display: inline-block;
    }
    }
.btn-float:hover {
    background: linear-gradient(90deg, #99e9f2, #b2f2bb);
    transform: translateY(-2px);
}
.grid-clientes {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
}

.card-cliente {
    background-color: #ffffff;
    border: 1px solid #dee2e6;
    border-radius: 0.8rem;
    padding: 1rem 1.25rem;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
    transition: transform 0.2s ease;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    min-height: 160px;
}

.card-cliente:hover {
    transform: translateY(-2px);
    background-color: #f8f9fa;
}

.card-cliente h2 {
    font-size: 1.1rem;
    font-weight: 600;
    color: #343a40;
    margin-bottom: 0.5rem;
}

.card-cliente p {
    margin: 0.2rem 0;
    color: #495057;
    font-size: 0.9rem;
}

.card-cliente .acciones {
    margin-top: auto;
    display: flex;
    gap: 1rem;
    justify-content: flex-start;
}

.card-cliente .acciones a {
    color: #4dabf7;
    font-size: 1.2rem;
    transition: color 0.2s ease;
}

.card-cliente .acciones a:hover {
    color: #228be6;
}
.card-categoria-1 {
    border-left: 5px solid #4dabf7; /* Azul */
}

.card-categoria-2 {
    border-left: 5px solid #63e6be; /* Verde */
}

.card-categoria-3 {
    border-left: 5px solid #ffa94d; /* Naranja */
}

.card-categoria-4 {
    border-left: 5px solid #ff6b6b; /* Rojo */
}

.card-categoria-5 {
    border-left: 5px solid #b197fc; /* Violeta claro */
}

.card-categoria-6 {
    border-left: 5px solid #20c997; /* Verde esmeralda */
}

.card-categoria-7 {
    border-left: 5px solid #fab005; /* Amarillo intenso */
}

.card-categoria-8 {
    border-left: 5px solid #845ef7; /* Morado */
}

.card-categoria-9 {
    border-left: 5px solid #ff8787; /* Rosa coral */
}

.card-categoria-10 {
    border-left: 5px solid #66d9e8; /* Celeste */
}

</style>
<div class="container">
    <p class="total-clientes">Total de clientes: <strong>{{ count($clientes) }}</strong></p>
<div class="search-bar">
    <div class="search-wrapper">
        <i class="search-icon" data-feather="search"></i>
        <input type="text" id="searchCliente" placeholder="Buscar cliente...">
    </div>
    <a href="{{ route('clientes.create') }}" class="btn btn-primary">+ Nuevo Cliente</a>
</div>
@if($alertasGenerales->isNotEmpty())
    <div style="background-color: #fff3cd; color: #856404; border-radius: 0.6rem; padding: 1rem; margin-bottom: 2rem; border: 1px solid #ffeeba;">
        <strong>🔔 Atenciones pendientes:</strong>
   <ul class="alertas-list" style="margin: 0.5rem 0 0 1rem; padding-left: 0;">
    @foreach($alertasGenerales as $alerta)
        <li>
            <span>
                @if(isset($alerta['cliente']))
                    {{ $alerta['cliente']->nombre }} {{ $alerta['cliente']->apellido }} –
                @else
                    Cliente no disponible –
                @endif

                @if($alerta['dias'] < 0)
                    seguimiento vencido ({{ \Carbon\Carbon::parse($alerta['fecha'])->format('d/m/Y') }})
                @elseif($alerta['dias'] === 0)
                    seguimiento para hoy ({{ \Carbon\Carbon::parse($alerta['fecha'])->format('d/m/Y') }})
                @else
                    seguimiento en {{ $alerta['dias'] }} días ({{ \Carbon\Carbon::parse($alerta['fecha'])->format('d/m/Y') }})
                @endif
            </span>

            <form method="POST" action="{{ route('seguimientos.completar', $alerta['seguimiento_id']) }}">
                @csrf
                @method('PATCH')
                <button type="submit" style="background:#4dabf7; color:white; border:none; padding:0.3rem 0.7rem; border-radius:0.3rem; cursor:pointer;">
                    Completado
                </button>
            </form>
        </li>
    @endforeach
</ul>

    </div>
@else
    <div style="background-color: #d1e7dd; color: #0f5132; border-radius: 0.6rem; padding: 1rem; margin-bottom: 2rem; border: 1px solid #badbcc;">
        <strong>✅ No hay atenciones pendientes.</strong>
    </div>
@endif



<div id="lista-clientes" class="grid-clientes">
@foreach($clientes as $cliente)
    @php
        $seguimientosCliente = $seguimientosPorCliente[$cliente->id] ?? collect();
        $seguimientoPendiente = $seguimientosCliente->whereNull('completado')->sortBy('fecha_seguimiento')->first();
    @endphp

    <div class="card-cliente card-categoria-{{ $cliente->categoria_id }}">
        <h2>{{ $cliente->nombre }} {{ $cliente->apellido }}</h2>
        <p><strong>Tel:</strong> {{ $cliente->telefono ?? 'No registrado' }}</p>
        <p><strong>Email:</strong> {{ $cliente->email ?? 'No registrado' }}</p>
        <p><strong>Dirección:</strong> {{ $cliente->comentarios ?? 'No registrado' }}</p>
        <p><strong>Categoría:</strong> {{ $cliente->categoria->nombre ?? 'No registrado' }}</p>

        @if($seguimientoPendiente)
            @php
                $fecha = \Carbon\Carbon::parse($seguimientoPendiente->fecha_seguimiento)->startOfDay();
                $dias = now()->startOfDay()->diffInDays($fecha, false);
            @endphp

            @if($dias < 0)
                <div class="alerta-seguimiento" style="background-color: #f8d7da; color: #721c24; padding: 0.5rem; border-radius: 0.5rem; margin-top: 0.5rem;">
                    ⚠️ Seguimiento vencido el {{ $fecha->format('d/m/Y') }}
                    <form method="POST" action="{{ route('seguimientos.completar', $seguimientoPendiente->id) }}" style="margin-top: 0.5rem;">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-sm" style="background-color: #e03131; color: white; border: none; border-radius: 0.4rem; padding: 0.3rem 0.6rem;">
                            Marcar como completado
                        </button>
                    </form>
                </div>
            @elseif($dias === 0)
                <div class="alerta-seguimiento" style="background-color: #ffeeba; color: #856404; padding: 0.5rem; border-radius: 0.5rem; margin-top: 0.5rem;">
                    📅 Seguimiento para hoy ({{ $fecha->format('d/m/Y') }})
                    <form method="POST" action="{{ route('seguimientos.completar', $seguimientoPendiente->id) }}" style="margin-top: 0.5rem;">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-sm" style="background-color: #4dabf7; color: white; border: none; border-radius: 0.4rem; padding: 0.3rem 0.6rem;">
                            Marcar como completado
                        </button>
                    </form>
                </div>
            @elseif($dias > 0 && $dias <= 7)
                <div class="alerta-seguimiento" style="background-color: #fff3cd; color: #856404; padding: 0.5rem; border-radius: 0.5rem; margin-top: 0.5rem;">
                    📅 Seguimiento en {{ $dias }} día{{ $dias > 1 ? 's' : '' }} ({{ $fecha->format('d/m/Y') }})
                </div>
            @endif
        @endif

        <div class="acciones">
            <a href="{{ route('seguimientos.index', $cliente->id) }}" title="Seguimientos"><i data-feather="file-text"></i></a>
            <a href="{{ route('clientes.edit', $cliente->id) }}" title="Editar"><i data-feather="edit-2"></i></a>
        </div>
    </div>
@endforeach




</div>
</div>
<a href="{{ route('clientes.create') }}" class="btn btn-float d-md-none" title="Nuevo Cliente">+</a>
<!-- Feather Icons -->
<script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
<script>
    feather.replace();
</script>

<!-- Buscador -->
<script>
document.getElementById('searchCliente').addEventListener('input', function () {
    const query = this.value.toLowerCase();
    document.querySelectorAll('#lista-clientes .card-cliente').forEach(item => {
        const nombre = item.querySelector('h2').textContent.toLowerCase();
        item.style.display = nombre.includes(query) ? 'flex' : 'none';
    });
});

</script>
@endsection
