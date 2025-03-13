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
            [
                'title' => 'Dune: Part Two',
                'director' => 'Denis Villeneuve',
                'actors' => 'Timothée Chalamet, Zendaya, Rebecca Ferguson',
                'description' => 'Paul Atreides unites with Chani and the Fremen while seeking revenge against the conspirators who destroyed his family.',
                'duration' => 166,
                'movie_genre' => 'Science Fiction',
                'release_date' => '2024-03-01',
                'image' => 'dune.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'The Batman',
                'director' => 'Matt Reeves',
                'actors' => 'Robert Pattinson, Zoë Kravitz, Paul Dano',
                'description' => 'When a sadistic serial killer begins murdering key political figures in Gotham, Batman is forced to investigate the city\'s hidden corruption.',
                'duration' => 176,
                'movie_genre' => 'Action',
                'release_date' => '2022-03-04',
                'image' => 'batman.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Everything Everywhere All at Once',
                'director' => 'Daniel Kwan, Daniel Scheinert',
                'actors' => 'Michelle Yeoh, Stephanie Hsu, Ke Huy Quan',
                'description' => 'A middle-aged Chinese immigrant is swept up in an insane adventure where she alone can save existence by exploring other universes.',
                'duration' => 139,
                'movie_genre' => 'Adventure',
                'release_date' => '2022-03-25',
                'image' => 'everything.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
