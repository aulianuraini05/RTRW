<?php

namespace Database\Seeders;

use App\Models\Rt;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $rtCount = 10;

        foreach (range(1, $rtCount) as $number) {
            Rt::factory()->create([
                'name' => "RT {$number}",
                'code' => 'RT' . str_pad((string) $number, 2, '0', STR_PAD_LEFT),
            ]);
        }

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}
