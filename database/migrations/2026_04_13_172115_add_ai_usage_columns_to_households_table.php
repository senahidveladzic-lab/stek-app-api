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
        Schema::table('households', function (Blueprint $table) {
            $table->unsignedInteger('ai_reports_used')->default(0)->after('max_members');
            $table->date('ai_reports_month')->nullable()->after('ai_reports_used');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('households', function (Blueprint $table) {
            $table->dropColumn(['ai_reports_used', 'ai_reports_month']);
        });
    }
};
