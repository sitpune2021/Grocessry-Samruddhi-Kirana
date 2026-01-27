<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {

    public function up(): void
    {
        /**
         * STEP 1:
         * Add NORMAL index so FK has support
         */
        DB::statement("
            CREATE INDEX idx_challan_product
            ON warehouse_stock (supplier_challan_id, product_id)
        ");

        /**
         * STEP 2:
         * Drop WRONG unique index (now FK is safe)
         */
        DB::statement("
            ALTER TABLE warehouse_stock
            DROP INDEX uniq_challan_product
        ");

        /**
         * STEP 3:
         * Add CORRECT unique index
         */
        DB::statement("
            ALTER TABLE warehouse_stock
            ADD UNIQUE uniq_challan_warehouse (supplier_challan_id, warehouse_id)
        ");
    }

    public function down(): void
    {
        /**
         * Rollback safely
         */
        DB::statement("
            ALTER TABLE warehouse_stock
            DROP INDEX uniq_challan_warehouse
        ");

        DB::statement("
            DROP INDEX idx_challan_product ON warehouse_stock
        ");

        DB::statement("
            ALTER TABLE warehouse_stock
            ADD UNIQUE uniq_challan_product (supplier_challan_id, product_id)
        ");
    }
};
