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
    Schema::table('retailer_orders', function (Blueprint $table) {

        // ❌ remove old columns (if exist)
        $table->dropColumn([
            'total_amount',
        ]);

        // ✅ new structure
        $table->string('order_no')->after('id')->unique();
        $table->enum('status', [
            'pending',
            'approved',
            'dispatched',
            'delivered',
            'cancelled'
        ])->default('pending')->change();

        $table->decimal('total_amount', 12, 2)->default(0)->after('status');
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
