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
            // Drop old 'role' column if it exists
            if (Schema::hasColumn('users', 'role')) {
                $table->dropColumn('role');
            }

            // Add 'role_id' column with foreign key
            $table->unsignedBigInteger('role_id')->after('mobile')->default(8); // default = customer
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop foreign key and column
            $table->dropForeign(['role_id']);
            $table->dropColumn('role_id');

            // Add back old 'role' column
            $table->string('role')->nullable()->after('mobile');
        });
    }
};
