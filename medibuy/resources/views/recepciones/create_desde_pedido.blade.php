@extends('layouts.app')

@section('content')
<div class="container mt-5 mb-5">
    <div class="recepcion-wrapper">
        <div class="recepcion-header">
            <h2>Registrar Recepción</h2>
            <p class="pedido-id">Pedido #{{ $pedido->id }}</p>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('recepciones.storeDesdePedido') }}" method="POST" id="form-recepcion">
            @csrf
            <input type="hidden" name="pedido_id" value="{{ $pedido->id }}">

            <div class="form-grid">
                <div class="form-group">
                    <label for="fecha">Fecha de Recepción</label>
                    <input type="date" name="fecha" id="fecha" class="form-control" required value="{{ old('fecha', date('Y-m-d')) }}">
                </div>

                <div class="form-group">
                    <label for="recibido_por">Recibido por</label>
                    <input type="text" name="recibido_por" id="recibido_por" class="form-control" required value="{{ old('recibido_por', Auth::user()->name) }}">
                </div>
            </div>

            <div class="form-group">
                <label for="observaciones">Observaciones generales</label>
                <textarea name="observaciones" id="observaciones" class="form-control" rows="2">{{ old('observaciones') }}</textarea>
            </div>

            <div class="equipos-header d-flex justify-content-between align-items-center mb-2">
                <h4>Equipos y Componentes</h4>
                <div>
                    <button type="button" class="btn btn-outline-success btn-sm" onclick="marcarTodo(true)">Marcar todos</button>
                    <button type="button" class="btn btn-outline-warning btn-sm" onclick="marcarTodo(false)">Desmarcar todos</button>
                </div>
            </div>

            @php $equipoIndex = 0; @endphp
            @forelse ($pedido->equipos as $pequipo)
                @php
                    $componentes = $pedido->componentes->where('equipo_id', $pequipo->id)->filter(function($comp) {
                        return $comp->cantidad_ya_recibida < $comp->cantidad_esperada;
                    });
                @endphp

                @if ($componentes->isEmpty())
                    @continue
                @endif

                <div class="equipo-card border rounded p-3 mb-3">
                    <div class="equipo-header mb-2 d-flex justify-content-between">
                        <strong>{{ $pequipo->nombre ?? 'No especificado' }}</strong>
                        <span class="text-muted">Cantidad: {{ $pequipo->cantidad }}</span>
                    </div>
                    <div class="equipo-body">
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead class="table-light text-center">
                                    <tr>
                                        <th style="width: 50px;"></th>
                                        <th>Componente</th>
                                        <th style="width: 100px;">Esperado</th>
                                        <th style="width: 120px;">Recibido</th>
                                        <th>Observación</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $compIndex = 0; @endphp
                                    @foreach ($componentes as $comp)
                                        @php $oldChecked = old("componentes.$equipoIndex.$compIndex.recibido", true); @endphp
                                        <tr>
                                            <td class="text-center">
                                                <input type="checkbox" class="form-check-input recibido-checkbox"
                                                    name="componentes[{{ $equipoIndex }}][{{ $compIndex }}][recibido]"
                                                    value="1" {{ $oldChecked ? 'checked' : '' }}
                                                    onchange="toggleCantidad(this)">
                                            </td>
                                            <td>
                                                <strong>{{ $comp->nombre ?? 'Sin nombre' }}</strong>
                                                <input type="hidden" name="componentes[{{ $equipoIndex }}][{{ $compIndex }}][nombre]" value="{{ $comp->nombre }}">
                                                <input type="hidden" name="componentes[{{ $equipoIndex }}][{{ $compIndex }}][equipo]" value="{{ $pequipo->nombre }}">
                                                <input type="hidden" name="componentes[{{ $equipoIndex }}][{{ $compIndex }}][equipo_id]" value="{{ $pequipo->id }}">
                                            </td>
                                            <td class="text-center">{{ $comp->cantidad_esperada }}</td>
                                            <td>
                                                <input type="number"
                                                    name="componentes[{{ $equipoIndex }}][{{ $compIndex }}][cantidad_recibida]"
                                                    class="form-control cantidad-input"
                                                    min="0"
                                                    value="{{ old("componentes.$equipoIndex.$compIndex.cantidad_recibida", $comp->cantidad_esperada - $comp->cantidad_ya_recibida) }}">
                                            </td>
                                            <td>
                                                <input type="text"
                                                    name="componentes[{{ $equipoIndex }}][{{ $compIndex }}][observacion]"
                                                    class="form-control"
                                                    placeholder="Ej. pieza dañada..."
                                                    value="{{ old("componentes.$equipoIndex.$compIndex.observacion") }}">
                                            </td>
                                        </tr>
                                        @php $compIndex++; @endphp
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @php $equipoIndex++; @endphp
            @empty
                <p class="text-muted">No hay equipos registrados en este pedido.</p>
            @endforelse

            <div class="text-end mt-4">
                <button type="submit" class="btn btn-success">Registrar Recepción</button>
                <a href="{{ route('recepciones.index') }}" class="btn btn-outline-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>


<script>
    function marcarTodo(valor) {
        document.querySelectorAll('.recibido-checkbox').forEach(cb => {
            cb.checked = valor;
        });
    }

    function toggleCantidad(checkbox) {
        const row = checkbox.closest('tr');
        const cantidadInput = row.querySelector('.cantidad-input');
        cantidadInput.disabled = !checkbox.checked;
        if (!checkbox.checked) cantidadInput.value = 0;
    }

    // Al cargar, deshabilita los que no están marcados
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.recibido-checkbox').forEach(cb => {
            toggleCantidad(cb);
        });
    });
</script>


<style>
    body {
        background-color: #f4f7fb;
    }
    .recepcion-wrapper {
        background: #fff;
        border-radius: 1rem;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        padding: 2rem;
    }
    .recepcion-header h2 {
        font-size: 1.5rem;
        color: #1e293b;
        margin-bottom: 0.25rem;
    }
    .pedido-id {
        font-size: 0.9rem;
        color: #6b7280;
    }
    .form-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 1.5rem;
    }
    .form-group {
        flex: 1 1 300px;
    }
    label {
        font-weight: 500;
        color: #374151;
        margin-bottom: 0.25rem;
        display: block;
    }
    .form-control {
        border-radius: 0.5rem;
        font-size: 0.9rem;
    }
    .equipos-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin: 2rem 0 1rem;
    }
    .equipos-header h4 {
        font-size: 1.1rem;
        color: #1f2937;
        margin: 0;
    }
    .equipo-card {
        background-color: #f8fafc;
        border-radius: 0.75rem;
        padding: 1rem;
        margin-bottom: 1.5rem;
    }
    .equipo-header {
        display: flex;
        justify-content: space-between;
        font-weight: 600;
        color: #111827;
        margin-bottom: 0.75rem;
    }
    .cantidad {
        color: #6b7280;
        font-weight: normal;
    }
    .table {
        font-size: 0.85rem;
    }
    .cantidad-input {
        max-width: 100px;
    }
    .btn-success {
        background-color: #22c55e;
        border: none;
        padding: 0.5rem 1.25rem;
        font-weight: 500;
        border-radius: 0.5rem;
    }
    .btn-outline-secondary {
        border: 1px solid #cbd5e1;
        color: #334155;
        font-weight: 500;
        padding: 0.5rem 1.25rem;
        border-radius: 0.5rem;
    }
</style>


@endsection