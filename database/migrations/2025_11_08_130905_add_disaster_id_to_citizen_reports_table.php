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
        Schema::table('citizen_reports', function (Blueprint $table) {
            $table->foreignId('disaster_id')->nullable()->after('user_id')->constrained()->onDelete('cascade');
            $table->enum('report_type', ['felt_tremor', 'infrastructure_damage', 'safety_update', 'casualty', 'resource_need', 'other'])->after('disaster_id')->default('other');

            // Make these fields nullable since they'll come from the disaster
            $table->enum('type', ['flood', 'typhoon', 'fire', 'earthquake', 'other'])->nullable()->change();
            $table->string('name')->nullable()->change();
            $table->decimal('latitude', 10, 6)->nullable()->change();
            $table->decimal('longitude', 10, 6)->nullable()->change();
            $table->string('location')->nullable()->change();
            $table->enum('severity', ['low', 'moderate', 'high', 'critical'])->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('citizen_reports', function (Blueprint $table) {
            $table->dropForeign(['disaster_id']);
            $table->dropColumn(['disaster_id', 'report_type']);
        });
    }
};
