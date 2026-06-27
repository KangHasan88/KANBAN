<!-- Manage Labels Modal -->
<div id="labelsModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-6 border-0 w-full max-w-2xl shadow-xl rounded-xl bg-white">
        <div class="flex justify-between items-center mb-5">
            <h3 class="text-xl font-semibold" style="color: #1e3a5f;">🏷️ Manage Labels</h3>
            <button onclick="closeLabelsModal()" class="text-gray-400 hover:text-gray-600 text-2xl transition">&times;</button>
        </div>
        
        <!-- Create Label Form -->
        <div class="mb-6 p-5 bg-gradient-to-r from-gray-50 to-gray-100 rounded-xl">
            <h4 class="font-semibold mb-3 text-gray-700">Create New Label</h4>
            <form id="createLabelForm" onsubmit="createLabel(event)">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Label Name</label>
                        <input type="text" id="labelName" placeholder="e.g., Bug, Feature, Urgent" required 
                               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Color</label>
                        <div class="flex items-center gap-3">
                            <input type="color" id="labelColor" value="#3b82f6" class="w-12 h-10 rounded border cursor-pointer">
                            <span class="text-sm text-gray-500" id="labelColorHex">#3b82f6</span>
                        </div>
                    </div>
                </div>
                
                <!-- Color Presets -->
                <label class="block text-sm font-medium text-gray-600 mb-2">Quick Select Color</label>
                <div class="grid grid-cols-8 gap-2 mb-4">
                    <button type="button" onclick="setLabelColor('#ef4444')" class="w-10 h-10 rounded-full border-2 border-transparent hover:border-gray-400 transition shadow-sm transform hover:scale-105" style="background-color: #ef4444;" title="Red - Bug, Urgent"></button>
                    <button type="button" onclick="setLabelColor('#f97316')" class="w-10 h-10 rounded-full border-2 border-transparent hover:border-gray-400 transition shadow-sm transform hover:scale-105" style="background-color: #f97316;" title="Orange - Testing, Warning"></button>
                    <button type="button" onclick="setLabelColor('#eab308')" class="w-10 h-10 rounded-full border-2 border-transparent hover:border-gray-400 transition shadow-sm transform hover:scale-105" style="background-color: #eab308;" title="Yellow - Review, Pending"></button>
                    <button type="button" onclick="setLabelColor('#22c55e')" class="w-10 h-10 rounded-full border-2 border-transparent hover:border-gray-400 transition shadow-sm transform hover:scale-105" style="background-color: #22c55e;" title="Green - Feature, Done"></button>
                    <button type="button" onclick="setLabelColor('#3b82f6')" class="w-10 h-10 rounded-full border-2 border-transparent hover:border-gray-400 transition shadow-sm transform hover:scale-105" style="background-color: #3b82f6;" title="Blue - Enhancement, Task"></button>
                    <button type="button" onclick="setLabelColor('#a855f7')" class="w-10 h-10 rounded-full border-2 border-transparent hover:border-gray-400 transition shadow-sm transform hover:scale-105" style="background-color: #a855f7;" title="Purple - Design, Creative"></button>
                    <button type="button" onclick="setLabelColor('#6b7280')" class="w-10 h-10 rounded-full border-2 border-transparent hover:border-gray-400 transition shadow-sm transform hover:scale-105" style="background-color: #6b7280;" title="Gray - Documentation, Backlog"></button>
                    <button type="button" onclick="setLabelColor('#1e3a5f')" class="w-10 h-10 rounded-full border-2 border-transparent hover:border-gray-400 transition shadow-sm transform hover:scale-105" style="background-color: #1e3a5f;" title="Navy - Default"></button>
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-600 mb-1">Description (optional)</label>
                    <textarea id="labelDescription" placeholder="Describe what this label is for..." rows="2" 
                              class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition"></textarea>
                </div>
                
                <div class="flex justify-end">
                    <button type="submit" class="btn-accent px-5 py-2 rounded-lg font-semibold flex items-center gap-2">
                        <span>+</span> Create Label
                    </button>
                </div>
            </form>
        </div>
        
        <!-- Labels List -->
        <div>
            <h4 class="font-semibold mb-3 text-gray-700 flex items-center gap-2">
                <span>📋</span> Existing Labels
            </h4>
            <div id="labelsList" class="space-y-2 max-h-96 overflow-y-auto pr-2">
                <div class="text-center text-gray-500 py-8">
                    <div class="inline-block w-6 h-6 border-2 border-gray-300 border-t-green-500 rounded-full animate-spin"></div>
                    <p class="mt-2">Loading labels...</p>
                </div>
            </div>
        </div>
        
        <div class="mt-6 flex justify-end border-t pt-4">
            <button onclick="closeLabelsModal()" class="px-5 py-2 bg-gray-200 rounded-lg hover:bg-gray-300 transition font-medium">Close</button>
        </div>
    </div>
