<!-- Task Detail Modal -->
<div id="taskDetailModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" data-current-task-id="">
    <div class="relative top-10 mx-auto p-6 border-0 w-full max-w-2xl shadow-xl rounded-xl bg-white dark:bg-gray-800">
        <div class="flex justify-between items-start mb-4 pb-3 border-b dark:border-gray-700">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-gradient-to-r from-blue-500 to-purple-500 flex items-center justify-center text-white text-xl">
                    📋
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-800 dark:text-white" id="taskDetailTitle">Task Detail</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400" id="taskDetailList">Loading...</p>
                </div>
            </div>
            
            <!-- 🔔 WATCH BUTTON -->
            <div class="flex items-center gap-2">
                <button id="watchTaskBtn" onclick="toggleWatchTask()" 
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-medium transition-all duration-200 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer">
                    <svg id="watchIcon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                    <span id="watchBtnText" class="dark:text-white">Watch</span>
                    <span id="watchersCount" class="text-xs bg-gray-200 dark:bg-gray-700 rounded-full px-1.5 py-0.5 ml-1 hidden dark:text-gray-300">0</span>
                </button>
                
                <button onclick="closeTaskDetailModal()" class="text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300 text-2xl transition">&times;</button>
            </div>
        </div>
        
        <div class="max-h-[60vh] overflow-y-auto pr-2 space-y-4">
            <!-- Task Labels -->
            <div>
                <h4 class="text-sm font-semibold text-gray-600 dark:text-gray-400 mb-2 flex items-center gap-2">
                    <span>🏷️</span> Labels
                </h4>
                <div id="taskDetailLabels" class="flex flex-wrap gap-2">
                    <span class="text-gray-400 dark:text-gray-500 text-sm">No labels</span>
                </div>
            </div>
            
            <!-- Task Description -->
            <div>
                <h4 class="text-sm font-semibold text-gray-600 dark:text-gray-400 mb-2 flex items-center gap-2">
                    <span>📝</span> Description
                </h4>
                <div id="taskDetailDescription" class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 text-gray-700 dark:text-gray-300" style="white-space: pre-wrap; word-wrap: break-word;">
                    No description
                </div>
            </div>
            
            <!-- Task Details Grid -->
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Priority</p>
                    <div id="taskDetailPriority" class="font-semibold dark:text-gray-300">-</div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Due Date</p>
                    <div id="taskDetailDueDate" class="font-semibold dark:text-gray-300">-</div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Assigned To</p>
                    <div id="taskDetailAssignee" class="font-semibold flex items-center gap-2 dark:text-gray-300">-</div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Created At</p>
                    <div id="taskDetailCreated" class="font-semibold text-sm dark:text-gray-300">-</div>
                </div>
            </div>
            
            <!-- Watchers List -->
            <div class="border-t dark:border-gray-700 pt-3 mt-3">
                <h4 class="text-sm font-semibold text-gray-600 dark:text-gray-400 mb-2 flex items-center gap-2">
                    <span>👥</span> Watchers
                    <span id="watchersCountText" class="text-xs bg-gray-200 dark:bg-gray-700 rounded-full px-2 py-0.5 dark:text-gray-300">0</span>
                </h4>
                <div id="watchersList" class="flex flex-wrap gap-2">
                    <span class="text-gray-400 dark:text-gray-500 text-sm">No watchers yet. Click "Watch" to get updates.</span>
                </div>
            </div>
            
            <!-- Time Tracking Section -->
            <div class="border-t dark:border-gray-700 pt-3 mt-3">
                <div class="flex justify-between items-center mb-3">
                    <h4 class="text-sm font-semibold text-gray-600 dark:text-gray-400 flex items-center gap-2">
                        <span>⏱️</span> Time Tracking
                        <span id="totalTimeDisplay" class="text-xs bg-gray-200 dark:bg-gray-700 rounded-full px-2 py-0.5 dark:text-gray-300">00:00:00</span>
                    </h4>
                    <div class="flex gap-2" id="timerControls">
                        <button id="timerStartBtn" onclick="startTimer()" class="px-3 py-1 bg-green-500 text-white rounded-lg text-xs hover:bg-green-600 transition">▶ Start</button>
                        <button id="timerPauseBtn" onclick="pauseTimer()" class="px-3 py-1 bg-yellow-500 text-white rounded-lg text-xs hover:bg-yellow-600 transition hidden">⏸ Pause</button>
                        <button id="timerStopBtn" onclick="stopTimer()" class="px-3 py-1 bg-red-500 text-white rounded-lg text-xs hover:bg-red-600 transition hidden">⏹ Stop</button>
                        <button onclick="showTimeHistory()" class="px-3 py-1 bg-gray-500 text-white rounded-lg text-xs hover:bg-gray-600 transition">📋 History</button>
                    </div>
                </div>
                <div id="timerDisplay" class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3 text-center">
                    <div class="text-3xl font-mono font-bold text-gray-800 dark:text-white" id="timerClock">00:00:00</div>
                    <div id="timerStatus" class="text-xs text-gray-500 dark:text-gray-400 mt-1">Not started</div>
                </div>
            </div>
            
            <!-- Recurring Section -->
            <div class="border-t dark:border-gray-700 pt-3 mt-3">
                <div class="flex justify-between items-center mb-3">
                    <h4 class="text-sm font-semibold text-gray-600 dark:text-gray-400 flex items-center gap-2">
                        <span>🔄</span> Repeat
                    </h4>
                    <button onclick="editRecurringSettings()" id="editRecurringBtn" class="text-xs text-indigo-500 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300">
                        ✏️ Edit
                    </button>
                </div>
                <div id="recurringDisplay" class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3 text-sm text-gray-600 dark:text-gray-400">
                    <span class="text-gray-400 dark:text-gray-500">Not set</span>
                </div>
            </div>
            
            <!-- Custom Fields Section -->
            <div class="border-t dark:border-gray-700 pt-3 mt-3" id="customFieldsSection" style="display: none;">
                <div class="flex justify-between items-center mb-3">
                    <h4 class="text-sm font-semibold text-gray-600 dark:text-gray-400 flex items-center gap-2">
                        <span>⚙️</span> Custom Fields
                    </h4>
                    <button onclick="editCustomFields()" class="text-xs text-indigo-500 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300">
                        ✏️ Edit
                    </button>
                </div>
                <div id="customFieldsContainer" class="space-y-3">
                    <div class="text-center text-gray-400 dark:text-gray-500 text-sm py-2">Loading custom fields...</div>
                </div>
            </div>

            <!-- Checklists Section -->
            <div id="checklistsSection" class="border-t dark:border-gray-700 pt-3 mt-3">
                <div class="flex justify-between items-center mb-3">
                    <h4 class="text-sm font-semibold text-gray-600 dark:text-gray-400 flex items-center gap-2">
                        <span>✅</span> Checklists
                    </h4>
                    <button onclick="showAddChecklistForm()" class="text-xs text-blue-500 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">
                        + Add checklist
                    </button>
                </div>
                
                <div id="addChecklistForm" class="hidden mb-3">
                    <input type="text" id="newChecklistName" placeholder="Checklist name (e.g., Tasks to complete)" 
                           class="w-full border dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm mb-2">
                    <div class="flex justify-end gap-2">
                        <button onclick="hideAddChecklistForm()" class="px-3 py-1 bg-gray-200 dark:bg-gray-700 rounded text-xs dark:text-gray-300">Cancel</button>
                        <button onclick="createChecklist()" class="px-3 py-1 bg-blue-500 text-white rounded text-xs">Create</button>
                    </div>
                </div>
                
                <div id="checklistsContainer" class="space-y-3">
                    <div class="text-center text-gray-400 dark:text-gray-500 text-sm py-4">No checklists yet. Click "Add checklist" to start.</div>
                </div>
            </div>
            
            <!-- Attachments Section -->
            <div class="border-t dark:border-gray-700 pt-3 mt-3">
                <div class="flex justify-between items-center mb-3">
                    <h4 class="text-sm font-semibold text-gray-600 dark:text-gray-400 flex items-center gap-2">
                        <span>📎</span> Attachments
                        <span id="attachmentsCount" class="text-xs bg-gray-200 dark:bg-gray-700 rounded-full px-2 py-0.5 dark:text-gray-300">0</span>
                    </h4>
                    <button onclick="document.getElementById('fileInput').click()" class="text-xs text-blue-500 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">
                        + Add attachment
                    </button>
                </div>
                
                <input type="file" id="fileInput" class="hidden" onchange="uploadAttachment(this.files[0])">
                
                <div id="attachmentsContainer" class="space-y-2">
                    <div class="text-center text-gray-400 dark:text-gray-500 text-sm py-2">No attachments yet. Click "Add attachment" to upload files.</div>
                </div>
            </div>
            
            <!-- Comments Section -->
            <div class="border-t dark:border-gray-700 pt-3 mt-3">
                <h4 class="text-sm font-semibold text-gray-600 dark:text-gray-400 mb-3 flex items-center gap-2">
                    <span>💬</span> Comments
                    <span id="commentsCount" class="text-xs bg-gray-200 dark:bg-gray-700 rounded-full px-2 py-0.5 dark:text-gray-300">0</span>
                </h4>
                
                <div id="commentsList" class="space-y-3 max-h-80 overflow-y-auto mb-3 pr-1">
                    <div class="text-center text-gray-400 dark:text-gray-500 text-sm py-4">No comments yet. Be the first to comment!</div>
                </div>
                
                <div class="mt-3 relative">
                    <div class="flex gap-2">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 rounded-full bg-gradient-to-r from-blue-500 to-purple-500 flex items-center justify-center text-white text-xs font-bold">
                                {{ substr(auth()->user()->name, 0, 1) }}
                            </div>
                        </div>
                        <div class="flex-1 relative">
                            <textarea id="commentInput" rows="2" placeholder="Write a comment... (type @ to mention someone)" 
                                      class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 resize-none"></textarea>
                            <div class="flex justify-end mt-2">
                                <button onclick="submitComment()" class="px-3 py-1 bg-green-600 text-white rounded-lg text-sm hover:bg-green-700 transition">
                                    Post Comment
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Activity History Link -->
            <div class="bg-blue-50 dark:bg-blue-900/30 rounded-lg p-3">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-sm font-semibold text-blue-700 dark:text-blue-400">📋 Activity History</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Lihat semua perubahan pada task ini</p>
                    </div>
                    <button onclick="if(currentTaskIdForDetail) { closeTaskDetailModal(); showTaskActivityV2(currentTaskIdForDetail); } else { console.error('No task ID available'); alert('Error: Task ID not found'); }" 
                            class="btn-outline-accent text-sm px-4 py-2 dark:border-green-600 dark:text-green-400 dark:hover:bg-green-600 dark:hover:text-white">
                        View History →
                    </button>
                </div>
            </div>
        </div>
        
        <!-- FOOTER MODAL -->
        <div class="mt-6 flex justify-end gap-3 border-t dark:border-gray-700 pt-4">
            <button onclick="closeTaskDetailModal()" class="px-5 py-2 bg-gray-200 dark:bg-gray-700 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition font-medium dark:text-gray-300">Close</button>
            <button onclick="editTaskFromDetailModal()" class="px-5 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium">
                ✏️ Edit Task
            </button>
        </div>
    </div>
