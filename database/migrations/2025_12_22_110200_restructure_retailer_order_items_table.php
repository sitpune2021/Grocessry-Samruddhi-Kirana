<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up()
    {
        Schema::table('retailer_order_items', function (Blueprint $table) {

            // ❌ rename column
            $table->renameColumn('order_id', 'retailer_order_id');

            // ❌ rename qty → quantity
            $table->renameColumn('qty', 'quantity');

            // ✅ add new columns
            $table->foreignId('category_id')
                  ->after('retailer_order_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->decimal('total', 10, 2)
                  ->after('price');

        });
    }

    public function down()
    {
        Schema::table('retailer_order_items', function (Blueprint $table) {

            $table->renameColumn('retailer_order_id', 'order_id');
            $table->renameColumn('quantity', 'qty');

            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');
            $table->dropColumn('total');
        });
    }
};
