@extends('layouts.app')

@section('title', 'Automovil')
@section('titulo', 'Editar')

@section('content')
<link rel="stylesheet" href="{{ asset('css/asistencias.css') }}?v={{ time() }}">
<style>
    body{
        background: #F5FAFF;
    }
    #file-input-text-1,
#file-input-text-2,
#file-input-text-3,
#file-input-text-4 {
        width: 300px; /* Hace que el texto ocupe todo el ancho disponible */
        display: block;
        white-space: nowrap; /* Evita que el texto se rompa en múltiples líneas */
    }
    @media (max-width: 767px) {
        #file-input-text-1,
#file-input-text-2,
#file-input-text-3,
#file-input-text-4 {
        width: 150px; /* Hace que el texto ocupe todo el ancho disponible */
        display: block;
        white-space: normal; /* Permite que el texto se divida en varias líneas si es necesario */
        font-size: 16px; /* Aumenta o ajusta el tamaño de la fuente si lo deseas */
        padding: 10px; /* Agrega espacio alrededor del texto */
    }
    .input_consulta {
        width: 100% !important;
    }
    .form-control {
        width: 100% !important;
    }
}
</style>
<body>

<div class="form-contenedor">

<form action="{{ route('camionetas.update', $camioneta->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

<!-- Fotos del Vehículo (Máximo 3) -->
<div class="mb-3">
    <div class="form-group">
        <label for="fotos" class="label_nomina">
            Fotos del Vehículo (Exactamente 4)
            <div class="info-icon">?
                <div class="info-tooltip text-center">Máximo 4 (una por campo).</div>
            </div>
            </div>
        </label>
        <div class="d-flex align-items-center input-containerr">
            <label for="evidencia1" class="file-input-label d-flex align-items-center">
                <div class="icon-containerr">
                    <img src="{{ asset('images/adjunto-archivo.png') }}" alt="Icono de pin" class="icon-pin">
                </div>
                <span id="file-label-text-1">Seleccione archivo 1</span>
            </label>
            <input type="file" name="fotos[]" class="file-input" accept="image/*" id="fotos1" onchange="updateFileInputTextAndValidate('fotos1', 'file-label-text-1', 'file-input-text-images1')">
            <div id="file-input-text-images1" class="file-input-text">Sin selección</div>
            <p id="mensajeError" style="color: red; font-size: 14px;"></p>
        </div>
    </div>


    <div class="division"></div>
<h5 class="titulos_encabezado">Datos Generales</h5>


