<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('warehouse_transfers', function (Blueprint $table) {
            $table->renameColumn('from_warehouse_id', 'approved_by_warehouse_id');
            $table->renameColumn('to_warehouse_id', 'requested_by_warehouse_id');
        });
    }

    public function down()
    {
        Schema::table('warehouse_transfers', function (Blueprint $table) {
            $table->renameColumn('approved_by_warehouse_id', 'from_warehouse_id');
            $table->renameColumn('requested_by_warehouse_id', 'to_warehouse_id');
        });
    }
};
