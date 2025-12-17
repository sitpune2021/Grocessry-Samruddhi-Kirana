<?php

// database/migrations/xxxx_xx_xx_add_soft_deletes_to_product_batches.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('product_batches', function (Blueprint $table) {
            $table->softDeletes(); // adds deleted_at
        });
    }

    public function down(): void
    {
        Schema::table('product_batches', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
