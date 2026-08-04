@extends('layouts.app')

@section('title', 'Automóvil')
@section('titulo', 'Editar Automóvil')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>
    /* VARIABLES DE TEMA PREMIUM (Idénticas a la vista de registro) */
    :root {
        --bg-body: #F9FAFB;
        --surface: #FFFFFF;
        --primary: #0F172A;
        --primary-hover: #334155;
        --accent: #3B82F6;
        --danger: #EF4444;
        --success: #10B981; /* Verde para archivos existentes */
        --text-main: #1E293B;
        --text-muted: #64748B;
        --border-color: #E2E8F0;
        --border-focus: #93C5FD;
        --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
        --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
        --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
        --radius: 12px;
        --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    body {
        background-color: var(--bg-body);
        font-family: 'Inter', sans-serif;
        color: var(--text-main);
    }

    /* ANIMACIONES TIPO REACT/FRAMER MOTION */
    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .stagger-1 { animation: fadeUp 0.6s ease-out forwards; animation-delay: 0.1s; opacity: 0; }
    .stagger-2 { animation: fadeUp 0.6s ease-out forwards; animation-delay: 0.2s; opacity: 0; }
    .stagger-3 { animation: fadeUp 0.6s ease-out forwards; animation-delay: 0.3s; opacity: 0; }

    /* CONTENEDOR PRINCIPAL */
    .premium-container {
        max-width: 1200px;
        margin: 2rem auto;
        background: var(--surface);
        border-radius: var(--radius);
        box-shadow: var(--shadow-lg);
        padding: 2.5rem;
        border: 1px solid rgba(255, 255, 255, 0.5);
    }

    /* TIPOGRAFÍA Y ENCABEZADOS */
    .section-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--primary);
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .section-title::before {
        content: '';
        display: block;
        width: 4px;
        height: 20px;
        background: var(--accent);
        border-radius: 4px;
    }
    .division {
        height: 1px;
        background: var(--border-color);
        margin: 2.5rem 0;
    }

    /* INPUTS Y FORMULARIOS MODERNOS */
    .form-group { position: relative; margin-bottom: 1.5rem; }
    .label_nomina {
        font-weight: 500; font-size: 0.875rem; color: var(--text-muted);
        margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.5rem;
    }
    .input-wrapper { position: relative; display: flex; align-items: center; }
    .input-icon {
        position: absolute; left: 12px; width: 20px; height: 20px;
        opacity: 0.5; transition: var(--transition); pointer-events: none;
    }
    .form-control {
        width: 100%; padding: 0.75rem 1rem 0.75rem 2.5rem;
        background-color: var(--bg-body); border: 1px solid var(--border-color);
        border-radius: 8px; font-size: 0.95rem; color: var(--text-main);
        transition: var(--transition);
    }
    .form-control:focus {
        outline: none; background-color: var(--surface);
        border-color: var(--accent); box-shadow: 0 0 0 4px var(--border-focus);
    }
    .form-control:focus + .input-icon,
    .form-control:not(:placeholder-shown) + .input-icon { opacity: 1; }

    /* ZONAS DE UPLOAD DE ARCHIVOS (DROPZONE STYLE) */
    .upload-zone {
        border: 2px dashed var(--border-color); border-radius: var(--radius);
        padding: 2rem; text-align: center; background: var(--bg-body);
        transition: var(--transition); cursor: pointer; display: flex;
        flex-direction: column; align-items: center; gap: 0.75rem;
    }
    .upload-zone:hover { border-color: var(--accent); background: #EFF6FF; }
    .upload-zone img { width: 32px; height: 32px; opacity: 0.7; }
    .upload-zone-text { font-size: 0.875rem; color: var(--text-muted); }
    .upload-zone-text strong { color: var(--accent); }

    /* CONTENEDOR DE PREVISUALIZACIÓN Y BOTÓN DE ELIMINAR */
    .image-preview-container {
        display: flex; gap: 12px; margin-top: 1rem; flex-wrap: wrap;
    }
    .preview-wrapper { position: relative; animation: fadeUp 0.3s ease-out; }
    .image-preview {
        width: 90px; height: 90px; object-fit: cover; border-radius: 8px;
        box-shadow: var(--shadow-sm); border: 1px solid var(--border-color);
        transition: var(--transition);
    }

    /* Etiqueta para fotos que ya estaban en BD */
    .existing-badge {
        position: absolute; bottom: 4px; left: 50%; transform: translateX(-50%);
        background: rgba(0,0,0,0.6); color: white; font-size: 10px;
        padding: 2px 6px; border-radius: 4px; white-space: nowrap;
    }

    .remove-btn {
        position: absolute; top: -6px; right: -6px; background: var(--danger);
        color: white; border: none; border-radius: 50%; width: 20px; height: 20px;
        font-size: 12px; display: flex; align-items: center; justify-content: center;
        cursor: pointer; opacity: 0; transition: var(--transition); box-shadow: var(--shadow-md);
    }
    .preview-wrapper:hover .image-preview { filter: brightness(0.8); }
    .preview-wrapper:hover .remove-btn { opacity: 1; transform: scale(1.1); }

    /* ARCHIVOS PEQUEÑOS (PDFs) */
    .file-pill {
        display: flex; align-items: center; background: var(--bg-body);
        border: 1px solid var(--border-color); border-radius: 8px;
        padding: 0.5rem; transition: var(--transition);
    }
    .file-pill:hover { border-color: var(--accent); box-shadow: var(--shadow-sm); }
    .file-pill label {
        cursor: pointer; display: flex; align-items: center; gap: 0.5rem;
        padding: 0.25rem 0.5rem; background: var(--surface); border-radius: 6px;
        border: 1px solid var(--border-color); font-size: 0.8rem;
        font-weight: 500; transition: var(--transition);
    }
    .file-pill label:hover { background: var(--bg-body); }
    .file-name-display {
        font-size: 0.85rem; color: var(--text-muted); margin-left: 0.75rem;
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 150px;
    }

    /* Estilo para mostrar si ya existe un archivo PDF cargado */
    .file-exists-indicator {
        display: inline-flex; align-items: center; gap: 4px; font-size: 0.75rem;
        color: var(--success); margin-top: 4px; margin-left: 4px;
    }
    .file-exists-indicator svg { width: 14px; height: 14px; }

    /* BOTÓN SUBMIT PREMIUM Y CONTENEDOR DE ACCIONES */
    .form-actions {
        display: flex; justify-content: flex-end; margin-top: 2rem;
        padding-top: 1.5rem; border-top: 1px solid var(--border-color);
    }
    .btn-premium {
        background: var(--primary); color: white; padding: 0.75rem 1.5rem;
        border: none; border-radius: 8px; font-weight: 500; font-size: 0.95rem;
        cursor: pointer; transition: var(--transition); display: flex;
        align-items: center; justify-content: center; gap: 0.5rem;
    }
    .btn-premium:hover {
        background: var(--primary-hover); transform: translateY(-2px); box-shadow: var(--shadow-md);
    }
    .btn-premium:active { transform: translateY(0); }

    /* TOOLTIPS MINIMALISTAS */
    .info-icon {
        display: inline-flex; align-items: center; justify-content: center;
        width: 16px; height: 16px; background: var(--border-color);
        color: var(--text-muted); border-radius: 50%; font-size: 10px;
        cursor: help; position: relative;
    }
    .info-tooltip {
        position: absolute; bottom: 120%; left: 50%; transform: translateX(-50%) translateY(5px);
        background: var(--primary); color: white; padding: 0.5rem; border-radius: 4px;
        font-size: 0.75rem; opacity: 0; visibility: hidden; transition: var(--transition);
        white-space: nowrap; z-index: 10;
    }
    .info-icon:hover .info-tooltip { opacity: 1; visibility: visible; transform: translateX(-50%) translateY(0); }

    .error-message { color: var(--danger); font-size: 0.85rem; margin-top: 0.5rem; display: none; }

    .server-feedback {
        margin-bottom: 1.5rem;
        padding: 1rem 1.1rem;
        border-radius: 10px;
        border: 1px solid #fecaca;
        background: #fef2f2;
        color: #991b1b;
        font-size: 0.92rem;
    }
    .server-feedback ul {
        margin: .5rem 0 0 1rem;
        padding: 0;
    }
    .server-feedback strong {
        display: block;
        margin-bottom: .35rem;
    }
</style>

@php
    use Illuminate\Support\Facades\Storage;

    $fotosExistentes = [];

    if (!empty($camioneta->fotos)) {
        $decoded = json_decode($camioneta->fotos, true);

        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            foreach ($decoded as $foto) {
                if (!empty($foto)) {
                    $fotosExistentes[] = [
                        'path' => $foto,
                        'url'  => Storage::url($foto),
                        'isNew' => false,
                    ];
                }
            }
        } elseif (is_string($camioneta->fotos) && $camioneta->fotos !== '') {
            $fotosExistentes[] = [
                'path' => $camioneta->fotos,
                'url'  => Storage::url($camioneta->fotos),
                'isNew' => false,
            ];
        }
    }
