@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="bg-white rounded-xl shadow-md p-5 mb-6">
            <div class="flex items-center justify-between flex-wrap gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-full bg-gradient-to-r from-[#1e3a5f] to-[#2d4a7c] flex items-center justify-center shadow-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-[#1e3a5f]">Global Search</h1>
                        <p class="text-gray-500 text-sm">Search across all boards you have access to</p>
                    </div>
                </div>
                
                <!-- Tombol Back to Board -->
                <a href="{{ url()->previous() }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-all duration-200 text-sm font-medium">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Board
                </a>
            </div>
        </div>
        
        <!-- Search Bar -->
        <div class="bg-white rounded-xl shadow-sm p-4 mb-6 border border-gray-100">
            <div class="relative">
                <svg class="absolute left-4 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <input type="text" id="searchInput" 
                       placeholder="Search tasks by title, description, or anything..."
                       class="w-full pl-12 pr-24 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#1e3a5f] focus:border-transparent text-lg">
                <div class="absolute right-2 top-1/2 transform -translate-y-1/2 flex gap-2">
                    <button id="searchBtn" class="px-4 py-1.5 bg-[#1e3a5f] text-white rounded-lg hover:bg-[#2d4a7c] transition text-sm font-medium">
                        Search
                    </button>
                </div>
            </div>
            <p class="text-xs text-gray-400 mt-2 flex items-center gap-2">
                <span>💡</span> Press <kbd class="px-2 py-0.5 bg-gray-100 rounded text-xs font-mono">Enter</kbd> to search
            </p>
        </div>
        
        <!-- Filter Bar -->
        <div class="bg-white rounded-xl shadow-sm p-4 mb-6 border border-gray-100" id="filterBar" style="display: none;">
            <div class="flex flex-wrap gap-3 items-end">
                <div class="flex-1 min-w-[150px]">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Board</label>
                    <select id="filterBoard" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1e3a5f]">
                        <option value="">All Boards</option>
                    </select>
                </div>
                <div class="flex-1 min-w-[150px]">
                    <label class="block text-xs font-medium text-gray-500 mb-1">List</label>
                    <select id="filterList" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1e3a5f]">
                        <option value="">All Lists</option>
                    </select>
                </div>
                <div class="flex-1 min-w-[150px]">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Priority</label>
                    <select id="filterPriority" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1e3a5f]">
                        <option value="">All</option>
                        <option value="high">🔴 High</option>
                        <option value="medium">🟡 Medium</option>
                        <option value="low">🟢 Low</option>
                    </select>
                </div>
                <div class="flex-1 min-w-[150px]">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
                    <select id="filterStatus" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1e3a5f]">
                        <option value="active">Active Tasks</option>
                        <option value="archived">Archived Tasks</option>
                        <option value="both">Both</option>
                    </select>
                </div>
                <div>
                    <button onclick="resetFilters()" class="px-4 py-2 text-gray-500 hover:text-[#1e3a5f] transition text-sm">
                        Reset Filters
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Search Stats -->
        <div id="searchStats" class="text-sm text-gray-500 mb-4" style="display: none;">
            Found <span id="resultCount">0</span> results
        </div>
        
        <!-- Results -->
        <div id="resultsContainer" class="space-y-3">
            <div class="text-center py-16">
                <div class="w-20 h-20 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                    <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <p class="text-gray-500">Start typing to search across your boards</p>
            </div>
        </div>
        
        <!-- Pagination -->
        <div id="paginationContainer" class="mt-6 flex justify-center" style="display: none;"></div>
    </div>
</div>

<script>
let currentPage = 1;
let currentQuery = '';
let isLoading = false;

// ==============================================
// SEARCH FUNCTION
// ==============================================

function performSearch(page = 1) {
    const query = document.getElementById('searchInput').value.trim();
    const boardId = document.getElementById('filterBoard').value;
    const listId = document.getElementById('filterList').value;
    const priority = document.getElementById('filterPriority').value;
    const status = document.getElementById('filterStatus').value;
    
    if (!query && !boardId && !priority && status === 'active') {
        document.getElementById('resultsContainer').innerHTML = `
            <div class="text-center py-16">
                <div class="w-20 h-20 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                    <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <p class="text-gray-500">Start typing to search across your boards</p>
            </div>
        `;
        document.getElementById('filterBar').style.display = 'none';
        document.getElementById('searchStats').style.display = 'none';
        document.getElementById('paginationContainer').style.display = 'none';
        return;
    }
    
    currentQuery = query;
    currentPage = page;
    isLoading = true;
    
    showLoadingState();
    
    let url = `/search/api?page=${page}&q=${encodeURIComponent(query)}`;
    if (boardId) url += `&board_id=${boardId}`;
    if (listId) url += `&list_id=${listId}`;
    if (priority) url += `&priority=${priority}`;
    if (status) url += `&status=${status}`;
    
    fetch(url, {
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateFilterOptions(data);
            renderResults(data.tasks.data || []);
            updateResultCount(data.tasks.total);
            renderPagination(data.tasks);
            document.getElementById('filterBar').style.display = 'flex';
            document.getElementById('searchStats').style.display = 'block';
        } else {
            showError(data.message);
        }
    })
    .catch(error => {
        console.error('Search error:', error);
        showError('Failed to search. Please try again.');
    })
    .finally(() => {
        isLoading = false;
    });
}

