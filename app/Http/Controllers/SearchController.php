<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Board;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{
    public function index()
    {
        return view('search.index');
    }
    
    public function api(Request $request)
    {
        try {
            $query = $request->get('q', '');
            $filterBoard = $request->get('board_id');
            $filterAssignee = $request->get('assignee_id');
            $filterLabel = $request->get('label_id');
            $filterPriority = $request->get('priority');
            $filterList = $request->get('list_id');
            $filterStatus = $request->get('status', 'active'); // active, archived, both
            
            // Ambil semua board yang user punya akses
            $boardIds = Board::where('user_id', auth()->id())
                ->pluck('id')
                ->merge(
                    auth()->user()->sharedBoards()->pluck('boards.id')
                )->unique();
            
            // Query dasar
            $tasksQuery = Task::whereIn('task_list_id', function($sub) use ($boardIds) {
                $sub->select('id')
                    ->from('task_lists')
                    ->whereIn('board_id', $boardIds);
            });
            
            // Filter berdasarkan status archive
            if ($filterStatus === 'active') {
                $tasksQuery->active();
            } elseif ($filterStatus === 'archived') {
                $tasksQuery->archived();
            }
            // 'both' -> no filter
            
            // Search by title or description
            if (!empty($query)) {
                $tasksQuery->where(function($q) use ($query) {
                    $q->where('title', 'LIKE', "%{$query}%")
                      ->orWhere('description', 'LIKE', "%{$query}%");
                });
            }
            
            // Filter by board
            if (!empty($filterBoard)) {
                $tasksQuery->whereIn('task_list_id', function($sub) use ($filterBoard) {
                    $sub->select('id')
                        ->from('task_lists')
                        ->where('board_id', $filterBoard);
                });
            }
            
            // Filter by list
            if (!empty($filterList)) {
                $tasksQuery->where('task_list_id', $filterList);
            }
            
            // Filter by priority
            if (!empty($filterPriority)) {
                $tasksQuery->where('priority', $filterPriority);
            }
            
            // Filter by assignee
            if (!empty($filterAssignee)) {
                $tasksQuery->whereHas('assignees', function($q) use ($filterAssignee) {
                    $q->where('user_id', $filterAssignee);
                });
            }
            
            // Filter by label
            if (!empty($filterLabel)) {
                $tasksQuery->whereHas('labels', function($q) use ($filterLabel) {
                    $q->where('label_id', $filterLabel);
                });
            }
            
            $tasks = $tasksQuery
                ->with(['taskList.board', 'assignees', 'labels'])
                ->orderBy('updated_at', 'desc')
                ->paginate(20);
            
            // Get data untuk filter dropdown
            $boards = Board::whereIn('id', $boardIds)
                ->with('lists')
                ->get();
            
            $users = \App\Models\User::orderBy('name')->get();
            
            $labels = \App\Models\Label::whereIn('board_id', $boardIds)
                ->orderBy('name')
                ->get();
            
            return response()->json([
                'success' => true,
                'tasks' => $tasks,
                'boards' => $boards,
                'users' => $users,
                'labels' => $labels,
                'query' => $query,
                'filters' => [
                    'board_id' => $filterBoard,
                    'assignee_id' => $filterAssignee,
                    'label_id' => $filterLabel,
                    'priority' => $filterPriority,
                    'list_id' => $filterList,
                    'status' => $filterStatus,
                ]
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Search API error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}