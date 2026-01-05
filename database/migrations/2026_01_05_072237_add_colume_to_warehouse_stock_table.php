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
        Schema::table('warehouse_stock', function (Blueprint $table) {
            $table->string('bill_no')->nullable()->after('quantity');
            $table->string('challan_no')->nullable()->after('bill_no');
            $table->string('batch_no')->nullable()->after('challan_no');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('warehouse_stock', function (Blueprint $table) {
            $table->dropColumn(['bill_no', 'challan_no', 'batch_no']);
        });
    }
};
