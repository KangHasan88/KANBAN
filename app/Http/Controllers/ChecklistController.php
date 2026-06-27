<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Checklist;
use App\Models\ChecklistItem;
use App\Models\TaskActivity;
use Illuminate\Http\Request;

class ChecklistController extends Controller
{
    // Create new checklist
    public function store(Request $request, Task $task)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);
        
        $maxOrder = $task->checklists()->max('order') ?? -1;
        
        $checklist = $task->checklists()->create([
            'name' => $request->name,
            'order' => $maxOrder + 1
        ]);
        
        TaskActivity::create([
            'action' => 'added_checklist',
            'user_id' => auth()->id(),
            'task_id' => $task->id,
            'old_value' => null,
            'new_value' => $checklist->name
        ]);
        
        // 🔔 NOTIFY WATCHERS
        $task->notifyWatchers('added_checklist', auth()->id(), $task->id, null, $checklist->name);
        
        return response()->json(['success' => true, 'checklist' => $checklist]);
    }
    
    // Update checklist name
    public function update(Request $request, Checklist $checklist)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);
        
        $oldName = $checklist->name;
        $task = $checklist->task;
        $newName = $request->name;
        
        $checklist->update(['name' => $newName]);
        
        TaskActivity::create([
            'action' => 'updated_checklist',
            'user_id' => auth()->id(),
            'task_id' => $task->id,
            'old_value' => $oldName,
            'new_value' => $newName
        ]);
        
        // 🔔 NOTIFY WATCHERS
        $task->notifyWatchers('updated_checklist', auth()->id(), $task->id, $oldName, $newName);
        
        return response()->json(['success' => true]);
    }
    
    // Delete checklist
    public function destroy(Checklist $checklist)
    {
        $taskId = $checklist->task_id;
        $task = $checklist->task;
        $checklistName = $checklist->name;
        
        $checklist->delete();
        
        TaskActivity::create([
            'action' => 'deleted_checklist',
            'user_id' => auth()->id(),
            'task_id' => $taskId,
            'old_value' => $checklistName,
            'new_value' => null
        ]);
        
        // 🔔 NOTIFY WATCHERS
        $task->notifyWatchers('deleted_checklist', auth()->id(), $taskId, $checklistName, null);
        
        return response()->json(['success' => true]);
    }
    
    // Add item to checklist - 🔔 DENGAN NOTIFIKASI
    public function addItem(Request $request, Checklist $checklist)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);
        
        $maxOrder = $checklist->items()->max('order') ?? -1;
        
        $item = $checklist->items()->create([
            'name' => $request->name,
            'order' => $maxOrder + 1
        ]);
        
        // 🔔 NOTIFY WATCHERS
        $task = $checklist->task;
        $task->notifyWatchers('added_checklist_item', auth()->id(), $task->id, null, $item->name);
        
        return response()->json(['success' => true, 'item' => $item]);
    }
    
    // Toggle checklist item (check/uncheck) - 🔔 DENGAN NOTIFIKASI
    public function toggleItem(ChecklistItem $item)
    {
        $item->update(['is_checked' => !$item->is_checked]);
        
        $task = $item->checklist->task;
        
        TaskActivity::create([
            'action' => $item->is_checked ? 'checked_item' : 'unchecked_item',
            'user_id' => auth()->id(),
            'task_id' => $task->id,
            'old_value' => $item->name,
            'new_value' => $item->is_checked ? 'completed' : 'pending'
        ]);
        
        // 🔔 NOTIFY WATCHERS
        $action = $item->is_checked ? 'checked_item' : 'unchecked_item';
        $task->notifyWatchers($action, auth()->id(), $task->id, $item->name, $item->is_checked ? 'completed' : 'pending');
        
        return response()->json([
            'success' => true, 
            'is_checked' => $item->is_checked,
            'progress' => $this->getChecklistProgress($item->checklist_id)
        ]);
    }
    
    // Update checklist item name
    public function updateItem(Request $request, ChecklistItem $item)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);
        
        $oldName = $item->name;
        $task = $item->checklist->task;
        $newName = $request->name;
        
        $item->update(['name' => $newName]);
        
        // 🔔 NOTIFY WATCHERS
        $task->notifyWatchers('updated_checklist_item', auth()->id(), $task->id, $oldName, $newName);
        
        return response()->json(['success' => true]);
    }
    
    // Delete checklist item - 🔔 DENGAN NOTIFIKASI
    public function deleteItem(ChecklistItem $item)
    {
        $task = $item->checklist->task;
        $itemName = $item->name;
        
        $item->delete();
        
        // 🔔 NOTIFY WATCHERS
        $task->notifyWatchers('deleted_checklist_item', auth()->id(), $task->id, $itemName, null);
        
        return response()->json(['success' => true]);
    }
    
    // Get checklist progress
    private function getChecklistProgress($checklistId)
    {
        $checklist = Checklist::find($checklistId);
        if (!$checklist) return 0;
        
        $total = $checklist->items()->count();
        if ($total === 0) return 0;
        
        $completed = $checklist->items()->where('is_checked', true)->count();
        return round(($completed / $total) * 100);
    }
}