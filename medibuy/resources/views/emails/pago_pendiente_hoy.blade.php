<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Recordatorio de Pago</title>
<style>
  /* Reset */
  body, p, h1, h3, ul, li, a {
    margin: 0; padding: 0; 
    font-family: Arial, sans-serif;
  }
  body {
    background-color: #f4f6f8;
    color: #1b1b1b;
    line-height: 1.6;
    padding: 20px;
  }
  .container {
    max-width: 600px;
    background: #ffffff;
    margin: auto;
    border-radius: 8px;
    box-shadow: 0 0 20px rgba(0,0,0,0.05);
    overflow: hidden;
  }
  .header {
    background-color: #004a99;
    color: #ffffff;
    padding: 20px 30px;
    text-align: center;
    font-weight: bold;
    border-bottom: 3px solid #0073e6;
  }
  .header img {
    max-height: 60px;
    margin-bottom: 12px;
  }
  .header small {
    display: block;
    font-weight: normal;
    font-size: 13px;
    color: #cdd9f7;
    margin-top: 4px;
  }
  .content {
    padding: 30px;
  }
  h1 {
    color: #004a99;
    font-size: 22px;
    margin-bottom: 20px;
  }
  h3 {
    color: #004a99;
    margin-top: 30px;
    margin-bottom: 12px;
    font-weight: normal;
  }
  p {
    margin-bottom: 15px;
    font-size: 15px;
  }
  ul {
    list-style-type: none;
    margin-left: 0;
    padding-left: 0;
  }
  li {
    padding: 6px 0;
    font-size: 15px;
    border-bottom: 1px solid #e2e8f0;
  }
  li:last-child {
    border: none;
  }
  .button {
    display: inline-block;
    background-color: #0073e6;
    color: white !important;
    padding: 12px 25px;
    border-radius: 6px;
    text-decoration: none;
    font-weight: 600;
    margin-top: 25px;
    box-shadow: 0 3px 8px rgba(0,115,230,0.4);
  }
  .footer {
    text-align: center;
    font-size: 13px;
    color: #8898aa;
    padding: 20px 30px;
    border-top: 1px solid #e2e8f0;
  }
  .footer .contact-info {
    margin-bottom: 6px;
  }
</style>
</head>
<body>
  <div class="container">

    <div class="header">
      <img src="https://medibuy.grupomedibuy.com/images/logomedy.png" alt="GrupoMedibuy Logo" style="max-height: 60px;" />
      <small>Correo generado automáticamente por el sistema de GrupoMedibuy</small>
    </div>

    <div class="content">
      <h1>Hola {{ $venta->cliente->nombre }},</h1>

      <p>Este es un recordatorio amable de que hoy <strong>{{ \Carbon\Carbon::today()->translatedFormat('d \d\e F \d\e Y') }}</strong> tienes un pago programado correspondiente a tu compra con nosotros.</p>

      <h3>💳 Detalles del Pago</h3>
      <ul>
        <li><strong>Cliente:</strong> {{ $venta->cliente->nombre }} {{ $venta->cliente->apellido }}</li>
        <li><strong>Folio de Venta:</strong> No.2025-{{ $venta->id }}</li>
        <li><strong>Monto a Pagar:</strong> ${{ number_format($venta->pagosFinanciamiento()->whereDate('fecha_pago', today())->first()->monto ?? 0, 2) }}</li>
        <li><strong>Método Sugerido:</strong> En efectivo o transferencia</li>
      </ul>

      <a href="{{ route('ventas.show', $venta->id) }}" class="button" target="_blank" rel="noopener">Ver Detalles de la Venta</a>

      <p style="margin-top: 35px; font-weight: 600;">
        Recuerda: evita recargos realizando tu pago puntualmente.<br>
        ¡Gracias por tu preferencia!
      </p>

      <p>Atentamente,<br>Equipo de Administración – <strong>Grupo Medibuy</strong></p>
    </div>

    <div class="footer">
     <div class="contact-info" >
  Si tienes dudas, quejas o sugerencias, contáctanos vía WhatsApp al teléfono 
  <strong>
    <a href="https://wa.me/527224485191?text={{ urlencode('Hola, soy el cliente ' . $venta->cliente->nombre . ', con el folio de venta No.2025-' . $venta->id . '. Quisiera hacer una consulta relacionada con mi compra. Agradezco su atención.') }}" target="_blank" rel="noopener" style="color: #0073e6; text-decoration: none;">
      +52 722 448 5191
    </a>
  </strong> 
  o al correo 
  <a href="mailto:compras@grupomedibuy.com" style="color: #0073e6; text-decoration: none;">compras@grupomedibuy.com</a>.
</div>


      &copy; {{ date('Y') }} GrupoMedibuy. Todos los derechos reservados.
    </div>

  </div>
</body>
</html>
