<?php

namespace Database\Factories;

use App\Models\Project;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TimeLog>
 */
class TimeLogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Generate a random start time within the past 30 days
        $startTime = fake()->dateTimeBetween('-30 days', '-1 hour');

        // End time will be 1-4 hours after start time
        $endTime = Carbon::instance($startTime)->addMinutes(fake()->numberBetween(30, 240));

        // Calculate hours - this would normally be handled by the model setter
        // but we're explicitly setting it in the factory for consistency
        $hours = Carbon::instance($startTime)->diffInMinutes($endTime) / 60;

        // Sample tags for time logs
        $possibleTags = ['design', 'development', 'meeting', 'bug-fix', 'documentation', 'planning', 'research'];
        $tagCount = fake()->numberBetween(0, 3);
        $tags = $tagCount > 0 ? implode(',', fake()->randomElements($possibleTags, $tagCount)) : null;

        return [
            'project_id' => Project::factory(),
            'start_time' => $startTime,
            'end_time' => $endTime,
            'description' => fake()->sentence(),
            'hours' => $hours,
            'is_billable' => fake()->boolean(80), // 80% chance to be billable
            'tags' => $tags,
        ];
    }

    /**
     * Generate a time log that is in progress (no end time).
     */
    public function inProgress(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'start_time' => fake()->dateTimeBetween('-4 hours', 'now'),
                'end_time' => null,
                'hours' => 0, // Hours will be 0 until time log is completed
            ];
        });
    }

    /**
     * Generate a billable time log.
     */
    public function billable(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'is_billable' => true,
            ];
        });
    }

    /**
     * Generate a non-billable time log.
     */
    public function nonBillable(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'is_billable' => false,
            ];
        });
    }
}
