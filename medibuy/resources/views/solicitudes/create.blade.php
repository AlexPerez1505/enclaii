@extends('layouts.app')
@section('title', 'Materiales')
@section('titulo', 'Solicitar Material')
@section('content')

<link rel="stylesheet" href="{{ asset('css/asistencias.css') }}?v={{ time() }}">
<div class="formulario-contenedor">

    <form action="{{ route('solicitudes.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <div class="form-group">
                <label for="categoria" class="label_nomina">Categoría</label>
                <div class="form-group">
                    <div class="input_consulta">
                        <div class="icon-container2">
                            <img src="{{ asset('images/serie.png') }}" alt="Acceso" class="icon2">
                        </div>
                        <select name="categoria" class="form-control" style="background-color: #ffff;" required>
                            @foreach($categorias as $categoria)
                                <option value="{{ $categoria }}">{{ $categoria }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Material -->
            <div class="col-md-6 col-12 mb-3">
                <div class="form-group">
                    <label for="material" class="label_nomina">Material/Equipo/Etc</label>
                    <div class="form-group">
                        <div class="input_consulta">
                            <div class="icon-container2">
                                <img src="{{ asset('images/serie.png') }}" alt="Acceso" class="icon2">
                            </div>
                            <input type="text" name="material" class="form-control" style="background-color: #ffff;" required>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cantidad -->
            <div class="col-md-6 col-12 mb-3">
                <div class="form-group">
                    <label for="cantidad" class="label_nomina">Cantidad</label>
                    <div class="form-group">
                        <div class="input_consulta">
                            <div class="icon-container2">
                                <img src="{{ asset('images/serie.png') }}" alt="Acceso" class="icon2">
                            </div>
                            <input type="number" name="cantidad" class="form-control" style="background-color: #ffff;" required min="1">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Justificación -->
        <div class="form-group mb-3">
            <label for="justificación" class="label_nomina">Justificación</label>
            <textarea class="form-control select" name="justificacion" placeholder="Describe la necesidad del equipo o insumo solicitado, su uso y urgencia." style="height: 130px;"></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Enviar Solicitud</button>
    </form>
</div>
@endsection
