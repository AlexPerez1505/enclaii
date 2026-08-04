<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Notificación de Pago Programado</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f8f9fa;
      margin: 0;
      padding: 0;
    }

    .container {
      max-width: 600px;
      margin: 30px auto;
      background: #fff;
      border-radius: 8px;
      overflow: hidden;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }

    .header {
      background: #1e73be;
      color: white;
      padding: 20px;
      text-align: center;
    }

    .header img {
      max-height: 60px;
      margin-bottom: 10px;
    }

    .header small {
      display: block;
      font-size: 13px;
    }

    .content {
      padding: 30px;
      color: #333;
    }

    .content h2 {
      margin-top: 0;
      color: #1e73be;
    }

    .content ul {
      padding-left: 20px;
      line-height: 1.6;
    }

    .button {
      display: inline-block;
      margin-top: 20px;
      padding: 10px 16px;
      background-color: #1e73be;
      color: white;
      text-decoration: none;
      border-radius: 5px;
      font-weight: bold;
    }

    .footer {
      text-align: center;
      font-size: 12px;
      color: #777;
      padding: 20px;
      background-color: #f1f1f1;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="header">
      <img src="https://medibuy.grupomedibuy.com/images/logomedy.png" alt="GrupoMedibuy Logo" />
      <small>Notificación interna - Sistema GrupoMedibuy</small>
    </div>
    <div class="content">
      <h2>⚠️ Notificación de Pago Programado</h2>
      <p>Hoy <strong>{{ \Carbon\Carbon::today()->translatedFormat('d \d\e F \d\e Y') }}</strong>, el cliente <strong>{{ $venta->cliente->nombre }} {{ $venta->cliente->apellido }}</strong> tiene un pago programado.</p>
      <h3>📋 Detalles</h3>
      <ul>
        <li><strong>Cliente:</strong> {{ $venta->cliente->nombre }} {{ $venta->cliente->apellido }}</li>
        <li><strong>Folio de Venta:</strong> No.2025-{{ $venta->id }}</li>
        <li><strong>Monto a pagar:</strong> ${{ number_format($venta->pagosFinanciamiento()->whereDate('fecha_pago', today())->first()->monto ?? 0, 2) }}</li>
      </ul>
      <a href="{{ route('ventas.show', $venta->id) }}" class="button" target="_blank" rel="noopener">Ver Detalles</a>
      <p style="margin-top: 35px;">Este mensaje ha sido generado automáticamente como parte del sistema de alertas para el equipo administrativo.</p>
    </div>
    <div class="footer">
      &copy; {{ date('Y') }} GrupoMedibuy. Todos los derechos reservados.
    </div>
  </div>
</body>
</html>
