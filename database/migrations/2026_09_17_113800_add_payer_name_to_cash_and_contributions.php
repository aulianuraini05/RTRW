<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('cash_transactions', 'payer_name')) {
            Schema::table('cash_transactions', function (Blueprint $table) {
                $table->string('payer_name')->nullable()->after('user_id');
            });
        }

        if (! Schema::hasColumn('contributions', 'payer_name')) {
            Schema::table('contributions', function (Blueprint $table) {
                $table->string('payer_name')->nullable()->after('user_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('cash_transactions', 'payer_name')) {
            Schema::table('cash_transactions', function (Blueprint $table) {
                $table->dropColumn('payer_name');
            });
        }

        if (Schema::hasColumn('contributions', 'payer_name')) {
            Schema::table('contributions', function (Blueprint $table) {
                $table->dropColumn('payer_name');
            });
        }
    }
};
