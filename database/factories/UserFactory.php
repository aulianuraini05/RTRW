<?php

namespace Database\Factories;

use App\Models\Rt;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'role' => 'warga',
            'rt_id' => Rt::factory(),
            'no_whatsapp' => '08'.fake()->numerify('##########'),
            'nik' => fake()->unique()->numerify('################'),
            'no_kk' => fake()->numerify('################'),
            'tempat_lahir' => fake()->city(),
            'tanggal_lahir' => fake()->date('Y-m-d', '-20 years'),
            'jenis_kelamin' => fake()->randomElement(['L', 'P']),
            'status_perkawinan' => fake()->randomElement(['belum_kawin', 'kawin']),
            'agama' => fake()->randomElement(['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha']),
            'pendidikan_terakhir' => fake()->randomElement(['SD', 'SMP', 'SMA/SMK', 'S1']),
            'pekerjaan' => fake()->jobTitle(),
            'alamat_rumah' => fake()->streetAddress(),
            'no_rumah' => fake()->numerify('##'),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
