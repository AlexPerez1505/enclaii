@foreach($solicitudes as $solicitud)
    <div class="card my-3">
        <div class="card-body">
            <strong>{{ $solicitud->material }}</strong> ({{ $solicitud->categoria }}) - {{ $solicitud->cantidad }} unidades <br>
            <strong>Justificación:</strong> {{ $solicitud->justificacion ?? 'No especificada' }} <br>
            Solicitado por: {{ $solicitud->user->name }} <br>
            Estado: 
            <span class="badge 
                @if($solicitud->estado == 'Pendiente') bg-warning 
                @elseif($solicitud->estado == 'Rechazada') bg-danger 
                @elseif($solicitud->estado == 'Entregado') bg-success 
                @else bg-secondary 
                @endif">
                {{ $solicitud->estado }}
            </span>

            @if($solicitud->estado == 'Rechazada' && $solicitud->motivo_rechazo)
                <div class="mt-2 text-danger">
                    <strong>Motivo de rechazo:</strong> {{ $solicitud->motivo_rechazo }}
                </div>
            @endif

            <div class="mt-2">
                @if($solicitud->estado == 'Pendiente')
                    <form method="POST" action="{{ route('solicitudes.marcarEnPlanta', $solicitud) }}" class="d-inline">
                        @csrf
                        @method('PUT')
                        <button class="btn btn-sm btn-outline-info">Marcar como En Planta</button>
                    </form>

                    <button class="btn btn-sm btn-outline-danger" onclick="document.getElementById('form-rechazo-{{ $solicitud->id }}').classList.toggle('d-none')">Rechazar</button>

                    <form method="POST" action="{{ route('solicitudes.rechazar', $solicitud) }}" class="mt-2 d-none" id="form-rechazo-{{ $solicitud->id }}">
                        @csrf
                        @method('PUT')
                        <textarea name="motivo_rechazo" class="form-control mb-2" placeholder="Escribe el motivo del rechazo" required></textarea>
                        <button class="btn btn-sm btn-danger">Confirmar Rechazo</button>
                    </form>
                @endif

                @if($solicitud->estado == 'En Planta')
                    <form method="POST" action="{{ route('solicitudes.entregar', $solicitud) }}" class="d-inline">
                        @csrf
                        @method('PUT')
                        <button class="btn btn-sm btn-outline-success">Marcar como Entregado</button>
                    </form>
                @endif
            </div>
        </div>
    </div>
@endforeach
