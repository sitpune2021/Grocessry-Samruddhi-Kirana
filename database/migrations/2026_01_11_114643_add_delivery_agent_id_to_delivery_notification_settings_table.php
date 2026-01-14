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
        Schema::table('delivery_notification_settings', function (Blueprint $table) {
            $table->foreignId('delivery_agent_id')
                ->after('id')
                ->constrained('users')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('delivery_notification_settings', function (Blueprint $table) {
            $table->dropForeign(['delivery_agent_id']);
            $table->dropColumn('delivery_agent_id');
        });
    }
};
