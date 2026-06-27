<?php

namespace App\Jobs;

use App\Models\Task;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendDueDateReminderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, SerializesModels;

    protected $task;
    protected $reminderType;

    public function __construct(Task $task, $reminderType)
    {
        $this->task = $task;
        $this->reminderType = $reminderType;
    }

    public function handle()
    {
        $task = $this->task;
        
        // Skip if task already archived
        if ($task->isArchived()) {
            return;
        }
        
        // Get all assignees
        $assignees = $task->assignees;
        
        if ($assignees->isEmpty()) {
            return;
        }
        
        $board = $task->taskList->board;
        $dueDate = $task->due_date;
        
        // Determine message based on reminder type
        $title = '';
        $message = '';
        
        switch ($this->reminderType) {
            case 'tomorrow':
                $title = '⏰ Task Due Tomorrow';
                $message = "Task \"{$task->title}\" is due tomorrow (" . date('d M Y', strtotime($dueDate)) . ")";
                break;
            case 'today':
                $title = '⚠️ Task Due Today';
                $message = "Task \"{$task->title}\" is due today! Please complete it ASAP.";
                break;
            case 'overdue':
                $title = '🔴 Task Overdue';
                $message = "Task \"{$task->title}\" was due on " . date('d M Y', strtotime($dueDate)) . " and is now OVERDUE!";
                break;
            default:
                return;
        }
        
        // Send notification to each assignee
        foreach ($assignees as $assignee) {
            Notification::create([
                'type' => 'due_date_reminder',
                'title' => $title,
                'message' => $message,
                'user_id' => $assignee->id,
                'from_user_id' => null,
                'task_id' => $task->id,
                'board_id' => $board->id,
                'is_read' => false,
                'data' => [
                    'due_date' => $dueDate,
                    'reminder_type' => $this->reminderType,
                    'task_title' => $task->title
                ]
            ]);
        }
        
        Log::info("Due date reminder sent for task #{$task->id} ({$this->reminderType}) to " . $assignees->count() . " assignee(s)");
    }
}