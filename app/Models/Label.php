<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Label extends Model
{
    protected $table = 'labels';
    
    protected $fillable = ['name', 'color', 'description', 'board_id', 'user_id'];
    
    public function board()
    {
        return $this->belongsTo(Board::class);
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function tasks()
    {
        return $this->belongsToMany(Task::class, 'task_label');
    }
}