@extends('layouts.app')

@section('title', 'Calendar - ' . $board->name)

@section('content')
<style>
    /* Modern CSS Variables */
    :root {
        --primary-dark: #1e3a5f;
        --primary-light: #2d4a7c;
        --accent: #10b981;
        --accent-dark: #059669;
        --gray-50: #f9fafb;
        --gray-100: #f3f4f6;
        --gray-200: #e5e7eb;
        --gray-300: #d1d5db;
        --gray-400: #9ca3af;
        --gray-500: #6b7280;
        --gray-600: #4b5563;
        --gray-700: #374151;
        --gray-800: #1f2937;
        --gray-900: #111827;
        --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
        --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1);
        --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1);
        --shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1);
        --radius-sm: 0.5rem;
        --radius-md: 0.75rem;
        --radius-lg: 1rem;
        --radius-xl: 1.25rem;
    }

    /* FullCalendar Customization */
    .fc {
        background: white;
        border-radius: var(--radius-lg);
        padding: 1.25rem;
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--gray-200);
    }
    
    .fc .fc-toolbar-title {
        font-size: 1.25rem;
        font-weight: 700;
        background: linear-gradient(135deg, var(--primary-dark), var(--primary-light));
        background-clip: text;
        -webkit-background-clip: text;
        color: transparent;
        letter-spacing: -0.025em;
    }
    
    .fc .fc-button-primary {
        background-color: var(--primary-dark) !important;
        border-color: var(--primary-dark) !important;
        border-radius: var(--radius-md) !important;
        font-weight: 500 !important;
        padding: 0.5rem 1rem !important;
        transition: all 0.2s ease !important;
        text-transform: capitalize !important;
    }
    
    .fc .fc-button-primary:hover {
        background-color: var(--primary-light) !important;
        border-color: var(--primary-light) !important;
        transform: translateY(-1px);
        box-shadow: var(--shadow-md);
    }
    
    .fc .fc-button-primary:disabled {
        background-color: var(--gray-400) !important;
        border-color: var(--gray-400) !important;
        opacity: 0.6;
    }
    
    .fc .fc-button-primary:focus {
        box-shadow: 0 0 0 2px rgba(30, 58, 95, 0.2) !important;
    }
    
    .fc .fc-day-today {
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.05), rgba(16, 185, 129, 0.02)) !important;
        border: 1px solid rgba(16, 185, 129, 0.2) !important;
    }
    
    .fc-daygrid-day-frame {
        cursor: pointer;
        transition: all 0.2s ease;
        border-radius: var(--radius-sm);
        margin: 2px;
    }
    
    .fc-daygrid-day-frame:hover {
        background-color: var(--gray-50);
        transform: scale(1.01);
        box-shadow: var(--shadow-sm);
    }
    
    .fc-event {
        cursor: pointer;
        border-radius: var(--radius-sm);
        font-size: 0.75rem;
        font-weight: 500;
        transition: all 0.2s ease;
        border: none !important;
        padding: 4px 8px !important;
        margin: 2px 4px !important;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
    }
    
    .fc-event:hover {
        transform: translateY(-1px);
        filter: brightness(0.95);
        box-shadow: var(--shadow-md);
    }
    
    .fc-event-title {
        font-weight: 500;
        white-space: normal;
        word-wrap: break-word;
        line-height: 1.3;
    }
    
    .fc-event-main {
        overflow-wrap: break-word;
        word-wrap: break-word;
        white-space: normal;
    }
    
    .fc-daygrid-day-number {
        font-size: 0.875rem;
        font-weight: 500;
        color: var(--gray-700);
        padding: 0.5rem;
    }
    
    .fc-daygrid-day-top {
        display: flex;
        justify-content: flex-end;
    }

    /* Calendar Header - Sama dengan Activity Log */
    .calendar-header-modern {
        background: white;
        border-radius: 1rem;
        padding: 1.25rem;
        margin-bottom: 1.5rem;
        border: 1px solid #e5e7eb;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    }
    
    /* Tooltip Modern */
    .tooltip-modern {
        position: absolute;
        background: rgba(255, 255, 255, 0.98);
        backdrop-filter: blur(12px);
        border-radius: var(--radius-md);
        box-shadow: var(--shadow-xl);
        padding: 0.875rem;
        max-width: 280px;
        z-index: 1000;
        border: 1px solid var(--gray-200);
        pointer-events: none;
        transition: all 0.2s ease;
    }
    
    .tooltip-modern-title {
        font-weight: 700;
        background: linear-gradient(135deg, var(--primary-dark), var(--primary-light));
        background-clip: text;
        -webkit-background-clip: text;
        color: transparent;
        margin-bottom: 0.5rem;
        font-size: 0.875rem;
    }
    
    .tooltip-modern-list {
        font-size: 0.7rem;
        color: var(--gray-500);
        margin-bottom: 0.25rem;
        display: flex;
        align-items: center;
        gap: 0.375rem;
    }
    
    .tooltip-modern-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        padding: 0.25rem 0.5rem;
        border-radius: 9999px;
        font-size: 0.65rem;
        font-weight: 500;
    }
    
    /* Modal Modern */
    .modal-modern {
        border-radius: var(--radius-xl);
        box-shadow: var(--shadow-xl);
        border: none;
        overflow: hidden;
    }
    
    .modal-modern-header {
        background: linear-gradient(135deg, var(--gray-50), white);
        border-bottom: 1px solid var(--gray-200);
        padding: 1.25rem 1.5rem;
    }
    
    .modal-modern-body {
        padding: 1.5rem;
        max-height: 60vh;
        overflow-y: auto;
    }
    
    .modal-modern-footer {
        background: var(--gray-50);
        border-top: 1px solid var(--gray-200);
        padding: 1rem 1.5rem;
    }
    
    /* Badge Styles */
    .badge-priority-high {
        background-color: #fee2e2;
        color: #dc2626;
    }
    
    .badge-priority-medium {
        background-color: #fef3c7;
        color: #d97706;
    }
    
    .badge-priority-low {
        background-color: #d1fae5;
        color: #059669;
    }
    
    /* Animations */
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .animate-fade-in {
        animation: fadeIn 0.2s ease-out;
    }
    
    /* Custom Scrollbar */
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
    }
    
    .custom-scrollbar::-webkit-scrollbar-track {
        background: var(--gray-100);
        border-radius: 10px;
    }
    
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: var(--gray-300);
        border-radius: 10px;
    }
    
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: var(--gray-400);
    }
    
    /* Spinner */
    .spinner-modern {
        display: inline-block;
        width: 1.25rem;
        height: 1.25rem;
        border: 2px solid var(--gray-200);
        border-top-color: var(--primary-dark);
        border-radius: 50%;
        animation: spin 0.6s linear infinite;
    }
    
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
    
    /* Button Styles */
    .btn-modern {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        border-radius: var(--radius-md);
        font-weight: 500;
        font-size: 0.875rem;
        transition: all 0.2s ease;
        cursor: pointer;
        border: none;
    }
    
    .btn-modern-primary {
        background: linear-gradient(135deg, var(--primary-dark), var(--primary-light));
        color: white;
        box-shadow: var(--shadow-sm);
    }
    
    .btn-modern-primary:hover {
        transform: translateY(-1px);
        box-shadow: var(--shadow-md);
    }
    
    .btn-modern-secondary {
        background-color: var(--gray-100);
        color: var(--gray-700);
        border: 1px solid var(--gray-200);
    }
    
    .btn-modern-secondary:hover {
        background-color: var(--gray-200);
        transform: translateY(-1px);
    }
    
    /* Avatar Group */
    .avatar-group {
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }
    
    .avatar {
        width: 2rem;
        height: 2rem;
        border-radius: 9999px;
        background: linear-gradient(135deg, var(--primary-dark), var(--primary-light));
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 0.75rem;
        font-weight: 600;
        box-shadow: var(--shadow-sm);
        border: 2px solid white;
    }
    
    .avatar-sm {
        width: 1.75rem;
        height: 1.75rem;
        font-size: 0.65rem;
    }
