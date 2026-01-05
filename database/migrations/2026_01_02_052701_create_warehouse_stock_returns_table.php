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
        Schema::create('warehouse_stock_returns', function (Blueprint $table) {
            $table->id();

            $table->string('return_number')->unique()->nullable();

            $table->unsignedBigInteger('from_warehouse_id'); // Branch
            $table->unsignedBigInteger('to_warehouse_id');   // Main

            $table->string('return_reason');

            $table->enum('status', [
                'draft',
                'approved',
                'dispatched',
                'in_transit',
                'received',
                'closed',
                'rejected'
            ])->default('draft');

            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('approved_by')->nullable();

            $table->timestamp('dispatched_at')->nullable();
            $table->timestamp('received_at')->nullable();

            $table->text('remarks')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouse_stock_returns');
    }
};
