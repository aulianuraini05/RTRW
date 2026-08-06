<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Letter>
 */
class LetterFactory extends Factory
{
    public function definition(): array
    {
        $types = ['Surat Keterangan Domisili', 'Surat Pengantar KTP', 'Surat Pengantar KK', 'Surat Keterangan Usaha', 'Surat Keterangan Tidak Mampu'];

        return [
            'user_id' => User::factory(),
            'letter_number' => 'SURAT/'.now()->format('Ymd').'/'.str_pad((string) fake()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT),
            'letter_type' => fake()->randomElement($types),
            'submission_date' => fake()->dateTimeBetween('-1 month', 'now')->format('Y-m-d'),
            'letter_date' => fake()->optional()->dateTimeBetween('-1 month', 'now')->format('Y-m-d'),
            'purpose' => fake()->paragraph(),
            'letter_status' => 'diajukan',
        ];
    }
}
