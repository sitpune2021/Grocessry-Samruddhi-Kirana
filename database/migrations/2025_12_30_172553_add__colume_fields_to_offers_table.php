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
        Schema::table('offers', function (Blueprint $table) {
            $table->string('code')->after('title');
            $table->decimal('min_amount', 10, 2)->nullable()->after('end_date');
            $table->integer('max_usage')->nullable()->after('min_amount');
            $table->text('terms_condition')->nullable()->after('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('offers', function (Blueprint $table) {
            $table->dropColumn([
                'code',
                'min_amount',
                'max_usage',
                'terms_condition'
            ]);
        });
    }
};
