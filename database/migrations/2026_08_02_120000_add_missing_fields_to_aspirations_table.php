<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('aspirations', 'aspiration_title')) {
            Schema::table('aspirations', function (Blueprint $table) {
                $table->string('aspiration_title');
            });
        }

        if (! Schema::hasColumn('aspirations', 'aspiration_content')) {
            Schema::table('aspirations', function (Blueprint $table) {
                $table->text('aspiration_content');
            });
        }

        if (! Schema::hasColumn('aspirations', 'category')) {
            Schema::table('aspirations', function (Blueprint $table) {
                $table->string('category');
            });
        }

        if (! Schema::hasColumn('aspirations', 'submission_date')) {
            Schema::table('aspirations', function (Blueprint $table) {
                $table->date('submission_date');
            });
        }

        if (! Schema::hasColumn('aspirations', 'aspiration_status')) {
            Schema::table('aspirations', function (Blueprint $table) {
                $table->string('aspiration_status')->default('dikirim');
            });
        }
    }

    public function down(): void
    {
        $columns = collect([
            'aspiration_status',
            'submission_date',
            'category',
            'aspiration_content',
            'aspiration_title',
        ])->filter(fn (string $column) => Schema::hasColumn('aspirations', $column))->all();

        if ($columns !== []) {
            Schema::table('aspirations', function (Blueprint $table) use ($columns) {
                $table->dropColumn($columns);
            });
        }
    }
};
