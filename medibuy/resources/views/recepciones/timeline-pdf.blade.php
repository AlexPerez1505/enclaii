<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte Global de Recepciones</title>
    <style>
        body {
            font-family: 'Helvetica', sans-serif;
            font-size: 12px;
            color: #333;
            margin: 30px;
        }

        h1 {
            text-align: center;
            color: #005b96;
            font-size: 22px;
            margin-bottom: 5px;
        }

        .intro {
            text-align: center;
            font-size: 13px;
            margin-bottom: 25px;
            color: #555;
        }

        .section {
            margin-top: 30px;
        }

        .section-title {
            background-color: #005b96;
            color: #fff;
            padding: 8px 12px;
            font-size: 15px;
            margin-bottom: 10px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 12px;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 6px 8px;
        }

        th {
            background-color: #e1f0fa;
            font-weight: bold;
        }

        .small {
            font-size: 11px;
            color: #666;
        }

        .pedido-info {
            margin-top: 10px;
            font-style: italic;
            font-size: 13px;
            color: #444;
        }

        .reporte-parrafo {
            margin-bottom: 10px;
            text-align: justify;
            line-height: 1.5;
        }
    </style>
</head>
<body>
    <h1>Reporte Global de Recepciones</h1>
    <p class="intro">Generado el {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}</p>

    @if(request()->filled('pedido_id'))
        <p class="pedido-info">Este reporte incluye únicamente información del <strong>Pedido #{{ request('pedido_id') }}</strong>.</p>
    @else
        <p class="pedido-info">Este reporte incluye todas las recepciones registradas hasta la fecha.</p>
    @endif

    {{-- Calcular totales --}}
    @php
          $totalEsperados = count($componentesPendientes) + count($componentesParciales);
        foreach ($recepciones as $r) {
            $totalEsperados += $r->componentes->count();
        }

        $totalRecibidos = 0;
        foreach ($recepciones as $r) {
            foreach ($r->componentes as $c) {
                $totalRecibidos += $c->cantidad_recibida;
            }
        }
        foreach ($componentesParciales as $c) {
            $totalRecibidos += $c->cantidad_recibida;
        }
    @endphp

    <div class="section">
        <div class="section-title">Resumen General</div>
        <p class="reporte-parrafo">
            El presente documento detalla el historial global de recepciones realizadas dentro del sistema, incluyendo componentes completamente recibidos, aquellos que han sido entregados de forma parcial, y los que aún se encuentran pendientes. 
            Se ha generado con el objetivo de brindar una visión clara del avance de los pedidos.
        </p>
        <p><strong>Total de componentes esperados:</strong> {{ $totalEsperados }}</p>
        <p><strong>Total de componentes recibidos:</strong> {{ $totalRecibidos }}</p>
    </div>

    {{-- Recepciones completas --}}
    <div class="section">
        <div class="section-title">Recepciones Registradas</div>
        @forelse($recepciones as $recepcion)
            <p class="reporte-parrafo">
                El usuario <strong>{{ $recepcion->recibido_por }}</strong> recibió el paquete 
                <strong>#{{ $recepcion->id }}</strong>
                @if($recepcion->pedido)
                    correspondiente al <strong>pedido #{{ $recepcion->pedido->id }}</strong>,
                @endif
                el día <strong>{{ \Carbon\Carbon::parse($recepcion->fecha)->format('d/m/Y') }}</strong> 
                a las <strong>{{ \Carbon\Carbon::parse($recepcion->created_at)->format('H:i') }} horas</strong>,
                registrando un total de <strong>{{ $recepcion->componentes->sum('cantidad_recibida') }}</strong> unidades recibidas. 
                Observaciones generales: <em>{{ $recepcion->observaciones ?? 'Sin observaciones.' }}</em>
            </p>

            <table class="table">
                <thead>
                    <tr>
                        <th>Componente</th>
                        <th>Cantidad Recibida</th>
                        <th>Equipo</th>
                        <th>Observaciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($recepcion->componentes as $componente)
                        <tr>
                            <td>{{ $componente->nombre_componente }}</td>
                            <td>{{ $componente->cantidad_recibida }}</td>
                            <td>{{ $componente->nombre_equipo }}</td>
                            <td>{{ $componente->observaciones ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @empty
            <p>No se han registrado recepciones.</p>
        @endforelse
    </div>



    {{-- Pendientes --}}
    <div class="section">
        <div class="section-title">Componentes Pendientes</div>
        @forelse($componentesPendientes as $c)
            <p class="reporte-parrafo">
                El componente <strong>{{ $c->nombre }}</strong> del equipo ID <strong>{{ $c->equipo_id }}</strong>
                aún no ha sido recibido. Se espera una cantidad total de <strong>{{ $c->cantidad_esperada }}</strong>.
                @if($c->pedido)
                    Este componente forma parte del pedido #{{ $c->pedido->id }}.
                @endif
            </p>

            <table class="table">
                <thead>
                    <tr>
                        <th>Cantidad Esperada</th>
                        <th>Equipo</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ $c->cantidad_esperada }}</td>
                        <td>{{ $c->equipo_id }}</td>
                    </tr>
                </tbody>
            </table>
        @empty
            <p>No hay componentes pendientes.</p>
        @endforelse
    </div>
</body>
</html>
