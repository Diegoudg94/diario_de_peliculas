@extends('layouts.app')
@section('title', 'Mi colección')
@section('content')
<header class="hero mb-4">
    <div class="hero-copy"><div class="section-kicker"><i class="bi bi-stars" aria-hidden="true"></i> TU DIARIO DE CINE</div><h1>Las películas se terminan.<br><em>Las historias se quedan.</em></h1><p>Guarda lo que viste, lo que sentiste y lo que vale la pena recordar.</p><a class="btn btn-gold btn-lg" href="{{ route('peliculas.create') }}"><i class="bi bi-plus-lg" aria-hidden="true"></i> Registrar película</a></div>
    <div class="hero-art" aria-hidden="true"><div class="reel reel-one"></div><div class="reel reel-two"></div><i class="bi bi-play-fill"></i></div>
</header>

<section class="mb-5" aria-labelledby="stats-heading"><h2 id="stats-heading" class="visually-hidden">Estadísticas</h2><div class="row g-3">
    <div class="col-6 col-lg-3"><div class="stat-card"><div class="stat-icon"><i class="bi bi-collection-play" aria-hidden="true"></i></div><div class="stat-value">{{ $total }}</div><div class="stat-label">Películas vistas</div></div></div>
    <div class="col-6 col-lg-3"><div class="stat-card"><div class="stat-icon"><i class="bi bi-star-fill" aria-hidden="true"></i></div><div class="stat-value">{{ $promedio === null ? '—' : number_format($promedio, 1) }}<small>{{ $promedio === null ? '' : '/10' }}</small></div><div class="stat-label">Promedio</div></div></div>
    <div class="col-6 col-lg-3"><div class="stat-card"><div class="stat-icon"><i class="bi bi-trophy" aria-hidden="true"></i></div><div class="stat-value stat-movie">{{ $mejor?->titulo ?? '—' }}</div><div class="stat-label">Mejor calificada @if($mejor) · {{ $mejor->calificacion }}/10 @endif</div></div></div>
    <div class="col-6 col-lg-3"><div class="stat-card"><div class="stat-icon"><i class="bi bi-calendar-check" aria-hidden="true"></i></div><div class="stat-value">{{ $delMes }}</div><div class="stat-label">Vistas este mes</div></div></div>
</div></section>

<section aria-labelledby="collection-heading" class="collection-section">
    <div class="section-heading"><div><div class="section-kicker">TU COLECCIÓN</div><h2 id="collection-heading">Películas registradas</h2></div><span class="collection-count">{{ $peliculas->total() }} {{ $peliculas->total() === 1 ? 'resultado' : 'resultados' }}</span></div>
    <form method="GET" action="{{ route('peliculas.index') }}" class="filter-bar row g-3 align-items-end" aria-label="Buscar y filtrar películas">
        <div class="col-lg-5"><label for="buscar" class="form-label">Buscar por título</label><div class="input-icon"><i class="bi bi-search" aria-hidden="true"></i><input id="buscar" name="buscar" class="form-control" value="{{ $filtros['buscar'] ?? '' }}" placeholder="¿Qué película buscas?"></div></div>
        <div class="col-sm-6 col-lg-3"><label for="genero" class="form-label">Género</label><select id="genero" name="genero" class="form-select"><option value="">Todos los géneros</option>@foreach($generos as $genero)<option value="{{ $genero }}" @selected(($filtros['genero'] ?? '') === $genero)>{{ $genero }}</option>@endforeach</select></div>
        <div class="col-sm-6 col-lg-2"><label for="orden" class="form-label">Ordenar por</label><select id="orden" name="orden" class="form-select"><option value="recientes" @selected(($filtros['orden'] ?? '') === 'recientes')>Más recientes</option><option value="antiguas" @selected(($filtros['orden'] ?? '') === 'antiguas')>Más antiguas</option><option value="mejor" @selected(($filtros['orden'] ?? '') === 'mejor')>Mejor calificación</option><option value="menor" @selected(($filtros['orden'] ?? '') === 'menor')>Menor calificación</option></select></div>
        <div class="col-lg-2 d-flex gap-2"><button class="btn btn-gold flex-grow-1" type="submit">Aplicar</button><a class="btn btn-outline-light" href="{{ route('peliculas.index') }}" aria-label="Limpiar filtros"><i class="bi bi-x-lg" aria-hidden="true"></i></a></div>
    </form>

    @if($peliculas->isEmpty())
        <div class="empty-state"><i class="bi bi-film" aria-hidden="true"></i><h3>{{ $total === 0 ? 'Tu diario comienza aquí' : 'No encontramos películas' }}</h3><p>{{ $total === 0 ? 'Registra tu primera película para empezar tu colección.' : 'Prueba con otro título o cambia los filtros.' }}</p><a class="btn btn-gold" href="{{ $total === 0 ? route('peliculas.create') : route('peliculas.index') }}">{{ $total === 0 ? 'Agregar película' : 'Ver todas' }}</a></div>
    @else
        <div class="row g-4">@foreach($peliculas as $pelicula)<div class="col-12 col-md-6 col-lg-4">@include('peliculas.partials.card')</div>@endforeach</div>
        <div class="mt-4">{{ $peliculas->links() }}</div>
    @endif
</section>
@endsection