</div>

<style>
.mention-autocomplete {
    position: absolute;
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 0.5rem;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    max-height: 200px;
    overflow-y: auto;
    z-index: 100;
    min-width: 220px;
}

.mention-item {
    padding: 0.5rem 0.75rem;
    cursor: pointer;
    transition: all 0.15s ease;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.mention-item:hover,
.mention-item.active {
    background-color: #eff6ff;
}

.mention-item-avatar {
    width: 28px;
    height: 28px;
    border-radius: 9999px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 0.75rem;
    font-weight: bold;
    flex-shrink: 0;
}

.mention-item-name {
    font-weight: 500;
    color: #1f2937;
    font-size: 0.8rem;
}

.mention-item-username {
    font-size: 0.65rem;
    color: #6b7280;
}

.btn-outline-accent {
    background-color: transparent;
    color: #10b981;
    border: 1px solid #10b981;
    transition: all 0.2s ease;
    border-radius: 0.5rem;
    padding: 0.5rem 1rem;
    font-weight: 500;
    cursor: pointer;
}

.btn-outline-accent:hover {
    background-color: #10b981;
    color: white;
    transform: translateY(-1px);
}

.btn-accent {
    background-color: #10b981;
    color: white;
    transition: all 0.2s ease;
    border-radius: 0.5rem;
    padding: 0.5rem 1rem;
    font-weight: 500;
    cursor: pointer;
    border: none;
}

.btn-accent:hover {
    background-color: #34d399;
    transform: translateY(-1px);
}

.spinner {
    display: inline-block;
    width: 1rem;
    height: 1rem;
    border: 2px solid #e5e7eb;
    border-top-color: #3b82f6;
    border-radius: 50%;
    animation: spin 0.6s linear infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

/* ==============================================
   DARK MODE FOR TASK DETAIL MODAL
   ============================================== */
.dark #taskDetailModal .bg-white {
    background-color: var(--bg-secondary) !important;
}

.dark #taskDetailModal .bg-gray-50 {
    background-color: var(--list-bg) !important;
}

.dark #taskDetailModal .border-b,
.dark #taskDetailModal .border-t {
    border-color: var(--border-color) !important;
}

.dark #taskDetailModal .btn-outline-accent {
    border-color: #059669;
    color: #34d399;
}

.dark #taskDetailModal .btn-outline-accent:hover {
    background-color: #059669;
    color: white;
}

.dark .mention-autocomplete {
    background-color: var(--bg-secondary) !important;
    border-color: var(--border-color) !important;
}

.dark .mention-item:hover {
    background-color: var(--button-hover) !important;
}

.dark #watchTaskBtn {
    background-color: var(--bg-secondary) !important;
    border-color: var(--border-color) !important;
}

.dark .btn-accent {
    background-color: #059669;
}

.dark .btn-accent:hover {
    background-color: #10b981;
}

.dark .bg-blue-50 {
    background-color: #1e3a5f !important;
}

.dark .text-blue-700 {
    color: #60a5fa !important;
}

/* Dark mode for task detail modal */
.dark #taskDetailModal .bg-white {
    background-color: var(--bg-secondary) !important;
}

.dark #taskDetailModal .bg-gray-50 {
    background-color: var(--list-bg) !important;
}

.dark #taskDetailModal .border-b,
.dark #taskDetailModal .border-t {
    border-color: var(--border-color) !important;
}

.dark #taskDetailModal .btn-outline-accent {
    border-color: #059669;
    color: #34d399;
}

.dark #taskDetailModal .btn-outline-accent:hover {
    background-color: #059669;
    color: white;
}

.dark .mention-autocomplete {
    background-color: var(--bg-secondary) !important;
    border-color: var(--border-color) !important;
}

.dark .mention-item:hover {
    background-color: var(--button-hover) !important;
}

.dark #watchTaskBtn {
    background-color: var(--bg-secondary) !important;
    border-color: var(--border-color) !important;
}

.dark .btn-accent {
    background-color: #059669;
}

.dark .btn-accent:hover {
    background-color: #10b981;
}

.dark .bg-blue-50 {
    background-color: #1e3a5f !important;
}

.dark .text-blue-700 {
    color: #60a5fa !important;
}

/* ==============================================
   DARK MODE - DUE DATE STYLES (TASK DETAIL MODAL)
   ============================================== */

/* Overdue text in task detail modal */
.dark #taskDetailDueDate .text-red-600 {
    color: #fca5a5 !important;
}

.dark #taskDetailDueDate span {
    color: inherit !important;
}

/* Overdue badge */
.dark #taskDetailDueDate .bg-red-100 {
    background-color: #7f1d1d !important;
    color: #fca5a5 !important;
}

.dark #taskDetailDueDate .text-red-600 {
    color: #fca5a5 !important;
}

/* Due date section */
.dark #taskDetailDueDate {
    color: #fca5a5 !important;
}

/* ==============================================
   DARK MODE - CHECKLIST BADGE STYLES (MODAL)
   ============================================== */

.dark .bg-green-100 {
    background-color: #065f46 !important;
    color: #86efac !important;
}

.dark .bg-green-100 .text-green-700 {
    color: #86efac !important;
}

.dark .bg-gray-100 {
    background-color: #374151 !important;
    color: #d1d5db !important;
}

.dark .bg-gray-100 span {
    color: #d1d5db !important;
}

.dark .text-green-700 {
    color: #86efac !important;
}
</style>

<script>
// ==============================================
// TASK DETAIL MODAL - FULL VERSION
// WITH WATCHER, CUSTOM FIELDS, RECURRING, & TIME TRACKING
// ==============================================

let currentTaskIdForDetail = null;

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = `fixed bottom-4 right-4 z-50 px-4 py-2 rounded-lg shadow-lg text-white ${type === 'success' ? 'bg-green-500' : type === 'error' ? 'bg-red-500' : 'bg-blue-500'} dark:shadow-gray-800`;
    notification.textContent = message;
    document.body.appendChild(notification);
    setTimeout(() => notification.remove(), 3000);
}

function formatDate(dateString) {
    const date = new Date(dateString);
    const now = new Date();
    const diffMs = now - date;
    const diffMins = Math.floor(diffMs / 60000);
    const diffHours = Math.floor(diffMs / 3600000);
    const diffDays = Math.floor(diffMs / 86400000);
    
    if (diffMins < 1) return 'Just now';
    if (diffMins < 60) return `${diffMins}m ago`;
    if (diffHours < 24) return `${diffHours}h ago`;
    if (diffDays < 7) return `${diffDays}d ago`;
    return date.toLocaleDateString();
}

