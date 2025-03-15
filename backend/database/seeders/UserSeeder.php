<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin user
        DB::table('users')->insert([
            'name' => 'Admin',
            'last_name' => 'Admin',
            'email' => 'admin@cinema.com',
            'password' => Hash::make('password'),
            'role_id' => 1, // admin
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
