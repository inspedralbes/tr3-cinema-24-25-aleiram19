<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Movie;
use App\Models\MovieGenre;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Movie>
 */
class MovieFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Movie::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'director' => fake()->name(),
            'actors' => fake()->name() . ', ' . fake()->name() . ', ' . fake()->name(),
            'description' => fake()->paragraph(),
            'trailer' => 'https://www.youtube.com/watch?v=' . fake()->lexify('???????????'),
            'duration' => fake()->numberBetween(90, 180),
            'movie_genre_id' => MovieGenre::factory(),
            'release_date' => fake()->dateTimeBetween('-1 year', '+1 month')->format('Y-m-d'),
            'image' => 'movies/' . fake()->uuid() . '.jpg'
        ];
    }
}
