<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('tasks', function (Blueprint $table) {
            // Gantt fields
            if (!Schema::hasColumn('tasks', 'start_date')) {
                $table->date('start_date')->nullable()->after('due_date');
            }
            if (!Schema::hasColumn('tasks', 'duration')) {
                $table->integer('duration')->default(1)->after('start_date'); // duration in days
            }
            if (!Schema::hasColumn('tasks', 'progress')) {
                $table->integer('progress')->default(0)->after('duration'); // 0-100
            }
        });
    }

    public function down()
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn(['start_date', 'duration', 'progress']);
        });
    }
};