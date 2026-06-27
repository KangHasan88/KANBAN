<!-- Custom Fields Modal -->
<div id="customFieldsModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-10 mx-auto p-6 border-0 w-full max-w-2xl shadow-xl rounded-xl bg-white">
        <div class="flex justify-between items-center mb-5 pb-3 border-b border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-600 to-indigo-500 flex items-center justify-center shadow-md">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-800">Custom Fields</h3>
                    <p class="text-sm text-gray-500">Add extra fields to your tasks</p>
                </div>
            </div>
            <button onclick="closeCustomFieldsModal()" class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 p-2 rounded-full transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <!-- Form Create Field -->
        <div class="mb-6 p-4 bg-gradient-to-r from-gray-50 to-gray-100 rounded-xl">
            <h4 class="font-semibold mb-3 text-gray-700">➕ Create New Field</h4>
            <form id="createCustomFieldForm" onsubmit="createCustomField(event)">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Field Name (API)</label>
                        <input type="text" id="cfName" placeholder="e.g., estimated_hours" required 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                        <p class="text-xs text-gray-400 mt-1">Unique identifier, lowercase and underscores</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Display Label</label>
                        <input type="text" id="cfLabel" placeholder="e.g., Estimated Hours" required 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Field Type</label>
                        <select id="cfType" onchange="toggleOptionsField()" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                            <option value="text">📝 Text</option>
                            <option value="number">🔢 Number</option>
                            <option value="date">📅 Date</option>
                            <option value="dropdown">📋 Dropdown</option>
                            <option value="checkbox">☑️ Checkbox</option>
                            <option value="textarea">📄 Textarea</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Options (for Dropdown)</label>
                        <div id="optionsField" class="hidden">
                            <textarea id="cfOptions" placeholder="Option 1&#10;Option 2&#10;Option 3" rows="2" 
                                      class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500"></textarea>
                            <p class="text-xs text-gray-400 mt-1">One option per line</p>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-4 mb-4">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" id="cfRequired" class="w-4 h-4 rounded border-gray-300 text-indigo-600">
                        <span class="text-sm text-gray-600">Required field</span>
                    </label>
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition text-sm font-medium">
                        + Create Field
                    </button>
                </div>
            </form>
        </div>
        
        <!-- List Existing Fields -->
        <div>
            <h4 class="font-semibold mb-3 text-gray-700 flex items-center gap-2">
                <span>📋</span> Existing Fields
                <span id="fieldsCount" class="text-xs bg-gray-200 rounded-full px-2 py-0.5">0</span>
            </h4>
            <div id="customFieldsList" class="space-y-2 max-h-96 overflow-y-auto pr-2">
                <div class="text-center text-gray-500 py-8">
                    <div class="inline-block w-6 h-6 border-2 border-gray-300 border-t-indigo-500 rounded-full animate-spin"></div>
                    <p class="mt-2">Loading fields...</p>
                </div>
            </div>
        </div>
        
        <div class="mt-6 flex justify-end gap-3 border-t pt-4">
            <button onclick="closeCustomFieldsModal()" class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300 transition">Close</button>
        </div>
    </div>
</div>

<script>
let currentBoardId = {{ $board->id }};

function openCustomFieldsModal() {
    document.getElementById('customFieldsModal').classList.remove('hidden');
    loadCustomFields();
}

function closeCustomFieldsModal() {
    document.getElementById('customFieldsModal').classList.add('hidden');
}

function toggleOptionsField() {
    const type = document.getElementById('cfType').value;
    const optionsDiv = document.getElementById('optionsField');
    optionsDiv.classList.toggle('hidden', type !== 'dropdown');
}

