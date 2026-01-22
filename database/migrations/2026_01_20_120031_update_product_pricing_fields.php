<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {

            $table->decimal('gst_amount', 10, 2)
                ->after('gst_percentage')
                ->nullable();

            $table->decimal('final_price', 10, 2)
                ->after('gst_amount')
                ->nullable();

            if (Schema::hasColumn('products', 'discount_type')) {
                $table->dropColumn('discount_type');
            }

            if (Schema::hasColumn('products', 'discount_value')) {
                $table->dropColumn('discount_value');
            }

        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {

            $table->enum('discount_type', ['percentage', 'flat'])
                ->nullable()
                ->after('retailer_price');

            $table->decimal('discount_value', 10, 2)
                ->nullable()
                ->after('discount_type');

            $table->dropColumn(['gst_amount', 'final_price']);
        });
    }
};
