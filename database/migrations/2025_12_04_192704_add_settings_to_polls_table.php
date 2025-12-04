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
        Schema::table('polls', function (Blueprint $table) {
            $table->boolean('accepts_responses')->default(true);
            $table->boolean('require_email')->default(false);
            $table->boolean('auto_submit')->default(false);
            $table->boolean('collect_feedback')->default(false);
            $table->text('thank_you_message')->nullable();
            $table->string('thank_you_button_label')->nullable();
            $table->string('thank_you_button_url')->nullable();
            $table->string('redirect_url')->nullable();
            $table->boolean('hide_branding')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('polls', function (Blueprint $table) {
            $table->dropColumn([
                'accepts_responses',
                'require_email',
                'auto_submit',
                'collect_feedback',
                'thank_you_message',
                'thank_you_button_label',
                'thank_you_button_url',
                'redirect_url',
                'hide_branding',
            ]);
        });
    }
};