function showLoadingState() {
    const container = document.getElementById('resultsContainer');
    container.innerHTML = `
        <div class="text-center py-16">
            <div class="inline-block w-8 h-8 border-2 border-gray-300 border-t-[#1e3a5f] rounded-full animate-spin"></div>
            <p class="text-gray-500 mt-2">Searching...</p>
        </div>
    `;
}

function showError(message) {
    const container = document.getElementById('resultsContainer');
    container.innerHTML = `
        <div class="text-center py-16">
            <div class="w-20 h-20 mx-auto mb-4 bg-red-100 rounded-full flex items-center justify-center">
                <svg class="w-10 h-10 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <p class="text-red-500">${message}</p>
            <button onclick="performSearch()" class="mt-4 px-4 py-2 bg-[#1e3a5f] text-white rounded-lg">Try Again</button>
        </div>
    `;
}

function updateFilterOptions(data) {
    const boardSelect = document.getElementById('filterBoard');
    const currentBoardValue = boardSelect.value;
    boardSelect.innerHTML = '<option value="">All Boards</option>';
    if (data.boards) {
        data.boards.forEach(board => {
            boardSelect.innerHTML += `<option value="${board.id}" ${currentBoardValue == board.id ? 'selected' : ''}>${escapeHtml(board.name)}</option>`;
        });
    }
}

function renderResults(tasks) {
    const container = document.getElementById('resultsContainer');
    
    if (!tasks || tasks.length === 0) {
        container.innerHTML = `
            <div class="text-center py-16">
                <div class="w-20 h-20 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                    <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <p class="text-gray-500">No tasks found</p>
                <p class="text-sm text-gray-400 mt-1">Try different search terms or filters</p>
            </div>
        `;
        return;
    }
    
    let html = '';
    tasks.forEach(task => {
        const boardName = task.task_list?.board?.name || 'Unknown Board';
        const listName = task.task_list?.name || 'Unknown List';
        const priorityIcon = task.priority === 'high' ? '🔴' : (task.priority === 'medium' ? '🟡' : '🟢');
        const priorityClass = task.priority === 'high' ? 'text-red-600' : (task.priority === 'medium' ? 'text-yellow-600' : 'text-green-600');
        const isArchived = task.archived_at !== null;
        
        html += `
            <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition-all duration-200 border border-gray-100 cursor-pointer task-result-item" 
                 data-task-id="${task.id}"
                 onclick="openTaskDetailModal(${task.id})">
                <div class="p-5">
                    <div class="flex items-start justify-between flex-wrap gap-3">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap mb-2">
                                <span class="text-xs px-2 py-0.5 rounded-full bg-gray-100 text-gray-600">${escapeHtml(boardName)}</span>
                                <span class="text-xs px-2 py-0.5 rounded-full bg-gray-100 text-gray-600">📋 ${escapeHtml(listName)}</span>
                                ${isArchived ? '<span class="text-xs px-2 py-0.5 rounded-full bg-gray-200 text-gray-500">📦 Archived</span>' : '<span class="text-xs px-2 py-0.5 rounded-full bg-green-100 text-green-700">✓ Active</span>'}
                                <span class="text-xs font-medium ${priorityClass}">${priorityIcon} ${task.priority ? task.priority.charAt(0).toUpperCase() + task.priority.slice(1) : 'Medium'}</span>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-800 mb-1 hover:text-[#1e3a5f] transition">${highlightText(task.title, currentQuery)}</h3>
                            ${task.description ? `<p class="text-sm text-gray-500 line-clamp-2 mt-1">${highlightText(escapeHtml(task.description.substring(0, 150)), currentQuery)}</p>` : ''}
                            
                            <div class="flex items-center gap-4 mt-3 flex-wrap">
                                <div class="flex items-center gap-1 text-xs text-gray-400">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    ${formatDate(task.created_at)}
                                </div>
                                ${task.due_date ? `
                                <div class="flex items-center gap-1 text-xs ${new Date(task.due_date) < new Date() ? 'text-red-500' : 'text-gray-400'}">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Due ${formatDate(task.due_date)}
                                </div>
                                ` : ''}
                                ${task.assignees && task.assignees.length > 0 ? `
                                <div class="flex items-center gap-1">
                                    ${task.assignees.slice(0, 3).map(a => `<div class="w-5 h-5 rounded-full bg-[#1e3a5f] flex items-center justify-center text-white text-xs font-bold" title="${escapeHtml(a.name)}">${escapeHtml(a.name.charAt(0))}</div>`).join('')}
                                    ${task.assignees.length > 3 ? `<span class="text-xs text-gray-400">+${task.assignees.length - 3}</span>` : ''}
                                </div>
                                ` : ''}
                                ${task.labels && task.labels.length > 0 ? `
                                <div class="flex items-center gap-1">
                                    ${task.labels.slice(0, 2).map(l => `<span class="text-xs px-2 py-0.5 rounded-full" style="background-color: ${l.color}20; color: ${l.color}">${escapeHtml(l.name)}</span>`).join('')}
                                    ${task.labels.length > 2 ? `<span class="text-xs text-gray-400">+${task.labels.length - 2}</span>` : ''}
                                </div>
                                ` : ''}
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        `;
    });
    
    container.innerHTML = html;
}

