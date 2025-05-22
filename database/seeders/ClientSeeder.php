<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the admin user
        $adminUser = User::where('email', 'admin@gmail.com')->first();

        // Create clients for admin user
        if ($adminUser) {
            // Create specific clients
            Client::create([
                'user_id' => $adminUser->id,
                'name' => 'ABC Corporation',
                'email' => 'info@abccorp.com',
                'contact_person' => 'John Smith',
                'notes' => 'Important client with multiple projects',
            ]);

            Client::create([
                'user_id' => $adminUser->id,
                'name' => 'XYZ Solutions',
                'email' => 'contact@xyzsolutions.com',
                'contact_person' => 'Sarah Johnson',
                'notes' => 'New client with potential for growth',
            ]);

            // Create some random clients
            Client::factory(3)->create([
                'user_id' => $adminUser->id,
            ]);
        }

        // Create clients for regular users too
        User::where('email', '<>', 'admin@gmail.com')->each(function ($user) {
            Client::factory(2)->create([
                'user_id' => $user->id,
            ]);
        });
    }
}