</div>

<!-- Assign Labels to Task Modal -->
<div id="assignLabelModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-6 border-0 w-96 shadow-xl rounded-xl bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold" style="color: #1e3a5f;">🏷️ Assign Labels</h3>
            <button onclick="closeAssignLabelModal()" class="text-gray-400 hover:text-gray-600 text-2xl transition">&times;</button>
        </div>
        
        <div id="availableLabels" class="space-y-2 max-h-96 overflow-y-auto">
            <div class="text-center text-gray-500 py-8">
                <div class="inline-block w-6 h-6 border-2 border-gray-300 border-t-green-500 rounded-full animate-spin"></div>
                <p class="mt-2">Loading...</p>
            </div>
        </div>
        
        <div class="mt-6 flex justify-end">
            <button onclick="closeAssignLabelModal()" class="px-5 py-2 bg-gray-200 rounded-lg hover:bg-gray-300 transition font-medium">Close</button>
        </div>
    </div>
</div>

<script>
// ==============================================
// LABELS SYSTEM - FULL VERSION WITH COLOR PRESET
// ==============================================

let currentTaskForLabels = null;
let boardLabels = [];

// ==============================================
// COLOR PRESET FOR LABELS
// ==============================================

function setLabelColor(color) {
    const colorInput = document.getElementById('labelColor');
    const colorHexSpan = document.getElementById('labelColorHex');
    if (colorInput) {
        colorInput.value = color;
        if (colorHexSpan) colorHexSpan.textContent = color;
    }
}

// Update color hex display when color picker changes
document.addEventListener('DOMContentLoaded', function() {
    const labelColorPicker = document.getElementById('labelColor');
    const colorHexSpan = document.getElementById('labelColorHex');
    if (labelColorPicker && colorHexSpan) {
        labelColorPicker.addEventListener('input', function(e) {
            colorHexSpan.textContent = e.target.value;
        });
    }
});

// ==============================================
// LOAD LABELS
// ==============================================

function loadLabels() {
    const boardId = {{ $board->id }};
    
    console.log('Loading labels for board:', boardId);
    
    const container = document.getElementById('labelsList');
    if (container) {
        container.innerHTML = '<div class="text-center text-gray-500 py-8"><div class="inline-block w-6 h-6 border-2 border-gray-300 border-t-green-500 rounded-full animate-spin"></div><p class="mt-2">Loading labels...</p></div>';
    }
    
    fetch(`/boards/${boardId}/labels`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json'
        },
        credentials: 'same-origin'
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('Labels data received:', data);
        
        if (data && typeof data === 'object') {
            if (data.error) {
                renderLabelsError(data.error);
                return;
            }
            let labelsArray = Array.isArray(data) ? data : [];
            renderLabelsList(labelsArray);
        } else {
            renderLabelsList([]);
        }
    })
    .catch(error => {
        console.error('Error loading labels:', error);
        renderLabelsError(error.message);
    });
}