function highlightText(text, query) {
    if (!query || query.trim() === '') return text;
    const regex = new RegExp(`(${escapeRegex(query)})`, 'gi');
    return text.replace(regex, '<mark class="bg-yellow-200 text-gray-900 px-0.5 rounded">$1</mark>');
}

function escapeRegex(string) {
    return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
}

function updateResultCount(count) {
    document.getElementById('resultCount').textContent = count;
}

function renderPagination(pagination) {
    const container = document.getElementById('paginationContainer');
    if (!pagination || pagination.last_page <= 1) {
        container.style.display = 'none';
        return;
    }
    
    container.style.display = 'flex';
    let html = '<div class="flex gap-2">';
    
    if (pagination.current_page > 1) {
        html += `<button onclick="performSearch(${pagination.current_page - 1})" class="px-3 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">← Prev</button>`;
    }
    
    const startPage = Math.max(1, pagination.current_page - 2);
    const endPage = Math.min(pagination.last_page, pagination.current_page + 2);
    
    if (startPage > 1) {
        html += `<button onclick="performSearch(1)" class="px-3 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">1</button>`;
        if (startPage > 2) html += `<span class="px-2 py-2">...</span>`;
    }
    
    for (let i = startPage; i <= endPage; i++) {
        const isActive = i === pagination.current_page;
        html += `<button onclick="performSearch(${i})" class="px-3 py-2 ${isActive ? 'bg-[#1e3a5f] text-white' : 'bg-white border border-gray-300 hover:bg-gray-50'} rounded-lg transition">${i}</button>`;
    }
    
    if (endPage < pagination.last_page) {
        if (endPage < pagination.last_page - 1) html += `<span class="px-2 py-2">...</span>`;
        html += `<button onclick="performSearch(${pagination.last_page})" class="px-3 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">${pagination.last_page}</button>`;
    }
    
    if (pagination.current_page < pagination.last_page) {
        html += `<button onclick="performSearch(${pagination.current_page + 1})" class="px-3 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">Next →</button>`;
    }
    
    html += '</div>';
    container.innerHTML = html;
}

function resetFilters() {
    document.getElementById('filterBoard').value = '';
    document.getElementById('filterList').innerHTML = '<option value="">All Lists</option>';
    document.getElementById('filterPriority').value = '';
    document.getElementById('filterStatus').value = 'active';
    performSearch();
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
}

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Event listeners
document.getElementById('searchInput')?.addEventListener('keypress', function(e) {
    if (e.key === 'Enter') performSearch();
});

document.getElementById('searchBtn')?.addEventListener('click', () => performSearch());
document.getElementById('filterPriority')?.addEventListener('change', () => performSearch());
document.getElementById('filterStatus')?.addEventListener('change', () => performSearch());
document.getElementById('filterBoard')?.addEventListener('change', () => performSearch());

// Trigger list update when board changes
document.getElementById('filterBoard')?.addEventListener('change', function() {
    const boardId = this.value;
    if (boardId) {
        fetch(`/boards/${boardId}/labels?ajax=1`, {
            headers: { 'Accept': 'application/json' }
        })
        .then(response => response.json())
        .then(labels => {
            const labelSelect = document.getElementById('filterLabel');
            if (labelSelect) {
                labelSelect.innerHTML = '<option value="">All Labels</option>';
                labels.forEach(label => {
                    labelSelect.innerHTML += `<option value="${label.id}">${escapeHtml(label.name)}</option>`;
                });
            }
        })
        .catch(console.error);
        
        fetch(`/boards/${boardId}/lists`, {
            headers: { 'Accept': 'application/json' }
        })
        .then(response => response.json())
        .then(lists => {
            const listSelect = document.getElementById('filterList');
            listSelect.innerHTML = '<option value="">All Lists</option>';
            lists.forEach(list => {
                listSelect.innerHTML += `<option value="${list.id}">${escapeHtml(list.name)}</option>`;
            });
        })
        .catch(console.error);
    } else {
        document.getElementById('filterList').innerHTML = '<option value="">All Lists</option>';
    }
});

function openTaskDetailModal(taskId) {
    if (typeof window.openTaskDetailModal === 'function') {
        window.openTaskDetailModal(taskId);
    } else {
        window.location.href = appUrl(`tasks/${taskId}/edit`);
    }
}
</script>

<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
mark {
    background-color: #fef08a;
    color: #1e3a5f;
    padding: 0 2px;
    border-radius: 4px;
}
.task-result-item {
    transition: all 0.2s ease;
}
.task-result-item:hover {
    transform: translateY(-2px);
}
</style>
@endsection
