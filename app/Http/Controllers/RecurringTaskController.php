<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\RecurringTask;
use App\Models\TaskActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RecurringTaskController extends Controller
{
    public function show(Task $task)
    {
        $board = $task->taskList->board;
        if (!$board->hasAccess(auth()->id())) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $recurring = $task->recurringTask;
        
        return response()->json([
            'success' => true,
            'recurring' => $recurring,
            'is_recurring' => $recurring !== null
        ]);
    }
    
    public function store(Request $request, Task $task)
    {
        try {
            $board = $task->taskList->board;
            if (!$board->hasAccess(auth()->id())) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            
            $permission = $this->getUserPermission($board);
            if ($permission !== 'owner' && $permission !== 'edit') {
                return response()->json(['error' => 'No edit permission'], 403);
            }
            
            $request->validate([
                'frequency' => 'required|in:daily,weekly,monthly,yearly',
                'interval' => 'integer|min:1|max:365',
                'until_date' => 'nullable|date|after:today',
                'occurrences' => 'nullable|integer|min:1|max:100',
                'is_active' => 'boolean'
            ]);
            
            if ($request->until_date && $request->occurrences) {
                return response()->json(['error' => 'Choose only one: until_date OR occurrences'], 422);
            }
            
            $recurring = RecurringTask::updateOrCreate(
                ['task_id' => $task->id],
                [
                    'frequency' => $request->frequency,
                    'interval' => $request->interval ?? 1,
                    'until_date' => $request->until_date,
                    'occurrences' => $request->occurrences,
                    'created_by' => auth()->id(),
                    'is_active' => $request->is_active ?? true
                ]
            );
            
            TaskActivity::create([
                'action' => 'set_recurring',
                'user_id' => auth()->id(),
                'task_id' => $task->id,
                'new_value' => $recurring->frequency_text
            ]);
            
            $task->notifyWatchers('set_recurring', auth()->id(), $task->id, null, $recurring->frequency_text);
            
            return response()->json([
                'success' => true,
                'recurring' => $recurring,
                'message' => 'Recurring task set successfully'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Recurring store error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    public function destroy(Task $task)
    {
        try {
            $board = $task->taskList->board;
            if (!$board->hasAccess(auth()->id())) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            
            $permission = $this->getUserPermission($board);
            if ($permission !== 'owner' && $permission !== 'edit') {
                return response()->json(['error' => 'No edit permission'], 403);
            }
            
            if ($task->recurringTask) {
                $task->recurringTask->delete();
                
                TaskActivity::create([
                    'action' => 'removed_recurring',
                    'user_id' => auth()->id(),
                    'task_id' => $task->id,
                    'old_value' => 'Recurring'
                ]);
                
                $task->notifyWatchers('removed_recurring', auth()->id(), $task->id, 'Recurring', null);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Recurring task removed'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Recurring destroy error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    private function getUserPermission($board)
    {
        if ($board->user_id === auth()->id()) {
            return 'owner';
        }
        
        $sharedUser = $board->sharedUsers()->where('user_id', auth()->id())->first();
        return $sharedUser ? $sharedUser->pivot->permission : null;
    }
}