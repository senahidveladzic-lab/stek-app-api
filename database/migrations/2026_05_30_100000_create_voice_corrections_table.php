<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('voice_corrections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('whisper_transcript')->nullable();
            $table->string('original_description')->nullable();
            $table->string('original_category_key', 50)->nullable();
            $table->decimal('original_amount', 10, 2)->nullable();
            $table->string('corrected_description')->nullable();
            $table->string('corrected_category_key', 50)->nullable();
            $table->decimal('corrected_amount', 10, 2)->nullable();
            $table->timestamps();

            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('voice_corrections');
    }
};
