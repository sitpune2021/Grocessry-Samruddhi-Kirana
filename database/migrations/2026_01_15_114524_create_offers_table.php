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
        Schema::create('offers', function (Blueprint $table) {

            $table->id();

            // Offer details
            $table->string('title');
            $table->text('description')->nullable();

            // Warehouse (offer applicable to which warehouse)
            $table->foreignId('warehouse_id')
                ->nullable()
                ->constrained('warehouses')
                ->cascadeOnDelete();

            // Offer type
            $table->enum('offer_type', ['flat_discount', 'percentage','buy_x_get_y']);

            // Buy X Get Y logic
            $table->integer('buy_quantity')->nullable();   // X
            $table->integer('get_quantity')->nullable();   // Y

            $table->foreignId('buy_product_id')
                ->nullable()
                ->constrained('products')
                ->cascadeOnDelete();

            $table->foreignId('get_product_id')
                ->nullable()
                ->constrained('products')
                ->cascadeOnDelete();

            // Discount fields
            $table->decimal('discount_value', 10, 2)->nullable();
            $table->decimal('max_discount', 10, 2)->nullable();
            $table->decimal('min_order_amount', 10, 2);

            // Validity period
            $table->date('start_date');
            $table->date('end_date');

            // Status
            $table->boolean('status')->default(true); // Active / Inactive

            $table->timestamps();

            // Indexes
            $table->index(['warehouse_id', 'status']);
            $table->index(['start_date', 'end_date']);
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offers');
    }
};
