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
        Schema::create('products', function (Blueprint $table) {
    $table->id();

    $table->unsignedBigInteger('category_id');

    $table->string('name');
    $table->string('sku')->nullable();
    $table->text('description')->nullable();

    $table->decimal('base_price', 10, 2)->default(0);
    $table->decimal('retailer_price', 10, 2)->default(0);
    $table->decimal('mrp', 10, 2)->nullable();

    $table->decimal('gst_percentage', 5, 2)->default(0);

    $table->integer('stock')->default(0);

    $table->timestamps();

    $table->foreign('category_id')
        ->references('id')
        ->on('categories')
        ->onDelete('cascade');
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
