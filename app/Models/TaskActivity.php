<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskActivity extends Model
{
    protected $fillable = [
        'action', 
        'user_id', 
        'task_id',
        'field',
        'old_value',
        'new_value'
    ];
    
    protected $casts = [
        'created_at' => 'datetime',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function task()
    {
        return $this->belongsTo(Task::class);
    }
    
    // Helper untuk format activity
    public function getFormattedActionAttribute()
    {
        switch ($this->action) {
            case 'created':
                return "Created this task";
            case 'updated_title':
                return "Changed title from '{$this->old_value}' to '{$this->new_value}'";
            case 'updated_description':
                return "Updated description";
            case 'updated_priority':
                return "Changed priority from '{$this->old_value}' to '{$this->new_value}'";
            case 'updated_due_date':
                return "Changed due date from '{$this->old_value}' to '{$this->new_value}'";
            case 'moved':
                return "Moved from '{$this->old_value}' to '{$this->new_value}'";
            case 'assigned':
                return "Assigned to {$this->new_value}";
            default:
                return $this->action;
        }
    }
}