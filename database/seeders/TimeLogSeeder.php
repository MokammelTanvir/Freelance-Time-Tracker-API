<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\TimeLog;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TimeLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Find website redesign project to add specific time logs
        $websiteProject = Project::where('title', 'Website Redesign')->first();

        if ($websiteProject) {
            // Create some predetermined time logs
            TimeLog::create([
                'project_id' => $websiteProject->id,
                'start_time' => Carbon::now()->subDays(5)->setHour(9)->setMinute(0),
                'end_time' => Carbon::now()->subDays(5)->setHour(12)->setMinute(30),
                'description' => 'Initial wireframes and design concepts',
                'is_billable' => true,
                'tags' => 'design,planning',
            ]);

            TimeLog::create([
                'project_id' => $websiteProject->id,
                'start_time' => Carbon::now()->subDays(4)->setHour(14)->setMinute(0),
                'end_time' => Carbon::now()->subDays(4)->setHour(18)->setMinute(0),
                'description' => 'Homepage layout implementation',
                'is_billable' => true,
                'tags' => 'development',
            ]);

            // Create an in-progress time log
            TimeLog::create([
                'project_id' => $websiteProject->id,
                'start_time' => Carbon::now()->subHour(1),
                'end_time' => null,
                'description' => 'Working on responsive design fixes',
                'hours' => 0,
                'is_billable' => true,
                'tags' => 'development,bug-fix',
            ]);
        }

        // Find e-commerce project to add specific time logs
        $ecommerceProject = Project::where('title', 'E-commerce Platform')->first();

        if ($ecommerceProject) {
            TimeLog::create([
                'project_id' => $ecommerceProject->id,
                'start_time' => Carbon::now()->subDays(3)->setHour(10)->setMinute(0),
                'end_time' => Carbon::now()->subDays(3)->setHour(15)->setMinute(30),
                'description' => 'Product catalog database design',
                'is_billable' => true,
                'tags' => 'development,planning',
            ]);

            TimeLog::create([
                'project_id' => $ecommerceProject->id,
                'start_time' => Carbon::now()->subDays(2)->setHour(9)->setMinute(0),
                'end_time' => Carbon::now()->subDays(2)->setHour(10)->setMinute(0),
                'description' => 'Client meeting to discuss features',
                'is_billable' => false, // Meetings often marked as non-billable
                'tags' => 'meeting',
            ]);
        }

        // Create random time logs for all projects
        Project::all()->each(function ($project) {
            // Create 1-5 time logs for each project
            $count = rand(1, 5);
            TimeLog::factory($count)->create([
                'project_id' => $project->id,
            ]);
        });
    }
}