</style>

<div class="min-h-screen bg-gray-50">
    <div class="container mx-auto px-4 py-8">
        <!-- Header - Sama dengan Activity Log -->
        <div class="calendar-header-modern">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-full bg-gradient-to-br from-[#1e3a5f] to-[#2d4a7c] flex items-center justify-center shadow-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-[#1e3a5f]">Calendar View</h1>
                        <p class="text-gray-500 text-sm">{{ $board->name }}</p>
                    </div>
                </div>
                <div class="flex gap-2 flex-wrap">
                    <button onclick="switchView('dayGridMonth')" id="monthViewBtn" class="btn-modern btn-modern-primary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                        </svg>
                        Monthly
                    </button>
                    <button onclick="switchView('timeGridWeek')" id="weekViewBtn" class="btn-modern btn-modern-secondary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7h16M4 12h16M4 17h16"></path>
                        </svg>
                        Weekly
                    </button>
                    <button onclick="switchView('timeGridDay')" id="dayViewBtn" class="btn-modern btn-modern-secondary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Daily
                    </button>
                    <a href="{{ route('boards.show', $board) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-all duration-200 text-sm font-medium ml-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to Board
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Calendar Container -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <div id="calendar"></div>
        </div>
    </div>
</div>

<!-- Modal Task Detail - Modern Design -->
<div id="taskDetailModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm overflow-y-auto h-full w-full z-50 transition-all duration-300">
    <div class="relative top-20 mx-auto p-0 border-0 w-full max-w-2xl shadow-2xl rounded-2xl bg-white animate-fade-in">
        <div class="modal-modern-header">
            <div class="flex justify-between items-start">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-[#1e3a5f] to-[#2d4a7c] flex items-center justify-center shadow-md">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-800" id="modalTaskTitle">Task Detail</h3>
                        <p class="text-sm text-gray-500 mt-0.5" id="modalTaskList">Loading...</p>
                    </div>
                </div>
                <button onclick="closeTaskDetailModal()" class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 p-2 rounded-lg transition-all duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
        
        <div class="modal-modern-body custom-scrollbar">
            <!-- Description Card -->
            <div class="bg-gradient-to-br from-gray-50 to-white rounded-xl p-4 mb-4 border border-gray-100">
                <div class="flex items-center gap-2 mb-2">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path>
                    </svg>
                    <h4 class="text-sm font-semibold text-gray-700">Description</h4>
                </div>
                <div id="modalTaskDescription" class="text-gray-600 text-sm leading-relaxed whitespace-pre-wrap">-</div>
            </div>
            
            <!-- Details Grid -->
            <div class="grid grid-cols-2 gap-3 mb-4">
                <div class="bg-gradient-to-br from-gray-50 to-white rounded-xl p-3 border border-gray-100">
                    <p class="text-xs text-gray-500 mb-1 flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Priority
                    </p>
                    <p id="modalTaskPriority" class="font-semibold text-sm">-</p>
                </div>
                <div class="bg-gradient-to-br from-gray-50 to-white rounded-xl p-3 border border-gray-100">
                    <p class="text-xs text-gray-500 mb-1 flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        Due Date
                    </p>
                    <p id="modalTaskDueDate" class="font-semibold text-sm">-</p>
                </div>
                <div class="bg-gradient-to-br from-gray-50 to-white rounded-xl p-3 border border-gray-100">
                    <p class="text-xs text-gray-500 mb-1 flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        List
                    </p>
                    <p id="modalTaskOriginalList" class="font-semibold text-sm">-</p>
                </div>
                <div class="bg-gradient-to-br from-gray-50 to-white rounded-xl p-3 border border-gray-100">
                    <p class="text-xs text-gray-500 mb-1 flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Assignees
                    </p>
                    <div id="modalTaskAssignees" class="flex gap-1 mt-1 flex-wrap">-</div>
                </div>
            </div>
            
            <!-- Labels Card -->
            <div class="bg-gradient-to-br from-gray-50 to-white rounded-xl p-4 border border-gray-100">
                <div class="flex items-center gap-2 mb-2">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l5 5a2 2 0 01.586 1.414V19a2 2 0 01-2 2H7a2 2 0 01-2-2V5a2 2 0 012-2z"></path>
                    </svg>
                    <h4 class="text-sm font-semibold text-gray-700">Labels</h4>
                </div>
                <div id="modalTaskLabels" class="flex flex-wrap gap-2">-</div>
            </div>
        </div>
        
        <div class="modal-modern-footer flex justify-end gap-3">
            <button onclick="closeTaskDetailModal()" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition cursor-pointer">Close</button>
            <button onclick="openEditFromModal()" class="px-4 py-2 bg-[#1e3a5f] text-white rounded-lg hover:bg-[#2d4a7c] transition cursor-pointer flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                </svg>
                Edit Task
            </button>
        </div>
    </div>
</div>

<!-- Modal Edit Task - Modern Design -->
<div id="editTaskModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm overflow-y-auto h-full w-full z-50 transition-all duration-300">
    <div class="relative top-20 mx-auto p-6 border-0 w-full max-w-md shadow-2xl rounded-2xl bg-white animate-fade-in">
        <div class="flex justify-between items-center mb-5 pb-3 border-b border-gray-100">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-[#1e3a5f] to-[#2d4a7c] flex items-center justify-center shadow-sm">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-800">Edit Task</h3>
            </div>
            <button onclick="closeEditTaskModal()" class="text-gray-400 hover:text-gray-600 p-1 rounded-lg transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <form id="editTaskForm" onsubmit="submitEditTask(event)" class="space-y-4">
            @csrf
            @method('PUT')
            <input type="hidden" id="editTaskId" name="task_id">
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Title *</label>
                <input type="text" id="editTaskTitle" name="title" required class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#1e3a5f] focus:border-transparent transition-all">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea id="editTaskDescription" name="description" rows="3" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#1e3a5f] focus:border-transparent resize-none"></textarea>
            </div>
            
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Priority</label>
                    <select id="editTaskPriority" name="priority" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#1e3a5f]">
                        <option value="low">🟢 Low Priority</option>
                        <option value="medium">🟡 Medium Priority</option>
                        <option value="high">🔴 High Priority</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Due Date</label>
                    <input type="date" id="editTaskDueDate" name="due_date" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#1e3a5f]">
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">👥 Assignees (Multiple)</label>
                <select id="editTaskAssignee" name="assignees[]" multiple class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#1e3a5f] min-h-[100px]">
                </select>
                <p class="text-xs text-gray-400 mt-1">💡 Hold Ctrl (Windows) or Cmd (Mac) to select multiple users</p>
            </div>
            
            <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                <button type="button" onclick="closeEditTaskModal()" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-all duration-200 cursor-pointer font-medium">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-[#1e3a5f] text-white rounded-xl hover:bg-[#2d4a7c] transition-all duration-200 cursor-pointer font-medium">Update Task</button>
            </div>
        </form>
    </div>
</div>

<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/id.js"></script>

<script>
    let calendar = null;
    let currentTaskId = null;
    @php
        $canEdit = isset($permission) && ($permission === 'owner' || $permission === 'edit');
    @endphp
    let permission = {{ $canEdit ? 'true' : 'false' }};
    
    document.addEventListener('DOMContentLoaded', function() {
        initCalendar();
    });
    
    function initCalendar() {
        const calendarEl = document.getElementById('calendar');
        
        calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'id',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: ''
            },
            buttonText: {
                today: 'Today',
                month: 'Month',
                week: 'Week',
                day: 'Day'
            },
            events: function(fetchInfo, successCallback, failureCallback) {
                fetch(`/boards/{{ $board->id }}/calendar/api?start=${fetchInfo.startStr}&end=${fetchInfo.endStr}`, {
                    headers: { 'Accept': 'application/json' }
                })
                .then(response => response.json())
                .then(events => successCallback(events))
                .catch(error => {
                    console.error('Error fetching events:', error);
                    failureCallback(error);
                });
            },
            eventClick: function(info) {
                const taskId = info.event.id;
                openTaskDetailModal(taskId);
            },
            eventDrop: function(info) {
                if (!permission) {
                    showNotification('You do not have permission to edit tasks', 'error');
                    info.revert();
                    return;
                }
                
                const taskId = info.event.id;
                const newDueDate = info.event.startStr;
                
                showUpdatingNotification();
                
                fetch(`/boards/{{ $board->id }}/calendar/update-due-date`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        task_id: taskId,
                        due_date: newDueDate
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification('Due date updated!', 'success');
                        calendar.refetchEvents();
                    } else {
                        showNotification('Failed to update: ' + (data.message || 'Unknown error'), 'error');
                        info.revert();
                        calendar.refetchEvents();
                    }
                })
                .catch(error => {
                    console.error('Error updating due date:', error);
                    showNotification('Error updating due date', 'error');
                    info.revert();
                    calendar.refetchEvents();
                });
            },
            eventDidMount: function(info) {
                let tooltip = null;
                
                info.el.addEventListener('mouseenter', function() {
                    const props = info.event.extendedProps;
                    const title = info.event.title;
                    const dueDate = new Date(info.event.startStr).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
                    const listName = props.list_name || 'Unknown';
                    const assignees = props.assignees || [];
                    const priority = props.priority || 'medium';
                    const priorityText = priority === 'high' ? 'High' : (priority === 'medium' ? 'Medium' : 'Low');
                    const priorityColor = priority === 'high' ? 'bg-red-100 text-red-600' : (priority === 'medium' ? 'bg-yellow-100 text-yellow-600' : 'bg-green-100 text-green-600');
                    
                    tooltip = document.createElement('div');
                    tooltip.className = 'tooltip-modern';
                    tooltip.innerHTML = `
                        <div class="tooltip-modern-title">${escapeHtml(title)}</div>
                        <div class="tooltip-modern-list">
                            <span class="tooltip-modern-badge ${priorityColor}">${priorityText}</span>
                            <span>📋 ${escapeHtml(listName)}</span>
                        </div>
                        <div class="tooltip-modern-list">📅 ${dueDate}</div>
                        ${assignees.length > 0 ? `
                            <div class="avatar-group mt-2">
                                ${assignees.map(a => `<div class="avatar avatar-sm" style="background: linear-gradient(135deg, #1e3a5f, #2d4a7c);">${a.name ? a.name.charAt(0) : '?'}</div>`).join('')}
                            </div>
                        ` : '<div class="tooltip-modern-list mt-1">👤 No assignee</div>'}
                    `;
                    document.body.appendChild(tooltip);
                    
                    const rect = info.el.getBoundingClientRect();
                    tooltip.style.left = rect.left + window.scrollX + 'px';
                    tooltip.style.top = rect.bottom + window.scrollY + 5 + 'px';
                });
                
                info.el.addEventListener('mouseleave', function() {
                    if (tooltip) {
                        tooltip.remove();
                        tooltip = null;
                    }
                });
            },
            editable: permission,
            droppable: false,
            dayMaxEvents: true,
            height: 'auto',
            contentHeight: 'auto',
            eventTimeFormat: { hour: '2-digit', minute: '2-digit', meridiem: false },
            eventDisplay: 'block',
            displayEventTime: false
        });
        
        calendar.render();
    }
    
    function switchView(view) {
        if (calendar) {
            calendar.changeView(view);
            
            document.getElementById('monthViewBtn').className = view === 'dayGridMonth' ? 'btn-modern btn-modern-primary' : 'btn-modern btn-modern-secondary';
            document.getElementById('weekViewBtn').className = view === 'timeGridWeek' ? 'btn-modern btn-modern-primary' : 'btn-modern btn-modern-secondary';
            document.getElementById('dayViewBtn').className = view === 'timeGridDay' ? 'btn-modern btn-modern-primary' : 'btn-modern btn-modern-secondary';
        }
    }
    
    // ==============================================
    // TASK DETAIL MODAL FUNCTIONS
    // ==============================================
    
    function openTaskDetailModal(taskId) {
        console.log('openTaskDetailModal called with taskId:', taskId);
        
        if (!taskId || taskId === 'null' || taskId === 'undefined' || taskId === 0 || taskId === '0') {
            console.error('Invalid task ID for detail modal:', taskId);
            showNotification('Invalid task ID', 'error');
            return;
        }
        
        const numericTaskId = parseInt(taskId);
        if (isNaN(numericTaskId) || numericTaskId <= 0) {
            console.error('Invalid numeric task ID:', numericTaskId);
            showNotification('Invalid task ID', 'error');
            return;
        }
        
        currentTaskId = numericTaskId;
        
        document.getElementById('modalTaskTitle').innerHTML = '<div class="spinner-modern"></div> Loading...';
        document.getElementById('modalTaskDescription').innerHTML = '<div class="spinner-modern"></div> Loading...';
        
        fetch(`/tasks/${numericTaskId}/edit`, {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
        })
        .then(response => {
            if (!response.ok) throw new Error('HTTP ' + response.status);
            return response.json();
        })
        .then(task => {
            document.getElementById('modalTaskTitle').textContent = task.title;
            document.getElementById('modalTaskList').innerHTML = `<span class="text-gray-400">📌</span> ${task.task_list?.name || '-'}`;
            document.getElementById('modalTaskDescription').textContent = task.description || 'No description provided.';
            
            const priorityEl = document.getElementById('modalTaskPriority');
            if (task.priority === 'high') priorityEl.innerHTML = '<span class="bg-red-100 text-red-600 px-2 py-1 rounded-lg text-xs font-semibold">🔴 High</span>';
            else if (task.priority === 'medium') priorityEl.innerHTML = '<span class="bg-yellow-100 text-yellow-600 px-2 py-1 rounded-lg text-xs font-semibold">🟡 Medium</span>';
            else priorityEl.innerHTML = '<span class="bg-green-100 text-green-600 px-2 py-1 rounded-lg text-xs font-semibold">🟢 Low</span>';
            
            const dueDateEl = document.getElementById('modalTaskDueDate');
            if (task.due_date) {
                const date = new Date(task.due_date);
                const isOverdue = date < new Date() && !task.archived_at;
                dueDateEl.innerHTML = `<span class="${isOverdue ? 'text-red-600' : 'text-gray-700'}">📅 ${date.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })}${isOverdue ? ' <span class="text-xs bg-red-100 text-red-600 px-2 py-0.5 rounded-full ml-2">Overdue</span>' : ''}</span>`;
            } else {
                dueDateEl.innerHTML = '<span class="text-gray-400">Not set</span>';
            }
            
            document.getElementById('modalTaskOriginalList').textContent = task.task_list?.name || '-';
            
            const assigneesEl = document.getElementById('modalTaskAssignees');
            if (task.assignees && task.assignees.length > 0) {
                assigneesEl.innerHTML = `<div class="flex items-center gap-1">${task.assignees.map(a => `<div class="w-7 h-7 rounded-full bg-[#1e3a5f] flex items-center justify-center text-white text-xs font-bold" title="${escapeHtml(a.name)}">${a.name.charAt(0)}</div>`).join('')}</div>`;
            } else {
                assigneesEl.innerHTML = '<span class="text-gray-400 text-sm">No assignees</span>';
            }
            
            const labelsEl = document.getElementById('modalTaskLabels');
            if (task.labels && task.labels.length > 0) {
                labelsEl.innerHTML = task.labels.map(l => `<span class="text-xs px-3 py-1.5 rounded-full" style="background-color: ${l.color}20; color: ${l.color}; border-left: 3px solid ${l.color}">${escapeHtml(l.name)}</span>`).join('');
            } else {
                labelsEl.innerHTML = '<span class="text-gray-400 text-sm">No labels</span>';
            }
            
            document.getElementById('taskDetailModal').classList.remove('hidden');
        })
        .catch(error => {
            console.error('Error loading task:', error);
            document.getElementById('modalTaskTitle').textContent = 'Error loading task';
            document.getElementById('modalTaskDescription').innerHTML = '<span class="text-red-500">Failed to load task details</span>';
        });
    }
    
    function closeTaskDetailModal() {
        document.getElementById('taskDetailModal').classList.add('hidden');
    }
    
    function openEditFromModal() {
        if (!currentTaskId) {
            showNotification('No task selected', 'error');
            return;
        }
        document.getElementById('taskDetailModal').classList.add('hidden');
        openEditTaskModal(currentTaskId);
    }
    
    // ==============================================
    // EDIT TASK MODAL FUNCTIONS
    // ==============================================
    
    function openEditTaskModal(taskId) {
        const numericTaskId = parseInt(taskId);
        if (isNaN(numericTaskId) || numericTaskId <= 0) {
            showNotification('Invalid task ID', 'error');
            return;
        }
        
        showNotification('Loading task data...', 'info');
        
        fetch(`/tasks/${numericTaskId}/assignable-users`, {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
        })
        .then(response => response.json())
        .then(data => {
            const assigneeSelect = document.getElementById('editTaskAssignee');
            if (assigneeSelect && data.all_users && !data.error) {
                assigneeSelect.innerHTML = '';
                data.all_users.forEach(user => {
                    const isSelected = data.assigned_users && data.assigned_users.includes(user.id);
                    assigneeSelect.innerHTML += `<option value="${user.id}" ${isSelected ? 'selected' : ''}>${escapeHtml(user.name)} (${escapeHtml(user.username)})</option>`;
                });
            }
        })
        .catch(error => console.error('Error loading users:', error));
        
        fetch(`/tasks/${numericTaskId}/edit`, {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
        })
        .then(response => {
            if (!response.ok) throw new Error('HTTP ' + response.status);
            return response.json();
        })
        .then(task => {
            document.getElementById('editTaskId').value = task.id;
            document.getElementById('editTaskTitle').value = task.title;
            document.getElementById('editTaskDescription').value = task.description || '';
            document.getElementById('editTaskPriority').value = task.priority || 'medium';
            
            if (task.due_date) {
                const formattedDate = new Date(task.due_date).toISOString().split('T')[0];
                document.getElementById('editTaskDueDate').value = formattedDate;
            } else {
                document.getElementById('editTaskDueDate').value = '';
            }
            
            document.getElementById('editTaskModal').classList.remove('hidden');
        })
        .catch(error => {
            console.error('Error fetching task:', error);
            showNotification('Failed to load task data: ' + error.message, 'error');
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
        selectedAssignees.forEach(assigneeId => formData.append('assignees[]', assigneeId));
        
        showNotification('Updating task...', 'info');
        
        fetch(`/tasks/${taskId}`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.id || data.success) {
                showNotification('✓ Task updated!', 'success');
                closeEditTaskModal();
                if (calendar) calendar.refetchEvents();
                if (currentTaskId) openTaskDetailModal(currentTaskId);
            } else {
                showNotification('Failed to update task: ' + (data.message || 'Unknown error'), 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error updating task: ' + error.message, 'error');
        });
    }
    
    // ==============================================
    // UTILITY FUNCTIONS
    // ==============================================
    
    function showUpdatingNotification() {
        const notification = document.createElement('div');
        notification.className = 'fixed bottom-4 right-4 z-50 px-4 py-2 rounded-lg shadow-lg text-white bg-blue-500 animate-fade-in';
        notification.textContent = 'Updating due date...';
        document.body.appendChild(notification);
        setTimeout(() => notification.remove(), 1500);
    }
    
    function showNotification(message, type) {
        const existing = document.querySelectorAll('.calendar-notification');
        existing.forEach(n => n.remove());
        
        const notification = document.createElement('div');
        notification.className = `calendar-notification fixed bottom-4 right-4 z-50 px-4 py-2 rounded-lg shadow-lg text-white ${type === 'success' ? 'bg-green-500' : 'bg-red-500'} animate-fade-in`;
        notification.textContent = message;
        document.body.appendChild(notification);
        setTimeout(() => notification.remove(), 3000);
    }
    
    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
</script>
@endsection