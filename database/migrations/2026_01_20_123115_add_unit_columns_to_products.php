<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {

    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {

            $table->unsignedBigInteger('unit_id')->nullable()->after('name');
            $table->decimal('unit_value', 10, 2)->nullable()->after('unit_id');
        });

        DB::statement("
            UPDATE products
            SET unit_id = 5, unit_value = 1
            WHERE unit_id IS NULL
        ");

        Schema::table('products', function (Blueprint $table) {
            $table->foreign('unit_id')
                ->references('id')
                ->on('units')
                ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['unit_id']);
            $table->dropColumn(['unit_id', 'unit_value']);
        });
    }
};
