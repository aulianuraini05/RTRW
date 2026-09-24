<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('aspirations', function (Blueprint $table) {
            $table->string('forwarded_to')->nullable()->after('aspiration_status');
            $table->foreignId('forwarded_by')->nullable()->after('forwarded_to')->constrained('users')->nullOnDelete();
            $table->timestamp('forwarded_at')->nullable()->after('forwarded_by');
        });
    }

    public function down(): void
    {
        Schema::table('aspirations', function (Blueprint $table) {
            $table->dropColumn(['forwarded_to', 'forwarded_by', 'forwarded_at']);
        });
    }
};
