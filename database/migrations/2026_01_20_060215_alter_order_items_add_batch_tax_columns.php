<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {

            // Product batch (FIFO / expiry tracking)
            $table->unsignedBigInteger('product_batch_id')
                  ->nullable()
                  ->after('product_id');

            // Tax percent applied on this line
            $table->decimal('tax_percent', 5, 2)
                  ->nullable()
                  ->after('price');

            // Tax amount for this line
            $table->decimal('tax_amount', 10, 2)
                  ->nullable()
                  ->after('tax_percent');

            // Final line total (price + tax)
            $table->decimal('line_total', 10, 2)
                  ->nullable()
                  ->after('tax_amount');
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn([
                'product_batch_id',
                'tax_percent',
                'tax_amount',
                'line_total',
            ]);
        });
    }
};
