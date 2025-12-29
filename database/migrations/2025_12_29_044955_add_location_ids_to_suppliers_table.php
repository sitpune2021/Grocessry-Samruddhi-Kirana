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
        Schema::table('suppliers', function (Blueprint $table) {
            $table->unsignedBigInteger('state_id')->nullable()->after('id');
            $table->unsignedBigInteger('district_id')->nullable()->after('state_id');
            $table->unsignedBigInteger('taluka_id')->nullable()->after('district_id');

            // Optional foreign keys
            $table->foreign('state_id')->references('id')->on('states')->nullOnDelete();
            $table->foreign('district_id')->references('id')->on('districts')->nullOnDelete();
            $table->foreign('taluka_id')->references('id')->on('talukas')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropForeign(['state_id']);
            $table->dropForeign(['district_id']);
            $table->dropForeign(['taluka_id']);

            $table->dropColumn(['state_id', 'district_id', 'taluka_id']);
        });
    }
};
