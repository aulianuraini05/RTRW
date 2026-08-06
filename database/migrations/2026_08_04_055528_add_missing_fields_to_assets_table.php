<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('assets', 'asset_name')) {
            Schema::table('assets', function (Blueprint $table) {
                $table->string('asset_name')->after('id');
            });
        }

        if (! Schema::hasColumn('assets', 'asset_type')) {
            Schema::table('assets', function (Blueprint $table) {
                $table->string('asset_type')->after('asset_name');
            });
        }

        if (! Schema::hasColumn('assets', 'quantity')) {
            Schema::table('assets', function (Blueprint $table) {
                $table->integer('quantity')->default(1)->after('asset_type');
            });
        }

        if (! Schema::hasColumn('assets', 'condition')) {
            Schema::table('assets', function (Blueprint $table) {
                $table->string('condition')->default('baik')->after('quantity');
            });
        }

        if (! Schema::hasColumn('assets', 'description')) {
            Schema::table('assets', function (Blueprint $table) {
                $table->text('description')->nullable()->after('condition');
            });
        }
    }

    public function down(): void
    {
        $columns = collect(['description', 'condition', 'quantity', 'asset_type', 'asset_name'])
            ->filter(fn (string $column) => Schema::hasColumn('assets', $column))
            ->all();

        if ($columns !== []) {
            Schema::table('assets', function (Blueprint $table) use ($columns) {
                $table->dropColumn($columns);
            });
        }
    }
};
