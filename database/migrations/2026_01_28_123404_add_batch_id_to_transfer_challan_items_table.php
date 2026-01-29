<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transfer_challan_items', function (Blueprint $table) {
            $table->unsignedBigInteger('batch_no')->nullable()->after('product_id');

            // If you want foreign key (recommended)
            $table->foreign('batch_no')->references('id')->on('product_batches')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('transfer_challan_items', function (Blueprint $table) {
            $table->dropForeign(['batch_no']);
            $table->dropColumn('batch_no');
        });
    }
};