@endphp

<div class="container">
    <div class="premium-container stagger-1">

        @if(session('error') || $errors->any())
            <div class="server-feedback">
                <strong>{{ session('error', 'No se pudo actualizar el vehículo.') }}</strong>

                @if(session('error_detalle'))
                    <div style="margin-bottom:.35rem;">{{ session('error_detalle') }}</div>
                @endif

                @if($errors->any())
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>
        @endif

        <form action="{{ route('camionetas.update', $camioneta->id) }}" method="POST" enctype="multipart/form-data" id="registroVehiculoForm">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <h5 class="section-title">Evidencia Fotográfica</h5>
                <p class="text-muted" style="font-size: 0.875rem; margin-bottom: 1rem;">Actualiza las fotografías del vehículo. Puedes subir hasta 4 imágenes en total.</p>

                <label for="fotos" class="upload-zone" id="drop-zone-fotos">
                    <img src="{{ asset('images/adjunto-archivo.png') }}" alt="Subir">
                    <div class="upload-zone-text">
                        <strong>Haz clic para buscar</strong> o arrastra las nuevas imágenes aquí
                    </div>
                    <span id="file-label-text" style="font-size: 0.8rem; color: var(--text-muted);">Formatos: JPG, PNG</span>
                </label>

                <input type="file" accept="image/*" id="fotos" multiple style="display: none;">
                <input type="file" name="fotos[]" id="fotos_reales" multiple style="display: none;">
                <input type="hidden" name="fotos_eliminadas" id="fotos_eliminadas" value="">

                <div id="image-preview-container" class="image-preview-container"></div>
                <p id="mensajeError" class="error-message"></p>
            </div>

            <div class="division"></div>

            <h5 class="section-title stagger-2">Datos Generales del Vehículo</h5>

            <div class="row stagger-2">
                <div class="col-12 col-md-6 mb-3">
                    <div class="form-group">
                        <label for="placa" class="label_nomina">Número de Placa</label>
                        <div class="input-wrapper">
                            <input type="text" name="placa" class="form-control" placeholder="Ej. ABC-123-D" value="{{ old('placa', $camioneta->placa) }}" required>
                            <img src="{{ asset('images/placa.png') }}" alt="Placa" class="input-icon">
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 mb-3">
                    <div class="form-group">
                        <label for="vin" class="label_nomina">Número de Serie (VIN)</label>
                        <div class="input-wrapper">
                            <input type="text" name="vin" class="form-control" placeholder="17 caracteres" value="{{ old('vin', $camioneta->vin) }}" required>
                            <img src="{{ asset('images/serie.png') }}" alt="VIN" class="input-icon">
                        </div>
                    </div>
                </div>
            </div>

            <div class="row stagger-2">
                <div class="col-12 col-md-4 mb-3">
                    <div class="form-group">
                        <label for="marca" class="label_nomina">Marca</label>
                        <div class="input-wrapper">
                            <input type="text" name="marca" class="form-control" placeholder="Ej. Ford" value="{{ old('marca', $camioneta->marca) }}" required>
                            <img src="{{ asset('images/camioneta.png') }}" alt="Marca" class="input-icon">
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4 mb-3">
                    <div class="form-group">
                        <label for="modelo" class="label_nomina">Modelo</label>
                        <div class="input-wrapper">
                            <input type="text" name="modelo" class="form-control" placeholder="Ej. Ranger" value="{{ old('modelo', $camioneta->modelo) }}" required>
                            <img src="{{ asset('images/carro.png') }}" alt="Modelo" class="input-icon">
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4 mb-3">
                    <div class="form-group">
                        <label for="anio" class="label_nomina">Año</label>
                        <div class="input-wrapper">
                            <input type="number" name="anio" class="form-control" placeholder="2024" value="{{ old('anio', $camioneta->anio) }}" required>
                            <img src="{{ asset('images/anio.png') }}" alt="Año" class="input-icon">
                        </div>
                    </div>
                </div>
            </div>

            <div class="row stagger-2">
                <div class="col-12 col-md-3 mb-3">
                    <div class="form-group">
                        <label class="label_nomina">Color</label>
                        <div class="input-wrapper">
                            <input type="text" name="color" class="form-control" value="{{ old('color', $camioneta->color) }}" required>
                            <img src="{{ asset('images/paleta.png') }}" alt="Color" class="input-icon">
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-3 mb-3">
                    <div class="form-group">
                        <label class="label_nomina">Tipo de Motor</label>
                        <div class="input-wrapper">
                            <input type="text" name="tipo_motor" class="form-control" value="{{ old('tipo_motor', $camioneta->tipo_motor) }}">
                            <img src="{{ asset('images/motor.png') }}" alt="Motor" class="input-icon">
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-3 mb-3">
                    <div class="form-group">
                        <label class="label_nomina">Combustible</label>
                        <div class="input-wrapper">
                            <input type="text" name="tipo_combustible" class="form-control" value="{{ old('tipo_combustible', $camioneta->tipo_combustible) }}">
                            <img src="{{ asset('images/gasolina.png') }}" alt="Combustible" class="input-icon">
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-3 mb-3">
                    <div class="form-group">
                        <label class="label_nomina">Cap. de Carga</label>
                        <div class="input-wrapper">
                            <input type="text" name="capacidad_carga" class="form-control" value="{{ old('capacidad_carga', $camioneta->capacidad_carga) }}">
                            <img src="{{ asset('images/carga.png') }}" alt="Carga" class="input-icon">
                        </div>
                    </div>
                </div>
            </div>

            <div class="row stagger-2">
                <div class="col-12 col-md-4 mb-3">
                    <div class="form-group">
                        <label class="label_nomina">Fecha de Adquisición</label>
                        <input type="date" name="fecha_adquisicion" class="form-control" style="padding-left: 1rem;" value="{{ old('fecha_adquisicion', $camioneta->fecha_adquisicion ? \Carbon\Carbon::parse($camioneta->fecha_adquisicion)->format('Y-m-d') : '') }}">
                    </div>
                </div>
                <div class="col-12 col-md-4 mb-3">
                    <div class="form-group">
                        <label class="label_nomina">Último Mantenimiento</label>
                        <input type="date" name="ultimo_mantenimiento" class="form-control" style="padding-left: 1rem;" value="{{ old('ultimo_mantenimiento', $camioneta->ultimo_mantenimiento ? \Carbon\Carbon::parse($camioneta->ultimo_mantenimiento)->format('Y-m-d') : '') }}">
                    </div>
                </div>
                <div class="col-12 col-md-4 mb-3">
                    <div class="form-group">
                        <label class="label_nomina">Próximo Mantenimiento</label>
                        <input type="date" name="proximo_mantenimiento" class="form-control" style="padding-left: 1rem;" value="{{ old('proximo_mantenimiento', $camioneta->proximo_mantenimiento ? \Carbon\Carbon::parse($camioneta->proximo_mantenimiento)->format('Y-m-d') : '') }}">
                    </div>
                </div>
            </div>

            <div class="row stagger-2">
                <div class="col-12 col-md-3 mb-3">
                    <div class="form-group">
                        <label class="label_nomina">Última Verificación</label>
                        <input type="date" name="ultima_verificacion" class="form-control" style="padding-left: 1rem;"
                            value="{{ old('ultima_verificacion', $camioneta->ultima_verificacion ? \Carbon\Carbon::parse($camioneta->ultima_verificacion)->format('Y-m-d') : '') }}">
                    </div>
                </div>
                <div class="col-12 col-md-3 mb-3">
                    <div class="form-group">
                        <label class="label_nomina">Próxima Verificación</label>
                        <input type="date" name="proxima_verificacion" class="form-control" style="padding-left: 1rem;"
                            value="{{ old('proxima_verificacion', $camioneta->proxima_verificacion ? \Carbon\Carbon::parse($camioneta->proxima_verificacion)->format('Y-m-d') : '') }}">
                    </div>
                </div>
                <div class="col-12 col-md-2 mb-3">
                    <div class="form-group">
                        <label class="label_nomina">Kilometraje</label>
                        <input type="number" name="kilometraje" class="form-control" step="1" style="padding-left: 1rem;" value="{{ old('kilometraje', $camioneta->kilometraje) }}">
                    </div>
                </div>
                <div class="col-12 col-md-2 mb-3">
                    <div class="form-group">
                        <label class="label_nomina">Rendimiento (Km/L)</label>
                        <input type="number" name="rendimiento_litro" class="form-control" step="0.1" style="padding-left: 1rem;" value="{{ old('rendimiento_litro', $camioneta->rendimiento_litro) }}">
                    </div>
                </div>
                <div class="col-12 col-md-2 mb-3">
                    <div class="form-group">
                        <label class="label_nomina">Costo Llenado ($)</label>
                        <input type="number" name="costo_llenado" class="form-control" step="0.01" style="padding-left: 1rem;" value="{{ old('costo_llenado', $camioneta->costo_llenado) }}">
                    </div>
                </div>
            </div>

            <div class="division"></div>

            <h5 class="section-title stagger-3">Documentación Obligatoria
                <span class="info-icon">?<span class="info-tooltip">Subir nuevo archivo reemplazará el anterior</span></span>
            </h5>

            <div class="row stagger-3">
                <div class="col-md-6 col-12 mb-3">
                    <div class="form-group">
                        <label class="label_nomina">Tarjeta de Circulación</label>
                        <div class="file-pill">
                            <label for="tarjeta_circulacion">
                                <img src="{{ asset('images/adjunto-archivo.png') }}" alt="Pin" width="16"> Reemplazar
                            </label>
                            <input type="file" name="tarjeta_circulacion" accept=".pdf" id="tarjeta_circulacion" style="display: none;">
                            <span id="file-input-text-1" class="file-name-display">Ningún archivo nuevo</span>
                        </div>
                        @if($camioneta->tarjeta_circulacion)
                        <div class="file-exists-indicator">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Archivo actual guardado
                        </div>
                        @endif
                    </div>
                </div>

                <div class="col-md-6 col-12 mb-3">
                    <div class="form-group">
                        <label class="label_nomina">Verificación Vehicular</label>
                        <div class="file-pill">
                            <label for="verificacion">
                                <img src="{{ asset('images/adjunto-archivo.png') }}" alt="Pin" width="16"> Reemplazar
                            </label>
                            <input type="file" name="verificacion" accept=".pdf" id="verificacion" style="display: none;">
                            <span id="file-input-text-2" class="file-name-display">Ningún archivo nuevo</span>
                        </div>
                        @if($camioneta->verificacion)
                        <div class="file-exists-indicator">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Archivo actual guardado
                        </div>
                        @endif
                    </div>
                </div>

                <div class="col-md-6 col-12 mb-3">
                    <div class="form-group">
                        <label class="label_nomina">Pago de Tenencia</label>
                        <div class="file-pill">
                            <label for="tenencia">
                                <img src="{{ asset('images/adjunto-archivo.png') }}" alt="Pin" width="16"> Reemplazar
                            </label>
                            <input type="file" name="tenencia" accept=".pdf" id="tenencia" style="display: none;">
                            <span id="file-input-text-3" class="file-name-display">Ningún archivo nuevo</span>
                        </div>
                        @if($camioneta->tenencia)
                        <div class="file-exists-indicator">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Archivo actual guardado
                        </div>
                        @endif
                    </div>
                </div>

                <div class="col-md-6 col-12 mb-3">
                    <div class="form-group">
                        <label class="label_nomina">Póliza de Seguro</label>
                        <div class="file-pill">
                            <label for="seguro">
                                <img src="{{ asset('images/adjunto-archivo.png') }}" alt="Pin" width="16"> Reemplazar
                            </label>
                            <input type="file" name="seguro" accept=".pdf" id="seguro" style="display: none;">
                            <span id="file-input-text-4" class="file-name-display">Ningún archivo nuevo</span>
                        </div>
                        @if($camioneta->seguro)
                        <div class="file-exists-indicator">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Archivo actual guardado
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="form-actions stagger-3">
                <button type="submit" class="btn-premium">
                    Actualizar Vehículo
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                </button>
            </div>

        </form>
    </div>
