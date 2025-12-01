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
        Schema::create('polls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->onDelete('cascade');
            $table->string('question');
            $table->string('layout_type')->default('vertical');
            $table->boolean('auto_submit')->default(true);
            $table->boolean('require_email')->default(false);
            $table->boolean('collect_feedback')->default(false);
            $table->text('thank_you_message')->nullable();
            $table->boolean('hide_branding')->default(false);
            $table->string('status')->default('draft');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('polls');
    }
};
