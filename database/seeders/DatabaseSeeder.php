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

        // Seed RW master data (single wilayah - update alamat global di sini)
        \App\Models\Rw::updateOrCreate(
            ['name' => 'RW 10'],
            [
                'code' => 'KETUA-RW',
                'admin_code' => 'ADMIN-UTAMA',
                'kelurahan' => 'Kelurahan Contoh',
                'kecamatan' => 'Kecamatan Contoh',
                'kota_kabupaten' => 'Kota Contoh',
                'provinsi' => 'Jawa Barat',
                'kode_pos' => '40100',
                'alamat_lengkap' => 'Jl. Contoh No. 123',
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