function formatTimeDisplay(seconds) {
    const hours = Math.floor(seconds / 3600);
    const minutes = Math.floor((seconds % 3600) / 60);
    const secs = seconds % 60;
    return `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
}

// ==============================================
// EDIT TASK FROM DETAIL MODAL
// ==============================================

function editTaskFromDetailModal() {
    console.log('🔧 editTaskFromDetailModal called');
    console.log('📌 currentTaskIdForDetail:', currentTaskIdForDetail);
    
    if (!currentTaskIdForDetail || currentTaskIdForDetail <= 0) {
        console.error('❌ No valid task ID for edit');
        alert('Error: Task ID not found. Please refresh the page.');
        return;
    }
    
    const taskIdToEdit = currentTaskIdForDetail;
    console.log('✅ Will edit task ID:', taskIdToEdit);
    
    closeTaskDetailModal();
    
    if (typeof openEditTaskModal === 'function') {
        openEditTaskModal(taskIdToEdit);
    } else {
        console.error('❌ openEditTaskModal function not found');
        alert('Error: Edit function not available');
    }
}

// ==============================================
// TASK WATCHER FUNCTIONS
// ==============================================

function loadWatchersStatus() {
    if (!currentTaskIdForDetail || currentTaskIdForDetail <= 0) return;
    
    fetch(`/tasks/${currentTaskIdForDetail}/watchers`, {
        headers: { 
            'Accept': 'application/json', 
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '' 
        },
        credentials: 'same-origin'
    })
    .then(response => {
        if (!response.ok) throw new Error('HTTP ' + response.status);
        return response.json();
    })
    .then(data => {
        if (data.success) {
            const watchBtnText = document.getElementById('watchBtnText');
            const watchIcon = document.getElementById('watchIcon');
            const watchersCount = document.getElementById('watchersCount');
            const watchersCountText = document.getElementById('watchersCountText');
            
            if (data.is_watching) {
                watchBtnText.textContent = 'Watching';
                if (watchBtnText.parentElement) {
                    watchBtnText.parentElement.classList.add('bg-green-50', 'border-green-200', 'text-green-700');
                }
            } else {
                watchBtnText.textContent = 'Watch';
                if (watchBtnText.parentElement) {
                    watchBtnText.parentElement.classList.remove('bg-green-50', 'border-green-200', 'text-green-700');
                }
            }
            
            if (data.count > 0) {
                watchersCount.textContent = data.count;
                watchersCount.classList.remove('hidden');
                if (watchersCountText) watchersCountText.textContent = data.count;
            } else {
                watchersCount.classList.add('hidden');
                if (watchersCountText) watchersCountText.textContent = '0';
            }
            
            renderWatchersList(data.watchers || []);
        }
    })
    .catch(error => console.error('Error loading watchers:', error));
}

function renderWatchersList(watchers) {
    const container = document.getElementById('watchersList');
    if (!container) return;
    
    if (!watchers || watchers.length === 0) {
        container.innerHTML = '<span class="text-gray-400 dark:text-gray-500 text-sm">No watchers yet. Click "Watch" to get updates.</span>';
        return;
    }
    
    let html = '<div class="flex flex-wrap gap-2 items-center">';
    watchers.forEach(watcher => {
        const avatarUrl = watcher.avatar_url || `https://ui-avatars.com/api/?background=1e3a5f&color=fff&name=${encodeURIComponent(watcher.name)}`;
        html += `
            <div class="flex items-center gap-1 bg-gray-100 dark:bg-gray-700 rounded-full px-2 py-1" title="${escapeHtml(watcher.name)}">
                <img src="${avatarUrl}" class="w-5 h-5 rounded-full object-cover" onerror="this.src='https://ui-avatars.com/api/?background=1e3a5f&color=fff&name=${escapeHtml(watcher.name.charAt(0))}'">
                <span class="text-xs text-gray-600 dark:text-gray-400">${escapeHtml(watcher.name)}</span>
            </div>
        `;
    });
    html += '</div>';
    
    container.innerHTML = html;
}

function toggleWatchTask() {
    if (!currentTaskIdForDetail || currentTaskIdForDetail <= 0) {
        showNotification('No task selected', 'error');
        return;
    }
    
    const btn = document.getElementById('watchBtnText');
    const originalText = btn ? btn.textContent : 'Watch';
    if (btn) btn.textContent = '...';
    
    fetch(`/tasks/${currentTaskIdForDetail}/watch/toggle`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
            'Accept': 'application/json'
        },
        credentials: 'same-origin'
    })
    .then(response => {
        if (!response.ok) throw new Error('HTTP ' + response.status);
        return response.json();
    })
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            loadWatchersStatus();
        } else {
            showNotification(data.message || 'Failed to toggle watch', 'error');
            if (btn) btn.textContent = originalText;
        }
    })
    .catch(error => {
        console.error('Error toggling watch:', error);
        showNotification('Error toggling watch: ' + error.message, 'error');
        if (btn) btn.textContent = originalText;
    });
}

// ==============================================
// MENTION AUTOCOMPLETE
// ==============================================

let mentionUsers = [];
let mentionSuggestions = [];
let mentionCurrentIndex = -1;
let mentionStartPos = -1;
let mentionPopup = null;
let isFetchingUsers = false;

function fetchMentionUsers() {
    if (!currentTaskIdForDetail || currentTaskIdForDetail <= 0) {
        console.warn('⚠️ Skipping fetchMentionUsers - invalid task ID:', currentTaskIdForDetail);
        return;
    }
    
    if (isFetchingUsers) return;
    isFetchingUsers = true;
    
    const taskId = currentTaskIdForDetail;
    console.log('📤 Fetching mention users for task:', taskId);
    
    fetch(`/tasks/${taskId}/assignable-users`, {
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
        },
        credentials: 'same-origin'
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.error) {
            console.error('Error response:', data.error);
            mentionUsers = [];
        } else {
            mentionUsers = data.all_users || [];
            console.log('✅ Mention users loaded:', mentionUsers.length);
        }
        isFetchingUsers = false;
    })
    .catch(err => {
        console.error('❌ Error fetching users for mention:', err.message);
        mentionUsers = [];
        isFetchingUsers = false;
    });
}

function createMentionPopup() {
    let popup = document.getElementById('mention-popup');
    if (!popup) {
        popup = document.createElement('div');
        popup.id = 'mention-popup';
        popup.className = 'mention-autocomplete hidden';
        document.body.appendChild(popup);
    }
    return popup;
}

function showMentionSuggestions(filterText, cursorPosition, textarea) {
    if (!mentionUsers || mentionUsers.length === 0) {
        if (!isFetchingUsers) {
            fetchMentionUsers();
        }
        return;
    }
    
    mentionPopup = createMentionPopup();
    
    const searchTerm = filterText.toLowerCase();
    const filtered = mentionUsers.filter(user => 
        (user.name && user.name.toLowerCase().includes(searchTerm)) || 
        (user.username && user.username.toLowerCase().includes(searchTerm))
    );
    
    if (filtered.length === 0) {
        hideMentionPopup();
        return;
    }
    
    mentionSuggestions = filtered;
    mentionCurrentIndex = -1;
    
    let html = '';
    filtered.forEach((user, index) => {
        const avatarText = user.name ? user.name.charAt(0).toUpperCase() : '?';
        html += `
            <div class="mention-item" data-user-id="${user.id}" data-username="${user.username}" data-name="${user.name || ''}" data-index="${index}">
                <div class="mention-item-avatar" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    ${escapeHtml(avatarText)}
                </div>
                <div>
                    <div class="mention-item-name">${escapeHtml(user.name || 'Unknown')}</div>
                    <div class="mention-item-username">@${escapeHtml(user.username || 'unknown')}</div>
                </div>
            </div>
        `;
    });
    
    mentionPopup.innerHTML = html;
    mentionPopup.classList.remove('hidden');
    
    const rect = textarea.getBoundingClientRect();
    const scrollTop = window.scrollY || document.documentElement.scrollTop;
    const scrollLeft = window.scrollX || document.documentElement.scrollLeft;
    
    mentionPopup.style.left = rect.left + scrollLeft + 'px';
    mentionPopup.style.bottom = (window.innerHeight - rect.top + scrollTop + 5) + 'px';
    
    document.querySelectorAll('.mention-item').forEach(item => {
        item.addEventListener('click', () => {
            const username = item.dataset.username;
            if (username) {
                insertMention(username, textarea);
            }
        });
    });
}

function hideMentionPopup() {
    if (mentionPopup) {
        mentionPopup.classList.add('hidden');
        mentionSuggestions = [];
        mentionCurrentIndex = -1;
    }
}

function insertMention(username, textarea) {
    if (mentionStartPos === -1) return;
    
    const currentValue = textarea.value;
    const beforeMention = currentValue.substring(0, mentionStartPos);
    const afterCursor = currentValue.substring(textarea.selectionStart);
    const mentionToInsert = `@${username} `;
    
    textarea.value = beforeMention + mentionToInsert + afterCursor;
    const newCursorPos = mentionStartPos + mentionToInsert.length;
    textarea.setSelectionRange(newCursorPos, newCursorPos);
    textarea.dispatchEvent(new Event('input', { bubbles: true }));
    
    hideMentionPopup();
    mentionStartPos = -1;
}

function handleMentionKeyboard(e, textarea) {
    if (!mentionPopup || mentionPopup.classList.contains('hidden')) return false;
    
    const items = document.querySelectorAll('.mention-item');
    if (items.length === 0) return false;
    
    switch (e.key) {
        case 'ArrowDown':
            e.preventDefault();
            mentionCurrentIndex = Math.min(mentionCurrentIndex + 1, items.length - 1);
            updateActiveMentionItem(items);
            break;
        case 'ArrowUp':
            e.preventDefault();
            mentionCurrentIndex = Math.max(mentionCurrentIndex - 1, 0);
            updateActiveMentionItem(items);
            break;
        case 'Enter':
        case 'Tab':
            e.preventDefault();
            if (mentionCurrentIndex >= 0 && mentionSuggestions[mentionCurrentIndex]) {
                insertMention(mentionSuggestions[mentionCurrentIndex].username, textarea);
            }
            break;
        case 'Escape':
            e.preventDefault();
            hideMentionPopup();
            mentionStartPos = -1;
            break;
        default:
            return false;
    }
    return true;
}

function updateActiveMentionItem(items) {
    items.forEach((item, index) => {
        if (index === mentionCurrentIndex) {
            item.classList.add('active');
            item.scrollIntoView({ block: 'nearest' });
        } else {
            item.classList.remove('active');
        }
    });
}

function initMentionAutocomplete() {
    const textarea = document.getElementById('commentInput');
    if (!textarea) return;
    
    if (currentTaskIdForDetail && currentTaskIdForDetail > 0) {
        fetchMentionUsers();
    }
    
    textarea.addEventListener('input', function(e) {
        const cursorPos = this.selectionStart;
        const textBeforeCursor = this.value.substring(0, cursorPos);
        const lastAtIndex = textBeforeCursor.lastIndexOf('@');
        
        if (lastAtIndex !== -1) {
            const charBeforeAt = lastAtIndex > 0 ? textBeforeCursor[lastAtIndex - 1] : '';
            const isValidStart = !charBeforeAt.match(/[\w]/);
            
            if (isValidStart) {
                const searchText = textBeforeCursor.substring(lastAtIndex + 1);
                mentionStartPos = lastAtIndex;
                showMentionSuggestions(searchText, cursorPos, this);
            } else {
                hideMentionPopup();
            }
        } else {
            hideMentionPopup();
        }
    });
    
    textarea.addEventListener('keydown', function(e) {
        if (handleMentionKeyboard(e, this)) return;
        if (e.key === 'ArrowLeft' || e.key === 'ArrowRight' || e.key === 'Home' || e.key === 'End') {
            hideMentionPopup();
            mentionStartPos = -1;
        }
    });
    
    textarea.addEventListener('blur', function() {
        setTimeout(() => {
            if (mentionPopup && !mentionPopup.matches(':hover')) {
                hideMentionPopup();
                mentionStartPos = -1;
            }
        }, 200);
    });
    
    document.addEventListener('click', function(e) {
        if (mentionPopup && !mentionPopup.contains(e.target) && e.target !== textarea) {
            hideMentionPopup();
            mentionStartPos = -1;
        }
    });
}

