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
                'trailer' => 'https://www.youtube.com/embed/aSgOPHG8d5g',
                'duration' => 166,
                'movie_genre_id' => 1, // Ciencia Ficción
                'release_date' => '2024-03-01',
                'image' => 'dune_parte_dos.jpg',
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
                'image' => 'godzilla_kong.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // 3. Kung Fu Panda 4
            [
                'title' => 'Kung Fu Panda 4',
                'director' => 'Desconocido',
                'actors' => 'Desconocidos',
                'description' => 'Po regresa en una nueva aventura en la que debe entrenar a una generación emergente de guerreros y enfrentar desafíos que ponen a prueba su valor y habilidades.',
                'trailer' => 'https://www.youtube.com/embed/clx-c3hDMTg',
                'duration' => 94,
                'movie_genre_id' => 3, // Aventura
                'release_date' => '2023-08-01',
                'image' => 'kung_fu_panda4.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // 4. Ghostbusters: Apocalipsis Fantasma
            [
                'title' => 'Ghostbusters: Apocalipsis Fantasma',
                'director' => 'Desconocido',
                'actors' => 'Desconocidos',
                'description' => 'Los cazafantasmas se reúnen para enfrentar una amenaza sobrenatural que desata el caos en la ciudad, combinando humor y terror en una lucha épica contra lo paranormal.',
                'trailer' => 'https://www.youtube.com/embed/fcAUUb3WGrY',
                'duration' => 115,
                'movie_genre_id' => 4, // Comedia
                'release_date' => '2022-11-01',
                'image' => 'ghostbusters_apocalipsis.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // 5. Furiosa: De la Saga Mad Max
            [
                'title' => 'Furiosa: De la Saga Mad Max',
                'director' => 'George Miller',
                'actors' => 'Charlize Theron, Tom Hardy',
                'description' => 'En un mundo post-apocalíptico, Furiosa lidera una rebelión en una implacable búsqueda de justicia y redención, marcando un hito en la legendaria saga de Mad Max.',
                'trailer' => 'https://www.youtube.com/embed/GYyTdpR6HnQ',
                'duration' => 150,
                'movie_genre_id' => 2, // Acción
                'release_date' => '2023-05-01',
                'image' => 'furiosa.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // 6. Inside Out 2
            [
                'title' => 'Inside Out 2',
                'director' => 'Pete Docter',
                'actors' => 'Desconocidos',
                'description' => 'La segunda entrega de Inside Out explora el mundo emocional de una niña en crecimiento, enfrentando nuevos retos y descubriendo la importancia de cada sentimiento en su vida.',
                'trailer' => 'https://www.youtube.com/embed/VPC7iyA4Es0',
                'duration' => 105,
                'movie_genre_id' => 3, // Aventura
                'release_date' => '2024-01-01',
                'image' => 'inside_out2.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // 7. Mi Villano Favorito 4
            [
                'title' => 'Mi Villano Favorito 4',
                'director' => 'Desconocido',
                'actors' => 'Desconocidos',
                'description' => 'Gru y sus adorables secuaces se embarcan en una nueva aventura llena de humor y corazón, enfrentando desafíos inesperados en el mundo de los villanos.',
                'trailer' => 'https://www.youtube.com/embed/ejl0IxTXOe4',
                'duration' => 95,
                'movie_genre_id' => 4, // Comedia
                'release_date' => '2023-09-01',
                'image' => 'mi_villano_favorito4.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // 8. Alien: Romulus
            [
                'title' => 'Alien: Romulus',
                'director' => 'Desconocido',
                'actors' => 'Desconocidos',
                'description' => 'Una nueva entrega en el icónico universo de Alien, donde la tripulación de una nave espacial se enfrenta a horrores indescriptibles al explorar los confines misteriosos del espacio.',
                'trailer' => 'https://www.youtube.com/embed/yyLbSCpGaVw',
                'duration' => 120,
                'movie_genre_id' => 5, // Terror
                'release_date' => '2023-07-01',
                'image' => 'alien_romulus.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // 9. Deadpool & Wolverine
            [
                'title' => 'Deadpool & Wolverine',
                'director' => 'Desconocido',
                'actors' => 'Ryan Reynolds, Hugh Jackman',
                'description' => 'Una unión explosiva entre dos anti-héroes, donde el humor irreverente de Deadpool se mezcla con la ferocidad de Wolverine en una aventura llena de acción y sarcasmo.',
                'trailer' => 'https://www.youtube.com/embed/XTKYBdWDrTI',
                'duration' => 128,
                'movie_genre_id' => 2, // Acción
                'release_date' => '2023-10-01',
                'image' => 'deadpool_wolverine.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // 10. Joker: Folie à Deux
            [
                'title' => 'Joker: Folie à Deux',
                'director' => 'Todd Phillips',
                'actors' => 'Desconocidos',
                'description' => 'Una profunda exploración psicológica del caos en la mente del Joker, que ahonda en sus complejas relaciones y motivaciones a través de una narrativa intensa y perturbadora.',
                'trailer' => 'https://www.youtube.com/embed/a5JqIwRgZwI',
                'duration' => 135,
                'movie_genre_id' => 6, // Drama
                'release_date' => '2023-04-01',
                'image' => 'joker_folie.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // 11. Mufasa: El Rey León
            [
                'title' => 'Mufasa: El Rey León',
                'director' => 'Desconocido',
                'actors' => 'Desconocidos',
                'description' => 'Una emocionante reinvención del clásico "El Rey León", en la que la majestuosa figura de Mufasa se enfrenta a nuevos desafíos en una epopeya de legado, coraje y emociones intensas.',
                'trailer' => 'https://www.youtube.com/embed/Z_AvpI0-QMk',
                'duration' => 118,
                'movie_genre_id' => 3, // Aventura
                'release_date' => '2023-03-01',
                'image' => 'mufasa.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // 12. Oppenheimer
            [
                'title' => 'Oppenheimer',
                'director' => 'Christopher Nolan',
                'actors' => 'Cillian Murphy, Emily Blunt',
                'description' => 'Un drama histórico que examina la vida del científico detrás de la creación de la bomba atómica, explorando las implicaciones morales y personales de un descubrimiento devastador.',
                'trailer' => 'https://www.youtube.com/embed/uYPbbksJxIg',
                'duration' => 180,
                'movie_genre_id' => 6, // Drama
                'release_date' => '2023-12-01',
                'image' => 'oppenheimer.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // 13. EL CABALLERO OSCURO
            [
                'title' => 'El Caballero Oscuro',
                'director' => 'Christopher Nolan',
                'actors' => 'Christian Bale, Heath Ledger',
                'description' => 'Una intensa reimaginación del icónico caballero enmascarado, en la que Batman lucha contra la corrupción y el crimen para salvar una Gotham al borde del caos.',
                'trailer' => '',
                'duration' => 152,
                'movie_genre_id' => 2, // Acción
                'release_date' => '2023-02-01',
                'image' => '/img/img6.jpeg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // 14. INTERSTELLAR
            [
                'title' => 'Interstellar',
                'director' => 'Christopher Nolan',
                'actors' => 'Matthew McConaughey, Anne Hathaway, Jessica Chastain',
                'description' => 'Un viaje épico a través del espacio y el tiempo en el que un grupo de astronautas se aventura más allá de nuestro sistema solar en busca de un nuevo hogar para la humanidad.',
                'trailer' => '',
                'duration' => 169,
                'movie_genre_id' => 1, // Ciencia Ficción
                'release_date' => '2014-11-07',
                'image' => '/img/img7.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // 15. GLADIATOR
            [
                'title' => 'Gladiator',
                'director' => 'Ridley Scott',
                'actors' => 'Russell Crowe, Joaquin Phoenix',
                'description' => 'Una epopeya histórica que narra la lucha de un general romano caído en desgracia, convertido en gladiador, en su camino hacia la venganza y la redención en el Coliseo.',
                'trailer' => '',
                'duration' => 155,
                'movie_genre_id' => 2, // Acción
                'release_date' => '2000-05-05',
                'image' => '/img/img8.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // 16. BEAUTY AND THE BEAST
            [
                'title' => 'Beauty and the Beast',
                'director' => 'Bill Condon',
                'actors' => 'Emma Watson, Dan Stevens',
                'description' => 'Una encantadora adaptación del clásico cuento, donde la belleza y la bestia descubren que el amor y la comprensión pueden transformar incluso las almas más endurecidas.',
                'trailer' => '',
                'duration' => 129,
                'movie_genre_id' => 6, // Drama
                'release_date' => '2023-11-01',
                'image' => '/img/img9.jpeg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // 17. MICKEY 17
            [
                'title' => 'Mickey  17',
                'director' => 'Desconocido',
                'actors' => 'Desconocidos',
                'description' => 'Una intrigante historia de ciencia ficción que sigue a un enigmático personaje a través de universos paralelos y realidades alternativas llenas de misterios y descubrimientos.',
                'trailer' => '',
                'duration' => 166,
                'movie_genre_id' => 1, // Ciencia Ficción
                'release_date' => '2023-01-01',
                'image' => '/img/img5.jpeg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // 18. WOLFGANG
            [
                'title' => 'WolfGang',
                'director' => 'Desconocido',
                'actors' => 'Desconocidos',
                'description' => 'Una emotiva narrativa que combina drama y música, contando la historia de un individuo extraordinario cuya pasión y talento lo impulsan a superar grandes desafíos personales.',
                'trailer' => '',
                'duration' => 132,
                'movie_genre_id' => 6, // Drama
                'release_date' => '2023-07-15',
                'image' => '/img/img10.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // 19. ANORA
            [
                'title' => 'Anora',
                'director' => 'Desconocido',
                'actors' => 'Desconocidos',
                'description' => 'Un drama intenso y cautivador que explora las complejidades de las relaciones humanas, destacando actuaciones sobresalientes y una narrativa profundamente emotiva.',
                'trailer' => '',
                'duration' => 125,
                'movie_genre_id' => 6, // Drama
                'release_date' => '2023-06-15',
                'image' => '/img/img11.jpeg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // 20. PADDINGTON: AVENTURA EN LA SELVA
            [
                'title' => 'Paddington: Aventura en la Selva',
                'director' => 'Desconocido',
                'actors' => 'Desconocidos',
                'description' => 'Una divertida y aventurera historia familiar en la que Paddington se embarca en emocionantes peripecias en una selva llena de descubrimientos, amistad y risas contagiosas.',
                'trailer' => '',
                'duration' => 95,
                'movie_genre_id' => 4, // Comedia
                'release_date' => '2023-08-01',
                'image' => '/img/img12.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
