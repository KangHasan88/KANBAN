<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskAttachment;
use App\Models\TaskActivity;
use App\Services\UnsplashService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UnsplashController extends Controller
{
    protected $unsplashService;
    
    public function __construct(UnsplashService $unsplashService)
    {
        $this->unsplashService = $unsplashService;
    }
    
    // Search photos
    public function search(Request $request)
    {
        try {
            $query = $request->get('q', '');
            $page = $request->get('page', 1);
            
            if (strlen($query) < 2) {
                $result = $this->unsplashService->getRandomPhotos(20, 'nature');
            } else {
                $result = $this->unsplashService->searchPhotos($query, $page, 24);
            }
            
            return response()->json($result);
            
        } catch (\Exception $e) {
            Log::error('Unsplash search error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    // Apply cover from Unsplash to task
    public function applyCover(Request $request, Task $task)
    {
        try {
            $request->validate([
                'image_url' => 'required|url',
                'photo_id' => 'required|string',
                'author_name' => 'nullable|string',
                'author_link' => 'nullable|url'
            ]);
            
            // Check permission
            $board = $task->taskList->board;
            if (!$board->hasAccess(auth()->id())) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            
            $permission = $this->getUserPermission($board);
            if ($permission !== 'owner' && $permission !== 'edit') {
                return response()->json(['error' => 'No edit permission'], 403);
            }
            
            // Download image
            $result = $this->unsplashService->downloadPhoto(
                $request->image_url,
                $task->id,
                $request->photo_id,
                $request->author_name,
                $request->author_link
            );
            
            if (!$result['success']) {
                return response()->json(['error' => $result['message']], 500);
            }
            
            // Remove existing cover
            TaskAttachment::where('task_id', $task->id)
                ->where('is_cover', true)
                ->update(['is_cover' => false]);
            
            // Create attachment
            $attachment = TaskAttachment::create([
                'file_name' => $result['file_name'],
                'file_path' => $result['file_path'],
                'file_type' => 'image',
                'is_cover' => true,
                'file_size' => 0,
                'mime_type' => 'image/jpeg',
                'task_id' => $task->id,
                'user_id' => auth()->id()
            ]);
            
            // Log activity
            TaskActivity::create([
                'action' => 'set_cover_from_unsplash',
                'user_id' => auth()->id(),
                'task_id' => $task->id,
                'new_value' => "Set cover from Unsplash by {$request->author_name}"
            ]);
            
            // 🔔 NOTIFY WATCHERS
            $task->notifyWatchers('set_cover', auth()->id(), $task->id, null, $result['file_name']);
            
            return response()->json([
                'success' => true,
                'attachment' => $attachment,
                'message' => 'Cover image applied from Unsplash'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Apply cover error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
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