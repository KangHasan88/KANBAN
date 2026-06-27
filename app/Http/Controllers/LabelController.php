<?php

namespace App\Http\Controllers;

use App\Models\Board;
use App\Models\Label;
use App\Models\Task;
use Illuminate\Http\Request;

class LabelController extends Controller
{
    // Get all labels for a board
    public function index(Board $board)
    {
        try {
            if ($board->user_id !== auth()->id() && 
                !$board->sharedUsers()->where('user_id', auth()->id())->exists()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            
            $labels = $board->labels()->get();
            
            return response()->json($labels->toArray());
            
        } catch (\Exception $e) {
            \Log::error('Labels index error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    // Store new label
    public function store(Request $request, Board $board)
    {
        try {
            if ($board->user_id !== auth()->id()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            
            $request->validate([
                'name' => 'required|string|max:50',
                'color' => 'required|string|max:7',
                'description' => 'nullable|string|max:255'
            ]);
            
            $label = $board->labels()->create([
                'name' => $request->name,
                'color' => $request->color,
                'description' => $request->description,
                'user_id' => auth()->id()
            ]);
            
            return response()->json([
                'success' => true, 
                'label' => $label
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Labels store error: ' . $e->getMessage());
            return response()->json([
                'success' => false, 
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    // Update label
    public function update(Request $request, Label $label)
    {
        try {
            if ($label->user_id !== auth()->id() && $label->board->user_id !== auth()->id()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            
            $request->validate([
                'name' => 'required|string|max:50',
                'color' => 'required|string|max:7',
                'description' => 'nullable|string|max:255'
            ]);
            
            $label->update($request->only('name', 'color', 'description'));
            
            return response()->json([
                'success' => true, 
                'label' => $label
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Labels update error: ' . $e->getMessage());
            return response()->json([
                'success' => false, 
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    // Delete label
    public function destroy(Label $label)
    {
        try {
            if ($label->user_id !== auth()->id() && $label->board->user_id !== auth()->id()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            
            $label->delete();
            
            return response()->json(['success' => true]);
            
        } catch (\Exception $e) {
            \Log::error('Labels destroy error: ' . $e->getMessage());
            return response()->json([
                'success' => false, 
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    // Assign label to task - WITH WATCHER NOTIFICATION
    public function assign(Request $request, Task $task)
    {
        try {
            $request->validate([
                'label_id' => 'required|exists:labels,id'
            ]);
            
            $label = Label::find($request->label_id);
            
            // Check access
            if ($label->board_id !== $task->taskList->board_id) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            
            if (!$task->labels->contains($label->id)) {
                $task->labels()->attach($label->id);
                
                // Log activity
                \App\Models\TaskActivity::create([
                    'action' => 'assigned_label',
                    'user_id' => auth()->id(),
                    'task_id' => $task->id,
                    'field' => 'label',
                    'old_value' => null,
                    'new_value' => $label->name
                ]);
                
                // 🔔 NOTIFY WATCHERS ABOUT LABEL ASSIGNMENT
                $task->notifyWatchers('assigned_label', auth()->id(), $task->id, null, $label->name);
            }
            
            return response()->json(['success' => true]);
            
        } catch (\Exception $e) {
            \Log::error('Labels assign error: ' . $e->getMessage());
            return response()->json([
                'success' => false, 
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    // Remove label from task - WITH WATCHER NOTIFICATION
    public function remove(Task $task, Label $label)
    {
        try {
            if ($label->board_id !== $task->taskList->board_id) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            
            $labelName = $label->name;
            $task->labels()->detach($label->id);
            
            // Log activity
            \App\Models\TaskActivity::create([
                'action' => 'removed_label',
                'user_id' => auth()->id(),
                'task_id' => $task->id,
                'field' => 'label',
                'old_value' => $labelName,
                'new_value' => null
            ]);
            
            // 🔔 NOTIFY WATCHERS ABOUT LABEL REMOVAL
            $task->notifyWatchers('removed_label', auth()->id(), $task->id, $labelName, null);
            
            return response()->json(['success' => true]);
            
        } catch (\Exception $e) {
            \Log::error('Labels remove error: ' . $e->getMessage());
            return response()->json([
                'success' => false, 
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    // Get tasks by label
    public function tasks(Label $label)
    {
        try {
            if ($label->user_id !== auth()->id() && $label->board->user_id !== auth()->id()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            
            $tasks = $label->tasks()->with('taskList')->get();
            
            return response()->json($tasks);
            
        } catch (\Exception $e) {
            \Log::error('Labels tasks error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}