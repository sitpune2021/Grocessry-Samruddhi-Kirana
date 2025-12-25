<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('retailers', function (Blueprint $table) {
            $table->unsignedBigInteger('state_id')->nullable()->after('address');
            $table->unsignedBigInteger('district_id')->nullable()->after('state_id');
            $table->unsignedBigInteger('taluka_id')->nullable()->after('district_id');

            // Optional: indexes
            $table->index('state_id');
            $table->index('district_id');
            $table->index('taluka_id');
        });
    }

    public function down(): void
    {
        Schema::table('retailers', function (Blueprint $table) {
            $table->dropIndex(['district_id']);
            $table->dropIndex(['taluka_id']);

            $table->dropColumn(['district_id', 'taluka_id']);
        });
    }
};
