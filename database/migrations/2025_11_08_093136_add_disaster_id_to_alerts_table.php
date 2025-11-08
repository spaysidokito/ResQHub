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
        Schema::table('alerts', function (Blueprint $table) {
            // Make earthquake_id nullable since alerts can be for any disaster type
            $table->foreignId('earthquake_id')->nullable()->change();

            // Add disaster_id for general disasters
            $table->foreignId('disaster_id')->nullable()->after('earthquake_id')->constrained()->onDelete('cascade');

            // Add index for disaster_id
            $table->index(['disaster_id', 'is_read']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('alerts', function (Blueprint $table) {
            $table->dropForeign(['disaster_id']);
            $table->dropIndex(['disaster_id', 'is_read']);
            $table->dropColumn('disaster_id');
        });
    }
};
