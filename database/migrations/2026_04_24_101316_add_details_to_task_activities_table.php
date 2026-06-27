<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('task_activities', function (Blueprint $table) {
            $table->string('field')->nullable()->after('action');
            $table->string('old_value')->nullable()->after('field');
            $table->string('new_value')->nullable()->after('old_value');
        });
    }

    public function down()
    {
        Schema::table('task_activities', function (Blueprint $table) {
            $table->dropColumn(['field', 'old_value', 'new_value']);
        });
    }
};