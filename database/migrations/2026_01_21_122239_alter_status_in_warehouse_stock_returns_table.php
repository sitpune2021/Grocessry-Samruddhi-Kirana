<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("
            ALTER TABLE warehouse_stock_returns
            MODIFY COLUMN status ENUM(
                'draft',
                'approved',
                'dispatched',
                'in_transit',
                'received',
                'MASTER_CREATED',
                'MASTER_APPROVED',
                'MASTER_DISPATCHED',
                'MASTER_RECEIVED',
                'DISTRICT_CREATED',
                'DISTRICT_APPROVED',
                'DISTRICT_DISPATCHED',
                'DISTRICT_RECEIVED'
            ) NOT NULL DEFAULT 'draft'
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("
            ALTER TABLE warehouse_stock_returns
            MODIFY COLUMN status ENUM(
                'draft',
                'approved',
                'dispatched',
                'in_transit',
                'received',
                'MASTER_CREATED',
                'MASTER_APPROVED',
                'MASTER_DISPATCHED',
                'MASTER_RECEIVED'
            ) NOT NULL DEFAULT 'draft'
        ");
    }
};