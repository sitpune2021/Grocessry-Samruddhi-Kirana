<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('warehouse_stock_returns', function (Blueprint $table) {
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
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('warehouse_stock_returns', function (Blueprint $table) {

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

            ) NOT NULL DEFAULT 'draft'
        ");
        });
    }
};
