<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Catálogo</title>
  <style>
    *{font-family: DejaVu Sans, sans-serif;}
    body{font-size:12px;color:#0f172a;margin:22px;}
    .top{display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:14px;}
    .h1{font-size:16px;font-weight:800;letter-spacing:.2px;}
    .meta{color:#64748b;font-weight:700;font-size:11px;margin-top:4px;}
    .card{border:1px solid #e7ebf0;border-radius:12px;padding:12px;margin-bottom:12px;}
    .sec{font-weight:800;color:#0f172a;margin:12px 0 8px;font-size:13px;}
    table{width:100%;border-collapse:collapse;}
    th,td{border-bottom:1px solid #eef2f7;padding:8px 6px;vertical-align:top;}
    th{color:#475569;font-weight:800;text-align:left;font-size:11px;letter-spacing:.2px;}
    td{font-weight:700;color:#0f172a;}
    .muted{color:#64748b;font-weight:700;}
    .right{text-align:right;}
  </style>
</head>
<body>
  <div class="top">
    <div>
      <div class="h1">Catálogo</div>
      <div class="meta">
        Generado: {{ $generatedAt?->format('Y-m-d H:i') }}
        @if(!empty($q)) • Filtro: "{{ $q }}" @endif
        • Scope: {{ $scope }}
      </div>
    </div>
  </div>

  @if(($scope==='all' || $scope==='productos') && !empty($productos) && count($productos))
    <div class="card">
      <div class="sec">Productos</div>
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Tipo</th>
            <th>Marca</th>
            <th>Modelo</th>
            <th class="right">Precio</th>
            <th>Familias</th>
          </tr>
        </thead>
        <tbody>
          @foreach($productos as $p)
            @php $fam = $p->familias?->pluck('nombre')->join(', ') ?? ''; @endphp
            <tr>
              <td class="muted">{{ $p->id }}</td>
              <td>{{ $p->tipo_equipo ?? '' }}</td>
              <td class="muted">{{ $p->marca ?? '' }}</td>
              <td class="muted">{{ $p->modelo ?? '' }}</td>
              <td class="right">${{ number_format((float)($p->precio ?? 0), 2) }}</td>
              <td class="muted">{{ $fam }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  @endif

  @if(($scope==='all' || $scope==='paquetes') && !empty($paquetes) && count($paquetes))
    <div class="card">
      <div class="sec">Paquetes</div>
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Contenido</th>
            <th class="right">Total</th>
          </tr>
        </thead>
        <tbody>
          @foreach($paquetes as $pkg)
            @php
              $total = 0;
              $contenido = '';
              if ($pkg->productos) {
                $contenido = $pkg->productos->map(function($pp){
                  $n = $pp->tipo_equipo ?? 'Producto';
                  $m = trim(($pp->marca ?? '').' '.($pp->modelo ?? ''));
                  return trim($n.' '.$m);
                })->implode(' | ');

                $total = $pkg->productos->sum(function($pp){
                  $precio = (float)($pp->precio ?? 0);
                  $cant = (int)($pp->pivot->cantidad ?? 1);
                  return $precio * max(1, $cant);
                });
              }
            @endphp
            <tr>
              <td class="muted">{{ $pkg->id }}</td>
              <td>{{ $pkg->nombre ?? 'Paquete' }}</td>
              <td class="muted">{{ $contenido }}</td>
              <td class="right">${{ number_format((float)$total, 2) }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  @endif
</body>
</html>
