<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('announcements', 'announcement_title')) {
            Schema::table('announcements', function (Blueprint $table) {
                $table->string('announcement_title')->after('id');
            });
        }

        if (! Schema::hasColumn('announcements', 'announcement_content')) {
            Schema::table('announcements', function (Blueprint $table) {
                $table->text('announcement_content')->after('announcement_title');
            });
        }

        if (! Schema::hasColumn('announcements', 'publication_date')) {
            Schema::table('announcements', function (Blueprint $table) {
                $table->date('publication_date')->after('announcement_content');
            });
        }
    }

    public function down(): void
    {
        Schema::table('announcements', function (Blueprint $table) {
            $table->dropColumn(['announcement_title', 'announcement_content', 'publication_date']);
        });
    }
};
