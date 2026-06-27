<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskAttachment;
use App\Models\TaskActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class AttachmentController extends Controller
{
    // Upload attachment to task
    public function upload(Request $request, Task $task)
    {
        try {
            $validator = Validator::make($request->all(), [
                'file' => 'required|file|max:5120',
            ]);
            
            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()->first()], 422);
            }
            
            $file = $request->file('file');
            $originalName = $file->getClientOriginalName();
            $fileSize = $file->getSize();
            $mimeType = $file->getMimeType();
            $fileType = $this->getFileType($mimeType);
            
            $filename = time() . '_' . uniqid() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $originalName);
            $path = $file->storeAs('attachments/' . $task->id, $filename, 'public');
            
            $attachment = $task->attachments()->create([
                'file_name' => $originalName,
                'file_path' => Storage::url($path),
                'file_type' => $fileType,
                'file_size' => $fileSize,
                'mime_type' => $mimeType,
                'user_id' => auth()->id(),
                'is_cover' => false,
            ]);
            
            TaskActivity::create([
                'action' => 'uploaded_file',
                'user_id' => auth()->id(),
                'task_id' => $task->id,
                'new_value' => $originalName
            ]);
            
            // 🔔 NOTIFY WATCHERS
            $task->notifyWatchers('uploaded_file', auth()->id(), $task->id, null, $originalName);
            
            return response()->json([
                'success' => true,
                'attachment' => $attachment,
                'message' => 'File uploaded successfully'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Upload error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    // Download attachment
    public function download(TaskAttachment $attachment)
    {
        $filePath = str_replace('/storage/', '', $attachment->file_path);
        $fullPath = storage_path('app/public/' . $filePath);
        
        if (!file_exists($fullPath)) {
            abort(404, 'File not found');
        }
        
        return response()->download($fullPath, $attachment->file_name);
    }
    
    // Delete attachment
    public function destroy(TaskAttachment $attachment)
    {
        try {
            if ($attachment->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            
            $taskId = $attachment->task_id;
            $task = Task::find($taskId);
            $fileName = $attachment->file_name;
            
            $filePath = str_replace('/storage/', '', $attachment->file_path);
            $fullPath = storage_path('app/public/' . $filePath);
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }
            
            $attachment->delete();
            
            TaskActivity::create([
                'action' => 'deleted_file',
                'user_id' => auth()->id(),
                'task_id' => $taskId,
                'old_value' => $fileName
            ]);
            
            // 🔔 NOTIFY WATCHERS
            if ($task) {
                $task->notifyWatchers('deleted_file', auth()->id(), $taskId, $fileName, null);
            }
            
            return response()->json(['success' => true]);
            
        } catch (\Exception $e) {
            Log::error('Delete attachment error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    // Set attachment as cover
    public function setCover(TaskAttachment $attachment)
    {
        try {
            $taskId = $attachment->task_id;
            $task = Task::find($taskId);
            $fileName = $attachment->file_name;
            
            // Check permission
            $board = $task->taskList->board;
            if (!$board->hasAccess(auth()->id())) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            
            $permission = $this->getUserPermission($board);
            if ($permission !== 'owner' && $permission !== 'edit') {
                return response()->json(['error' => 'No edit permission'], 403);
            }
            
            // Remove cover from all other attachments of this task
            TaskAttachment::where('task_id', $taskId)
                ->where('is_cover', true)
                ->update(['is_cover' => false]);
            
            // Set this attachment as cover
            $attachment->update(['is_cover' => true]);
            
            TaskActivity::create([
                'action' => 'set_cover',
                'user_id' => auth()->id(),
                'task_id' => $taskId,
                'new_value' => $fileName
            ]);
            
            // 🔔 NOTIFY WATCHERS
            if ($task) {
                $task->notifyWatchers('set_cover', auth()->id(), $taskId, null, $fileName);
            }
            
            return response()->json(['success' => true]);
            
        } catch (\Exception $e) {
            Log::error('Set cover error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    // Remove cover from task
    public function removeCover(Task $task)
    {
        try {
            // Check permission
            $board = $task->taskList->board;
            if (!$board->hasAccess(auth()->id())) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            
            $permission = $this->getUserPermission($board);
            if ($permission !== 'owner' && $permission !== 'edit') {
                return response()->json(['error' => 'No edit permission'], 403);
            }
            
            // Remove cover
            TaskAttachment::where('task_id', $task->id)
                ->where('is_cover', true)
                ->update(['is_cover' => false]);
            
            // Log activity
            TaskActivity::create([
                'action' => 'removed_cover',
                'user_id' => auth()->id(),
                'task_id' => $task->id,
                'old_value' => 'Cover image',
                'new_value' => null
            ]);
            
            // 🔔 NOTIFY WATCHERS
            $task->notifyWatchers('removed_cover', auth()->id(), $task->id, 'Cover image', null);
            
            return response()->json(['success' => true]);
            
        } catch (\Exception $e) {
            Log::error('Remove cover error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    // Get file type based on mime type
    private function getFileType($mimeType)
    {
        if (str_starts_with($mimeType, 'image/')) {
            return 'image';
        } elseif ($mimeType === 'application/pdf') {
            return 'pdf';
        } elseif (str_contains($mimeType, 'word') || str_contains($mimeType, 'document')) {
            return 'document';
        } elseif (str_contains($mimeType, 'sheet') || str_contains($mimeType, 'excel')) {
            return 'spreadsheet';
        } elseif (str_contains($mimeType, 'zip') || str_contains($mimeType, 'rar')) {
            return 'archive';
        } elseif (str_contains($mimeType, 'text/')) {
            return 'text';
        } else {
            return 'other';
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