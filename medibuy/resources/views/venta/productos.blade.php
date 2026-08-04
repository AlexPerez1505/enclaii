@extends('layouts.app')

@section('content')
<style>
    :root {
        --pastel-blue: #d0ebff;
        --accent-blue: #228be6;
        --pastel-gray: #f8f9fa;
        --text-dark: #343a40;
    }

    body { background: var(--pastel-gray); }

    .productos-card {
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
    <div class="productos-card">
        <h2>Productos de la venta #{{ $venta->id }}</h2>

        <div class="table-responsive">
            <table class="table table-borderless">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Precio unitario</th>
                        <th>Subtotal</th>
                        <th>Sobreprecio</th>
                        <th>Registro</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($productos as $p)
                        <tr>
                            <td data-label="Producto">{{ $p->nombre_producto }}</td>
                            <td data-label="Cantidad">{{ $p->cantidad }}</td>
                            <td data-label="Precio unitario">${{ number_format($p->precio_unitario, 2) }}</td>
                            <td data-label="Subtotal">${{ number_format($p->subtotal, 2) }}</td>
                            <td data-label="Sobreprecio">${{ number_format($p->sobreprecio, 2) }}</td>
                            <td data-label="Registro">{{ $p->registro_id ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">No hay productos asociados a esta venta.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
