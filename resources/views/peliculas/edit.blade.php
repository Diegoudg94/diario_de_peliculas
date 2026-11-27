@extends('layouts.app')
@section('title', 'Editar '.$pelicula->titulo)
@section('content')
<div class="page-header"><a href="{{ route('peliculas.show', $pelicula) }}" class="back-link"><i class="bi bi-arrow-left" aria-hidden="true"></i> Volver al detalle</a><div class="section-kicker">EDITAR REGISTRO</div><h1>Editar película</h1><p>{{ $pelicula->titulo }}</p></div>
<div class="form-panel"><form method="POST" action="{{ route('peliculas.update', $pelicula) }}">@method('PUT') @include('peliculas.partials.form')</form></div>
@endsection
