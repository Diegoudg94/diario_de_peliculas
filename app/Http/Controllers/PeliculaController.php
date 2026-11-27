<?php

namespace App\Http\Controllers;

use App\Models\Pelicula;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Validation\Rule;

class PeliculaController extends Controller
{
    public const GENEROS = ['Acción', 'Animación', 'Aventura', 'Ciencia ficción', 'Comedia', 'Documental', 'Drama', 'Fantasía', 'Romance', 'Suspenso', 'Terror', 'Otro'];

    public function index(Request $request): View
    {
        $filtros = $request->validate([
            'buscar' => ['nullable', 'string', 'max:100'],
            'genero' => ['nullable', Rule::in(self::GENEROS)],
            'orden' => ['nullable', Rule::in(['recientes', 'antiguas', 'mejor', 'menor'])],
        ]);

        $query = Pelicula::query();
        if (!empty($filtros['buscar'])) {
            $query->where('titulo', 'like', '%'.$filtros['buscar'].'%');
        }
        if (!empty($filtros['genero'])) {
            $query->where('genero', $filtros['genero']);
        }
        match ($filtros['orden'] ?? 'recientes') {
            'antiguas' => $query->orderBy('fecha_vista')->orderBy('id'),
            'mejor' => $query->orderByDesc('calificacion')->orderByDesc('fecha_vista')->orderByDesc('id'),
            'menor' => $query->orderBy('calificacion')->orderByDesc('fecha_vista')->orderByDesc('id'),
            default => $query->orderByDesc('fecha_vista')->orderByDesc('id'),
        };

        $inicioMes = now()->startOfMonth()->toDateString();
        $finMes = now()->endOfMonth()->toDateString();

        return view('peliculas.index', [
            'peliculas' => $query->paginate(9)->withQueryString(),
            'total' => Pelicula::count(),
            'promedio' => Pelicula::avg('calificacion'),
            'mejor' => Pelicula::orderByDesc('calificacion')->orderByDesc('fecha_vista')->first(),
            'delMes' => Pelicula::whereBetween('fecha_vista', [$inicioMes, $finMes])->count(),
            'generos' => self::GENEROS,
            'filtros' => $filtros,
        ]);
    }

    public function create(): View
    {
        return view('peliculas.create', ['generos' => self::GENEROS]);
    }

    public function store(Request $request): RedirectResponse
    {
        $pelicula = Pelicula::create($this->datosValidados($request));
        return redirect()->route('peliculas.show', $pelicula)->with('success', 'Película registrada correctamente.');
    }

    public function show(Pelicula $pelicula): View
    {
        return view('peliculas.show', compact('pelicula'));
    }

    public function edit(Pelicula $pelicula): View
    {
        return view('peliculas.edit', ['pelicula' => $pelicula, 'generos' => self::GENEROS]);
    }

    public function update(Request $request, Pelicula $pelicula): RedirectResponse
    {
        $pelicula->update($this->datosValidados($request));
        return redirect()->route('peliculas.show', $pelicula)->with('success', 'Película actualizada correctamente.');
    }

    public function destroy(Pelicula $pelicula): RedirectResponse
    {
        $pelicula->delete();
        return redirect()->route('peliculas.index')->with('success', 'Película eliminada correctamente.');
    }

    private function datosValidados(Request $request): array
    {
        return $request->validate([
            'titulo' => ['required', 'string', 'max:150'],
            'anio_estreno' => ['required', 'integer', 'between:1888,'.(now()->year + 5)],
            'genero' => ['required', Rule::in(self::GENEROS)],
            'director' => ['nullable', 'string', 'max:120'],
            'fecha_vista' => ['required', 'date', 'before_or_equal:today'],
            'calificacion' => ['required', 'integer', 'between:1,10'],
            'comentario' => ['required', 'string', 'max:3000'],
            'portada_url' => ['nullable', 'url:http,https', 'max:2048'],
        ], [
            'required' => 'Este campo es obligatorio.',
            'anio_estreno.between' => 'Introduce un año de estreno válido.',
            'calificacion.between' => 'La calificación debe estar entre 1 y 10.',
            'fecha_vista.before_or_equal' => 'La fecha no puede ser futura.',
            'portada_url.url' => 'Usa una URL válida que comience con http o https.',
        ]);
    }
}
