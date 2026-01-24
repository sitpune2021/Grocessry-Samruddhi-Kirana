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
            $table->unsignedBigInteger('supplier_challan_id')->nullable()->after('warehouse_id');

            $table->foreign('supplier_challan_id')
                ->references('id')
                ->on('supplier_challans')
                ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('warehouse_stock', function (Blueprint $table) {
            $table->dropForeign(['supplier_challan_id']);
            $table->dropColumn('supplier_challan_id');
        });
    }
};
