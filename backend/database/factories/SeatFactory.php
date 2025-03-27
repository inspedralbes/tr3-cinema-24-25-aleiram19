<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Seat;
use App\Models\Auditorium;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Seat>
 */
class SeatFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Seat::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $row = fake()->randomElement(['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H']);
        $number = fake()->numberBetween(1, 15);
        
        return [
            'auditorium_id' => Auditorium::factory(),
            'number' => $row . $number,
            'status' => 'available', // Por defecto: disponible
        ];
    }
    
    /**
     * Indicar que el asiento está ocupado
     */
    public function occupied()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'busy',
            ];
        });
    }
    
    /**
     * Indicar que el asiento está reservado (que será 'busy' en la base de datos)
     */
    public function reserved()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'busy',
            ];
        });
    }
}
