<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration 
{
    public function up(): void
    {
        Schema::table('warehouse_transfers', function (Blueprint $table) {
            $table->tinyInteger('status')->default(0)->after('id');
        });
    }

    public function down(): void
    {
        Schema::table('warehouse_transfers', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
