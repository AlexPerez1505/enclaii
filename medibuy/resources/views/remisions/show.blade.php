@extends('layouts.app')

@section('title', 'Remisión de Mantenimiento')
@section('titulo', 'Remisión de Mantenimiento')

@section('content')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<style>
  :root{
    --bg:#f6f7fb;
    --card:#ffffff;
    --ink:#0b1220;
    --muted:#64748b;
    --line:#e6eaf2;
    --brand:#2563eb;
    --brand-soft: rgba(37,99,235,.10);
    --ok:#16a34a;
    --shadow: 0 18px 60px rgba(2,6,23,.08);
    --shadow2: 0 10px 28px rgba(2,6,23,.06);
    --r: 18px;
  }

  body{
    background: var(--bg);
    font-family: Inter, system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
    color: var(--ink);
  }

  .wrap{ max-width: 1200px; margin: 18px auto; padding: 0 14px; }

  .topbar{
    background: linear-gradient(180deg, rgba(37,99,235,.12), rgba(255,255,255,0));
    border: 1px solid rgba(226,232,240,.92);
    border-radius: var(--r);
    box-shadow: var(--shadow);
    overflow: hidden;
  }
  .topbar-inner{
    padding: 16px 16px 14px;
    border-bottom: 1px solid var(--line);
    display:flex; align-items:flex-start; justify-content:space-between; gap:12px; flex-wrap:wrap;
  }
  .title{
    display:flex; align-items:center; gap:10px;
    font-weight: 900; letter-spacing:.2px;
    font-size: 16px;
  }
  .title i{ color: var(--brand); }
  .sub{
    margin-top:6px; color: var(--muted);
    font-size: 12px; font-weight: 650;
  }

  .badge-pill{
    display:inline-flex; align-items:center; gap:8px;
    padding: 8px 10px;
    border-radius: 999px;
    border: 1px solid rgba(37,99,235,.20);
    background: rgba(37,99,235,.08);
    color: var(--brand);
    font-weight: 900;
    font-size: 12px;
    white-space: nowrap;
  }

  .grid{
    display:grid;
    grid-template-columns: 1.6fr .9fr;
    gap: 14px;
    padding: 14px;
  }
  @media (max-width: 992px){
    .grid{ grid-template-columns: 1fr; }
  }

  .cardx{
    background: var(--card);
    border: 1px solid rgba(226,232,240,.92);
    border-radius: var(--r);
    box-shadow: var(--shadow2);
    overflow: hidden;
  }
  .cardx-h{
    padding: 14px 14px 10px;
    border-bottom: 1px solid var(--line);
    display:flex; align-items:center; justify-content:space-between; gap:10px; flex-wrap:wrap;
  }
  .cardx-h .h{
    font-weight: 1000;
    font-size: 13px;
    display:flex; align-items:center; gap:10px;
  }
  .cardx-b{ padding: 14px; }

  .kv{
    display:grid;
    grid-template-columns: 160px 1fr;
    gap: 10px 12px;
    align-items:start;
  }
  @media (max-width: 560px){
    .kv{ grid-template-columns: 1fr; }
  }
  .k{
    color: var(--muted);
    font-weight: 900;
    font-size: 12px;
  }
  .v{
    color: var(--ink);
    font-weight: 750;
    font-size: 13px;
  }
  .mono{ font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", monospace; }

  .table-wrap{
    border: 1px solid rgba(226,232,240,.92);
    border-radius: 16px;
    overflow: hidden;
  }
  table{ margin:0; }
  thead th{
    background: rgba(2,6,23,.03);
    color: var(--muted);
    font-weight: 950;
    font-size: 12px;
    border-bottom: 1px solid var(--line) !important;
    white-space: nowrap;
  }
  tbody td{
    border-top: 1px solid rgba(226,232,240,.75) !important;
    font-weight: 650;
    font-size: 13px;
    color: var(--ink);
    vertical-align: top;
  }
  .muted{ color: var(--muted); font-weight: 650; }

  .pill{
    display:inline-flex; align-items:center; gap:6px;
    padding: 4px 10px;
    border-radius: 999px;
    border: 1px solid rgba(2,6,23,.10);
    background: rgba(2,6,23,.03);
    font-weight: 900;
    font-size: 12px;
  }
  .pill.ok{
    border-color: rgba(22,163,74,.18);
    background: rgba(22,163,74,.10);
    color: var(--ok);
  }
  .pill.brand{
    border-color: rgba(37,99,235,.18);
    background: rgba(37,99,235,.10);
    color: var(--brand);
  }

  .summary{
    position: sticky;
    top: 14px;
  }
  @media (max-width: 992px){
    .summary{ position: static; }
  }

  .sumrow{
    display:flex;
    align-items:center;
    justify-content:space-between;
    padding: 10px 0;
    border-bottom: 1px dashed rgba(226,232,240,.95);
  }
  .sumrow:last-child{ border-bottom:0; }
  .sumk{
    color: var(--muted);
    font-weight: 950;
    font-size: 12px;
  }
  .sumv{
    font-weight: 1000;
    font-variant-numeric: tabular-nums;
  }

  .totalbox{
    margin-top: 12px;
    padding: 12px;
    border-radius: 16px;
    border: 1px solid rgba(37,99,235,.18);
    background: rgba(37,99,235,.08);
  }
  .totalbox .big{
    font-size: 20px;
    font-weight: 1100;
    letter-spacing: .2px;
  }

  .actions{
    display:flex;
    gap:10px;
    flex-wrap:wrap;
    justify-content:flex-end;
  }
  .btnx{
    border-radius: 14px;
    font-weight: 950;
    padding: 10px 14px;
  }

  /* Mobile: tabla -> cards */
  .mobile-items{ display:none; }
  @media (max-width: 720px){
    .desktop-items{ display:none; }
    .mobile-items{ display:block; }
    .item-card{
      border: 1px solid rgba(226,232,240,.92);
      border-radius: 16px;
      background:#fff;
      padding: 12px;
      box-shadow: var(--shadow2);
      margin-bottom: 10px;
    }
    .item-card .t{
      font-weight: 1000;
      display:flex; justify-content:space-between; gap:10px;
    }
    .item-card .meta{
      margin-top: 8px;
      display:grid;
      grid-template-columns: 1fr 1fr;
      gap: 8px;
      font-size: 12px;
    }
    .item-card .meta .k{ font-size: 11px; }
  }
</style>

@php
  $folio = $remision->folio ?? $remision->id;
  $fecha = $remision->created_at?->format('d/m/Y H:i') ?? '—';

  $clienteNombre = trim(($remision->cliente->nombre ?? '').' '.($remision->cliente->apellido ?? '')) ?: ($remision->cliente->nombre ?? '—');
  $tel = $remision->cliente->telefono ?? 'No disponible';

  $asesor = mb_strtoupper($remision->usuario->name ?? ($remision->user->name ?? 'No disponible'), 'UTF-8');

  $tieneEnvio = (bool)($remision->tiene_envio ?? false);
  $envioCosto = (float)($remision->envio_costo ?? 0);
  $envioDir   = $remision->envio_direccion ?? null;

  $meses = $remision->meses_a_pagar ?? null;
  $mensualidad = $remision->mensualidad ?? null;

  $items = collect($remision->items ?? []);
@endphp

<div class="wrap">

  <div class="topbar">
    <div class="topbar-inner">
      <div>
        <div class="title">
          <i class="bi bi-wrench-adjustable-circle"></i>
          Remisión de Mantenimiento
          <span class="pill brand">Folio: {{ $folio }}</span>
        </div>
        <div class="sub">
          Fecha: <span class="mono">{{ $fecha }}</span>
          · Cliente: <b>{{ $clienteNombre }}</b>
        </div>
      </div>

      <div class="d-flex gap-2 flex-wrap align-items-center">
        <span class="badge-pill">
          <i class="bi bi-shield-check"></i>
          Registro interno
        </span>
      </div>
    </div>

    <div class="grid">

      {{-- IZQUIERDA --}}
      <div class="cardx">
        <div class="cardx-h">
          <div class="h"><i class="bi bi-person-badge"></i> Datos</div>
          <span class="pill {{ $tieneEnvio ? 'ok' : '' }}">
            <i class="bi {{ $tieneEnvio ? 'bi-truck' : 'bi-building' }}"></i>
            {{ $tieneEnvio ? 'Con envío' : 'Sin envío' }}
          </span>
        </div>

        <div class="cardx-b">
          <div class="kv">
            <div class="k">Cliente</div>
            <div class="v">{{ $clienteNombre }}</div>

            <div class="k">Celular</div>
            <div class="v">{{ $tel }}</div>

            <div class="k">Asesor</div>
            <div class="v">{{ $asesor }}</div>

            <div class="k">IVA</div>
            <div class="v">
              @if($remision->aplicar_iva)
                <span class="pill ok"><i class="bi bi-check2-circle"></i> Aplica IVA</span>
              @else
                <span class="pill"><i class="bi bi-dash-circle"></i> Sin IVA</span>
              @endif
            </div>

            <div class="k">Envío</div>
            <div class="v">
              @if($tieneEnvio)
                <div class="mb-1">
                  <span class="pill ok">
                    <i class="bi bi-truck"></i> ${{ number_format($envioCosto, 2) }}
                  </span>
                </div>
                <div class="muted">{{ $envioDir ?: 'Dirección no capturada' }}</div>
              @else
                <span class="muted">No aplica</span>
              @endif
            </div>

            <div class="k">Meses / Mensualidad</div>
            <div class="v">
              @if(!empty($meses))
                <span class="pill brand"><i class="bi bi-calendar2-week"></i> {{ $meses }} meses</span>
                <span class="pill"><i class="bi bi-cash-coin"></i> ${{ number_format((float)$mensualidad, 2) }}</span>
              @else
                <span class="muted">No aplica</span>
              @endif
            </div>
          </div>
        </div>

        <div class="cardx-h" style="border-top: 1px solid var(--line);">
          <div class="h"><i class="bi bi-list-check"></i> Ítems</div>
          <span class="pill">{{ $items->count() }} registros</span>
        </div>

        <div class="cardx-b">

          {{-- Desktop table --}}
          <div class="desktop-items">
            <div class="table-wrap">
              <table class="table table-sm align-middle">
                <thead>
                  <tr>
                    <th style="width:54px;">#</th>
                    <th>Concepto</th>
                    <th style="width:92px;">Cant</th>
                    <th style="width:110px;">Unidad</th>
                    <th style="width:130px;" class="text-end">P. Unit</th>
                    <th style="width:130px;" class="text-end">Subtotal</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($items as $i => $item)
                    <tr>
                      <td class="muted">{{ $i+1 }}</td>
                      <td>
                        <div style="font-weight:950;">{{ $item->nombre_item }}</div>
                        @if(!empty($item->descripcion_item))
                          <div class="muted" style="font-size:12px;">{{ $item->descripcion_item }}</div>
                        @endif
                      </td>
                      <td>{{ $item->cantidad }}</td>
                      <td class="mono">{{ $item->unidad }}</td>
                      <td class="text-end mono">${{ number_format((float)$item->importe_unitario, 2) }}</td>
                      <td class="text-end mono">${{ number_format((float)$item->subtotal, 2) }}</td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="6" class="text-center muted py-4">
                        No hay ítems registrados.
                      </td>
                    </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>

          {{-- Mobile cards --}}
          <div class="mobile-items">
            @forelse($items as $i => $item)
              <div class="item-card">
                <div class="t">
                  <div>{{ $i+1 }}. {{ $item->nombre_item }}</div>
                  <div class="mono">${{ number_format((float)$item->subtotal, 2) }}</div>
                </div>
                @if(!empty($item->descripcion_item))
                  <div class="muted" style="margin-top:6px; font-size:12px;">{{ $item->descripcion_item }}</div>
                @endif

                <div class="meta">
                  <div>
                    <div class="k">Cantidad</div>
                    <div class="v">{{ $item->cantidad }}</div>
                  </div>
                  <div>
                    <div class="k">Unidad</div>
                    <div class="v mono">{{ $item->unidad }}</div>
                  </div>
                  <div>
                    <div class="k">P. Unit</div>
                    <div class="v mono">${{ number_format((float)$item->importe_unitario, 2) }}</div>
                  </div>
                  <div>
                    <div class="k">Subtotal</div>
                    <div class="v mono">${{ number_format((float)$item->subtotal, 2) }}</div>
                  </div>
                </div>
              </div>
            @empty
              <div class="muted">No hay ítems registrados.</div>
            @endforelse
          </div>

        </div>
      </div>

      {{-- DERECHA: RESUMEN --}}
      <div class="cardx summary">
        <div class="cardx-h">
          <div class="h"><i class="bi bi-receipt"></i> Resumen</div>
          <span class="pill brand"><i class="bi bi-calculator"></i> Totales</span>
        </div>

        <div class="cardx-b">
          <div class="sumrow">
            <div class="sumk">Subtotal</div>
            <div class="sumv mono">${{ number_format((float)$remision->subtotal, 2) }}</div>
          </div>
          <div class="sumrow">
            <div class="sumk">IVA</div>
            <div class="sumv mono">${{ number_format((float)$remision->iva, 2) }}</div>
          </div>
          <div class="sumrow">
            <div class="sumk">Envío</div>
            <div class="sumv mono">${{ number_format($tieneEnvio ? $envioCosto : 0, 2) }}</div>
          </div>

          <div class="totalbox">
            <div class="d-flex justify-content-between align-items-end gap-2">
              <div>
                <div class="sumk">Total</div>
                <div class="big mono">${{ number_format((float)$remision->total, 2) }}</div>
              </div>
              <div class="text-end">
                <div class="sumk">Mensualidad</div>
                <div class="big mono" style="font-size:18px;">
                  ${{ number_format((float)($mensualidad ?? 0), 2) }}
                </div>
              </div>
            </div>
            <div class="muted" style="margin-top:8px; font-size:12px;">
              {{ mb_strtoupper($remision->importe_letra ?? '—', 'UTF-8') }}
            </div>
          </div>

          <div class="mt-3">
            <div class="actions">
              <a href="{{ url()->previous() }}" class="btn btn-light btnx" style="border:1px solid var(--line);">
                <i class="bi bi-arrow-left-circle me-1"></i> Volver
              </a>

              <a href="{{ route('remisions.descargarPdf', $remision->id) }}" class="btn btn-primary btnx">
                <i class="bi bi-filetype-pdf me-1"></i> PDF
              </a>

              {{-- ✅ Actívalo si ya agregaste la ruta remisions.ticketMantenimientoPdf --}}
              @if(\Illuminate\Support\Facades\Route::has('remisions.ticketMantenimientoPdf'))
                <a href="{{ route('remisions.ticketMantenimientoPdf', $remision->id) }}" class="btn btn-dark btnx">
                  <i class="bi bi-printer me-1"></i> Ticket
                </a>
              @endif
            </div>

            <div class="muted" style="margin-top:10px; font-size:12px;">
              Consejo: imprime el ticket de mantenimiento para checklist y firmas.
            </div>
          </div>

        </div>
      </div>

    </div>
  </div>

</div>
@endsection
