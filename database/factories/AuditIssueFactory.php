<?php

namespace Database\Factories;

use App\Models\AuditIssue;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AuditIssue>
 */
class AuditIssueFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
        'type' => fake()->randomElement(['seo', 'performance', 'accessibility']),
        'severity' => fake()->randomElement(['error', 'warning', 'info']),
        'message' => fake()->sentence(),
        'recommendation' => fake()->paragraph(),
    ];
    }
}
