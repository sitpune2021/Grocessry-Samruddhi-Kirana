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
        Schema::create('units', function (Blueprint $table) {
            $table->id();

            $table->string('name')->comment('Unit full name e.g. Kilogram');
            $table->string('short_name')->comment('Unit short name e.g. KG');

            $table->unsignedBigInteger('created_by')->nullable()
                ->comment('User who created the unit');

            $table->timestamps();

            // Unique constraints
            $table->unique('name');
            $table->unique('short_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};
