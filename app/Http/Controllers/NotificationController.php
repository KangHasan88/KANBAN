<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    // Get all notifications for current user
    public function index(Request $request)
    {
        try {
            $notifications = Notification::where('user_id', auth()->id())
                ->with('fromUser', 'task')
                ->orderBy('created_at', 'desc')
                ->paginate(20);
            
            $unreadCount = Notification::where('user_id', auth()->id())
                ->where('is_read', false)
                ->count();
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'notifications' => $notifications,
                    'unread_count' => $unreadCount
                ]);
            }
            
            return view('notifications.index', compact('notifications', 'unreadCount'));
            
        } catch (\Exception $e) {
            \Log::error('Notification index error: ' . $e->getMessage());
            if ($request->ajax()) {
                return response()->json(['error' => $e->getMessage()], 500);
            }
            return back()->with('error', 'Failed to load notifications');
        }
    }
    
    // Mark single notification as read
    public function markAsRead(Notification $notification)
    {
        try {
            if ($notification->user_id !== auth()->id()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            
            $notification->markAsRead();
            
            return response()->json(['success' => true]);
            
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    // Mark all notifications as read
    public function markAllAsRead()
    {
        try {
            Notification::where('user_id', auth()->id())
                ->where('is_read', false)
                ->update(['is_read' => true]);
            
            return response()->json(['success' => true]);
            
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    // Get unread count
    public function unreadCount()
    {
        try {
            $count = Notification::where('user_id', auth()->id())
                ->where('is_read', false)
                ->count();
            
            return response()->json(['count' => $count]);
            
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    // Delete notification
    public function destroy(Notification $notification)
    {
        try {
            if ($notification->user_id !== auth()->id()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            
            $notification->delete();
            
            return response()->json(['success' => true]);
            
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}