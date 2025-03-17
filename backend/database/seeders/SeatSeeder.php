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
        // Crear asientos para la Sala 1 (auditorium_id = 1) - 12 filas (A-L) × 10 sillones
        for ($row = 'A'; $row <= 'L'; $row++) {
            for ($column = 1; $column <= 10; $column++) {
                DB::table('seats')->insert([
                    'auditorium_id' => 1,
                    'number' => $row . $column,
                    'status' => 'available',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Crear asientos para la Sala 2 (auditorium_id = 2) - 12 filas (A-L) × 10 sillones
        for ($row = 'A'; $row <= 'L'; $row++) {
            for ($column = 1; $column <= 10; $column++) {
                DB::table('seats')->insert([
                    'auditorium_id' => 2,
                    'number' => $row . $column,
                    'status' => 'available',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Crear asientos para la Sala VIP (auditorium_id = 5) - con menos filas pero también con fila F para VIP
        for ($row = 'A'; $row <= 'F'; $row++) {
            for ($column = 1; $column <= 10; $column++) {
                DB::table('seats')->insert([
                    'auditorium_id' => 5,
                    'number' => $row . $column,
                    'status' => 'available',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
