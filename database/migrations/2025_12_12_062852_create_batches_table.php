<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('batches', function (Blueprint $table) {
            $table->id();

            // Relations
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('supplier_id')->nullable();

            // Batch Details
            $table->string('batch_no')->nullable();
            $table->date('expiry_date')->nullable();
            $table->integer('quantity')->default(0);

            // Pricing
            $table->decimal('purchase_price', 8, 2)->nullable();
            $table->decimal('mrp', 8, 2)->nullable();

            // Status (1 = active, 0 = expired/disabled)
            $table->boolean('status')->default(1);

            $table->timestamps();
            $table->softDeletes();

            // Foreign Keys
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('batches');
    }
};
