@extends('layouts.app')

@section('title', 'Editar Cliente')
@section('titulo', 'Editar')

@section('content')
<link rel="stylesheet" href="{{ asset('css/asistencias.css') }}?v={{ time() }}">
<style>
    .form-contenedor {
        background: #ffffff;
        padding: 2rem;
        border-radius: 15px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        width: 100%;
        max-width: 700px;
        text-align: center;
        animation: fade-in 0.5s ease-in-out;
        margin: auto;
        margin-top: 100px;
        margin-bottom: 130px;
    }

    body {
        background-color: #f0f4f8;
        font-family: 'Inter', sans-serif;
    }
</style>
<body>

<div class="form-contenedor container" style="margin-top:120px;">
    <form action="{{ route('clientes.update', $cliente->id) }}" method="POST" class="needs-validation" novalidate>
        @csrf
        @method('PUT')

        <h5 class="titulos_encabezado mb-4"><strong>Editar Cliente</strong></h5>

        <div class="row">
            <!-- Nombre -->
            <div class="col-md-6 mb-3">
                <div class="form-group">
                    <label for="nombre" class="label_nomina">Nombre</label>
                    <div class="input_consulta">
                        <div class="icon-container2">
                            <img src="{{ asset('images/placa.png') }}" alt="Acceso" class="icon2">
                        </div>
                        <input id="nombre" type="text" class="form-control" style="background-color: #fff;" name="nombre" value="{{ old('nombre', $cliente->nombre) }}" required>
                    </div>
                </div>
            </div>

            <!-- Apellido -->
            <div class="col-md-6 mb-3">
                <div class="form-group">
                    <label for="apellido" class="label_nomina">Apellido</label>
                    <div class="input_consulta">
                        <div class="icon-container2">
                            <img src="{{ asset('images/placa.png') }}" alt="Acceso" class="icon2">
                        </div>
                        <input id="apellido" type="text" name="apellido" class="form-control" style="background-color: #fff;" value="{{ old('apellido', $cliente->apellido) }}" required>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Teléfono -->
            <div class="col-md-4 mb-3">
                <div class="form-group">
                    <label for="telefono" class="label_nomina">Teléfono</label>
                    <div class="input_consulta">
                        <div class="icon-container2">
                            <img src="{{ asset('images/placa.png') }}" alt="Acceso" class="icon2">
                        </div>
                        <input id="telefono" type="text" name="telefono" class="form-control" style="background-color: #fff;" value="{{ old('telefono', $cliente->telefono) }}">
                    </div>
                </div>
            </div>

            <!-- Email -->
            <div class="col-md-4 mb-3">
                <div class="form-group">
                    <label for="email" class="label_nomina">Correo</label>
                    <div class="input_consulta">
                        <div class="icon-container2">
                            <img src="{{ asset('images/placa.png') }}" alt="Acceso" class="icon2">
                        </div>
                        <input id="email" type="email" name="email" class="form-control" style="background-color: #fff;" value="{{ old('email', $cliente->email) }}">
                    </div>
                </div>
            </div>

            <!-- Categoría -->
            <div class="col-md-4 mb-3">
                <div class="form-group">
                    <label for="categoria_id" class="label_nomina">Categoría</label>
                    <div class="input_consulta">
                        <div class="icon-container2">
                            <img src="{{ asset('images/placa.png') }}" alt="Acceso" class="icon2">
                        </div>
                        <select name="categoria_id" class="form-control" style="background-color: #fff;" required>
                            <option value="">Seleccione una categoría</option>
                            @foreach($categorias as $categoria)
                                <option value="{{ $categoria->id }}" {{ $cliente->categoria_id == $categoria->id ? 'selected' : '' }}>
                                    {{ $categoria->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dirección -->
        <div class="row">
            <div class="col-12 mb-3">
                <div class="form-group">
                    <label for="comentarios" class="label_nomina">Dirección</label>
                    <div class="input_consulta">
                        <div class="icon-container2">
                            <img src="{{ asset('images/placa.png') }}" alt="Acceso" class="icon2">
                        </div>
                        <textarea id="comentarios" name="comentarios" class="form-control" style="background-color: #fff;" rows="3">{{ old('comentarios', $cliente->comentarios) }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between">
            <a href="{{ route('clientes.index') }}" class="btn btn-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
        </div>
    </form>
</div>

<script>
    (function () {
        'use strict'
        var forms = document.querySelectorAll('.needs-validation')

        Array.prototype.slice.call(forms)
            .forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }
                    form.classList.add('was-validated')
                }, false)
            })
    })()
</script>

@endsection
