<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {

            // Warehouse
            $table->unsignedBigInteger('warehouse_id')
                  ->after('id');

            // Supplier
            $table->unsignedBigInteger('supplier_id')
                  ->after('warehouse_id');

            // Foreign keys
            $table->foreign('warehouse_id')
                  ->references('id')
                  ->on('warehouses')
                  ->onDelete('cascade');

            $table->foreign('supplier_id')
                  ->references('id')
                  ->on('suppliers')
                  ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropForeign(['warehouse_id']);
            $table->dropForeign(['supplier_id']);
            $table->dropColumn(['warehouse_id', 'supplier_id']);
        });
    }
};
