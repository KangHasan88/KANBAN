<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('task_activities', function (Blueprint $table) {
            $table->id();
            $table->text('action');
            $table->foreignId('user_id')->constrained();
            $table->foreignId('task_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('task_activities');
    }
};