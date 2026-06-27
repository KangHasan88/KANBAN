<?php

use Illuminate\Support\Facades\Schedule;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ==============================================
// RECURRING TASKS SCHEDULE
// ==============================================

// Generate recurring tasks setiap hari jam 00:00
Schedule::command('recurring-tasks:generate')
    ->dailyAt('00:00')
    ->before(function () {
        Log::info('[Schedule] Recurring tasks generator started');
    })
    ->after(function () {
        Log::info('[Schedule] Recurring tasks generator finished');
    });

// ==============================================
// AUTO ARCHIVE SCHEDULE
// ==============================================

// Jalankan auto archive setiap jam
Schedule::command('kanban:auto-archive')
    ->hourly()
    ->before(function () {
        Log::info('[Schedule] Auto archive started');
    })
    ->after(function () {
        Log::info('[Schedule] Auto archive finished');
    });

// Alternatif: jalankan setiap 6 jam (uncomment jika ingin)
// Schedule::command('kanban:auto-archive')->everySixHours();

// Alternatif: jalankan setiap hari jam 2 pagi (uncomment jika ingin)
// Schedule::command('kanban:auto-archive')->dailyAt('02:00');

// ==============================================
// DUE DATE REMINDERS SCHEDULE
// ==============================================

// Kirim reminder setiap hari jam 8 pagi
Schedule::command('kanban:due-date-reminders')
    ->dailyAt('08:00')
    ->before(function () {
        Log::info('[Schedule] Due date reminders started');
    })
    ->after(function () {
        Log::info('[Schedule] Due date reminders finished');
    });

// Alternatif: setiap jam (kalau butuh lebih sering)
// Schedule::command('kanban:due-date-reminders')->hourly();

// Alternatif: setiap hari jam 9 pagi dan 2 siang
// Schedule::command('kanban:due-date-reminders')->twiceDaily(9, 14);

// ==============================================
// QUEUE WORKER (OPSIONAL)
// ==============================================

// Process queue jobs setiap menit (jika tidak pakai supervisor/PM2)
// Schedule::command('queue:work --stop-when-empty')
//     ->everyMinute()
//     ->withoutOverlapping()
//     ->runInBackground();

// ==============================================
// DATABASE BACKUP (OPSIONAL - jika perlu)
// ==============================================

// Backup database setiap hari jam 1 pagi (uncomment jika ingin)
// Schedule::command('backup:run')->dailyAt('01:00');