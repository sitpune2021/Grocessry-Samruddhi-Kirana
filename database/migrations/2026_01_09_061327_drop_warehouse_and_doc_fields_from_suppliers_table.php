<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   
    public function up(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {

            if (Schema::hasColumn('suppliers', 'warehouse_id')) {
                $table->dropForeign(['warehouse_id']);
                $table->dropColumn('warehouse_id');
            }

            if (Schema::hasColumn('suppliers', 'batch_no')) {
                $table->dropColumn('batch_no');
            }

            if (Schema::hasColumn('suppliers', 'bill_no')) {
                $table->dropColumn('bill_no');
            }

            if (Schema::hasColumn('suppliers', 'challan_no')) {
                $table->dropColumn('challan_no');
            }
        });
    }

    public function down(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {

            $table->foreignId('warehouse_id')
                ->nullable()
                ->constrained('warehouses')
                ->cascadeOnDelete();

            $table->string('batch_no')->nullable();
            $table->string('bill_no')->nullable();
            $table->string('challan_no')->nullable();
        });
    }

};
