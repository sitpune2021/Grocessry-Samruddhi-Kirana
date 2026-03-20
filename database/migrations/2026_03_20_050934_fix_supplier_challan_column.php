<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // ✅ Correct index name
        DB::statement("
            ALTER TABLE warehouse_stock 
            DROP INDEX warehouse_stock_supplier_challan_id_foreign
        ");

        // ✅ Now modify column (if needed)
        DB::statement("
            ALTER TABLE warehouse_stock 
            MODIFY supplier_challan_id TEXT NULL,
            MODIFY batch_no TEXT NULL
        ");
    }

    public function down()
    {
        DB::statement("
            ALTER TABLE warehouse_stock 
            MODIFY supplier_challan_id BIGINT UNSIGNED NULL,
            MODIFY batch_no VARCHAR(191) NULL
        ");

        // 🔙 Add index back
        DB::statement("
            ALTER TABLE warehouse_stock 
            ADD INDEX warehouse_stock_supplier_challan_id_foreign (supplier_challan_id)
        ");
    }
};