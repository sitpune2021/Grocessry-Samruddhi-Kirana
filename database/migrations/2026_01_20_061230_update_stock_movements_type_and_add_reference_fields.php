<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stock_movements', function (Blueprint $table) {

            $table->enum('type', [
                'order',
                'purchase',
                'return',
                'adjustment',
                'in',
                'out',
                'dispatch',
                'transfer',
            ])->change();

            $table->unsignedBigInteger('reference_id')
                  ->nullable()
                  ->after('type');

            $table->unsignedBigInteger('created_by')
                  ->nullable()
                  ->after('warehouse_id');
        });
    }

    public function down(): void
    {
        Schema::table('stock_movements', function (Blueprint $table) {

           $table->enum('type', [
                'order',
                'purchase',
                'return',
                'adjustment',
            ])->change();

            $table->dropColumn([
                'reference_id',
                'created_by',
            ]);
        });
    }
};
