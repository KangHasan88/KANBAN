@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="bg-white rounded-xl shadow-md p-5 mb-6">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-full bg-gradient-to-r from-[#1e3a5f] to-[#2d4a7c] flex items-center justify-center shadow-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-[#1e3a5f]">Activity Log</h1>
                        <p class="text-gray-500 text-sm">{{ $board->name }}</p>
                    </div>
                </div>
                <a href="{{ route('boards.show', $board) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-all duration-200 text-sm font-medium">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Board
                </a>
            </div>
        </div>
        
        <!-- Filter Bar -->
        <div class="bg-white rounded-xl shadow-sm p-4 mb-6 border border-gray-100">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">User</label>
                    <select id="filterUser" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1e3a5f]">
                        <option value="">All Users</option>
                        @foreach($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Action Type</label>
                    <select id="filterAction" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1e3a5f]">
                        <option value="">All Actions</option>
                        @foreach($actions as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Date From</label>
                    <input type="date" id="filterDateFrom" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1e3a5f]">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Date To</label>
                    <input type="date" id="filterDateTo" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1e3a5f]">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Search</label>
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <input type="text" id="filterSearch" placeholder="Search task or activity..." class="w-full pl-9 pr-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#1e3a5f]">
                    </div>
                </div>
            </div>
            <div class="flex justify-end gap-2 mt-3 pt-3 border-t border-gray-100">
                <button onclick="resetFilters()" class="px-3 py-1.5 text-sm text-gray-500 hover:text-[#1e3a5f] transition">Reset Filters</button>
                <button onclick="exportActivities()" class="px-3 py-1.5 text-sm bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    Export CSV
                </button>
            </div>
        </div>
        
        <!-- Stats Summary -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
            <div class="bg-white rounded-xl p-3 border border-gray-100 text-center">
                <div class="text-2xl font-bold text-[#1e3a5f]" id="totalCount">0</div>
                <div class="text-xs text-gray-500">Total Activities</div>
            </div>
            <div class="bg-white rounded-xl p-3 border border-gray-100 text-center">
                <div class="text-2xl font-bold text-green-600" id="todayCount">0</div>
                <div class="text-xs text-gray-500">Today</div>
            </div>
            <div class="bg-white rounded-xl p-3 border border-gray-100 text-center">
                <div class="text-2xl font-bold text-blue-600" id="thisWeekCount">0</div>
                <div class="text-xs text-gray-500">This Week</div>
            </div>
            <div class="bg-white rounded-xl p-3 border border-gray-100 text-center">
                <div class="text-2xl font-bold text-purple-600" id="thisMonthCount">0</div>
                <div class="text-xs text-gray-500">This Month</div>
            </div>
        </div>
        
        <!-- Activities Timeline -->
        <div id="activitiesContainer" class="space-y-4">
            <div class="text-center py-16">
                <div class="inline-block w-8 h-8 border-2 border-gray-300 border-t-[#1e3a5f] rounded-full animate-spin"></div>
                <p class="text-gray-500 mt-2">Loading activities...</p>
            </div>
        </div>
        
        <!-- Pagination -->
        <div id="paginationContainer" class="mt-6 flex justify-center"></div>
    </div>
</div>

<style>
    .timeline-dot {
        transition: all 0.2s ease;
    }
    .activity-item {
        transition: all 0.2s ease;
    }
    .activity-item:hover {
        transform: translateX(4px);
    }
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>

<script>
let currentPage = 1;
let totalActivities = 0;

// ==============================================
// LOAD ACTIVITIES
// ==============================================

function loadActivities(page = 1) {
    currentPage = page;
    
    const userId = document.getElementById('filterUser').value;
    const action = document.getElementById('filterAction').value;
    const dateFrom = document.getElementById('filterDateFrom').value;
    const dateTo = document.getElementById('filterDateTo').value;
    const search = document.getElementById('filterSearch').value;
    
    let url = `{{ route("boards.activity-log.api", $board) }}?page=${page}`;
    if (userId) url += `&user_id=${userId}`;
    if (action) url += `&action=${action}`;
    if (dateFrom) url += `&date_from=${dateFrom}`;
    if (dateTo) url += `&date_to=${dateTo}`;
    if (search) url += `&search=${encodeURIComponent(search)}`;
    
    fetch(url, {
        headers: { 'Accept': 'application/json' }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            totalActivities = data.pagination.total;
            renderActivities(data.activities);
            renderPagination(data.pagination);
            updateStats(data.activities);
        } else {
            showError(data.message);
        }
    })
    .catch(error => {
        console.error('Error loading activities:', error);
        showError('Failed to load activities');
    });
}

