<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('taluka_transfers', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('from_warehouse_id');
            $table->unsignedBigInteger('to_warehouse_id');

            $table->foreignId('category_id')
                  ->constrained('categories')
                  ->cascadeOnDelete();

            $table->foreignId('product_id')
                  ->constrained('products')
                  ->cascadeOnDelete();

            $table->foreignId('batch_id')
                  ->constrained('product_batches')
                  ->cascadeOnDelete();

            $table->integer('quantity');

            $table->tinyInteger('status')->default(0);

            $table->softDeletes();
            $table->timestamps();

            // Optional foreign keys for warehouse
            $table->foreign('from_warehouse_id')
                  ->references('id')->on('warehouses')
                  ->cascadeOnDelete();

            $table->foreign('to_warehouse_id')
                  ->references('id')->on('warehouses')
                  ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('taluka_transfers');
    }
};
