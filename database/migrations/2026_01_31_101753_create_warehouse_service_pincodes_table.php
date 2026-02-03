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
        Schema::create('warehouse_service_pincodes', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('warehouse_id');
    $table->string('pincode', 10);
    $table->timestamps();

    $table->foreign('warehouse_id')
          ->references('id')->on('warehouses')
          ->onDelete('cascade');

    $table->index(['pincode', 'warehouse_id']);
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouse_service_pincodes');
    }
};
