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
        Schema::table('cart_items', function (Blueprint $table) {
            if (!Schema::hasColumn('cart_items', 'cgst_amount')) {
                $table->decimal('cgst_amount', 10, 2)
                    ->default(0)
                    ->after('price');
            }

            if (!Schema::hasColumn('cart_items', 'sgst_amount')) {
                $table->decimal('sgst_amount', 10, 2)
                    ->default(0)
                    ->after('cgst_amount');
            }

            if (!Schema::hasColumn('cart_items', 'tax_total')) {
                $table->decimal('tax_total', 10, 2)
                    ->default(0)
                    ->after('sgst_amount');
            }

            if (!Schema::hasColumn('cart_items', 'item_total')) {
                $table->decimal('item_total', 10, 2)
                    ->default(0)
                    ->after('tax_total');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            if (Schema::hasColumn('cart_items', 'cgst_amount')) {
                $table->dropColumn('cgst_amount');
            }
            if (Schema::hasColumn('cart_items', 'sgst_amount')) {
                $table->dropColumn('sgst_amount');
            }
            if (Schema::hasColumn('cart_items', 'tax_total')) {
                $table->dropColumn('tax_total');
            }
            if (Schema::hasColumn('cart_items', 'item_total')) {
                $table->dropColumn('item_total');
            }
        });
    }
};
