<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0b1220">
    <title>@yield('title', 'CineDiario') · CineDiario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
    <a class="visually-hidden-focusable skip-link" href="#contenido">Saltar al contenido</a>
    <nav class="navbar navbar-expand-sm navbar-dark cine-nav">
        <div class="container">
            <a class="navbar-brand brand" href="{{ route('peliculas.index') }}" aria-label="CineDiario, ir al inicio"><span class="brand-mark"><i class="bi bi-film" aria-hidden="true"></i></span> Cine<span>Diario</span></a>
            <div class="ms-auto d-flex align-items-center gap-2">
                <a class="nav-link text-light d-none d-sm-inline me-3" href="{{ route('peliculas.index') }}">Mi colección</a>
                <a class="btn btn-gold" href="{{ route('peliculas.create') }}"><i class="bi bi-plus-lg" aria-hidden="true"></i> <span>Agregar película</span></a>
            </div>
        </div>
    </nav>

    <main id="contenido" class="container main-content">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="status">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar mensaje"></button></div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger" role="alert"><strong>Revisa el formulario.</strong> Hay campos que necesitan corrección.</div>
        @endif
        @yield('content')
    </main>

    <footer class="site-footer mt-auto">
        <div class="container py-4">
            <div class="row gy-3 align-items-start">
                <div class="col-md-6"><div class="footer-brand"><i class="bi bi-film" aria-hidden="true"></i> CineDiario</div><p class="mb-0">Proyecto final · Licenciatura en Diseño de Sistemas Web<br>Conceptualización de Servicios en Nube</p></div>
                <div class="col-md-6"><div class="footer-title">Servicios en la nube</div><div class="d-flex flex-wrap gap-3 footer-links">
                    <a href="https://github.com/" target="_blank" rel="noopener noreferrer">GitHub <i class="bi bi-box-arrow-up-right" aria-hidden="true"></i></a>
                    <a href="https://railway.com/" target="_blank" rel="noopener noreferrer">Railway <i class="bi bi-box-arrow-up-right" aria-hidden="true"></i></a>
                    <a href="https://www.imdb.com/" target="_blank" rel="noopener noreferrer">IMDb <i class="bi bi-box-arrow-up-right" aria-hidden="true"></i></a>
                    <a href="https://www.youtube.com/" target="_blank" rel="noopener noreferrer">YouTube <i class="bi bi-box-arrow-up-right" aria-hidden="true"></i></a>
                    <a href="https://www.justwatch.com/mx" target="_blank" rel="noopener noreferrer">JustWatch <i class="bi bi-box-arrow-up-right" aria-hidden="true"></i></a>
                </div></div>
            </div>
        </div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/app.js') }}" defer></script>
</body>
</html>
