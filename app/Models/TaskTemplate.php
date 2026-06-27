<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskTemplate extends Model
{
    protected $fillable = [
        'name',
        'description',
        'priority',
        'checklist_items',
        'label_ids',
        'assignee_ids',
        'board_id',
        'user_id'
    ];
    
    protected $casts = [
        'checklist_items' => 'array',
        'label_ids' => 'array',
        'assignee_ids' => 'array',
    ];
    
    public function board()
    {
        return $this->belongsTo(Board::class);
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function getLabels()
    {
        if (empty($this->label_ids)) {
            return collect();
        }
        return Label::whereIn('id', $this->label_ids)->get();
    }
    
    public function getAssignees()
    {
        if (empty($this->assignee_ids)) {
            return collect();
        }
        return User::whereIn('id', $this->assignee_ids)->get();
    }
}