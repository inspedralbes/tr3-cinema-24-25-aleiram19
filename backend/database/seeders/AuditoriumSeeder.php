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
                'capacity' => 120,
                'type' => '2D',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Sala 2',
                'capacity' => 120,
                'type' => '2D',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
