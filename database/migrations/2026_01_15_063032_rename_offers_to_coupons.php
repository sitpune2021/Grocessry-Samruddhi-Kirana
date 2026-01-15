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
        // 1. Drop old coupons table if it exists
        if (Schema::hasTable('coupons')) {
            Schema::drop('coupons');
        }

        // 2. Rename offers table to coupons
        if (Schema::hasTable('offers')) {
            Schema::rename('offers', 'coupons');
        }
    }

    public function down(): void
    {
        // Rollback: rename coupons back to offers
        if (Schema::hasTable('coupons')) {
            Schema::rename('coupons', 'offers');
        }
    }
};
