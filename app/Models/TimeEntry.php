<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class TimeEntry extends Model
{
    protected $table = 'time_entries';
    
    protected $fillable = [
        'task_id', 'user_id', 'started_at', 'paused_at', 'ended_at',
        'total_seconds', 'status', 'note'
    ];
    
    protected $casts = [
        'started_at' => 'datetime',
        'paused_at' => 'datetime',
        'ended_at' => 'datetime',
        'total_seconds' => 'integer',
    ];
    
    public function task()
    {
        return $this->belongsTo(Task::class);
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function start()
    {
        $this->update([
            'started_at' => now(),
            'status' => 'running',
            'paused_at' => null,
        ]);
    }
    
    public function pause()
    {
        if ($this->status !== 'running') return;
        
        $now = now();
        $elapsed = $this->started_at->diffInSeconds($now);
        
        $this->update([
            'paused_at' => $now,
            'total_seconds' => ($this->total_seconds ?? 0) + $elapsed,
            'status' => 'paused',
        ]);
    }
    
    public function resume()
    {
        if ($this->status !== 'paused') return;
        
        $this->update([
            'started_at' => now(),
            'paused_at' => null,
            'status' => 'running',
        ]);
    }
    
    public function stop()
    {
        if ($this->status === 'running' && $this->started_at) {
            $elapsed = $this->started_at->diffInSeconds(now());
            $this->total_seconds = ($this->total_seconds ?? 0) + $elapsed;
        }
        
        $this->update([
            'ended_at' => now(),
            'status' => 'stopped',
            'started_at' => null,
            'paused_at' => null,
        ]);
    }
    
    public function getCurrentDuration()
    {
        if ($this->status === 'running' && $this->started_at) {
            return ($this->total_seconds ?? 0) + $this->started_at->diffInSeconds(now());
        }
        return $this->total_seconds ?? 0;
    }
    
    public function getFormattedTotalAttribute()
    {
        $seconds = $this->getCurrentDuration();
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $secs = $seconds % 60;
        
        return sprintf('%02d:%02d:%02d', $hours, $minutes, $secs);
    }
}