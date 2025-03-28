<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Role;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Role>
 */
class RoleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Role::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->word()
        ];
    }

    /**
     * Indicate that the role is admin.
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'admin',
        ]);
    }

    /**
     * Indicate that the role is user.
     */
    public function user(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'user',
        ]);
    }
}
