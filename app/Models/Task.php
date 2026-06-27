<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DateTime;
use Illuminate\Support\Facades\Log;

class Task extends Model
{
    protected $fillable = [
        'title', 
        'description', 
        'order', 
        'priority', 
        'due_date', 
        'task_list_id',
        'archived_at'
    ];
    
    protected $casts = [
        'due_date' => 'date',
        'archived_at' => 'datetime',
    ];
    
    // ==============================================
    // RELATIONSHIPS
    // ==============================================
    
    public function taskList()
    {
        return $this->belongsTo(TaskList::class);
    }
    
    // Multiple assignees (MANY-TO-MANY)
    public function assignees()
    {
        return $this->belongsToMany(User::class, 'task_user');
    }
    
    public function activities()
    {
        return $this->hasMany(TaskActivity::class)->orderBy('created_at', 'desc');
    }
    
    public function labels()
    {
        return $this->belongsToMany(Label::class, 'task_label');
    }
    
    public function checklists()
    {
        return $this->hasMany(Checklist::class)->orderBy('order');
    }
    
    public function comments()
    {
        return $this->hasMany(Comment::class)->whereNull('parent_id')->orderBy('created_at', 'asc');
    }
    
    public function allComments()
    {
        return $this->hasMany(Comment::class);
    }
    
    public function attachments()
    {
        return $this->hasMany(TaskAttachment::class)->orderBy('created_at', 'desc');
    }
    
    // ==============================================
    // CUSTOM FIELDS
    // ==============================================
    
    public function customFieldValues()
    {
        return $this->hasMany(TaskCustomFieldValue::class);
    }
    
    public function getCustomFieldValue($fieldId)
    {
        $value = $this->customFieldValues()->where('custom_field_id', $fieldId)->first();
        return $value ? $value->value : null;
    }
    
    // ==============================================
    // RECURRING TASK
    // ==============================================
    
    public function recurringTask()
    {
        return $this->hasOne(RecurringTask::class);
    }
    
    public function isRecurring()
    {
        return $this->recurringTask !== null;
    }
    
    // ==============================================
    // TIME TRACKING
    // ==============================================
    
    public function timeEntries()
    {
        return $this->hasMany(TimeEntry::class);
    }
    
    public function activeTimeEntry()
    {
        return $this->hasOne(TimeEntry::class)->whereIn('status', ['running', 'paused']);
    }
    
    public function getTotalTimeAttribute()
    {
        $total = $this->timeEntries()->sum('total_seconds');
        
        $runningEntry = $this->timeEntries()->where('status', 'running')->first();
        if ($runningEntry) {
            $total += $runningEntry->started_at->diffInSeconds(now());
        }
        
        return $total;
    }
    
    public function getFormattedTotalTimeAttribute()
    {
        $seconds = $this->total_time;
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $secs = $seconds % 60;
        
        return sprintf('%02d:%02d:%02d', $hours, $minutes, $secs);
    }
    
    // ==============================================
    // TASK WATCHERS (MULTI OBSERVER)
    // ==============================================
    
    public function watchers()
    {
        return $this->belongsToMany(User::class, 'task_watchers', 'task_id', 'user_id')
                    ->withTimestamps()
                    ->select('users.id', 'users.name', 'users.email', 'users.avatar');
    }
    
    public function isWatchedBy($userId)
    {
        return $this->watchers()->where('users.id', $userId)->exists();
    }
    
    public function addWatcher($userId)
    {
        if (!$this->isWatchedBy($userId)) {
            $this->watchers()->attach($userId);
            Log::info('Watcher added', ['task_id' => $this->id, 'user_id' => $userId]);
        }
    }
    
    public function removeWatcher($userId)
    {
        $this->watchers()->detach($userId);
        Log::info('Watcher removed', ['task_id' => $this->id, 'user_id' => $userId]);
    }
    
    public function notifyWatchers($action, $actorId, $taskId, $oldValue = null, $newValue = null)
    {
        Log::info('🔔🔔🔔 NOTIFYWATCHERS CALLED 🔔🔔🔔', [
            'action' => $action,
            'task_id' => $this->id,
            'task_title' => $this->title,
            'actor_id' => $actorId
        ]);
        
        $watchers = $this->watchers()->where('users.id', '!=', $actorId)->get();
        
        Log::info('Watchers count (excluding actor): ' . $watchers->count());
        
        if ($watchers->isEmpty()) {
            Log::info('No watchers to notify, skipping notification creation');
            return;
        }
        
        foreach ($watchers as $watcher) {
            Log::info('Watcher found: ID=' . $watcher->id . ', Name=' . $watcher->name);
        }
        
        $actor = User::find($actorId);
        $actorName = $actor ? $actor->name : 'Someone';
        
        foreach ($watchers as $watcher) {
            Log::info('Creating notification for watcher', [
                'watcher_id' => $watcher->id,
                'watcher_name' => $watcher->name
            ]);
            
            try {
                Notification::create([
                    'type' => 'task_watcher',
                    'title' => '👀 Task Update: ' . $this->title,
                    'message' => $actorName . ' ' . $this->getWatcherMessage($action, $actorId, $oldValue, $newValue),
                    'user_id' => $watcher->id,
                    'from_user_id' => $actorId,
                    'task_id' => $taskId,
                    'board_id' => $this->taskList->board_id,
                    'is_read' => false,
                    'data' => [
                        'action' => $action,
                        'task_title' => $this->title,
                        'old_value' => $oldValue,
                        'new_value' => $newValue
                    ]
                ]);
                Log::info('✅ Notification created successfully for watcher ID=' . $watcher->id);
            } catch (\Exception $e) {
                Log::error('❌ Failed to create notification: ' . $e->getMessage());
                Log::error('Error trace: ' . $e->getTraceAsString());
            }
        }
        
        Log::info('Notified ' . $watchers->count() . ' watchers');
    }
    
