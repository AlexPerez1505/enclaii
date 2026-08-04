<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Pedidos</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th, td { border: 1px solid #999; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .titulo { text-align: center; margin-bottom: 20px; font-size: 20px; font-weight: bold; }
        .subtitulo { margin-top: 10px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="titulo">Reporte de Pedidos</div>

    @foreach($pedidos as $pedido)
        <div>
            <div class="subtitulo">Pedido #{{ $pedido->id }} | {{ $pedido->created_at->format('d/m/Y') }}</div>
            <p><strong>Programado para:</strong> {{ optional($pedido->fecha_programada)->format('d/m/Y') ?? 'N/A' }}</p>
            <p><strong>Observaciones:</strong> {{ $pedido->observaciones ?? 'Sin observaciones' }}</p>

            <table>
                <thead>
                    <tr>
                        <th>Equipo</th>
                        <th>Cantidad</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pedido->equipos as $equipo)
                        <tr>
                            <td>{{ $equipo->nombre }}</td>
                            <td>{{ $equipo->cantidad }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <table>
                <thead>
                    <tr>
                        <th>Componente</th>
                        <th>Equipo ID</th>
                        <th>Cantidad Esperada</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pedido->componentes as $componente)
                        <tr>
                            <td>{{ $componente->nombre }}</td>
                            <td>{{ $componente->equipo_id }}</td>
                            <td>{{ $componente->cantidad_esperada }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <hr>
    @endforeach
</body>
</html>
