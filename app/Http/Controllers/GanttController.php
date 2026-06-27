<?php

namespace App\Http\Controllers;

use App\Models\Board;
use App\Models\Task;
use App\Models\GanttSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GanttController extends Controller
{
    // Show Gantt Chart page
    public function index(Board $board)
    {
        if (!$board->hasAccess(auth()->id())) {
            abort(403, 'No access to this board');
        }

        $permission = $this->getUserPermission($board);
        $settings = GanttSetting::getForBoard($board->id);

        return view('boards.gantt', compact('board', 'permission', 'settings'));
    }

    // API: Get tasks for Gantt chart
    public function getTasks(Request $request, Board $board)
    {
        try {
            if (!$board->hasAccess(auth()->id())) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $taskListIds = $board->lists()->pluck('id');

            $query = Task::whereIn('task_list_id', $taskListIds)
                ->with(['assignees', 'labels', 'taskList']);

            // Apply filters
            if ($request->list_id && $request->list_id !== 'all') {
                $query->where('task_list_id', $request->list_id);
            }

            if ($request->assignee_id && $request->assignee_id !== 'all') {
                $query->whereHas('assignees', function($q) use ($request) {
                    $q->where('user_id', $request->assignee_id);
                });
            }

            if ($request->label_id && $request->label_id !== 'all') {
                $query->whereHas('labels', function($q) use ($request) {
                    $q->where('label_id', $request->label_id);
                });
            }

            if ($request->priority && $request->priority !== 'all') {
                $query->where('priority', $request->priority);
            }

            // Filter archived or active
            if ($request->status === 'active') {
                $query->active();
            } elseif ($request->status === 'archived') {
                $query->archived();
            }

            $tasks = $query->get();

            $ganttData = [];
            foreach ($tasks as $task) {
                // Calculate start date (use start_date if set, otherwise created_at, otherwise due_date - duration)
                $startDate = $task->start_date;
                $endDate = $task->due_date;
                
                if (!$startDate && $endDate) {
                    $startDate = (clone $endDate)->subDays($task->duration - 1);
                }
                
                if (!$startDate && !$endDate) {
                    $startDate = $task->created_at ? $task->created_at->format('Y-m-d') : now()->format('Y-m-d');
                    $endDate = (clone $startDate)->addDays($task->duration - 1);
                }

                $priorityColors = [
                    'high' => '#ef4444',
                    'medium' => '#f59e0b',
                    'low' => '#10b981',
                ];

                $ganttData[] = [
                    'id' => $task->id,
                    'name' => $task->title,
                    'start' => $startDate ? date('Y-m-d', strtotime($startDate)) : null,
                    'end' => $endDate ? date('Y-m-d', strtotime($endDate)) : null,
                    'progress' => $task->progress ?? 0,
                    'priority' => $task->priority,
                    'priority_color' => $priorityColors[$task->priority] ?? '#6b7280',
                    'list_name' => $task->taskList ? $task->taskList->name : 'Unknown',
                    'list_id' => $task->task_list_id,
                    'assignees' => $task->assignees->map(function($assignee) {
                        return [
                            'id' => $assignee->id,
                            'name' => $assignee->name,
                            'avatar' => $assignee->getAvatarUrlAttribute(),
                        ];
                    }),
                    'labels' => $task->labels,
                    'is_archived' => $task->isArchived(),
                ];
            }

            return response()->json([
                'success' => true,
                'tasks' => $ganttData,
            ]);

        } catch (\Exception $e) {
            \Log::error('Gantt API error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // API: Update task date/duration from Gantt drag & drop
    public function updateTask(Request $request, Board $board)
    {
        try {
            if (!$board->hasAccess(auth()->id())) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $permission = $this->getUserPermission($board);
            if ($permission !== 'owner' && $permission !== 'edit') {
                return response()->json(['error' => 'No edit permission'], 403);
            }

            $request->validate([
                'task_id' => 'required|exists:tasks,id',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date',
                'duration' => 'nullable|integer|min:1',
            ]);

            $task = Task::find($request->task_id);
            $oldStart = $task->start_date;
            $oldEnd = $task->due_date;
            $oldDuration = $task->duration;

            $task->start_date = $request->start_date;
            $task->due_date = $request->end_date;
            
            if ($request->has('duration')) {
                $task->duration = $request->duration;
            } else if ($request->start_date && $request->end_date) {
                // Calculate duration from start and end dates
                $start = new \DateTime($request->start_date);
                $end = new \DateTime($request->end_date);
                $task->duration = $start->diff($end)->days + 1;
            }
            
            $task->save();

            // Log activity
            \App\Models\TaskActivity::create([
                'action' => 'updated_gantt',
                'user_id' => auth()->id(),
                'task_id' => $task->id,
                'old_value' => "Start: {$oldStart}, End: {$oldEnd}, Duration: {$oldDuration}",
                'new_value' => "Start: {$task->start_date}, End: {$task->due_date}, Duration: {$task->duration}",
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Task updated successfully',
                'task' => [
                    'id' => $task->id,
                    'start' => $task->start_date,
                    'end' => $task->due_date,
                    'duration' => $task->duration,
                ],
            ]);

        } catch (\Exception $e) {
            \Log::error('Update gantt task error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // API: Update task progress
    public function updateProgress(Request $request, Board $board)
    {
        try {
            if (!$board->hasAccess(auth()->id())) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $permission = $this->getUserPermission($board);
            if ($permission !== 'owner' && $permission !== 'edit') {
                return response()->json(['error' => 'No edit permission'], 403);
            }

            $request->validate([
                'task_id' => 'required|exists:tasks,id',
                'progress' => 'required|integer|min:0|max:100',
            ]);

            $task = Task::find($request->task_id);
            $task->progress = $request->progress;
            $task->save();

            return response()->json([
                'success' => true,
                'message' => 'Progress updated',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // API: Save Gantt settings
    public function saveSettings(Request $request, Board $board)
    {
        try {
            if (!$board->hasAccess(auth()->id())) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $settings = GanttSetting::getForBoard($board->id);
            $settings->update($request->only([
                'view_mode', 'zoom_level', 'show_weekends', 'show_progress', 'show_dependencies', 'filters'
            ]));

            return response()->json([
                'success' => true,
                'settings' => $settings,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // Export Gantt as image
    public function export(Request $request, Board $board)
    {
        // This will be handled by frontend using html2canvas
        // Return the HTML for export
        return view('boards.gantt-export', compact('board'));
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