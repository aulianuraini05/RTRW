<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('announcements', function (Blueprint $table) {
            if (! Schema::hasColumn('announcements', 'category')) {
                $table->string('category')->default('umum')->after('announcement_content');
            }

            if (! Schema::hasColumn('announcements', 'priority')) {
                $table->string('priority')->default('biasa')->after('category');
            }

            if (! Schema::hasColumn('announcements', 'is_pinned')) {
                $table->boolean('is_pinned')->default(false)->after('status');
            }

            if (! Schema::hasColumn('announcements', 'read_count')) {
                $table->unsignedInteger('read_count')->default(0)->after('is_pinned');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('announcements', function (Blueprint $table) {
            $table->dropColumn(['category', 'priority', 'is_pinned', 'read_count']);
        });
    }
};
