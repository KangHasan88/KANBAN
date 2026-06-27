@extends('layouts.app')

@section('title', 'Dashboard - ' . $board->name)

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <div class="container mx-auto px-4 py-6">
        <!-- Header -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-5 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 dark:text-white">📊 Dashboard</h1>
                    <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">{{ $board->name }}</p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('boards.show', $board) }}" 
                       class="flex items-center gap-2 px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to Board
                    </a>
                    <button onclick="exportDashboard()" 
                            class="flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                        Export Chart
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Filter Bar -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-4 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">List</label>
                    <select id="filterList" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm">
                        <option value="">All Lists</option>
                        @foreach($board->lists as $list)
                        <option value="{{ $list->id }}">{{ $list->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Assignee</label>
                    <select id="filterAssignee" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm">
                        <option value="">All Users</option>
                        @foreach($board->sharedUsers as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                        <option value="{{ $board->owner->id }}">{{ $board->owner->name }} (Owner)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Date From</label>
                    <input type="date" id="filterDateFrom" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Date To</label>
                    <input type="date" id="filterDateTo" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm">
                </div>
            </div>
            <div class="flex justify-end mt-4">
                <button onclick="applyFilters()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">Apply Filters</button>
            </div>
        </div>
        
        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 border border-gray-100 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 dark:text-gray-400 text-sm">Total Tasks</p>
                        <p class="text-2xl font-bold text-gray-800 dark:text-white" id="totalTasks">0</p>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 border border-gray-100 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 dark:text-gray-400 text-sm">Completed</p>
                        <p class="text-2xl font-bold text-green-600 dark:text-green-400" id="completedTasks">0</p>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-green-100 dark:bg-green-900 flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 border border-gray-100 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 dark:text-gray-400 text-sm">Completion Rate</p>
                        <p class="text-2xl font-bold text-indigo-600 dark:text-indigo-400" id="completionRate">0%</p>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center">
                        <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 border border-gray-100 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 dark:text-gray-400 text-sm">Total Time</p>
                        <p class="text-2xl font-bold text-purple-600 dark:text-purple-400" id="totalTime">0h</p>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-purple-100 dark:bg-purple-900 flex items-center justify-center">
                        <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Charts Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Task Distribution Chart -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-4">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-3">📊 Task by List</h3>
                <canvas id="listChart" height="250"></canvas>
            </div>
            
            <!-- Priority Distribution -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-4">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-3">🎯 Priority Distribution</h3>
                <canvas id="priorityChart" height="250"></canvas>
            </div>
        </div>
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Task Trend -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-4">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-3">📈 Task Trend (Last 30 Days)</h3>
                <canvas id="trendChart" height="250"></canvas>
            </div>
            
            <!-- Assignee Performance -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-4">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-3">👥 Assignee Performance</h3>
                <div id="assigneeList" class="space-y-2 max-h-80 overflow-y-auto">
                    <div class="text-center text-gray-400 py-4">Loading...</div>
                </div>
            </div>
        </div>
        
        <!-- Recent Activity Timeline -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-4">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">📋 Recent Activity</h3>
            <div id="recentActivitiesList" class="space-y-3 max-h-96 overflow-y-auto">
                <div class="text-center text-gray-400 py-8">
                    <div class="inline-block w-6 h-6 border-2 border-gray-300 border-t-blue-500 rounded-full animate-spin"></div>
                    <p class="mt-2">Loading activities...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<!-- html2canvas for export -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

<script>
let charts = {};

function formatActionText(action) {
    const actions = {
        'created': 'created this task',
        'updated_title': 'changed the title',
        'updated_description': 'updated description',
        'updated_priority': 'changed priority',
        'updated_due_date': 'changed due date',
        'moved': 'moved this task',
        'commented': 'added a comment',
        'assigned': 'assigned user to this task',
        'archived': 'archived this task',
        'unarchived': 'restored this task',
        'added_checklist': 'added a checklist',
        'uploaded_file': 'uploaded a file',
        'set_cover': 'changed cover image',
        'deleted_comment': 'deleted a comment',
        'deleted_file': 'deleted a file',
        'assigned_label': 'added a label',
        'removed_label': 'removed a label',
        'set_recurring': 'set recurring task',
        'removed_recurring': 'removed recurring',
        'recurring_created': 'created recurring task',
        'time_tracking_started': 'started time tracking',
        'time_tracking_paused': 'paused time tracking',
        'time_tracking_stopped': 'stopped time tracking',
    };
    return actions[action] || 'made changes to this task';
}

function applyFilters() {
    const filters = {
        list_id: document.getElementById('filterList').value,
        assignee_id: document.getElementById('filterAssignee').value,
        date_from: document.getElementById('filterDateFrom').value,
        date_to: document.getElementById('filterDateTo').value,
    };
    
    fetch('{{ route("boards.dashboard.stats", $board) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify(filters)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateDashboard(data.data);
        }
    })
    .catch(error => console.error('Error:', error));
}

function updateDashboard(stats) {
    // Update summary
    document.getElementById('totalTasks').textContent = stats.task_stats.total;
    document.getElementById('completedTasks').textContent = stats.task_stats.completed;
    document.getElementById('completionRate').textContent = stats.task_stats.progress + '%';
    document.getElementById('totalTime').textContent = stats.summary.total_time;
    
    // Update List Chart
    if (charts.listChart) charts.listChart.destroy();
    const listCtx = document.getElementById('listChart').getContext('2d');
    charts.listChart = new Chart(listCtx, {
        type: 'bar',
        data: {
            labels: stats.list_stats.map(l => l.name),
            datasets: [{
                label: 'Tasks',
                data: stats.list_stats.map(l => l.count),
                backgroundColor: stats.list_stats.map(l => l.color + '80'),
                borderColor: stats.list_stats.map(l => l.color),
                borderWidth: 1
            }]
        },
        options: { responsive: true, maintainAspectRatio: true }
    });
    
    // Update Priority Chart
    if (charts.priorityChart) charts.priorityChart.destroy();
    const priorityCtx = document.getElementById('priorityChart').getContext('2d');
    charts.priorityChart = new Chart(priorityCtx, {
        type: 'doughnut',
        data: {
            labels: ['High', 'Medium', 'Low'],
            datasets: [{
                data: [stats.priority_stats.high, stats.priority_stats.medium, stats.priority_stats.low],
                backgroundColor: ['#ef4444', '#f59e0b', '#10b981'],
                borderWidth: 0
            }]
        },
        options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
    });
    
    // Update Trend Chart
    if (charts.trendChart) charts.trendChart.destroy();
    const trendCtx = document.getElementById('trendChart').getContext('2d');
    charts.trendChart = new Chart(trendCtx, {
        type: 'line',
        data: {
            labels: stats.trend_stats.map(t => t.date),
            datasets: [
                { label: 'Total Tasks', data: stats.trend_stats.map(t => t.total), borderColor: '#3b82f6', fill: false, tension: 0.3 },
                { label: 'Completed', data: stats.trend_stats.map(t => t.completed), borderColor: '#10b981', fill: false, tension: 0.3 }
            ]
        },
        options: { responsive: true, maintainAspectRatio: true }
    });
    
    // Update Assignee List
    const assigneeList = document.getElementById('assigneeList');
    if (stats.assignee_stats.length === 0) {
        assigneeList.innerHTML = '<div class="text-center text-gray-400 py-4">No assignee data available</div>';
    } else {
        assigneeList.innerHTML = stats.assignee_stats.map(a => `
            <div class="flex items-center justify-between p-2 bg-gray-50 dark:bg-gray-700 rounded-lg">
                <div class="flex items-center gap-2">
                    <img src="${a.avatar}" class="w-8 h-8 rounded-full object-cover" onerror="this.src='https://ui-avatars.com/api/?background=1e3a5f&color=fff&name=${a.name}'">
                    <span class="font-medium text-gray-800 dark:text-white">${escapeHtml(a.name)}</span>
                </div>
                <div class="flex items-center gap-4">
                    <span class="text-sm text-gray-500 dark:text-gray-400">${a.total} tasks</span>
                    <div class="w-24 bg-gray-200 dark:bg-gray-600 rounded-full h-2">
                        <div class="bg-green-500 rounded-full h-2" style="width: ${a.progress}%"></div>
                    </div>
                    <span class="text-sm font-medium text-green-600 dark:text-green-400">${a.progress}%</span>
                </div>
            </div>
        `).join('');
    }
    
    // ==============================================
    // RECENT ACTIVITIES - PERBAIKI INI
    // ==============================================
    const recentActivitiesContainer = document.getElementById('recentActivitiesList');
    if (stats.activity_stats && stats.activity_stats.recent && stats.activity_stats.recent.length > 0) {
        recentActivitiesContainer.innerHTML = stats.activity_stats.recent.map(activity => `
            <div class="flex items-start gap-3 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg hover:shadow-sm transition">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 rounded-full bg-gradient-to-r from-gray-400 to-gray-500 flex items-center justify-center text-white text-xs font-bold">
                        ${activity.user_name.charAt(0)}
                    </div>
                </div>
                <div class="flex-1">
                    <p class="text-sm text-gray-800 dark:text-gray-200">
                        <span class="font-semibold">${escapeHtml(activity.user_name)}</span>
                        <span class="text-gray-500 dark:text-gray-400"> ${formatActionText(activity.action)}</span>
                    </p>
                    <p class="text-xs text-gray-400 mt-1" title="${activity.created_at}">${activity.time_ago}</p>
                </div>
            </div>
        `).join('');
    } else {
        recentActivitiesContainer.innerHTML = '<div class="text-center text-gray-400 py-8">No recent activities found</div>';
    }
}

function exportDashboard() {
    html2canvas(document.querySelector('.container')).then(canvas => {
        const link = document.createElement('a');
        link.download = 'dashboard.png';
        link.href = canvas.toDataURL();
        link.click();
    });
}

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

document.addEventListener('DOMContentLoaded', () => {
    applyFilters();
});
</script>
@endsection