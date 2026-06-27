<?php

namespace App\Http\Controllers;

use App\Models\Board;
use App\Models\TaskActivity;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ActivityLogController extends Controller
{
    // ==============================================
    // SHOW ACTIVITY LOG PAGE
    // ==============================================
    
    public function index(Board $board)
    {
        if (!$board->hasAccess(auth()->id())) {
            abort(403, 'No access to this board');
        }
        
        $users = User::orderBy('name')->get();
        $actions = $this->getActionTypes();
        
        return view('boards.activity-log', compact('board', 'users', 'actions'));
    }
    
    // ==============================================
    // API: GET ACTIVITIES (with filters)
    // ==============================================
    
    public function api(Request $request, Board $board)
    {
        try {
            if (!$board->hasAccess(auth()->id())) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            
            $query = TaskActivity::whereHas('task', function($q) use ($board) {
                $q->whereIn('task_list_id', function($sub) use ($board) {
                    $sub->select('id')
                        ->from('task_lists')
                        ->where('board_id', $board->id);
                });
            })->with(['user', 'task']);
            
            // Filter by user
            if ($request->user_id) {
                $query->where('user_id', $request->user_id);
            }
            
            // Filter by action type
            if ($request->action) {
                $query->where('action', $request->action);
            }
            
            // Search by task title or description
            if ($request->search) {
                $search = '%' . $request->search . '%';
                $query->where(function($q) use ($search) {
                    $q->whereHas('task', function($sub) use ($search) {
                        $sub->where('title', 'LIKE', $search);
                    })->orWhere('old_value', 'LIKE', $search)
                      ->orWhere('new_value', 'LIKE', $search);
                });
            }
            
            // Filter by date range
            if ($request->date_from) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }
            if ($request->date_to) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }
            
            $activities = $query->orderBy('created_at', 'desc')
                ->paginate(30);
            
            // Format activities for display
            $formattedActivities = $this->formatActivities($activities->items());
            
            return response()->json([
                'success' => true,
                'activities' => $formattedActivities,
                'pagination' => [
                    'current_page' => $activities->currentPage(),
                    'last_page' => $activities->lastPage(),
                    'per_page' => $activities->perPage(),
                    'total' => $activities->total(),
                ]
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Activity log API error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    // ==============================================
    // FORMAT ACTIVITIES FOR DISPLAY
    // ==============================================
    
    private function formatActivities($activities)
    {
        $formatted = [];
        
        foreach ($activities as $activity) {
            $icon = $this->getIconForAction($activity->action);
            $actionText = $this->getActionText($activity);
            $taskTitle = $activity->task ? $activity->task->title : 'Unknown Task';
            $taskId = $activity->task ? $activity->task->id : null;
            $userName = $activity->user ? $activity->user->name : 'System';
            $userAvatar = $activity->user ? $this->getUserAvatar($activity->user) : null;
            
            $formatted[] = [
                'id' => $activity->id,
                'icon' => $icon,
                'icon_color' => $this->getIconColor($activity->action),
                'action_text' => $actionText,
                'task_title' => $taskTitle,
                'task_id' => $taskId,
                'user_name' => $userName,
                'user_avatar' => $userAvatar,
                'created_at' => $activity->created_at,
                'created_at_human' => $activity->created_at->diffForHumans(),
                'created_at_formatted' => $activity->created_at->format('d M Y, H:i')
            ];
        }
        
        return $formatted;
    }
    
    private function getIconForAction($action)
    {
        $icons = [
            'created' => '✅',
            'updated_title' => '✏️',
            'updated_description' => '📝',
            'updated_priority' => '🎯',
            'updated_due_date' => '📅',
            'moved' => '🔄',
            'deleted' => '🗑️',
            'assigned' => '👤',
            'unassigned' => '👤',
            'commented' => '💬',
            'archived' => '📦',
            'unarchived' => '📦',
            'bulk_archived' => '📦',
            'bulk_restored' => '📦',
            'auto_archived' => '🤖',
            'added_checklist' => '✅',
            'updated_checklist' => '✅',
            'deleted_checklist' => '🗑️',
            'uploaded_file' => '📎',
            'deleted_file' => '🗑️',
            'set_cover' => '🖼️',
            'removed_cover' => '🖼️',
            'assigned_label' => '🏷️',
            'removed_label' => '🏷️',
            'created_from_template' => '📋',
        ];
        
        return $icons[$action] ?? '📌';
    }
    
    private function getIconColor($action)
    {
        $colors = [
            'created' => 'text-green-600',
            'updated_title' => 'text-blue-600',
            'updated_description' => 'text-blue-600',
            'updated_priority' => 'text-yellow-600',
            'updated_due_date' => 'text-orange-600',
            'moved' => 'text-purple-600',
            'deleted' => 'text-red-600',
            'commented' => 'text-teal-600',
            'archived' => 'text-gray-600',
            'auto_archived' => 'text-gray-600',
            'uploaded_file' => 'text-cyan-600',
            'set_cover' => 'text-emerald-600',
        ];
        
        return $colors[$action] ?? 'text-gray-500';
    }
    
    private function getActionText($activity)
    {
        $userName = $activity->user ? $activity->user->name : 'System';
        
        switch ($activity->action) {
            case 'created':
                return "created this task";
            case 'updated_title':
                return "changed title from '{$activity->old_value}' to '{$activity->new_value}'";
            case 'updated_description':
                return "updated the description";
            case 'updated_priority':
                return "changed priority from '{$activity->old_value}' to '{$activity->new_value}'";
            case 'updated_due_date':
                $old = $activity->old_value ?: 'not set';
                $new = $activity->new_value ?: 'not set';
                return "changed due date from '{$old}' to '{$new}'";
            case 'moved':
                return "moved from '{$activity->old_value}' to '{$activity->new_value}'";
            case 'deleted':
                return "deleted task '{$activity->old_value}'";
            case 'assigned':
                return "assigned to {$activity->new_value}";
            case 'unassigned':
                return "unassigned from {$activity->old_value}";
            case 'commented':
                return "added a comment";
            case 'deleted_comment':
                return "deleted a comment";
            case 'archived':
                return "archived this task";
            case 'unarchived':
                return "restored this task from archive";
            case 'bulk_archived':
                return "bulk archived this task";
            case 'bulk_restored':
                return "restored this task (bulk)";
            case 'auto_archived':
                return "auto archived";
            case 'added_checklist':
                return "added checklist '{$activity->new_value}'";
            case 'updated_checklist':
                return "renamed checklist from '{$activity->old_value}' to '{$activity->new_value}'";
            case 'deleted_checklist':
                return "deleted checklist '{$activity->old_value}'";
            case 'checked_item':
                return "checked item '{$activity->old_value}'";
            case 'unchecked_item':
                return "unchecked item '{$activity->old_value}'";
            case 'uploaded_file':
                return "uploaded file '{$activity->new_value}'";
            case 'deleted_file':
                return "deleted file '{$activity->old_value}'";
            case 'set_cover':
                return "set cover image to '{$activity->new_value}'";
            case 'removed_cover':
                return "removed cover image";
            case 'assigned_label':
                return "added label '{$activity->new_value}'";
            case 'removed_label':
                return "removed label '{$activity->old_value}'";
            case 'created_from_template':
                return "created from template '{$activity->new_value}'";
            default:
                return $activity->action;
        }
    }
    
    private function getUserAvatar($user)
    {
        if ($user->avatar) {
            return asset($user->avatar);
        }
        return 'https://ui-avatars.com/api/?background=1e3a5f&color=fff&name=' . urlencode($user->name);
    }
    
    private function getActionTypes()
    {
        return [
            'created' => 'Task Created',
            'updated_title' => 'Title Changed',
            'updated_description' => 'Description Changed',
            'updated_priority' => 'Priority Changed',
            'updated_due_date' => 'Due Date Changed',
            'moved' => 'Task Moved',
            'deleted' => 'Task Deleted',
            'archived' => 'Task Archived',
            'unarchived' => 'Task Restored',
            'commented' => 'Comment Added',
            'assigned' => 'Assignee Added',
            'unassigned' => 'Assignee Removed',
            'added_checklist' => 'Checklist Added',
            'uploaded_file' => 'File Uploaded',
            'assigned_label' => 'Label Added',
            'removed_label' => 'Label Removed',
        ];
    }
}