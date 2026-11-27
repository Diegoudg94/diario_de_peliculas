<?php

namespace Database\Seeders;

use App\Models\Pelicula;
use Illuminate\Database\Seeder;

class PeliculaSeeder extends Seeder
{
    public function run(): void
    {
        $ejemplos = [
            ['titulo' => 'El viaje de Chihiro', 'anio_estreno' => 2001, 'genero' => 'Animación', 'director' => 'Hayao Miyazaki', 'fecha_vista' => '2026-11-08', 'calificacion' => 10, 'comentario' => 'Un viaje imaginativo que siempre ofrece algo nuevo.'],
            ['titulo' => 'Interestelar', 'anio_estreno' => 2014, 'genero' => 'Ciencia ficción', 'director' => 'Christopher Nolan', 'fecha_vista' => '2026-10-12', 'calificacion' => 9, 'comentario' => 'La escala visual y la música hacen memorable la historia.'],
            ['titulo' => 'Roma', 'anio_estreno' => 2018, 'genero' => 'Drama', 'director' => 'Alfonso Cuarón', 'fecha_vista' => '2026-09-20', 'calificacion' => 9, 'comentario' => 'Una mirada íntima y conmovedora de la vida cotidiana.'],
            ['titulo' => 'Coco', 'anio_estreno' => 2017, 'genero' => 'Animación', 'director' => 'Lee Unkrich', 'fecha_vista' => '2026-11-14', 'calificacion' => 8, 'comentario' => 'Colorida y emotiva; una celebración de la memoria familiar.'],
            ['titulo' => 'Parásitos', 'anio_estreno' => 2019, 'genero' => 'Suspenso', 'director' => 'Bong Joon-ho', 'fecha_vista' => '2026-08-05', 'calificacion' => 10, 'comentario' => 'Tensión e ingenio en cada escena.'],
            ['titulo' => 'La La Land', 'anio_estreno' => 2016, 'genero' => 'Romance', 'director' => 'Damien Chazelle', 'fecha_vista' => '2026-07-21', 'calificacion' => 7, 'comentario' => 'Una historia musical luminosa con un final agridulce.'],
        ];

        foreach ($ejemplos as $ejemplo) {
            Pelicula::firstOrCreate(
                ['titulo' => $ejemplo['titulo'], 'anio_estreno' => $ejemplo['anio_estreno']],
                $ejemplo
            );
        }
    }
}
