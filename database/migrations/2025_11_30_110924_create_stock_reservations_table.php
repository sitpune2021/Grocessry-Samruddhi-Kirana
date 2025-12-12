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
        Schema::create('stock_reservations', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('warehouse_id');
    $table->unsignedBigInteger('product_id');
    $table->unsignedBigInteger('batch_id')->nullable();
    $table->unsignedBigInteger('order_id')->nullable();
    $table->unsignedBigInteger('user_id')->nullable();
    $table->integer('qty');
    $table->dateTime('reserved_at');
    $table->dateTime('expires_at');
    $table->enum('status',['active','expired','consumed'])->default('active');
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_reservations');
    }
};
