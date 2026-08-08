<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('retailers', function (Blueprint $table) {

            $table->foreignId('user_id')
                ->nullable()
                ->after('id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('shop_id')
                ->nullable()
                ->after('user_id')
                ->constrained('warehouses')
                ->cascadeOnDelete();

            $table->date('dob')->nullable()->after('address');

            $table->enum('gender', ['male', 'female'])
                ->nullable()
                ->after('dob');

            $table->string('gst_number')->nullable()->after('gender');

            $table->string('shop_name')->nullable()->after('gst_number');

            $table->foreignId('created_by')
                ->nullable()
                ->after('is_active')
                ->constrained('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('retailers', function (Blueprint $table) {

            $table->dropForeign(['user_id']);
            $table->dropForeign(['shop_id']);
            $table->dropForeign(['created_by']);

            $table->dropColumn([
                'user_id',
                'shop_id',
                'dob',
                'gender',
                'gst_number',
                'shop_name',
                'created_by'
            ]);
        });
    }
};