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
        Schema::create('product_movements', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('product_id');
    $table->unsignedBigInteger('batch_id')->nullable();
    $table->unsignedBigInteger('from_warehouse')->nullable();
    $table->unsignedBigInteger('to_warehouse')->nullable();
    $table->integer('quantity');
    $table->enum('movement_type',['in','out','transfer','adjustment']);
    $table->unsignedBigInteger('reference_id')->nullable(); // e.g. order_id/transfer_id
    $table->unsignedBigInteger('performed_by')->nullable();
    $table->timestamps();

    $table->index(['product_id','batch_id']);
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_movements');
    }
};
