<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('warehouse_stock', function (Blueprint $table) {
            $table->foreignId('category_id')
                  ->after('warehouse_id')
                  ->constrained('categories')
                  ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('warehouse_stock', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');
        });
    }
};
