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
        Schema::create('customer_order_returns', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('order_item_id')->nullable();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('customer_id');

            $table->unsignedInteger('quantity');

            $table->string('reason')->nullable();

            // Return process status
            $table->enum('status', [
                'requested',
                'approved',
                'rejected',
                'received',
                'refunded'
            ])->default('requested');

            // Quality check status
            $table->enum('qc_status', [
                'pending',
                'passed',
                'failed'
            ])->default('pending');

            $table->timestamp('received_at')->nullable();

            $table->timestamps();

            // Indexes
            $table->index('order_id');
            $table->index('product_id');
            $table->index('customer_id');

            // Foreign keys (optional – enable if tables exist)
            /*
            $table->foreign('order_id')->references('id')->on('customer_orders')->cascadeOnDelete();
            $table->foreign('order_item_id')->references('id')->on('customer_order_items')->nullOnDelete();
            $table->foreign('product_id')->references('id')->on('products')->restrictOnDelete();
            $table->foreign('customer_id')->references('id')->on('users')->restrictOnDelete();
            */
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_order_returns');
    }
};
