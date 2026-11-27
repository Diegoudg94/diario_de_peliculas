<article class="movie-card h-100">
    <div class="poster-wrap">
        <img src="{{ $pelicula->portada_url ?: asset('images/poster-placeholder.svg') }}" alt="{{ $pelicula->portada_url ? 'Portada de '.$pelicula->titulo : 'Portada genérica de película' }}" loading="lazy" onerror="this.onerror=null;this.src='{{ asset('images/poster-placeholder.svg') }}'">
        <span class="rating-badge" aria-label="Calificación {{ $pelicula->calificacion }} de 10"><i class="bi bi-star-fill" aria-hidden="true"></i> {{ $pelicula->calificacion }}<small>/10</small></span>
    </div>
    <div class="movie-body">
        <div class="eyebrow">{{ $pelicula->genero }} <span aria-hidden="true">·</span> {{ $pelicula->anio_estreno }}</div>
        <h3><a href="{{ route('peliculas.show', $pelicula) }}">{{ $pelicula->titulo }}</a></h3>
        <p class="movie-comment">{{ \Illuminate\Support\Str::limit($pelicula->comentario, 105) }}</p>
        <div class="movie-meta"><i class="bi bi-calendar3" aria-hidden="true"></i> Vista el {{ $pelicula->fecha_vista->format('d/m/Y') }}</div>
        <div class="movie-actions">
            <a href="{{ route('peliculas.show', $pelicula) }}" class="btn btn-outline-light btn-sm">Ver detalle</a>
            <a href="{{ route('peliculas.edit', $pelicula) }}" class="btn btn-link btn-sm" aria-label="Editar {{ $pelicula->titulo }}"><i class="bi bi-pencil-square" aria-hidden="true"></i></a>
            <form action="{{ route('peliculas.destroy', $pelicula) }}" method="POST" class="d-inline delete-form" data-title="{{ $pelicula->titulo }}">
                @csrf @method('DELETE')
                <button class="btn btn-link btn-sm text-danger" type="submit" aria-label="Eliminar {{ $pelicula->titulo }}"><i class="bi bi-trash3" aria-hidden="true"></i></button>
            </form>
        </div>
    </div>
</article>
