<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('aspirations', 'user_id')) {
            return;
        }

        Schema::table('aspirations', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('aspirations', 'user_id')) {
            return;
        }

        Schema::table('aspirations', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
        });
    }
};
