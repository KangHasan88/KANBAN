<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('gantt_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('board_id')->constrained()->onDelete('cascade');
            $table->string('view_mode')->default('day'); // day, week, month, quarter, year
            $table->integer('zoom_level')->default(1);
            $table->boolean('show_weekends')->default(true);
            $table->boolean('show_progress')->default(true);
            $table->boolean('show_dependencies')->default(false);
            $table->json('filters')->nullable(); // store filter preferences
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('gantt_settings');
    }
};