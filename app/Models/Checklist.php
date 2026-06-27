<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Checklist extends Model
{
    protected $fillable = ['name', 'task_id', 'order'];
    
    public function task()
    {
        return $this->belongsTo(Task::class);
    }
    
    public function items()
    {
        return $this->hasMany(ChecklistItem::class)->orderBy('order');
    }
    
    public function getProgressAttribute()
    {
        $total = $this->items()->count();
        if ($total === 0) return 0;
        $completed = $this->items()->where('is_checked', true)->count();
        return round(($completed / $total) * 100);
    }
    
    public function getCompletedCountAttribute()
    {
        return $this->items()->where('is_checked', true)->count();
    }
    
    public function getTotalCountAttribute()
    {
        return $this->items()->count();
    }
}