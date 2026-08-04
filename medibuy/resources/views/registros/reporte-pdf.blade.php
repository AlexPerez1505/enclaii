<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>{{ strtoupper($titulo ?? 'REPORTE') }}</title>

  <style>
    @page { margin: 18px 18px; }

    :root{
      --ink:#0f172a;
      --muted:#64748b;
      --line:#e2e8f0;
      --soft:#f8fafc;
      --chip:#f1f5f9;
    }

    body{
      font-family: DejaVu Sans, sans-serif;
      font-size: 10px;
      color: var(--ink);
      letter-spacing: .2px;
      text-transform: uppercase;
    }

    .top{
      display:flex;
      justify-content:space-between;
      align-items:flex-start;
      margin-bottom: 10px;
      padding-bottom: 10px;
      border-bottom: 1px solid var(--line);
    }
    .h1{
      font-size: 14px;
      font-weight: 800;
      margin: 0;
      line-height: 1.2;
    }
    .meta{
      margin-top: 4px;
      color: var(--muted);
      font-size: 9px;
      line-height: 1.35;
    }
    .meta b{ color: var(--ink); }

    .chips{ margin-top: 7px; }
    .chip{
      display:inline-block;
      padding: 3px 8px;
      border-radius: 999px;
      background: var(--chip);
      border: 1px solid var(--line);
      color: var(--ink);
      font-weight: 700;
      font-size: 8.7px;
      margin: 2px 6px 0 0;
      white-space: nowrap;
    }

    .group{
      margin-top: 12px;
      page-break-inside: avoid;
    }
    .gtitle{
      font-size: 10px;
      font-weight: 800;
      margin: 0 0 6px 0;
      display:flex;
      justify-content:space-between;
      align-items:baseline;
      padding: 6px 8px;
      background: var(--soft);
      border: 1px solid var(--line);
      border-radius: 8px;
    }
    .gcount{
      color: var(--muted);
      font-weight: 700;
      font-size: 9px;
    }

    table{
      width:100%;
      border-collapse: separate;
      border-spacing: 0;
      border: 1px solid var(--line);
      border-radius: 10px;
      overflow: hidden;
    }
    thead th{
      background: var(--soft);
      color: var(--ink);
      font-weight: 800;
      font-size: 9px;
      padding: 7px 8px;
      border-bottom: 1px solid var(--line);
      text-align: left;
    }
    tbody td{
      padding: 7px 8px;
      border-bottom: 1px solid var(--line);
      font-size: 9.5px;
      vertical-align: top;
    }
    tbody tr:last-child td{ border-bottom: 0; }

    thead th + th,
    tbody td + td{
      border-left: 1px solid var(--line);
    }

    .mono{ font-weight: 800; letter-spacing: .6px; }
    .muted{ color: var(--muted); }

    .footer{
      margin-top: 10px;
      padding-top: 8px;
      border-top: 1px solid var(--line);
      color: var(--muted);
      font-size: 8.5px;
    }
  </style>
</head>

<body>
  @php
    // ===== fallbacks para que JAMÁS truene =====
    $titulo = $titulo ?? 'REPORTE';
    $total = $total ?? 0;
    $filtrosTxt = $filtrosTxt ?? [];

    // $rows puede venir si alguien no manda groups
    $rows = $rows ?? collect();

    // $groups: si no viene, lo armamos
    $groups = $groups ?? collect(['TODOS' => $rows]);

    // Estados: soporta string (estado_proceso) y numérico (estado_actual)
    $estados = $estados ?? [
      'registro'      => 'REGISTRO',
      'hojalateria'   => 'HOJALATERÍA',
      'mantenimiento' => 'MANTENIMIENTO',
      'stock'         => 'STOCK',
      'vendido'       => 'VENDIDO',
      'defectuoso'    => 'DEFECTUOSO',
      1 => 'STOCK',
      2 => 'VENDIDO',
      3 => 'MANTENIMIENTO',
      4 => 'DEFECTUOSO',
    ];
  @endphp

  <div class="top">
    <div>
      <div class="h1">{{ strtoupper($titulo) }}</div>

      <div class="meta">
        GENERADO: {{ strtoupper(now()->format('Y-m-d H:i')) }}
        &nbsp;|&nbsp; TOTAL: <b>{{ $total }}</b>
      </div>

      @if(!empty($filtrosTxt))
        <div class="chips meta">
          FILTROS:
          @foreach($filtrosTxt as $f)
            <span class="chip">{{ strtoupper($f) }}</span>
          @endforeach
        </div>
      @endif
    </div>
  </div>

  @foreach($groups as $groupName => $items)
    <div class="group">
      <div class="gtitle">
        <div>{{ strtoupper($groupName) }}</div>
        <div class="gcount">{{ $items->count() }}</div>
      </div>

      <table>
        <thead>
          <tr>
            <th style="width:18%">SERIE</th>
            <th style="width:16%">MARCA</th>
            <th style="width:16%">MODELO</th>
            <th style="width:16%">TIPO</th>
            <th style="width:16%">SUBTIPO</th>
            <th style="width:18%">ESTADO</th>
          </tr>
        </thead>
        <tbody>
          @foreach($items as $r)
            @php
              // Estado preferido: estado_proceso (string). Si no existe, usar estado_actual (num).
              $estadoKey = $r->estado_proceso ?? $r->estado_actual ?? 'registro';
              $estadoTxt = $estados[$estadoKey] ?? $estados[(int)$estadoKey] ?? $estadoKey ?? '—';
            @endphp
            <tr>
              <td class="mono">{{ strtoupper($r->numero_serie ?? '—') }}</td>
              <td>{{ strtoupper($r->marca ?? '—') }}</td>
              <td>{{ strtoupper($r->modelo ?? '—') }}</td>
              <td>{{ strtoupper($r->tipo_equipo ?? '—') }}</td>
              <td>{{ strtoupper($r->subtipo_equipo ?? '—') }}</td>
              <td>{{ strtoupper($estadoTxt ?? '—') }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  @endforeach

  <div class="footer">
    DOCUMENTO GENERADO AUTOMÁTICAMENTE — INVENTARIO
  </div>
</body>
</html>
