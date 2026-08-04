@if($publicaciones->where('fijado', true)->count())
<div class="grid">
    @foreach($publicaciones->where('fijado', true) as $pub)
        <div class="card">
            <a href="{{ route('publicaciones.show', $pub->id) }}" class="card-link">
                <span class="badge">Fijado</span>
                @include('components.pub-card', ['pub' => $pub])
            </a>
        </div>
    @endforeach
</div>
@endif

<h2 class="titulo-publicaciones">
    <i class="fas fa-clock-rotate-left"></i>
    <span>Últimas Publicaciones</span>
</h2>

@if($publicaciones->where('fijado', false)->count())
<div class="grid">
    @foreach($publicaciones->where('fijado', false) as $pub)
        <div class="card">
            <a href="{{ route('publicaciones.show', $pub->id) }}" class="card-link">
                @if($pub->created_at->gt(now()->subDays(3)))
                    <span class="badge nueva">Nuevo</span>
                @endif
                @include('components.pub-card', ['pub' => $pub])
            </a>
        </div>
    @endforeach
</div>
@endif
