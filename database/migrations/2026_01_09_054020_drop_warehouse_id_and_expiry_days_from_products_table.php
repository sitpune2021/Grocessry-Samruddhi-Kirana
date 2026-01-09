<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {

            // Drop foreign key first if exists
            if (Schema::hasColumn('products', 'warehouse_id')) {
                $table->dropForeign(['warehouse_id']);
                $table->dropColumn('warehouse_id');
            }

            // Drop expiry_days
            if (Schema::hasColumn('products', 'expiry_days')) {
                $table->dropColumn('expiry_days');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {

            // Restore warehouse_id
            $table->foreignId('warehouse_id')
                ->nullable()
                ->constrained('warehouses')
                ->cascadeOnDelete();

            // Restore expiry_days
            $table->integer('expiry_days')->nullable();
        });
    }
};
