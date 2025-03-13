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
        // Proyecciones para la película 1 (Dune: Part Two)
        DB::table('screening')->insert([
            [
                'movie_id' => 1,
                'auditorium_id' => 1,
                'date_time' => Carbon::now()->addDays(1)->setHour(15)->setMinute(0),
                'price' => 8.50,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'movie_id' => 1,
                'auditorium_id' => 1,
                'date_time' => Carbon::now()->addDays(1)->setHour(18)->setMinute(0),
                'price' => 9.50,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'movie_id' => 1,
                'auditorium_id' => 1,
                'date_time' => Carbon::now()->addDays(1)->setHour(21)->setMinute(0),
                'price' => 10.50,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Proyecciones para la película 2 (The Batman)
        DB::table('screening')->insert([
            [
                'movie_id' => 2,
                'auditorium_id' => 2,
                'date_time' => Carbon::now()->addDays(1)->setHour(16)->setMinute(0),
                'price' => 10.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'movie_id' => 2,
                'auditorium_id' => 2,
                'date_time' => Carbon::now()->addDays(1)->setHour(19)->setMinute(30),
                'price' => 12.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Proyecciones para la película 3 (Everything Everywhere All at Once)
        DB::table('screening')->insert([
            [
                'movie_id' => 3,
                'auditorium_id' => 3,
                'date_time' => Carbon::now()->addDays(1)->setHour(17)->setMinute(30),
                'price' => 14.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'movie_id' => 3,
                'auditorium_id' => 3,
                'date_time' => Carbon::now()->addDays(1)->setHour(21)->setMinute(0),
                'price' => 15.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
