<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('customer_order_returns', function (Blueprint $table) {
            $table->enum('return_type', ['refund', 'exchange'])
                ->after('reason');

            // refund details
            $table->decimal('refund_amount', 10, 2)
                ->nullable()
                ->after('return_type');

            $table->timestamp('refunded_at')
                ->nullable()
                ->after('refund_amount');

            // exchange details
            $table->unsignedBigInteger('exchange_order_id')
                ->nullable()
                ->after('refunded_at');

            // lifecycle end
            $table->timestamp('closed_at')
                ->nullable()
                ->after('exchange_order_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_order_returns', function (Blueprint $table) {
            //
            $table->dropColumn([
                'return_type',
                'refund_amount',
                'refunded_at',
                'exchange_order_id',
                'closed_at',
            ]);
        });
    }
};
