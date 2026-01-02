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
        Schema::create('transfer_challans', function (Blueprint $table) {
            $table->id();
            $table->string('challan_no')->unique()->nullable();
            $table->unsignedBigInteger('from_warehouse_id')->nullable();
            $table->unsignedBigInteger('to_warehouse_id')->nullable();
            $table->date('transfer_date')->nullable();
            $table->enum('status', ['pending', 'dispatched', 'received'])->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('transfer_challans');
    }
};
