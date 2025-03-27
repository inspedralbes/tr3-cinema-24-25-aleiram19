<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\Role;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            // No asignamos rol por defecto
            'role_id' => null,
        ];
    }
    
    /**
     * Configurar como usuario normal
     */
    public function user(): static
    {
        // Buscamos o creamos el rol de usuario normal
        $role = Role::where('name', 'user')->first();
        if (!$role) {
            $role = Role::create(['name' => 'user']);
        }
        
        return $this->state(fn (array $attributes) => [
            'role_id' => $role->id,
        ]);
    }
    
    /**
     * Configurar como administrador
     */
    public function admin(): static
    {
        // Buscamos o creamos el rol de administrador
        $role = Role::where('name', 'admin')->first();
        if (!$role) {
            $role = Role::create(['name' => 'admin']);
        }
        
        return $this->state(fn (array $attributes) => [
            'role_id' => $role->id,
        ]);
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
