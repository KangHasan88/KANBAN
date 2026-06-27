<!-- Modal Add List -->
<div id="addListModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <h3 class="text-lg font-medium mb-4">Add New List</h3>
        <form id="addListForm" onsubmit="submitAddList(event)">
            @csrf
            <input type="text" id="listName" name="name" placeholder="List Name" required class="w-full border rounded px-3 py-2 mb-3" autocomplete="off">
            
            <!-- Live Preview Header -->
            <label class="block text-sm font-medium text-gray-700 mb-2">Preview</label>
            <div id="addColorPreview" class="rounded-lg p-3 mb-3 text-center font-semibold transition-all" style="background-color: #e2e8f0; color: #1f2937;">
                List Header Preview
            </div>
            
            <!-- Color Picker dengan Preset Warna -->
            <label class="block text-sm font-medium text-gray-700 mb-2">List Color</label>
            <div class="grid grid-cols-8 gap-2 mb-3">
                <button type="button" onclick="setAddListColor('#ef4444')" class="w-8 h-8 rounded-full border-2 border-transparent hover:border-gray-400 transition shadow-sm" style="background-color: #ef4444;" title="Red - Bug, Urgent, Blocked"></button>
                <button type="button" onclick="setAddListColor('#f97316')" class="w-8 h-8 rounded-full border-2 border-transparent hover:border-gray-400 transition shadow-sm" style="background-color: #f97316;" title="Orange - Testing, Warning"></button>
                <button type="button" onclick="setAddListColor('#eab308')" class="w-8 h-8 rounded-full border-2 border-transparent hover:border-gray-400 transition shadow-sm" style="background-color: #eab308;" title="Yellow - Review, Pending"></button>
                <button type="button" onclick="setAddListColor('#22c55e')" class="w-8 h-8 rounded-full border-2 border-transparent hover:border-gray-400 transition shadow-sm" style="background-color: #22c55e;" title="Green - Done, Success"></button>
                <button type="button" onclick="setAddListColor('#3b82f6')" class="w-8 h-8 rounded-full border-2 border-transparent hover:border-gray-400 transition shadow-sm" style="background-color: #3b82f6;" title="Blue - To Do, Task, Normal"></button>
                <button type="button" onclick="setAddListColor('#a855f7')" class="w-8 h-8 rounded-full border-2 border-transparent hover:border-gray-400 transition shadow-sm" style="background-color: #a855f7;" title="Purple - Design, Creative"></button>
                <button type="button" onclick="setAddListColor('#6b7280')" class="w-8 h-8 rounded-full border-2 border-transparent hover:border-gray-400 transition shadow-sm" style="background-color: #6b7280;" title="Gray - Documentation, Backlog"></button>
                <button type="button" onclick="setAddListColor('#1e3a5f')" class="w-8 h-8 rounded-full border-2 border-transparent hover:border-gray-400 transition shadow-sm" style="background-color: #1e3a5f;" title="Navy - Default"></button>
            </div>
            <div class="flex items-center gap-3 mb-3">
                <input type="color" id="listColor" name="color" value="#e2e8f0" class="w-full border rounded px-3 py-2">
                <span class="text-xs text-gray-500">Custom color</span>
            </div>
            <p class="text-xs text-gray-500 mb-3">💡 Click on color preset above or use custom color picker</p>
            
            <div class="flex justify-end">
                <button type="button" onclick="closeAddListModal()" class="mr-2 px-4 py-2 bg-gray-300 rounded hover:bg-gray-400 transition">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition">Add List</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Add Task -->
<div id="addTaskModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <h3 class="text-lg font-medium mb-4">Add New Task</h3>
        <form id="addTaskForm" onsubmit="submitAddTask(event)">
            @csrf
            <input type="hidden" id="taskListId" name="list_id">
            <input type="text" id="taskTitle" name="title" placeholder="Task Title" required class="w-full border rounded px-3 py-2 mb-3">
            <textarea id="taskDescription" name="description" placeholder="Description" rows="3" class="w-full border rounded px-3 py-2 mb-3"></textarea>
            <select id="taskPriority" name="priority" class="w-full border rounded px-3 py-2 mb-3">
                <option value="low">🟢 Low Priority</option>
                <option value="medium" selected>🟡 Medium Priority</option>
                <option value="high">🔴 High Priority</option>
            </select>
            <input type="date" id="taskDueDate" name="due_date" class="w-full border rounded px-3 py-2 mb-3">
            <div class="flex justify-end">
                <button type="button" onclick="closeAddTaskModal()" class="mr-2 px-4 py-2 bg-gray-300 rounded hover:bg-gray-400 transition">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition">Add Task</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Task - WITH MULTIPLE ASSIGNEE -->
