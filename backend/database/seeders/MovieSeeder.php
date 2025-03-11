<?php

namespace Database\Seeders;

use App\Models\Movie;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MovieSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Limpiar la tabla antes de insertar nuevos registros
        DB::table('movies')->truncate();
        
        // Lista de películas para el seeder
        $movies = [
            [
                'title' => 'El Padrino',
                'description' => 'La historia de la familia Corleone, una de las más poderosas mafias de Nueva York en 1945.',
                'duration' => 175,
                'movie_genre' => 'Drama, Crimen',
                'release_date' => '1972-03-24',
                'image' => 'https://image.tmdb.org/t/p/w500/rPdtLWNsZmAtoZl9PK7S2wE3qiS.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Pulp Fiction',
                'description' => 'Las vidas de dos mafiosos, un boxeador, la esposa de un gánster y un par de bandidos se entrelazan en una historia de violencia y redención.',
                'duration' => 154,
                'movie_genre' => 'Crimen, Drama',
                'release_date' => '1994-10-14',
                'image' => 'https://image.tmdb.org/t/p/w500/d5iIlFn5s0ImszYzBPb8JPIfbXD.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Interestelar',
                'description' => 'Un equipo de exploradores viaja a través de un agujero de gusano en el espacio en un intento de asegurar la supervivencia de la humanidad.',
                'duration' => 169,
                'movie_genre' => 'Aventura, Drama, Ciencia ficción',
                'release_date' => '2014-11-07',
                'image' => 'https://image.tmdb.org/t/p/w500/gEU2QniE6E77NI6lCU6MxlNBvIx.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'El Caballero de la Noche',
                'description' => 'Batman se enfrenta al Joker, un criminal que busca sumir a Ciudad Gótica en la anarquía.',
                'duration' => 152,
                'movie_genre' => 'Acción, Crimen, Drama',
                'release_date' => '2008-07-18',
                'image' => 'https://image.tmdb.org/t/p/w500/8QDQExnfNFOtabLDKqfDQuHDsIg.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'La La Land',
                'description' => 'Un pianista de jazz y una aspirante a actriz se enamoran mientras persiguen sus sueños en Los Ángeles.',
                'duration' => 128,
                'movie_genre' => 'Comedia, Drama, Romance',
                'release_date' => '2016-12-09',
                'image' => 'https://image.tmdb.org/t/p/w500/uDO8zWDhfWwoFdKS4fzkUJt0Rf0.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'El Rey León',
                'description' => 'Un joven príncipe león huye de su reino solo para aprender el verdadero significado de la responsabilidad y la valentía.',
                'duration' => 88,
                'movie_genre' => 'Animación, Aventura, Drama',
                'release_date' => '1994-06-24',
                'image' => 'https://image.tmdb.org/t/p/w500/sKCr78MXSLixwmZ8DyJLrpMsd15.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Matrix',
                'description' => 'Un hacker descubre la verdadera naturaleza de su realidad y su papel en la guerra contra sus controladores.',
                'duration' => 136,
                'movie_genre' => 'Acción, Ciencia ficción',
                'release_date' => '1999-03-31',
                'image' => 'https://image.tmdb.org/t/p/w500/f89U3ADr1oiB1s9GkdPOEpXUk5H.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Parásitos',
                'description' => 'La familia Kim, que vive en la pobreza, desarrolla un interés peculiar en la adinerada familia Park, hasta que un incidente desencadena una serie de eventos inesperados.',
                'duration' => 132,
                'movie_genre' => 'Comedia, Drama, Suspense',
                'release_date' => '2019-05-30',
                'image' => 'https://image.tmdb.org/t/p/w500/7IiTTgloJzvGI1TAYymCfbfl3vT.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'El Señor de los Anillos: El Retorno del Rey',
                'description' => 'Gandalf y Aragorn lideran el mundo de los hombres contra la armada de Sauron para distraer su atención de Frodo y Sam, quienes se aproximan al Monte del Destino con el Anillo Único.',
                'duration' => 201,
                'movie_genre' => 'Aventura, Fantasía, Acción',
                'release_date' => '2003-12-17',
                'image' => 'https://image.tmdb.org/t/p/w500/mWuFbQrXyLk2kMBKF9TUPtDwuPx.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'El Club de la Lucha',
                'description' => 'Un oficinista insomne y un vendedor de jabón forman un club de lucha clandestino que evoluciona en algo mucho más peligroso.',
                'duration' => 139,
                'movie_genre' => 'Drama',
                'release_date' => '1999-10-15',
                'image' => 'https://image.tmdb.org/t/p/w500/pB8BM7pdSp6B6Ih7QZ4DrQ3PmJK.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];
        
        // Insertar los datos en la tabla
        DB::table('movies')->insert($movies);
    }
}
