<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            MovieSeeder::class,
            AuditoriumSeeder::class,
            SeatSeeder::class,
            ScreeningSeeder::class,
            SnackSeeder::class,
            // BookingSeeder y TicketSeeder no se incluyen aquí porque dependen de la interacción del usuario
        ]);
    }
}
