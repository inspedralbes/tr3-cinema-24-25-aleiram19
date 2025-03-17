<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\MovieGenre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class MovieController extends Controller
{
    /**
     * Muestra un listado de todas las películas.
     */
    public function index(Request $request)
    {
        $movies = Movie::with('movieGenre')->get();
        
        // Si la solicitud es de API o comienza con /api/, devolver JSON
        if ($request->expectsJson() || strpos($request->path(), 'api/') === 0) {
            return response()->json($movies);
        }
        
        // Si es una solicitud web, devolver vista
        return view('movies.index', compact('movies'));
    }

    /**
     * Muestra una película específica.
     */
    public function show(Request $request, $id)
    {
        $movie = Movie::with('movieGenre', 'screenings.auditorium')->findOrFail($id);
        
        // Si la solicitud es de API o comienza con /api/, devolver JSON
        if ($request->expectsJson() || strpos($request->path(), 'api/') === 0) {
            return response()->json($movie);
        }
        
        // Si es una solicitud web, devolver vista
        return view('movies.show', compact('movie'));
    }
    
    /**
     * Muestra el formulario para crear una nueva película (Solo Web)
     */
    public function create()
    {
        $genres = MovieGenre::all();
        return view('movies.create', compact('genres'));
    }

    /**
     * Almacena una nueva película.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'director' => 'required|string|max:255',
            'actors' => 'nullable|string',
            'description' => 'required|string',
            'trailer' => 'nullable|url',
            'duration' => 'required|integer|min:1',
            'movie_genre_id' => 'required|exists:movie_genres,id',
            'release_date' => 'required|date',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->all();
        
        // Manejar la subida de la imagen si existe
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('posters', 'public');
            $data['image'] = $imagePath;
        }

        $movie = Movie::create($data);
        
        if ($request->expectsJson() || strpos($request->path(), 'api/') === 0) {
            return response()->json([
                'message' => 'Película creada correctamente',
                'movie' => $movie
            ], 201);
        } else {
            return redirect()->route('movies.index')
                ->with('success', 'Película creada correctamente');
        }
    }
    
    /**
     * Muestra el formulario para editar una película (Solo Web)
     */
    public function edit($id)
    {
        $movie = Movie::findOrFail($id);
        $genres = MovieGenre::all();
        return view('movies.edit', compact('movie', 'genres'));
    }

    /**
     * Actualiza una película existente.
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'string|max:255',
            'director' => 'string|max:255',
            'actors' => 'nullable|string',
            'description' => 'string',
            'trailer' => 'nullable|url',
            'duration' => 'integer|min:1',
            'movie_genre_id' => 'exists:movie_genres,id',
            'release_date' => 'date',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson() || strpos($request->path(), 'api/') === 0) {
                return response()->json(['errors' => $validator->errors()], 422);
            } else {
                return redirect()->back()->withErrors($validator)->withInput();
            }
        }

        $movie = Movie::findOrFail($id);
        $data = $request->except('image');
        
        if ($request->hasFile('image')) {
            if ($movie->image && Storage::disk('public')->exists($movie->image)) {
                Storage::disk('public')->delete($movie->image);
            }
            
            $imagePath = $request->file('image')->store('posters', 'public');
            $data['image'] = $imagePath;
        }

        $movie->update($data);
        
        if ($request->expectsJson() || strpos($request->path(), 'api/') === 0) {
            return response()->json([
                'message' => 'Película actualizada correctamente',
                'movie' => $movie
            ]);
        } else {
            return redirect()->route('movies.index')
                ->with('success', 'Película actualizada correctamente');
        }
    }

    /**
     * Elimina una película.
     */
    public function destroy(Request $request, $id)
    {
        $movie = Movie::findOrFail($id);
        
        // Verificar si tiene sesiones asociadas
        if ($movie->screenings()->count() > 0) {
            if ($request->expectsJson() || strpos($request->path(), 'api/') === 0) {
                return response()->json([
                    'message' => 'No se puede eliminar la película porque tiene sesiones programadas'
                ], 400);
            } else {
                return redirect()->back()
                    ->with('error', 'No se puede eliminar la película porque tiene sesiones programadas');
            }
        }
        
        // Eliminar la imagen si existe
        if ($movie->image && Storage::disk('public')->exists($movie->image)) {
            Storage::disk('public')->delete($movie->image);
        }
        
        $movie->delete();
        
        if ($request->expectsJson() || strpos($request->path(), 'api/') === 0) {
            return response()->json([
                'message' => 'Película eliminada correctamente'
            ]);
        } else {
            return redirect()->route('movies.index')
                ->with('success', 'Película eliminada correctamente');
        }
    }
    
    /**
     * Obtiene las películas en cartelera (con sesiones futuras)
     */
    public function getCurrentMovies(Request $request)
    {
        try {
            // Obtener todas las películas sin filtro de proyecciones
            $movies = Movie::with('movieGenre')->get();
            
            if ($request->expectsJson() || strpos($request->path(), 'api/') === 0) {
                return response()->json($movies);
            } else {
                return view('movies.current', compact('movies'));
            }
        } catch (\Exception $e) {
            // Registrar el error para diagnóstico
            \Log::error('Error en getCurrentMovies: ' . $e->getMessage());
            
            if ($request->expectsJson() || strpos($request->path(), 'api/') === 0) {
                return response()->json(['error' => 'Error al obtener las películas en cartelera'], 500);
            } else {
                return back()->with('error', 'Error al cargar las películas en cartelera');
            }
        }
    }
    
    /**
     * Obtiene todas las películas en formato JSON para API
     */
    public function getMovies()
    {
        $movies = Movie::with('movieGenre')->get();
        
        // Formatear datos si es necesario
        $formatted = $movies->map(function($movie) {
            return [
                'id' => $movie->id,
                'title' => $movie->title,
                'director' => $movie->director,
                'actors' => $movie->actors,
                'description' => $movie->description,
                'trailer' => $movie->trailer,
                'duration' => $movie->duration,
                'image' => $movie->image,
                'release_date' => $movie->release_date,
                'genre' => $movie->movieGenre ? [
                    'id' => $movie->movieGenre->id,
                    'name' => $movie->movieGenre->name
                ] : null
            ];
        });
        
        return response()->json($formatted);
    }
}
