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
        Schema::create('stock_transfers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('from_warehouse');
            $table->unsignedBigInteger('to_warehouse');
            $table->enum('status',['pending','approved','in_transit','completed','rejected']);
            $table->unsignedBigInteger('driver_id')->nullable();
            $table->string('vehicle_no')->nullable();
            $table->string('otp')->nullable();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_transfers');
    }
};
