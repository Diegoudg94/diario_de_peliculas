@extends('layouts.app')
@section('title', 'Nueva película')
@section('content')
<div class="page-header"><a href="{{ route('peliculas.index') }}" class="back-link"><i class="bi bi-arrow-left" aria-hidden="true"></i> Volver a mi colección</a><div class="section-kicker">NUEVO REGISTRO</div><h1>Agrega una película</h1><p>Una historia más para recordar.</p></div>
<div class="form-panel"><form method="POST" action="{{ route('peliculas.store') }}">@include('peliculas.partials.form')</form></div>
@endsection
