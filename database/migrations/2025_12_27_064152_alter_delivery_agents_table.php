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
        Schema::table('delivery_agents', function (Blueprint $table) {

            // Drop old columns (User related)
            $table->dropColumn([
                'name',
                'mobile',
                'email',
                'aadhaar_no',
                'pan_no',
            ]);

            // Add new columns
            $table->unsignedBigInteger('user_id')
                ->unique()
                ->after('id');

            $table->date('dob')->nullable()->after('user_id');

            $table->enum('gender', ['male', 'female'])
                ->nullable()
                ->after('dob');

            $table->string('aadhaar_card')
                ->nullable()
                ->after('gender');

            $table->string('driving_license')
                ->nullable()
                ->after('aadhaar_card');

            // Foreign key
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('delivery_agents', function (Blueprint $table) {

            // Drop foreign key & column
            $table->dropForeign(['user_id']);
            $table->dropColumn([
                'user_id',
                'dob',
                'gender',
                'aadhaar_card',
                'driving_license',
            ]);

            // Restore removed columns
            $table->string('name')->after('id');
            $table->string('mobile', 15)->unique()->after('name');
            $table->string('email')->nullable()->unique()->after('mobile');
            $table->string('aadhaar_no', 20)->nullable()->unique()->after('email');
            $table->string('pan_no', 20)->nullable()->unique()->after('aadhaar_no');
        });
    }
};