function loadCustomFields() {
    const container = document.getElementById('customFieldsList');
    const countSpan = document.getElementById('fieldsCount');
    
    fetch(`/boards/${currentBoardId}/custom-fields`, {
        headers: { 'Accept': 'application/json' }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.fields) {
            const fields = data.fields;
            countSpan.textContent = fields.length;
            
            if (fields.length === 0) {
                container.innerHTML = '<div class="text-center text-gray-400 py-8">No custom fields yet. Create one above.</div>';
                return;
            }
            
            let html = '';
            fields.forEach((field, index) => {
                const typeIcon = {
                    'text': '📝', 'number': '🔢', 'date': '📅', 
                    'dropdown': '📋', 'checkbox': '☑️', 'textarea': '📄'
                }[field.type] || '🏷️';
                
                html += `
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-xl group" data-field-id="${field.id}">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="drag-handle cursor-move text-gray-400 hover:text-gray-600 px-1">⋮⋮</span>
                                <span class="font-medium text-gray-800">${escapeHtml(field.label)}</span>
                                <span class="text-xs px-2 py-0.5 rounded-full bg-gray-200">${typeIcon} ${field.type}</span>
                                ${field.required ? '<span class="text-xs px-2 py-0.5 rounded-full bg-red-100 text-red-600">Required</span>' : ''}
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Name: ${escapeHtml(field.name)}</p>
                            ${field.options ? `<p class="text-xs text-gray-400 mt-1">Options: ${field.options.join(', ')}</p>` : ''}
                        </div>
                        <div class="flex gap-2 opacity-0 group-hover:opacity-100 transition">
                            <button onclick="editCustomField(${field.id})" class="text-blue-500 hover:text-blue-700 p-1 rounded">✏️</button>
                            <button onclick="deleteCustomField(${field.id})" class="text-red-500 hover:text-red-700 p-1 rounded">🗑️</button>
                        </div>
                    </div>
                `;
            });
            container.innerHTML = html;
            initFieldSorting();
        }
    })
    .catch(error => {
        console.error('Error loading custom fields:', error);
        container.innerHTML = '<div class="text-center text-red-500 py-8">Error loading fields</div>';
    });
}

function initFieldSorting() {
    const container = document.getElementById('customFieldsList');
    if (!container) return;
    
    let dragSrc = null;
    
    container.querySelectorAll('[data-field-id]').forEach(item => {
        item.setAttribute('draggable', 'true');
        
        item.addEventListener('dragstart', (e) => {
            dragSrc = item;
            e.dataTransfer.effectAllowed = 'move';
            item.style.opacity = '0.5';
        });
        
        item.addEventListener('dragend', (e) => {
            if (dragSrc) dragSrc.style.opacity = '';
            dragSrc = null;
        });
        
        item.addEventListener('dragover', (e) => {
            e.preventDefault();
            e.dataTransfer.dropEffect = 'move';
        });
        
        item.addEventListener('drop', (e) => {
            e.preventDefault();
            if (!dragSrc || dragSrc === item) return;
            
            const parent = container;
            const items = [...parent.children];
            const srcIndex = items.indexOf(dragSrc);
            const destIndex = items.indexOf(item);
            
            if (srcIndex < destIndex) {
                item.insertAdjacentElement('afterend', dragSrc);
            } else {
                item.insertAdjacentElement('beforebegin', dragSrc);
            }
            
            dragSrc.style.opacity = '';
            saveFieldOrder();
        });
    });
}

function saveFieldOrder() {
    const items = document.querySelectorAll('#customFieldsList [data-field-id]');
    const fields = [];
    
    items.forEach((item, index) => {
        fields.push({
            id: parseInt(item.dataset.fieldId),
            order: index
        });
    });
    
    fetch(`/boards/${currentBoardId}/custom-fields/reorder`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ fields: fields })
    })
    .catch(error => console.error('Error saving order:', error));
}

