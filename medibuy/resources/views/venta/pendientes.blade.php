@extends('layouts.app')

@section('content')
<style>
    :root {
        --pastel-blue: #d0ebff;
        --accent-blue: #228be6;
        --pastel-green: #d3f9d8;
        --accent-green: #38b000;
        --pastel-gray: #f8f9fa;
        --text-dark: #343a40;
    }

    body { background: var(--pastel-gray); }

    .ventas-card {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 4px 14px rgba(0,0,0,0.06);
        padding: 24px 28px;
        margin-bottom: 32px;
        animation: fadeIn 0.5s ease-in-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to   { opacity: 1; transform: none; }
    }

    h2 {
        font-weight: 700;
        font-size: 2rem;
        color: var(--accent-blue);
        margin-bottom: 24px;
    }

    table {
        background: white;
        border-radius: 12px;
        overflow: hidden;
    }

    th {
        background: var(--pastel-blue);
        color: var(--accent-blue);
        font-weight: 600;
        border: none;
    }

    td {
        vertical-align: middle !important;
        color: var(--text-dark);
    }

    .btn-ver {
        background: var(--pastel-green);
        color: var(--accent-green);
        font-weight: 600;
        border: none;
        padding: 8px 18px;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(56,176,0,0.1);
        transition: transform .2s, box-shadow .2s, filter .2s;
    }

    .btn-ver:hover {
        transform: scale(1.04);
        box-shadow: 0 4px 12px rgba(56,176,0,0.2);
        filter: brightness(1.05);
    }

    @media(max-width: 768px) {
        table thead { display: none; }
        table, table tbody, table tr, table td { display: block; width: 100%; }
        table tr { margin-bottom: 1rem; border-bottom: 1px solid #dee2e6; }
        table td { padding: .75rem 1rem; }
        table td::before {
            content: attr(data-label);
            font-weight: 600;
            display: block;
            margin-bottom: 0.5rem;
            color: var(--accent-blue);
        }
    }
</style>

<div class="container mt-4">
    <div class="ventas-card" style="margin-top:110px;">
        <h2>Ventas pendientes por checklist</h2>

        <div class="table-responsive">
            <table class="table table-borderless">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Cliente</th>
                        <th>Fecha</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ventas as $venta)
                        <tr>
                            <td data-label="ID">{{ $venta->id }}</td>
                            <td data-label="Cliente">{{ $venta->cliente ?? 'N/A' }}</td>
                            <td data-label="Fecha">{{ \Carbon\Carbon::parse($venta->fecha)->format('d/m/Y') }}</td>
                            <td data-label="Acción">
                               <a href="{{ route('checklists.wizard', $venta->id) }}" class="btn btn-ver">Checklist</a>


                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted">No hay ventas pendientes.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
