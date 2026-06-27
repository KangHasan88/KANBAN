<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class RecurringTask extends Model
{
    protected $fillable = [
        'task_id', 'frequency', 'interval', 'week_days', 'month_days',
        'until_date', 'occurrences', 'occurrences_count', 'last_generated_at',
        'created_by', 'is_active'
    ];
    
    protected $casts = [
        'week_days' => 'array',
        'month_days' => 'array',
        'until_date' => 'date',
        'last_generated_at' => 'date',
        'is_active' => 'boolean',
        'interval' => 'integer',
        'occurrences' => 'integer',
        'occurrences_count' => 'integer',
    ];
    
    public function task()
    {
        return $this->belongsTo(Task::class);
    }
    
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    
    public function getNextDate($fromDate = null)
    {
        $fromDate = $fromDate ? Carbon::parse($fromDate) : Carbon::today();
        
        if ($this->until_date && $fromDate->gt($this->until_date)) {
            return null;
        }
        
        if ($this->occurrences && $this->occurrences_count >= $this->occurrences) {
            return null;
        }
        
        $nextDate = clone $fromDate;
        
        switch ($this->frequency) {
            case 'daily':
                $nextDate = $fromDate->copy()->addDays($this->interval);
                break;
            case 'weekly':
                $nextDate = $fromDate->copy()->addWeeks($this->interval);
                break;
            case 'monthly':
                $nextDate = $fromDate->copy()->addMonths($this->interval);
                break;
            case 'yearly':
                $nextDate = $fromDate->copy()->addYears($this->interval);
                break;
            default:
                return null;
        }
        
        if ($this->until_date && $nextDate->gt($this->until_date)) {
            return null;
        }
        
        return $nextDate;
    }
    
    public function generateNextTask()
    {
        $nextDueDate = $this->getNextDate();
        if (!$nextDueDate) {
            $this->update(['is_active' => false]);
            return null;
        }
        
        $originalTask = $this->task;
        
        // CARI LIST DEFAULT UNTUK TASK BARU (To Do)
        $board = $originalTask->taskList->board;
        $targetList = $board->lists()->where('name', 'To Do')->first();
        
        if (!$targetList) {
            // Fallback ke list dengan order terkecil (paling kiri)
            $targetList = $board->lists()->orderBy('order')->first();
        }
        
        if (!$targetList) {
            Log::error('Recurring task: No target list found', ['board_id' => $board->id]);
            return null;
        }
        
        $taskListId = $targetList->id;
        $maxOrder = $targetList->tasks()->max('order') ?? -1;
        
        // Buat task baru
        $newTask = Task::create([
            'title' => $originalTask->title,
            'description' => $originalTask->description,
            'priority' => $originalTask->priority,
            'due_date' => $nextDueDate,
            'task_list_id' => $taskListId,
            'order' => $maxOrder + 1,
        ]);
        
        // Copy assignees
        foreach ($originalTask->assignees as $assignee) {
            $newTask->assignees()->attach($assignee->id);
        }
        
        // Copy labels
        foreach ($originalTask->labels as $label) {
            $newTask->labels()->attach($label->id);
        }
        
        // Copy checklists
        foreach ($originalTask->checklists as $checklist) {
            $newChecklist = $newTask->checklists()->create([
                'name' => $checklist->name,
                'order' => $checklist->order
            ]);
            foreach ($checklist->items as $item) {
                $newChecklist->items()->create([
                    'name' => $item->name,
                    'is_checked' => false,
                    'order' => $item->order
                ]);
            }
        }
        
        // Copy attachments & cover
        foreach ($originalTask->attachments as $attachment) {
            $oldFilePath = str_replace('/storage/', '', $attachment->file_path);
            $oldFullPath = storage_path('app/public/' . $oldFilePath);
            
            if (file_exists($oldFullPath)) {
                $newFileName = time() . '_' . uniqid() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $attachment->file_name);
                $newFilePath = 'attachments/' . $newTask->id . '/' . $newFileName;
                $newFullPath = storage_path('app/public/' . $newFilePath);
                
                $folderPath = storage_path('app/public/attachments/' . $newTask->id);
                if (!file_exists($folderPath)) {
                    mkdir($folderPath, 0777, true);
                }
                
                copy($oldFullPath, $newFullPath);
                
                $newTask->attachments()->create([
                    'file_name' => $attachment->file_name,
                    'file_path' => '/storage/' . $newFilePath,
                    'file_type' => $attachment->file_type,
                    'file_size' => $attachment->file_size,
                    'mime_type' => $attachment->mime_type,
                    'user_id' => $attachment->user_id,
                    'is_cover' => $attachment->is_cover,
                ]);
            }
        }
        
        // Update occurrences count
        $this->increment('occurrences_count');
        $this->update(['last_generated_at' => now()]);
        
        // Log activity
        TaskActivity::create([
            'action' => 'recurring_created',
            'user_id' => $this->created_by,
            'task_id' => $newTask->id,
            'new_value' => "Generated from recurring task #{$originalTask->id} in list '{$targetList->name}'"
        ]);
        
        // Notify watchers
        $originalTask->notifyWatchers('recurring_created', $this->created_by, $newTask->id, null, $nextDueDate->format('Y-m-d'));
        
        return $newTask;
    }
    
    public function getFrequencyTextAttribute()
    {
        $frequencies = [
            'daily' => 'Every ' . $this->interval . ' day(s)',
            'weekly' => 'Every ' . $this->interval . ' week(s)',
            'monthly' => 'Every ' . $this->interval . ' month(s)',
            'yearly' => 'Every ' . $this->interval . ' year(s)',
        ];
        
        $text = $frequencies[$this->frequency] ?? $this->frequency;
        
        if ($this->until_date) {
            $text .= ' until ' . $this->until_date->format('d M Y');
        }
        
        if ($this->occurrences) {
            $text .= ' for ' . $this->occurrences . ' times';
        }
        
        return $text;
    }
}