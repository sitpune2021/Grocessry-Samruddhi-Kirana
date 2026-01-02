<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('warehouse_stock_return_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('stock_return_id');
            $table->unsignedBigInteger('product_id');

            $table->string('batch_no')->nullable();

            $table->integer('return_qty');
            $table->integer('received_qty')->default(0);
            $table->integer('damaged_qty')->default(0);

            $table->enum('condition', [
                'good',
                'damaged',
                'expired'
            ])->default('good');

            // ✅ Product image (return proof / damage proof)
            $table->string('product_image')->nullable();
            // OR if you want multiple images:
            // $table->json('product_images')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouse_stock_return_items');
    }
};
