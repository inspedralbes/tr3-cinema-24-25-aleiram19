<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ScreeningSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Sesiones normales (una sesión por día y usando solo los horarios permitidos)
        DB::table('screenings')->insert([
            [
                'movie_id' => 1,
                'auditorium_id' => 1,
                // Para la película 1, se elige el horario de las 16:00
                'date_time' => Carbon::now()->addDays(1)->setTime(16, 0),
                'price' => 6.00, // Precio normal
                'is_special' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'movie_id' => 2,
                'auditorium_id' => 2,
                // Para la película 2, se elige el horario de las 18:00
                'date_time' => Carbon::now()->addDays(1)->setTime(18, 0),
                'price' => 6.00, // Precio normal
                'is_special' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
        // Configuración de sesión especial (día de l'espectador)
        // Suponemos que el día de l'espectador es el día siguiente al de las sesiones normales.
        // En este ejemplo se programa una sesión especial para la película 1 con precio especial.
        DB::table('screenings')->insert([
            [
                'movie_id' => 1,
                'auditorium_id' => 1,
                'date_time' => Carbon::now()->addDays(2)->setTime(16, 0), // Usando uno de los horarios permitidos
                'price' => 4.00, // Precio especial para el día del espectador (normal)
                'is_special' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}