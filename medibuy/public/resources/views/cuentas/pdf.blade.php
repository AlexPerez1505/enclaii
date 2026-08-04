<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Cuentas</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #339af0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 6px;
            text-align: center;
        }
        th {
            background-color: #e3f2fd;
        }
        .total-strong {
            font-weight: bold;
        }
        .footer {
            text-align: center;
            font-size: 10px;
            margin-top: 30px;
        }
    </style>
</head>
<body>

    <h2>Reporte de Cuentas</h2>

    <table>
        <thead>
            <tr>
                
                <th>Lugar</th>
                <th>Casetas</th>
                <th>Gasolina</th>
                <th>Viáticos</th>
                <th>Adicional</th>
                <th>Descripción</th>
                <th>Total</th>
                <th>Fecha</th>
            </tr>
        </thead>
        <tbody>
            @foreach($cuentas as $cuenta)
            <tr>
                
                <td>{{ $cuenta->lugar }}</td>
                <td>${{ number_format($cuenta->casetas, 2) }}</td>
                <td>${{ number_format($cuenta->gasolina, 2) }}</td>
                <td>${{ number_format($cuenta->viaticos, 2) }}</td>
                <td>${{ number_format($cuenta->adicional, 2) }}</td>
                <td>{{ $cuenta->descripcion }}</td>
                <td class="total-strong">${{ number_format($cuenta->total, 2) }}</td>
                <td>{{ $cuenta->created_at->format('d/m/Y H:i') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Reporte generado automáticamente por el sistema - {{ now()->format('d/m/Y H:i') }}
    </div>

</body>
</html>
