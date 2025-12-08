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
        Schema::create('challans', function (Blueprint $table) {
        $table->id();
        $table->string('challan_no')->unique();
        $table->unsignedBigInteger('transfer_id')->nullable();
        $table->unsignedBigInteger('warehouse_from')->nullable();
        $table->unsignedBigInteger('warehouse_to')->nullable();
        $table->date('challan_date')->nullable();
        $table->decimal('total_items',10,2)->default(0);
        $table->string('status')->default('generated');
        $table->timestamps();

            $table->index('challan_no');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('challans');
    }
};
