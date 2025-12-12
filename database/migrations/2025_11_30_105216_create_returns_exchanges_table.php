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
        Schema::create('returns_exchanges', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('order_id');
        $table->unsignedBigInteger('order_item_id');
        $table->unsignedBigInteger('customer_id');
        $table->enum('type',['return','exchange']);
        $table->text('reason')->nullable();
        $table->enum('status',['requested','approved','rejected','collected','processed']);
        $table->decimal('refund_amount',10,2)->nullable();
        $table->unsignedBigInteger('processed_by')->nullable(); // admin id
        $table->text('notes')->nullable();
        $table->timestamps();
        $table->index('order_id');
    });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('returns_exchanges');
    }
};
