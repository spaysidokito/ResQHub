<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('earthquakes', function (Blueprint $table) {
            $table->id();
            $table->string('external_id')->unique();
            $table->decimal('magnitude', 4, 2);
            $table->string('location');
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->integer('depth');
            $table->timestamp('occurred_at');
            $table->string('source')->default('USGS');
            $table->json('details')->nullable();
            $table->timestamps();

            $table->index(['magnitude', 'occurred_at']);
            $table->index(['latitude', 'longitude']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('earthquakes');
    }
};
