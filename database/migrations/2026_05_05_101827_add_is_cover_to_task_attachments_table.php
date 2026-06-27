<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('task_attachments', function (Blueprint $table) {
            $table->boolean('is_cover')->default(false)->after('file_type');
        });
    }

    public function down()
    {
        Schema::table('task_attachments', function (Blueprint $table) {
            $table->dropColumn('is_cover');
        });
    }
};