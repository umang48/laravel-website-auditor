<?php

namespace Database\Factories;

use App\Models\Audit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Audit>
 */
class AuditFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $isCompleted = fake()->boolean(80); // 80% chance the audit is completed
    $startedAt = fake()->dateTimeBetween('-1 month', 'now');
    
    return [
        'status' => $isCompleted ? 'completed' : fake()->randomElement(['pending', 'processing', 'failed']),
        'score' => $isCompleted ? fake()->numberBetween(40, 100) : null,
        'started_at' => $startedAt,
        'completed_at' => $isCompleted ? fake()->dateTimeInInterval($startedAt, '+5 minutes') : null,
    ];
    }
}
