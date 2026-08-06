<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Asset>
 */
class AssetFactory extends Factory
{
    public function definition(): array
    {
        $types = ['Elektronik', 'Furniture', 'Kendaraan', 'Alat Kebersihan', 'Alat Olahraga', 'Bangunan'];
        $conditions = ['baik', 'baik', 'baik', 'rusak ringan', 'perlu perbaikan'];

        return [
            'asset_name' => fake()->words(2, true),
            'asset_type' => fake()->randomElement($types),
            'quantity' => fake()->numberBetween(1, 10),
            'condition' => fake()->randomElement($conditions),
            'description' => fake()->optional()->sentence(),
        ];
    }
}
