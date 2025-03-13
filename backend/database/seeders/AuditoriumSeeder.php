<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AuditoriumSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('auditoriums')->insert([
            [
                'name' => 'Sala 1',
                'capacity' => 80,
                'type' => '2D',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Sala 2',
                'capacity' => 80,
                'type' => '3D',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Sala 3',
                'capacity' => 80,
                'type' => '2D',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Sala 4',
                'capacity' => 80,
                'type' => '2D',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Sala VIP',
                'capacity' => 40,
                'type' => 'IMAX',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
