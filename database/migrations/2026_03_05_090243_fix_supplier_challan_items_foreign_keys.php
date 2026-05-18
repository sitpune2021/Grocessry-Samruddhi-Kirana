<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('supplier_challan_items', function (Blueprint $table) {

            if (!Schema::hasColumn('supplier_challan_items', 'category_id')) {
                $table->unsignedBigInteger('category_id')
                    ->nullable()
                    ->after('supplier_challan_id');
            }

            if (!Schema::hasColumn('supplier_challan_items', 'sub_category_id')) {
                $table->unsignedBigInteger('sub_category_id')
                    ->nullable()
                    ->after('category_id');
            }

            $table->decimal('rate', 10, 2)->nullable()->change();
            $table->integer('ordered_qty')->nullable()->change();
            $table->integer('received_qty')->nullable()->change();
        });

        // SAFELY DROP FK IF EXISTS
        $this->dropForeignIfExists(
            'supplier_challan_items',
            'supplier_challan_items_category_id_foreign'
        );

        $this->dropForeignIfExists(
            'supplier_challan_items',
            'supplier_challan_items_sub_category_id_foreign'
        );

        Schema::table('supplier_challan_items', function (Blueprint $table) {

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

    public function down(): void
    {
        $this->dropForeignIfExists(
            'supplier_challan_items',
            'supplier_challan_items_category_id_foreign'
        );

        $this->dropForeignIfExists(
            'supplier_challan_items',
            'supplier_challan_items_sub_category_id_foreign'
        );

        Schema::table('supplier_challan_items', function (Blueprint $table) {

            if (Schema::hasColumn('supplier_challan_items', 'category_id')) {
                $table->dropColumn('category_id');
            }

            if (Schema::hasColumn('supplier_challan_items', 'sub_category_id')) {
                $table->dropColumn('sub_category_id');
            }

            $table->decimal('rate', 10, 2)->nullable(false)->change();
            $table->integer('ordered_qty')->nullable(false)->change();
            $table->integer('received_qty')->nullable(false)->default(0)->change();
        });
    }

    private function dropForeignIfExists($table, $foreignKey)
    {
        $database = DB::getDatabaseName();

        $exists = DB::select("
            SELECT CONSTRAINT_NAME
            FROM information_schema.TABLE_CONSTRAINTS
            WHERE TABLE_SCHEMA = ?
            AND TABLE_NAME = ?
            AND CONSTRAINT_NAME = ?
        ", [$database, $table, $foreignKey]);

        if (!empty($exists)) {
            DB::statement("ALTER TABLE `$table` DROP FOREIGN KEY `$foreignKey`");
        }
    }
};