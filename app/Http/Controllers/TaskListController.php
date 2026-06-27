<?php

namespace App\Http\Controllers;

use App\Models\Board;
use App\Models\TaskList;
use Illuminate\Http\Request;

class TaskListController extends Controller
{
    public function store(Request $request, $boardId)
    {
        try {
            // Cari board berdasarkan ID
            $board = Board::findOrFail($boardId);
            
            // Validasi input
            $request->validate([
                'name' => 'required|string|max:255'
            ]);
            
            // Hitung order terakhir
            $maxOrder = $board->lists()->max('order') ?? -1;
            
            // Create list baru
            $list = $board->lists()->create([
                'name' => $request->name,
                'color' => $request->color ?? '#e2e8f0',
                'order' => $maxOrder + 1
            ]);
            
            // Kalau request dari AJAX, return JSON
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => $list,
                    'message' => 'List created successfully'
                ]);
            }
            
            // Kalau request biasa, redirect back
            return redirect()->back()->with('success', 'List created successfully');
            
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Failed to create list');
        }
    }
    
    public function update(Request $request, TaskList $taskList)
    {
        $taskList->update($request->only('name', 'color'));
        
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true]);
        }
        
        return redirect()->back();
    }
    
    public function destroy(TaskList $taskList)
    {
        $taskList->delete();
        
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json(['success' => true]);
        }
        
        return redirect()->back();
    }
    
    public function reorder(Request $request)
    {
        foreach ($request->lists as $index => $listId) {
            TaskList::where('id', $listId)->update(['order' => $index]);
        }
        
        return response()->json(['success' => true]);
    }
}