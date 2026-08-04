@extends('layouts.app')
@section('title', 'Compras')
@section('titulo', 'Compras')
@section('content')
<div class="container mt-5">
    <div class="card shadow p-4 border-0 rounded-4" style="background-color: #ffffff;">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
            <h2 class="m-0" style="color: #1e293b; font-weight: 600; margin-top:110px;">📦 Pedidos Pendientes</h2>
           <a href="{{ route('pedidos.create') }}" 
   class="btn" 
   style="background-color: #dcfce7; color: #15803d; border: 1px solid #86efac; padding: 0.45rem 1rem; font-weight: 500; border-radius: 0.5rem;">
   + Nuevo Pedido
</a>
        </div>
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if($pedidos->isEmpty())
            <p class="text-muted">No hay pedidos pendientes.</p>
        @else
            <div class="table-responsive">
                <table class="table align-middle" style="border-collapse: separate; border-spacing: 0 0.75rem;">
                    <thead style="background-color: #f3f4f6; color: #374151;">
                        <tr>
                            <th>ID Pedido</th>
                            <th>Fecha Programada</th>
                            <th>Creado por</th>
                            <th>Observaciones</th>
                            <th>Equipos</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pedidos as $pedido)
                            <tr style="background-color: #f9fafb; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                                <td style="font-weight: 500;">#{{ $pedido->id }}</td>
                                <td>{{ \Carbon\Carbon::parse($pedido->fecha_programada)->format('Y-m-d') }}</td>
                                <td>{{ $pedido->creado_por }}</td>
                                <td class="text-muted">{{ $pedido->observaciones }}</td>
                                <td>
                                    <ul class="mb-0 ps-3">
                                        @foreach($pedido->equipos as $pequipo)
                                            <li>{{ $pequipo->cantidad }} × {{ $pequipo->nombre }}</li>
                                        @endforeach
                                    </ul>
                                </td>
                            <td class="text-center"> 
                                <a href="{{ route('recepciones.createDesdePedido', $pedido->id) }}" 
                                class="btn btn-sm" 
                                style="border: 1px solid #fdba74; color: #b45309; background-color: #ffedd5; padding: 0.4rem 0.8rem; border-radius: 0.5rem; font-weight: 500;">
                                        + Registrar Recepción
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
<style>
    ul {
        padding-left: 1rem;
    }

    th, td {
        vertical-align: middle;
    }

    table thead th {
        font-size: 0.95rem;
        font-weight: 600;
        text-transform: uppercase;
        border-bottom: none;
    }

    table tbody td {
        font-size: 0.9rem;
        color: #374151;
    }

    .table tbody tr {
        transition: box-shadow 0.2s ease;
    }

    .table tbody tr:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.06);
    }

    .container {
        margin-top: 110px !important;
    }

    body {
        background: #f4f7fb;
    }

    /* Responsive ajustes */
    @media (max-width: 768px) {
        .table thead {
            display: none;
        }

        .table, .table tbody, .table tr, .table td {
            display: block;
            width: 100%;
        }

        .table tr {
            margin-bottom: 1rem;
            border-radius: 0.5rem;
            box-shadow: 0 1px 6px rgba(0,0,0,0.05);
            padding: 0.75rem;
            background-color: #ffffff;
        }

        .table td {
            padding: 0.5rem 0;
            text-align: right;
            position: relative;
        }

        .table td::before {
            content: attr(data-label);
            position: absolute;
            left: 0;
            width: 50%;
            padding-left: 1rem;
            font-weight: bold;
            text-align: left;
            color: #6b7280;
        }
    }
</style>

<script>
// Ajuste para responsive labels
document.addEventListener('DOMContentLoaded', () => {
    const headers = Array.from(document.querySelectorAll('table thead th')).map(th => th.innerText.trim());
    document.querySelectorAll('table tbody tr').forEach(tr => {
        tr.querySelectorAll('td').forEach((td, index) => {
            td.setAttribute('data-label', headers[index] || '');
        });
    });
});
</script>
@endsection
