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

            $table->unsignedBigInteger('supplier_id');
            $table->unsignedBigInteger('warehouse_id');
            $table->unsignedBigInteger('created_by');

            $table->date('challan_date');

            $table->enum('status', ['received', 'partial', 'rejected'])
                ->default('received');

            $table->timestamps();

            // 🔹 Foreign Keys
            $table->foreign('supplier_id')
                ->references('id')->on('suppliers')
                ->onDelete('cascade');

            $table->foreign('warehouse_id')
                ->references('id')->on('warehouses')
                ->onDelete('cascade');

            $table->foreign('created_by')
                ->references('id')->on('users')
                ->onDelete('cascade');
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('supplier_challans');
    }
};
