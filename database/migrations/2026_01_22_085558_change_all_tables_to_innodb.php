<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        $tables = DB::select("
            SELECT table_name 
            FROM information_schema.tables 
            WHERE table_schema = DATABASE()
        ");

        foreach ($tables as $table) {
            DB::statement("ALTER TABLE `{$table->table_name}` ENGINE=InnoDB");
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down()
    {
        // no rollback
    }
};
