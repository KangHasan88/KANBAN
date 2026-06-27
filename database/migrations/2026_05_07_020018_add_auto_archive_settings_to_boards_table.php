<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('boards', function (Blueprint $table) {
            $table->boolean('auto_archive_enabled')->default(false)->after('description');
            $table->integer('auto_archive_days')->default(7)->after('auto_archive_enabled');
            $table->string('auto_archive_list_name')->default('Done')->after('auto_archive_days');
        });
    }

    public function down()
    {
        Schema::table('boards', function (Blueprint $table) {
            $table->dropColumn(['auto_archive_enabled', 'auto_archive_days', 'auto_archive_list_name']);
        });
    }
};