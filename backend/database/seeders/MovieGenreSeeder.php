<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MovieGenreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Extraer los géneros únicos del MovieSeeder
        $genres = [
            'Ciencia Ficción',
            'Acción',
            'Aventura',
            'Comedia',
            'Terror',
            'Drama',
            'Acción/Historia',
            'Fantasía/Romance',
            'Drama/Música',
            'Familiar/Aventura'
        ];

        // Insertar géneros únicos
        foreach ($genres as $genre) {
            DB::table('movie_genres')->insert([
                'name' => $genre,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Ya no es necesario actualizar las películas, ya que MovieSeeder ahora usará directamente los IDs de géneros
    }

    /**
     * [DEPRECADO] Este método ya no es necesario porque MovieSeeder ahora usa directamente los IDs de géneros
     * Se deja comentado como referencia por si es necesario en el futuro.
     */
    /*
    private function updateMoviesWithGenreIds(): void
    {
        // Obtener todas las películas
        $movies = DB::table('movies')->get();
        
        foreach ($movies as $movie) {
            // El valor original de movie_genre
            $genreName = $movie->movie_genre ?? '';
            
            // Buscar el ID del género correspondiente
            $genre = DB::table('movie_genres')
                ->where('name', $genreName)
                ->orWhere('name', 'like', $genreName . '%') // Para manejar géneros compuestos
                ->first();
            
            if ($genre) {
                // Actualizar la película con el ID del género
                DB::table('movies')
                    ->where('id', $movie->id)
                    ->update(['movie_genre_id' => $genre->id]);
            }
        }
    }
    */
}
