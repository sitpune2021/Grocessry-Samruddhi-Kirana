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
        Schema::create('retailer_cart_items', function (Blueprint $table) {

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Cart
            |--------------------------------------------------------------------------
            */
            $table->unsignedBigInteger('cart_id');

            /*
            |--------------------------------------------------------------------------
            | Product
            |--------------------------------------------------------------------------
            */
            $table->unsignedBigInteger('product_id');

            /*
            |--------------------------------------------------------------------------
            | Category
            |--------------------------------------------------------------------------
            */
            $table->unsignedBigInteger('category_id');

            /*
            |--------------------------------------------------------------------------
            | Quantity
            |--------------------------------------------------------------------------
            */
            $table->unsignedInteger('quantity');

            /*
            |--------------------------------------------------------------------------
            | Retailer Price
            |--------------------------------------------------------------------------
            */
            $table->decimal('price', 10, 2);

            /*
            |--------------------------------------------------------------------------
            | Discount
            |--------------------------------------------------------------------------
            */
            $table->decimal('discount_amount', 10, 2)
                ->default(0);

            /*
            |--------------------------------------------------------------------------
            | Total
            |--------------------------------------------------------------------------
            */
            $table->decimal('total', 10, 2);

            $table->timestamps();


            /*
            |--------------------------------------------------------------------------
            | Indexes
            |--------------------------------------------------------------------------
            */

            $table->index('cart_id');
            $table->index('product_id');
            $table->index('category_id');


            /*
            |--------------------------------------------------------------------------
            | Foreign Keys
            |--------------------------------------------------------------------------
            */

            $table->foreign('cart_id')
                ->references('id')
                ->on('retailer_carts')
                ->onDelete('cascade');

            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->onDelete('cascade');

            $table->foreign('category_id')
                ->references('id')
                ->on('categories')
                ->onDelete('cascade');


            /*
            |--------------------------------------------------------------------------
            | Duplicate Product Prevention
            |--------------------------------------------------------------------------
            |
            | Same product same cart mein duplicate row nahi banegi.
            |
            */

            $table->unique([
                'cart_id',
                'product_id'
            ]);
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('retailer_cart_items');
    }
};