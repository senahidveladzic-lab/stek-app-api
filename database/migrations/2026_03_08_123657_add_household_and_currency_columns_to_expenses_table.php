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
        Schema::table('expenses', function (Blueprint $table) {
            $table->foreignId('household_id')->nullable()->after('user_id')->constrained()->cascadeOnDelete();
            $table->decimal('original_amount', 10, 2)->nullable()->after('currency');
            $table->string('original_currency', 3)->nullable()->after('original_amount');

            $table->index(['household_id', 'expense_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropIndex(['household_id', 'expense_date']);
            $table->dropConstrainedForeignId('household_id');
            $table->dropColumn(['original_amount', 'original_currency']);
        });
    }
};
