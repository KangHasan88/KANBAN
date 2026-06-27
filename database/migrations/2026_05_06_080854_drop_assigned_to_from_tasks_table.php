<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('tasks', function (Blueprint $table) {
            // Hapus foreign key constraint terlebih dahulu
            $table->dropForeign(['assigned_to']);
        });
    }

    public function down()
    {
        Schema::table('tasks', function (Blueprint $table) {
            // Kembalikan foreign key jika rollback
            $table->foreign('assigned_to')->references('id')->on('users')->onDelete('set null');
        });
    }
};