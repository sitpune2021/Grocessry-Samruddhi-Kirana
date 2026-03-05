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
        Schema::table('supplier_challan_items', function (Blueprint $table) {

            if (!Schema::hasColumn('supplier_challan_items', 'category_id')) {
                $table->unsignedBigInteger('category_id')->nullable()->after('supplier_challan_id');
            }

            if (!Schema::hasColumn('supplier_challan_items', 'sub_category_id')) {
                $table->unsignedBigInteger('sub_category_id')->nullable()->after('category_id');
            }

            $table->decimal('rate', 10, 2)->nullable()->change();
            $table->integer('ordered_qty')->nullable()->change();
            $table->integer('received_qty')->nullable()->change();
        });

        Schema::table('supplier_challan_items', function (Blueprint $table) {

            // Drop if exists
            try {
                $table->dropForeign(['category_id']);
            } catch (\Exception $e) {
            }

            try {
                $table->dropForeign(['sub_category_id']);
            } catch (\Exception $e) {
            }

            // Add foreign keys
            $table->foreign('category_id')
                ->references('id')
                ->on('categories')
                ->cascadeOnDelete();

            $table->foreign('sub_category_id')
                ->references('id')
                ->on('sub_categories')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('supplier_challan_items', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropForeign(['sub_category_id']);

            $table->dropColumn(['category_id', 'sub_category_id']);

            $table->decimal('rate', 10, 2)->nullable(false)->change();
            $table->integer('ordered_qty')->nullable(false)->change();
            $table->integer('received_qty')->nullable(false)->default(0)->change();
        });
    }
};
