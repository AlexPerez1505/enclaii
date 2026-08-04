<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Auditoría de Financiamientos IA</title>
    <style>
        * { box-sizing: border-box; }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #1f2937;
            margin: 0;
            padding: 0;
            background: #ffffff;
        }

        .page {
            padding: 26px 28px 22px;
        }

        .header {
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 14px;
            margin-bottom: 18px;
        }

        .title {
            font-size: 22px;
            font-weight: bold;
            color: #111827;
            margin: 0 0 6px;
        }

        .subtitle {
            font-size: 11px;
            color: #6b7280;
            margin: 0;
        }

        .meta {
            margin-top: 10px;
            font-size: 11px;
            color: #4b5563;
        }

        .meta div {
            margin-bottom: 3px;
        }

        .section {
            margin-top: 18px;
        }

        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #111827;
            margin: 0 0 10px;
            padding-bottom: 6px;
            border-bottom: 1px solid #e5e7eb;
        }

        .summary-box {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 12px 14px;
            line-height: 1.6;
        }

        .kpis {
            width: 100%;
            border-collapse: separate;
            border-spacing: 8px;
            margin-top: 4px;
        }

        .kpi {
            width: 50%;
            vertical-align: top;
            border: 1px solid #e5e7eb;
            background: #f8fafc;
            border-radius: 10px;
            padding: 12px;
        }

        .kpi-label {
            font-size: 10px;
            color: #6b7280;
            text-transform: uppercase;
            margin-bottom: 6px;
        }

        .kpi-value {
            font-size: 18px;
            font-weight: bold;
            color: #111827;
            margin-bottom: 6px;
        }

        .kpi-detail {
            font-size: 11px;
            color: #6b7280;
        }

        .two-col {
            width: 100%;
            border-collapse: separate;
            border-spacing: 8px;
        }

        .two-col td {
            width: 50%;
            vertical-align: top;
        }

        .card {
            border: 1px solid #e5e7eb;
            background: #ffffff;
            border-radius: 10px;
            padding: 12px 14px;
        }

        .list {
            margin: 0;
            padding-left: 18px;
        }

        .list li {
            margin-bottom: 6px;
            line-height: 1.5;
        }

        .bottleneck {
            border-bottom: 1px solid #e5e7eb;
            padding: 8px 0;
        }

        .bottleneck:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .bottleneck-title {
            font-weight: bold;
            color: #111827;
            margin-bottom: 4px;
        }

        .bottleneck-impact {
            color: #6b7280;
            margin-bottom: 4px;
        }

        .table-wrap {
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            overflow: hidden;
            margin-top: 10px;
        }

        table.data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }

        table.data-table thead th {
            background: #f3f4f6;
            color: #374151;
            font-weight: bold;
            text-align: left;
            padding: 9px 10px;
            border-bottom: 1px solid #e5e7eb;
        }

        table.data-table tbody td {
            padding: 9px 10px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: top;
        }

        table.data-table tbody tr:last-child td {
            border-bottom: none;
        }

        .chart {
            margin-top: 12px;
            text-align: center;
            page-break-inside: avoid;
        }

        .chart img {
            max-width: 100%;
            width: 100%;
            height: auto;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            background: #fff;
            padding: 6px;
        }

        .muted {
            color: #6b7280;
        }

        .footer {
            margin-top: 22px;
            padding-top: 10px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            font-size: 10px;
            color: #9ca3af;
        }
    </style>
</head>
<body>
@php
    $analysis = $analysis ?? [];
    $chartImages = $chartImages ?? [];
    $narrative = data_get($analysis, 'narrative', []);
    $kpis = data_get($analysis, 'kpis', []);
    $tables = data_get($analysis, 'tables', []);
@endphp

