@extends('layouts.app')

@section('title', $board->name . ' - Gantt Chart')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <div class="container mx-auto px-4 py-6">
        
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-5 mb-6">
            <div class="flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <a href="{{ route('boards.show', $board) }}" class="text-gray-500 hover:text-gray-700">← Kembali</a>
                    <h1 class="text-xl font-bold">📊 Bagan Gantt</h1>
                </div>
                <button onclick="exportGantt()" class="px-4 py-2 bg-green-600 text-white rounded-lg">📸 Export</button>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 mb-6">
            <div class="flex flex-wrap gap-3 items-center">
                <select id="filterList" class="border rounded-lg px-3 py-2 text-sm">
                    <option value="all">Semua List</option>
                    @foreach($board->lists as $list)
                    <option value="{{ $list->id }}">{{ $list->name }}</option>
                    @endforeach
                </select>
                
                <select id="filterPriority" class="border rounded-lg px-3 py-2 text-sm">
                    <option value="all">Semua Prioritas</option>
                    <option value="high">🔴 Tinggi</option>
                    <option value="medium">🟡 Sedang</option>
                    <option value="low">🟢 Rendah</option>
                </select>
                
                <select id="filterStatus" class="border rounded-lg px-3 py-2 text-sm">
                    <option value="active">Task Aktif</option>
                    <option value="all">Semua Task</option>
                </select>
                
                <button onclick="applyFilters()" class="px-4 py-2 bg-blue-600 text-white rounded-lg">Terapkan</button>
                <button onclick="resetView()" class="px-4 py-2 bg-gray-500 text-white rounded-lg">Reset</button>
            </div>
        </div>

        <!-- Gantt Chart Container -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-4 overflow-auto">
            <div id="gantt-chart" style="min-width: 100%; min-height: 500px;">
                <div class="text-center py-20">
                    <div class="spinner"></div>
                    <p class="mt-2 text-gray-500">Memuat data...</p>
                </div>
            </div>
        </div>

        <!-- Legend -->
        <div class="mt-4 flex flex-wrap gap-4 justify-center">
            <div class="flex items-center gap-2"><div class="w-4 h-4 rounded" style="background: #ef4444;"></div><span class="text-sm">Prioritas Tinggi (Aktif)</span></div>
            <div class="flex items-center gap-2"><div class="w-4 h-4 rounded" style="background: #f59e0b;"></div><span class="text-sm">Prioritas Sedang (Aktif)</span></div>
            <div class="flex items-center gap-2"><div class="w-4 h-4 rounded" style="background: #10b981;"></div><span class="text-sm">Prioritas Rendah (Aktif)</span></div>
            <div class="flex items-center gap-2"><div class="w-4 h-4 rounded" style="background: #22c55e;"></div><span class="text-sm">✓ Selesai / Diarsipkan</span></div>
        </div>
    </div>
</div>

