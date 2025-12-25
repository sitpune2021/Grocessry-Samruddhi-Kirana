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
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'effective_date')) {
                $table->dropColumn('effective_date');
            }

            if (Schema::hasColumn('products', 'expiry_date')) {
                $table->dropColumn('expiry_date');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->date('effective_date')->nullable()->after('description');
            $table->date('expiry_date')->nullable()->after('effective_date');
        });
    }
};
