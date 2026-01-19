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
        Schema::table('users', function (Blueprint $table) {
            $table->dateTime('duty_start_time')
                ->nullable()
                ->after('is_online');

            $table->integer('total_duty_minutes')
                ->default(0)->nullable()
                ->after('duty_start_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('duty_start_time');

            $table->dropColumn('total_duty_minutes');
        });
    }
};