function renderLabelsList(labels) {
    const container = document.getElementById('labelsList');
    if (!container) return;
    
    if (!labels || !Array.isArray(labels) || labels.length === 0) {
        container.innerHTML = '<div class="text-center text-gray-500 py-8">✨ No labels yet. Create your first label above!</div>';
        return;
    }
    
    let html = '';
    labels.forEach(label => {
        const safeName = escapeHtml(label.name || 'Unnamed');
        const safeColor = label.color || '#3b82f6';
        const safeDescription = escapeHtml(label.description || 'No description');
        
        html += `
            <div class="flex justify-between items-center p-4 bg-gray-50 rounded-xl hover:shadow-md transition-all group" data-label-id="${label.id}">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-full shadow-sm" style="background-color: ${safeColor}; border: 2px solid white; box-shadow: 0 1px 3px rgba(0,0,0,0.1);"></div>
                    <div>
                        <div class="flex items-center gap-2">
                            <p class="font-semibold text-gray-800">${safeName}</p>
                            <span class="text-xs px-2 py-0.5 rounded-full" style="background-color: ${safeColor}20; color: ${safeColor};">${safeColor}</span>
                        </div>
                        <p class="text-sm text-gray-500">${safeDescription}</p>
                    </div>
                </div>
                <div class="flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                    <button onclick="editLabel(${label.id}, '${safeName.replace(/'/g, "\\'")}', '${safeColor}', '${safeDescription.replace(/'/g, "\\'")}')" 
                            class="text-blue-600 hover:text-blue-800 p-1 rounded hover:bg-blue-100 transition" title="Edit label">
                        ✏️
                    </button>
                    <button onclick="deleteLabel(${label.id})" 
                            class="text-red-600 hover:text-red-800 p-1 rounded hover:bg-red-100 transition" title="Delete label">
                        🗑️
                    </button>
                </div>
            </div>
        `;
    });
    
    container.innerHTML = html;
}

function renderLabelsError(errorMessage) {
    const container = document.getElementById('labelsList');
    if (container) {
        container.innerHTML = `
            <div class="text-center text-red-500 py-8">
                <div class="text-lg mb-2">⚠️ Error Loading Labels</div>
                <div class="text-sm">${escapeHtml(errorMessage)}</div>
                <button onclick="loadLabels()" class="mt-4 px-4 py-2 btn-accent rounded-lg text-sm">Retry</button>
            </div>
        `;
    }
}

// ==============================================
// CREATE LABEL
// ==============================================

function createLabel(event) {
    event.preventDefault();
    event.stopPropagation();
    
    const boardId = {{ $board->id }};
    const name = document.getElementById('labelName')?.value?.trim();
    const color = document.getElementById('labelColor')?.value || '#3b82f6';
    const description = document.getElementById('labelDescription')?.value?.trim() || '';
    
    if (!name) {
        showNotification('Please enter a label name', 'error');
        return;
    }
    
    console.log('Creating label:', { name, color, description });
    
    const submitBtn = document.querySelector('#createLabelForm button[type="submit"]');
    const originalText = submitBtn?.textContent;
    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<div class="spinner w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></div> Creating...';
    }
    
    fetch(`/boards/${boardId}/labels`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        credentials: 'same-origin',
        body: JSON.stringify({ name, color, description })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('✓ Label created successfully!', 'success');
            document.getElementById('createLabelForm').reset();
            document.getElementById('labelColor').value = '#3b82f6';
            document.getElementById('labelColorHex').textContent = '#3b82f6';
            loadLabels();
        } else {
            showNotification('Failed to create label: ' + (data.error || 'Unknown error'), 'error');
        }
    })
    .catch(error => {
        console.error('Error creating label:', error);
        showNotification('Error creating label: ' + error.message, 'error');
    })
    .finally(() => {
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    });
}

// ==============================================
// EDIT LABEL
// ==============================================

