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
        Schema::create('expiry_alert_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_batch_id')->constrained();
            $table->date('alert_date');
            $table->timestamps();

            $table->unique(['product_batch_id', 'alert_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expiry_alert_logs');
    }
};
