<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('retailer_carts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('retailer_id')
                ->constrained('retailers')
                ->cascadeOnDelete();

            $table->foreignId('warehouse_id')
                ->constrained('warehouses')
                ->cascadeOnDelete();

            $table->string('status')->default('active');

            $table->timestamps();

            $table->index(['retailer_id', 'warehouse_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('retailer_carts');
    }
};