<script>
    // ==============================================
    // KANBAN BOARD SCRIPTS - FULL VERSION
    // WITH MULTIPLE ASSIGNEE & ARCHIVE
    // ==============================================

    // --- Global State (DEKLARASI SEKALI SAJA) ---
    let isSubmitting = false;
    let isDragging = false;
    let isResizing = false;
    let currentList = null;
    let startX = 0;
    let startWidth = 0;
    let compactMode = localStorage.getItem('kanban_compact_mode') === 'true';

    // ==============================================
    // HELPER FUNCTIONS
    // ==============================================

    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function showNotification(message, type = 'info') {
        const existingNotifications = document.querySelectorAll('.kanban-notification');
        existingNotifications.forEach(notif => notif.remove());
        
        const colors = { success: 'bg-green-500', error: 'bg-red-500', info: 'bg-blue-500' };
        const notification = document.createElement('div');
        notification.className = `kanban-notification fixed bottom-4 right-4 ${colors[type]} text-white px-4 py-2 rounded shadow-lg z-50 transition-opacity duration-300`;
        notification.innerHTML = message;
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.style.opacity = '0';
            setTimeout(() => notification.remove(), 300);
        }, 2000);
    }

    // ==============================================
    // COLOR PRESET FUNCTIONS
    // ==============================================

    function updateAddColorPreview(color) {
        const preview = document.getElementById('addColorPreview');
        if (preview) {
            preview.style.backgroundColor = color;
            const r = parseInt(color.slice(1,3), 16);
            const g = parseInt(color.slice(3,5), 16);
            const b = parseInt(color.slice(5,7), 16);
            const brightness = (r * 299 + g * 587 + b * 114) / 1000;
            preview.style.color = brightness > 128 ? '#1f2937' : 'white';
        }
    }

    function updateColorPreview(color) {
        const preview = document.getElementById('colorPreview');
        if (preview) {
            preview.style.backgroundColor = color;
            const r = parseInt(color.slice(1,3), 16);
            const g = parseInt(color.slice(3,5), 16);
            const b = parseInt(color.slice(5,7), 16);
            const brightness = (r * 299 + g * 587 + b * 114) / 1000;
            preview.style.color = brightness > 128 ? '#1f2937' : 'white';
        }
    }

    function setAddListColor(color) {
        const colorInput = document.getElementById('listColor');
        if (colorInput) {
            colorInput.value = color;
            updateAddColorPreview(color);
            colorInput.style.backgroundColor = color;
            const r = parseInt(color.slice(1,3), 16);
            const g = parseInt(color.slice(3,5), 16);
            const b = parseInt(color.slice(5,7), 16);
            const brightness = (r * 299 + g * 587 + b * 114) / 1000;
            colorInput.style.color = brightness > 128 ? '#000' : '#fff';
        }
    }

    function setEditListColor(color) {
        const colorInput = document.getElementById('editListColor');
        if (colorInput) {
            colorInput.value = color;
            updateColorPreview(color);
            colorInput.style.backgroundColor = color;
            const r = parseInt(color.slice(1,3), 16);
            const g = parseInt(color.slice(3,5), 16);
            const b = parseInt(color.slice(5,7), 16);
            const brightness = (r * 299 + g * 587 + b * 114) / 1000;
            colorInput.style.color = brightness > 128 ? '#000' : '#fff';
        }
    }

    function updateListHeaderColor(listId, color) {
        const listElement = document.querySelector(`.kanban-list[data-list-id="${listId}"]`);
        if (listElement) {
            const header = listElement.querySelector('.list-header');
            if (header) {
                header.style.backgroundColor = color;
                const r = parseInt(color.slice(1,3), 16);
                const g = parseInt(color.slice(3,5), 16);
                const b = parseInt(color.slice(5,7), 16);
                const brightness = (r * 299 + g * 587 + b * 114) / 1000;
                header.style.color = brightness > 128 ? '#1f2937' : 'white';
                const buttons = header.querySelectorAll('button');
                buttons.forEach(btn => { btn.style.color = brightness > 128 ? '#1f2937' : 'white'; });
                const badge = header.querySelector('span');
                if (badge) badge.style.backgroundColor = 'rgba(0,0,0,0.15)';
            }
        }
    }

    // ==============================================
    // DRAG AND DROP (SORTABLE)
    // ==============================================

    document.addEventListener('DOMContentLoaded', function() {
        initDragAndDrop();
        initColorPickerPreview();
        
        if (compactMode) {
            const container = document.querySelector('.kanban-board-container');
            if (container) container.classList.add('compact-mode');
            const btn = document.getElementById('compactModeBtn');
            if (btn) btn.textContent = '📐 Normal';
        }
    });

    function initDragAndDrop() {
        const taskContainers = document.querySelectorAll('.kanban-list .list-tasks');
        if (taskContainers.length === 0) return;
        
        taskContainers.forEach(container => {
            new Sortable(container, {
                group: 'tasks',
                animation: 200,
                onStart: function() { isDragging = true; },
                onEnd: function() {
                    isDragging = false;
                    saveTaskOrderAfterDrag();
                }
            });
        });
    }

    function initColorPickerPreview() {
        const addColorPicker = document.getElementById('listColor');
        const editColorPicker = document.getElementById('editListColor');
        
        if (addColorPicker) {
            addColorPicker.addEventListener('input', function(e) {
                updateAddColorPreview(e.target.value);
                this.style.backgroundColor = e.target.value;
                const color = e.target.value;
                const r = parseInt(color.slice(1,3), 16);
                const g = parseInt(color.slice(3,5), 16);
                const b = parseInt(color.slice(5,7), 16);
                const brightness = (r * 299 + g * 587 + b * 114) / 1000;
                this.style.color = brightness > 128 ? '#000' : '#fff';
            });
            setAddListColor('#e2e8f0');
        }
        
        if (editColorPicker) {
            editColorPicker.addEventListener('input', function(e) {
                updateColorPreview(e.target.value);
                this.style.backgroundColor = e.target.value;
                const color = e.target.value;
                const r = parseInt(color.slice(1,3), 16);
                const g = parseInt(color.slice(3,5), 16);
                const b = parseInt(color.slice(5,7), 16);
                const brightness = (r * 299 + g * 587 + b * 114) / 1000;
                this.style.color = brightness > 128 ? '#000' : '#fff';
            });
        }
    }

    function saveTaskOrderAfterDrag() {
        const lists = document.querySelectorAll('.kanban-list');
        const tasksData = {};
        
        lists.forEach(list => {
            const listId = list.dataset.listId;
            const taskElements = list.querySelectorAll('.task-card');
            const taskIds = Array.from(taskElements).map(task => task.dataset.taskId);
            if (taskIds.length > 0) tasksData[listId] = taskIds;
        });
        
        showNotification('Saving changes...', 'info');
        
        fetch('{{ route("tasks.reorder") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ tasks: tasksData })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) showNotification('✓ Changes saved!', 'success');
            else showNotification('Failed to save: ' + (data.message || 'Unknown error'), 'error');
        })
        .catch(error => {
            console.error('Reorder error:', error);
            showNotification('Failed to save changes.', 'error');
        });
    }

    // ==============================================
    // MODAL: ADD LIST
    // ==============================================

    function openAddListModal() {
        document.getElementById('addListModal').classList.remove('hidden');
        const form = document.getElementById('addListForm');
        if (form) form.reset();
        isSubmitting = false;
        setAddListColor('#e2e8f0');
        const btn = document.querySelector('#addListForm button[type="submit"]');
        if (btn) { btn.disabled = false; btn.textContent = 'Add List'; }
    }

    function closeAddListModal() {
        document.getElementById('addListModal').classList.add('hidden');
    }

    function submitAddList(event) {
        event.preventDefault();
        event.stopPropagation();

        if (isSubmitting) {
            showNotification('Please wait, still processing...', 'info');
            return false;
        }

        const boardId = {{ $board->id ?? 'null' }};
        if (!boardId) {
            alert('Board ID not found. Please refresh the page.');
            return false;
        }

        const listName = document.getElementById('listName')?.value?.trim();
        const listColor = document.getElementById('listColor')?.value || '#e2e8f0';

        if (!listName) {
            alert('Please enter a list name.');
            return false;
        }

        isSubmitting = true;

        const submitBtn = document.querySelector('#addListForm button[type="submit"]');
        const originalText = submitBtn?.textContent || 'Add List';
        if (submitBtn) { submitBtn.disabled = true; submitBtn.textContent = 'Creating...'; }

        fetch(`/boards/${boardId}/lists`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ name: listName, color: listColor })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('List created!', 'success');
                window.location.reload();
            } else {
                alert('Failed to create list: ' + (data.message || 'Unknown error'));
                isSubmitting = false;
                if (submitBtn) { submitBtn.disabled = false; submitBtn.textContent = originalText; }
            }
        })
        .catch(error => {
            console.error('Error creating list:', error);
            alert('An error occurred while creating the list.');
            isSubmitting = false;
            if (submitBtn) { submitBtn.disabled = false; submitBtn.textContent = originalText; }
        });

        return false;
    }

    // ==============================================
    // MODAL: ADD TASK
    // ==============================================

    function openAddTaskModal(listId) {
        document.getElementById('taskListId').value = listId;
        document.getElementById('addTaskModal').classList.remove('hidden');
        const form = document.getElementById('addTaskForm');
        if (form) form.reset();
    }

    function closeAddTaskModal() {
        document.getElementById('addTaskModal').classList.add('hidden');
    }

    function submitAddTask(event) {
        event.preventDefault();
        const listId = document.getElementById('taskListId').value;
        const formData = new FormData(event.target);

        showNotification('Creating task...', 'info');

        fetch(`/lists/${listId}/tasks`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.id) {
                showNotification('✓ Task created!', 'success');
                window.location.reload();
            } else {
                alert('Failed to create task');
            }
        })
        .catch(error => console.error('Error:', error));
    }

    // ==============================================
    // MODAL: EDIT TASK - WITH MULTIPLE ASSIGNEE (FIXED - WITH FALLBACK)
    // ==============================================

    function openEditTaskModal(taskId, event) {
        // Debug log
        console.log('openEditTaskModal called with taskId:', taskId, 'Type:', typeof taskId);
        
        // Jika taskId null/undefined, coba ambil dari data attribute via event
        if (!taskId || taskId === 'null' || taskId === 'undefined' || taskId === 0 || taskId === '0') {
            // Coba ambil dari event target jika ada
            if (event && event.target) {
                const taskCard = event.target.closest('.task-card');
                if (taskCard && taskCard.dataset && taskCard.dataset.taskId) {
                    taskId = taskCard.dataset.taskId;
                    console.log('Retrieved taskId from dataset:', taskId);
                }
            }
            
            // Jika masih null, coba cari task card dengan data-task-id
            if (!taskId || taskId === 'null' || taskId === 'undefined') {
                const taskCards = document.querySelectorAll('.task-card');
                for (let card of taskCards) {
                    if (card.dataset && card.dataset.taskId && card.dataset.taskId !== 'null') {
                        taskId = card.dataset.taskId;
                        console.log('Found taskId from task-card dataset:', taskId);
                        break;
                    }
                }
            }
        }
        
        // Validasi final
        if (!taskId || taskId === 'null' || taskId === 'undefined' || taskId === 0 || taskId === '0') {
            console.error('Invalid task ID for edit modal:', taskId);
            showNotification('Invalid task ID. Please refresh the page.', 'error');
            alert('Error: Invalid task ID. Please refresh the page and try again.');
            return;
        }
        
        const numericTaskId = parseInt(taskId);
        if (isNaN(numericTaskId) || numericTaskId <= 0) {
            console.error('Invalid numeric task ID:', numericTaskId);
            showNotification('Invalid task ID. Please refresh the page.', 'error');
            alert('Error: Invalid task ID. Please refresh the page and try again.');
            return;
        }
        
        showNotification('Loading task data...', 'info');
        
        // Load assignable users first (for multiple select)
        fetch(`/tasks/${numericTaskId}/assignable-users`, {
            headers: { 
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('HTTP ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            const assigneeSelect = document.getElementById('editTaskAssignee');
            if (assigneeSelect && data.all_users && !data.error) {
                assigneeSelect.innerHTML = '';
                
                data.all_users.forEach(user => {
                    const isSelected = data.assigned_users && data.assigned_users.includes(user.id);
                    const selectedAttr = isSelected ? 'selected' : '';
                    assigneeSelect.innerHTML += `<option value="${user.id}" ${selectedAttr}>${escapeHtml(user.name)} (${escapeHtml(user.username)})</option>`;
                });
            }
        })
        .catch(error => console.error('Error loading users:', error));
        
        // Load task data
        fetch(`/tasks/${numericTaskId}/edit`, {
            headers: { 
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('HTTP ' + response.status);
            }
            return response.json();
        })
        .then(task => {
            console.log('Task loaded:', task);
            document.getElementById('editTaskId').value = task.id;
            document.getElementById('editTaskTitle').value = task.title;
            document.getElementById('editTaskDescription').value = task.description || '';
            document.getElementById('editTaskPriority').value = task.priority || 'medium';
            
            if (task.due_date) {
                const dueDate = new Date(task.due_date);
                const formattedDate = dueDate.toISOString().split('T')[0];
                document.getElementById('editTaskDueDate').value = formattedDate;
            } else {
                document.getElementById('editTaskDueDate').value = '';
            }
            
            document.getElementById('editTaskModal').classList.remove('hidden');
        })
        .catch(error => {
            console.error('Error fetching task:', error);
            showNotification('Failed to load task data: ' + error.message, 'error');
            alert('Error loading task: ' + error.message);
        });
    }

    function closeEditTaskModal() {
        document.getElementById('editTaskModal').classList.add('hidden');
    }

    function submitEditTask(event) {
        event.preventDefault();
        const taskId = document.getElementById('editTaskId').value;
        
        const assigneeSelect = document.getElementById('editTaskAssignee');
        const selectedAssignees = Array.from(assigneeSelect.selectedOptions).map(option => option.value);
        
        const formData = new FormData(event.target);
        formData.append('_method', 'PUT');
        
        formData.delete('assignees[]');
        selectedAssignees.forEach(assigneeId => {
            formData.append('assignees[]', assigneeId);
        });
        
        showNotification('Updating task...', 'info');
        
        fetch(`/tasks/${taskId}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.id || data.success) {
                showNotification('✓ Task updated!', 'success');
                window.location.reload();
            } else {
                alert('Failed to update task: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error updating task: ' + error.message);
        });
    }

    // ==============================================
    // MODAL: EDIT LIST
    // ==============================================

    function editList(id, name, color) {
        document.getElementById('editListId').value = id;
        document.getElementById('editListName').value = name;
        setEditListColor(color || '#e2e8f0');
        document.getElementById('editListModal').classList.remove('hidden');
    }

    function closeEditListModal() {
        document.getElementById('editListModal').classList.add('hidden');
    }

    function submitEditList(event) {
        event.preventDefault();
        const listId = document.getElementById('editListId').value;
        const newColor = document.getElementById('editListColor').value;
        const newName = document.getElementById('editListName').value;
        
        updateListHeaderColor(listId, newColor);
        
        const formData = new FormData(event.target);
        formData.append('_method', 'PUT');

        showNotification('Updating list...', 'info');

        fetch(`/lists/${listId}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('✓ List updated!', 'success');
                closeEditListModal();
                const listElement = document.querySelector(`.kanban-list[data-list-id="${listId}"]`);
                if (listElement) {
                    const titleElement = listElement.querySelector('.list-header h3');
                    if (titleElement) titleElement.textContent = newName;
                }
            } else {
                showNotification('Failed to update list', 'error');
                location.reload();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error updating list', 'error');
            location.reload();
        });
    }

    function deleteList(listId) {
        if (confirm('Are you sure you want to delete this entire list and all its tasks?')) {
            showNotification('Deleting list...', 'info');
            
            fetch(`/lists/${listId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('✓ List deleted!', 'success');
                    window.location.reload();
                } else {
                    alert('Failed to delete list');
                }
            })
            .catch(error => console.error('Error:', error));
        }
    }

    // ==============================================
    // DELETE TASK FUNCTION - ONLY ADMIN
    // ==============================================

    function deleteTaskCard(taskId, taskTitle) {
        if (!confirm(`⚠️ Delete Task\n\nAre you sure you want to delete task "${taskTitle}"?\n\nThis action cannot be undone!`)) {
            return;
        }
        
        showNotification('🗑️ Deleting task...', 'info');
        
        fetch(`/tasks/${taskId}/delete`, {
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
                showNotification('✓ Task deleted successfully!', 'success');
                setTimeout(() => location.reload(), 500);
            } else {
                showNotification('❌ Failed to delete task: ' + (data.message || 'Unknown error'), 'error');
            }
        })
        .catch(error => {
            console.error('Error deleting task:', error);
            showNotification('❌ Error deleting task: ' + error.message, 'error');
        });
    }

    // ==============================================
    // ARCHIVE FUNCTION - FIXED
    // ==============================================

    function archiveTask(taskId, taskTitle) {
        if (!confirm(`Archive task "${taskTitle}"?\n\nArchived tasks can be restored later from the Archived page.`)) {
            return;
        }
        
        showNotification('Archiving task...', 'info');
        
        fetch(`/tasks/${taskId}/archive`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            credentials: 'same-origin'
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => { throw new Error(err.message || 'HTTP ' + response.status); });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                showNotification('✓ Task archived!', 'success');
                
                const taskCard = document.querySelector(`.task-card[data-task-id="${taskId}"]`);
                if (taskCard) {
                    const list = taskCard.closest('.kanban-list');
                    taskCard.remove();
                    
                    if (list) {
                        const counter = list.querySelector('.list-header .rounded-full');
                        if (counter) {
                            let count = parseInt(counter.textContent) - 1;
                            counter.textContent = count;
                        }
                    }
                }
            } else {
                showNotification('Failed to archive: ' + (data.message || 'Unknown error'), 'error');
            }
        })
        .catch(error => {
            console.error('Archive error:', error);
            showNotification('Error archiving task. Please refresh the page.', 'error');
        });
    }

    // ==============================================
    // RESIZE HANDLER FUNCTIONS
    // ==============================================

    function initResizeHandles() {
        const handles = document.querySelectorAll('.resize-handle');
        handles.forEach(handle => {
            handle.removeEventListener('mousedown', startResize);
            handle.addEventListener('mousedown', startResize);
        });
    }

    function startResize(e) {
        e.preventDefault();
        e.stopPropagation();
        
        isResizing = true;
        currentList = e.target.closest('.kanban-list');
        startX = e.clientX;
        startWidth = currentList.offsetWidth;
        
        document.addEventListener('mousemove', resizeList);
        document.addEventListener('mouseup', stopResize);
        document.body.style.cursor = 'ew-resize';
        document.body.style.userSelect = 'none';
    }

    function resizeList(e) {
        if (!isResizing || !currentList) return;
        const deltaX = e.clientX - startX;
        let newWidth = startWidth + deltaX;
        newWidth = Math.min(500, Math.max(250, newWidth));
        currentList.style.width = newWidth + 'px';
        localStorage.setItem(`list_width_${currentList.dataset.listId}`, newWidth);
    }

    function stopResize() {
        isResizing = false;
        currentList = null;
        document.removeEventListener('mousemove', resizeList);
        document.removeEventListener('mouseup', stopResize);
        document.body.style.cursor = '';
        document.body.style.userSelect = '';
        saveListWidths();
    }

    function saveListWidths() {
        const lists = document.querySelectorAll('.kanban-list');
        const widths = {};
        lists.forEach(list => {
            widths[list.dataset.listId] = parseInt(list.style.width);
        });
        
        fetch('{{ route("lists.save-widths") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ widths })
        }).catch(e => console.error('Error saving widths:', e));
    }

    function loadSavedWidths() {
        const lists = document.querySelectorAll('.kanban-list');
        lists.forEach(list => {
            const savedWidth = localStorage.getItem(`list_width_${list.dataset.listId}`);
            if (savedWidth) list.style.width = savedWidth + 'px';
        });
    }

    // ==============================================
    // COMPACT MODE TOGGLE
    // ==============================================

    function toggleCompactMode() {
        compactMode = !compactMode;
        localStorage.setItem('kanban_compact_mode', compactMode);
        const container = document.querySelector('.kanban-board-container');
        const btn = document.getElementById('compactModeBtn');
        if (compactMode) {
            if (container) container.classList.add('compact-mode');
            if (btn) btn.textContent = '📐 Normal';
        } else {
            if (container) container.classList.remove('compact-mode');
            if (btn) btn.textContent = '📏 Compact';
        }
    }

// ==============================================
// REMOVE COVER FROM CARD (show.blade.php)
// ==============================================

function removeCoverFromCard(taskId) {
    if (!confirm('Remove cover image from this task?')) return;
    
    showNotification('Removing cover...', 'info');
    
    fetch(`/tasks/${taskId}/remove-cover`, {
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
            showNotification('✓ Cover removed!', 'success');
            location.reload();
        } else {
            showNotification('Failed to remove cover', 'error');
        }
    })
    .catch(error => {
        console.error('Remove cover error:', error);
        showNotification('Error removing cover', 'error');
    });
}

// ==============================================
// UPLOAD COVER DIRECTLY (TANPA UNSPLASH)
// ==============================================

function uploadCoverForTask(taskId) {
    console.log('📤 uploadCoverForTask called for task:', taskId);
    
    // Buat input file temporary
    const fileInput = document.createElement('input');
    fileInput.type = 'file';
    fileInput.accept = 'image/*';
    fileInput.style.display = 'none';
    
    fileInput.onchange = function(e) {
        const file = e.target.files[0];
        if (!file) return;
        
        // Validasi tipe file
        if (!file.type.startsWith('image/')) {
            showNotification('Please select an image file (JPG, PNG, GIF)', 'error');
            return;
        }
        
        // Validasi ukuran (max 5MB)
        if (file.size > 5 * 1024 * 1024) {
            showNotification('File too large! Max 5MB', 'error');
            return;
        }
        
        uploadAndSetAsCover(taskId, file);
        fileInput.remove();
    };
    
    document.body.appendChild(fileInput);
    fileInput.click();
}

function uploadAndSetAsCover(taskId, file) {
    showNotification('📤 Uploading cover...', 'info');
    
    const formData = new FormData();
    formData.append('file', file);
    
    // Upload file dulu
    fetch(`/tasks/${taskId}/attachments`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        credentials: 'same-origin',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('HTTP ' + response.status);
        }
        return response.json();
    })
    .then(data => {
        if (data.success && data.attachment) {
            const attachmentId = data.attachment.id;
            console.log('✅ File uploaded, attachment ID:', attachmentId);
            
            // Set sebagai cover
            return fetch(`/attachments/${attachmentId}/set-cover`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                credentials: 'same-origin'
            });
        } else {
            throw new Error(data.error || 'Upload failed');
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('✓ Cover uploaded and set!', 'success');
            setTimeout(() => location.reload(), 500);
        } else {
            throw new Error(data.error || 'Failed to set as cover');
        }
    })
    .catch(error => {
        console.error('Upload cover error:', error);
        showNotification('Failed to upload cover: ' + error.message, 'error');
    });
}

// ==============================================
// REMOVE COVER FROM CARD (show.blade.php)
// ==============================================

function removeCoverFromCard(taskId) {
    if (!confirm('Remove cover image from this task?')) return;
    
    showNotification('Removing cover...', 'info');
    
    fetch(`/tasks/${taskId}/remove-cover`, {
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
            showNotification('✓ Cover removed!', 'success');
            location.reload();
        } else {
            showNotification('Failed to remove cover', 'error');
        }
    })
    .catch(error => {
        console.error('Remove cover error:', error);
        showNotification('Error removing cover', 'error');
    });
}

</script>

<!-- Include SortableJS -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>