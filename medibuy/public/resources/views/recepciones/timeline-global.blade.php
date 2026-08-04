@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="{{ asset('css/timeline.css') }}">
<style>
@media (max-width: 768px) {
  .timeline {
    position: relative;
    padding-left: 40px;
  }

  .timeline::before {
    content: '';
    position: absolute;
    top: 0;
    left: 20px;
    width: 3px;
    height: 100%;
    background-color: #d0d0d0;
    z-index: 0;
  }

  .timeline__event {
    flex-direction: column;
    width: 90vw;
    margin: 30px auto;
    position: relative;
    padding-left: 40px;
    background: transparent;
  }

  .timeline__event__icon {
    position: absolute;
    left: 3px;
    top: 0;
    width: 36px;
    height: 36px;
    background: #f6a4ec;
    color: #9251ac;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 2;
    box-shadow: 0 0 0 3px #fff;
    transition: transform 0.3s ease, background-color 0.3s ease, color 0.3s ease;
  }

  .timeline__event__icon:hover {
    transform: scale(1.2) rotate(5deg);
    background-color: #fff;
    color: #000;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
  }

  .timeline__event__content {
    width: 100%;
    border-radius: 6px;
    background: #fff;
    padding: 16px;
    margin-left: 10px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
  }

  .timeline__event__date {
    font-size: 1rem;
    padding: 8px;
    background: #9251ac;
    color: #f6a4ec;
    border-radius: 6px;
    margin-bottom: 10px;
  }

  .timeline__event__title {
    font-size: 1rem;
    font-weight: bold;
    color: #9251ac;
  }
}

</style>
<div class="container mt-4">
    <h2 class="seccion-titulo">Historial Global de Recepciones</h2>
<form action="{{ route('recepciones.timeline.pdf') }}" method="GET" class="mb-4 d-flex align-items-center gap-3"> 
    <label for="pedido_id">Filtrar por pedido:</label>
    <select name="pedido_id" id="pedido_id" class="form-control" style="width: 200px;">
        <option value="">Todos</option>
        @foreach ($pedidosDisponibles as $pedido)
            <option value="{{ $pedido->id }}">{{ $pedido->id }}</option>
        @endforeach
    </select>

    <button type="submit" class="btn btn-danger">
        <i class="fas fa-file-pdf"></i> Exportar PDF
    </button>
</form>

    @if ($recepciones->isEmpty() && $componentesPendientes->isEmpty() && $componentesParciales->isEmpty())
        <p class="mensaje-vacio">No hay recepciones ni componentes registrados.</p>
    @else
        <div class="timeline">
            {{-- Recepciones realizadas --}}
            @foreach ($recepciones as $index => $recepcion)
                @php
                    $tipo = match($index % 3) {
                        0 => 'timeline__event--type1',
                        1 => 'timeline__event--type2',
                        2 => 'timeline__event--type3',
                    };
                @endphp
                <div class="timeline__event animated fadeInUp delay-{{ 3 - ($index % 3) }}s {{ $tipo }}">
                    <div class="timeline__event__icon">
                        <i class="fas fa-box-open"></i>
                    </div>
                    <div class="timeline__event__date">
                        {{ \Carbon\Carbon::parse($recepcion->fecha)->format('d M Y') }}
                    </div>
                    <div class="timeline__event__content">
                        <div class="timeline__event__title">
                            Recepción #{{ $recepcion->id }}
                            @if($recepcion->pedido)
                                - Pedido #{{ $recepcion->pedido->id }}
                            @endif
                            - {{ $recepcion->recibido_por }}
                        </div>
                        <div class="timeline__event__description">
                            <p><strong>Observaciones:</strong> {{ $recepcion->observaciones ?? 'Sin observaciones generales.' }}</p>
                            <ul>
                                @foreach ($recepcion->componentes as $componente)
                                    <li style="margin-bottom: 10px;">
                                        <strong>{{ $componente->nombre_componente }}</strong>
                                        <span> x{{ $componente->cantidad_recibida }}</span><br>
                                        <small><strong>Equipo:</strong> {{ $componente->nombre_equipo }}</small><br>
                                        @if ($componente->observaciones)
                                            <em>Obs: {{ $componente->observaciones }}</em>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endforeach

            {{-- Componentes parcialmente recibidos --}}
            @foreach ($componentesParciales as $componente)
                <div class="timeline__event animated fadeInUp delay-1s timeline__event--type5">
                    <div class="timeline__event__icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div class="timeline__event__date">
                        Parcial
                    </div>
                    <div class="timeline__event__content">
                        <div class="timeline__event__title">
                            {{ $componente->nombre }}
                            @if($componente->pedido)
                                (Pedido #{{ $componente->pedido->id }})
                            @endif
                        </div>
                        <div class="timeline__event__description">
                            <p><strong>Cantidad Esperada:</strong> {{ $componente->cantidad_esperada }}</p>
                            <p><strong>Recibida:</strong> {{ $componente->cantidad_recibida }}</p>
                            <p><strong>Equipo ID:</strong> {{ $componente->equipo_id }}</p>
                            <p><em>Este componente ha sido recibido parcialmente.</em></p>
                        </div>
                    </div>
                </div>
            @endforeach

            {{-- Componentes esperados aún no recibidos --}}
            @foreach ($componentesPendientes as $componente)
                <div class="timeline__event animated fadeInUp delay-1s timeline__event--type4">
                    <div class="timeline__event__icon">
                        <i class="fas fa-hourglass-half"></i>
                    </div>
                    <div class="timeline__event__date">
                        Pendiente
                    </div>
                    <div class="timeline__event__content">
                        <div class="timeline__event__title">
                            {{ $componente->nombre }}
                            @if($componente->pedido)
                                (Pedido #{{ $componente->pedido->id }})
                            @endif
                        </div>
                        <div class="timeline__event__description">
                            <p><strong>Cantidad Esperada:</strong> {{ $componente->cantidad_esperada }}</p>
                            <p><strong>Equipo ID:</strong> {{ $componente->equipo_id }}</p>
                            <p><em>Este componente aún no ha sido recibido.</em></p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
