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
        Schema::create('team_challenges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('type', ['collaborative', 'competitive', 'milestone'])->default('collaborative');
            $table->integer('target_points')->default(0); // Target points for the challenge
            $table->integer('reward_points')->default(0); // Points awarded upon completion
            $table->json('requirements')->nullable(); // Specific requirements for completion
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->enum('status', ['active', 'completed', 'expired'])->default('active');
            $table->integer('progress')->default(0); // Current progress towards completion
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('team_challenges');
    }
};
