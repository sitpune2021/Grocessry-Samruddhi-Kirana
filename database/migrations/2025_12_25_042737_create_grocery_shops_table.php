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
        Schema::create('grocery_shops', function (Blueprint $table) {
    $table->id();
    $table->string('shop_name');
    $table->string('owner_name')->nullable();
    $table->string('mobile_no')->nullable();

    $table->text('address')->nullable();
    $table->unsignedBigInteger('state_id')->nullable();
    $table->unsignedBigInteger('district_id')->nullable();
    $table->unsignedBigInteger('taluka_id')->nullable();
    $table->string('pincode', 10)->nullable();

    $table->enum('status', ['active', 'inactive'])->default('active');
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grocery_shops');
    }
};
