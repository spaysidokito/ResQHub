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
        Schema::create('badges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('icon')->nullable(); // Icon name or path
            $table->string('color')->default('#3B82F6'); // Badge color
            $table->enum('type', ['achievement', 'milestone', 'special'])->default('achievement');
            $table->json('criteria')->nullable(); // Achievement criteria (e.g., points threshold, task count)
            $table->integer('points_reward')->default(0); // Bonus points for earning this badge
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('badges');
    }
};
