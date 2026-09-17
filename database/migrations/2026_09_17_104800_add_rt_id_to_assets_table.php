<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('assets', 'rt_id')) {
            Schema::table('assets', function (Blueprint $table) {
                $table->foreignId('rt_id')->nullable()->after('id')->constrained('rts')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('assets', 'rt_id')) {
            Schema::table('assets', function (Blueprint $table) {
                $table->dropConstrainedForeignId('rt_id');
            });
        }
    }
};
