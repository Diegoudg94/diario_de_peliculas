<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pelicula extends Model
{
    protected $table = 'peliculas';

    protected $fillable = [
        'titulo', 'anio_estreno', 'genero', 'director', 'fecha_vista',
        'calificacion', 'comentario', 'portada_url',
    ];

    protected function casts(): array
    {
        return ['fecha_vista' => 'date', 'calificacion' => 'integer'];
    }
}
