<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Tabel custom fields definition per board
        Schema::create('custom_fields', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('label');
            $table->enum('type', ['text', 'number', 'date', 'dropdown', 'checkbox', 'textarea']);
            $table->json('options')->nullable();
            $table->boolean('required')->default(false);
            $table->integer('order')->default(0);
            $table->foreignId('board_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        // Tabel custom field values for tasks
        Schema::create('task_custom_field_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained()->onDelete('cascade');
            $table->foreignId('custom_field_id')->constrained()->onDelete('cascade');
            $table->text('value')->nullable();
            $table->timestamps();
            
            $table->unique(['task_id', 'custom_field_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('task_custom_field_values');
        Schema::dropIfExists('custom_fields');
    }
};