<div class="row">
    <!-- Número de Placa y Número de Serie (VIN) en una fila -->
    <div class="col-12 col-md-6 mb-3">
        <div class="form-group">
            <label for="placa" class="label_nomina">Número de Placa</label>
            <div class="input_consulta">
                <div class="icon-container2">
                    <img src="{{ asset('images/placa.png') }}" alt="Acceso" class="icon2">
                </div>
                <input type="text" name="placa" class="form-control" style="background-color: #ffff; display:block; width: 100%;" value="{{ old('placa', $camioneta->placa) }}">
            </div>
        </div>
    </div>
    
    <div class="col-12 col-md-6 mb-3">
        <div class="form-group">
            <label for="vin" class="label_nomina">Número de Serie (VIN)</label>
            <div class="input_consulta">
                <div class="icon-container2">
                    <img src="{{ asset('images/serie.png') }}" alt="Acceso" class="icon2">
                </div>
                <input type="text" name="vin" class="form-control" style="background-color: #ffff; display:block; width: 100%;" value="{{ old('vin', $camioneta->vin) }}">
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Marca, Modelo y Año en una fila -->
    <div class="col-12 col-md-4 mb-3">
        <div class="form-group">
            <label for="marca" class="label_nomina">Marca</label>
            <div class="input_consulta">
                <div class="icon-container2">
                    <img src="{{ asset('images/camioneta.png') }}" alt="Acceso" class="icon2">
                </div>
                <input type="text" name="marca" class="form-control" style="background-color: #ffff; display:block; width: 100%;" value="{{ old('marca', $camioneta->marca) }}">
            </div>
        </div>
    </div>

    <div class="col-12 col-md-4 mb-3">
        <div class="form-group">
            <label for="modelo" class="label_nomina">Modelo</label>
            <div class="input_consulta">
                <div class="icon-container2">
                    <img src="{{ asset('images/carro.png') }}" alt="Acceso" class="icon2">
                </div>
                <input type="text" name="modelo" class="form-control" style="background-color: #ffff; display:block; width: 100%;" value="{{ old('modelo', $camioneta->modelo) }}">
            </div>
        </div>
    </div>

    <div class="col-12 col-md-4 mb-3">
        <div class="form-group">
            <label for="anio" class="label_nomina">Año</label>
            <div class="input_consulta">
                <div class="icon-container2">
                    <img src="{{ asset('images/anio.png') }}" alt="Acceso" class="icon2">
                </div>
                <input type="number" name="anio" class="form-control" style="background-color: #ffff; display:block; width: 100%;" value="{{ old('anio', $camioneta->anio) }}">
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Color, Tipo de Motor, Tipo de Combustible, y Capacidad de Carga en una fila -->
    <div class="col-12 col-md-3 mb-3">
        <div class="form-group">
            <label for="color" class="label_nomina">Color</label>
            <div class="input_consulta">
                <div class="icon-container2">
                    <img src="{{ asset('images/paleta.png') }}" alt="Acceso" class="icon2">
                </div>
                <input type="text" name="color" class="form-control" style="background-color: #ffff; display:block; width: 100%;" value="{{ old('color', $camioneta->color) }}">
            </div>
        </div>
    </div>

    <div class="col-12 col-md-3 mb-3">
        <div class="form-group">
            <label for="tipo_motor" class="label_nomina">Tipo de Motor</label>
            <div class="input_consulta">
                <div class="icon-container2">
                    <img src="{{ asset('images/motor.png') }}" alt="Acceso" class="icon2">
                </div>
                <input type="text" name="tipo_motor" class="form-control" style="background-color: #ffff; display:block; width: 100%;" value="{{ old('tipo_motor', $camioneta->tipo_motor) }}">
            </div>
        </div>
    </div>

    <div class="col-12 col-md-3 mb-3">
        <div class="form-group">
            <label for="tipo_combustible" class="label_nomina">Tipo de Combustible</label>
            <div class="input_consulta">
                <div class="icon-container2">
                    <img src="{{ asset('images/gasolina.png') }}" alt="Acceso" class="icon2">
                </div>
                <input type="text" name="tipo_combustible" class="form-control" style="background-color: #ffff; display:block; width: 100%;" value="{{ old('tipo_combustible', $camioneta->tipo_combustible) }}">
            </div>
        </div>
    </div>

    <div class="col-12 col-md-3 mb-3">
        <div class="form-group">
            <label for="capacidad_carga" class="label_nomina">Capacidad de Carga</label>
            <div class="input_consulta">
                <div class="icon-container2">
                    <img src="{{ asset('images/carga.png') }}" alt="Acceso" class="icon2">
                </div>
                <input type="text" name="capacidad_carga" class="form-control" style="background-color: #ffff; display:block; width: 100%;" value="{{ old('capacidad_carga', $camioneta->capacidad_carga) }}">
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Fecha de Adquisición, Último Mantenimiento, y Próximo Mantenimiento en una fila -->
    <div class="col-12 col-md-4 mb-3">
        <div class="form-group">
            <label for="fecha_adquisicion" class="label_nomina">Fecha de Adquisición</label>
            <input type="date" name="fecha_adquisicion" class="form-control" value="{{ old('fecha_adquisicion', $camioneta->fecha_adquisicion) }}">
        </div>
    </div>

    <div class="col-12 col-md-4 mb-3">
        <div class="form-group">
            <label for="ultimo_mantenimiento" class="label_nomina">Último Mantenimiento</label>
            <input type="date" name="ultimo_mantenimiento" class="form-control" value="{{ old('ultimo_mantenimiento', $camioneta->ultimo_mantenimiento) }}">
        
        </div>
    </div>

    <div class="col-12 col-md-4 mb-3">
        <div class="form-group">
            <label for="proximo_mantenimiento" class="label_nomina">Próximo Mantenimiento</label>
            <input type="date" name="proximo_mantenimiento" class="form-control" value="{{ old('proximo_mantenimiento', $camioneta->proximo_mantenimiento) }}">
        </div>
    </div>