</div>

<script>
    let existingFotos = @json($fotosExistentes);
    let newFilesArray = [];
    let fotosEliminadasIds = [];

    document.addEventListener("DOMContentLoaded", () => {
        const setupFileInput = (inputId, textId) => {
            const fileInput = document.getElementById(inputId);
            const textDisplay = document.getElementById(textId);

            if (fileInput && textDisplay) {
                fileInput.addEventListener('change', (e) => {
                    textDisplay.textContent = e.target.files.length > 0
                        ? e.target.files[0].name
                        : 'Ningún archivo nuevo';
                    textDisplay.style.color = e.target.files.length > 0 ? 'var(--text-main)' : 'var(--text-muted)';
                });
            }
        };

        setupFileInput('tarjeta_circulacion', 'file-input-text-1');
        setupFileInput('verificacion', 'file-input-text-2');
        setupFileInput('tenencia', 'file-input-text-3');
        setupFileInput('seguro', 'file-input-text-4');

        const dropZone = document.getElementById('drop-zone-fotos');
        const fileInputFotos = document.getElementById('fotos');

        if (dropZone) {
            ['dragenter', 'dragover'].forEach(eventName => {
                dropZone.addEventListener(eventName, (e) => {
                    e.preventDefault();
                    dropZone.style.borderColor = 'var(--accent)';
                    dropZone.style.background = '#EFF6FF';
                });
            });

            ['dragleave', 'drop'].forEach(eventName => {
                dropZone.addEventListener(eventName, (e) => {
                    e.preventDefault();
                    dropZone.style.borderColor = 'var(--border-color)';
                    dropZone.style.background = 'var(--bg-body)';
                });
            });

            dropZone.addEventListener('drop', (e) => {
                handleNewFiles(e.dataTransfer.files);
            });
        }

        if (fileInputFotos) {
            fileInputFotos.addEventListener('change', (e) => {
                handleNewFiles(e.target.files);
                fileInputFotos.value = "";
            });
        }

        renderAllPreviews();
    });

    function handleNewFiles(files) {
        const errorMessage = document.getElementById('mensajeError');
        const incomingFiles = Array.from(files || []);
        let totalCount = existingFotos.length + newFilesArray.length + incomingFiles.length;

        if (totalCount > 4) {
            errorMessage.textContent = "Has superado el límite total de 4 imágenes (contando las guardadas).";
            errorMessage.style.display = "block";

            let spaceLeft = 4 - (existingFotos.length + newFilesArray.length);
            if (spaceLeft > 0) {
                newFilesArray = newFilesArray.concat(incomingFiles.slice(0, spaceLeft));
            }
        } else {
            errorMessage.style.display = "none";
            newFilesArray = newFilesArray.concat(incomingFiles);
        }

        renderAllPreviews();
        updateRealInput();
    }

    function renderAllPreviews() {
        const previewContainer = document.getElementById('image-preview-container');
        const labelText = document.getElementById('file-label-text');

        previewContainer.innerHTML = "";
        let total = existingFotos.length + newFilesArray.length;

        if (total > 0) {
            labelText.textContent = `${total} archivo(s) en total`;
            labelText.style.color = 'var(--accent)';
            labelText.style.fontWeight = '600';

            existingFotos.forEach((foto, index) => {
                createPreviewElement(previewContainer, foto.url, true, index, index);
            });

            newFilesArray.forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = function (e) {
                    createPreviewElement(previewContainer, e.target.result, false, index, existingFotos.length + index);
                };
                reader.readAsDataURL(file);
            });
        } else {
            labelText.textContent = "Formatos: JPG, PNG";
            labelText.style.color = 'var(--text-muted)';
            labelText.style.fontWeight = 'normal';
        }
    }

    function createPreviewElement(container, src, isExisting, sourceIndex, displayIndex) {
        const wrapper = document.createElement("div");
        wrapper.classList.add("preview-wrapper");
        wrapper.style.animationDelay = `${displayIndex * 0.1}s`;

        const img = document.createElement("img");
        img.src = src;
        img.classList.add("image-preview");

        if (isExisting) {
            const badge = document.createElement("span");
            badge.classList.add("existing-badge");
            badge.innerText = "Guardada";
            wrapper.appendChild(badge);
        }

        const removeBtn = document.createElement("button");
        removeBtn.innerHTML = "✕";
        removeBtn.classList.add("remove-btn");
        removeBtn.type = "button";
        removeBtn.onclick = () => removeMixedImage(isExisting, sourceIndex);

        wrapper.appendChild(img);
        wrapper.appendChild(removeBtn);
        container.appendChild(wrapper);
    }

    function removeMixedImage(isExisting, index) {
        if (isExisting) {
            fotosEliminadasIds.push(existingFotos[index].path);
            document.getElementById('fotos_eliminadas').value = JSON.stringify(fotosEliminadasIds);
            existingFotos.splice(index, 1);
        } else {
            newFilesArray.splice(index, 1);
        }

        renderAllPreviews();
        updateRealInput();

        const errorMessage = document.getElementById('mensajeError');
        if ((existingFotos.length + newFilesArray.length) <= 4) {
            errorMessage.style.display = "none";
        }
    }

    function updateRealInput() {
        const dataTransfer = new DataTransfer();
        newFilesArray.forEach(file => dataTransfer.items.add(file));
        document.getElementById('fotos_reales').files = dataTransfer.files;
    }
</script>
@endsection