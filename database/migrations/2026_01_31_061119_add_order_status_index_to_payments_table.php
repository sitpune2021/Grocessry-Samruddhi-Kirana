<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->index(['order_id', 'status'], 'idx_order_status');
            $table->unsignedInteger('attempt_no')
                ->default(1)
                ->after('payment_id');
            $table->string('failure_reason')
                ->nullable()
                ->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex('idx_order_status');
            $table->dropColumn('attempt_no');
            $table->dropColumn('failure_reason');
        });
    }
};
