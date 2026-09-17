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

        // Seed RW master data (Ketua RW & Admin Utama codes)
        \App\Models\Rw::updateOrCreate(
            ['name' => 'RW 10'],
            [
                'code' => 'KETUA-RW',
                'admin_code' => 'ADMIN-UTAMA',
            ]
        );

        $rtCount = 10;

        foreach (range(1, $rtCount) as $number) {
            $padded = str_pad((string) $number, 2, '0', STR_PAD_LEFT);
            Rt::updateOrCreate(
                ['name' => "RT {$number}"],
                [
                    'code' => "WARGA-RT{$padded}",
                    'admin_code' => "KETUA-RT{$padded}",
                ]
            );
        }
    }
}
