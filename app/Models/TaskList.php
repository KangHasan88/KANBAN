<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskList extends Model
{
    protected $fillable = ['name', 'order', 'color', 'board_id'];
    
    public function tasks()
    {
        return $this->hasMany(Task::class)->active()->orderBy('order');
    }
    
    // Optional: ambil semua task termasuk yang archived (buat keperluan tertentu)
    public function allTasks()
    {
        return $this->hasMany(Task::class)->orderBy('order');
    }
    
    public function board()
    {
        return $this->belongsTo(Board::class);
    }
    
    // Helper untuk cek apakah ini list target auto archive
    public function isAutoArchiveTargetList($configuredName = null)
    {
        $targetName = $configuredName ?? ($this->board ? $this->board->getAutoArchiveListName() : 'Done');
        return $this->name === $targetName;
    }
    
    // Helper untuk cek apakah ini list "Done" (default)
    public function isDoneList()
    {
        return $this->name === 'Done';
    }
}