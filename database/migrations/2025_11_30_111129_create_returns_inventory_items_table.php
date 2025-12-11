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
        Schema::create('returns_inventory_items', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('return_id');
    $table->unsignedBigInteger('product_id');
    $table->unsignedBigInteger('batch_id')->nullable();
    $table->integer('qty');
    $table->enum('condition',['good','damaged']);
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('returns_inventory_items');
    }
};