function editLabel(id, currentName, currentColor, currentDescription) {
    // Create custom prompt with color picker
    const modalHtml = `
        <div id="editLabelOverlay" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-[60]">
            <div class="bg-white rounded-xl p-6 w-96 shadow-2xl">
                <h3 class="text-lg font-semibold mb-4" style="color: #1e3a5f;">Edit Label</h3>
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-600 mb-1">Label Name</label>
                    <input type="text" id="editLabelName" value="${escapeHtml(currentName)}" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                </div>
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-600 mb-1">Color</label>
                    <div class="flex items-center gap-3">
                        <input type="color" id="editLabelColor" value="${currentColor}" class="w-12 h-10 rounded border cursor-pointer">
                        <span id="editLabelColorHex" class="text-sm text-gray-500">${currentColor}</span>
                    </div>
                </div>
                <div class="grid grid-cols-8 gap-2 mb-4">
                    <button type="button" onclick="setEditLabelColor('#ef4444')" class="w-8 h-8 rounded-full border-2 border-transparent hover:border-gray-400 transition" style="background-color: #ef4444;"></button>
                    <button type="button" onclick="setEditLabelColor('#f97316')" class="w-8 h-8 rounded-full border-2 border-transparent hover:border-gray-400 transition" style="background-color: #f97316;"></button>
                    <button type="button" onclick="setEditLabelColor('#eab308')" class="w-8 h-8 rounded-full border-2 border-transparent hover:border-gray-400 transition" style="background-color: #eab308;"></button>
                    <button type="button" onclick="setEditLabelColor('#22c55e')" class="w-8 h-8 rounded-full border-2 border-transparent hover:border-gray-400 transition" style="background-color: #22c55e;"></button>
                    <button type="button" onclick="setEditLabelColor('#3b82f6')" class="w-8 h-8 rounded-full border-2 border-transparent hover:border-gray-400 transition" style="background-color: #3b82f6;"></button>
                    <button type="button" onclick="setEditLabelColor('#a855f7')" class="w-8 h-8 rounded-full border-2 border-transparent hover:border-gray-400 transition" style="background-color: #a855f7;"></button>
                    <button type="button" onclick="setEditLabelColor('#6b7280')" class="w-8 h-8 rounded-full border-2 border-transparent hover:border-gray-400 transition" style="background-color: #6b7280;"></button>
                    <button type="button" onclick="setEditLabelColor('#1e3a5f')" class="w-8 h-8 rounded-full border-2 border-transparent hover:border-gray-400 transition" style="background-color: #1e3a5f;"></button>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-600 mb-1">Description</label>
                    <textarea id="editLabelDescription" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2">${escapeHtml(currentDescription)}</textarea>
                </div>
                <div class="flex justify-end gap-2">
                    <button onclick="closeEditLabelModal()" class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300 transition">Cancel</button>
                    <button onclick="saveEditLabel(${id})" class="px-4 py-2 btn-accent rounded-lg">Save</button>
                </div>
            </div>
        </div>
    `;
    
    // Remove existing overlay if any
    const existingOverlay = document.getElementById('editLabelOverlay');
    if (existingOverlay) existingOverlay.remove();
    
    // Add overlay to body
    document.body.insertAdjacentHTML('beforeend', modalHtml);
    
    // Add color preview update
    const editColorPicker = document.getElementById('editLabelColor');
    const editColorHex = document.getElementById('editLabelColorHex');
    if (editColorPicker && editColorHex) {
        editColorPicker.addEventListener('input', function(e) {
            editColorHex.textContent = e.target.value;
        });
    }
}

function setEditLabelColor(color) {
    const colorInput = document.getElementById('editLabelColor');
    const colorHex = document.getElementById('editLabelColorHex');
    if (colorInput) {
        colorInput.value = color;
        if (colorHex) colorHex.textContent = color;
    }
}

function closeEditLabelModal() {
    const overlay = document.getElementById('editLabelOverlay');
    if (overlay) overlay.remove();
}