// ==============================================
// TIME TRACKING FUNCTIONS
// ==============================================

let timerInterval = null;
let currentTimerStatus = 'stopped';
let currentTotalSeconds = 0;
let currentActiveEntry = null;

function loadTimerStatus() {
    if (!currentTaskIdForDetail) return;
    
    console.log('Loading timer status for task:', currentTaskIdForDetail);
    
    fetch(`/tasks/${currentTaskIdForDetail}/time-status`, {
        headers: { 'Accept': 'application/json' }
    })
    .then(response => response.json())
    .then(data => {
        console.log('Timer status response:', data);
        
        if (data.success) {
            currentTotalSeconds = data.total_seconds || 0;
            updateTimerDisplay(currentTotalSeconds);
            document.getElementById('totalTimeDisplay').textContent = data.total_time || '00:00:00';
            
            if (data.active_entry) {
                currentActiveEntry = data.active_entry;
                
                if (data.active_entry.status === 'running') {
                    console.log('Timer is RUNNING');
                    currentTimerStatus = 'running';
                    showTimerControls('running');
                    startTimerTick(data.active_entry);
                } else if (data.active_entry.status === 'paused') {
                    console.log('Timer is PAUSED');
                    currentTimerStatus = 'paused';
                    showTimerControls('paused');
                    updateTimerDisplay(data.active_entry.total_seconds);
                }
            } else {
                console.log('No active timer');
                currentTimerStatus = 'stopped';
                showTimerControls('stopped');
                if (timerInterval) clearInterval(timerInterval);
            }
        }
    })
    .catch(error => console.error('Error loading timer status:', error));
}

function startTimerTick(entry) {
    if (timerInterval) clearInterval(timerInterval);
    
    let elapsed = entry.total_seconds || 0;
    const startedAt = new Date(entry.started_at);
    
    timerInterval = setInterval(() => {
        const now = new Date();
        const diff = Math.floor((now - startedAt) / 1000);
        updateTimerDisplay(elapsed + diff);
        document.getElementById('totalTimeDisplay').textContent = formatTimeDisplay(elapsed + diff);
    }, 1000);
}

function updateTimerDisplay(seconds) {
    const clockEl = document.getElementById('timerClock');
    if (clockEl) clockEl.textContent = formatTimeDisplay(seconds);
}

function showTimerControls(status) {
    const startBtn = document.getElementById('timerStartBtn');
    const pauseBtn = document.getElementById('timerPauseBtn');
    const stopBtn = document.getElementById('timerStopBtn');
    const statusText = document.getElementById('timerStatus');
    
    console.log('Show timer controls for status:', status);
    
    if (status === 'running') {
        if (startBtn) startBtn.classList.add('hidden');
        if (pauseBtn) pauseBtn.classList.remove('hidden');
        if (stopBtn) stopBtn.classList.remove('hidden');
        if (statusText) statusText.textContent = '🟢 Timer is running';
    } else if (status === 'paused') {
        if (startBtn) {
            startBtn.classList.remove('hidden');
            startBtn.textContent = '▶ Resume';
        }
        if (pauseBtn) pauseBtn.classList.add('hidden');
        if (stopBtn) stopBtn.classList.remove('hidden');
        if (statusText) statusText.textContent = '⏸ Timer paused';
    } else {
        if (startBtn) {
            startBtn.classList.remove('hidden');
            startBtn.textContent = '▶ Start';
        }
        if (pauseBtn) pauseBtn.classList.add('hidden');
        if (stopBtn) stopBtn.classList.add('hidden');
        if (statusText) statusText.textContent = '⏹ Not started';
    }
}

function startTimer() {
    if (!currentTaskIdForDetail) return;
    
    console.log('Starting timer for task:', currentTaskIdForDetail);
    showNotification('Starting timer...', 'info');
    
    fetch(`/tasks/${currentTaskIdForDetail}/time-start`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        console.log('Start timer response:', data);
        if (data.success) {
            showNotification('✓ Timer started', 'success');
            setTimeout(() => loadTimerStatus(), 500);
        } else {
            showNotification('Failed: ' + (data.error || 'Unknown error'), 'error');
        }
    })
    .catch(error => {
        console.error('Error starting timer:', error);
        showNotification('Error starting timer', 'error');
    });
}

function pauseTimer() {
    if (!currentTaskIdForDetail) return;
    
    console.log('Pausing timer for task:', currentTaskIdForDetail);
    showNotification('Pausing timer...', 'info');
    
    fetch(`/tasks/${currentTaskIdForDetail}/time-pause`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        console.log('Pause timer response:', data);
        if (data.success) {
            showNotification('✓ Timer paused', 'success');
            if (timerInterval) clearInterval(timerInterval);
            setTimeout(() => loadTimerStatus(), 500);
        } else {
            showNotification('Failed: ' + (data.error || 'Unknown error'), 'error');
        }
    })
    .catch(error => {
        console.error('Error pausing timer:', error);
        showNotification('Error pausing timer', 'error');
    });
}

function stopTimer() {
    if (!currentTaskIdForDetail) return;
    
    if (!confirm('Stop timer? This will save your time entry.')) return;
    
    console.log('Stopping timer for task:', currentTaskIdForDetail);
    showNotification('Stopping timer...', 'info');
    
    fetch(`/tasks/${currentTaskIdForDetail}/time-stop`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        console.log('Stop timer response:', data);
        if (data.success) {
            showNotification('✓ Timer stopped! Total time: ' + data.total_time, 'success');
            if (timerInterval) clearInterval(timerInterval);
            setTimeout(() => loadTimerStatus(), 500);
        } else {
            showNotification('Failed: ' + (data.error || 'Unknown error'), 'error');
        }
    })
    .catch(error => {
        console.error('Error stopping timer:', error);
        showNotification('Error stopping timer', 'error');
    });
}

function showTimeHistory() {
    if (!currentTaskIdForDetail) return;
    
    fetch(`/tasks/${currentTaskIdForDetail}/time-history`, {
        headers: { 'Accept': 'application/json' }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            let html = `
                <div id="timeHistoryModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-[80]">
                    <div class="relative top-20 mx-auto p-6 border-0 w-full max-w-2xl shadow-xl rounded-xl bg-white dark:bg-gray-800">
                        <div class="flex justify-between items-center mb-4 pb-3 border-b dark:border-gray-700">
                            <h3 class="text-lg font-bold text-gray-800 dark:text-white">⏱️ Time Tracking History</h3>
                            <button onclick="closeTimeHistoryModal()" class="text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300 text-2xl">&times;</button>
                        </div>
                        <div class="mb-4 p-3 bg-gray-100 dark:bg-gray-700 rounded-lg text-center">
                            <span class="font-semibold dark:text-gray-300">Total Time: </span>
                            <span class="text-2xl font-mono font-bold text-indigo-600 dark:text-indigo-400">${data.total_time}</span>
                        </div>
                        <div class="max-h-96 overflow-y-auto">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-50 dark:bg-gray-700 sticky top-0">
                                    <tr>
                                        <th class="p-2 text-left dark:text-gray-300">User</th>
                                        <th class="p-2 text-left dark:text-gray-300">Duration</th>
                                        <th class="p-2 text-left dark:text-gray-300">Date</th>
                                    </tr>
                                </thead>
                                <tbody>
            `;
            
            data.entries.data.forEach(entry => {
                let duration = entry.total_seconds;
                const durationStr = formatTimeDisplay(duration);
                const date = new Date(entry.created_at).toLocaleDateString('id-ID');
                
                html += `
                    <tr class="border-b dark:border-gray-700">
                        <td class="p-2 dark:text-gray-300">${escapeHtml(entry.user?.name || 'Unknown')}</td>
                        <td class="p-2 font-mono dark:text-gray-300">${durationStr}</td>
                        <td class="p-2 text-gray-500 dark:text-gray-400">${date}</td>
                    </tr>
                `;
            });
            
            html += `
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4 flex justify-end">
                            <button onclick="closeTimeHistoryModal()" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition dark:text-gray-300">Close</button>
                        </div>
                    </div>
                </div>
            `;
            
            const existingModal = document.getElementById('timeHistoryModal');
            if (existingModal) existingModal.remove();
            document.body.insertAdjacentHTML('beforeend', html);
        }
    })
    .catch(error => console.error('Error loading time history:', error));
}

function closeTimeHistoryModal() {
    const modal = document.getElementById('timeHistoryModal');
    if (modal) modal.remove();
}

// ==============================================
// RECURRING TASKS FUNCTIONS
// ==============================================

let currentRecurringData = null;

function loadRecurringSettings() {
    if (!currentTaskIdForDetail) return;
    
    fetch(`/tasks/${currentTaskIdForDetail}/recurring`, {
        headers: { 'Accept': 'application/json' }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            currentRecurringData = data.recurring;
            renderRecurringDisplay();
        }
    })
    .catch(error => console.error('Error loading recurring:', error));
}

