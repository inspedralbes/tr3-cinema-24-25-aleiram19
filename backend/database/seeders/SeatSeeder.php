<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SeatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear asientos para la Sala 1 (auditorium_id = 1)
        for ($row = 'A'; $row <= 'H'; $row++) {
            for ($column = 1; $column <= 10; $column++) {
                DB::table('seat')->insert([
                    'auditorium_id' => 1,
                    'number' => $row . $column,
                    'status' => 'available',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Crear asientos para la Sala 2 (auditorium_id = 2)
        for ($row = 'A'; $row <= 'H'; $row++) {
            for ($column = 1; $column <= 10; $column++) {
                DB::table('seat')->insert([
                    'auditorium_id' => 2,
                    'number' => $row . $column,
                    'status' => 'available',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Crear asientos para la Sala VIP (auditorium_id = 3)
        for ($row = 'A'; $row <= 'D'; $row++) {
            for ($column = 1; $column <= 10; $column++) {
                DB::table('seat')->insert([
                    'auditorium_id' => 3,
                    'number' => $row . $column,
                    'status' => 'available',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
