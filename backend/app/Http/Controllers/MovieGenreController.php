<?php

namespace App\Http\Controllers;

use App\Models\MovieGenre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MovieGenreController extends Controller
{
    /**
     * Muestra un listado de todos los géneros de películas.
     */
    public function index()
    {
        $genres = MovieGenre::all();
        return response()->json($genres);
    }

    /**
     * Muestra un género específico.
     */
    public function show($id)
    {
        $genre = MovieGenre::findOrFail($id);
        return response()->json($genre);
    }

    /**
     * Almacena un nuevo género.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:movie_genres,name'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $genre = MovieGenre::create([
            'name' => $request->name
        ]);
        
        return response()->json([
            'message' => 'Género creado correctamente',
            'genre' => $genre
        ], 201);
    }

    /**
     * Actualiza un género existente.
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:movie_genres,name,'.$id
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $genre = MovieGenre::findOrFail($id);
        $genre->update([
            'name' => $request->name
        ]);
        
        return response()->json([
            'message' => 'Género actualizado correctamente',
            'genre' => $genre
        ]);
    }

    /**
     * Elimina un género.
     */
    public function destroy($id)
    {
        $genre = MovieGenre::findOrFail($id);
        
        // Verificar si hay películas asociadas a este género
        if ($genre->movies()->count() > 0) {
            return response()->json([
                'message' => 'No se puede eliminar el género porque tiene películas asociadas'
            ], 400);
        }
        
        $genre->delete();
        
        return response()->json([
            'message' => 'Género eliminado correctamente'
        ]);
    }
    
    /**
     * Obtiene las películas de un género específico.
     */
    public function getMovies($id)
    {
        $genre = MovieGenre::findOrFail($id);
        $movies = $genre->movies;
        
        return response()->json($movies);
    }
}