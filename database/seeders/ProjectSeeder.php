<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Project;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Find ABC Corporation client to add specific projects
        $abcClient = Client::where('name', 'ABC Corporation')->first();

        if ($abcClient) {
            Project::create([
                'client_id' => $abcClient->id,
                'title' => 'Website Redesign',
                'description' => 'Complete redesign of corporate website with modern UI/UX',
                'status' => 'active',
                'deadline' => Carbon::now()->addMonths(2),
                'hourly_rate' => 85.00,
            ]);

            Project::create([
                'client_id' => $abcClient->id,
                'title' => 'Mobile App Development',
                'description' => 'Development of iOS and Android apps for customer engagement',
                'status' => 'on-hold',
                'deadline' => Carbon::now()->addMonths(4),
                'hourly_rate' => 95.00,
            ]);
        }

        // Find XYZ Solutions client to add specific project
        $xyzClient = Client::where('name', 'XYZ Solutions')->first();

        if ($xyzClient) {
            Project::create([
                'client_id' => $xyzClient->id,
                'title' => 'E-commerce Platform',
                'description' => 'Development of an online store with payment processing',
                'status' => 'active',
                'deadline' => Carbon::now()->addMonths(3),
                'hourly_rate' => 90.00,
            ]);
        }

        // Create random projects for other clients
        Client::whereNotIn('name', ['ABC Corporation', 'XYZ Solutions'])
            ->each(function ($client) {
                // Create 1-3 random projects for each client
                $count = rand(1, 3);
                Project::factory($count)->create([
                    'client_id' => $client->id,
                ]);
            });
    }
}
