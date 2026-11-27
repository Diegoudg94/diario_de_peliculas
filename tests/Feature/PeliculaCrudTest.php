<?php

namespace Tests\Feature;

use App\Models\Pelicula;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PeliculaCrudTest extends TestCase
{
    use RefreshDatabase;

    private function datos(array $cambios = []): array
    {
        return array_merge([
            'titulo' => 'Prueba de película', 'anio_estreno' => 2020,
            'genero' => 'Drama', 'director' => 'Directora Ejemplo',
            'fecha_vista' => now()->toDateString(), 'calificacion' => 8,
            'comentario' => 'Una reseña personal de prueba.',
            'portada_url' => 'https://example.com/poster.jpg',
        ], $cambios);
    }

    public function test_crud_completo_y_persistencia(): void
    {
        $this->get('/peliculas')->assertOk()->assertSee('Tu diario comienza aquí');
        $this->get('/peliculas/create')->assertOk();

        $respuesta = $this->post('/peliculas', $this->datos());
        $pelicula = Pelicula::firstOrFail();
        $respuesta->assertRedirect(route('peliculas.show', $pelicula));
        $this->assertDatabaseHas('peliculas', ['titulo' => 'Prueba de película']);
        $this->get(route('peliculas.show', $pelicula))->assertOk()->assertSee('Una reseña personal de prueba.');
        $this->get(route('peliculas.edit', $pelicula))->assertOk();

        $this->put(route('peliculas.update', $pelicula), $this->datos(['titulo' => 'Título editado', 'calificacion' => 9]))
            ->assertRedirect(route('peliculas.show', $pelicula));
        $this->assertDatabaseHas('peliculas', ['id' => $pelicula->id, 'titulo' => 'Título editado', 'calificacion' => 9]);

        $this->delete(route('peliculas.destroy', $pelicula))->assertRedirect(route('peliculas.index'));
        $this->assertDatabaseCount('peliculas', 0);
    }

    public function test_validacion_del_servidor(): void
    {
        $this->post('/peliculas', $this->datos([
            'titulo' => '', 'anio_estreno' => 1800, 'genero' => 'Inválido',
            'fecha_vista' => now()->addDay()->toDateString(),
            'calificacion' => 11, 'portada_url' => 'javascript:alert(1)',
        ]))->assertSessionHasErrors(['titulo', 'anio_estreno', 'genero', 'fecha_vista', 'calificacion', 'portada_url']);
        $this->assertDatabaseCount('peliculas', 0);
    }

    public function test_busqueda_filtros_orden_y_estadisticas(): void
    {
        Pelicula::create($this->datos(['titulo' => 'Alfa', 'genero' => 'Drama', 'calificacion' => 10]));
        Pelicula::create($this->datos(['titulo' => 'Beta', 'genero' => 'Comedia', 'calificacion' => 6, 'fecha_vista' => now()->subMonth()->toDateString()]));

        $this->get('/peliculas')->assertOk()->assertSee('8.0')->assertSee('Alfa')->assertSee('Beta');
        $this->get('/peliculas?buscar=Alfa')->assertOk()->assertSee('1 resultado')->assertSee('Editar Alfa')->assertDontSee('Editar Beta');
        $this->get('/peliculas?genero=Comedia')->assertOk()->assertSee('1 resultado')->assertSee('Editar Beta')->assertDontSee('Editar Alfa');
        $this->get('/peliculas?orden=mejor')->assertOk()->assertSeeInOrder(['Alfa', 'Beta']);
    }

    public function test_enlaces_externos_codificados_y_comentario_escapado(): void
    {
        $pelicula = Pelicula::create($this->datos(['titulo' => 'Amor & cine', 'comentario' => '<script>alert(1)</script>']));
        $this->get(route('peliculas.show', $pelicula))->assertOk()
            ->assertSee('Amor%20%26%20cine', false)
            ->assertSee('https://www.imdb.com/find/', false)
            ->assertSee('https://www.youtube.com/results', false)
            ->assertSee('https://www.justwatch.com/mx/buscar', false)
            ->assertSee('rel="noopener noreferrer"', false)
            ->assertDontSee('<script>alert(1)</script>', false);
    }

    public function test_ruta_de_salud_y_portada_predeterminada(): void
    {
        $this->get('/up')->assertOk();
        $pelicula = Pelicula::create($this->datos(['portada_url' => null, 'director' => null]));
        $this->get(route('peliculas.show', $pelicula))->assertOk()
            ->assertSee('images/poster-placeholder.svg')
            ->assertDontSee('Dir. Directora Ejemplo');
    }
}
