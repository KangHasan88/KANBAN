<!-- Export Modal -->
<div id="exportModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-10 mx-auto p-6 border-0 w-full max-w-2xl shadow-xl rounded-xl bg-white">
        <div class="flex justify-between items-center mb-5 pb-3 border-b border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-green-600 to-green-500 flex items-center justify-center shadow-md">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-800">Export Data</h3>
                    <p class="text-sm text-gray-500">Export tasks to CSV, Excel, or PDF</p>
                </div>
            </div>
            <button onclick="closeExportModal()" class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 p-2 rounded-full transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <form id="exportForm" onsubmit="submitExport(event)">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Format</label>
                    <select id="exportFormat" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500">
                        <option value="csv">📄 CSV (Comma Separated Values)</option>
                        <option value="excel">📊 Excel (XLSX)</option>
                        <option value="pdf">📑 PDF (Portable Document Format)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">List / Status</label>
                    <select id="exportList" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500">
                        <option value="all">All Lists</option>
                        @foreach($lists as $list)
                        <option value="{{ $list->id }}">{{ $list->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Priority</label>
                    <select id="exportPriority" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500">
                        <option value="all">All Priorities</option>
                        <option value="high">🔴 High</option>
                        <option value="medium">🟡 Medium</option>
                        <option value="low">🟢 Low</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Status</label>
                    <select id="exportStatus" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500">
                        <option value="all">All Tasks</option>
                        <option value="active">Active Only</option>
                        <option value="archived">Archived Only</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Assignee</label>
                    <select id="exportAssignee" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500">
                        <option value="all">All Users</option>
                        @foreach($sharedUsers as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Label</label>
                    <select id="exportLabel" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500">
                        <option value="all">All Labels</option>
                        @foreach($labels as $label)
                        <option value="{{ $label->id }}">{{ $label->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Date From</label>
                    <input type="date" id="exportDateFrom" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Date To</label>
                    <input type="date" id="exportDateTo" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-600 mb-1">Search Title</label>
                    <input type="text" id="exportSearch" placeholder="Search by task title..." 
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500">
                </div>
            </div>
            
            <div class="bg-blue-50 rounded-lg p-3 mb-4">
                <div class="flex items-start gap-2">
                    <span class="text-blue-500 text-lg">💡</span>
                    <div class="text-xs text-blue-800">
                        <p class="font-medium mb-1">Export will include:</p>
                        <p>Task ID, Title, Description, List, Priority, Due Date, Assignees, Labels, Status, Created At, Updated At, Total Time</p>
                    </div>
                </div>
            </div>
            
            <div class="flex justify-end gap-3 border-t pt-4">
                <button type="button" onclick="closeExportModal()" class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300 transition">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    Export
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openExportModal() {
    document.getElementById('exportModal').classList.remove('hidden');
}

function closeExportModal() {
    document.getElementById('exportModal').classList.add('hidden');
}

function submitExport(event) {
    event.preventDefault();
    
    const format = document.getElementById('exportFormat').value;
    const listId = document.getElementById('exportList').value;
    const priority = document.getElementById('exportPriority').value;
    const status = document.getElementById('exportStatus').value;
    const assigneeId = document.getElementById('exportAssignee').value;
    const labelId = document.getElementById('exportLabel').value;
    const dateFrom = document.getElementById('exportDateFrom').value;
    const dateTo = document.getElementById('exportDateTo').value;
    const search = document.getElementById('exportSearch').value;
    
    let url = '';
    if (format === 'csv') {
        url = '{{ route("boards.export.csv", $board) }}';
    } else if (format === 'excel') {
        url = '{{ route("boards.export.excel", $board) }}';
    } else {
        url = '{{ route("boards.export.pdf", $board) }}';
    }
    
    // Buat form untuk submit (agar bisa download file)
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = url;
    form.target = '_blank';
    
    const csrf = document.createElement('input');
    csrf.type = 'hidden';
    csrf.name = '_token';
    csrf.value = document.querySelector('meta[name="csrf-token"]').content;
    form.appendChild(csrf);
    
    const fields = {
        list_id: listId,
        priority: priority,
        status: status,
        assignee_id: assigneeId,
        label_id: labelId,
        date_from: dateFrom,
        date_to: dateTo,
        search: search
    };
    
    for (const [key, value] of Object.entries(fields)) {
        if (value) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = key;
            input.value = value;
            form.appendChild(input);
        }
    }
    
    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
    
    showNotification('Export started...', 'success');
    closeExportModal();
}

document.addEventListener('click', function(event) {
    const modal = document.getElementById('exportModal');
    if (event.target === modal) {
        closeExportModal();
    }
});
</script>