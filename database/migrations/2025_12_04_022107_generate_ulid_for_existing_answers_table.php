<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('answers')
            ->whereNull('ulid')
            ->update(['ulid' => DB::raw('(SELECT ULID())')]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('answers')
            ->whereNotNull('ulid')
            ->update(['ulid' => null]);
    }
};
