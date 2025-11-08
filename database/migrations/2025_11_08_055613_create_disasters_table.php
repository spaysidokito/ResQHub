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
        Schema::create('disasters', function (Blueprint $table) {
            $table->id();
            $table->string('external_id')->unique();
            $table->enum('type', ['flood', 'typhoon', 'fire', 'earthquake'])->index();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->string('location');
            $table->string('country')->default('Philippines')->index();
            $table->enum('severity', ['low', 'moderate', 'high', 'critical'])->index();
            $table->enum('status', ['active', 'monitoring', 'resolved'])->default('active')->index();
            $table->json('details')->nullable();
            $table->timestamp('started_at');
            $table->timestamp('ended_at')->nullable();
            $table->string('source')->default('PAGASA');
            $table->boolean('is_verified')->default(false);
            $table->timestamps();

            $table->index(['type', 'status', 'severity']);
            $table->index(['country', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('disasters');
    }
};