<div id="editTaskModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <h3 class="text-lg font-medium mb-4">Edit Task</h3>
        <form id="editTaskForm" onsubmit="submitEditTask(event)">
            @csrf
            @method('PUT')
            <input type="hidden" id="editTaskId" name="task_id">
            <input type="text" id="editTaskTitle" name="title" required class="w-full border rounded px-3 py-2 mb-3">
            <textarea id="editTaskDescription" name="description" rows="3" class="w-full border rounded px-3 py-2 mb-3"></textarea>
            <select id="editTaskPriority" name="priority" class="w-full border rounded px-3 py-2 mb-3">
                <option value="low">🟢 Low Priority</option>
                <option value="medium">🟡 Medium Priority</option>
                <option value="high">🔴 High Priority</option>
            </select>
            <input type="date" id="editTaskDueDate" name="due_date" class="w-full border rounded px-3 py-2 mb-3">
            
            <!-- Multiple Assignees Select -->
            <div class="mb-3">
                <label class="block text-sm font-medium text-gray-700 mb-1">👥 Assign To (Multiple)</label>
                <select id="editTaskAssignee" name="assignees[]" multiple 
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 min-h-[100px]">
                    <!-- Options will be filled by JavaScript -->
                </select>
                <p class="text-xs text-gray-500 mt-1">💡 Hold Ctrl (Windows) or Cmd (Mac) to select multiple users</p>
            </div>
            
            <div class="flex justify-end">
                <button type="button" onclick="closeEditTaskModal()" class="mr-2 px-4 py-2 bg-gray-300 rounded hover:bg-gray-400 transition">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition">Update Task</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit List -->
<div id="editListModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <h3 class="text-lg font-medium mb-4">Edit List</h3>
        <form id="editListForm" onsubmit="submitEditList(event)">
            @csrf
            @method('PUT')
            <input type="hidden" id="editListId">
            <input type="text" id="editListName" name="name" required class="w-full border rounded px-3 py-2 mb-3">
            
            <!-- Live Preview Header -->
            <label class="block text-sm font-medium text-gray-700 mb-2">Preview</label>
            <div id="colorPreview" class="rounded-lg p-3 mb-3 text-center font-semibold transition-all" style="background-color: #e2e8f0; color: #1f2937;">
                List Header Preview
            </div>
            
            <!-- Color Picker dengan Preset Warna -->
            <label class="block text-sm font-medium text-gray-700 mb-2">List Color</label>
            <div class="grid grid-cols-8 gap-2 mb-3">
                <button type="button" onclick="setEditListColor('#ef4444')" class="w-8 h-8 rounded-full border-2 border-transparent hover:border-gray-400 transition shadow-sm" style="background-color: #ef4444;" title="Red - Bug, Urgent, Blocked"></button>
                <button type="button" onclick="setEditListColor('#f97316')" class="w-8 h-8 rounded-full border-2 border-transparent hover:border-gray-400 transition shadow-sm" style="background-color: #f97316;" title="Orange - Testing, Warning"></button>
                <button type="button" onclick="setEditListColor('#eab308')" class="w-8 h-8 rounded-full border-2 border-transparent hover:border-gray-400 transition shadow-sm" style="background-color: #eab308;" title="Yellow - Review, Pending"></button>
                <button type="button" onclick="setEditListColor('#22c55e')" class="w-8 h-8 rounded-full border-2 border-transparent hover:border-gray-400 transition shadow-sm" style="background-color: #22c55e;" title="Green - Done, Success"></button>
                <button type="button" onclick="setEditListColor('#3b82f6')" class="w-8 h-8 rounded-full border-2 border-transparent hover:border-gray-400 transition shadow-sm" style="background-color: #3b82f6;" title="Blue - To Do, Task, Normal"></button>
                <button type="button" onclick="setEditListColor('#a855f7')" class="w-8 h-8 rounded-full border-2 border-transparent hover:border-gray-400 transition shadow-sm" style="background-color: #a855f7;" title="Purple - Design, Creative"></button>
                <button type="button" onclick="setEditListColor('#6b7280')" class="w-8 h-8 rounded-full border-2 border-transparent hover:border-gray-400 transition shadow-sm" style="background-color: #6b7280;" title="Gray - Documentation, Backlog"></button>
                <button type="button" onclick="setEditListColor('#1e3a5f')" class="w-8 h-8 rounded-full border-2 border-transparent hover:border-gray-400 transition shadow-sm" style="background-color: #1e3a5f;" title="Navy - Default"></button>
            </div>
            <div class="flex items-center gap-3 mb-3">
                <input type="color" id="editListColor" name="color" class="w-full border rounded px-3 py-2">
                <span class="text-xs text-gray-500">Custom color</span>
            </div>
            <p class="text-xs text-gray-500 mb-3">💡 Click on color preset above or use custom color picker</p>
            
            <div class="flex justify-end">
                <button type="button" onclick="closeEditListModal()" class="mr-2 px-4 py-2 bg-gray-300 rounded hover:bg-gray-400 transition">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition">Update List</button>
            </div>
        </form>
    </div>
</div>