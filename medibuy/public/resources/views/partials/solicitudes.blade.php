@foreach($solicitudes as $solicitud)
    <div class="card my-2">
        <div class="card-body">
            <strong>{{ $solicitud->material }}</strong> ({{ $solicitud->categoria }}) - {{ $solicitud->cantidad }} unidades
            <br>
            Estado: 
            <span class="badge 
                @if($solicitud->estado == 'Pendiente') bg-warning 
                @elseif($solicitud->estado == 'Rechazada') bg-danger 
                @elseif($solicitud->estado == 'Entregado') bg-success 
                @else bg-secondary 
                @endif">
                {{ $solicitud->estado }}
            </span>

            @if($solicitud->estado === 'Rechazada')
                <div class="mt-2 text-danger">
                    <strong>Motivo de rechazo:</strong> {{ $solicitud->motivo_rechazo ?? 'No especificado' }}
                </div>
            @endif

            @if($solicitud->estado === 'Entregado')
                <div class="mt-2">
                    <strong>Entregado por:</strong> {{ $solicitud->entregadoPor->name ?? 'N/A' }} <br>
                    <strong>Fecha de entrega:</strong> {{ \Carbon\Carbon::parse($solicitud->fecha_entrega)->format('d/m/Y H:i') }}
                </div>
            @endif

            <br>
            <small>Solicitado el {{ \Carbon\Carbon::parse($solicitud->created_at)->format('d/m/Y H:i') }}</small>
        </div>
    </div>
@endforeach
