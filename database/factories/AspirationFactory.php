<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Aspiration>
 */
class AspirationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'aspiration_title' => fake()->sentence(4),
            'aspiration_content' => fake()->paragraph(),
            'category' => 'Pengaduan lingkungan',
            'submission_date' => fake()->dateTimeBetween('-1 month', 'now')->format('Y-m-d'),
            'aspiration_status' => 'dikirim',
        ];
    }
}