function renderRecurringDisplay() {
    const container = document.getElementById('recurringDisplay');
    if (!container) return;
    
    if (currentRecurringData && currentRecurringData.is_active) {
        let frequencyText = '';
        switch (currentRecurringData.frequency) {
            case 'daily': frequencyText = `Every ${currentRecurringData.interval} day(s)`; break;
            case 'weekly': frequencyText = `Every ${currentRecurringData.interval} week(s)`; break;
            case 'monthly': frequencyText = `Every ${currentRecurringData.interval} month(s)`; break;
            case 'yearly': frequencyText = `Every ${currentRecurringData.interval} year(s)`; break;
            default: frequencyText = currentRecurringData.frequency;
        }
        
        let endText = '';
        if (currentRecurringData.until_date) {
            endText = `until ${new Date(currentRecurringData.until_date).toLocaleDateString('id-ID')}`;
        } else if (currentRecurringData.occurrences) {
            endText = `for ${currentRecurringData.occurrences} times (${currentRecurringData.occurrences_count || 0} done)`;
        }
        
        container.innerHTML = `
            <div class="flex items-center gap-2 flex-wrap">
                <span class="bg-indigo-100 dark:bg-indigo-900 text-indigo-700 dark:text-indigo-300 px-2 py-1 rounded-full text-xs">🔄 ${frequencyText}</span>
                ${endText ? `<span class="text-gray-500 dark:text-gray-400 text-xs">${endText}</span>` : ''}
                ${!currentRecurringData.is_active ? '<span class="text-red-500 dark:text-red-400 text-xs">(Inactive)</span>' : ''}
            </div>
        `;
    } else {
        container.innerHTML = '<span class="text-gray-400 dark:text-gray-500">Not set - task will not repeat</span>';
    }
}

function editRecurringSettings() {
    const modalHtml = `
        <div id="recurringModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-[70]">
            <div class="relative top-20 mx-auto p-6 border-0 w-full max-w-md shadow-xl rounded-xl bg-white dark:bg-gray-800">
                <div class="flex justify-between items-center mb-4 pb-3 border-b dark:border-gray-700">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white">🔄 Recurring Task Settings</h3>
                    <button onclick="closeRecurringModal()" class="text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300 text-2xl">&times;</button>
                </div>
                <form id="recurringForm" onsubmit="saveRecurringSettings(event)">
                    <div class="space-y-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Repeat</label>
                            <select id="recurringFrequency" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                                <option value="">Don't repeat</option>
                                <option value="daily" ${currentRecurringData?.frequency === 'daily' ? 'selected' : ''}>Daily</option>
                                <option value="weekly" ${currentRecurringData?.frequency === 'weekly' ? 'selected' : ''}>Weekly</option>
                                <option value="monthly" ${currentRecurringData?.frequency === 'monthly' ? 'selected' : ''}>Monthly</option>
                                <option value="yearly" ${currentRecurringData?.frequency === 'yearly' ? 'selected' : ''}>Yearly</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Repeat every (interval)</label>
                            <input type="number" id="recurringInterval" value="${currentRecurringData?.interval || 1}" min="1" max="365" 
                                   class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">End after</label>
                            <div class="flex gap-4">
                                <label class="flex items-center gap-2">
                                    <input type="radio" name="endType" value="never" ${!currentRecurringData?.until_date && !currentRecurringData?.occurrences ? 'checked' : ''}> Never
                                </label>
                                <label class="flex items-center gap-2">
                                    <input type="radio" name="endType" value="date" ${currentRecurringData?.until_date ? 'checked' : ''}> On date
                                </label>
                                <label class="flex items-center gap-2">
                                    <input type="radio" name="endType" value="occurrences" ${currentRecurringData?.occurrences ? 'checked' : ''}> After times
                                </label>
                            </div>
                        </div>
                        <div id="endDateField" class="${currentRecurringData?.until_date ? '' : 'hidden'}">
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Until Date</label>
                            <input type="date" id="recurringUntilDate" value="${currentRecurringData?.until_date || ''}" 
                                   class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div id="endOccurrencesField" class="${currentRecurringData?.occurrences ? '' : 'hidden'}">
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Number of times</label>
                            <input type="number" id="recurringOccurrences" value="${currentRecurringData?.occurrences || 1}" min="1" max="100" 
                                   class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div class="flex items-center gap-4">
                            <label class="flex items-center gap-2">
                                <input type="checkbox" id="recurringActive" ${currentRecurringData?.is_active !== false ? 'checked' : ''} 
                                       class="w-4 h-4 rounded border-gray-300 dark:border-gray-600 text-indigo-600 focus:ring-indigo-500">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Active</span>
                            </label>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 pt-3 border-t dark:border-gray-700">
                        <button type="button" onclick="closeRecurringModal()" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition dark:text-gray-300">Cancel</button>
                        ${currentRecurringData ? `<button type="button" onclick="deleteRecurringSettings()" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition">Delete</button>` : ''}
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">Save</button>
                    </div>
                </form>
            </div>
        </div>
    `;
    
    const existingModal = document.getElementById('recurringModal');
    if (existingModal) existingModal.remove();
    
    document.body.insertAdjacentHTML('beforeend', modalHtml);
    
    document.querySelectorAll('input[name="endType"]').forEach(radio => {
        radio.addEventListener('change', (e) => {
            const dateField = document.getElementById('endDateField');
            const occurrencesField = document.getElementById('endOccurrencesField');
            
            if (e.target.value === 'date') {
                dateField.classList.remove('hidden');
                occurrencesField.classList.add('hidden');
            } else if (e.target.value === 'occurrences') {
                dateField.classList.add('hidden');
                occurrencesField.classList.remove('hidden');
            } else {
                dateField.classList.add('hidden');
                occurrencesField.classList.add('hidden');
            }
        });
    });
}

function closeRecurringModal() {
    const modal = document.getElementById('recurringModal');
    if (modal) modal.remove();
}

function saveRecurringSettings(event) {
    event.preventDefault();
    
    const frequency = document.getElementById('recurringFrequency').value;
    const interval = parseInt(document.getElementById('recurringInterval').value);
    const isActive = document.getElementById('recurringActive').checked;
    
    let untilDate = null;
    let occurrences = null;
    
    const endType = document.querySelector('input[name="endType"]:checked')?.value;
    if (endType === 'date') {
        untilDate = document.getElementById('recurringUntilDate').value;
    } else if (endType === 'occurrences') {
        occurrences = parseInt(document.getElementById('recurringOccurrences').value);
    }
    
    if (!frequency) {
        if (currentRecurringData) {
            deleteRecurringSettings();
        }
        closeRecurringModal();
        return;
    }
    
    showNotification('Saving recurring settings...', 'info');
    
    fetch(`/tasks/${currentTaskIdForDetail}/recurring`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            frequency: frequency,
            interval: interval,
            until_date: untilDate,
            occurrences: occurrences,
            is_active: isActive
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('✓ Recurring settings saved!', 'success');
            closeRecurringModal();
            loadRecurringSettings();
        } else {
            showNotification('Failed: ' + (data.error || data.message || 'Unknown error'), 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error saving recurring settings', 'error');
    });
}

function deleteRecurringSettings() {
    if (!confirm('Remove recurring settings for this task?')) return;
    
    showNotification('Removing recurring settings...', 'info');
    
    fetch(`/tasks/${currentTaskIdForDetail}/recurring`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('✓ Recurring settings removed!', 'success');
            closeRecurringModal();
            currentRecurringData = null;
            renderRecurringDisplay();
        } else {
            showNotification('Failed to remove recurring settings', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error removing recurring settings', 'error');
    });
}

// ==============================================
// CHECKLIST FUNCTIONS (disederhanakan)
// ==============================================

function showAddChecklistForm() {
    document.getElementById('addChecklistForm').classList.remove('hidden');
}

function hideAddChecklistForm() {
    document.getElementById('addChecklistForm').classList.add('hidden');
    document.getElementById('newChecklistName').value = '';
}

function createChecklist() {
    const taskId = currentTaskIdForDetail;
    const name = document.getElementById('newChecklistName').value.trim();
    
    if (!name) {
        alert('Please enter a checklist name');
        return;
    }
    
    fetch(`/tasks/${taskId}/checklists`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        credentials: 'same-origin',
        body: JSON.stringify({ name: name })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            hideAddChecklistForm();
            loadChecklists(taskId);
            showNotification('Checklist created!', 'success');
        } else {
            alert('Failed to create checklist');
        }
    });
}

function loadChecklists(taskId) {
    fetch(`/tasks/${taskId}/edit`, {
        headers: { 'Accept': 'application/json' },
        credentials: 'same-origin'
    })
    .then(response => response.json())
    .then(task => {
        renderChecklists(task.checklists || []);
    });
}

function renderChecklists(checklists) {
    const container = document.getElementById('checklistsContainer');
    
    if (!checklists || checklists.length === 0) {
        container.innerHTML = '<div class="text-center text-gray-400 dark:text-gray-500 text-sm py-4">No checklists yet. Click "Add checklist" to start.</div>';
        return;
    }
    
    let html = '';
    checklists.forEach(checklist => {
        const items = checklist.items || [];
        const total = items.length;
        const completed = items.filter(i => i.is_checked).length;
        const progress = total > 0 ? Math.round((completed / total) * 100) : 0;
        
        html += `
            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3" data-checklist-id="${checklist.id}">
                <div class="flex justify-between items-center mb-2">
                    <input type="text" value="${escapeHtml(checklist.name)}" 
                           onblur="updateChecklistName(${checklist.id}, this.value)"
                           class="font-medium text-gray-800 dark:text-gray-200 bg-transparent border-b border-transparent hover:border-gray-300 dark:hover:border-gray-500 focus:border-blue-500 focus:outline-none px-1">
                    <button onclick="deleteChecklist(${checklist.id})" class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 text-xs px-2">🗑️</button>
                </div>
                
                <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400 mb-1">
                    <span>${completed}/${total} completed</span>
                    <span>${progress}%</span>
                </div>
                <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-1.5 mb-3">
                    <div class="bg-green-500 rounded-full h-1.5 transition-all" style="width: ${progress}%"></div>
                </div>
                
                <div class="space-y-1">
                    ${renderChecklistItems(items, checklist.id)}
                </div>
                
                <div class="mt-2">
                    <input type="text" placeholder="+ Add item" 
                           onkeypress="if(event.key === 'Enter') addChecklistItem(${checklist.id}, this.value)"
                           class="w-full text-sm text-gray-500 dark:text-gray-400 bg-transparent border-none focus:outline-none placeholder-gray-400 dark:placeholder-gray-500">
                </div>
            </div>
        `;
    });
    
    container.innerHTML = html;
}

function renderChecklistItems(items, checklistId) {
    if (!items || items.length === 0) return '';
    
    let html = '';
    items.forEach(item => {
        const textColor = item.is_checked ? 'text-gray-400 dark:text-gray-500' : 'text-gray-700 dark:text-gray-300';
        const textDecoration = item.is_checked ? 'line-through' : 'none';
        
        html += `
            <div class="flex items-center gap-2 py-1 group" data-item-id="${item.id}">
                <input type="checkbox" 
                       ${item.is_checked ? 'checked' : ''} 
                       onchange="toggleChecklistItem(${item.id}, this.checked)"
                       class="w-4 h-4 rounded border-gray-300 dark:border-gray-600 text-green-500 focus:ring-green-500 cursor-pointer">
                <input type="text" value="${escapeHtml(item.name)}"
                       onblur="updateChecklistItem(${item.id}, this.value)"
                       class="flex-1 text-sm ${textColor} bg-transparent border-b border-transparent hover:border-gray-300 dark:hover:border-gray-600 focus:border-blue-500 focus:outline-none transition-all duration-200"
                       style="text-decoration: ${textDecoration}">
                <button onclick="deleteChecklistItem(${item.id})" 
                        class="opacity-0 group-hover:opacity-100 text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 text-xs transition">✕</button>
            </div>
        `;
    });
    return html;
}

function updateChecklistName(checklistId, newName) {
    if (!newName.trim()) return;
    
    fetch(`/checklists/${checklistId}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        credentials: 'same-origin',
        body: JSON.stringify({ name: newName })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) showNotification('Checklist updated!', 'success');
    });
}

