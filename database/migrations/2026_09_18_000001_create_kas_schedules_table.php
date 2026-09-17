<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Nominal kas bulanan yang ditetapkan Ketua RT per RT.
     * Satu RT hanya boleh punya satu jadwal per bulan+tahun.
     */
    public function up(): void
    {
        if (! Schema::hasTable('kas_schedules')) {
            Schema::create('kas_schedules', function (Blueprint $table) {
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

        if (! Schema::hasColumn('cash_transactions', 'kas_schedule_id')) {
            Schema::table('cash_transactions', function (Blueprint $table) {
                $table->foreignId('kas_schedule_id')
                    ->nullable()
                    ->after('user_id')
                    ->constrained('kas_schedules')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('cash_transactions', 'kas_schedule_id')) {
            Schema::table('cash_transactions', function (Blueprint $table) {
                $table->dropConstrainedForeignId('kas_schedule_id');
            });
        }

        Schema::dropIfExists('kas_schedules');
    }
};
