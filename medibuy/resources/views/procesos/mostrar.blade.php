<h2>Historial de Procesos</h2>
<table>
    <thead>
        <tr>
            <th>Tipo de Proceso</th>
            <th>Descripción</th>
            <th>Evidencia 1</th>
            <th>Evidencia 2</th>
            <th>Evidencia 3</th>
            <th>Video</th>
        </tr>
    </thead>
    <tbody>
        @foreach($registro->procesos as $proceso)
        <tr>
            <td>{{ $proceso->tipo_proceso }}</td>
            <td>{{ $proceso->descripcion_proceso }}</td>
            <td>
                @if($proceso->evidencia1)
                    <img src="{{ asset('storage/'.$proceso->evidencia1) }}" width="100">
                @endif
            </td>
            <td>
                @if($proceso->evidencia2)
                    <img src="{{ asset('storage/'.$proceso->evidencia2) }}" width="100">
                @endif
            </td>
            <td>
                @if($proceso->evidencia3)
                    <img src="{{ asset('storage/'.$proceso->evidencia3) }}" width="100">
                @endif
            </td>
            <td>
                @if($proceso->video)
                    <video width="200" controls>
                        <source src="{{ asset('storage/'.$proceso->video) }}" type="video/mp4">
                    </video>
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
