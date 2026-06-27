<?php

namespace App\Http\Controllers;

use App\Models\Board;
use App\Models\Task;
use App\Models\TimeEntry;
use App\Models\TaskActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function index(Board $board)
    {
        if (!$board->hasAccess(auth()->id())) {
            abort(403);
        }
        
        $permission = $this->getUserPermission($board);
        
        return view('boards.dashboard', compact('board', 'permission'));
    }
    
    public function getStats(Request $request, Board $board)
    {
        try {
            Log::info('Dashboard stats called for board: ' . $board->id);
            
            if (!$board->hasAccess(auth()->id())) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            
            // Get all task list IDs from this board
            $taskListIds = $board->lists()->pluck('id');
            $taskIds = Task::whereIn('task_list_id', $taskListIds)->pluck('id');
            
            // Basic stats
            $totalTasks = $taskIds->count();
            $completedTasks = Task::whereIn('id', $taskIds)->whereNotNull('archived_at')->count();
            $activeTasks = $totalTasks - $completedTasks;
            $completionRate = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
            
            // Priority stats
            $highPriority = Task::whereIn('id', $taskIds)->where('priority', 'high')->count();
            $mediumPriority = Task::whereIn('id', $taskIds)->where('priority', 'medium')->count();
            $lowPriority = Task::whereIn('id', $taskIds)->where('priority', 'low')->count();
            
            // List stats
            $listStats = [];
            foreach ($board->lists as $list) {
                $listStats[] = [
                    'name' => $list->name,
                    'count' => Task::where('task_list_id', $list->id)->count(),
                    'color' => $list->color,
                ];
            }
            
            // Trend stats (last 7 days)
            $trendStats = [];
            for ($i = 6; $i >= 0; $i--) {
                $date = now()->subDays($i);
                $trendStats[] = [
                    'date' => $date->format('d M'),
                    'total' => Task::whereIn('id', $taskIds)->whereDate('created_at', '<=', $date)->count(),
                    'completed' => Task::whereIn('id', $taskIds)->whereNotNull('archived_at')->whereDate('archived_at', '<=', $date)->count(),
                ];
            }
            
            // Assignee stats
            $assigneeStats = [];
            $users = collect([$board->owner])->merge($board->sharedUsers);
            foreach ($users as $user) {
                $userTaskIds = Task::whereIn('task_list_id', $taskListIds)
                    ->whereHas('assignees', function($q) use ($user) {
                        $q->where('user_id', $user->id);
                    })->pluck('id');
                
                $userTotal = $userTaskIds->count();
                $userCompleted = Task::whereIn('id', $userTaskIds)->whereNotNull('archived_at')->count();
                
                if ($userTotal > 0) {
                    $assigneeStats[] = [
                        'name' => $user->name,
                        'total' => $userTotal,
                        'completed' => $userCompleted,
                        'progress' => round(($userCompleted / $userTotal) * 100),
                        'avatar' => $this->getAvatarUrl($user),
                    ];
                }
            }
            usort($assigneeStats, function($a, $b) {
                return $b['total'] <=> $a['total'];
            });
            
            // Summary
            $totalSeconds = TimeEntry::whereIn('task_id', $taskIds)->sum('total_seconds');
            $hours = floor($totalSeconds / 3600);
            $minutes = floor(($totalSeconds % 3600) / 60);
            
            // ==============================================
            // RECENT ACTIVITIES - PERBAIKI INI
            // ==============================================
            $recentActivities = TaskActivity::whereIn('task_id', $taskIds)
                ->with('user')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();
            
            $formattedActivities = [];
            foreach ($recentActivities as $activity) {
                $formattedActivities[] = [
                    'action' => $activity->action,
                    'user_name' => $activity->user ? $activity->user->name : 'System',
                    'created_at' => $activity->created_at->format('d M Y H:i'),
                    'time_ago' => $activity->created_at->diffForHumans(),
                ];
            }
            
            $responseData = [
                'task_stats' => [
                    'total' => $totalTasks,
                    'active' => $activeTasks,
                    'completed' => $completedTasks,
                    'progress' => $completionRate,
                ],
                'priority_stats' => [
                    'high' => $highPriority,
                    'medium' => $mediumPriority,
                    'low' => $lowPriority,
                ],
                'list_stats' => $listStats,
                'trend_stats' => $trendStats,
                'assignee_stats' => $assigneeStats,
                'summary' => [
                    'total_time' => $hours . 'h ' . $minutes . 'm',
                    'completion_rate' => $completionRate,
                ],
                'activity_stats' => [
                    'recent' => $formattedActivities,
                    'by_action' => [
                        ['action' => 'created', 'count' => Task::whereIn('id', $taskIds)->count()],
                        ['action' => 'completed', 'count' => $completedTasks],
                    ],
                    'by_date' => [],
                ],
            ];
            
            Log::info('Dashboard stats response prepared successfully');
            
            return response()->json([
                'success' => true,
                'data' => $responseData
            ]);
            
        } catch (\Exception $e) {
            Log::error('Dashboard stats error: ' . $e->getMessage());
            Log::error('Error trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    private function getAvatarUrl($user)
    {
        if ($user->avatar && file_exists(public_path($user->avatar))) {
            return asset($user->avatar);
        }
        return 'https://ui-avatars.com/api/?background=1e3a5f&color=fff&name=' . urlencode($user->name);
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