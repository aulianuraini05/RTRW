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
        Schema::create('letters', function (Blueprint $table) {
    $table->id();

            $table->string('letter_number')->unique();
            $table->string('letter_type');
            $table->date('submission_date');
            $table->date('letter_date')->nullable();
            $table->text('purpose')->nullable();
            $table->string('letter_status');

    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('letters');
    }
};
