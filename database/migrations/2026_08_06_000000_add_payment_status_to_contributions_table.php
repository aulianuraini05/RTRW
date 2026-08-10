<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('contributions', 'payment_status')) {
            Schema::table('contributions', function (Blueprint $table) {
                $table->string('payment_status')->default('pending');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('contributions', 'payment_status')) {
            Schema::table('contributions', function (Blueprint $table) {
                $table->dropColumn('payment_status');
            });
        }
    }
};
