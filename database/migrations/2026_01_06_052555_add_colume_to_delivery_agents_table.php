<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::table('delivery_agents', function (Blueprint $table) {
            $table->string('name')->nullable()->after('user_id');
            $table->string('mobile', 10)->unique()->after('name');
            $table->string('email')->nullable()->unique()->after('mobile');
            $table->string('password')->nullable()->after('email');
            $table->string('otp', 6)->nullable()->after('password');
            $table->uuid('otp_token')->nullable()->after('otp');
            $table->timestamp('otp_expiry')->nullable()->after('otp_token');
        });
    }

    public function down(): void
    {
        Schema::table('delivery_agents', function (Blueprint $table) {
            $table->dropColumn([
                'name',
                'mobile',
                'email',
                'password',
                'otp',
                'otp_token',
                'otp_expiry'
            ]);
        });
    }
};
