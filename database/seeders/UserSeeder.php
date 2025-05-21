<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create an admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('password'),
        ]);

        // Create a regular user
        User::create([
            'name' => 'Test User',
            'email' => 'user@gmail.com',
            'password' => Hash::make('password'),
        ]);

        // Create some random users
        User::factory(5)->create();
    }
}
