<?php

namespace App\Http\Controllers;

use App\Models\Board;
use App\Models\Task;
use App\Models\TaskActivity;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CalendarController extends Controller
{
    // ==============================================
    // SHOW CALENDAR PAGE
    // ==============================================
    
    public function index(Board $board)
    {
        if (!$board->hasAccess(auth()->id())) {
            abort(403, 'No access to this board');
        }
        
        $permission = $this->getUserPermission($board);
        
        return view('boards.calendar', compact('board', 'permission'));
    }
    
    // ==============================================
    // API: GET TASKS FOR CALENDAR (FIX URL ENCODING)
    // ==============================================
    
    public function getTasks(Request $request, Board $board)
    {
        try {
            if (!$board->hasAccess(auth()->id())) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            
            // Decode URL parameters
            $startParam = urldecode($request->start ?? '');
            $endParam = urldecode($request->end ?? '');
            
            // Remove timezone suffix (e.g., "+07:00", " 07:00")
            $startParam = preg_replace('/[\s\+]\d{2}:\d{2}(:\d{2})?$/', '', $startParam);
            $endParam = preg_replace('/[\s\+]\d{2}:\d{2}(:\d{2})?$/', '', $endParam);
            
            // Parse dates
            $startDate = !empty($startParam) ? Carbon::parse($startParam)->startOfDay() : Carbon::now()->startOfMonth();
            $endDate = !empty($endParam) ? Carbon::parse($endParam)->endOfDay() : Carbon::now()->endOfMonth();
            
            // Get task list IDs for this board
            $taskListIds = $board->lists()->pluck('id');
            
            $tasks = Task::whereIn('task_list_id', $taskListIds)
                ->whereNotNull('due_date')
                ->whereBetween('due_date', [$startDate, $endDate])
                ->with(['assignees', 'labels', 'taskList'])
                ->get();
            
            $events = [];
            foreach ($tasks as $task) {
                $priorityColor = $task->priority === 'high' ? '#ef4444' : ($task->priority === 'medium' ? '#f59e0b' : '#10b981');
                $isOverdue = Carbon::parse($task->due_date)->isPast() && !$task->isArchived();
                
                $events[] = [
                    'id' => $task->id,
                    'title' => $task->title,
                    'start' => $task->due_date->format('Y-m-d'),
                    'end' => $task->due_date->format('Y-m-d'),
                    'url' => '#',
                    'backgroundColor' => $isOverdue ? '#ef4444' : $priorityColor,
                    'borderColor' => $isOverdue ? '#dc2626' : $priorityColor,
                    'textColor' => '#ffffff',
                    'extendedProps' => [
                        'description' => $task->description,
                        'priority' => $task->priority,
                        'priority_color' => $priorityColor,
                        'is_overdue' => $isOverdue,
                        'list_name' => $task->taskList ? $task->taskList->name : 'Unknown',
                        'assignees' => $task->assignees,
                        'labels' => $task->labels
                    ]
                ];
            }
            
            return response()->json($events);
            
        } catch (\Exception $e) {
            \Log::error('Calendar API error: ' . $e->getMessage());
            return response()->json([
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }
    
    // ==============================================
    // UPDATE TASK DUE DATE (Drag & Drop)
    // ==============================================
    
    public function updateDueDate(Request $request, Board $board)
    {
        try {
            if (!$board->hasAccess(auth()->id())) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            
            $request->validate([
                'task_id' => 'required|exists:tasks,id',
                'due_date' => 'required|date'
            ]);
            
            $task = Task::find($request->task_id);
            
            if (!$task) {
                return response()->json(['error' => 'Task not found'], 404);
            }
            
            // Check permission for edit
            $permission = $this->getUserPermission($board);
            if ($permission !== 'owner' && $permission !== 'edit') {
                return response()->json(['error' => 'No edit permission'], 403);
            }
            
            $oldDueDate = $task->due_date ? $task->due_date->format('Y-m-d') : null;
            $newDueDate = $request->due_date;
            
            $task->update(['due_date' => $newDueDate]);
            
            // Log activity
            TaskActivity::create([
                'action' => 'updated_due_date',
                'user_id' => auth()->id(),
                'task_id' => $task->id,
                'field' => 'due_date',
                'old_value' => $oldDueDate ?? '(not set)',
                'new_value' => $newDueDate
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Due date updated successfully'
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Update due date error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    // ==============================================
    // GET USER PERMISSION FOR BOARD
    // ==============================================
    
    private function getUserPermission($board)
    {
        if ($board->user_id === auth()->id()) {
            return 'owner';
        }
        
        $sharedUser = $board->sharedUsers()->where('user_id', auth()->id())->first();
        return $sharedUser ? $sharedUser->pivot->permission : null;
    }
}