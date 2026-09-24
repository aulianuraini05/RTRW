<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rws', function (Blueprint $table) {
            $table->string('kelurahan')->nullable()->after('admin_code');
            $table->string('kecamatan')->nullable()->after('kelurahan');
            $table->string('kota_kabupaten')->nullable()->after('kecamatan');
            $table->string('provinsi')->nullable()->after('kota_kabupaten');
            $table->string('kode_pos', 10)->nullable()->after('provinsi');
            $table->string('alamat_lengkap')->nullable()->after('kode_pos');
        });
    }

    public function down(): void
    {
        Schema::table('rws', function (Blueprint $table) {
            $table->dropColumn([
                'kelurahan',
                'kecamatan',
                'kota_kabupaten',
                'provinsi',
                'kode_pos',
                'alamat_lengkap',
            ]);
        });
    }
};
