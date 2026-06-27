<?php

namespace App\Http\Controllers;

use App\Models\Board;
use App\Models\User;
use Illuminate\Http\Request;

class BoardController extends Controller
{
    public function index()
    {
        $ownedBoards = Board::where('user_id', auth()->id())->get();
        $sharedBoards = auth()->user()->sharedBoards;
        
        return view('boards.index', compact('ownedBoards', 'sharedBoards'));
    }
    
    public function show(Board $board)
    {
        if (!$board->hasAccess(auth()->id())) {
            abort(403, 'Anda tidak memiliki akses ke board ini');
        }
        
        // FIX: Gunakan assignees (bukan assignee)
        $board->load([
            'lists.tasks.assignees',
            'lists.tasks.labels',
            'lists.tasks.attachments',
            'lists.tasks.checklists.items',
            'sharedUsers',
            'owner'
        ]);
        
        $permission = $this->getUserPermission($board);
        
        // ==============================================
        // DATA UNTUK MODAL EXPORT
        // ==============================================
        $lists = $board->lists;
        $sharedUsers = $board->sharedUsers;
        $labels = $board->labels;
        
        return view('boards.show', compact('board', 'permission', 'lists', 'sharedUsers', 'labels'));
    }
    
    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);
        
        $board = Board::create([
            'name' => $request->name,
            'description' => $request->description,
            'user_id' => auth()->id()
        ]);
        
        return redirect()->route('boards.show', $board);
    }
    
    public function update(Request $request, Board $board)
    {
        $this->authorizeAccess($board, 'edit');
        
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000'
        ]);
        
        $board->update($request->only('name', 'description'));
        
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'board' => $board]);
        }
        
        return redirect()->back()->with('success', 'Board updated successfully!');
    }
    
    public function destroy(Board $board)
    {
        $this->authorizeAccess($board, 'edit');
        $board->delete();
        return redirect()->route('home');
    }
    
    public function share(Request $request, Board $board)
    {
        if ($board->user_id !== auth()->id()) {
            return response()->json(['error' => 'Only board owner can share'], 403);
        }
        
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'permission' => 'required|in:view,edit'
        ]);
        
        if ($board->sharedUsers()->where('user_id', $request->user_id)->exists()) {
            return response()->json(['error' => 'User already has access'], 422);
        }
        
        $board->sharedUsers()->attach($request->user_id, [
            'permission' => $request->permission
        ]);
        
        return response()->json(['success' => true, 'message' => 'Board shared successfully']);
    }
    
    public function unshare(Board $board, User $user)
    {
        if ($board->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $board->sharedUsers()->detach($user->id);
        return response()->json(['success' => true]);
    }
    
    public function updatePermission(Request $request, Board $board, User $user)
    {
        if ($board->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $request->validate(['permission' => 'required|in:view,edit']);
        
        $board->sharedUsers()->updateExistingPivot($user->id, [
            'permission' => $request->permission
        ]);
        
        return response()->json(['success' => true]);
    }
    
    // ==============================================
    // AUTO ARCHIVE SETTINGS
    // ==============================================
    
    public function updateAutoArchiveSettings(Request $request, Board $board)
    {
        // Hanya owner yang bisa ubah setting
        if ($board->user_id !== auth()->id()) {
            return response()->json(['error' => 'Only board owner can change auto archive settings'], 403);
        }
        
        $request->validate([
            'auto_archive_enabled' => 'required|boolean',
            'auto_archive_days' => 'required|integer|min:1|max:90',
            'auto_archive_list_name' => 'nullable|string|max:255'
        ]);
        
        $board->update([
            'auto_archive_enabled' => $request->auto_archive_enabled,
            'auto_archive_days' => $request->auto_archive_days,
            'auto_archive_list_name' => $request->auto_archive_list_name ?? 'Done'
        ]);
        
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Auto archive settings updated',
                'settings' => [
                    'enabled' => $board->auto_archive_enabled,
                    'days' => $board->auto_archive_days,
                    'list_name' => $board->auto_archive_list_name
                ]
            ]);
        }
        
        return redirect()->back()->with('success', 'Auto archive settings updated');
    }
    
    // ==============================================
    // COVER SETTINGS
    // ==============================================
    
    public function updateCoverSettings(Request $request, Board $board)
    {
        // Log untuk debugging
        \Log::info('updateCoverSettings called', [
            'board_id' => $board->id,
            'user_id' => auth()->id(),
            'is_owner' => $board->user_id === auth()->id(),
            'cover_enabled' => $request->cover_enabled
        ]);
        
        // Hanya owner yang bisa ubah setting
        if ($board->user_id !== auth()->id()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['error' => 'Only board owner can change cover settings'], 403);
            }
            return redirect()->back()->with('error', 'Only board owner can change cover settings');
        }
        
        $request->validate([
            'cover_enabled' => 'required|boolean'
        ]);
        
        $board->update([
            'cover_enabled' => $request->cover_enabled
        ]);
        
        \Log::info('Cover settings updated', [
            'board_id' => $board->id,
            'new_cover_enabled' => $board->cover_enabled
        ]);
        
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Cover settings updated successfully',
                'cover_enabled' => $board->cover_enabled
            ]);
        }
        
        return redirect()->back()->with('success', 'Cover settings updated');
    }
    
    private function authorizeAccess($board, $requiredPermission = 'view')
    {
        if ($board->user_id === auth()->id()) {
            return true;
        }
        
        $sharedUser = $board->sharedUsers()->where('user_id', auth()->id())->first();
        
        if (!$sharedUser) {
            abort(403, 'No access to this board');
        }
        
        if ($requiredPermission === 'edit' && $sharedUser->pivot->permission !== 'edit') {
            abort(403, 'You only have view permission');
        }
        
        return true;
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