function saveEditLabel(id) {
    const name = document.getElementById('editLabelName')?.value?.trim();
    const color = document.getElementById('editLabelColor')?.value;
    const description = document.getElementById('editLabelDescription')?.value?.trim() || '';
    
    if (!name) {
        showNotification('Label name is required', 'error');
        return;
    }
    
    fetch(`/labels/${id}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ name, color, description })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('✓ Label updated!', 'success');
            closeEditLabelModal();
            loadLabels();
        } else {
            showNotification('Failed to update label', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error updating label', 'error');
    });
}

// ==============================================
// DELETE LABEL
// ==============================================

function deleteLabel(id) {
    if (!confirm('Are you sure you want to delete this label? It will be removed from all tasks.')) return;
    
    fetch(`/labels/${id}`, {
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
            showNotification('✓ Label deleted!', 'success');
            loadLabels();
        } else {
            showNotification('Failed to delete label', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error deleting label', 'error');
    });
}

// ==============================================
// ASSIGN / REMOVE LABEL FROM TASK
// ==============================================

function openAssignLabelModal(taskId) {
    currentTaskForLabels = taskId;
    document.getElementById('assignLabelModal').classList.remove('hidden');
    
    const boardId = {{ $board->id }};
    
    fetch(`/boards/${boardId}/labels`, {
        method: 'GET',
        headers: { 
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        credentials: 'same-origin'
    })
    .then(response => response.json())
    .then(labels => {
        renderAvailableLabels(labels, taskId);
    })
    .catch(error => {
        console.error('Error loading labels:', error);
        document.getElementById('availableLabels').innerHTML = '<div class="text-center text-red-500 py-4">Error loading labels</div>';
    });
}

function renderAvailableLabels(labels, taskId) {
    const container = document.getElementById('availableLabels');
    
    if (!labels || labels.length === 0) {
        container.innerHTML = '<div class="text-center text-gray-500 py-4">No labels available. Create some labels first!</div>';
        return;
    }
    
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
        const assignedLabelIds = task.labels ? task.labels.map(l => l.id) : [];
        
        let html = '';
        labels.forEach(label => {
            const isAssigned = assignedLabelIds.includes(label.id);
            html += `
                <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg mb-2 hover:bg-gray-100 transition">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full shadow-sm" style="background-color: ${label.color}; border: 2px solid white;"></div>
                        <div>
                            <p class="font-medium text-gray-800">${escapeHtml(label.name)}</p>
                            <p class="text-xs text-gray-500">${escapeHtml(label.description || 'No description')}</p>
                        </div>
                    </div>
                    <button onclick="${isAssigned ? `removeLabelFromTask(${label.id})` : `assignLabelToTask(${label.id})`}" 
                            class="${isAssigned ? 'text-red-600 hover:text-red-800' : 'text-green-600 hover:text-green-800'} px-3 py-1 rounded-lg font-medium transition">
                        ${isAssigned ? 'Remove' : 'Assign'}
                    </button>
                </div>
            `;
        });
        
        container.innerHTML = html;
    })
    .catch(error => {
        console.error('Error:', error);
        container.innerHTML = '<div class="text-center text-red-500 py-4">Error loading task data</div>';
    });
}

function assignLabelToTask(labelId) {
    if (!currentTaskForLabels) return;
    
    fetch(`/tasks/${currentTaskForLabels}/labels`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ label_id: labelId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('✓ Label assigned!', 'success');
            setTimeout(() => location.reload(), 500);
        } else {
            showNotification('Failed to assign label', 'error');
        }
    });
}

function removeLabelFromTask(labelId) {
    if (!currentTaskForLabels) return;
    
    fetch(`/tasks/${currentTaskForLabels}/labels/${labelId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('✓ Label removed!', 'success');
            setTimeout(() => location.reload(), 500);
        } else {
            showNotification('Failed to remove label', 'error');
        }
    });
}

function removeLabelFromTaskCard(taskId, labelId) {
    fetch(`/tasks/${taskId}/labels/${labelId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) location.reload();
    });
}

// ==============================================
// MODAL CONTROLS
// ==============================================

function openLabelsModal() {
    const modal = document.getElementById('labelsModal');
    if (modal) {
        modal.classList.remove('hidden');
        loadLabels();
    }
}

function closeLabelsModal() {
    const modal = document.getElementById('labelsModal');
    if (modal) modal.classList.add('hidden');
}

function closeAssignLabelModal() {
    const modal = document.getElementById('assignLabelModal');
    if (modal) modal.classList.add('hidden');
    currentTaskForLabels = null;
}

// ==============================================
// UTILITY FUNCTIONS
// ==============================================

function showNotification(message, type = 'info') {
    const existing = document.querySelectorAll('.kanban-notification');
    existing.forEach(n => n.remove());
    
    const colors = {
        success: 'bg-green-500',
        error: 'bg-red-500',
        info: 'bg-blue-500'
    };
    
    const notification = document.createElement('div');
    notification.className = `kanban-notification fixed bottom-4 right-4 z-50 px-4 py-2 rounded-lg shadow-lg text-white ${colors[type]} transition-opacity duration-300`;
    notification.textContent = message;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.opacity = '0';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Close modal when clicking outside
document.addEventListener('click', function(event) {
    const labelsModal = document.getElementById('labelsModal');
    const assignModal = document.getElementById('assignLabelModal');
    
    if (event.target === labelsModal) closeLabelsModal();
    if (event.target === assignModal) closeAssignLabelModal();
});

console.log('Labels modal script loaded successfully');
</script>