<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MovieSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('movies')->insert([
            // 1. Dune: Parte Dos
            [
                'title' => 'Dune: Parte Dos',
                'director' => 'Denis Villeneuve',
                'actors' => 'Timothée Chalamet, Zendaya, Rebecca Ferguson',
                'description' => 'Continuación de la épica adaptación de "Dune", en la que Paul Atreides se une a la rebelión de los Fremen para vengar a su familia y desafiar el destino en el árido planeta Arrakis.',
                'trailer' => 'https://www.youtube.com/embed/cvqiYcjKzqs',
                'duration' => 166,
                'movie_genre_id' => 1, // Ciencia Ficción
                'release_date' => '2024-03-01',
                'image' => '/img/dune_peli.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // 2. Godzilla y Kong: El Nuevo Imperio
            [
                'title' => 'Godzilla y Kong: El Nuevo Imperio',
                'director' => 'Adam Wingard',
                'actors' => 'Alexander Skarsgård, Millie Bobby Brown, Rebecca Hall',
                'description' => 'La batalla colosal entre Godzilla y Kong continúa en una aventura épica donde ambos titanes deben enfrentarse a una amenaza común y descubrir nuevas alianzas en medio de la destrucción.',
                'trailer' => 'https://www.youtube.com/embed/odM92ap8_c0',
                'duration' => 115,
                'movie_genre_id' => 2, // Acción
                'release_date' => '2023-06-01',
                'image' => '/img/godzilla_peli.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // 3. Kung Fu Panda 4
            [
                'title' => 'Kung Fu Panda 4',
                'director' => 'Jennifer Yuh Nelson',
                'actors' => 'Jack Black, Angelina Jolie, Dustin Hoffman',
                'description' => 'Po regresa en una nueva aventura en la que debe entrenar a una generación emergente de guerreros y enfrentar desafíos que ponen a prueba su valor y habilidades.',
                'trailer' => 'https://www.youtube.com/embed/yD8rNMiaX-w',
                'duration' => 94,
                'movie_genre_id' => 3, // Aventura
                'release_date' => '2023-08-01',
                'image' => '/img/kungfu_peli.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // 4. Furiosa: De la Saga Mad Max
            [
                'title' => 'Furiosa: De la Saga Mad Max',
                'director' => 'George Miller',
                'actors' => 'Charlize Theron, Tom Hardy',
                'description' => 'En un mundo post-apocalíptico, Furiosa lidera una rebelión en una implacable búsqueda de justicia y redención, marcando un hito en la legendaria saga de Mad Max.',
                'trailer' => 'https://www.youtube.com/embed/BUvtEhcuavU',
                'duration' => 150,
                'movie_genre_id' => 2, // Acción
                'release_date' => '2023-05-01',
                'image' => '/img/furiosa_peli.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // 5. Inside Out 2
            [
                'title' => 'Inside Out 2',
                'director' => 'Pete Docter',
                'actors' => 'Amy Poehler, Phyllis Smith, Bill Hader',
                'description' => 'La segunda entrega de Inside Out explora el mundo emocional de una niña en crecimiento, enfrentando nuevos retos y descubriendo la importancia de cada sentimiento en su vida.',
                'trailer' => 'https://www.youtube.com/embed/ahogVfXzqs4',
                'duration' => 105,
                'movie_genre_id' => 3, // Aventura
                'release_date' => '2024-01-01',
                'image' => '/img/inside_peli.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // 6. Deadpool & Wolverine
            [
                'title' => 'Deadpool & Wolverine',
                'director' => 'David Leitch',
                'actors' => 'Ryan Reynolds, Hugh Jackman',
                'description' => 'Una unión explosiva entre dos anti-héroes, donde el humor irreverente de Deadpool se mezcla con la ferocidad de Wolverine en una aventura llena de acción y sarcasmo.',
                'trailer' => 'https://www.youtube.com/embed/uDzfa0w86VwI',
                'duration' => 128,
                'movie_genre_id' => 2, // Acción
                'release_date' => '2023-10-01',
                'image' => '/img/deadpool_peli.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // 7. Mufasa: El Rey León
            [
                'title' => 'Mufasa: El Rey León',
                'director' => 'Jon Favreau',
                'actors' => 'Donald Glover, Beyoncé, James Earl Jones',
                'description' => 'Una emocionante reinvención del clásico "El Rey León", en la que la majestuosa figura de Mufasa se enfrenta a nuevos desafíos en una epopeya de legado, coraje y emociones intensas.',
                'trailer' => 'https://www.youtube.com/embed/o17MF9vnabg',
                'duration' => 118,
                'movie_genre_id' => 3, // Aventura
                'release_date' => '2023-03-01',
                'image' => '/img/mufasa_peli.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // 8. EL CABALLERO OSCURO
            [
                'title' => 'El Caballero Oscuro',
                'director' => 'Christopher Nolan',
                'actors' => 'Christian Bale, Heath Ledger',
                'description' => 'Una intensa reimaginación del icónico caballero enmascarado, en la que Batman lucha contra la corrupción y el crimen para salvar una Gotham al borde del caos.',
                'trailer' => 'https://www.youtube.com/embed/e0qwi-4iOpE',
                'duration' => 152,
                'movie_genre_id' => 2, // Acción
                'release_date' => '2023-02-01',
                'image' => '/img/batman_peli.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // 9. INTERSTELLAR
            [
                'title' => 'Interstellar',
                'director' => 'Christopher Nolan',
                'actors' => 'Matthew McConaughey, Anne Hathaway, Jessica Chastain',
                'description' => 'Un viaje épico a través del espacio y el tiempo en el que un grupo de astronautas se aventura más allá de nuestro sistema solar en busca de un nuevo hogar para la humanidad.',
                'trailer' => 'https://www.youtube.com/embed/UoSSbmD9vqc',
                'duration' => 169,
                'movie_genre_id' => 1, // Ciencia Ficción
                'release_date' => '2014-11-07',
                'image' => '/img/interestelar_peli.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // 10. GLADIATOR
            [
                'title' => 'Gladiator',
                'director' => 'Ridley Scott',
                'actors' => 'Russell Crowe, Joaquin Phoenix',
                'description' => 'Una epopeya histórica que narra la lucha de un general romano caído en desgracia, convertido en gladiador, en su camino hacia la venganza y la redención en el Coliseo.',
                'trailer' => 'https://www.youtube.com/embed/HCjuv9STNps',
                'duration' => 155,
                'movie_genre_id' => 2, // Acción
                'release_date' => '2000-05-05',
                'image' => '/img/gladiator_peli.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // 11. BEAUTY AND THE BEAST
            [
                'title' => 'Beauty and the Beast',
                'director' => 'Bill Condon',
                'actors' => 'Emma Watson, Dan Stevens',
                'description' => 'Una encantadora adaptación del clásico cuento, donde la belleza y la bestia descubren que el amor y la comprensión pueden transformar incluso las almas más endurecidas.',
                'trailer' => 'https://www.youtube.com/embed/pnkgw6pAKkM',
                'duration' => 129,
                'movie_genre_id' => 5, // Drama
                'release_date' => '2023-11-01',
                'image' => '/img/bellaybestia_peli.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // 12. MICKEY 17
            [
                'title' => 'Mickey 17',
                'director' => 'Bong Joon Ho',
                'actors' => 'Robert Pattinson, Zoë Kravitz',
                'description' => 'Una intrigante historia de ciencia ficción que sigue a un enigmático personaje a través de universos paralelos y realidades alternativas llenas de misterios y descubrimientos.',
                'trailer' => 'https://www.youtube.com/embed/osYpGSz_0i4',
                'duration' => 166,
                'movie_genre_id' => 1, // Ciencia Ficción
                'release_date' => '2023-01-01',
                'image' => '/img/mickey_peli.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // 13. ANORA
            [
                'title' => 'Anora',
                'director' => 'Greta Gerwig',
                'actors' => 'Saoirse Ronan, Timothée Chalamet',
                'description' => 'Un drama intenso y cautivador que explora las complejidades de las relaciones humanas, destacando actuaciones sobresalientes y una narrativa profundamente emotiva.',
                'trailer' => 'https://www.youtube.com/embed/xV7jnVhBD2E',
                'duration' => 125,
                'movie_genre_id' => 5, // Drama
                'release_date' => '2023-06-15',
                'image' => '/img/anora_peli.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // 14. PADDINGTON: AVENTURA EN LA SELVA
            [
                'title' => 'Paddington: Aventura en la Selva',
                'director' => 'Paul King',
                'actors' => 'Ben Whishaw, Hugh Bonneville, Nicole Kidman',
                'description' => 'Una divertida y aventurera historia familiar en la que Paddington se embarca en emocionantes peripecias en una selva llena de descubrimientos, amistad y risas contagiosas.',
                'trailer' => 'https://www.youtube.com/embed/NTvudSGfHRI',
                'duration' => 95,
                'movie_genre_id' => 4, // Comedia
                'release_date' => '2023-08-01',
                'image' => '/img/paddington_peli.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
