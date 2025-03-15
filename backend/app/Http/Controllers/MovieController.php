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
    public function index()
    {
        $movies = Movie::with('movieGenre')->get();
        return response()->json($movies);
    }

    /**
     * Muestra una película específica.
     */
    public function show($id)
    {
        $movie = Movie::with('movieGenre')->findOrFail($id);
        return response()->json($movie);
    }

    /**
     * Almacena una nueva película.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'duration' => 'required|integer|min:1',
            'movie_genre_id' => 'required|exists:movie_genres,id',
            'release_date' => 'required|date',
            'poster' => 'nullable|image|max:2048',
            'trailer_url' => 'nullable|url'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->all();
        
        // Manejar la subida de la imagen del poster si existe
        if ($request->hasFile('poster')) {
            $posterPath = $request->file('poster')->store('posters', 'public');
            $data['poster'] = $posterPath;
        }

        $movie = Movie::create($data);
        
        return response()->json([
            'message' => 'Película creada correctamente',
            'movie' => $movie
        ], 201);
    }

    /**
     * Actualiza una película existente.
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'string|max:255',
            'description' => 'string',
            'duration' => 'integer|min:1',
            'movie_genre_id' => 'exists:movie_genres,id',
            'release_date' => 'date',
            'poster' => 'nullable|image|max:2048',
            'trailer_url' => 'nullable|url'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $movie = Movie::findOrFail($id);
        $data = $request->except('poster');
        
        // Manejar la subida de la imagen del poster si existe
        if ($request->hasFile('poster')) {
            // Eliminar la imagen anterior si existe
            if ($movie->poster && Storage::disk('public')->exists($movie->poster)) {
                Storage::disk('public')->delete($movie->poster);
            }
            
            $posterPath = $request->file('poster')->store('posters', 'public');
            $data['poster'] = $posterPath;
        }

        $movie->update($data);
        
        return response()->json([
            'message' => 'Película actualizada correctamente',
            'movie' => $movie
        ]);
    }

    /**
     * Elimina una película.
     */
    public function destroy($id)
    {
        $movie = Movie::findOrFail($id);
        
        // Verificar si tiene sesiones asociadas
        if ($movie->screenings()->count() > 0) {
            return response()->json([
                'message' => 'No se puede eliminar la película porque tiene sesiones programadas'
            ], 400);
        }
        
        // Eliminar la imagen del poster si existe
        if ($movie->poster && Storage::disk('public')->exists($movie->poster)) {
            Storage::disk('public')->delete($movie->poster);
        }
        
        $movie->delete();
        
        return response()->json([
            'message' => 'Película eliminada correctamente'
        ]);
    }
    
    /**
     * Obtiene las películas en cartelera (con sesiones futuras)
     */
    public function getCurrentMovies()
    {
        // Obtener todas las películas sin filtro de proyecciones
        $movies = Movie::with('movieGenre')->get();
        
        return response()->json($movies);
    }
}