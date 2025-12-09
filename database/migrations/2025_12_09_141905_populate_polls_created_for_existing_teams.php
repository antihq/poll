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
        // Set polls_created to current poll count for existing teams
        // This ensures existing teams don't lose their quota
        $teams = DB::table('teams')->get();

        foreach ($teams as $team) {
            $pollCount = DB::table('polls')
                ->where('team_id', $team->id)
                ->count();

            DB::table('teams')
                ->where('id', $team->id)
                ->update(['polls_created' => $pollCount]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
