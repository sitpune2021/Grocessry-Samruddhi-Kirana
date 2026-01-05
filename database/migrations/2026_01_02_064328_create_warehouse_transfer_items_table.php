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
        Schema::create('warehouse_transfer_items', function (Blueprint $table) {
    $table->id();

    $table->foreignId('warehouse_transfer_request_id')
          ->constrained('warehouse_transfer_requests')
          ->onDelete('cascade');

    $table->foreignId('product_id')->constrained('products');

    $table->integer('requested_qty');
    $table->integer('approved_qty')->nullable();

    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouse_transfer_items');
    }
};
