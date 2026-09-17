<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menandai tiap catatan kas milik RT mana, agar Ketua RT
     * hanya bisa melihat kas RT-nya sendiri (privasi per RT).
     */
    public function up(): void
    {
        if (! Schema::hasColumn('cash_transactions', 'rt_id')) {
            Schema::table('cash_transactions', function (Blueprint $table) {
                $table->foreignId('rt_id')
                    ->nullable()
                    ->after('kas_schedule_id')
                    ->constrained('rts')
                    ->nullOnDelete();
            });
        }

        // Backfill: catatan yang tertaut akun mengikuti RT pemilik akun.
        DB::table('cash_transactions')
            ->whereNotNull('user_id')
            ->whereNull('rt_id')
            ->update(['rt_id' => DB::raw('(SELECT rt_id FROM users WHERE users.id = cash_transactions.user_id)')]);
    }

    public function down(): void
    {
        if (Schema::hasColumn('cash_transactions', 'rt_id')) {
            Schema::table('cash_transactions', function (Blueprint $table) {
                $table->dropConstrainedForeignId('rt_id');
            });
        }
    }
};
