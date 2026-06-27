<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('time_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('paused_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->integer('total_seconds')->default(0);
            $table->enum('status', ['running', 'paused', 'stopped'])->default('stopped');
            $table->text('note')->nullable();
            $table->timestamps();
            
            $table->index(['task_id', 'user_id', 'status']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('time_entries');
    }
};