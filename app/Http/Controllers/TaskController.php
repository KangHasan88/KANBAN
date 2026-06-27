<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskList;
use App\Models\TaskActivity;
use App\Models\User;
use App\Models\Board;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TaskController extends Controller
{
    // ==============================================
    // STORE TASK
    // ==============================================
    
    public function store(Request $request, TaskList $taskList)
    {
        $request->validate(['title' => 'required|string|max:255']);
        
        $maxOrder = $taskList->tasks()->max('order') ?? -1;
        
        $task = $taskList->tasks()->create([
            'title' => $request->title,
            'description' => $request->description,
            'order' => $maxOrder + 1,
            'priority' => $request->priority ?? 'medium',
            'due_date' => $request->due_date,
        ]);
        
        // Handle assignees
        if ($request->has('assignees')) {
            $task->assignees()->sync($request->assignees);
        }
        
        TaskActivity::create([
            'action' => 'created',
            'user_id' => auth()->id(),
            'task_id' => $task->id
        ]);
        
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json($task->load('assignees', 'labels'));
        }
        
        return redirect()->back();
    }
    
    // ==============================================
    // UPDATE TASK - WITH WATCHER NOTIFICATION (FIXED)
    // ==============================================
    
    public function update(Request $request, Task $task)
    {
        $oldValues = $task->getOriginal();
        
        Log::info('Task update started', ['task_id' => $task->id, 'user_id' => auth()->id()]);
        
        // Track title changes
        if ($request->has('title') && $oldValues['title'] != $request->title) {
            $action = 'updated_title';
            $oldValue = $oldValues['title'];
            $newValue = $request->title;
            
            TaskActivity::create([
                'action' => $action,
                'user_id' => auth()->id(),
                'task_id' => $task->id,
                'field' => 'title',
                'old_value' => $oldValue,
                'new_value' => $newValue
            ]);
            
            // 🔔 NOTIFY WATCHERS
            Log::info('Notifying watchers - title changed', ['task_id' => $task->id]);
            $task->notifyWatchers($action, auth()->id(), $task->id, $oldValue, $newValue);
        }
        
        // Track description changes
        if ($request->has('description') && $oldValues['description'] != $request->description) {
            $action = 'updated_description';
            $oldValue = $oldValues['description'] ?? '(empty)';
            $newValue = $request->description ?? '(empty)';
            
            TaskActivity::create([
                'action' => $action,
                'user_id' => auth()->id(),
                'task_id' => $task->id,
                'field' => 'description',
                'old_value' => $oldValue,
                'new_value' => $newValue
            ]);
            
            // 🔔 NOTIFY WATCHERS
            Log::info('Notifying watchers - description changed', ['task_id' => $task->id]);
            $task->notifyWatchers($action, auth()->id(), $task->id, $oldValue, $newValue);
        }
        
        // Track priority changes
        if ($request->has('priority') && $oldValues['priority'] != $request->priority) {
            $action = 'updated_priority';
            $oldValue = $oldValues['priority'];
            $newValue = $request->priority;
            
            TaskActivity::create([
                'action' => $action,
                'user_id' => auth()->id(),
                'task_id' => $task->id,
                'field' => 'priority',
                'old_value' => $oldValue,
                'new_value' => $newValue
            ]);
            
            // 🔔 NOTIFY WATCHERS
            Log::info('Notifying watchers - priority changed', ['task_id' => $task->id]);
            $task->notifyWatchers($action, auth()->id(), $task->id, $oldValue, $newValue);
        }
        
        // Track due date changes
        if ($request->has('due_date') && $oldValues['due_date'] != $request->due_date) {
            $action = 'updated_due_date';
            $oldValue = $oldValues['due_date'] ?? '(not set)';
            $newValue = $request->due_date ?? '(not set)';
            
            TaskActivity::create([
                'action' => $action,
                'user_id' => auth()->id(),
                'task_id' => $task->id,
                'field' => 'due_date',
                'old_value' => $oldValue,
                'new_value' => $newValue
            ]);
            
            // 🔔 NOTIFY WATCHERS
            Log::info('Notifying watchers - due date changed', ['task_id' => $task->id]);
            $task->notifyWatchers($action, auth()->id(), $task->id, $oldValue, $newValue);
        }
        
        // Update task basic info
        $task->update($request->only('title', 'description', 'priority', 'due_date'));
        
        // Multiple assignees - sync with watcher notification
        if ($request->has('assignees')) {
            $newAssigneeIds = $request->assignees;
            $oldAssigneeIds = $task->assignees->pluck('id')->toArray();
            
            $task->assignees()->sync($newAssigneeIds);
            
            $added = array_diff($newAssigneeIds, $oldAssigneeIds);
            $removed = array_diff($oldAssigneeIds, $newAssigneeIds);
            
            foreach ($added as $userId) {
                $user = User::find($userId);
                $userName = $user ? $user->name : 'User';
                TaskActivity::create([
                    'action' => 'assigned',
                    'user_id' => auth()->id(),
                    'task_id' => $task->id,
                    'field' => 'assignees',
                    'old_value' => null,
                    'new_value' => $userName
                ]);
                
                // 🔔 NOTIFY WATCHERS
                Log::info('Notifying watchers - assigned', ['task_id' => $task->id, 'user' => $userName]);
                $task->notifyWatchers('assigned', auth()->id(), $task->id, null, $userName);
            }
            
            foreach ($removed as $userId) {
                $user = User::find($userId);
                $userName = $user ? $user->name : 'User';
                TaskActivity::create([
                    'action' => 'unassigned',
                    'user_id' => auth()->id(),
                    'task_id' => $task->id,
                    'field' => 'assignees',
                    'old_value' => $userName,
                    'new_value' => null
                ]);
                
                // 🔔 NOTIFY WATCHERS
                Log::info('Notifying watchers - unassigned', ['task_id' => $task->id, 'user' => $userName]);
                $task->notifyWatchers('unassigned', auth()->id(), $task->id, $userName, null);
            }
        }
        
        // Track list movement
        if ($request->has('task_list_id') && $oldValues['task_list_id'] != $request->task_list_id) {
            $oldList = TaskList::find($oldValues['task_list_id']);
            $newList = TaskList::find($request->task_list_id);
            $oldListName = $oldList ? $oldList->name : 'Unknown';
            $newListName = $newList ? $newList->name : 'Unknown';
            
            TaskActivity::create([
                'action' => 'moved',
                'user_id' => auth()->id(),
                'task_id' => $task->id,
                'field' => 'task_list_id',
                'old_value' => $oldListName,
                'new_value' => $newListName
            ]);
            
            // 🔔 NOTIFY WATCHERS
            Log::info('Notifying watchers - moved', ['task_id' => $task->id, 'from' => $oldListName, 'to' => $newListName]);
            $task->notifyWatchers('moved', auth()->id(), $task->id, $oldListName, $newListName);
            
            $task->update(['task_list_id' => $request->task_list_id]);
        }
        
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json($task->load('assignees', 'labels'));
        }
        
        return redirect()->back();
    }
    
    // ==============================================
    // REORDER TASKS (Drag & Drop)
    // ==============================================
    
    public function reorder(Request $request)
    {
        try {
            DB::beginTransaction();
            
            $tasksData = $request->input('tasks', []);
            
            foreach ($tasksData as $listId => $taskIds) {
                foreach ($taskIds as $index => $taskId) {
                    $task = Task::find($taskId);
                    if ($task) {
                        $oldListId = $task->task_list_id;
                        $task->order = $index;
                        
                        if ($oldListId != $listId) {
                            $task->task_list_id = $listId;
                            
                            $oldList = TaskList::find($oldListId);
                            $newList = TaskList::find($listId);
                            $oldListName = $oldList ? $oldList->name : 'Unknown';
                            $newListName = $newList ? $newList->name : 'Unknown';
                            
                            TaskActivity::create([
                                'action' => 'moved',
                                'user_id' => auth()->id(),
                                'task_id' => $task->id,
                                'field' => 'task_list_id',
                                'old_value' => $oldListName,
                                'new_value' => $newListName
                            ]);
                            
                            // 🔔 NOTIFY WATCHERS
                            Log::info('Notifying watchers - reorder moved', ['task_id' => $task->id]);
                            $task->notifyWatchers('moved', auth()->id(), $task->id, $oldListName, $newListName);
                        }
                        
                        $task->save();
                    }
                }
            }
            
            DB::commit();
            return response()->json(['success' => true]);
            
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Reorder error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    
    // ==============================================
    // MOVE TASK
    // ==============================================
    
    public function move(Request $request, Task $task)
    {
        $oldListId = $task->task_list_id;
        $oldList = TaskList::find($oldListId);
        $newList = TaskList::find($request->task_list_id);
        $oldListName = $oldList ? $oldList->name : 'Unknown';
        $newListName = $newList ? $newList->name : 'Unknown';
        
        $task->update(['task_list_id' => $request->task_list_id]);
        
        TaskActivity::create([
            'action' => 'moved',
            'user_id' => auth()->id(),
            'task_id' => $task->id,
            'field' => 'task_list_id',
            'old_value' => $oldListName,
            'new_value' => $newListName
        ]);
        
        // 🔔 NOTIFY WATCHERS
        Log::info('Notifying watchers - move', ['task_id' => $task->id]);
        $task->notifyWatchers('moved', auth()->id(), $task->id, $oldListName, $newListName);
        
        return response()->json(['success' => true]);
    }
    
    // ==============================================
    // EDIT TASK - INCLUDE ASSIGNEES (MULTIPLE)
    // ==============================================
    
    public function edit(Task $task)
    {
        try {
            if (request()->ajax() || request()->wantsJson()) {
                $task->load('assignees', 'labels', 'taskList', 'checklists.items', 'attachments', 'comments');
                return response()->json($task);
            }
            return redirect()->back();
        } catch (\Exception $e) {
            Log::error('Task edit error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    // ==============================================
    // GET ACTIVITIES HISTORY
    // ==============================================
    
    public function activities(Task $task)
    {
        try {
            $activities = $task->activities()
                ->with('user')
                ->orderBy('created_at', 'desc')
                ->get();
            
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json($activities);
            }
            
            return view('tasks.activities', compact('task', 'activities'));
            
        } catch (\Exception $e) {
            Log::error('Activities error: ' . $e->getMessage());
            return response()->json([
                'error' => $e->getMessage(),
                'message' => 'Failed to load activities'
            ], 500);
        }
    }
    
    // ==============================================
    // GET ASSIGNABLE USERS
    // ==============================================
    
    public function getAssignableUsers(Task $task)
    {
        try {
            $users = User::select('id', 'name', 'username', 'email')
                ->orderBy('name')
                ->get();
            
            $assignedUserIds = $task->assignees->pluck('id')->toArray();
            
            return response()->json([
                'all_users' => $users,
                'assigned_users' => $assignedUserIds
            ]);
            
        } catch (\Exception $e) {
            Log::error('Get assignable users error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    // ==============================================
    // DELETE TASK - ADMIN ONLY
    // ==============================================
    
    public function deleteTask(Task $task)
    {
        try {
            if (!auth()->user()->isAdmin()) {
                if (request()->ajax() || request()->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Only administrators can delete tasks'
                    ], 403);
                }
                return redirect()->back()->with('error', 'Only administrators can delete tasks');
            }
            
            TaskActivity::create([
                'action' => 'deleted',
                'user_id' => auth()->id(),
                'task_id' => $task->id,
                'old_value' => $task->title,
                'new_value' => 'deleted by ' . auth()->user()->name
            ]);
            
            $taskTitle = $task->title;
            $task->delete();
            
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Task "' . $taskTitle . '" deleted successfully'
                ]);
            }
            
            return redirect()->back()->with('success', 'Task deleted successfully');
            
        } catch (\Exception $e) {
            Log::error('Delete task error: ' . $e->getMessage());
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with('error', 'Failed to delete task');
        }
    }
    
    // ==============================================
    // ARCHIVE TASK - WITH WATCHER NOTIFICATION
    // ==============================================
    
    public function archive(Task $task)
    {
        try {
            $board = $task->taskList->board;
            if (!$board->hasAccess(auth()->id())) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            
            $originalListName = $task->taskList->name;
            
            $task->archive();
            
            TaskActivity::create([
                'action' => 'archived',
                'user_id' => auth()->id(),
                'task_id' => $task->id,
                'field' => 'archived_at',
                'old_value' => null,
                'new_value' => "Archived from '{$originalListName}' list"
            ]);
            
            // 🔔 NOTIFY WATCHERS
            Log::info('Notifying watchers - archived', ['task_id' => $task->id]);
            $task->notifyWatchers('archived', auth()->id(), $task->id, null, $originalListName);
            
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Task archived successfully'
                ]);
            }
            
            return redirect()->back()->with('success', 'Task archived');
            
        } catch (\Exception $e) {
            Log::error('Archive error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    // ==============================================
    // UNARCHIVE (RESTORE) TASK - WITH WATCHER NOTIFICATION
    // ==============================================
    
    public function unarchive(Task $task)
    {
        try {
            $board = $task->taskList->board;
            if (!$board->hasAccess(auth()->id())) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            
            $task->unarchive();
            
            TaskActivity::create([
                'action' => 'unarchived',
                'user_id' => auth()->id(),
                'task_id' => $task->id,
                'field' => 'archived_at',
                'old_value' => 'Archived',
                'new_value' => 'Restored to active'
            ]);
            
            // 🔔 NOTIFY WATCHERS
            Log::info('Notifying watchers - unarchived', ['task_id' => $task->id]);
            $task->notifyWatchers('unarchived', auth()->id(), $task->id, 'Archived', 'Restored');
            
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Task restored successfully'
                ]);
            }
            
            return redirect()->back()->with('success', 'Task restored');
            
        } catch (\Exception $e) {
            Log::error('Unarchive error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    // ==============================================
    // SHOW ARCHIVED TASKS PAGE
    // ==============================================
    
    public function archived(Board $board)
    {
        if (!$board->hasAccess(auth()->id())) {
            abort(403, 'No access to this board');
        }
        
        $archivedTasks = Task::whereIn('task_list_id', $board->lists->pluck('id'))
            ->archived()
            ->with(['taskList', 'assignees', 'labels', 'attachments', 'comments'])
            ->orderBy('archived_at', 'desc')
            ->get();
        
        $permission = $this->getUserPermissionForBoard($board);
        
        return view('boards.archived', compact('board', 'archivedTasks', 'permission'));
    }
    
    // ==============================================
    // PERMANENTLY DELETE TASK (from archive)
    // ==============================================
    
    public function forceDelete(Task $task)
    {
        try {
            if (!auth()->user()->isAdmin()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only administrators can permanently delete tasks'
                ], 403);
            }
            
            $taskTitle = $task->title;
            
            foreach ($task->attachments as $attachment) {
                $filePath = str_replace('/storage/', '', $attachment->file_path);
                $fullPath = storage_path('app/public/' . $filePath);
                if (file_exists($fullPath)) {
                    unlink($fullPath);
                }
            }
            
            $task->forceDelete();
            
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Task '{$taskTitle}' permanently deleted"
                ]);
            }
            
            return redirect()->back()->with('success', 'Task permanently deleted');
            
        } catch (\Exception $e) {
            Log::error('Force delete error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    // ==============================================
    // CLEAR ALL ARCHIVED TASKS IN BOARD
    // ==============================================
    
    public function clearArchived(Board $board)
    {
        try {
            if (!auth()->user()->isAdmin()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only administrators can clear archived tasks'
                ], 403);
            }
            
            $archivedTasks = Task::whereIn('task_list_id', $board->lists->pluck('id'))
                ->archived()
                ->get();
            
            $count = $archivedTasks->count();
            
            foreach ($archivedTasks as $task) {
                foreach ($task->attachments as $attachment) {
                    $filePath = str_replace('/storage/', '', $attachment->file_path);
                    $fullPath = storage_path('app/public/' . $filePath);
                    if (file_exists($fullPath)) {
                        unlink($fullPath);
                    }
                }
                $task->forceDelete();
            }
            
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "{$count} archived tasks permanently deleted"
                ]);
            }
            
            return redirect()->back()->with('success', "{$count} archived tasks cleared");
            
        } catch (\Exception $e) {
            Log::error('Clear archived error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    // ==============================================
    // BULK ARCHIVE
    // ==============================================
    
    public function bulkArchive(Request $request)
    {
        try {
            $request->validate([
                'task_ids' => 'required|array',
                'task_ids.*' => 'integer|exists:tasks,id'
            ]);
            
            $taskIds = $request->task_ids;
            $archivedCount = 0;
            $failedCount = 0;
            
            foreach ($taskIds as $taskId) {
                $task = Task::find($taskId);
                if (!$task) {
                    $failedCount++;
                    continue;
                }
                
                $board = $task->taskList->board;
                $hasAccess = $board->hasAccess(auth()->id());
                
                if (!$hasAccess) {
                    $failedCount++;
                    continue;
                }
                
                $permission = 'view';
                if ($board->user_id === auth()->id()) {
                    $permission = 'owner';
                } else {
                    $sharedUser = $board->sharedUsers()->where('user_id', auth()->id())->first();
                    if ($sharedUser) {
                        $permission = $sharedUser->pivot->permission;
                    }
                }
                
                if ($permission !== 'owner' && $permission !== 'edit') {
                    $failedCount++;
                    continue;
                }
                
                if ($task->isArchived()) {
                    continue;
                }
                
                $originalListName = $task->taskList->name;
                $task->archive();
                
                TaskActivity::create([
                    'action' => 'bulk_archived',
                    'user_id' => auth()->id(),
                    'task_id' => $task->id,
                    'field' => 'bulk_archive',
                    'old_value' => null,
                    'new_value' => "Bulk archived from '{$originalListName}' list"
                ]);
                
                $archivedCount++;
            }
            
            $message = "{$archivedCount} task(s) archived successfully";
            if ($failedCount > 0) {
                $message .= ", {$failedCount} task(s) failed (no permission or not found)";
            }
            
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'archived_count' => $archivedCount,
                    'failed_count' => $failedCount,
                    'message' => $message
                ]);
            }
            
            return redirect()->back()->with('success', $message);
            
        } catch (\Exception $e) {
            Log::error('Bulk archive error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    // ==============================================
    // BULK RESTORE
    // ==============================================
    
    public function bulkRestore(Request $request)
    {
        try {
            $request->validate([
                'task_ids' => 'required|array',
                'task_ids.*' => 'integer|exists:tasks,id'
            ]);
            
            $taskIds = $request->task_ids;
            $restoredCount = 0;
            $failedCount = 0;
            
            foreach ($taskIds as $taskId) {
                $task = Task::find($taskId);
                if (!$task) {
                    $failedCount++;
                    continue;
                }
                
                $board = $task->taskList->board;
                if (!$board->hasAccess(auth()->id())) {
                    $failedCount++;
                    continue;
                }
                
                $permission = 'view';
                if ($board->user_id === auth()->id()) {
                    $permission = 'owner';
                } else {
                    $sharedUser = $board->sharedUsers()->where('user_id', auth()->id())->first();
                    if ($sharedUser) {
                        $permission = $sharedUser->pivot->permission;
                    }
                }
                
                if ($permission !== 'owner' && $permission !== 'edit') {
                    $failedCount++;
                    continue;
                }
                
                if (!$task->isArchived()) {
                    continue;
                }
                
                $task->unarchive();
                
                TaskActivity::create([
                    'action' => 'bulk_restored',
                    'user_id' => auth()->id(),
                    'task_id' => $task->id,
                    'field' => 'bulk_restore',
                    'old_value' => 'Archived',
                    'new_value' => 'Restored to active via bulk restore'
                ]);
                
                $restoredCount++;
            }
            
            $message = "{$restoredCount} task(s) restored successfully";
            if ($failedCount > 0) {
                $message .= ", {$failedCount} task(s) failed (no permission or not found)";
            }
            
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'restored_count' => $restoredCount,
                    'failed_count' => $failedCount,
                    'message' => $message
                ]);
            }
            
            return redirect()->back()->with('success', $message);
            
        } catch (\Exception $e) {
            Log::error('Bulk restore error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    // ==============================================
    // PRIVATE HELPER
    // ==============================================
    
    private function getUserPermissionForBoard($board)
    {
        if ($board->user_id === auth()->id()) {
            return 'owner';
        }
        
        $sharedUser = $board->sharedUsers()->where('user_id', auth()->id())->first();
        return $sharedUser ? $sharedUser->pivot->permission : null;
    }
}