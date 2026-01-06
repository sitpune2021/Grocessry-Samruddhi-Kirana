<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {

            // Drop foreign key first
            $table->dropForeign(['warehouse_id']);

            // Then drop column
            $table->dropColumn('warehouse_id');
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {

            // Re-add column
            $table->unsignedBigInteger('warehouse_id')->nullable();

            // Re-add foreign key
            $table->foreign('warehouse_id')
                  ->references('id')
                  ->on('warehouses')
                  ->onDelete('cascade');
        });
    }
};
