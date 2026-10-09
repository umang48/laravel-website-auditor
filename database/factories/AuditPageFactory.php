<?php

namespace Database\Factories;

use App\Models\AuditPage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AuditPage>
 */
class AuditPageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
       return [
        'url' => fake()->url(),
        'status_code' => fake()->randomElement([200, 200, 200, 301, 404, 500]), // Weighted towards 200 OK
        'response_time' => fake()->numberBetween(100, 3000), // 100ms to 3s
        'page_size' => fake()->numberBetween(15000, 3000000), // 15KB to 3MB
        'title' => fake()->sentence(5),
        'meta_description' => fake()->text(160),
        'canonical' => fake()->url(),
    ];
    }
}
