<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('user_addresses', function (Blueprint $table) {
            $table->string('flat_house')->after('last_name');
            $table->string('floor')->nullable()->after('flat_house');
            $table->string('area')->after('floor');
        });
    }

    public function down(): void
    {
        Schema::table('user_addresses', function (Blueprint $table) {
            $table->dropColumn(['flat_house', 'floor', 'area']);
        });
    }
};

