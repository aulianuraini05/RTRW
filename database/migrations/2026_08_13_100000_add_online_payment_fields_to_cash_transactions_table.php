<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cash_transactions', function (Blueprint $table) {
            if (! Schema::hasColumn('cash_transactions', 'amount')) {
                $table->decimal('amount', 12, 2)->nullable()->after('user_id');
            }
            if (! Schema::hasColumn('cash_transactions', 'payment_method')) {
                $table->string('payment_method')->nullable()->after('amount');
            }
            if (! Schema::hasColumn('cash_transactions', 'payment_code')) {
                $table->string('payment_code')->nullable()->after('payment_method');
            }
            if (! Schema::hasColumn('cash_transactions', 'paid_at')) {
                $table->timestamp('paid_at')->nullable()->after('payment_code');
            }
        });
    }

    public function down(): void
    {
        Schema::table('cash_transactions', function (Blueprint $table) {
            $table->dropColumn(['amount', 'payment_method', 'payment_code', 'paid_at']);
        });
    }
};
