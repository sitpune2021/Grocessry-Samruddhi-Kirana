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
      Schema::table('retailer_offers', function (Blueprint $table) {
            // Drop foreign key first
            $table->dropForeign(['retailer_id']);

            // Drop the column
            $table->dropColumn('retailer_id');

            // Add role_id and user_id as nullable
            $table->unsignedBigInteger('role_id')->nullable()->after('id');
            $table->unsignedBigInteger('user_id')->nullable()->after('role_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('retailer_offers', function (Blueprint $table) {
            // Rollback: add retailer_id first
            $table->unsignedBigInteger('retailer_id')->after('id');

            // Add foreign key constraint if needed
            $table->foreign('retailer_id')->references('id')->on('retailers')->onDelete('cascade');

            // Drop role_id and user_id
            $table->dropColumn(['role_id', 'user_id']);
        });
    }
};
