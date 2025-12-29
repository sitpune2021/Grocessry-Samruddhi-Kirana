<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('product_batches', function (Blueprint $table) {
        $table->unsignedBigInteger('sub_category_id')
              ->nullable()
              ->after('category_id');

        $table->foreign('sub_category_id')
              ->references('id')
              ->on('sub_categories');
    });
}

public function down()
{
    Schema::table('product_batches', function (Blueprint $table) {
        $table->dropForeign(['sub_category_id']);
        $table->dropColumn('sub_category_id');
    });
}

};
