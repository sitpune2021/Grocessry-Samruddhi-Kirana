<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::table('warehouse_stock', function (Blueprint $table) {
            // 1. Drop the foreign key constraint first
            $table->dropForeign('warehouse_stock_supplier_challan_id_foreign');

            // 2. Drop the index (Laravel's dropForeign sometimes leaves the index behind)
            $table->dropIndex('warehouse_stock_supplier_challan_id_foreign');
        });

        // 3. Now modify the columns to TEXT
        DB::statement("
            ALTER TABLE warehouse_stock 
            MODIFY supplier_challan_id TEXT NULL,
            MODIFY batch_no TEXT NULL
        ");
    }

    public function down()
    {
        // To go back, we must change TEXT back to BIGINT before adding a FK
        DB::statement("
            ALTER TABLE warehouse_stock 
            MODIFY supplier_challan_id BIGINT UNSIGNED NULL,
            MODIFY batch_no VARCHAR(191) NULL
        ");

        Schema::table('warehouse_stock', function (Blueprint $table) {
            // Re-add the foreign key (this automatically creates the index)
            $table->foreign('supplier_challan_id')
                ->references('id')
                ->on('supplier_challans') // Ensure this table name is correct
                ->onDelete('set null');
        });
    }
};
