<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Kontak
            $table->string('no_whatsapp', 20)->nullable()->after('email');
            // Kependudukan - wajib di register (ringan)
            $table->string('nik', 16)->nullable()->unique()->after('no_whatsapp');
            $table->string('no_kk', 16)->nullable()->index()->after('nik');
            // Demografi - diisi di lengkapi profil
            $table->string('tempat_lahir')->nullable()->after('no_kk');
            $table->date('tanggal_lahir')->nullable()->after('tempat_lahir');
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable()->after('tanggal_lahir');
            $table->enum('status_perkawinan', ['belum_kawin', 'kawin', 'cerai_hidup', 'cerai_mati'])->nullable()->after('jenis_kelamin');
            $table->string('agama', 20)->nullable()->after('status_perkawinan');
            $table->string('pendidikan_terakhir', 30)->nullable()->after('agama');
            $table->string('pekerjaan', 50)->nullable()->after('pendidikan_terakhir');
            $table->string('alamat_rumah')->nullable()->after('pekerjaan');
            $table->string('no_rumah', 10)->nullable()->after('alamat_rumah');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'no_whatsapp',
                'nik',
                'no_kk',
                'tempat_lahir',
                'tanggal_lahir',
                'jenis_kelamin',
                'status_perkawinan',
                'agama',
                'pendidikan_terakhir',
                'pekerjaan',
                'alamat_rumah',
                'no_rumah',
            ]);
        });
    }
};