</div>
<div class="row">
    <!-- Última Verificación, Próxima Verificación, Kilometraje, Rendimiento y Costo de Llenado -->
    <div class="col-12 col-md-3 mb-3">
        <div class="form-group">
            <label for="ultima_verificacion" class="label_nomina">Última Verificación</label>
            <input type="date" name="ultima_verificacion" class="form-control" 
                value="{{ old('ultima_verificacion', $camioneta->ultima_verificacion ? \Carbon\Carbon::parse($camioneta->ultima_verificacion)->format('Y-m-d') : '') }}">
        </div>
    </div>
    
    <div class="col-12 col-md-3 mb-3">
        <div class="form-group">
            <label for="proxima_verificacion" class="label_nomina">Próxima Verificación</label>
            <input type="date" name="proxima_verificacion" class="form-control" 
                value="{{ old('proxima_verificacion', $camioneta->proxima_verificacion ? \Carbon\Carbon::parse($camioneta->proxima_verificacion)->format('Y-m-d') : '') }}">
        </div>
    </div>

    <div class="col-12 col-md-2 mb-3">
        <div class="form-group">
            <label for="kilometraje" class="label_nomina">Kilometraje</label>
            <input type="number" name="kilometraje" class="form-control" step="1" value="{{ old('kilometraje', $camioneta->kilometraje) }}">
        </div>
    </div>

    <div class="col-12 col-md-2 mb-3">
        <div class="form-group">
            <label for="rendimiento_litro" class="label_nomina">Rendimiento (Km/L)</label>
            <input type="number" name="rendimiento_litro" class="form-control" step="0.1" value="{{ old('rendimiento_litro', $camioneta->rendimiento_litro) }}">
        </div>
    </div>

    <div class="col-12 col-md-2 mb-3">
        <div class="form-group">
            <label for="costo_llenado" class="label_nomina">Costo de Llenado ($)</label>
            <input type="number" name="costo_llenado" class="form-control" step="0.01" value="{{ old('costo_llenado', $camioneta->costo_llenado) }}">
        </div>
    </div>
</div>


