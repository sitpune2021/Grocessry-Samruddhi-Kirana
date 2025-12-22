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
        Schema::table('products', function (Blueprint $table) {

            // Brand relation
            $table->unsignedBigInteger('brand_id')
                ->nullable()
                ->after('category_id');

            $table->foreign('brand_id')
                ->references('id')
                ->on('brands')
                ->onDelete('cascade');

            // Dates (MUST be nullable initially)
            $table->date('effective_date')
                ->nullable()
                ->after('description');

            $table->date('expiry_date')
                ->nullable()
                ->after('effective_date');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign('products_brand_id_foreign');
            $table->dropColumn(['brand_id', 'effective_date', 'expiry_date']);
        });
    }
};
