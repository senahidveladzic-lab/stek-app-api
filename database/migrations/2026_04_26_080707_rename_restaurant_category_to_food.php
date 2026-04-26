<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('categories')->where('name', 'restaurant')->update(['name' => 'food']);
    }

    public function down(): void
    {
        DB::table('categories')->where('name', 'food')->update(['name' => 'restaurant']);
    }
};
