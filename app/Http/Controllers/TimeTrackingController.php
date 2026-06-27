<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TimeEntry;
use App\Models\TaskActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TimeTrackingController extends Controller
{
    public function status(Task $task)
    {
        try {
            $board = $task->taskList->board;
            if (!$board->hasAccess(auth()->id())) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            
            $activeEntry = TimeEntry::where('task_id', $task->id)
                ->where('user_id', auth()->id())
                ->whereIn('status', ['running', 'paused'])
                ->first();
            
            $timeEntries = TimeEntry::where('task_id', $task->id)
                ->where('user_id', auth()->id())
                ->orderBy('created_at', 'desc')
                ->take(10)
                ->get();
            
            // Hitung total detik dengan aman
            $totalSeconds = TimeEntry::where('task_id', $task->id)
                ->where('user_id', auth()->id())
                ->sum('total_seconds');
            
            if ($activeEntry && $activeEntry->status === 'running' && $activeEntry->started_at) {
                $totalSeconds += $activeEntry->started_at->diffInSeconds(now());
            }
            
            $hours = floor($totalSeconds / 3600);
            $minutes = floor(($totalSeconds % 3600) / 60);
            $secs = $totalSeconds % 60;
            $formattedTime = sprintf('%02d:%02d:%02d', $hours, $minutes, $secs);
            
            $formattedEntry = null;
            if ($activeEntry) {
                $formattedEntry = [
                    'id' => $activeEntry->id,
                    'status' => $activeEntry->status,
                    'started_at' => $activeEntry->started_at,
                    'total_seconds' => $activeEntry->total_seconds ?? 0,
                ];
            }
            
            return response()->json([
                'success' => true,
                'active_entry' => $formattedEntry,
                'time_entries' => $timeEntries,
                'total_time' => $formattedTime,
                'total_seconds' => $totalSeconds
            ]);
            
        } catch (\Exception $e) {
            Log::error('Time status error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function start(Task $task)
    {
        try {
            $board = $task->taskList->board;
            if (!$board->hasAccess(auth()->id())) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            
            $existingEntry = TimeEntry::where('task_id', $task->id)
                ->where('user_id', auth()->id())
                ->whereIn('status', ['running', 'paused'])
                ->first();
            
            if ($existingEntry) {
                if ($existingEntry->status === 'paused') {
                    $existingEntry->resume();
                    $message = 'Timer resumed';
                } else {
                    return response()->json(['error' => 'Timer already running'], 422);
                }
            } else {
                TimeEntry::create([
                    'task_id' => $task->id,
                    'user_id' => auth()->id(),
                    'started_at' => now(),
                    'status' => 'running',
                    'total_seconds' => 0,
                ]);
                $message = 'Timer started';
            }
            
            TaskActivity::create([
                'action' => 'time_tracking_changed',
                'user_id' => auth()->id(),
                'task_id' => $task->id,
                'new_value' => $message
            ]);
            
            $task->notifyWatchers('time_tracking_changed', auth()->id(), $task->id, null, $message);
            
            $totalSeconds = TimeEntry::where('task_id', $task->id)
                ->where('user_id', auth()->id())
                ->sum('total_seconds');
            
            $runningEntry = TimeEntry::where('task_id', $task->id)
                ->where('user_id', auth()->id())
                ->where('status', 'running')
                ->first();
                
            if ($runningEntry && $runningEntry->started_at) {
                $totalSeconds += $runningEntry->started_at->diffInSeconds(now());
            }
            
            $hours = floor($totalSeconds / 3600);
            $minutes = floor(($totalSeconds % 3600) / 60);
            $secs = $totalSeconds % 60;
            $formattedTime = sprintf('%02d:%02d:%02d', $hours, $minutes, $secs);
            
            return response()->json([
                'success' => true,
                'message' => $message,
                'total_time' => $formattedTime
            ]);
            
        } catch (\Exception $e) {
            Log::error('Start timer error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    public function pause(Task $task)
    {
        try {
            $board = $task->taskList->board;
            if (!$board->hasAccess(auth()->id())) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            
            $entry = TimeEntry::where('task_id', $task->id)
                ->where('user_id', auth()->id())
                ->where('status', 'running')
                ->first();
            
            if (!$entry) {
                return response()->json(['error' => 'No running timer found'], 404);
            }
            
            $entry->pause();
            
            TaskActivity::create([
                'action' => 'time_tracking_changed',
                'user_id' => auth()->id(),
                'task_id' => $task->id,
                'new_value' => 'Timer paused'
            ]);
            
            $task->notifyWatchers('time_tracking_changed', auth()->id(), $task->id, null, 'Paused tracking time');
            
            $totalSeconds = TimeEntry::where('task_id', $task->id)
                ->where('user_id', auth()->id())
                ->sum('total_seconds');
            
            $hours = floor($totalSeconds / 3600);
            $minutes = floor(($totalSeconds % 3600) / 60);
            $secs = $totalSeconds % 60;
            $formattedTime = sprintf('%02d:%02d:%02d', $hours, $minutes, $secs);
            
            return response()->json([
                'success' => true,
                'message' => 'Timer paused',
                'total_time' => $formattedTime
            ]);
            
        } catch (\Exception $e) {
            Log::error('Pause timer error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    public function stop(Task $task)
    {
        try {
            $board = $task->taskList->board;
            if (!$board->hasAccess(auth()->id())) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            
            $entry = TimeEntry::where('task_id', $task->id)
                ->where('user_id', auth()->id())
                ->where('status', 'running')
                ->first();
            
            if (!$entry) {
                return response()->json(['error' => 'No running timer found'], 404);
            }
            
            $entry->stop();
            
            TaskActivity::create([
                'action' => 'time_tracking_changed',
                'user_id' => auth()->id(),
                'task_id' => $task->id,
                'new_value' => 'Timer stopped'
            ]);
            
            $task->notifyWatchers('time_tracking_changed', auth()->id(), $task->id, null, 'Stopped tracking time');
            
            $totalSeconds = TimeEntry::where('task_id', $task->id)
                ->where('user_id', auth()->id())
                ->sum('total_seconds');
            
            $hours = floor($totalSeconds / 3600);
            $minutes = floor(($totalSeconds % 3600) / 60);
            $secs = $totalSeconds % 60;
            $formattedTime = sprintf('%02d:%02d:%02d', $hours, $minutes, $secs);
            
            return response()->json([
                'success' => true,
                'message' => 'Timer stopped',
                'total_time' => $formattedTime
            ]);
            
        } catch (\Exception $e) {
            Log::error('Stop timer error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    public function history(Task $task)
    {
        try {
            $board = $task->taskList->board;
            if (!$board->hasAccess(auth()->id())) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            
            $entries = TimeEntry::where('task_id', $task->id)
                ->with('user')
                ->orderBy('created_at', 'desc')
                ->paginate(20);
            
            $totalSeconds = TimeEntry::where('task_id', $task->id)->sum('total_seconds');
            
            $hours = floor($totalSeconds / 3600);
            $minutes = floor(($totalSeconds % 3600) / 60);
            $secs = $totalSeconds % 60;
            $formattedTime = sprintf('%02d:%02d:%02d', $hours, $minutes, $secs);
            
            return response()->json([
                'success' => true,
                'entries' => $entries,
                'total_time' => $formattedTime
            ]);
            
        } catch (\Exception $e) {
            Log::error('Time history error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}