<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {

            // Order source
            $table->enum('channel', ['web', 'app', 'pos'])
                  ->after('order_number');

            // Creator (admin / cashier)
            $table->unsignedBigInteger('created_by')
                  ->nullable()
                  ->after('user_id');

            // Warehouse
            $table->unsignedBigInteger('warehouse_id')
                  ->after('user_id');

            // Payment status
            $table->enum('payment_status', ['pending', 'paid', 'failed'])
                  ->default('pending');

            // Order type
            $table->enum('order_type', ['delivery', 'pickup', 'walkin'])
                  ->default('delivery');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'channel',
                'created_by',
                'warehouse_id',
                'payment_status',
                'order_type',
            ]);
        });
    }
};
