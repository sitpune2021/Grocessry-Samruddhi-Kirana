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
        Schema::create('customer_product_returns', function (Blueprint $table) {
            $table->id();

            // Relations
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->unsignedBigInteger('order_id')->nullable();
            $table->unsignedBigInteger('order_item_id')->nullable();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->unsignedBigInteger('warehouse_id')->nullable();
            $table->unsignedBigInteger('delivery_agent_id')->nullable();

            // Return info
            $table->integer('quantity')->nullable();
            $table->string('reason')->nullable();
            $table->text('comment')->nullable();

            // Status flow
            $table->string('status')->nullable();
            // RETURN_REQUESTED
            // APPROVED
            // PICKED_FOR_RETURN
            // RETURNING_TO_STORE
            // RETURNED
            // REJECTED

            // Tracking
            $table->timestamp('picked_at')->nullable();
            $table->timestamp('return_started_at')->nullable();
            $table->timestamp('returned_at')->nullable();

            // Location
            $table->decimal('start_latitude', 10, 7)->nullable();
            $table->decimal('start_longitude', 10, 7)->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Foreign keys (recommended)
            $table->foreign('customer_id')->references('id')->on('users');
            $table->foreign('order_id')->references('id')->on('orders');
            $table->foreign('order_item_id')->references('id')->on('order_items');
            $table->foreign('delivery_agent_id')->references('id')->on('users');
            $table->foreign('product_id')->references('id')->on('products');
            $table->foreign('warehouse_id')->references('id')->on('warehouses');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_product_returns');
    }
};
