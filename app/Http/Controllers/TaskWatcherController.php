<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TaskWatcherController extends Controller
{
    /**
     * Toggle watch status for a task (Watch/Unwatch)
     * 
     * @param Task $task
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggle(Task $task)
    {
        Log::info('=== Toggle Watch START ===');
        Log::info('Task ID: ' . $task->id);
        Log::info('User ID: ' . auth()->id());
        Log::info('User Name: ' . auth()->user()->name);
        
        try {
            // Check access to board
            $board = $task->taskList->board;
            Log::info('Board ID: ' . $board->id);
            Log::info('Board Owner: ' . $board->user_id);
            
            if (!$board->hasAccess(auth()->id())) {
                Log::warning('User does NOT have access to this board');
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            
            Log::info('User has access to board');
            
            // Check current watch status
            $isWatching = $task->isWatchedBy(auth()->id());
            Log::info('Current watch status: ' . ($isWatching ? 'WATCHING' : 'NOT WATCHING'));
            
            if ($isWatching) {
                // Remove watcher
                $task->removeWatcher(auth()->id());
                $message = 'You are no longer watching this task';
                $watching = false;
                Log::info('Watcher REMOVED successfully');
            } else {
                // Add watcher
                $task->addWatcher(auth()->id());
                $message = 'You are now watching this task';
                $watching = true;
                Log::info('Watcher ADDED successfully');
            }
            
            // Get updated watchers count
            $watchersCount = $task->watchers()->count();
            Log::info('Total watchers now: ' . $watchersCount);
            Log::info('=== Toggle Watch END ===');
            
            return response()->json([
                'success' => true,
                'watching' => $watching,
                'message' => $message,
                'watchers_count' => $watchersCount
            ]);
            
        } catch (\Exception $e) {
            Log::error('Toggle watch error: ' . $e->getMessage());
            Log::error('Error trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get list of watchers for a task
     * 
     * @param Task $task
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Task $task)
    {
        Log::info('=== Get Watchers START ===');
        Log::info('Task ID: ' . $task->id);
        Log::info('User ID: ' . auth()->id());
        
        try {
            // Check access to board
            $board = $task->taskList->board;
            Log::info('Board ID: ' . $board->id);
            
            if (!$board->hasAccess(auth()->id())) {
                Log::warning('User does NOT have access to this board');
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            
            Log::info('User has access to board');
            
            // Get watchers
            $watchers = $task->watchers()->get(['id', 'name', 'email', 'avatar']);
            Log::info('Watchers count from DB: ' . $watchers->count());
            
            // Add avatar URL for each watcher
            foreach ($watchers as $watcher) {
                if (method_exists($watcher, 'getAvatarUrlAttribute')) {
                    $watcher->avatar_url = $watcher->getAvatarUrlAttribute();
                    Log::info('Avatar URL for ' . $watcher->name . ': ' . $watcher->avatar_url);
                } else {
                    // Fallback if method doesn't exist
                    $watcher->avatar_url = 'https://ui-avatars.com/api/?background=1e3a5f&color=fff&name=' . urlencode($watcher->name);
                    Log::info('Fallback avatar for ' . $watcher->name);
                }
            }
            
            $isWatching = $task->isWatchedBy(auth()->id());
            Log::info('Current user is watching: ' . ($isWatching ? 'YES' : 'NO'));
            Log::info('=== Get Watchers END ===');
            
            return response()->json([
                'success' => true,
                'watchers' => $watchers,
                'is_watching' => $isWatching,
                'count' => $watchers->count()
            ]);
            
        } catch (\Exception $e) {
            Log::error('Get watchers error: ' . $e->getMessage());
            Log::error('Error trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'error' => $e->getMessage()
            ], 500);
        }
    }
}