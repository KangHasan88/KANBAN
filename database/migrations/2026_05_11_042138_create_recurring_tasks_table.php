<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('recurring_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained()->onDelete('cascade');
            $table->enum('frequency', ['daily', 'weekly', 'monthly', 'yearly', 'custom']);
            $table->integer('interval')->default(1);
            $table->json('week_days')->nullable(); // Untuk weekly: [1,2,3,4,5] (Senin-Jumat)
            $table->json('month_days')->nullable(); // Untuk monthly: [1,15] (tanggal 1 dan 15)
            $table->date('until_date')->nullable(); // Berhenti setelah tanggal tertentu
            $table->integer('occurrences')->nullable(); // Berhenti setelah berapa kali
            $table->integer('occurrences_count')->default(0); // Sudah berapa kali generate
            $table->date('last_generated_at')->nullable(); // Terakhir generate
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('recurring_tasks');
    }
};