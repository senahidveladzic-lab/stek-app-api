<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('global_corrections', function (Blueprint $table) {
            $table->id();
            $table->string('corrected_description');
            $table->string('corrected_category_key', 50);
            $table->unsignedInteger('frequency')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamp('promoted_at')->useCurrent();
            $table->timestamps();

            $table->unique(['corrected_description', 'corrected_category_key']);
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('global_corrections');
    }
};
