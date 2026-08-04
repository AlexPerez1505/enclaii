@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2 class="mb-3">Comparación de Componentes - Pedido #{{ $pedido->id }}</h2>

    <table class="table table-bordered table-hover">
        <thead class="thead-dark">
            <tr>
                <th>Componente</th>
                <th>Cantidad Solicitada</th>
                <th>Cantidad Recibida</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($comparacion as $item)
                <tr>
                    <td>{{ $item['nombre'] }}</td>
                    <td>{{ $item['cantidad_solicitada'] }}</td>
                    <td>{{ $item['cantidad_recibida'] }}</td>
                    <td>
                        @switch($item['estado'])
                            @case('completo')
                                <span class="text-success">✅ Completo</span>
                                @break
                            @case('parcial')
                                <span class="text-warning">🟡 Parcial</span>
                                @break
                            @case('faltante')
                                <span class="text-danger">❌ Faltante</span>
                                @break
                        @endswitch
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <a href="{{ route('recepciones.index') }}" class="btn btn-secondary mt-3">← Volver</a>
</div>
@endsection
