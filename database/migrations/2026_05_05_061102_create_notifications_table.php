<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // mention, comment, assign, etc
            $table->string('title');
            $table->text('message');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // receiver
            $table->foreignId('from_user_id')->nullable()->constrained('users')->onDelete('set null'); // sender
            $table->foreignId('task_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('board_id')->nullable()->constrained()->onDelete('cascade');
            $table->boolean('is_read')->default(false);
            $table->json('data')->nullable(); // additional data
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('notifications');
    }
};