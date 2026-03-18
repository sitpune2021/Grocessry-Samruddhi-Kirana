<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // 🔥 Step 1: Drop foreign key
        DB::statement('ALTER TABLE warehouse_stock DROP FOREIGN KEY warehouse_stock_supplier_challan_id_foreign');

        // 🔥 Step 2: Change column types
        DB::statement('ALTER TABLE warehouse_stock 
            MODIFY supplier_challan_id TEXT NULL,
            MODIFY batch_no TEXT NULL
        ');
    }

    public function down()
    {
        // 🔙 revert columns
        DB::statement('ALTER TABLE warehouse_stock 
            MODIFY supplier_challan_id BIGINT UNSIGNED NULL,
            MODIFY batch_no VARCHAR(191) NULL
        ');

        // 🔙 re-add foreign key
        DB::statement('ALTER TABLE warehouse_stock 
            ADD CONSTRAINT warehouse_stock_supplier_challan_id_foreign 
            FOREIGN KEY (supplier_challan_id) REFERENCES supplier_challans(id)
            ON DELETE SET NULL
        ');
    }
};
