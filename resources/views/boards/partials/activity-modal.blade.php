<!-- Activity History Modal -->
<div id="activityModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4 pb-2 border-b">
            <h3 class="text-lg font-semibold" style="color: #1e3a5f;">📋 Task Activity History</h3>
            <button onclick="closeActivityModal()" class="text-gray-500 hover:text-gray-700 text-2xl leading-none">&times;</button>
        </div>
        
        <div id="activityContent" class="space-y-3 max-h-96 overflow-y-auto pr-2">
            <div class="text-center text-gray-500 py-8">
                <div class="inline-block w-6 h-6 border-2 border-gray-300 border-t-blue-500 rounded-full animate-spin"></div>
                <p class="mt-2">Loading activity history...</p>
            </div>
        </div>
        
        <div class="mt-5 flex justify-end">
            <button onclick="closeActivityModal()" class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300 transition font-medium">Close</button>
        </div>
    </div>
</div>

<script>
let currentTaskId = null;

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function showTaskActivityV2(taskId) {
    console.log('=== showTaskActivityV2 called ===');
    console.log('Raw taskId received:', taskId);
    console.log('Type of taskId:', typeof taskId);
    
    let normalizedTaskId = null;
    
    if (taskId && typeof taskId === 'object') {
        if (taskId.id) normalizedTaskId = parseInt(taskId.id);
        else if (taskId.task_id) normalizedTaskId = parseInt(taskId.task_id);
        else {
            const str = JSON.stringify(taskId);
            const match = str.match(/\d+/);
            if (match) normalizedTaskId = parseInt(match[0]);
        }
    } else if (taskId !== null && taskId !== undefined && taskId !== 'null' && taskId !== 'undefined') {
        const parsed = parseInt(taskId);
        if (!isNaN(parsed)) normalizedTaskId = parsed;
    }
    
    if (!normalizedTaskId || normalizedTaskId <= 0) {
        const modal = document.getElementById('taskDetailModal');
        if (modal) {
            const attrId = modal.getAttribute('data-current-task-id');
            if (attrId && attrId !== 'null' && !isNaN(parseInt(attrId))) {
                normalizedTaskId = parseInt(attrId);
            }
        }
        if (!normalizedTaskId || normalizedTaskId <= 0) {
            console.error('❌ Invalid task ID:', taskId);
            alert('Error: Invalid task ID. Please refresh the page.');
            return;
        }
    }
    
    console.log('✅ Valid task ID:', normalizedTaskId);
    currentTaskId = normalizedTaskId;
    document.getElementById('activityModal').classList.remove('hidden');
    
    const container = document.getElementById('activityContent');
    if (container) {
        container.innerHTML = `
            <div class="text-center text-gray-500 py-8">
                <div class="inline-block w-6 h-6 border-2 border-gray-300 border-t-blue-500 rounded-full animate-spin"></div>
                <p class="mt-2">Loading activity history for task #${normalizedTaskId}...</p>
            </div>
        `;
    }
    
    fetch(`/tasks/${normalizedTaskId}/activities`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
        },
        credentials: 'same-origin'
    })
    .then(response => response.json())
    .then(activities => {
        if (!container) return;
        
        if (!activities || activities.length === 0) {
            container.innerHTML = `
                <div class="text-center text-gray-500 py-8">
                    <div class="text-4xl mb-2">📭</div>
                    <p>No activity yet for this task</p>
                    <p class="text-xs mt-1">Activities will appear when you create, move, or edit this task</p>
                </div>
            `;
            return;
        }
        
        let html = '';
        for (let i = 0; i < activities.length; i++) {
            const activity = activities[i];
            
            let icon = '📝';
            let bgColor = 'bg-gray-50';
            let actionText = '';
            
            if (activity.action === 'created') {
                icon = '✅';
                bgColor = 'bg-green-50';
                actionText = 'Created this task';
            } else if (activity.action === 'updated_title') {
                icon = '✏️';
                bgColor = 'bg-yellow-50';
                actionText = `Changed title from "${escapeHtml(activity.old_value || 'empty')}" to "${escapeHtml(activity.new_value || 'empty')}"`;
            } else if (activity.action === 'updated_description') {
                icon = '📄';
                bgColor = 'bg-yellow-50';
                actionText = 'Updated description';
            } else if (activity.action === 'updated_priority') {
                icon = '⭐';
                bgColor = 'bg-yellow-50';
                actionText = `Changed priority from "${escapeHtml(activity.old_value)}" to "${escapeHtml(activity.new_value)}"`;
            } else if (activity.action === 'updated_due_date') {
                icon = '📅';
                bgColor = 'bg-yellow-50';
                actionText = `Changed due date from "${escapeHtml(activity.old_value || 'not set')}" to "${escapeHtml(activity.new_value || 'not set')}"`;
            } else if (activity.action === 'moved') {
                icon = '🔄';
                bgColor = 'bg-blue-50';
                actionText = `Moved from "${escapeHtml(activity.old_value)}" to "${escapeHtml(activity.new_value)}"`;
            } else if (activity.action === 'deleted') {
                icon = '🗑️';
                bgColor = 'bg-red-50';
                actionText = `🗑️ Deleted task "${escapeHtml(activity.old_value)}" by ${escapeHtml(activity.user?.name || 'Admin')}`;
            } else if (activity.action === 'assigned_label') {
                icon = '🏷️';
                bgColor = 'bg-purple-50';
                actionText = `Added label "${escapeHtml(activity.new_value)}"`;
            } else if (activity.action === 'removed_label') {
                icon = '🏷️';
                bgColor = 'bg-orange-50';
                actionText = `Removed label "${escapeHtml(activity.old_value)}"`;
            } else if (activity.action === 'assigned') {
                icon = '👤';
                bgColor = 'bg-indigo-50';
                actionText = `Assigned to ${escapeHtml(activity.new_value)}`;
            } else {
                icon = '📝';
                bgColor = 'bg-gray-50';
                actionText = escapeHtml(activity.action);
            }
            
            const date = new Date(activity.created_at);
            const formattedDate = date.toLocaleDateString('id-ID', {
                day: 'numeric',
                month: 'long',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
            
            const userName = activity.user ? activity.user.name : (activity.user_id ? `User #${activity.user_id}` : 'System');
            
            html += `
                <div class="flex items-start space-x-3 p-3 ${bgColor} rounded-lg hover:shadow-sm transition">
                    <div class="text-2xl flex-shrink-0">${icon}</div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm text-gray-800 break-words">${actionText}</p>
                        <div class="flex justify-between items-center mt-1 flex-wrap gap-1">
                            <span class="text-xs text-gray-500">
                                👤 ${escapeHtml(userName)}
                            </span>
                            <span class="text-xs text-gray-400">
                                ${formattedDate}
                            </span>
                        </div>
                    </div>
                </div>
            `;
        }
        
        container.innerHTML = html;
    })
    .catch(error => {
        console.error('Error fetching activities:', error);
        if (container) {
            container.innerHTML = `
                <div class="text-center text-red-500 py-8">
                    <div class="text-5xl mb-3">⚠️</div>
                    <p class="text-lg font-semibold mb-2">Failed to load activity history</p>
                    <p class="text-sm mb-4">${escapeHtml(error.message)}</p>
                    <div class="flex justify-center gap-3">
                        <button onclick="closeActivityModal()" class="px-4 py-2 bg-gray-500 text-white rounded-lg text-sm hover:bg-gray-600 transition">
                            Close
                        </button>
                        <button onclick="showTaskActivityV2(${normalizedTaskId})" class="px-4 py-2 bg-blue-500 text-white rounded-lg text-sm hover:bg-blue-600 transition">
                            🔄 Retry
                        </button>
                    </div>
                </div>
            `;
        }
    });
}

function closeActivityModal() {
    document.getElementById('activityModal').classList.add('hidden');
    currentTaskId = null;
}

document.addEventListener('click', function(event) {
    const modal = document.getElementById('activityModal');
    if (event.target === modal) closeActivityModal();
});
</script>

<style>
.spinner {
    display: inline-block;
    width: 1.5rem;
    height: 1.5rem;
    border: 2px solid #e5e7eb;
    border-top-color: #3b82f6;
    border-radius: 50%;
    animation: spin 0.6s linear infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}
</style>