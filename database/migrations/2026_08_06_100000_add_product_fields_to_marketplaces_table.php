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
        if (! Schema::hasColumn('marketplaces', 'product_name')) {
            Schema::table('marketplaces', function (Blueprint $table) {
                $table->string('product_name');
                $table->text('description');
                $table->decimal('price', 15, 2);
                $table->integer('stock')->default(0);
                $table->string('product_status')->default('tersedia');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('marketplaces', function (Blueprint $table) {
            $table->dropColumn(['product_name', 'description', 'price', 'stock', 'product_status']);
        });
    }
};