function renderActivities(activities) {
    const container = document.getElementById('activitiesContainer');
    
    if (!activities || activities.length === 0) {
        container.innerHTML = `
            <div class="text-center py-16">
                <div class="w-20 h-20 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                    <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <p class="text-gray-500 font-medium">No activities found</p>
                <p class="text-sm text-gray-400 mt-1">Try adjusting your filters</p>
            </div>
        `;
        return;
    }
    
    let html = '';
    let lastDate = '';
    
    activities.forEach(activity => {
        const date = new Date(activity.created_at);
        const today = new Date();
        const yesterday = new Date(today);
        yesterday.setDate(yesterday.getDate() - 1);
        
        let dateHeader = '';
        const dateKey = date.toDateString();
        
        if (dateKey !== lastDate) {
            let dateLabel = '';
            if (dateKey === today.toDateString()) {
                dateLabel = 'Today';
            } else if (dateKey === yesterday.toDateString()) {
                dateLabel = 'Yesterday';
            } else {
                dateLabel = date.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
            }
            
            dateHeader = `
                <div class="flex items-center gap-3 mt-6 first:mt-0">
                    <div class="w-2 h-2 rounded-full bg-[#1e3a5f]"></div>
                    <h3 class="text-sm font-semibold text-gray-700">${dateLabel}</h3>
                    <div class="flex-1 h-px bg-gray-200"></div>
                </div>
            `;
            lastDate = dateKey;
        }
        
        html += `
            ${dateHeader}
            <div class="activity-item bg-white rounded-xl border border-gray-100 p-4 hover:shadow-md transition-all duration-200 cursor-pointer" onclick="openTaskDetail(${activity.task_id})">
                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0">
                        ${activity.user_avatar ? 
                            `<img src="${activity.user_avatar}" class="w-10 h-10 rounded-full object-cover">` :
                            `<div class="w-10 h-10 rounded-full bg-gradient-to-br from-[#1e3a5f] to-[#2d4a7c] flex items-center justify-center text-white text-sm font-bold">${activity.user_name.charAt(0)}</div>`
                        }
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="font-semibold text-gray-800">${escapeHtml(activity.user_name)}</span>
                            <span class="text-xl ${activity.icon_color}">${activity.icon}</span>
                            <span class="text-sm text-gray-600">${escapeHtml(activity.action_text)}</span>
                        </div>
                        <div class="mt-1">
                            <a href="#" onclick="event.stopPropagation(); window.location.href=appUrl('tasks/${activity.task_id}/edit')" class="text-sm font-medium text-[#1e3a5f] hover:underline">
                                ${escapeHtml(activity.task_title)}
                            </a>
                        </div>
                        <div class="flex items-center gap-2 mt-2 text-xs text-gray-400">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>${activity.created_at_formatted}</span>
                            <span class="text-gray-300">•</span>
                            <span>${activity.created_at_human}</span>
                        </div>
                    </div>
                </div>
            </div>
        `;
    });
    
    container.innerHTML = html;
}

function updateStats(activities) {
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    
    const weekAgo = new Date(today);
    weekAgo.setDate(weekAgo.getDate() - 7);
    
    const monthAgo = new Date(today);
    monthAgo.setDate(monthAgo.getDate() - 30);
    
    let todayCount = 0;
    let weekCount = 0;
    let monthCount = 0;
    
    activities.forEach(activity => {
        const activityDate = new Date(activity.created_at);
        if (activityDate >= today) todayCount++;
        if (activityDate >= weekAgo) weekCount++;
        if (activityDate >= monthAgo) monthCount++;
    });
    
    document.getElementById('totalCount').textContent = totalActivities;
    document.getElementById('todayCount').textContent = todayCount;
    document.getElementById('thisWeekCount').textContent = weekCount;
    document.getElementById('thisMonthCount').textContent = monthCount;
}

