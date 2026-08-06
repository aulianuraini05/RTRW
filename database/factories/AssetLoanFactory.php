<?php

namespace Database\Factories;

use App\Models\Asset;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\AssetLoan>
 */
class AssetLoanFactory extends Factory
{
    public function definition(): array
    {
        $borrowDate = fake()->dateTimeBetween('-1 week', '+1 week');

        return [
            'asset_id' => Asset::factory(),
            'user_id' => User::factory(),
            'quantity' => fake()->numberBetween(1, 3),
            'borrow_date' => $borrowDate->format('Y-m-d'),
            'return_date' => fake()->dateTimeBetween($borrowDate, '+2 weeks')->format('Y-m-d'),
            'actual_return_date' => fake()->optional(0.3)->dateTimeBetween($borrowDate, '+2 weeks')->format('Y-m-d'),
            'loan_status' => 'diajukan',
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