function createCustomField(event) {
    event.preventDefault();
    
    const name = document.getElementById('cfName').value.trim();
    const label = document.getElementById('cfLabel').value.trim();
    const type = document.getElementById('cfType').value;
    const required = document.getElementById('cfRequired').checked;
    let options = null;
    
    if (type === 'dropdown') {
        const optionsText = document.getElementById('cfOptions').value;
        options = optionsText.split('\n').filter(opt => opt.trim());
    }
    
    if (!name || !label) {
        showNotification('Please fill all required fields', 'error');
        return;
    }
    
    if (!/^[a-z][a-z0-9_]*$/.test(name)) {
        showNotification('Field name must be lowercase with underscores only', 'error');
        return;
    }
    
    showNotification('Creating field...', 'info');
    
    fetch(`/boards/${currentBoardId}/custom-fields`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            name: name,
            label: label,
            type: type,
            options: options,
            required: required
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('✓ Field created!', 'success');
            document.getElementById('createCustomFieldForm').reset();
            document.getElementById('optionsField').classList.add('hidden');
            loadCustomFields();
        } else {
            showNotification('Failed: ' + (data.error || 'Unknown error'), 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error creating field', 'error');
    });
}

// ==============================================
// EDIT CUSTOM FIELD - LENGKAP DENGAN DROPDOWN OPTIONS
// ==============================================

function editCustomField(fieldId) {
    // Ambil data field dari server
    fetch(`/boards/${currentBoardId}/custom-fields`, {
        headers: { 'Accept': 'application/json' }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.fields) {
            const field = data.fields.find(f => f.id === fieldId);
            if (!field) {
                showNotification('Field not found', 'error');
                return;
            }
            
            // Buat HTML untuk modal edit sesuai tipe field
            let optionsHtml = '';
            if (field.type === 'dropdown') {
                const currentOptions = field.options || [];
                optionsHtml = `
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-600 mb-1">Dropdown Options</label>
                        <div id="editOptionsList" class="space-y-2 mb-2">
                            ${currentOptions.map((opt, idx) => `
                                <div class="flex items-center gap-2">
                                    <input type="text" value="${escapeHtml(opt)}" class="option-input flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm" data-index="${idx}">
                                    <button type="button" onclick="removeEditOption(this)" class="text-red-500 hover:text-red-700 p-1">🗑️</button>
                                </div>
                            `).join('')}
                        </div>
                        <button type="button" onclick="addEditOption()" class="text-sm text-indigo-500 hover:text-indigo-700">+ Add Option</button>
                    </div>
                `;
            }
            
            const modalHtml = `
                <div id="editCustomFieldDetailModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-[70]">
                    <div class="relative top-20 mx-auto p-6 border-0 w-full max-w-md shadow-xl rounded-xl bg-white">
                        <div class="flex justify-between items-center mb-4 pb-3 border-b">
                            <h3 class="text-lg font-bold text-gray-800">Edit Custom Field</h3>
                            <button onclick="closeEditCustomFieldDetailModal()" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
                        </div>
                        <form id="editCustomFieldDetailForm" onsubmit="saveCustomFieldDetail(event, ${field.id})">
                            <div class="space-y-4 mb-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-600 mb-1">Field Name (API)</label>
                                    <input type="text" id="editFieldName" value="${escapeHtml(field.name)}" required 
                                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                                    <p class="text-xs text-gray-400 mt-1">Unique identifier, lowercase and underscores</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-600 mb-1">Display Label</label>
                                    <input type="text" id="editFieldLabel" value="${escapeHtml(field.label)}" required 
                                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-600 mb-1">Field Type</label>
                                    <select id="editFieldType" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500" ${field.type === 'dropdown' ? '' : 'disabled'}>
                                        <option value="text" ${field.type === 'text' ? 'selected' : ''}>📝 Text</option>
                                        <option value="number" ${field.type === 'number' ? 'selected' : ''}>🔢 Number</option>
                                        <option value="date" ${field.type === 'date' ? 'selected' : ''}>📅 Date</option>
                                        <option value="dropdown" ${field.type === 'dropdown' ? 'selected' : ''}>📋 Dropdown</option>
                                        <option value="checkbox" ${field.type === 'checkbox' ? 'selected' : ''}>☑️ Checkbox</option>
                                        <option value="textarea" ${field.type === 'textarea' ? 'selected' : ''}>📄 Textarea</option>
                                    </select>
                                </div>
                                ${optionsHtml}
                                <div class="flex items-center gap-4">
                                    <label class="flex items-center gap-2">
                                        <input type="checkbox" id="editFieldRequired" ${field.required ? 'checked' : ''} class="w-4 h-4 rounded border-gray-300 text-indigo-600">
                                        <span class="text-sm text-gray-600">Required field</span>
                                    </label>
                                </div>
                            </div>
                            <div class="flex justify-end gap-3 pt-3 border-t">
                                <button type="button" onclick="closeEditCustomFieldDetailModal()" class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300 transition">Cancel</button>
                                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            `;
            
            // Hapus modal lama jika ada
            const existingModal = document.getElementById('editCustomFieldDetailModal');
            if (existingModal) existingModal.remove();
            
            document.body.insertAdjacentHTML('beforeend', modalHtml);
        }
    })
    .catch(error => {
        console.error('Error fetching field:', error);
        showNotification('Error loading field data', 'error');
    });
}

