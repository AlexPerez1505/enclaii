@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="card shadow p-4 border-0 rounded-4" style="background-color: #ffffff;">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 style="color: #1e293b; font-weight: 600;">📋 Recepciones Registradas</h3>
        </div>

        @if ($recepciones->isEmpty())
            <p class="text-muted">No hay recepciones registradas.</p>
        @else
            <div class="table-responsive">
                <table class="table align-middle" style="border-collapse: separate; border-spacing: 0 0.75rem;">
                    <thead style="background-color: #f3f4f6; color: #374151;">
                        <tr>
                            <th>ID</th>
                            <th>Fecha</th>
                            <th>Recibido por</th>
                            <th>Componentes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($recepciones as $recepcion)
                            <tr style="background-color: #f9fafb; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                                <td style="font-weight: 500;">#{{ $recepcion->id }}</td>
                                <td>{{ \Carbon\Carbon::parse($recepcion->fecha)->format('d/m/Y') }}</td>
                                <td>{{ $recepcion->recibido_por }}</td>
                                <td class="text-muted">{{ $recepcion->componentes->count() }} componentes</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4 d-flex justify-content-center">
                {{ $recepciones->links() }}
            </div>
        @endif
    </div>
</div>

<style>
    .container {
        margin-top: 110px !important;
    }

    body {
        background: #f4f7fb;
    }

    table thead th {
        font-size: 0.9rem;
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
</style>
@endsection
