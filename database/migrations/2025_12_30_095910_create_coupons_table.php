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
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->enum('type', ['flat', 'percent', 'free_shipping']);
            $table->decimal('value', 8, 2);
            $table->decimal('min_cart_amount', 8, 2)->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('usage_limit')->nullable();
            $table->integer('per_user_limit')->default(1);
            $table->enum('is_active', [0,1])->default('1')->comment('0=inactive,1=active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
