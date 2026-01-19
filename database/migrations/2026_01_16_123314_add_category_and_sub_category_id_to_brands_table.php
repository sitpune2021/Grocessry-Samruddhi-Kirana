<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('brands', function (Blueprint $table) {

            // Category
            $table->foreignId('category_id')
                  ->nullable()
                  ->after('id')
                  ->constrained('categories')
                  ->nullOnDelete();

            // Sub Category
            $table->foreignId('sub_category_id')
                  ->nullable()
                  ->after('category_id')
                  ->constrained('sub_categories')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('brands', function (Blueprint $table) {

            // Drop FK first
            $table->dropForeign(['sub_category_id']);
            $table->dropForeign(['category_id']);

            // Drop columns
            $table->dropColumn(['sub_category_id', 'category_id']);
        });
    }
};
