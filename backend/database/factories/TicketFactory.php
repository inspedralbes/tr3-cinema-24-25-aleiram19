<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Screening;
use App\Models\Seat;
use App\Models\Snack;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ticket>
 */
class TicketFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Ticket::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'screening_id' => Screening::factory(),
            'seat_id' => Seat::factory(),
            'snack_id' => null, // Opcional
            'snack_quantity' => 0,
            'quantity' => 1,
            'total_pay' => fake()->randomFloat(2, 4, 20),
            'purchase_date' => fake()->dateTimeBetween('-1 month', 'now'),
            'confirmation_code' => strtoupper(fake()->lexify('????-????-????'))
        ];
    }
    
    /**
     * Incluir un snack en el ticket
     */
    public function withSnack()
    {
        return $this->state(function (array $attributes) {
            return [
                'snack_id' => Snack::factory(),
                'snack_quantity' => fake()->numberBetween(1, 3),
            ];
        });
    }
}