function deleteChecklist(checklistId) {
    if (!confirm('Delete this checklist and all its items?')) return;
    
    fetch(`/checklists/${checklistId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        credentials: 'same-origin'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Checklist deleted!', 'success');
            loadChecklists(currentTaskIdForDetail);
        }
    });
}

function addChecklistItem(checklistId, itemName) {
    if (!itemName.trim()) return;
    
    fetch(`/checklists/${checklistId}/items`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        credentials: 'same-origin',
        body: JSON.stringify({ name: itemName })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) loadChecklists(currentTaskIdForDetail);
    });
    event.target.value = '';
}

function toggleChecklistItem(itemId, isChecked) {
    fetch(`/checklist-items/${itemId}/toggle`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        credentials: 'same-origin'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            loadChecklists(currentTaskIdForDetail);
            showNotification(isChecked ? 'Item completed!' : 'Item unchecked', 'success');
        }
    });
}

function updateChecklistItem(itemId, newName) {
    if (!newName.trim()) return;
    
    fetch(`/checklist-items/${itemId}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        credentials: 'same-origin',
        body: JSON.stringify({ name: newName })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) showNotification('Item updated!', 'success');
    });
}

function deleteChecklistItem(itemId) {
    if (!confirm('Delete this item?')) return;
    
    fetch(`/checklist-items/${itemId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        credentials: 'same-origin'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Item deleted!', 'success');
            loadChecklists(currentTaskIdForDetail);
        }
    });
}

// ==============================================
// ATTACHMENT FUNCTIONS
// ==============================================

function loadAttachments(taskId) {
    fetch(`/tasks/${taskId}/edit`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        credentials: 'same-origin'
    })
    .then(response => response.json())
    .then(task => {
        renderAttachments(task.attachments || []);
        document.getElementById('attachmentsCount').textContent = task.attachments?.length || 0;
    })
    .catch(error => {
        console.error('Error loading attachments:', error);
        document.getElementById('attachmentsContainer').innerHTML = '<div class="text-center text-red-400 dark:text-red-500 text-sm py-2">Failed to load attachments</div>';
    });
}

function getFileIcon(fileType) {
    const icons = {
        'image': '🖼️',
        'pdf': '📄',
        'document': '📝',
        'spreadsheet': '📊',
        'archive': '🗜️',
        'text': '📃',
        'other': '📎'
    };
    return icons[fileType] || '📎';
}

function formatFileSize(bytes) {
    if (bytes >= 1073741824) return (bytes / 1073741824).toFixed(2) + ' GB';
    if (bytes >= 1048576) return (bytes / 1048576).toFixed(2) + ' MB';
    if (bytes >= 1024) return (bytes / 1024).toFixed(2) + ' KB';
    return bytes + ' bytes';
}

function uploadAttachment(file) {
    if (!file) return;
    
    const formData = new FormData();
    formData.append('file', file);
    
    showNotification('Uploading file...', 'info');
    
    fetch(`/tasks/${currentTaskIdForDetail}/attachments`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        credentials: 'same-origin',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('File uploaded!', 'success');
            loadAttachments(currentTaskIdForDetail);
        } else {
            showNotification('Upload failed: ' + (data.error || 'Unknown error'), 'error');
        }
    })
    .catch(error => {
        console.error('Upload error:', error);
        showNotification('Upload failed', 'error');
    });
    
    document.getElementById('fileInput').value = '';
}

function deleteAttachment(attachmentId) {
    if (!confirm('Are you sure you want to delete this file?')) return;
    
    fetch(`/attachments/${attachmentId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        credentials: 'same-origin'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('File deleted!', 'success');
            loadAttachments(currentTaskIdForDetail);
        } else {
            showNotification('Delete failed', 'error');
        }
    });
}

function setAsCover(attachmentId) {
    fetch(`/attachments/${attachmentId}/set-cover`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        credentials: 'same-origin'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Cover image set!', 'success');
            loadAttachments(currentTaskIdForDetail);
            setTimeout(() => location.reload(), 500);
        } else {
            showNotification('Failed to set cover', 'error');
        }
    })
    .catch(error => {
        console.error('Set cover error:', error);
        showNotification('Error setting cover', 'error');
    });
}

function removeCover() {
    if (!confirm('Remove cover image from this task?')) return;
    
    fetch(`/tasks/${currentTaskIdForDetail}/remove-cover`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        credentials: 'same-origin'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Cover removed!', 'success');
            loadAttachments(currentTaskIdForDetail);
            setTimeout(() => location.reload(), 500);
        }
    });
}

