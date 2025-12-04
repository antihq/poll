<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('answers')
            ->whereNull('ulid')
            ->get()
            ->each(function ($answer) {
                DB::table('answers')
                    ->where('id', $answer->id)
                    ->update(['ulid' => Str::ulid()]);
            });
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
