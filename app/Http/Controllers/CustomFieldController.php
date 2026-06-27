<?php

namespace App\Http\Controllers;

use App\Models\Board;
use App\Models\CustomField;
use App\Models\Task;
use App\Models\TaskCustomFieldValue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CustomFieldController extends Controller
{
    public function index(Board $board)
    {
        if (!$board->hasAccess(auth()->id())) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $fields = $board->customFields()->get();
        
        return response()->json([
            'success' => true,
            'fields' => $fields
        ]);
    }
    
    public function store(Request $request, Board $board)
    {
        if ($board->user_id !== auth()->id()) {
            return response()->json(['error' => 'Only board owner can add custom fields'], 403);
        }
        
        $request->validate([
            'name' => 'required|string|max:255|unique:custom_fields,name,NULL,id,board_id,' . $board->id,
            'label' => 'required|string|max:255',
            'type' => 'required|in:text,number,date,dropdown,checkbox,textarea',
            'options' => 'nullable|array',
            'required' => 'boolean'
        ]);
        
        $maxOrder = $board->customFields()->max('order') ?? -1;
        
        $field = $board->customFields()->create([
            'name' => $request->name,
            'label' => $request->label,
            'type' => $request->type,
            'options' => $request->type === 'dropdown' ? $request->options : null,
            'required' => $request->required ?? false,
            'order' => $maxOrder + 1,
            'user_id' => auth()->id()
        ]);
        
        return response()->json([
            'success' => true,
            'field' => $field,
            'message' => 'Custom field created successfully'
        ]);
    }
    
    public function update(Request $request, CustomField $customField)
    {
        $board = $customField->board;
        
        if ($board->user_id !== auth()->id()) {
            return response()->json(['error' => 'Only board owner can edit custom fields'], 403);
        }
        
        $request->validate([
            'name' => 'required|string|max:255|unique:custom_fields,name,' . $customField->id . ',id,board_id,' . $board->id,
            'label' => 'required|string|max:255',
            'type' => 'required|in:text,number,date,dropdown,checkbox,textarea',
            'options' => 'nullable|array',
            'required' => 'boolean'
        ]);
        
        $customField->update([
            'name' => $request->name,
            'label' => $request->label,
            'type' => $request->type,
            'options' => $request->type === 'dropdown' ? $request->options : null,
            'required' => $request->required ?? false
        ]);
        
        return response()->json([
            'success' => true,
            'field' => $customField,
            'message' => 'Custom field updated successfully'
        ]);
    }
    
    public function destroy(CustomField $customField)
    {
        $board = $customField->board;
        
        if ($board->user_id !== auth()->id()) {
            return response()->json(['error' => 'Only board owner can delete custom fields'], 403);
        }
        
        $customField->taskValues()->delete();
        $customField->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Custom field deleted successfully'
        ]);
    }
    
    public function reorder(Request $request, Board $board)
    {
        if ($board->user_id !== auth()->id()) {
            return response()->json(['error' => 'Only board owner can reorder custom fields'], 403);
        }
        
        $request->validate([
            'fields' => 'required|array',
            'fields.*.id' => 'required|exists:custom_fields,id',
            'fields.*.order' => 'required|integer'
        ]);
        
        foreach ($request->fields as $fieldData) {
            CustomField::where('id', $fieldData['id'])
                ->where('board_id', $board->id)
                ->update(['order' => $fieldData['order']]);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Fields reordered successfully'
        ]);
    }
    
    public function getTaskValues(Task $task)
    {
        $board = $task->taskList->board;
        
        if (!$board->hasAccess(auth()->id())) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $values = [];
        foreach ($board->customFields as $field) {
            $values[] = [
                'field' => $field,
                'value' => $task->getCustomFieldValue($field->id)
            ];
        }
        
        return response()->json([
            'success' => true,
            'values' => $values
        ]);
    }
    
    public function saveTaskValues(Request $request, Task $task)
    {
        $board = $task->taskList->board;
        
        if (!$board->hasAccess(auth()->id())) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $permission = $this->getUserPermission($board);
        if ($permission !== 'owner' && $permission !== 'edit') {
            return response()->json(['error' => 'No edit permission'], 403);
        }
        
        $request->validate([
            'values' => 'required|array',
            'values.*' => 'nullable|string'
        ]);
        
        foreach ($request->values as $fieldId => $value) {
            $field = CustomField::where('id', $fieldId)
                ->where('board_id', $board->id)
                ->first();
                
            if (!$field) continue;
            
            if ($field->type === 'checkbox') {
                $value = $value ? 1 : 0;
            }
            
            TaskCustomFieldValue::updateOrCreate(
                [
                    'task_id' => $task->id,
                    'custom_field_id' => $fieldId
                ],
                ['value' => $value]
            );
        }
        
        $task->notifyWatchers('updated_custom_fields', auth()->id(), $task->id, null, 'Custom fields updated');
        
        return response()->json([
            'success' => true,
            'message' => 'Custom fields saved successfully'
        ]);
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