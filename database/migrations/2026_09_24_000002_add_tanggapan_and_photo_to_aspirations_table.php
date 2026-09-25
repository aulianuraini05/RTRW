<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('aspirations', function (Blueprint $table) {
            if (! Schema::hasColumn('aspirations', 'tanggapan')) {
                $table->text('tanggapan')->nullable()->after('aspiration_status');
            }
            if (! Schema::hasColumn('aspirations', 'tanggapan_by')) {
                $table->foreignId('tanggapan_by')->nullable()->after('tanggapan')->constrained('users')->nullOnDelete();
            }
            if (! Schema::hasColumn('aspirations', 'tanggapan_at')) {
                $table->timestamp('tanggapan_at')->nullable()->after('tanggapan_by');
            }
            if (! Schema::hasColumn('aspirations', 'photo_path')) {
                $table->string('photo_path')->nullable()->after('tanggapan_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('aspirations', function (Blueprint $table) {
            try {
                $table->dropForeign(['tanggapan_by']);
            } catch (\Throwable $e) {
                // abaikan jika FK tidak ada
            }
            $columns = array_filter(
                ['tanggapan', 'tanggapan_by', 'tanggapan_at', 'photo_path'],
                fn (string $column) => Schema::hasColumn('aspirations', $column)
            );
            if (! empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