    private function getWatcherMessage($action, $actorId, $oldValue, $newValue)
    {
        $actor = User::find($actorId);
        $actorName = $actor ? $actor->name : 'Someone';
        
        $messages = [
            'updated_title' => "changed the title from '{$oldValue}' to '{$newValue}'",
            'updated_description' => "updated the description",
            'updated_priority' => "changed priority from '{$oldValue}' to '{$newValue}'",
            'updated_due_date' => "changed due date from '{$oldValue}' to '{$newValue}'",
            'moved' => "moved this task from '{$oldValue}' to '{$newValue}'",
            'commented' => "added a comment",
            'assigned' => "assigned {$newValue} to this task",
            'unassigned' => "removed {$oldValue} from this task",
            'archived' => "archived this task",
            'unarchived' => "restored this task from archive",
            'deleted_comment' => "deleted a comment",
            'added_checklist' => "added a checklist '{$newValue}'",
            'updated_checklist' => "renamed checklist from '{$oldValue}' to '{$newValue}'",
            'deleted_checklist' => "deleted checklist '{$oldValue}'",
            'added_checklist_item' => "added checklist item '{$newValue}'",
            'updated_checklist_item' => "renamed checklist item from '{$oldValue}' to '{$newValue}'",
            'deleted_checklist_item' => "deleted checklist item '{$oldValue}'",
            'checked_item' => "completed checklist item '{$oldValue}'",
            'unchecked_item' => "unchecked checklist item '{$oldValue}'",
            'uploaded_file' => "uploaded a file '{$newValue}'",
            'deleted_file' => "deleted a file '{$oldValue}'",
            'set_cover' => "changed the cover image",
            'removed_cover' => "removed the cover image",
            'assigned_label' => "added label '{$newValue}'",
            'removed_label' => "removed label '{$oldValue}'",
            'updated_custom_fields' => "updated custom fields",
            'set_recurring' => "set recurring task: {$newValue}",
            'removed_recurring' => "removed recurring settings",
            'recurring_created' => "created a new recurring task due on {$newValue}",
            'time_tracking_started' => "started tracking time",
            'time_tracking_paused' => "paused time tracking",
            'time_tracking_stopped' => "stopped tracking time",
        ];
        
        $defaultMessage = $messages[$action] ?? "made changes to this task";
        return $defaultMessage;
    }
    
    // ==============================================
    // ARCHIVE SCOPES & METHODS
    // ==============================================
    
    public function scopeActive($query)
    {
        return $query->whereNull('archived_at');
    }
    
    public function scopeArchived($query)
    {
        return $query->whereNotNull('archived_at');
    }
    
    public function isArchived()
    {
        return !is_null($this->archived_at);
    }
    
    public function archive()
    {
        $this->update(['archived_at' => now()]);
    }
    
    public function unarchive()
    {
        $this->update(['archived_at' => null]);
    }
    
    // ==============================================
    // CARD AGING METHODS (ADDED)
    // ==============================================
    
    /**
     * Check if this task is aging (not updated for a long time)
     */
    public function isAging()
    {
        $board = $this->taskList->board;
        if (!$board->isCardAgingEnabled()) {
            return false;
        }
        
        $daysSinceUpdate = $this->getDaysSinceLastUpdate();
        $agingDays = $board->getCardAgingDays();
        
        return $daysSinceUpdate >= $agingDays;
    }
    
    /**
     * Get aging level (1-5) for visual effect
     * Level 1 = slightly faded, Level 5 = very faded
     */
    public function getAgingLevel()
    {
        $board = $this->taskList->board;
        if (!$board->isCardAgingEnabled()) {
            return 0;
        }
        
        $daysSinceUpdate = $this->getDaysSinceLastUpdate();
        $agingDays = $board->getCardAgingDays();
        
        if ($daysSinceUpdate < $agingDays) {
            return 0;
        }
        
        $excessDays = $daysSinceUpdate - $agingDays;
        $level = min(floor($excessDays / 7) + 1, 5);
        
        return $level;
    }
    
    /**
     * Get number of days since last update (activity or creation)
     */
    public function getDaysSinceLastUpdate()
    {
        $lastActivity = $this->activities()->orderBy('created_at', 'desc')->first();
        
        if ($lastActivity) {
            $lastUpdate = $lastActivity->created_at;
        } else {
            $lastUpdate = $this->created_at;
        }
        
        return $lastUpdate->diffInDays(now());
    }
}