@extends('layouts.app')

@section('content')
<style>
    body {
        background-color: #F9FAFC;
        font-family: 'Poppins', sans-serif;
    }
    .container-custom {
        display: flex;
        flex-wrap: wrap;
        max-width: 1200px;
        margin: 50px auto;
        gap: 40px;
        padding: 20px;
        justify-content: center;
    }
    .image-gallery {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        min-width: 300px;
    }
    .image-gallery img {
        width: 100%;
        max-width: 500px;
        border-radius: 15px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease-in-out;
    }
    .image-gallery img:hover {
        transform: scale(1.05);
    }
    .thumbnails {
        display: flex;
        gap: 10px;
        margin-top: 15px;
        flex-wrap: wrap;
        justify-content: center;
    }
    .thumbnails img {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 8px;
        cursor: pointer;
        transition: transform 0.3s ease-in-out, opacity 0.3s;
    }
    .thumbnails img:hover {
        transform: scale(1.1);
        opacity: 0.8;
    }
    .details-section {
        flex: 1;
        background: #ffffff;
        padding: 30px;
        border-radius: 15px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        min-width: 300px;
    }
    .details-section h1 {
        font-size: 2rem;
        color: #1E6BB8;
        font-weight: bold;
    }
    .details-list {
        list-style: none;
        padding: 0;
        margin-top: 20px;
    }
    .details-list li {
        font-size: 1.1rem;
        margin-bottom: 10px;
    }
    .details-list strong {
        color: #212529;
    }
    .btn-container {
        margin-top: 20px;
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }
    .btn-custom {
        padding: 12px 20px;
        border-radius: 8px;
        font-weight: bold;
        text-align: center;
        transition: background 0.3s, transform 0.2s;
    }
    .btn-custom:hover {
        transform: scale(1.05);
    }
    @media (max-width: 768px) {
        .container-custom {
            flex-direction: column;
            align-items: center;
        }
        .details-section {
            text-align: center;
        }
    }
</style>

<div class="container-custom">
    <div class="image-gallery">
        <img id="main-image" src="{{ $camioneta->fotos ? Storage::url(json_decode($camioneta->fotos)[0]) : '/images/default-car.jpg' }}" alt="Foto Principal">
        <div class="thumbnails">
            @foreach(json_decode($camioneta->fotos) as $foto)
                <img src="{{ Storage::url($foto) }}" alt="Foto" onclick="document.getElementById('main-image').src=this.src">
            @endforeach
        </div>
    </div>
    <div class="details-section">
    <h1> {{ $camioneta->marca }} {{ $camioneta->modelo }}</h1>
    <ul class="details-list">
        @foreach([
            'Placa' => 'placa',
            'VIN' => 'vin',
            'Año' => 'anio',
            'Color' => 'color',
            'Tipo de Motor' => 'tipo_motor',
            'Capacidad de Carga' => 'capacidad_carga',
            'Tipo de Combustible' => 'tipo_combustible',
            'Fecha de Adquisición' => 'fecha_adquisicion',
            'Último Mantenimiento' => 'ultimo_mantenimiento',
            'Próximo Mantenimiento' => 'proximo_mantenimiento',
            'Última Verificación' => 'ultima_verificacion',
            'Próxima Verificación' => 'proxima_verificacion',
            'Kilometraje' => 'kilometraje',
            'Rendimiento por Litro' => 'rendimiento_litro',
            'Costo de Llenado' => 'costo_llenado'
            ] as $label => $field)
            <li>
                <strong>{{ $label }}:</strong> 
                @if($field == 'kilometraje')
                    {{ $camioneta->$field }} KM
                @elseif($field == 'rendimiento_litro')
                    {{ number_format($camioneta->$field, 0) }} KM
                @elseif($field == 'costo_llenado')
                    ${{ number_format($camioneta->$field, 0) }}
                @else
                    {{ $camioneta->$field ?? 'No especificado' }}
                @endif
            </li>
        @endforeach
    </ul>

    <h3 class="mt-4">📑 Documentos</h3>
    <ul>
        @foreach([
            'Tarjeta de Circulación' => 'tarjeta_circulacion',
            'Verificación' => 'verificacion',
            'Tenencia' => 'tenencia',
            'Seguro' => 'seguro'
        ] as $doc => $field)
            @if($camioneta->$field)
                <li><a href="{{ Storage::url($camioneta->$field) }}" target="_blank">{{ $doc }}</a></li>
            @endif
        @endforeach
    </ul>


        <div class="btn-container">
            <a href="{{ route('camionetas.index') }}" class="btn btn-primary btn-custom">⬅️ Volver</a>
            <a href="{{ route('camionetas.edit', $camioneta->id) }}" class="btn btn-warning btn-custom">✏️ Editar</a>
        </div>
    </div>
</div>
@endsection
