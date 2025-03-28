<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\User;
use App\Models\MovieGenre;
use App\Models\Movie;
use App\Models\Auditorium;
use App\Models\Seat;
use App\Models\Screening;
use App\Models\Snack;

class TestDatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database for testing.
     */
    public function run(): void
    {
        // Crear roles básicos
        $adminRole = Role::factory()->admin()->create();
        $userRole = Role::factory()->user()->create();

        // Crear usuarios de prueba
        $admin = User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@test.com',
            'role_id' => $adminRole->id
        ]);

        $user = User::factory()->create([
            'name' => 'User',
            'email' => 'user@test.com',
            'role_id' => $userRole->id
        ]);

        // Crear géneros de películas
        $genres = MovieGenre::factory()->count(5)->create();

        // Crear películas
        $movies = Movie::factory()
            ->count(10)
            ->recycle($genres)
            ->create();

        // Crear auditorios
        $auditoriums = Auditorium::factory()->count(3)->create();

        // Crear asientos para cada auditorio
        foreach ($auditoriums as $auditorium) {
            // Crear una matriz de asientos 8x15 (filas A-H, columnas 1-15)
            foreach (['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'] as $row) {
                for ($col = 1; $col <= 15; $col++) {
                    Seat::factory()->create([
                        'auditorium_id' => $auditorium->id,
                        'number' => $row . $col,
                        'status' => 'available'
                    ]);
                }
            }
        }

        // Crear proyecciones
        $screenings = Screening::factory()
            ->count(20)
            ->recycle($movies)
            ->recycle($auditoriums)
            ->create();

        // Crear snacks
        $snacks = Snack::factory()->count(5)->create();
    }
}
