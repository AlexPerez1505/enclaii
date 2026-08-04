{{-- resources/views/prestamos/pdf.blade.php --}}
@php
    use Illuminate\Support\Carbon;

    $fmt = fn($d, $f='Y-m-d') => $d ? Carbon::parse($d)->format($f) : '—';
    $dt  = fn($d) => $d ? Carbon::parse($d)->format('Y-m-d H:i') : '—';
    $cliente = optional($prestamo->cliente)->nombre ?? '—';

    // KPIs
    $total     = $prestamo->registros->count();
    $salidos   = $prestamo->registros->filter(fn($r)=>!empty($r->pivot->salida_scanned_at))->count();
    $devueltos = $prestamo->registros->filter(fn($r)=>!empty($r->pivot->devolucion_scanned_at))->count();
    $vendidos  = $prestamo->registros->filter(fn($r)=>!empty($r->pivot->vendido_scanned_at))->count();

    // Faltantes de devolución (no devuelto y no vendido)
    $faltanDevol = $prestamo->registros->filter(function($r){
        return empty($r->pivot->devolucion_scanned_at) && empty($r->pivot->vendido_scanned_at);
    });

    // Listado vendidos
    $listVendidos = $prestamo->registros->filter(fn($r)=>!empty($r->pivot->vendido_scanned_at));
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>Préstamo #{{ $prestamo->id }}</title>
<style>
  /* Márgenes de página A4 para Dompdf */
  @page { margin: 24mm 15mm 20mm 15mm; }

  * { box-sizing: border-box; }
  body { font-family: DejaVu Sans, Arial, sans-serif; color:#2a2e35; font-size:12px; }
  h1 { font-size:20px; margin:0 0 4px 0; }
  h2 { font-size:14px; margin:10px 0 6px 0; }
  .small { color:#7a7f87; font-size:10px; }
  .hr { height:1px; background:#e9ecef; margin:8px 0 12px; }

  .header { width:100%; }
  .header td { vertical-align:top; }
  .brand { color:#34c29e; font-weight:700; letter-spacing:.3px; }

  .meta { width:100%; border-collapse:collapse; margin-top:6px; table-layout: fixed; }
  .meta td { padding:6px 8px; border:1px solid #e9ecef; word-break: break-word; overflow-wrap: anywhere; }
  .meta .k { width:32%; background:#fafbfc; color:#7a7f87; }

  .kpis { width:100%; border-collapse:collapse; margin-top:8px; table-layout: fixed; }
  .kpis th, .kpis td { border:1px solid #e9ecef; padding:8px; text-align:center; }
  .kpis th { background:#f6fffb; border-color:#dff5ee; color:#127c64; font-size:11px; text-transform:uppercase; letter-spacing:.03em; }

  .chips { margin:6px 0 0 0; }
  .chip { display:inline-block; border:1px solid #e9ecef; border-radius:999px; padding:2px 8px; font-size:11px; margin:0 6px 6px 0; }
  .chip-warn { background:#fef3c7; color:#92400e; border-color:#f5d48a; }
  .chip-sold { background:#fee2e2; color:#991b1b; border-color:#f5bdbd; }

  .badge { display:inline-block; border:1px solid #e9ecef; border-radius:999px; padding:2px 8px; font-size:11px; }
  .badge-activo { background:#e7fdf6; color:#127c64; border-color:#dff5ee; }
  .badge-retrasado { background:#fff4e5; color:#9a5800; }
  .badge-devuelto { background:#eef7ff; color:#0b4c8c; }
  .badge-cancelado { background:#f7f7f8; color:#6a6e76; }
  .badge-vendido { background:#ffeef0; color:#b6232a; }

  /* Tabla de equipos: evitar desbordes */
  .table {
    width:100%; border-collapse:collapse; margin-top:10px;
    table-layout: fixed; /* fuerza respetar colgroup */
  }
  .table th {
    background:#f6fffb; border:1px solid #dff5ee; color:#127c64;
    font-size:11px; text-transform:uppercase; letter-spacing:.03em; padding:6px;
  }
  .table td {
    border:1px solid #e9ecef; padding:6px; font-size:11px; vertical-align: top;
    word-break: break-word; overflow-wrap: anywhere; white-space: normal;
  }
  .table tr:nth-child(even) td { background:#fcfdfd; }
  .table tr { page-break-inside: avoid; } /* evita cortar filas en saltos */

  .row { width:100%; }
  .col-6 { width:49%; display:inline-block; vertical-align:top; }
  .mt-8 { margin-top:8px; }
  .mt-12 { margin-top:12px; }
  .mt-18 { margin-top:18px; }

  .signature { height:90px; border:1px solid #e9ecef; border-radius:8px; display:block; text-align:center; }
  .signature img { max-height:88px; max-width:100%; }

  .footer { position:fixed; bottom:-8mm; left:0; right:0; text-align:center; color:#7a7f87; font-size:10px; }
</style>
</head>
<body>

<table class="header">
  <tr>
    <td>
      <h1>Préstamo #{{ $prestamo->id }}</h1>
      <div class="small">Generado: {{ now()->format('Y-m-d H:i') }}</div>
    </td>
    <td style="text-align:right">
      <div class="brand">MediBuy</div>
      <div class="small">Paquete de préstamo</div>
    </td>
  </tr>
</table>

<div class="hr"></div>

<div class="row">
  <div class="col-6">
    <h2>Datos del paquete</h2>
    <table class="meta">
      <tr><td class="k">Cliente</td><td>{{ $cliente }}</td></tr>
      <tr>
        <td class="k">Estado</td>
        <td>
          @php $cls = 'badge-'.($prestamo->estado ?? 'activo'); @endphp
          <span class="badge {{ $cls }}">{{ ucfirst($prestamo->estado) }}</span>
        </td>
      </tr>
      <tr><td class="k">Salida</td><td>{{ $fmt($prestamo->fecha_prestamo) }}</td></tr>
      <tr><td class="k">Regreso estimado</td><td>{{ $fmt($prestamo->fecha_devolucion_estimada) }}</td></tr>
      @if($prestamo->fecha_devolucion_real)
      <tr><td class="k">Regreso real</td><td>{{ $fmt($prestamo->fecha_devolucion_real) }}</td></tr>
      @endif
    </table>
  </div>

  <div class="col-6">
    <h2>Información adicional</h2>
    <table class="meta">
      <tr><td class="k">Usuario</td><td>{{ $prestamo->user_name }}</td></tr>
      <tr><td class="k">Creado</td><td>{{ optional($prestamo->created_at)->format('Y-m-d H:i') }}</td></tr>
      <tr><td class="k">Observaciones</td><td>{{ $prestamo->observaciones ?: '—' }}</td></tr>
    </table>
  </div>
</div>

<h2 class="mt-12">Resumen</h2>
<table class="kpis">
  <tr>
    <th>Total</th>
    <th>Salidos</th>
    <th>Devueltos</th>
    <th>Vendidos</th>
  </tr>
  <tr>
    <td>{{ $total }}</td>
    <td>{{ $salidos }}</td>
    <td>{{ $devueltos }}</td>
    <td>{{ $vendidos }}</td>
  </tr>
</table>

<table class="meta mt-8">
  <tr>
    <td class="k" style="width:25%;">Faltantes de devolución</td>
    <td>
      <div class="chips">
        @forelse($faltanDevol as $r)
          <span class="chip chip-warn">• {{ $r->numero_serie }}</span>
        @empty
          <span class="small">— Ninguno —</span>
        @endforelse
      </div>
    </td>
  </tr>
  <tr>
    <td class="k">Vendidos</td>
    <td>
      <div class="chips">
        @forelse($listVendidos as $r)
          <span class="chip chip-sold">• {{ $r->numero_serie }}</span>
        @empty
          <span class="small">— Ninguno —</span>
        @endforelse
      </div>
    </td>
  </tr>
</table>

<h2 class="mt-18">Equipos en el paquete ({{ $total }})</h2>
<table class="table">
  <!-- Anchos fijos por columna para evitar desbordes -->
  <colgroup>
    <col style="width:5%">   <!-- # -->
    <col style="width:13%">  <!-- Serie -->
    <col style="width:16%">  <!-- Subtipo -->
    <col style="width:12%">  <!-- Marca -->
    <col style="width:23%">  <!-- Modelo (puede ser largo) -->
    <col style="width:10%">  <!-- Salida -->
    <col style="width:10%">  <!-- Devolución -->
    <col style="width:11%">  <!-- Vendido -->
  </colgroup>
  <thead>
    <tr>
      <th>#</th>
      <th>Serie</th>
      <th>Subtipo</th>
      <th>Marca</th>
      <th>Modelo</th>
      <th>Salida</th>
      <th>Devolución</th>
      <th>Vendido</th>
    </tr>
  </thead>
  <tbody>
    @forelse($prestamo->registros as $i => $r)
      <tr>
        <td>{{ $i+1 }}</td>
        <td>{{ $r->numero_serie }}</td>
        <td>{{ $r->subtipo_equipo ?? '—' }}</td>
        <td>{{ $r->marca ?? '—' }}</td>
        <td>{{ $r->modelo ?? '—' }}</td>
        <td>{{ $dt($r->pivot->salida_scanned_at ?? null) }}</td>
        <td>{{ $dt($r->pivot->devolucion_scanned_at ?? null) }}</td>
        <td>{{ $dt($r->pivot->vendido_scanned_at ?? null) }}</td>
      </tr>
    @empty
      <tr><td colspan="8" class="small">No hay equipos vinculados.</td></tr>
    @endforelse
  </tbody>
</table>

<div class="mt-18 row">
  <div class="col-6">
    <h2>Firma de quien registra</h2>
    <div class="signature">
      @if(!empty($firmaBase64))
        <img src="{{ $firmaBase64 }}" alt="Firma">
      @else
        <span class="small" style="line-height:88px;">Sin firma</span>
      @endif
    </div>
  </div>
</div>

<div class="footer">
  MediBuy • Préstamo #{{ $prestamo->id }}
</div>

{{-- Numeración de páginas para Dompdf --}}
<script type="text/php">
if (isset($pdf)) {
    $text = "Página {PAGE_NUM} de {PAGE_COUNT}";
    $font = $fontMetrics->get_font("DejaVu Sans","normal");
    $size = 9;
    /* Coordenadas aproximadas para A4 con los márgenes definidos arriba */
    $pdf->page_text(510, 820, $text, $font, $size, [0.48,0.48,0.48]);
}
</script>

</body>
</html>