<div class="page">
    <div class="header">
        <h1 class="title">Auditoría de Financiamientos IA</h1>
        <p class="subtitle">Reporte ejecutivo generado desde el asistente financiero.</p>

        <div class="meta">
            <div><strong>Perfil:</strong> {{ data_get($analysis, 'persona', 'No especificado') }}</div>
            <div><strong>Pregunta:</strong> {{ data_get($analysis, 'question', 'Sin pregunta') }}</div>
            <div><strong>Generado:</strong> {{ data_get($analysis, 'generated_at', now()->format('d/m/Y H:i')) }}</div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Resumen ejecutivo</div>
        <div class="summary-box">
            {{ data_get($narrative, 'resumen_ejecutivo', 'Sin resumen disponible.') }}
        </div>
    </div>

    @if(!empty($kpis))
        <div class="section">
            <div class="section-title">KPIs principales</div>
            <table class="kpis">
                <tr>
                    @foreach($kpis as $index => $kpi)
                        <td class="kpi">
                            <div class="kpi-label">{{ data_get($kpi, 'label', '') }}</div>
                            <div class="kpi-value">{{ data_get($kpi, 'value', '') }}</div>
                            <div class="kpi-detail">{{ data_get($kpi, 'detail', '') }}</div>
                        </td>
                        @if($index % 2 === 1)
                            </tr><tr>
                        @endif
                    @endforeach
                </tr>
            </table>
        </div>
    @endif

    <div class="section">
        <table class="two-col">
            <tr>
                <td>
                    <div class="card">
                        <div class="section-title" style="margin-top:0;">Hallazgos</div>
                        @if(!empty(data_get($narrative, 'hallazgos', [])))
                            <ul class="list">
                                @foreach(data_get($narrative, 'hallazgos', []) as $item)
                                    <li>{{ $item }}</li>
                                @endforeach
                            </ul>
                        @else
                            <div class="muted">Sin hallazgos registrados.</div>
                        @endif
                    </div>
                </td>
                <td>
                    <div class="card">
                        <div class="section-title" style="margin-top:0;">Recomendaciones</div>
                        @if(!empty(data_get($narrative, 'recomendaciones', [])))
                            <ul class="list">
                                @foreach(data_get($narrative, 'recomendaciones', []) as $item)
                                    <li>{{ $item }}</li>
                                @endforeach
                            </ul>
                        @else
                            <div class="muted">Sin recomendaciones registradas.</div>
                        @endif
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="card">
            <div class="section-title" style="margin-top:0;">Cuellos de botella</div>
            @if(!empty(data_get($narrative, 'cuellos_botella', [])))
                @foreach(data_get($narrative, 'cuellos_botella', []) as $item)
                    <div class="bottleneck">
                        <div class="bottleneck-title">{{ data_get($item, 'titulo', '') }}</div>
                        <div class="bottleneck-impact"><strong>Impacto:</strong> {{ data_get($item, 'impacto', '') }}</div>
                        <div>{{ data_get($item, 'detalle', '') }}</div>
                    </div>
                @endforeach
            @else
                <div class="muted">Sin cuellos de botella registrados.</div>
            @endif
        </div>
    </div>

    @if(!empty($tables))
        <div class="section">
            <div class="section-title">Tablas de análisis</div>

            @foreach($tables as $table)
                <div class="table-wrap">
                    <table class="data-table">
                        <thead>
                            <tr>
                                @foreach(data_get($table, 'columns', []) as $col)
                                    <th>{{ $col }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @forelse(data_get($table, 'rows', []) as $row)
                                <tr>
                                    @foreach($row as $cell)
                                        <td>{{ $cell }}</td>
                                    @endforeach
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="20">Sin datos disponibles.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div style="height:10px;"></div>
            @endforeach
        </div>
    @endif

    @if(!empty($chartImages))
        <div class="section">
            <div class="section-title">Gráficas</div>

            @foreach($chartImages as $img)
                <div class="chart">
                    <img src="{{ $img }}" alt="Gráfica del análisis">
                </div>
            @endforeach
        </div>
    @endif

    @if(!empty(data_get($narrative, 'alertas', [])))
        <div class="section">
            <div class="section-title">Alertas</div>
            <div class="card">
                <ul class="list">
                    @foreach(data_get($narrative, 'alertas', []) as $alerta)
                        <li>{{ $alerta }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <div class="footer">
        Documento generado automáticamente por el Asistente Financiero IA
    </div>
</div>
</body>
</html>