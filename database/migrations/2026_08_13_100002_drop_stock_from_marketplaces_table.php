<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('marketplaces', function (Blueprint $table) {
            if (Schema::hasColumn('marketplaces', 'stock')) {
                $table->dropColumn('stock');
            }
        });
    }

    public function down(): void
    {
        Schema::table('marketplaces', function (Blueprint $table) {
            if (! Schema::hasColumn('marketplaces', 'stock')) {
                $table->integer('stock')->default(0)->after('price');
            }
        });
    }
};
