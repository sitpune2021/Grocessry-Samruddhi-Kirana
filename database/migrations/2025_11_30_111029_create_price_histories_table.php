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
        Schema::create('price_histories', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('product_id');
    $table->decimal('customer_price',10,2);
    $table->decimal('retailer_price',10,2);
    $table->unsignedBigInteger('changed_by')->nullable();
    $table->dateTime('effective_from');
    $table->dateTime('effective_to')->nullable();
    $table->timestamps();

    $table->index('product_id');
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('price_histories');
    }
};
