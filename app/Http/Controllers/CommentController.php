<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Comment;
use App\Models\CommentLike;
use App\Models\TaskActivity;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    // Store a new comment
    public function store(Request $request, Task $task)
    {
        try {
            $request->validate([
                'content' => 'required|string|max:1000'
            ]);
            
            // Extract mentions (@username)
            preg_match_all('/@([a-zA-Z0-9_]+)/', $request->content, $matches);
            $mentionedUsernames = $matches[1];
            $mentionedUserIds = User::whereIn('username', $mentionedUsernames)->pluck('id')->toArray();
            
            $comment = $task->comments()->create([
                'content' => $request->content,
                'user_id' => auth()->id(),
                'parent_id' => $request->parent_id ?? null,
                'mentions' => $mentionedUserIds
            ]);
            
            // Log activity
            TaskActivity::create([
                'action' => 'commented',
                'user_id' => auth()->id(),
                'task_id' => $task->id,
                'new_value' => 'Added a comment'
            ]);
            
            // 🔔 NOTIFY WATCHERS ABOUT NEW COMMENT
            $task->notifyWatchers('commented', auth()->id(), $task->id);
            
            // ==============================================
            // SEND NOTIFICATIONS
            // ==============================================
            
            $board = $task->taskList->board;
            $currentUser = auth()->user();
            
            // 1. Send notification to mentioned users
            foreach ($mentionedUserIds as $userId) {
                if ($userId == $currentUser->id) continue;
                
                Notification::create([
                    'type' => 'mention',
                    'title' => 'You were mentioned',
                    'message' => $currentUser->name . ' mentioned you in task "' . $task->title . '"',
                    'user_id' => $userId,
                    'from_user_id' => $currentUser->id,
                    'task_id' => $task->id,
                    'board_id' => $board->id,
                    'is_read' => false,
                    'data' => [
                        'comment_id' => $comment->id,
                        'comment_content' => $request->content
                    ]
                ]);
            }
            
            // 2. Send notification to task assignees (if not self and not already mentioned)
            foreach ($task->assignees as $assignee) {
                if ($assignee->id != $currentUser->id && !in_array($assignee->id, $mentionedUserIds)) {
                    Notification::create([
                        'type' => 'comment',
                        'title' => 'New comment on your task',
                        'message' => $currentUser->name . ' commented on task "' . $task->title . '"',
                        'user_id' => $assignee->id,
                        'from_user_id' => $currentUser->id,
                        'task_id' => $task->id,
                        'board_id' => $board->id,
                        'is_read' => false,
                        'data' => [
                            'comment_id' => $comment->id,
                            'comment_content' => $request->content
                        ]
                    ]);
                }
            }
            
            // 3. Send notification to board owner (if not self and not already notified)
            if ($board->user_id != $currentUser->id && 
                !$task->assignees->contains($board->user_id) && 
                !in_array($board->user_id, $mentionedUserIds)) {
                Notification::create([
                    'type' => 'comment',
                    'title' => 'New comment on board',
                    'message' => $currentUser->name . ' commented on task "' . $task->title . '"',
                    'user_id' => $board->user_id,
                    'from_user_id' => $currentUser->id,
                    'task_id' => $task->id,
                    'board_id' => $board->id,
                    'is_read' => false,
                    'data' => [
                        'comment_id' => $comment->id
                    ]
                ]);
            }
            
            return response()->json([
                'success' => true,
                'comment' => $comment->load('user')
            ]);
            
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
    
    // Update comment
    public function update(Request $request, Comment $comment)
    {
        try {
            // Only comment owner or admin can edit
            if (auth()->id() !== $comment->user_id && !auth()->user()->isAdmin()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            
            $request->validate([
                'content' => 'required|string|max:1000'
            ]);
            
            // Extract new mentions
            preg_match_all('/@([a-zA-Z0-9_]+)/', $request->content, $matches);
            $mentionedUsernames = $matches[1];
            $mentionedUserIds = User::whereIn('username', $mentionedUsernames)->pluck('id')->toArray();
            
            $comment->update([
                'content' => $request->content,
                'mentions' => $mentionedUserIds
            ]);
            
            return response()->json([
                'success' => true,
                'comment' => $comment->load('user')
            ]);
            
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
    
    // Delete comment
    public function destroy(Comment $comment)
    {
        try {
            // Only comment owner or admin can delete
            if (auth()->id() !== $comment->user_id && !auth()->user()->isAdmin()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            
            $taskId = $comment->task_id;
            $commentContent = $comment->content;
            
            // Delete all replies first
            $comment->replies()->delete();
            $comment->delete();
            
            // Log activity
            TaskActivity::create([
                'action' => 'deleted_comment',
                'user_id' => auth()->id(),
                'task_id' => $taskId,
                'old_value' => $commentContent
            ]);
            
            // 🔔 Notify watchers about deleted comment
            $task = Task::find($taskId);
            if ($task) {
                $task->notifyWatchers('deleted_comment', auth()->id(), $taskId, $commentContent, null);
            }
            
            return response()->json(['success' => true]);
            
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
    
    // Like a comment
    public function like(Comment $comment)
    {
        try {
            $existingLike = CommentLike::where('user_id', auth()->id())
                ->where('comment_id', $comment->id)
                ->first();
            
            if ($existingLike) {
                $existingLike->delete();
                $liked = false;
            } else {
                CommentLike::create([
                    'user_id' => auth()->id(),
                    'comment_id' => $comment->id
                ]);
                $liked = true;
            }
            
            // Update likes count
            $comment->update(['likes_count' => $comment->likes()->count()]);
            
            return response()->json([
                'success' => true,
                'liked' => $liked,
                'likes_count' => $comment->likes_count
            ]);
            
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
    
    // Get comments for a task
    public function index(Task $task)
    {
        try {
            $comments = $task->comments()->with(['user', 'replies.user', 'replies.likes'])->get();
            return response()->json($comments);
            
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}