<?php

namespace Database\Factories;

use App\Models\Rt;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Rt>
 */
class RtFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $number = fake()->unique()->numberBetween(1, 999);
        $padded = str_pad((string) $number, 2, '0', STR_PAD_LEFT);

        return [
            'name' => "RT {$number}",
            'code' => "WARGA-RT{$padded}",
            'admin_code' => "KETUA-RT{$padded}",
        ];
    }
}