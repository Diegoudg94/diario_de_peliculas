<?php

use App\Http\Controllers\PeliculaController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/peliculas');
Route::resource('peliculas', PeliculaController::class);
