<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('letter_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('letter_id')->constrained('letters')->cascadeOnDelete();
            $table->string('doc_key', 50);
            $table->string('label', 150);
            $table->string('file_path', 255);
            $table->timestamps();

            $table->unique(['letter_id', 'doc_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('letter_attachments');
    }
};
