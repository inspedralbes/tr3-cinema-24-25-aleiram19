<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SnackSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('snacks')->insert([
            [
                'name' => 'Palomitas pequeñas',
                'price' => 4.50,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Palomitas medianas',
                'price' => 5.50,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Palomitas grandes',
                'price' => 6.50,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Refresco pequeño',
                'price' => 3.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Refresco mediano',
                'price' => 4.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Refresco grande',
                'price' => 5.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Nachos con queso',
                'price' => 5.50,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Hot Dog',
                'price' => 4.50,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Combo 1 (Palomitas medianas + Refresco mediano)',
                'price' => 8.50,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Combo 2 (Palomitas grandes + 2 Refrescos medianos)',
                'price' => 12.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
