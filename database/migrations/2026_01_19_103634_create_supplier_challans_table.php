<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('supplier_challans', function (Blueprint $table) {
            $table->id();

            $table->string('challan_no')->unique();

            $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
            $table->foreignId('warehouse_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();

            $table->date('challan_date');

            $table->enum('status', ['received', 'partial', 'rejected'])
                ->default('received');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supplier_challans');
    }
};
