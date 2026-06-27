<?php

namespace App\Console\Commands;

use App\Models\Task;
use App\Jobs\SendDueDateReminderJob;
use Illuminate\Console\Command;
use Carbon\Carbon;

class SendDueDateRemindersCommand extends Command
{
    protected $signature = 'kanban:due-date-reminders';
    protected $description = 'Send due date reminders for tasks (tomorrow, today, overdue)';

    public function handle()
    {
        $this->info('Checking due date reminders...');
        
        $today = Carbon::today();
        $tomorrow = Carbon::tomorrow();
        
        // ==============================================
        // 1. Send reminders for tasks due TOMORROW
        // ==============================================
        
        $tasksDueTomorrow = Task::whereDate('due_date', $tomorrow)
            ->whereNull('archived_at')
            ->whereHas('assignees')
            ->get();
        
        $tomorrowCount = 0;
        foreach ($tasksDueTomorrow as $task) {
            SendDueDateReminderJob::dispatch($task, 'tomorrow');
            $tomorrowCount++;
        }
        
        $this->info("Dispatched {$tomorrowCount} reminders for tasks due tomorrow");
        
        // ==============================================
        // 2. Send reminders for tasks due TODAY (not yet completed)
        // Note: Task doesn't have a "completed" flag, so we check if it's still in progress
        // You can customize which lists are considered "incomplete"
        // ==============================================
        
        $tasksDueToday = Task::whereDate('due_date', $today)
            ->whereNull('archived_at')
            ->whereHas('assignees')
            ->get();
        
        $todayCount = 0;
        foreach ($tasksDueToday as $task) {
            SendDueDateReminderJob::dispatch($task, 'today');
            $todayCount++;
        }
        
        $this->info("Dispatched {$todayCount} reminders for tasks due today");
        
        // ==============================================
        // 3. Send reminders for OVERDUE tasks (due date < today, not archived)
        // ==============================================
        
        $overdueTasks = Task::whereDate('due_date', '<', $today)
            ->whereNull('archived_at')
            ->whereHas('assignees')
            ->get();
        
        $overdueCount = 0;
        foreach ($overdueTasks as $task) {
            SendDueDateReminderJob::dispatch($task, 'overdue');
            $overdueCount++;
        }
        
        $this->info("Dispatched {$overdueCount} reminders for overdue tasks");
        
        $this->info('Due date reminders dispatched successfully!');
        
        return Command::SUCCESS;
    }
}