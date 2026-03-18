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
        Schema::table('warehouse_stock_returns', function (Blueprint $table) {
            $table->unsignedBigInteger('received_by')
                ->nullable()
                ->after('approved_by');

            // Optional: Add foreign key
            $table->foreign('received_by')
                ->references('id')
                ->on('users')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('warehouse_stock_returns', function (Blueprint $table) {
            $table->dropForeign(['received_by']);
            $table->dropColumn('received_by');
        });
    }
};
