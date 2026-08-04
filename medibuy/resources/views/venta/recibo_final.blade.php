<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recibo Final de Venta</title>
    <style>
        @page {
            margin: 40px 50px;
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            font-size: 13px;
            color: #2c3e50;
            background-color: #ffffff;
        }

        .container {
            width: 100%;
        }

        .header {
            text-align: center;
            padding-bottom: 15px;
            margin-bottom: 25px;
            border-bottom: 2px solid #19589d;
        }

        .header img {
            height: 60px;
        }

        .header h1 {
            margin: 5px 0;
            font-size: 22px;
            color: #19589d;
        }

        .header p {
            margin: 2px 0;
            font-size: 12px;
            color: #555;
        }

        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #19589d;
            margin-top: 25px;
            margin-bottom: 10px;
            border-bottom: 1px solid #dcdcdc;
            padding-bottom: 4px;
        }

        .client-info, .summary-info {
            line-height: 1.7;
            margin-bottom: 15px;
            background-color: #f7f9fc;
            padding: 12px 16px;
            border-left: 4px solid #19589d;
            border-radius: 5px;
        }

        .client-info p,
        .summary-info p {
            margin: 4px 0;
        }

        .client-info strong,
        .summary-info strong {
            display: inline-block;
            width: 140px;
            color: #2c3e50;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

thead th {
    background-color: #eef2f6; /* azul grisáceo suave */
    color: #2c3e50; /* gris oscuro profesional */
    padding: 8px 10px;
    font-weight: 600;
    font-size: 12px;
    text-align: left;
    border-bottom: 1px solid #d3dce6;
    font-family: 'Segoe UI', sans-serif;
}


        tbody td {
            padding: 8px;
            font-size: 12px;
            border: none;
            vertical-align: top;
        }

        tbody tr {
            border-bottom: 1px solid #f0f0f0;
        }

        tbody img {
            border-radius: 4px;
        }

        .summary {
            background-color: #f5f7fa;
            border-left: 4px solid #27ae60;
            border-radius: 5px;
            padding: 12px 16px;
        }

        .summary p {
            margin: 5px 0;
            font-size: 13px;
        }

        .summary strong {
            width: 150px;
            display: inline-block;
        }

        .badge {
            color: #fff;
            background-color: #27ae60;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 11px;
        }

        .footer {
            text-align: center;
            font-size: 11px;
            color: #7f8c8d;
            margin-top: 35px;
            border-top: 1px solid #dcdcdc;
            padding-top: 10px;
        }
    </style>
</head>
<body>
<div class="container">

    <div class="header">
        <img src="{{ public_path('images/logomedy.png') }}" alt="Grupo MediBuy">
        <h1>Grupo MediBuy</h1>
        <p>RFC EMISOR: TEOA890725GC0 | compras@grupomedibuy.com | 722-448-5191</p>
        <p><strong>Recibo Final de Venta No. 2025-{{ $venta->id }}</strong></p>
        <p>Fecha de emisión: {{ \Carbon\Carbon::now()->format('d/m/Y') }}</p>
    </div>

    <div class="section-title">Datos del Cliente</div>
    <div class="client-info">
        <p><strong>Nombre:</strong> {{ mb_strtoupper($venta->cliente->nombre . ' ' . $venta->cliente->apellido, 'UTF-8') }}</p>
        <p><strong>Teléfono:</strong> {{ $venta->cliente->telefono ?? 'N/A' }}</p>
        <p><strong>Asesor:</strong> {{ mb_strtoupper($venta->usuario->name, 'UTF-8') }}</p>
        <p><strong>Fecha de Inicio:</strong> {{ $venta->created_at->format('d/m/Y') }}</p>
    </div>

    <div class="section-title">Detalle de Productos</div>
    <table>
        <thead>
        <tr>
            <th>Producto</th>
            <th>Descripción</th>
            <th>Cantidad</th>
            <th>Subtotal</th>
        </tr>
        </thead>
        <tbody>
        @foreach($venta->productos as $detalle)
            <tr>
                <td>
                    <img src="{{ public_path('storage/' . ($detalle->producto->imagen ?? 'default.jpg')) }}" width="50" alt="Imagen del producto">
                </td>
                <td>
                    {{ mb_strtoupper($detalle->producto->tipo_equipo ?? '—', 'UTF-8') }}
                    {{ mb_strtoupper($detalle->producto->modelo ?? '', 'UTF-8') }}<br>
                    {{ mb_strtoupper($detalle->producto->marca ?? '', 'UTF-8') }}
                </td>
                <td>{{ $detalle->cantidad }}</td>
                <td>${{ number_format($detalle->cantidad * $detalle->precio_unitario, 2) }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <div class="section-title">Pagos Realizados</div>
    <table>
        <thead>
        <tr>
            <th>#</th>
            <th>Fecha</th>
            <th>Monto</th>
        </tr>
        </thead>
        <tbody>
        @foreach($venta->pagos as $index => $pago)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($pago->fecha_pago)->format('d/m/Y') }}</td>
                <td>${{ number_format($pago->monto, 2) }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <div class="section-title">Resumen de la Venta</div>
    <div class="summary">
        <p><strong>Total de venta:</strong> ${{ number_format($venta->total, 2) }}</p>
        <p><strong>Total pagado:</strong> ${{ number_format($venta->pagos->sum('monto'), 2) }}</p>
        <p><strong>Estado:</strong> <span class="badge">CUENTA LÍQUIDADA</span></p>
    </div>

    <div class="footer">
        Gracias por confiar en Grupo MediBuy.<br>
        Este documento es válido como comprobante de pago completo.<br>
        Consulte términos y condiciones en www.grupomedibuy.com.
    </div>
</div>
</body>
</html>
