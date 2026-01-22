<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('warehouse_transfers', function (Blueprint $table) {
            $table->unsignedBigInteger('challan_id')
                  ->nullable()
                  ->after('batch_id');
        });
    }

    public function down()
    {
        Schema::table('warehouse_transfers', function (Blueprint $table) {
            $table->dropColumn('challan_id');
        });
    }
};
