<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Nominal iuran bulanan yang ditetapkan Ketua RT per RT.
     * Satu RT hanya boleh punya satu jadwal per bulan+tahun.
     */
    public function up(): void
    {
        if (! Schema::hasTable('iuran_schedules')) {
            Schema::create('iuran_schedules', function (Blueprint $table) {
                $table->id();
                $table->foreignId('rt_id')->constrained('rts')->cascadeOnDelete();
                $table->unsignedTinyInteger('month');
                $table->unsignedSmallInteger('year');
                $table->decimal('amount', 12, 2);
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();

                $table->unique(['rt_id', 'month', 'year']);
            });
        }

        if (! Schema::hasColumn('contributions', 'iuran_schedule_id')) {
            Schema::table('contributions', function (Blueprint $table) {
                $table->foreignId('iuran_schedule_id')
                    ->nullable()
                    ->after('user_id')
                    ->constrained('iuran_schedules')
                    ->nullOnDelete();
            });
        }

        if (! Schema::hasColumn('contributions', 'rt_id')) {
            Schema::table('contributions', function (Blueprint $table) {
                $table->foreignId('rt_id')
                    ->nullable()
                    ->after('iuran_schedule_id')
                    ->constrained('rts')
                    ->nullOnDelete();
            });
        }

        // Backfill: catatan yang tertaut akun mengikuti RT pemilik akun.
        DB::table('contributions')
            ->whereNotNull('user_id')
            ->whereNull('rt_id')
            ->update(['rt_id' => DB::raw('(SELECT rt_id FROM users WHERE users.id = contributions.user_id)')]);
    }

    public function down(): void
    {
        if (Schema::hasColumn('contributions', 'rt_id')) {
            Schema::table('contributions', function (Blueprint $table) {
                $table->dropConstrainedForeignId('rt_id');
            });
        }

        if (Schema::hasColumn('contributions', 'iuran_schedule_id')) {
            Schema::table('contributions', function (Blueprint $table) {
                $table->dropConstrainedForeignId('iuran_schedule_id');
            });
        }

        Schema::dropIfExists('iuran_schedules');
    }
};
