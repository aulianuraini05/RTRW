<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('asset_loans')) {
            Schema::create('asset_loans', function (Blueprint $table) {
                $table->id();

                $table->foreignId('asset_id')->constrained()->cascadeOnDelete();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->integer('quantity')->default(1);
                $table->date('borrow_date');
                $table->date('return_date')->nullable();
                $table->date('actual_return_date')->nullable();
                $table->string('loan_status')->default('diajukan');
                $table->text('notes')->nullable();

                $table->timestamps();
            });

            return;
        }

        if (! Schema::hasColumn('asset_loans', 'loan_status')) {
            Schema::table('asset_loans', function (Blueprint $table) {
                $table->string('loan_status')->default('diajukan')->after('status');
            });
        }

        if (! Schema::hasColumn('asset_loans', 'actual_return_date')) {
            Schema::table('asset_loans', function (Blueprint $table) {
                $table->date('actual_return_date')->nullable()->after('return_date');
            });
        }

        if (! Schema::hasColumn('asset_loans', 'quantity')) {
            Schema::table('asset_loans', function (Blueprint $table) {
                $table->integer('quantity')->default(1)->after('user_id');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_loans');
    }
};
