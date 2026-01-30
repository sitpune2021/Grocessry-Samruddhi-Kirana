<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('on_sale_products', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('product_batch_id');
            $table->unsignedBigInteger('warehouse_id');

            $table->decimal('mrp', 10, 2);
            $table->decimal('original_price', 10, 2);
            $table->decimal('sale_price', 10, 2);
            $table->unsignedTinyInteger('discount_percent');

            $table->date('sale_start_date');
            $table->date('sale_end_date');

            $table->enum('channel', ['online','offline'])->default('online');
            $table->enum('status', ['active', 'expired', 'disabled'])->default('active');

            $table->timestamps();

            /* ---------- Indexes ---------- */
            $table->index(['product_id']);
            $table->index(['product_batch_id']);
            $table->index(['warehouse_id']);
            $table->index(['status']);

            $table->foreign('product_id')->references('id')->on('products');
            $table->foreign('product_batch_id')->references('id')->on('product_batches');
            $table->foreign('warehouse_id')->references('id')->on('warehouses');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('on_sale_products');
    }
};
