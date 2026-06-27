<?php

namespace App\Http\Controllers;

use App\Models\Board;
use App\Models\Task;
use App\Models\TaskList;
use App\Models\TaskTemplate;
use App\Models\TaskActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TaskTemplateController extends Controller
{
    // ==============================================
    // GET ALL TEMPLATES FOR A BOARD
    // ==============================================
    
    public function index(Board $board)
    {
        try {
            if (!$board->hasAccess(auth()->id())) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            
            $templates = $board->taskTemplates()
                ->orderBy('name')
                ->get();
            
            return response()->json($templates);
            
        } catch (\Exception $e) {
            \Log::error('Get templates error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    // ==============================================
    // STORE TEMPLATE (Manual or from existing task)
    // ==============================================
    
    public function store(Request $request, Board $board)
    {
        try {
            if (!$board->hasAccess(auth()->id())) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            
            $request->validate([
                'name' => 'required|string|max:255',
                'task_id' => 'nullable|exists:tasks,id'
            ]);
            
            $checklistItems = [];
            $labelIds = [];
            $assigneeIds = [];
            $description = $request->description;
            $priority = $request->priority ?? 'medium';
            
            // IF FROM EXISTING TASK
            if ($request->task_id) {
                $task = Task::with(['checklists.items', 'labels', 'assignees'])->find($request->task_id);
                
                if ($task) {
                    $description = $task->description;
                    $priority = $task->priority;
                    
                    // Copy checklist items from task
                    foreach ($task->checklists as $checklist) {
                        foreach ($checklist->items as $item) {
                            $checklistItems[] = [
                                'name' => $item->name,
                                'is_checked' => false
                            ];
                        }
                    }
                    
                    // Copy label IDs
                    $labelIds = $task->labels->pluck('id')->toArray();
                    
                    // Copy assignee IDs
                    $assigneeIds = $task->assignees->pluck('id')->toArray();
                } else {
                    return response()->json([
                        'success' => false,
                        'error' => 'Task not found'
                    ], 404);
                }
            } 
            // IF MANUAL CREATE
            else {
                if ($request->has('checklist_items')) {
                    $checklistItems = $request->checklist_items;
                }
                if ($request->has('label_ids')) {
                    $labelIds = $request->label_ids;
                }
                if ($request->has('assignee_ids')) {
                    $assigneeIds = $request->assignee_ids;
                }
                $description = $request->description;
                $priority = $request->priority ?? 'medium';
            }
            
            $template = TaskTemplate::create([
                'name' => $request->name,
                'description' => $description,
                'priority' => $priority,
                'checklist_items' => $checklistItems,
                'label_ids' => $labelIds,
                'assignee_ids' => $assigneeIds,
                'board_id' => $board->id,
                'user_id' => auth()->id()
            ]);
            
            return response()->json([
                'success' => true,
                'template' => $template,
                'message' => 'Template created successfully'
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Store template error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    // ==============================================
    // UPDATE TEMPLATE
    // ==============================================
    
    public function update(Request $request, TaskTemplate $taskTemplate)
    {
        try {
            if ($taskTemplate->user_id !== auth()->id() && $taskTemplate->board->user_id !== auth()->id()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            
            $request->validate([
                'name' => 'required|string|max:255'
            ]);
            
            $taskTemplate->update($request->only([
                'name', 'description', 'priority', 'checklist_items', 'label_ids', 'assignee_ids'
            ]));
            
            return response()->json([
                'success' => true,
                'template' => $taskTemplate,
                'message' => 'Template updated successfully'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    // ==============================================
    // DELETE TEMPLATE
    // ==============================================
    
    public function destroy(TaskTemplate $taskTemplate)
    {
        try {
            if ($taskTemplate->user_id !== auth()->id() && $taskTemplate->board->user_id !== auth()->id()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            
            $taskTemplate->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Template deleted successfully'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    // ==============================================
    // CREATE TASK FROM TEMPLATE
    // ==============================================
    
    public function createTask(Request $request, TaskTemplate $taskTemplate)
    {
        try {
            $request->validate([
                'task_list_id' => 'required|exists:task_lists,id'
            ]);
            
            $taskList = TaskList::find($request->task_list_id);
            
            $board = $taskList->board;
            if (!$board->hasAccess(auth()->id())) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            
            $permission = $this->getUserPermission($board);
            if ($permission !== 'owner' && $permission !== 'edit') {
                return response()->json(['error' => 'No edit permission'], 403);
            }
            
            $maxOrder = $taskList->tasks()->max('order') ?? -1;
            
            DB::beginTransaction();
            
            $task = $taskList->tasks()->create([
                'title' => $taskTemplate->name,
                'description' => $taskTemplate->description,
                'priority' => $taskTemplate->priority,
                'order' => $maxOrder + 1,
                'due_date' => $request->due_date ?? null
            ]);
            
            if (!empty($taskTemplate->assignee_ids)) {
                $task->assignees()->sync($taskTemplate->assignee_ids);
            }
            
            if (!empty($taskTemplate->label_ids)) {
                $task->labels()->sync($taskTemplate->label_ids);
            }
            
            if (!empty($taskTemplate->checklist_items)) {
                $checklist = $task->checklists()->create([
                    'name' => 'Checklist',
                    'order' => 0
                ]);
                
                foreach ($taskTemplate->checklist_items as $index => $item) {
                    $checklist->items()->create([
                        'name' => $item['name'],
                        'is_checked' => false,
                        'order' => $index
                    ]);
                }
            }
            
            TaskActivity::create([
                'action' => 'created_from_template',
                'user_id' => auth()->id(),
                'task_id' => $task->id,
                'new_value' => "Created from template '{$taskTemplate->name}'"
            ]);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'task' => $task->load('assignees', 'labels', 'checklists'),
                'message' => 'Task created from template successfully'
            ]);
            
        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Create task from template error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
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