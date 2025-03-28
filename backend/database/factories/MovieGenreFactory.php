<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\MovieGenre;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MovieGenre>
 */
class MovieGenreFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = MovieGenre::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        static $sequence = 1;
        $genreName = fake()->randomElement([
            'Action', 'Adventure', 'Animation', 'Comedy', 'Crime', 
            'Documentary', 'Drama', 'Family', 'Fantasy', 'Horror', 
            'Mystery', 'Romance', 'Sci-Fi', 'Thriller', 'War'
        ]);
        
        return [
            'name' => $genreName . '-' . $sequence++
        ];
    }
}
