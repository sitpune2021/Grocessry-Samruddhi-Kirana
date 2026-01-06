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
        Schema::create('taxes', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // GST 5%, GST 12%, etc.
            $table->decimal('cgst', 5, 2)->default(0); // CGST percentage
            $table->decimal('sgst', 5, 2)->default(0); // SGST percentage
            $table->decimal('igst', 5, 2)->default(0); // IGST percentage
            $table->boolean('is_active')->default(1); // Active flag
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('taxes');
    }
};
