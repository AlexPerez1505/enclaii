<!-- Mostrar imagen -->
@if($pub->tipo === 'imagen')
    <img src="{{ Storage::url($pub->archivo) }}" alt="Imagen de publicación">
@elseif($pub->tipo === 'video')
    <video controls>
        <source src="{{ Storage::url($pub->archivo) }}">
        Tu navegador no soporta el video.
    </video>
@else
    <div class="info">
        <h3>{{ $pub->titulo }}</h3>
        <p>{{ $pub->descripcion }}</p>
    </div>
    <a class="doc-link" href="{{ Storage::url($pub->archivo) }}" target="_blank">📄 Ver Documento</a>
@endif

@if($pub->tipo !== 'documento')
    <div class="info">
        <h3>{{ $pub->titulo }}</h3>
        <p>{{ $pub->descripcion }}</p>
    </div>
@endif
