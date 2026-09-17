<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Iuran punya banyak jenis (sampah, keamanan, pembangunan, ...).
     * Satu RT boleh punya beberapa jenis iuran dalam bulan yang sama,
     * tapi tidak boleh dobel untuk jenis + bulan yang sama.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('iuran_schedules', 'jenis')) {
            Schema::table('iuran_schedules', function (Blueprint $table) {
                $table->string('jenis', 50)->default('Lainnya')->after('rt_id');
            });
        }

        try {
            Schema::table('iuran_schedules', function (Blueprint $table) {
                $table->dropUnique(['rt_id', 'month', 'year']);
            });
        } catch (Throwable) {
            // Abaikan jika constraint tidak ada.
        }

        try {
            Schema::table('iuran_schedules', function (Blueprint $table) {
                $table->unique(['rt_id', 'month', 'year', 'jenis']);
            });
        } catch (Throwable) {
            // Abaikan jika constraint sudah ada.
        }
    }

    public function down(): void
    {
        try {
            Schema::table('iuran_schedules', function (Blueprint $table) {
                $table->dropUnique(['rt_id', 'month', 'year', 'jenis']);
            });
        } catch (Throwable) {
        }

        try {
            Schema::table('iuran_schedules', function (Blueprint $table) {
                $table->unique(['rt_id', 'month', 'year']);
            });
        } catch (Throwable) {
        }

        if (Schema::hasColumn('iuran_schedules', 'jenis')) {
            Schema::table('iuran_schedules', function (Blueprint $table) {
                $table->dropColumn('jenis');
            });
        }
    }
};
