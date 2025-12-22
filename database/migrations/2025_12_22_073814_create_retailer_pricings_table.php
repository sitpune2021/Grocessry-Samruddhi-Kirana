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
        Schema::create('retailer_pricings', function (Blueprint $table) {
    $table->id();
    $table->foreignId('retailer_id')->constrained()->cascadeOnDelete();
    $table->foreignId('product_id')->constrained()->cascadeOnDelete();
    $table->foreignId('category_id')->constrained()->cascadeOnDelete();

    $table->decimal('base_price', 10, 2);
    $table->decimal('discount_percent', 5, 2)->default(0);
    $table->decimal('discount_amount', 10, 2)->default(0);

    $table->decimal('effective_price', 10, 2);

    $table->date('effective_from');
    $table->date('effective_to')->nullable();

    $table->boolean('is_active')->default(1);

    $table->timestamps();

    // prevent duplicate active pricing
    $table->unique(['retailer_id', 'product_id', 'effective_from']);
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('retailer_pricings');
    }
};
