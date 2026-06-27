<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'type', 'title', 'message', 'user_id', 'from_user_id', 
        'task_id', 'board_id', 'is_read', 'data'
    ];
    
    protected $casts = [
        'is_read' => 'boolean',
        'data' => 'array',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    public function fromUser()
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }
    
    public function task()
    {
        return $this->belongsTo(Task::class);
    }
    
    public function board()
    {
        return $this->belongsTo(Board::class);
    }
    
    public function markAsRead()
    {
        $this->update(['is_read' => true]);
    }
}