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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable(); // null => system/broadcast
            $table->string('title');
            $table->text('message');
            $table->json('data')->nullable(); // additional payload
            $table->enum('channel',['in_app','sms','email','whatsapp'])->default('in_app');
            $table->boolean('read')->default(false);
            $table->timestamps();

            $table->index('user_id');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
