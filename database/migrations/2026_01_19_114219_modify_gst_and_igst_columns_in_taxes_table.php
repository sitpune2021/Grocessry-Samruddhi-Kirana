<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('taxes', function (Blueprint $table) {
          
            $table->decimal('igst', 5, 2)
                  ->nullable()
                  ->change();
 
            $table->decimal('gst', 5, 2)
                  ->nullable(false)
                  ->change();
        });
    }

    public function down(): void
    {
        Schema::table('taxes', function (Blueprint $table) {
         
            $table->decimal('igst', 5, 2)
                  ->nullable(false)
                  ->change();

            $table->decimal('gst', 5, 2)
                  ->nullable()
                  ->change();
        });
    }
};