function renderPagination(pagination) {
    const container = document.getElementById('paginationContainer');
    
    if (pagination.last_page <= 1) {
        container.innerHTML = '';
        return;
    }
    
    let html = '<div class="flex gap-2">';
    
    if (pagination.current_page > 1) {
        html += `<button onclick="loadActivities(${pagination.current_page - 1})" class="px-3 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">← Prev</button>`;
    }
    
    const startPage = Math.max(1, pagination.current_page - 2);
    const endPage = Math.min(pagination.last_page, pagination.current_page + 2);
    
    if (startPage > 1) {
        html += `<button onclick="loadActivities(1)" class="px-3 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">1</button>`;
        if (startPage > 2) html += `<span class="px-2 py-2">...</span>`;
    }
    
    for (let i = startPage; i <= endPage; i++) {
        const isActive = i === pagination.current_page;
        html += `<button onclick="loadActivities(${i})" class="px-3 py-2 ${isActive ? 'bg-[#1e3a5f] text-white' : 'bg-white border border-gray-300 hover:bg-gray-50'} rounded-lg transition">${i}</button>`;
    }
    
    if (endPage < pagination.last_page) {
        if (endPage < pagination.last_page - 1) html += `<span class="px-2 py-2">...</span>`;
        html += `<button onclick="loadActivities(${pagination.last_page})" class="px-3 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">${pagination.last_page}</button>`;
    }
    
    if (pagination.current_page < pagination.last_page) {
        html += `<button onclick="loadActivities(${pagination.current_page + 1})" class="px-3 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">Next →</button>`;
    }
    
    html += '</div>';
    container.innerHTML = html;
}

function resetFilters() {
    document.getElementById('filterUser').value = '';
    document.getElementById('filterAction').value = '';
    document.getElementById('filterDateFrom').value = '';
    document.getElementById('filterDateTo').value = '';
    document.getElementById('filterSearch').value = '';
    loadActivities(1);
}

function exportActivities() {
    const userId = document.getElementById('filterUser').value;
    const action = document.getElementById('filterAction').value;
    const dateFrom = document.getElementById('filterDateFrom').value;
    const dateTo = document.getElementById('filterDateTo').value;
    const search = document.getElementById('filterSearch').value;
    
    let url = `{{ route("boards.activity-log.api", $board) }}?export=1&page=1`;
    if (userId) url += `&user_id=${userId}`;
    if (action) url += `&action=${action}`;
    if (dateFrom) url += `&date_from=${dateFrom}`;
    if (dateTo) url += `&date_to=${dateTo}`;
    if (search) url += `&search=${encodeURIComponent(search)}`;
    
    fetch(url, { headers: { 'Accept': 'application/json' } })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.activities) {
            let csv = "\uFEFFUser,Action,Task,Date,Time\n";
            data.activities.forEach(act => {
                csv += `"${act.user_name}","${act.action_text}","${act.task_title}","${act.created_at_formatted}","${act.created_at_human}"\n`;
            });
            const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            const url = URL.createObjectURL(blob);
            link.href = url;
            link.setAttribute('download', `activity_log_${new Date().toISOString().split('T')[0]}.csv`);
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            URL.revokeObjectURL(url);
        } else {
            showNotification('No data to export', 'error');
        }
    })
    .catch(error => console.error('Export error:', error));
}

function openTaskDetail(taskId) {
    if (taskId && typeof openTaskDetailModal === 'function') {
        openTaskDetailModal(taskId);
    } else if (taskId) {
        window.location.href = appUrl(`tasks/${taskId}/edit`);
    }
}

function showError(message) {
    const container = document.getElementById('activitiesContainer');
    container.innerHTML = `
        <div class="text-center py-16">
            <div class="w-20 h-20 mx-auto mb-4 bg-red-100 rounded-full flex items-center justify-center">
                <svg class="w-10 h-10 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <p class="text-red-500">${message}</p>
            <button onclick="loadActivities()" class="mt-4 px-4 py-2 bg-[#1e3a5f] text-white rounded-lg">Try Again</button>
        </div>
    `;
}

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = `fixed bottom-4 right-4 z-50 px-4 py-2 rounded-lg shadow-lg text-white ${type === 'success' ? 'bg-green-500' : 'bg-red-500'}`;
    notification.textContent = message;
    document.body.appendChild(notification);
    setTimeout(() => notification.remove(), 3000);
}

// Event listeners
document.getElementById('filterUser')?.addEventListener('change', () => loadActivities(1));
document.getElementById('filterAction')?.addEventListener('change', () => loadActivities(1));
document.getElementById('filterDateFrom')?.addEventListener('change', () => loadActivities(1));
document.getElementById('filterDateTo')?.addEventListener('change', () => loadActivities(1));
document.getElementById('filterSearch')?.addEventListener('keyup', function(e) {
    if (e.key === 'Enter') loadActivities(1);
});

// Initial load
loadActivities();
</script>
@endsection
