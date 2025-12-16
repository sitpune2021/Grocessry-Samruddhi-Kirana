<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ALTER column enum to add 'transfer'
        DB::statement("ALTER TABLE stock_movements MODIFY COLUMN type ENUM('in','out','transfer') NOT NULL DEFAULT 'in'");
    }

    public function down(): void
    {
        // Rollback to previous enum
        DB::statement("ALTER TABLE stock_movements MODIFY COLUMN type ENUM('in','out') NOT NULL DEFAULT 'in'");
    }
};
