<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $titulo ?? 'Estado del formulario' }}</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <style>
    :root{
      --bg:#f6f8fb;
      --card:#fff;
      --line:#e8edf3;
      --text:#0f172a;
      --muted:#64748b;
      --ok:#dcfce7;
      --ok-ink:#166534;
      --warn:#fff7ed;
      --warn-ink:#9a3412;
    }
    body{
      margin:0;
      background:var(--bg);
      font-family:Inter,system-ui,-apple-system,Segoe UI,Roboto,Helvetica,Arial,sans-serif;
      color:var(--text);
      display:flex;
      align-items:center;
      justify-content:center;
      min-height:100vh;
      padding:20px;
    }
    .card{
      width:min(560px, 100%);
      background:var(--card);
      border:1px solid var(--line);
      border-radius:26px;
      padding:28px;
      box-shadow:0 18px 40px rgba(15,23,42,.08);
      text-align:center;
    }
    .icon{
      width:72px;
      height:72px;
      border-radius:999px;
      margin:0 auto 16px;
      display:flex;
      align-items:center;
      justify-content:center;
      font-size:32px;
      font-weight:800;
    }
    .ok .icon{ background:var(--ok); color:var(--ok-ink); }
    .bloqueado .icon,
    .expirado .icon{ background:var(--warn); color:var(--warn-ink); }
    h1{
      margin:0 0 8px;
      font-size:28px;
      font-weight:800;
    }
    p{
      margin:0;
      color:var(--muted);
      line-height:1.6;
    }
  </style>
</head>
<body>
  <div class="card {{ $status ?? 'ok' }}">
    <div class="icon">
      @if(($status ?? 'ok') === 'ok')
        ✓
      @else
        !
      @endif
    </div>

    <h1>{{ $titulo ?? 'Estado del formulario' }}</h1>
    <p>{{ $mensaje ?? 'Operación realizada.' }}</p>
  </div>
</body>
</html>