function renderAttachments(attachments) {
    const container = document.getElementById('attachmentsContainer');
    
    if (!attachments || attachments.length === 0) {
        container.innerHTML = '<div class="text-center text-gray-400 dark:text-gray-500 text-sm py-2">No attachments yet. Click "Add attachment" to upload files.</div>';
        return;
    }
    
    let html = '';
    attachments.forEach(attachment => {
        const isOwner = attachment.user_id === {{ auth()->id() }};
        const isAdmin = {{ auth()->user()->isAdmin() ? 'true' : 'false' }};
        const canDelete = isOwner || isAdmin;
        const isCover = attachment.is_cover;
        const isImage = attachment.file_type === 'image';
        
        let icon = getFileIcon(attachment.file_type);
        let previewHtml = '';
        
        if (isImage) {
            previewHtml = `<div class="mt-1">
                <img src="${attachment.file_path}" alt="${attachment.file_name}" class="max-w-full h-20 rounded object-cover cursor-pointer" onclick="window.open('${attachment.file_path}', '_blank')">
            </div>`;
        }
        
        html += `
            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-2 ${isCover ? 'ring-2 ring-green-500 bg-green-50 dark:bg-green-900/30' : ''}" data-attachment-id="${attachment.id}">
                <div class="flex justify-between items-center flex-wrap gap-2">
                    <div class="flex items-center gap-3 flex-1 min-w-0">
                        <div class="text-2xl flex-shrink-0">${icon}</div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-800 dark:text-gray-200 truncate" title="${attachment.file_name}">${escapeHtml(attachment.file_name)}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">${formatFileSize(attachment.file_size)}</p>
                            ${isCover ? '<p class="text-xs text-green-600 dark:text-green-400 font-medium">✓ Cover image</p>' : ''}
                        </div>
                    </div>
                    <div class="flex gap-1 flex-shrink-0">
                        <a href="/attachments/${attachment.id}/download" class="text-blue-500 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 p-1 rounded" title="Download">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                            </svg>
                        </a>
                        ${isImage && !isCover ? `
                        <button onclick="setAsCover(${attachment.id})" class="bg-green-500 hover:bg-green-600 text-white px-2 py-1 rounded-md text-xs font-medium shadow-sm transition-all" title="Set as cover">
                            🖼️ Set Cover
                        </button>
                        ` : ''}
                        ${canDelete ? `
                        <button onclick="deleteAttachment(${attachment.id})" class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 p-1 rounded" title="Delete">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                        ` : ''}
                    </div>
                </div>
                ${previewHtml}
            </div>
        `;
    });
    
    const hasCover = attachments.some(a => a.is_cover);
    if (hasCover) {
        html += `
            <div class="text-right mt-2">
                <button onclick="removeCover()" class="text-xs text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300">
                    Remove cover image
                </button>
            </div>
        `;
    }
    
    container.innerHTML = html;
}

// ==============================================
// COMMENTS FUNCTIONS
// ==============================================

function loadComments(taskId) {
    fetch(`/tasks/${taskId}/comments`, {
        headers: { 'Accept': 'application/json' },
        credentials: 'same-origin'
    })
    .then(response => response.json())
    .then(comments => {
        renderComments(comments);
        document.getElementById('commentsCount').textContent = comments.length;
    });
}

function renderComments(comments) {
    const container = document.getElementById('commentsList');
    
    if (!comments || comments.length === 0) {
        container.innerHTML = '<div class="text-center text-gray-400 dark:text-gray-500 text-sm py-4">No comments yet. Be the first to comment!</div>';
        return;
    }
    
    let html = '';
    comments.forEach(comment => {
        const isOwner = comment.user_id === {{ auth()->id() }};
        const isAdmin = {{ auth()->user()->isAdmin() ? 'true' : 'false' }};
        const canModify = isOwner || isAdmin;
        
        let formattedContent = escapeHtml(comment.content);
        formattedContent = formattedContent.replace(/@(\w+)/g, '<span class="text-blue-600 dark:text-blue-400 font-medium">@$1</span>');
        
        html += `
            <div class="comment-item bg-gray-50 dark:bg-gray-700 rounded-lg p-3" data-comment-id="${comment.id}">
                <div class="flex gap-2">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-r from-gray-400 to-gray-500 flex items-center justify-center text-white text-xs font-bold">
                            ${comment.user ? escapeHtml(comment.user.name.charAt(0)) : '?'}
                        </div>
                    </div>
                    <div class="flex-1">
                        <div class="flex justify-between items-start">
                            <div>
                                <span class="font-medium text-sm text-gray-800 dark:text-gray-200">${comment.user ? escapeHtml(comment.user.name) : 'Unknown'}</span>
                                <span class="text-xs text-gray-400 dark:text-gray-500 ml-2">${formatDate(comment.created_at)}</span>
                            </div>
                            ${canModify ? `
                            <div class="flex gap-1">
                                <button onclick="editComment(${comment.id})" class="text-blue-500 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 text-xs">Edit</button>
                                <button onclick="deleteComment(${comment.id})" class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 text-xs">Delete</button>
                            </div>
                            ` : ''}
                        </div>
                        <div class="comment-content text-sm text-gray-700 dark:text-gray-300 mt-1">${formattedContent}</div>
                        <div class="flex items-center gap-3 mt-2">
                            <button onclick="likeComment(${comment.id})" class="flex items-center gap-1 text-xs text-gray-500 dark:text-gray-400 hover:text-red-500 dark:hover:text-red-400 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                </svg>
                                <span id="likes-${comment.id}">${comment.likes_count || 0}</span>
                            </button>
                            <button onclick="showReplyForm(${comment.id})" class="text-xs text-gray-500 dark:text-gray-400 hover:text-blue-500 dark:hover:text-blue-400 transition">Reply</button>
                        </div>
                        <div id="reply-form-${comment.id}" class="hidden mt-2">
                            <textarea id="reply-input-${comment.id}" rows="2" placeholder="Write a reply..." 
                                      class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500"></textarea>
                            <div class="flex gap-2 mt-1">
                                <button onclick="submitReply(${comment.id})" class="px-2 py-1 bg-green-600 text-white rounded text-xs">Reply</button>
                                <button onclick="hideReplyForm(${comment.id})" class="px-2 py-1 bg-gray-300 dark:bg-gray-600 rounded text-xs dark:text-gray-300">Cancel</button>
                            </div>
                        </div>
                        <div id="replies-${comment.id}" class="ml-6 mt-2 space-y-2">
                            ${renderReplies(comment.replies)}
                        </div>
                    </div>
                </div>
            </div>
        `;
    });
    
    container.innerHTML = html;
}

function renderReplies(replies) {
    if (!replies || replies.length === 0) return '';
    
    let html = '';
    replies.forEach(reply => {
        const isOwner = reply.user_id === {{ auth()->id() }};
        const isAdmin = {{ auth()->user()->isAdmin() ? 'true' : 'false' }};
        const canModify = isOwner || isAdmin;
        
        let formattedContent = escapeHtml(reply.content);
        formattedContent = formattedContent.replace(/@(\w+)/g, '<span class="text-blue-600 dark:text-blue-400 font-medium">@$1</span>');
        
        html += `
            <div class="bg-white dark:bg-gray-800 rounded-lg p-2" data-reply-id="${reply.id}">
                <div class="flex gap-2">
                    <div class="flex-shrink-0">
                        <div class="w-6 h-6 rounded-full bg-gray-400 flex items-center justify-center text-white text-xs font-bold">
                            ${reply.user ? escapeHtml(reply.user.name.charAt(0)) : '?'}
                        </div>
                    </div>
                    <div class="flex-1">
                        <div class="flex justify-between items-start">
                            <div>
                                <span class="font-medium text-xs text-gray-800 dark:text-gray-200">${reply.user ? escapeHtml(reply.user.name) : 'Unknown'}</span>
                                <span class="text-xs text-gray-400 dark:text-gray-500 ml-1">${formatDate(reply.created_at)}</span>
                            </div>
                            ${canModify ? `
                            <div class="flex gap-1">
                                <button onclick="editComment(${reply.id})" class="text-blue-500 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 text-xs">Edit</button>
                                <button onclick="deleteComment(${reply.id})" class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 text-xs">Delete</button>
                            </div>
                            ` : ''}
                        </div>
                        <div class="text-xs text-gray-700 dark:text-gray-300 mt-0.5">${formattedContent}</div>
                        <button onclick="likeComment(${reply.id})" class="flex items-center gap-1 mt-1 text-xs text-gray-500 dark:text-gray-400 hover:text-red-500 dark:hover:text-red-400">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                            </svg>
                            <span id="likes-${reply.id}">${reply.likes_count || 0}</span>
                        </button>
                    </div>
                </div>
            </div>
        `;
    });
    return html;
}

function submitComment() {
    const content = document.getElementById('commentInput').value.trim();
    if (!content) {
        alert('Please enter a comment');
        return;
    }
    
    fetch(`/tasks/${currentTaskIdForDetail}/comments`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        credentials: 'same-origin',
        body: JSON.stringify({ content: content })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('commentInput').value = '';
            loadComments(currentTaskIdForDetail);
            showNotification('Comment posted!', 'success');
        }
    });
}

function submitReply(parentId) {
    const content = document.getElementById(`reply-input-${parentId}`).value.trim();
    if (!content) return;
    
    fetch(`/tasks/${currentTaskIdForDetail}/comments`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        credentials: 'same-origin',
        body: JSON.stringify({ content: content, parent_id: parentId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            hideReplyForm(parentId);
            loadComments(currentTaskIdForDetail);
            showNotification('Reply posted!', 'success');
        }
    });
}

function editComment(commentId) {
    const newContent = prompt('Edit your comment:');
    if (!newContent) return;
    
    fetch(`/comments/${commentId}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        credentials: 'same-origin',
        body: JSON.stringify({ content: newContent })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            loadComments(currentTaskIdForDetail);
            showNotification('Comment updated!', 'success');
        }
    });
}

