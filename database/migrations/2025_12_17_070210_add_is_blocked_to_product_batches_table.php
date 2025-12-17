<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   
    public function up()
{
    Schema::table('product_batches', function (Blueprint $table) {
        $table->boolean('is_blocked')->default(false)->after('expiry_date');
    });
}

public function down()
{
    Schema::table('product_batches', function (Blueprint $table) {
        $table->dropColumn('is_blocked');
    });
}

   
};