// Fungsi untuk menambah option di edit modal
function addEditOption() {
    const container = document.getElementById('editOptionsList');
    const newDiv = document.createElement('div');
    newDiv.className = 'flex items-center gap-2';
    newDiv.innerHTML = `
        <input type="text" class="option-input flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm" placeholder="New option">
        <button type="button" onclick="removeEditOption(this)" class="text-red-500 hover:text-red-700 p-1">🗑️</button>
    `;
    container.appendChild(newDiv);
}

// Fungsi untuk menghapus option di edit modal
function removeEditOption(btn) {
    const div = btn.closest('.flex');
    if (div) div.remove();
}

// Fungsi untuk menyimpan edit custom field
function saveCustomFieldDetail(event, fieldId) {
    event.preventDefault();
    
    const name = document.getElementById('editFieldName').value.trim();
    const label = document.getElementById('editFieldLabel').value.trim();
    const type = document.getElementById('editFieldType').value;
    const required = document.getElementById('editFieldRequired').checked;
    let options = null;
    
    if (type === 'dropdown') {
        const optionInputs = document.querySelectorAll('#editOptionsList .option-input');
        options = Array.from(optionInputs).map(input => input.value.trim()).filter(opt => opt);
    }
    
    if (!name || !label) {
        showNotification('Please fill all required fields', 'error');
        return;
    }
    
    if (!/^[a-z][a-z0-9_]*$/.test(name)) {
        showNotification('Field name must be lowercase with underscores only', 'error');
        return;
    }
    
    showNotification('Updating field...', 'info');
    
    fetch(`/custom-fields/${fieldId}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            name: name,
            label: label,
            type: type,
            options: options,
            required: required
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('✓ Field updated!', 'success');
            closeEditCustomFieldDetailModal();
            loadCustomFields(); // Refresh daftar fields
        } else {
            showNotification('Failed: ' + (data.error || 'Unknown error'), 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error updating field', 'error');
    });
}

function closeEditCustomFieldDetailModal() {
    const modal = document.getElementById('editCustomFieldDetailModal');
    if (modal) modal.remove();
}

function deleteCustomField(fieldId) {
    if (!confirm('Delete this custom field? All task values will be lost.')) return;
    
    fetch(`/custom-fields/${fieldId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('✓ Field deleted!', 'success');
            loadCustomFields();
        }
    });
}

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = `fixed bottom-4 right-4 z-50 px-4 py-2 rounded-lg shadow-lg text-white ${type === 'success' ? 'bg-green-500' : type === 'error' ? 'bg-red-500' : 'bg-blue-500'}`;
    notification.textContent = message;
    document.body.appendChild(notification);
    setTimeout(() => notification.remove(), 3000);
}
</script>