<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Screening;
use App\Models\Movie;
use App\Models\Auditorium;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Screening>
 */
class ScreeningFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Screening::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'movie_id' => Movie::factory(),
            'auditorium_id' => Auditorium::factory(),
            'date_time' => fake()->dateTimeBetween('now', '+2 weeks'),
            'price' => fake()->randomFloat(2, 4, 12),
            'is_special' => fake()->boolean(20), // 20% de probabilidad de ser día especial
        ];
    }
}