function deleteComment(commentId) {
    if (!confirm('Are you sure you want to delete this comment?')) return;
    
    fetch(`/comments/${commentId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        credentials: 'same-origin'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            loadComments(currentTaskIdForDetail);
            showNotification('Comment deleted!', 'success');
        }
    });
}

function likeComment(commentId) {
    fetch(`/comments/${commentId}/like`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        credentials: 'same-origin'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const likesSpan = document.getElementById(`likes-${commentId}`);
            if (likesSpan) likesSpan.textContent = data.likes_count;
        }
    });
}

function showReplyForm(commentId) {
    const form = document.getElementById(`reply-form-${commentId}`);
    if (form) form.classList.remove('hidden');
}

function hideReplyForm(commentId) {
    const form = document.getElementById(`reply-form-${commentId}`);
    if (form) form.classList.add('hidden');
    const input = document.getElementById(`reply-input-${commentId}`);
    if (input) input.value = '';
}

// ==============================================
// CUSTOM FIELDS FUNCTIONS
// ==============================================

let customFieldsData = [];

function loadCustomFieldsForTask() {
    if (!currentTaskIdForDetail) {
        console.log('No task ID, skipping custom fields load');
        return;
    }
    
    console.log('Loading custom fields for task:', currentTaskIdForDetail);
    
    fetch(`/tasks/${currentTaskIdForDetail}/custom-fields`, {
        headers: { 
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
        },
        credentials: 'same-origin'
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('HTTP ' + response.status);
        }
        return response.json();
    })
    .then(data => {
        console.log('Custom fields response:', data);
        if (data.success && data.values && data.values.length > 0) {
            customFieldsData = data.values;
            const section = document.getElementById('customFieldsSection');
            if (section) section.style.display = 'block';
            renderCustomFieldsDisplay();
        } else {
            const section = document.getElementById('customFieldsSection');
            if (section) section.style.display = 'none';
            console.log('No custom fields found for this board');
        }
    })
    .catch(error => {
        console.error('Error loading custom fields:', error);
        const section = document.getElementById('customFieldsSection');
        if (section) section.style.display = 'none';
    });
}

function renderCustomFieldsDisplay() {
    const container = document.getElementById('customFieldsContainer');
    if (!container) return;
    
    if (!customFieldsData || customFieldsData.length === 0) {
        container.innerHTML = '<div class="text-center text-gray-400 dark:text-gray-500 text-sm py-2">No custom fields for this board</div>';
        return;
    }
    
    let html = '<div class="grid grid-cols-1 gap-3">';
    customFieldsData.forEach(item => {
        const field = item.field;
        const value = item.value;
        let displayValue = '-';
        
        if (field.type === 'checkbox') {
            displayValue = value ? '✓ Yes' : '✗ No';
        } else if (field.type === 'date' && value) {
            displayValue = new Date(value).toLocaleDateString('id-ID');
        } else if (value) {
            displayValue = escapeHtml(value);
        }
        
        html += `
            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">${escapeHtml(field.label)}</p>
                <p class="text-sm text-gray-800 dark:text-gray-200 break-words">${displayValue}</p>
            </div>
        `;
    });
    html += '</div>';
    
    container.innerHTML = html;
}

function editCustomFields() {
    if (!customFieldsData || customFieldsData.length === 0) return;
    
    let modalHtml = `
        <div id="editCustomFieldsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-[60]">
            <div class="relative top-20 mx-auto p-6 border-0 w-full max-w-md shadow-xl rounded-xl bg-white dark:bg-gray-800">
                <div class="flex justify-between items-center mb-4 pb-3 border-b dark:border-gray-700">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white">Edit Custom Fields</h3>
                    <button onclick="closeEditCustomFieldsModal()" class="text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300 text-2xl">&times;</button>
                </div>
                <form id="editCustomFieldsForm" onsubmit="saveCustomFields(event)">
                    <div id="editCustomFieldsContainer" class="space-y-4 max-h-96 overflow-y-auto mb-4">
    `;
    
    customFieldsData.forEach(item => {
        const field = item.field;
        const value = item.value;
        
        if (field.type === 'checkbox') {
            modalHtml += `
                <div>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="custom_fields[${field.id}]" value="1" ${value ? 'checked' : ''} 
                               class="w-4 h-4 rounded border-gray-300 dark:border-gray-600 text-indigo-600 focus:ring-indigo-500">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">${escapeHtml(field.label)}</span>
                    </label>
                </div>
            `;
        } else if (field.type === 'dropdown') {
            const options = field.options || [];
            modalHtml += `
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">${escapeHtml(field.label)}</label>
                    <select name="custom_fields[${field.id}]" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                        <option value="">-- Select --</option>
                        ${options.map(opt => `<option value="${escapeHtml(opt)}" ${value === opt ? 'selected' : ''}>${escapeHtml(opt)}</option>`).join('')}
                    </select>
                </div>
            `;
        } else {
            modalHtml += `
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">${escapeHtml(field.label)}</label>
                    <input type="${field.type}" name="custom_fields[${field.id}]" value="${escapeHtml(value || '')}" 
                           class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                </div>
            `;
        }
    });
    
    modalHtml += `
                    </div>
                    <div class="flex justify-end gap-3 pt-3 border-t dark:border-gray-700">
                        <button type="button" onclick="closeEditCustomFieldsModal()" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition dark:text-gray-300">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">Save</button>
                    </div>
                </form>
            </div>
        </div>
    `;
    
    const existingModal = document.getElementById('editCustomFieldsModal');
    if (existingModal) existingModal.remove();
    
    document.body.insertAdjacentHTML('beforeend', modalHtml);
}

function closeEditCustomFieldsModal() {
    const modal = document.getElementById('editCustomFieldsModal');
    if (modal) modal.remove();
}

function saveCustomFields(event) {
    event.preventDefault();
    
    const formData = new FormData(event.target);
    const values = {};
    
    for (let [key, value] of formData.entries()) {
        const match = key.match(/custom_fields\[(\d+)\]/);
        if (match) {
            values[match[1]] = value;
        }
    }
    
    showNotification('Saving custom fields...', 'info');
    
    fetch(`/tasks/${currentTaskIdForDetail}/custom-fields`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ values: values })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('✓ Custom fields saved!', 'success');
            closeEditCustomFieldsModal();
            loadCustomFieldsForTask();
        } else {
            showNotification('Failed to save: ' + (data.message || 'Unknown error'), 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error saving custom fields', 'error');
    });
}

// ==============================================
// COVER FUNCTIONS
// ==============================================

function removeCoverFromTask() {
    if (!currentTaskIdForDetail) {
        showNotification('No task selected', 'error');
        return;
    }
    
    if (!confirm('Remove cover image from this task?')) return;
    
    showNotification('Removing cover...', 'info');
    
    fetch(`/tasks/${currentTaskIdForDetail}/remove-cover`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        credentials: 'same-origin'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('✓ Cover removed successfully!', 'success');
            loadAttachments(currentTaskIdForDetail);
            setTimeout(() => location.reload(), 500);
        } else {
            showNotification('Failed to remove cover: ' + (data.error || 'Unknown error'), 'error');
        }
    })
    .catch(error => {
        console.error('Remove cover error:', error);
        showNotification('Error removing cover', 'error');
    });
}

// ==============================================
// MAIN FUNCTIONS
// ==============================================

function openTaskDetailModal(taskId) {
    console.log('openTaskDetailModal called with taskId:', taskId);
    
    if (!taskId || taskId === 'null' || taskId === 'undefined' || taskId <= 0) {
        console.error('Invalid task ID for detail modal:', taskId);
        alert('Error: Invalid task ID. Please refresh the page.');
        return;
    }
    
    currentTaskIdForDetail = taskId;
    
    const modal = document.getElementById('taskDetailModal');
    if (modal) modal.setAttribute('data-current-task-id', taskId);
    
    document.getElementById('taskDetailModal').classList.remove('hidden');
    
    document.getElementById('taskDetailTitle').innerHTML = '<div class="spinner w-5 h-5 border-2 border-blue-500 border-t-transparent rounded-full animate-spin"></div> Loading...';
    document.getElementById('taskDetailList').textContent = 'Loading...';
    document.getElementById('taskDetailDescription').innerHTML = '<div class="spinner w-5 h-5 border-2 border-gray-300 border-t-blue-500 rounded-full animate-spin"></div> Loading...';
    
    fetch(`/tasks/${taskId}/edit`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        credentials: 'same-origin'
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('HTTP ' + response.status);
        }
        return response.json();
    })
    .then(task => {
        console.log('Task loaded for detail:', task.id, task.title);
        
        document.getElementById('taskDetailTitle').innerHTML = escapeHtml(task.title);
        
        if (task.task_list && task.task_list.name) {
            document.getElementById('taskDetailList').innerHTML = `<span class="text-gray-500 dark:text-gray-400">📌 List:</span> ${escapeHtml(task.task_list.name)}`;
        } else {
            document.getElementById('taskDetailList').innerHTML = '<span class="text-gray-500 dark:text-gray-400">List:</span> -';
        }
        
        const labelsContainer = document.getElementById('taskDetailLabels');
        if (task.labels && task.labels.length > 0) {
            let labelsHtml = '';
            task.labels.forEach(label => {
                labelsHtml += `<span class="px-3 py-1 rounded-full text-sm" style="background-color: ${label.color}20; color: ${label.color}; border-left: 3px solid ${label.color}">
                    ${escapeHtml(label.name)}
                </span>`;
            });
            labelsContainer.innerHTML = labelsHtml;
        } else {
            labelsContainer.innerHTML = '<span class="text-gray-400 dark:text-gray-500 text-sm">No labels assigned</span>';
        }
        
        const desc = task.description || 'No description provided.';
        document.getElementById('taskDetailDescription').innerHTML = escapeHtml(desc).replace(/\n/g, '<br>');
        
        const priorityEl = document.getElementById('taskDetailPriority');
        if (task.priority === 'high') {
            priorityEl.innerHTML = '<span class="text-red-600 dark:text-red-400">🔴 High Priority</span>';
        } else if (task.priority === 'medium') {
            priorityEl.innerHTML = '<span class="text-yellow-600 dark:text-yellow-400">🟡 Medium Priority</span>';
        } else if (task.priority === 'low') {
            priorityEl.innerHTML = '<span class="text-green-600 dark:text-green-400">🟢 Low Priority</span>';
        } else {
            priorityEl.innerHTML = '-';
        }
        
        const dueDateEl = document.getElementById('taskDetailDueDate');
        if (task.due_date) {
            const dueDate = new Date(task.due_date);
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            const isOverdue = dueDate < today;
            dueDateEl.innerHTML = `<span class="${isOverdue ? 'text-red-600 dark:text-red-400' : 'text-gray-700 dark:text-gray-300'}">
                📅 ${dueDate.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })}
                ${isOverdue ? '<span class="ml-2 text-xs bg-red-100 dark:bg-red-900 text-red-600 dark:text-red-400 px-2 py-0.5 rounded">Overdue</span>' : ''}
            </span>`;
        } else {
            dueDateEl.innerHTML = '<span class="text-gray-400 dark:text-gray-500">Not set</span>';
        }
        
        const assigneeEl = document.getElementById('taskDetailAssignee');
        if (task.assignees && task.assignees.length > 0) {
            assigneeEl.innerHTML = `<div class="flex items-center gap-1 flex-wrap">${task.assignees.map(a => `<div class="w-6 h-6 rounded-full bg-blue-500 flex items-center justify-center text-white text-xs font-bold" title="${a.name}">${a.name.charAt(0)}</div>`).join('')}</div>`;
        } else {
            assigneeEl.innerHTML = '<span class="text-gray-400 dark:text-gray-500">Not assigned</span>';
        }
        
        const createdAt = new Date(task.created_at);
        document.getElementById('taskDetailCreated').innerHTML = createdAt.toLocaleDateString('id-ID', {
            day: 'numeric', month: 'long', year: 'numeric',
            hour: '2-digit', minute: '2-digit'
        });
        
        if (task.checklists && task.checklists.length > 0) {
            renderChecklists(task.checklists);
        } else {
            document.getElementById('checklistsContainer').innerHTML = '<div class="text-center text-gray-400 dark:text-gray-500 text-sm py-4">No checklists yet. Click "Add checklist" to start.</div>';
        }
        
        loadAttachments(taskId);
        loadComments(taskId);
        loadWatchersStatus();
        loadCustomFieldsForTask();
        loadRecurringSettings();
        loadTimerStatus();
        
        setTimeout(() => {
            initMentionAutocomplete();
        }, 100);
    })
    .catch(error => {
        console.error('Error loading task detail:', error);
        document.getElementById('taskDetailTitle').innerHTML = 'Error loading task';
        document.getElementById('taskDetailDescription').innerHTML = '<span class="text-red-500 dark:text-red-400">Failed to load task details. Please try again.</span>';
    });
}

function closeTaskDetailModal() {
    document.getElementById('taskDetailModal').classList.add('hidden');
    currentTaskIdForDetail = null;
    hideMentionPopup();
}

document.addEventListener('click', function(event) {
    const modal = document.getElementById('taskDetailModal');
    if (event.target === modal) closeTaskDetailModal();
});
</script>