<style>
    .spinner {
        display: inline-block;
        width: 30px;
        height: 30px;
        border: 3px solid #e5e7eb;
        border-top-color: #3b82f6;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
    .gantt-row:hover {
        background-color: #f9fafb;
    }
    .dark .gantt-row:hover {
        background-color: #374151;
    }
    .gantt-task-bar {
        transition: all 0.2s ease;
        cursor: pointer;
        position: relative;
        border-radius: 6px;
    }
    .gantt-task-bar:hover {
        transform: translateY(-2px);
        filter: brightness(1.05);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>

<script>
let tasks = [];
let dateRange = { min: null, max: null };

document.addEventListener('DOMContentLoaded', () => {
    loadTasks();
});

async function loadTasks() {
    showLoading(true);
    
    const listId = document.getElementById('filterList').value;
    const priority = document.getElementById('filterPriority').value;
    const status = document.getElementById('filterStatus').value;
    
    let url = '{{ route("boards.gantt.tasks", $board) }}?';
    if (listId !== 'all') url += `&list_id=${listId}`;
    if (priority !== 'all') url += `&priority=${priority}`;
    if (status === 'active') url += `&status=active`;
    
    try {
        const response = await fetch(url, {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        });
        const data = await response.json();
        
        if (data.success && data.tasks) {
            tasks = data.tasks.filter(t => t.start && t.end);
            
            if (tasks.length === 0) {
                showEmptyState('Tidak ada task dengan due date');
                return;
            }
            
            calculateDateRange();
            renderGantt();
        } else {
            showEmptyState('Gagal memuat data');
        }
    } catch (error) {
        console.error('Error:', error);
        showEmptyState('Error: ' + error.message);
    }
    
    showLoading(false);
}

function calculateDateRange() {
    if (tasks.length === 0) {
        const today = new Date();
        dateRange.min = new Date(today);
        dateRange.max = new Date(today);
        dateRange.min.setDate(today.getDate() - 14);
        dateRange.max.setDate(today.getDate() + 14);
        return;
    }
    
    let minDate = new Date(tasks[0].start);
    let maxDate = new Date(tasks[0].end);
    
    tasks.forEach(task => {
        const start = new Date(task.start);
        const end = new Date(task.end);
        if (start < minDate) minDate = start;
        if (end > maxDate) maxDate = end;
    });
    
    // Add padding 14 days before first, 14 days after last
    dateRange.min = new Date(minDate);
    dateRange.min.setDate(minDate.getDate() - 14);
    dateRange.max = new Date(maxDate);
    dateRange.max.setDate(maxDate.getDate() + 14);
}

function renderGantt() {
    const container = document.getElementById('gantt-chart');
    if (!container) return;
    
    // Generate list of dates from min to max
    const dates = [];
    let current = new Date(dateRange.min);
    while (current <= dateRange.max) {
        dates.push(new Date(current));
        current.setDate(current.getDate() + 1);
    }
    
    const priorityColors = {
        'high': '#ef4444',
        'medium': '#f59e0b', 
        'low': '#10b981'
    };
    const archivedColor = '#22c55e';
    
    let html = `
        <div style="overflow-x: auto; min-width: ${dates.length * 80 + 280}px;">
            <table style="width: 100%; border-collapse: collapse; font-size: 12px;">
                <thead>
                    <tr style="border-bottom: 2px solid #e5e7eb;">
                        <th style="position: sticky; left: 0; background: white; padding: 12px; text-align: left; min-width: 250px; z-index: 10;">Task</th>
                        ${dates.map(date => `
                            <th style="text-align: center; padding: 8px 4px; min-width: 70px;">
                                <div style="font-size: 11px;">${getDayName(date)}</div>
                                <div style="font-size: 13px; font-weight: bold;">${date.getDate()}</div>
                                <div style="font-size: 10px; color: #9ca3af;">${getMonthName(date)}</div>
                            </th>
                        `).join('')}
                    </tr>
                </thead>
                <tbody>
    `;
    
    for (const task of tasks) {
        const taskStart = new Date(task.start);
        const taskEnd = new Date(task.end);
        
        // Hitung durasi dalam hari
        const duration = Math.ceil((taskEnd - taskStart) / (1000 * 60 * 60 * 24)) + 1;
        const progress = task.progress || 0;
        
        // Tentukan warna
        let color;
        if (task.is_archived) {
            color = archivedColor;
        } else {
            color = priorityColors[task.priority] || '#6b7280';
        }
        
        // Cari posisi start column berdasarkan tanggal
        let startCol = -1;
        for (let i = 0; i < dates.length; i++) {
            if (dates[i].getFullYear() === taskStart.getFullYear() &&
                dates[i].getMonth() === taskStart.getMonth() &&
                dates[i].getDate() === taskStart.getDate()) {
                startCol = i;
                break;
            }
        }
        
        if (startCol === -1) continue;
        
        // Hitung berapa kolom yang harus di-merge
        let spanCount = 0;
        for (let i = startCol; i < dates.length; i++) {
            if (dates[i] <= taskEnd) {
                spanCount++;
            } else {
                break;
            }
        }
        
        const statusBadge = task.is_archived ? '<span class="ml-2 text-xs bg-green-100 text-green-600 px-1.5 py-0.5 rounded-full">✓ Selesai</span>' : '';
        
        html += `
            <tr class="gantt-row" style="border-bottom: 1px solid #e5e7eb;" onclick="openTaskDetail(${task.id})">
                <td style="position: sticky; left: 0; background: white; padding: 12px;">
                    <div style="font-weight: 500;">${escapeHtml(task.name)} ${statusBadge}</div>
                    <div style="font-size: 11px; color: #6b7280;">${task.list_name || ''}</div>
                    <div style="font-size: 10px; color: #9ca3af;">📅 ${formatDate(task.start)} → ${formatDate(task.end)} (${spanCount} hari)</div>
                    ${task.assignees && task.assignees.length ? `<div style="font-size: 10px; color: #9ca3af;">👤 ${task.assignees.map(a => a.name).join(', ')}</div>` : ''}
                </td>
        `;
        
        // Render kolom timeline
        for (let i = 0; i < dates.length; i++) {
            if (i === startCol) {
                html += `<td colspan="${spanCount}" style="padding: 4px;">`;
                html += `
                    <div class="gantt-task-bar" style="background-color: ${color}; height: 42px; position: relative; overflow: hidden;">
                        <div style="position: absolute; left: 0; top: 0; height: 100%; width: ${progress}%; background-color: rgba(0,0,0,0.25);"></div>
                    </div>
                `;
                html += `</td>`;
                i += spanCount - 1;
            }
        }
        
        html += `</table>`;
    }
    
    html += `
                </tbody>
            </table>
        </div>
    `;
    
    container.innerHTML = html;
}

function formatDate(dateStr) {
    const date = new Date(dateStr);
    return `${date.getDate()}/${date.getMonth()+1}/${date.getFullYear()}`;
}

function getDayName(date) {
    const days = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
    return days[date.getDay()];
}

function getMonthName(date) {
    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
    return months[date.getMonth()];
}

function openTaskDetail(taskId) {
    if (typeof openTaskDetailModal === 'function') {
        openTaskDetailModal(taskId);
    } else {
        window.location.href = appUrl(`tasks/${taskId}/edit`);
    }
}

function applyFilters() {
    loadTasks();
}

function resetView() {
    document.getElementById('filterList').value = 'all';
    document.getElementById('filterPriority').value = 'all';
    document.getElementById('filterStatus').value = 'active';
    loadTasks();
}

function exportGantt() {
    const element = document.getElementById('gantt-chart');
    if (!element) return;
    
    showNotification('Mengeksport...', 'info');
    
    html2canvas(element, {
        scale: 2,
        backgroundColor: '#ffffff',
        logging: false
    }).then(canvas => {
        const link = document.createElement('a');
        const now = new Date();
        link.download = `gantt-${now.getTime()}.png`;
        link.href = canvas.toDataURL();
        link.click();
        showNotification('✓ Export berhasil!', 'success');
    }).catch(error => {
        console.error(error);
        showNotification('Gagal export', 'error');
    });
}

function showLoading(show) {
    const container = document.getElementById('gantt-chart');
    if (show && container) {
        container.innerHTML = `
            <div class="text-center py-20">
                <div class="spinner"></div>
                <p class="mt-2 text-gray-500">Memuat data...</p>
            </div>
        `;
    }
}

function showEmptyState(message) {
    const container = document.getElementById('gantt-chart');
    if (container) {
        container.innerHTML = `
            <div class="text-center py-20">
                <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                <p class="text-gray-500">${message}</p>
            </div>
        `;
    }
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
@endsection
