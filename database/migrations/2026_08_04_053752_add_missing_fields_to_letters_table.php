<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('letters', 'user_id')) {
            Schema::table('letters', function (Blueprint $table) {
                $table->foreignId('user_id')->nullable()->after('id')->constrained()->nullOnDelete();
            });
        }

        if (! Schema::hasColumn('letters', 'letter_number')) {
            Schema::table('letters', function (Blueprint $table) {
                $table->string('letter_number')->unique()->after('user_id');
            });
        }

        if (! Schema::hasColumn('letters', 'letter_type')) {
            Schema::table('letters', function (Blueprint $table) {
                $table->string('letter_type')->after('letter_number');
            });
        }

        if (! Schema::hasColumn('letters', 'submission_date')) {
            Schema::table('letters', function (Blueprint $table) {
                $table->date('submission_date')->after('letter_type');
            });
        }

        if (! Schema::hasColumn('letters', 'letter_date')) {
            Schema::table('letters', function (Blueprint $table) {
                $table->date('letter_date')->nullable()->after('submission_date');
            });
        }

        if (! Schema::hasColumn('letters', 'purpose')) {
            Schema::table('letters', function (Blueprint $table) {
                $table->text('purpose')->nullable()->after('letter_date');
            });
        }

        if (! Schema::hasColumn('letters', 'letter_status')) {
            Schema::table('letters', function (Blueprint $table) {
                $table->string('letter_status')->default('diajukan')->after('purpose');
            });
        }
    }

    public function down(): void
    {
        $columns = collect([
            'letter_status',
            'purpose',
            'letter_date',
            'submission_date',
            'letter_type',
            'letter_number',
        ])->filter(fn (string $column) => Schema::hasColumn('letters', $column))->all();

        if ($columns !== []) {
            Schema::table('letters', function (Blueprint $table) use ($columns) {
                if (in_array('letter_number', $columns)) {
                    $table->dropUnique(['letter_number']);
                }
                $table->dropColumn($columns);
            });
        }

        if (Schema::hasColumn('letters', 'user_id')) {
            Schema::table('letters', function (Blueprint $table) {
                $table->dropConstrainedForeignId('user_id');
            });
        }
    }
};