<div class="division"></div>
<h5 class="titulos_encabezado">Documentación Obligatoria</h5>
<br>
<div class="row">
    <div class="col-md-6 col-12 mb-3">
        <div class="form-group">
            <label class="label_nomina">Tarjeta de Circulación
                <div class="info-icon">
                    ?
                    <div class="info-tooltip text-center">Solo archivos en formato PDF</div>
                </div>
            </label>
            <div class="d-flex align-items-start input-containerr">
                <label for="tarjeta_circulacion" class="file-input-label d-flex align-items-center">
                    <div class="icon-containerr me-2">
                        <img src="{{ asset('images/adjunto-archivo.png') }}" alt="Icono de video" class="icon-pin">
                    </div>
                    <span id="file-label-text-1">Seleccione archivos</span>
                </label>
                <input type="file" name="tarjeta_circulacion" class="file-input" accept=".pdf" id="tarjeta_circulacion">
                <div id="file-input-text-1" class="file-input-text ms-2">Sin selección</div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-12 mb-3">
        <div class="form-group">
            <label class="label_nomina">Verificación Vehicular
                <div class="info-icon">
                    ?
                    <div class="info-tooltip text-center">Solo archivos en formato PDF</div>
                </div>
            </label>
            <div class="d-flex align-items-start input-containerr">
                <label for="verificacion" class="file-input-label d-flex align-items-center">
                    <div class="icon-containerr me-2">
                        <img src="{{ asset('images/adjunto-archivo.png') }}" alt="Icono de archivo" class="icon-pin">
                    </div>
                    <span id="file-label-text-2">Seleccione archivos</span>
                </label>
                <input type="file" name="verificacion" class="file-input" accept=".pdf" id="verificacion">
                <div id="file-input-text-2" class="file-input-text ms-2">Sin selección</div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6 col-12 mb-3">
        <div class="form-group">
            <label class="label_nomina">Pago de Tenencia
                <div class="info-icon">
                    ?
                    <div class="info-tooltip text-center">Solo archivos en formato PDF</div>
                </div>
            </label>
            <div class="d-flex align-items-start input-containerr">
                <label for="tenencia" class="file-input-label d-flex align-items-center">
                    <div class="icon-containerr me-2">
                        <img src="{{ asset('images/adjunto-archivo.png') }}" alt="Icono de archivo" class="icon-pin">
                    </div>
                    <span id="file-label-text-3">Seleccione archivos</span>
                </label>
                <input type="file" name="tenencia" class="file-input" accept=".pdf" id="tenencia">
                <div id="file-input-text-3" class="file-input-text ms-2">Sin selección</div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-12 mb-3">
        <div class="form-group">
            <label class="label_nomina">Póliza de Seguro
                <div class="info-icon">
                    ?
                    <div class="info-tooltip text-center">Solo archivos en formato PDF</div>
                </div>
            </label>
            <div class="d-flex align-items-start input-containerr">
                <label for="seguro" class="file-input-label d-flex align-items-center">
                    <div class="icon-containerr me-2">
                        <img src="{{ asset('images/adjunto-archivo.png') }}" alt="Icono de archivo" class="icon-pin">
                    </div>
                    <span id="file-label-text-4">Seleccione archivos</span>
                </label>
                <input type="file" name="seguro" class="file-input" accept=".pdf" id="seguro">
                <div id="file-input-text-4" class="file-input-text ms-2">Sin selección</div>
            </div>
        </div>
    </div>
</div>

<br>
       
        <button type="submit" class="btn btn-primary">Guardar</button>
    </form>
</div>

<!-- Script para Validar el Número Máximo de Imágenes -->
<script>
    // Función unificada para actualizar el texto de selección de archivo y validar cantidad de archivos
    function updateFileInputTextAndValidate(inputId, labelId, textId) {
        const fileInput = document.getElementById(inputId);
        const fileInputText = document.getElementById(textId);
        const files = fileInput.files;
        const mensajeError = document.getElementById("mensajeError");

        // Actualiza el texto con el número de archivos seleccionados
        if (files.length > 0) {
            fileInputText.textContent = files.length + " archivo(s) seleccionado(s)";
        } else {
            fileInputText.textContent = 'Sin selección';
        }

        // Verifica la cantidad de archivos seleccionados
        if (files.length > 3) {
            mensajeError.textContent = "Solo puedes seleccionar un máximo de 3 archivos.";
            fileInput.value = ""; // Limpia la selección
            fileInputText.textContent = "Sin selección"; // Restablece el texto
        } else {
            mensajeError.textContent = ""; // Borra el mensaje de error si la cantidad es válida
        }
    }

    // Función para actualizar el texto de archivo en otros campos (tarjeta_circulacion, verificacion, etc.)
    function updateFileInputText(inputId, labelId, textId) {
        const fileInput = document.getElementById(inputId);
        const fileInputText = document.getElementById(textId);

        fileInput.addEventListener('change', function() {
            if (fileInput.files.length > 0) {
                fileInputText.textContent = fileInput.files[0].name;
            } else {
                fileInputText.textContent = 'Sin selección';
            }
        });
    }

    // Llamada a la función para cada uno de los campos
    updateFileInputText('tarjeta_circulacion', 'file-label-text-1', 'file-input-text-1');
    updateFileInputText('verificacion', 'file-label-text-2', 'file-input-text-2');
    updateFileInputText('tenencia', 'file-label-text-3', 'file-input-text-3');
    updateFileInputText('seguro', 'file-label-text-4', 'file-input-text-4');
</script>
@endsection

