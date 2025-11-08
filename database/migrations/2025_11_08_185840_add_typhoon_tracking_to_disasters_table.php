<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('disasters', function (Blueprint $table) {
            $table->integer('wind_speed')->nullable()->after('longitude');
            $table->string('wind_direction')->nullable()->after('wind_speed');
            $table->string('movement_direction')->nullable()->after('wind_direction');
            $table->decimal('movement_speed', 5, 2)->nullable()->after('movement_direction');
            $table->integer('pressure')->nullable()->after('movement_speed');
            $table->timestamp('last_updated')->nullable()->after('pressure');
        });
    }

    public function down(): void
    {
        Schema::table('disasters', function (Blueprint $table) {
            $table->dropColumn([
                'wind_speed',
                'wind_direction',
                'movement_direction',
                'movement_speed',
                'pressure',
                'last_updated',
            ]);
        });
    }
};
