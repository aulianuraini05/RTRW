<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * RT pemilik aspirasi dicatat langsung di baris aspirasi.
     * Sebelumnya scope RT hanya lewat relasi user — akibatnya kalau akun
     * warga dihapus (user_id jadi NULL), aspirasinya hilang dari daftar RT.
     */
    public function up(): void
    {
        Schema::table('aspirations', function (Blueprint $table) {
            $table->foreignId('rt_id')->nullable()->after('user_id')->constrained('rts')->nullOnDelete();
        });

        // Backfill dari RT warga yang masih ada.
        DB::statement('UPDATE aspirations SET rt_id = (SELECT rt_id FROM users WHERE users.id = aspirations.user_id) WHERE rt_id IS NULL AND user_id IS NOT NULL');
    }

    public function down(): void
    {
        Schema::table('aspirations', function (Blueprint $table) {
            $table->dropConstrainedForeignId('rt_id');
        });
    }
};
