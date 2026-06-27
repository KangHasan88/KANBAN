@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="container mx-auto px-4 py-8">
        <!-- Header - White Background (like board) -->
        <div class="bg-white rounded-xl shadow-md p-5 mb-6">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <div class="flex items-center gap-3 mb-1">
                        <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center">
                            <span class="text-xl">📦</span>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold" style="color: #1e3a5f;">Archived Tasks</h1>
                            <p class="text-gray-500 text-sm mt-0.5">Board: {{ $board->name }}</p>
                        </div>
                    </div>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('boards.show', $board) }}" 
                       class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-all duration-200 text-sm font-medium">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to Board
                    </a>
                    @php
                        $canEdit = ($permission === 'owner' || $permission === 'edit');
                    @endphp
                    @if($canEdit)
                    <button onclick="toggleBulkRestoreMode()" id="bulkRestoreModeBtn" class="btn-outline text-sm">
                        ☑️ Bulk Restore
                    </button>
                    @endif
                    @if(auth()->user()->isAdmin())
                    <button onclick="clearAllArchived()" 
                            class="inline-flex items-center gap-2 px-4 py-2 bg-red-50 text-red-600 rounded-xl hover:bg-red-100 transition-all duration-200 text-sm font-medium">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Clear All
                    </button>
                    @endif
                </div>
            </div>
        </div>

        @if($archivedTasks->count() > 0)
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100 hover:shadow-md transition-all duration-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Total Archived</p>
                        <p class="text-2xl font-bold text-[#1e3a5f]">{{ $archivedTasks->count() }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                        <span class="text-blue-600 text-lg">📦</span>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100 hover:shadow-md transition-all duration-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">This Month</p>
                        <p class="text-2xl font-bold text-[#1e3a5f]">
                            {{ $archivedTasks->filter(function($task) { return $task->archived_at->month == now()->month; })->count() }}
                        </p>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center">
                        <span class="text-green-600 text-lg">📅</span>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100 hover:shadow-md transition-all duration-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">With Attachments</p>
                        <p class="text-2xl font-bold text-[#1e3a5f]">
                            {{ $archivedTasks->filter(function($task) { return $task->attachments->count() > 0; })->count() }}
                        </p>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center">
                        <span class="text-purple-600 text-lg">📎</span>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-4 border border-gray-100 hover:shadow-md transition-all duration-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">With Comments</p>
                        <p class="text-2xl font-bold text-[#1e3a5f]">
                            {{ $archivedTasks->filter(function($task) { return $task->comments->count() > 0; })->count() }}
                        </p>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-yellow-100 flex items-center justify-center">
                        <span class="text-yellow-600 text-lg">💬</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search & Filter Bar -->
        <div class="bg-white rounded-xl shadow-sm p-4 mb-6 border border-gray-100">
            <div class="flex flex-col sm:flex-row gap-4">
                <div class="flex-1 relative">
                    <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <input type="text" id="searchArchivedTask" placeholder="Search archived tasks..." 
                           class="w-full pl-9 pr-4 py-2 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#1e3a5f] focus:border-transparent transition-all">
                </div>
                <div class="flex gap-3">
                    <select id="filterList" class="px-4 py-2 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#1e3a5f] text-sm bg-white">
                        <option value="">All Lists</option>
                        @foreach($board->lists as $list)
                        <option value="{{ $list->name }}">{{ $list->name }}</option>
                        @endforeach
                    </select>
                    <select id="filterDate" class="px-4 py-2 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#1e3a5f] text-sm bg-white">
                        <option value="">All Time</option>
                        <option value="today">Today</option>
                        <option value="week">This Week</option>
                        <option value="month">This Month</option>
                        <option value="year">This Year</option>
                    </select>
                    <button onclick="resetFilters()" class="px-4 py-2 text-gray-500 hover:text-[#1e3a5f] transition-colors">
                        Reset
                    </button>
                </div>
            </div>
        </div>

        <!-- Floating Bulk Restore Action Bar -->
        <div id="bulkRestoreBar" class="hidden fixed bottom-6 left-1/2 transform -translate-x-1/2 z-50 bg-[#1e3a5f] text-white rounded-xl shadow-2xl px-6 py-3 flex items-center gap-4 animate-bounce-in">
            <span id="bulkRestoreSelectedCount" class="font-semibold">0 selected</span>
            <button onclick="bulkRestore()" class="px-4 py-2 bg-green-600 hover:bg-green-700 rounded-lg transition text-sm font-medium flex items-center gap-2">
                🔄 Restore Selected
            </button>
            <button onclick="cancelBulkRestoreMode()" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 rounded-lg transition text-sm font-medium">
                Cancel
            </button>
        </div>

        <!-- Archived Tasks Table -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
            <div class="overflow-x-auto">
                <table class="w-full" id="archivedTasksTable">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-4 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-10 bulk-checkbox-th" style="display: none;">
                                <input type="checkbox" id="selectAllCheckbox" class="rounded border-gray-300 text-green-600 focus:ring-green-500 w-4 h-4">
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Task</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Original List</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Archived Date</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Assignees</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100" id="archivedTasksBody">
                        @foreach($archivedTasks as $task)
                        <tr class="archived-task-row group transition-all duration-200 hover:bg-gray-50/80 cursor-pointer" 
                            data-task-id="{{ $task->id }}"
                            data-task-title="{{ strtolower($task->title) }}"
                            data-list-name="{{ $task->taskList->name }}"
                            data-archived-date="{{ $task->archived_at->format('Y-m-d') }}"
                            onclick="if (!bulkRestoreModeActive) openArchivedTaskDetail({{ $task->id }})">
                            <td class="px-4 py-4 bulk-checkbox-cell" style="display: none;" onclick="event.stopPropagation()">
                                <input type="checkbox" class="task-restore-checkbox w-4 h-4 rounded border-gray-300 text-green-600 focus:ring-green-500" data-task-id="{{ $task->id }}">
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center group-hover:bg-[#1e3a5f]/10 transition-colors">
                                        <span class="text-gray-500 group-hover:text-[#1e3a5f] transition-colors">📋</span>
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <p class="font-semibold text-gray-800 group-hover:text-[#1e3a5f] transition-colors">{{ $task->title }}</p>
                                            <div class="flex items-center gap-1">
                                                @if($task->attachments->count() > 0)
                                                <span class="inline-flex items-center gap-0.5 text-xs text-gray-400" title="{{ $task->attachments->count() }} attachment(s)">
                                                    📎 <span class="text-[10px]">{{ $task->attachments->count() }}</span>
                                                </span>
                                                @endif
                                                @if($task->comments->count() > 0)
                                                <span class="inline-flex items-center gap-0.5 text-xs text-gray-400" title="{{ $task->comments->count() }} comment(s)">
                                                    💬 <span class="text-[10px]">{{ $task->comments->count() }}</span>
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                        @if($task->description)
                                        <p class="text-sm text-gray-400 line-clamp-1 mt-0.5">{{ Str::limit($task->description, 70) }}</p>
                                        @endif
                                        @if($task->labels->count() > 0)
                                        <div class="flex flex-wrap gap-1 mt-2">
                                            @foreach($task->labels->take(2) as $label)
                                            <span class="text-xs px-2 py-0.5 rounded-full" 
                                                  style="background-color: {{ $label->color }}20; color: {{ $label->color }};">
                                                {{ $label->name }}
                                            </span>
                                            @endforeach
                                            @if($task->labels->count() > 2)
                                            <span class="text-xs text-gray-400">+{{ $task->labels->count() - 2 }}</span>
                                            @endif
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium"
                                      style="background-color: {{ $task->taskList->color }}20; color: {{ $task->taskList->color }};">
                                    <span class="w-1.5 h-1.5 rounded-full" style="background-color: {{ $task->taskList->color }};"></span>
                                    {{ $task->taskList->name }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <span class="text-sm text-gray-600">{{ $task->archived_at->format('d M Y, H:i') }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center -space-x-2">
                                    @forelse($task->assignees->take(3) as $assignee)
                                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-[#1e3a5f] to-[#2d4a7c] flex items-center justify-center text-white text-xs font-bold ring-2 ring-white shadow-sm" 
                                         title="{{ $assignee->name }}">
                                        {{ substr($assignee->name, 0, 1) }}
                                    </div>
                                    @empty
                                    <div class="flex items-center gap-1 text-gray-400 text-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                        <span>Unassigned</span>
                                    </div>
                                    @endforelse
                                    @if($task->assignees->count() > 3)
                                    <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center text-gray-600 text-xs font-bold ring-2 ring-white">
                                        +{{ $task->assignees->count() - 3 }}
                                    </div>
                                    @endif
                                </div>
                             </td>
                            <td class="px-6 py-4 text-right" onclick="event.stopPropagation()">
                                <div class="flex justify-end gap-2">
                                    <button onclick="unarchiveTask({{ $task->id }}, '{{ addslashes($task->title) }}')" 
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-green-50 text-green-700 rounded-lg hover:bg-green-100 transition-all duration-200 text-sm font-medium">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                        </svg>
                                        Restore
                                    </button>
                                    @if(auth()->user()->isAdmin())
                                    <button onclick="permanentlyDeleteTask({{ $task->id }}, '{{ addslashes($task->title) }}')" 
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-all duration-200 text-sm font-medium">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                        Delete
                                    </button>
                                    @endif
                                </div>
                             </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Footer -->
        <div class="flex flex-col sm:flex-row justify-between items-center gap-4 mt-6">
            <div class="text-sm text-gray-500">
                Showing <span id="visibleCount">{{ $archivedTasks->count() }}</span> of {{ $archivedTasks->count() }} archived tasks
            </div>
            <div class="flex items-center gap-2">
                <button onclick="exportArchivedTasks()" 
                        class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-all duration-200 text-sm font-medium">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    Export CSV
                </button>
            </div>
        </div>

        @else
        <!-- Empty State -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
            <div class="max-w-md mx-auto">
                <div class="w-24 h-24 mx-auto mb-6 bg-gray-100 rounded-full flex items-center justify-center">
                    <span class="text-5xl">📦</span>
                </div>
                <h3 class="text-xl font-semibold text-gray-800 mb-2">No Archived Tasks</h3>
                <p class="text-gray-500 mb-6">Tasks you archive will appear here. Archive tasks from your board to keep it clean.</p>
                <a href="{{ route('boards.show', $board) }}" 
                   class="inline-flex items-center gap-2 px-6 py-2.5 bg-[#1e3a5f] text-white rounded-xl hover:bg-[#2d4a7c] transition-all duration-200 font-medium">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Board
                </a>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Modal untuk melihat detail task archived -->
<div id="archivedTaskDetailModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-10 mx-auto p-6 border-0 w-full max-w-3xl shadow-xl rounded-xl bg-white">
        <div class="flex justify-between items-start mb-4 pb-3 border-b border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-gray-100 flex items-center justify-center text-gray-500 text-xl">
                    📦
                </div>
                <div>
                    <h3 class="text-xl font-bold text-[#1e3a5f]" id="archivedTaskDetailTitle">Archived Task Detail</h3>
                    <p class="text-sm text-gray-500 mt-0.5" id="archivedTaskDetailList">Loading...</p>
                </div>
            </div>
            <button onclick="closeArchivedTaskDetailModal()" class="text-gray-400 hover:text-gray-600 text-2xl transition">&times;</button>
        </div>
        
        <div class="max-h-[60vh] overflow-y-auto pr-2 space-y-5">
            <div class="bg-gray-50 rounded-xl p-3 flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center">
                    <span class="text-gray-500">📦</span>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Status</p>
                    <p class="text-sm font-medium text-gray-700">Archived <span id="archivedDateBadge" class="text-gray-400 text-xs ml-2"></span></p>
                </div>
            </div>
            
            <div>
                <h4 class="text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                    <span>🏷️</span> Labels
                </h4>
                <div id="archivedTaskDetailLabels" class="flex flex-wrap gap-2">
                    <span class="text-gray-400 text-sm">No labels</span>
                </div>
            </div>
            
            <div>
                <h4 class="text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                    <span>📝</span> Description
                </h4>
                <div id="archivedTaskDetailDescription" class="bg-gray-50 rounded-xl p-4 text-gray-700 text-sm leading-relaxed" style="white-space: pre-wrap; word-wrap: break-word;">
                    No description
                </div>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="bg-gray-50 rounded-xl p-3">
                    <p class="text-xs text-gray-500 mb-1">Priority</p>
                    <div id="archivedTaskDetailPriority" class="font-semibold">-</div>
                </div>
                <div class="bg-gray-50 rounded-xl p-3">
                    <p class="text-xs text-gray-500 mb-1">Due Date</p>
                    <div id="archivedTaskDetailDueDate" class="font-semibold">-</div>
                </div>
                <div class="bg-gray-50 rounded-xl p-3">
                    <p class="text-xs text-gray-500 mb-1">Assigned To</p>
                    <div id="archivedTaskDetailAssignee" class="font-semibold flex items-center gap-2 flex-wrap">-</div>
                </div>
                <div class="bg-gray-50 rounded-xl p-3">
                    <p class="text-xs text-gray-500 mb-1">Archived At</p>
                    <div id="archivedTaskDetailArchivedAt" class="font-semibold text-sm">-</div>
                </div>
            </div>
            
            <div class="border-t border-gray-100 pt-3">
                <h4 class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
                    <span>✅</span> Checklists
                </h4>
                <div id="archivedChecklistsContainer" class="space-y-3">
                    <div class="text-center text-gray-400 text-sm py-4">Loading checklists...</div>
                </div>
            </div>
            
            <div class="border-t border-gray-100 pt-3">
                <h4 class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
                    <span>📎</span> Attachments
                    <span id="archivedAttachmentsCount" class="text-xs bg-gray-100 rounded-full px-2 py-0.5">0</span>
                </h4>
                <div id="archivedAttachmentsContainer" class="space-y-2">
                    <div class="text-center text-gray-400 text-sm py-2">Loading attachments...</div>
                </div>
            </div>
            
            <div class="border-t border-gray-100 pt-3">
                <h4 class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
                    <span>💬</span> Comments
                    <span id="archivedCommentsCount" class="text-xs bg-gray-100 rounded-full px-2 py-0.5">0</span>
                </h4>
                <div id="archivedCommentsList" class="space-y-3 max-h-80 overflow-y-auto mb-3 pr-1">
                    <div class="text-center text-gray-400 text-sm py-4">Loading comments...</div>
                </div>
            </div>
            
            <div class="bg-blue-50 rounded-xl p-4">
                <div class="flex justify-between items-center flex-wrap gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                            <span class="text-blue-600 text-lg">📋</span>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-blue-800">Activity History</p>
                            <p class="text-xs text-gray-500">Lihat semua perubahan pada task ini</p>
                        </div>
                    </div>
                    <button onclick="if(currentArchivedTaskId) { closeArchivedTaskDetailModal(); showTaskActivityV2(currentArchivedTaskId); } else { alert('Error: Task ID not found'); }" 
                            class="inline-flex items-center gap-2 px-4 py-2 bg-white text-blue-600 rounded-xl hover:bg-blue-50 transition-all duration-200 text-sm font-medium shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        View History
                    </button>
                </div>
            </div>
        </div>
        
        <div class="mt-6 flex justify-end gap-3 border-t border-gray-100 pt-4">
            <button onclick="closeArchivedTaskDetailModal()" class="px-5 py-2 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-all duration-200 font-medium">Close</button>
            <button onclick="restoreFromModal()" class="inline-flex items-center gap-2 px-5 py-2 bg-green-600 text-white rounded-xl hover:bg-green-700 transition-all duration-200 font-medium shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                Restore Task
            </button>
        </div>
    </div>
</div>

<style>
    .line-clamp-1 {
        display: -webkit-box;
        -webkit-line-clamp: 1;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .btn-outline {
        background-color: transparent;
        color: #1e3a5f;
        border: 1px solid #1e3a5f;
        transition: all 0.2s ease;
        border-radius: 0.5rem;
        padding: 0.5rem 1rem;
        font-weight: 500;
        cursor: pointer;
    }
    .btn-outline:hover {
        background-color: #1e3a5f;
        color: white;
        transform: translateY(-1px);
    }
    .btn-primary {
        background-color: #1e3a5f;
        color: white;
        transition: all 0.2s ease;
        border-radius: 0.5rem;
        padding: 0.5rem 1rem;
        font-weight: 500;
        cursor: pointer;
        border: none;
    }
    .btn-primary:hover {
        background-color: #2d4a7c;
        transform: translateY(-1px);
    }
    @keyframes bounce-in {
        0% { transform: translateX(-50%) scale(0.8); opacity: 0; }
        80% { transform: translateX(-50%) scale(1.05); }
        100% { transform: translateX(-50%) scale(1); opacity: 1; }
    }
    .animate-bounce-in {
        animation: bounce-in 0.3s ease-out;
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
</style>

<script>
// ==============================================
// BULK RESTORE FUNCTIONS
// ==============================================

let bulkRestoreModeActive = false;
let selectedRestoreTasks = new Set();

function toggleBulkRestoreMode() {
    bulkRestoreModeActive = !bulkRestoreModeActive;
    const bulkBtn = document.getElementById('bulkRestoreModeBtn');
    const actionBar = document.getElementById('bulkRestoreBar');
    const checkboxes = document.querySelectorAll('.task-restore-checkbox');
    const checkboxCells = document.querySelectorAll('.bulk-checkbox-cell');
    const checkboxTh = document.querySelector('.bulk-checkbox-th');
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
    
    if (bulkRestoreModeActive) {
        if (bulkBtn) {
            bulkBtn.textContent = '❌ Exit Bulk Mode';
            bulkBtn.classList.remove('btn-outline');
            bulkBtn.classList.add('btn-primary');
        }
        if (actionBar) actionBar.classList.remove('hidden');
        checkboxCells.forEach(cell => {
            cell.style.display = 'table-cell';
        });
        if (checkboxTh) checkboxTh.style.display = 'table-cell';
        if (selectAllCheckbox) selectAllCheckbox.style.display = 'inline-block';
        selectedRestoreTasks.clear();
        checkboxes.forEach(cb => {
            cb.checked = false;
        });
        updateBulkRestoreSelectedCount();
    } else {
        cancelBulkRestoreMode();
    }
}

function cancelBulkRestoreMode() {
    bulkRestoreModeActive = false;
    selectedRestoreTasks.clear();
    const bulkBtn = document.getElementById('bulkRestoreModeBtn');
    const actionBar = document.getElementById('bulkRestoreBar');
    const checkboxes = document.querySelectorAll('.task-restore-checkbox');
    const checkboxCells = document.querySelectorAll('.bulk-checkbox-cell');
    const checkboxTh = document.querySelector('.bulk-checkbox-th');
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
    
    if (bulkBtn) {
        bulkBtn.textContent = '☑️ Bulk Restore';
        bulkBtn.classList.add('btn-outline');
        bulkBtn.classList.remove('btn-primary');
    }
    if (actionBar) actionBar.classList.add('hidden');
    checkboxCells.forEach(cell => {
        cell.style.display = 'none';
    });
    if (checkboxTh) checkboxTh.style.display = 'none';
    if (selectAllCheckbox) selectAllCheckbox.style.display = 'none';
    checkboxes.forEach(cb => {
        cb.checked = false;
    });
}

function updateBulkRestoreSelectedCount() {
    const countSpan = document.getElementById('bulkRestoreSelectedCount');
    if (countSpan) {
        countSpan.textContent = `${selectedRestoreTasks.size} selected`;
    }
}

// Event listener untuk checkbox individual
document.addEventListener('change', function(e) {
    if (e.target && e.target.classList.contains('task-restore-checkbox')) {
        const taskId = parseInt(e.target.dataset.taskId);
        if (e.target.checked) {
            selectedRestoreTasks.add(taskId);
        } else {
            selectedRestoreTasks.delete(taskId);
        }
        updateBulkRestoreSelectedCount();
        
        // Update select all checkbox status
        const allCheckboxes = document.querySelectorAll('.task-restore-checkbox');
        const selectAll = document.getElementById('selectAllCheckbox');
        if (selectAll && allCheckboxes.length > 0) {
            const allChecked = Array.from(allCheckboxes).every(cb => cb.checked);
            selectAll.checked = allChecked;
        }
    }
});

// Select All functionality
document.addEventListener('click', function(e) {
    if (e.target && e.target.id === 'selectAllCheckbox') {
        const isChecked = e.target.checked;
        const allCheckboxes = document.querySelectorAll('.task-restore-checkbox');
        allCheckboxes.forEach(cb => {
            cb.checked = isChecked;
            const taskId = parseInt(cb.dataset.taskId);
            if (isChecked) {
                selectedRestoreTasks.add(taskId);
            } else {
                selectedRestoreTasks.delete(taskId);
            }
        });
        updateBulkRestoreSelectedCount();
    }
});

function bulkRestore() {
    if (selectedRestoreTasks.size === 0) {
        showNotification('No tasks selected for restore', 'error');
        return;
    }
    
    const taskIds = Array.from(selectedRestoreTasks);
    const taskCount = taskIds.length;
    
    if (!confirm(`Restore ${taskCount} archived task${taskCount > 1 ? 's' : ''} to their original lists?`)) {
        return;
    }
    
    showNotification(`Restoring ${taskCount} tasks...`, 'info');
    
    fetch('{{ route("tasks.bulk-restore") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        credentials: 'same-origin',
        body: JSON.stringify({ task_ids: taskIds })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(`✓ ${data.restored_count} task(s) restored!`, 'success');
            setTimeout(() => location.reload(), 800);
        } else {
            showNotification('Failed to restore: ' + (data.message || 'Unknown error'), 'error');
        }
    })
    .catch(error => {
        console.error('Bulk restore error:', error);
        showNotification('Error restoring tasks', 'error');
    });
}

// ==============================================
// SEARCH & FILTER FUNCTIONS
// ==============================================

function filterArchivedTasks() {
    const searchTerm = document.getElementById('searchArchivedTask')?.value.toLowerCase() || '';
    const filterList = document.getElementById('filterList')?.value || '';
    const filterDate = document.getElementById('filterDate')?.value || '';
    const today = new Date();
    
    const rows = document.querySelectorAll('#archivedTasksBody tr');
    let visibleCount = 0;
    
    rows.forEach(row => {
        let show = true;
        
        if (searchTerm) {
            const title = row.getAttribute('data-task-title') || '';
            if (!title.includes(searchTerm)) show = false;
        }
        
        if (show && filterList) {
            const listName = row.getAttribute('data-list-name') || '';
            if (listName !== filterList) show = false;
        }
        
        if (show && filterDate) {
            const archivedDateStr = row.getAttribute('data-archived-date') || '';
            const archivedDate = new Date(archivedDateStr);
            
            switch(filterDate) {
                case 'today':
                    if (archivedDate.toDateString() !== today.toDateString()) show = false;
                    break;
                case 'week':
                    const weekAgo = new Date(today);
                    weekAgo.setDate(today.getDate() - 7);
                    if (archivedDate < weekAgo) show = false;
                    break;
                case 'month':
                    if (archivedDate.getMonth() !== today.getMonth() || archivedDate.getFullYear() !== today.getFullYear()) show = false;
                    break;
                case 'year':
                    if (archivedDate.getFullYear() !== today.getFullYear()) show = false;
                    break;
            }
        }
        
        row.style.display = show ? '' : 'none';
        if (show) visibleCount++;
    });
    
    const visibleCountSpan = document.getElementById('visibleCount');
    if (visibleCountSpan) visibleCountSpan.textContent = visibleCount;
}

function resetFilters() {
    document.getElementById('searchArchivedTask').value = '';
    document.getElementById('filterList').value = '';
    document.getElementById('filterDate').value = '';
    filterArchivedTasks();
}

// ==============================================
// EXPORT TO CSV
// ==============================================

function exportArchivedTasks() {
    const rows = document.querySelectorAll('#archivedTasksBody tr');
    const visibleRows = Array.from(rows).filter(row => row.style.display !== 'none');
    
    if (visibleRows.length === 0) {
        alert('No tasks to export');
        return;
    }
    
    let csvContent = "\uFEFFTask Title,Original List,Archived Date,Assignees,Attachments,Comments,Labels\n";
    
    visibleRows.forEach(row => {
        const title = row.querySelector('td:first-child .font-semibold')?.textContent || '';
        const listName = row.querySelector('td:nth-child(2) span')?.textContent?.trim() || '';
        const dateCell = row.querySelector('td:nth-child(3) .text-gray-600')?.textContent?.trim() || '';
        const assignees = Array.from(row.querySelectorAll('td:nth-child(4) .rounded-full[title]'))
            .map(avatar => avatar.getAttribute('title'))
            .join(', ') || 'Unassigned';
        const attachments = row.querySelector('td:first-child .flex.items-center.gap-1 .text-gray-400:first-child')?.textContent?.trim() || '0';
        const comments = row.querySelector('td:first-child .flex.items-center.gap-1 .text-gray-400:last-child')?.textContent?.trim() || '0';
        const labels = Array.from(row.querySelectorAll('td:first-child .flex.flex-wrap.gap-1 span'))
            .map(label => label.textContent?.trim())
            .join(', ') || '-';
        
        const escapedTitle = `"${title.replace(/"/g, '""')}"`;
        const escapedList = `"${listName.replace(/"/g, '""')}"`;
        const escapedAssignees = `"${assignees.replace(/"/g, '""')}"`;
        const escapedLabels = `"${labels.replace(/"/g, '""')}"`;
        
        csvContent += `${escapedTitle},${escapedList},${dateCell},${escapedAssignees},${attachments},${comments},${escapedLabels}\n`;
    });
    
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    link.setAttribute('href', url);
    link.setAttribute('download', `archived_tasks_${new Date().toISOString().split('T')[0]}.csv`);
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    URL.revokeObjectURL(url);
}

// ==============================================
// NOTIFICATION FUNCTION
// ==============================================

function showNotification(message, type) {
    const existing = document.querySelectorAll('.kanban-notification');
    existing.forEach(n => n.remove());
    
    const colors = { success: 'bg-green-500', error: 'bg-red-500', info: 'bg-blue-500' };
    const notification = document.createElement('div');
    notification.className = `kanban-notification fixed bottom-4 right-4 z-50 px-4 py-2 rounded-lg shadow-lg text-white ${colors[type]} transition-opacity duration-300`;
    notification.textContent = message;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.opacity = '0';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// ==============================================
// UNARCHIVE, DELETE, CLEAR FUNCTIONS
// ==============================================

function unarchiveTask(taskId, taskTitle) {
    if (!confirm(`Restore task "${taskTitle}" to its original list?`)) return;
    
    fetch(`/tasks/${taskId}/unarchive`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Failed to restore task: ' + (data.message || 'Unknown error'));
        }
    });
}

function permanentlyDeleteTask(taskId, taskTitle) {
    if (!confirm(`⚠️ PERMANENT DELETE\n\nDelete "${taskTitle}" permanently?\n\nThis action CANNOT be undone!`)) return;
    
    fetch(`/tasks/${taskId}/force-delete`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Failed to delete task: ' + (data.message || 'Unknown error'));
        }
    });
}

function clearAllArchived() {
    if (!confirm('⚠️ Clear all archived tasks?\n\nThis will permanently delete ALL archived tasks in this board. This action cannot be undone!')) return;
    
    fetch('{{ route("boards.archived.clear", $board) }}', {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Failed to clear archived tasks: ' + (data.message || 'Unknown error'));
        }
    });
}

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// ==============================================
// ARCHIVED TASK DETAIL MODAL FUNCTIONS
// ==============================================

let currentArchivedTaskId = null;
let currentArchivedTaskTitle = null;

function openArchivedTaskDetail(taskId) {
    if (bulkRestoreModeActive) {
        // If in bulk mode, don't open detail modal
        return;
    }
    
    if (!taskId || taskId === 'null' || taskId === 'undefined' || taskId <= 0) {
        console.error('Invalid task ID:', taskId);
        alert('Error: Invalid task ID');
        return;
    }
    
    currentArchivedTaskId = taskId;
    
    const modal = document.getElementById('archivedTaskDetailModal');
    if (modal) modal.classList.remove('hidden');
    
    document.getElementById('archivedTaskDetailTitle').innerHTML = '<div class="spinner w-5 h-5 border-2 border-blue-500 border-t-transparent rounded-full animate-spin"></div> Loading...';
    document.getElementById('archivedTaskDetailList').textContent = 'Loading...';
    document.getElementById('archivedTaskDetailDescription').innerHTML = '<div class="spinner w-5 h-5 border-2 border-gray-300 border-t-blue-500 rounded-full animate-spin"></div> Loading...';
    
    fetch(`/tasks/${taskId}/edit`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
        },
        credentials: 'same-origin'
    })
    .then(response => {
        if (!response.ok) throw new Error('HTTP ' + response.status);
        return response.json();
    })
    .then(task => {
        currentArchivedTaskTitle = task.title;
        
        document.getElementById('archivedTaskDetailTitle').innerHTML = escapeHtml(task.title);
        
        if (task.task_list && task.task_list.name) {
            document.getElementById('archivedTaskDetailList').innerHTML = `<span class="text-gray-500">📌 Original List:</span> ${escapeHtml(task.task_list.name)}`;
        } else {
            document.getElementById('archivedTaskDetailList').innerHTML = '<span class="text-gray-500">List:</span> -';
        }
        
        const archivedDateBadge = document.getElementById('archivedDateBadge');
        if (task.archived_at) {
            const archivedDate = new Date(task.archived_at);
            archivedDateBadge.innerHTML = `• Archived on ${archivedDate.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })}`;
        } else {
            archivedDateBadge.innerHTML = '';
        }
        
        const labelsContainer = document.getElementById('archivedTaskDetailLabels');
        if (task.labels && task.labels.length > 0) {
            let labelsHtml = '';
            task.labels.forEach(label => {
                labelsHtml += `<span class="px-3 py-1 rounded-full text-sm" style="background-color: ${label.color}20; color: ${label.color}; border-left: 3px solid ${label.color}">
                    ${escapeHtml(label.name)}
                </span>`;
            });
            labelsContainer.innerHTML = labelsHtml;
        } else {
            labelsContainer.innerHTML = '<span class="text-gray-400 text-sm">No labels assigned</span>';
        }
        
        const desc = task.description || 'No description provided.';
        document.getElementById('archivedTaskDetailDescription').innerHTML = escapeHtml(desc).replace(/\n/g, '<br>');
        
        const priorityEl = document.getElementById('archivedTaskDetailPriority');
        if (task.priority === 'high') {
            priorityEl.innerHTML = '<span class="text-red-600">🔴 High Priority</span>';
        } else if (task.priority === 'medium') {
            priorityEl.innerHTML = '<span class="text-yellow-600">🟡 Medium Priority</span>';
        } else if (task.priority === 'low') {
            priorityEl.innerHTML = '<span class="text-green-600">🟢 Low Priority</span>';
        } else {
            priorityEl.innerHTML = '-';
        }
        
        const dueDateEl = document.getElementById('archivedTaskDetailDueDate');
        if (task.due_date) {
            const dueDate = new Date(task.due_date);
            dueDateEl.innerHTML = `<span class="text-gray-700">📅 ${dueDate.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })}</span>`;
        } else {
            dueDateEl.innerHTML = '<span class="text-gray-400">Not set</span>';
        }
        
        const assigneeEl = document.getElementById('archivedTaskDetailAssignee');
        if (task.assignees && task.assignees.length > 0) {
            assigneeEl.innerHTML = `<div class="flex items-center gap-2 flex-wrap">${task.assignees.map(a => `<div class="w-8 h-8 rounded-full bg-gradient-to-br from-[#1e3a5f] to-[#2d4a7c] flex items-center justify-center text-white text-xs font-bold shadow-sm" title="${escapeHtml(a.name)}">${escapeHtml(a.name.charAt(0))}</div><span class="text-sm text-gray-700">${escapeHtml(a.name)}</span>`).join('')}</div>`;
        } else {
            assigneeEl.innerHTML = '<span class="text-gray-400">Not assigned</span>';
        }
        
        const archivedAtEl = document.getElementById('archivedTaskDetailArchivedAt');
        if (task.archived_at) {
            const archivedDate = new Date(task.archived_at);
            archivedAtEl.innerHTML = archivedDate.toLocaleDateString('id-ID', {
                day: 'numeric', month: 'long', year: 'numeric',
                hour: '2-digit', minute: '2-digit'
            });
        } else {
            archivedAtEl.innerHTML = '<span class="text-gray-400">Unknown</span>';
        }
        
        renderArchivedChecklists(task.checklists || []);
        renderArchivedAttachments(task.attachments || []);
        renderArchivedComments(task.comments || []);
    })
    .catch(error => {
        console.error('Error loading archived task detail:', error);
        document.getElementById('archivedTaskDetailTitle').innerHTML = 'Error loading task';
        document.getElementById('archivedTaskDetailDescription').innerHTML = '<span class="text-red-500">Failed to load task details. Please try again.</span>';
    });
}

function closeArchivedTaskDetailModal() {
    document.getElementById('archivedTaskDetailModal').classList.add('hidden');
    currentArchivedTaskId = null;
    currentArchivedTaskTitle = null;
}

function restoreFromModal() {
    if (!currentArchivedTaskId) return;
    if (!confirm(`Restore task "${currentArchivedTaskTitle}" to its original list?`)) return;
    
    fetch(`/tasks/${currentArchivedTaskId}/unarchive`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeArchivedTaskDetailModal();
            location.reload();
        } else {
            alert('Failed to restore task: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Restore error:', error);
        alert('Error restoring task');
    });
}

// ==============================================
// RENDER FUNCTIONS
// ==============================================

function renderArchivedChecklists(checklists) {
    const container = document.getElementById('archivedChecklistsContainer');
    if (!container) return;
    
    if (!checklists || checklists.length === 0) {
        container.innerHTML = '<div class="text-center text-gray-400 text-sm py-4">No checklists</div>';
        return;
    }
    
    let html = '';
    checklists.forEach(checklist => {
        const items = checklist.items || [];
        const total = items.length;
        const completed = items.filter(i => i.is_checked).length;
        const progress = total > 0 ? Math.round((completed / total) * 100) : 0;
        
        html += `
            <div class="bg-gray-50 rounded-xl p-3">
                <div class="font-medium text-gray-800 mb-2">${escapeHtml(checklist.name)}</div>
                <div class="flex justify-between text-xs text-gray-500 mb-1">
                    <span>${completed}/${total} completed</span>
                    <span>${progress}%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-1.5 mb-3">
                    <div class="bg-green-500 rounded-full h-1.5" style="width: ${progress}%"></div>
                </div>
                <div class="space-y-1">
                    ${items.map(item => `
                        <div class="flex items-center gap-2 py-1">
                            <input type="checkbox" ${item.is_checked ? 'checked' : ''} disabled class="w-4 h-4 rounded border-gray-300 cursor-not-allowed opacity-70">
                            <span class="flex-1 text-sm ${item.is_checked ? 'text-gray-400 line-through' : 'text-gray-700'}">${escapeHtml(item.name)}</span>
                        </div>
                    `).join('')}
                </div>
            </div>
        `;
    });
    
    container.innerHTML = html;
}

function getFileIcon(fileType) {
    const icons = { 'image': '🖼️', 'pdf': '📄', 'document': '📝', 'spreadsheet': '📊', 'archive': '🗜️', 'text': '📃', 'other': '📎' };
    return icons[fileType] || '📎';
}

function formatFileSize(bytes) {
    if (bytes >= 1073741824) return (bytes / 1073741824).toFixed(2) + ' GB';
    if (bytes >= 1048576) return (bytes / 1048576).toFixed(2) + ' MB';
    if (bytes >= 1024) return (bytes / 1024).toFixed(2) + ' KB';
    return bytes + ' bytes';
}

function renderArchivedAttachments(attachments) {
    const container = document.getElementById('archivedAttachmentsContainer');
    const countSpan = document.getElementById('archivedAttachmentsCount');
    
    if (countSpan) countSpan.textContent = attachments?.length || 0;
    if (!container) return;
    
    if (!attachments || attachments.length === 0) {
        container.innerHTML = '<div class="text-center text-gray-400 text-sm py-2">No attachments</div>';
        return;
    }
    
    let html = '';
    attachments.forEach(attachment => {
        const icon = getFileIcon(attachment.file_type);
        const isCover = attachment.is_cover;
        const isImage = attachment.file_type === 'image';
        
        let previewHtml = '';
        if (isImage) {
            previewHtml = `<div class="mt-2">
                <img src="${attachment.file_path}" alt="${attachment.file_name}" class="max-w-full h-20 rounded-lg object-cover cursor-pointer hover:opacity-90 transition" onclick="window.open('${attachment.file_path}', '_blank')">
            </div>`;
        }
        
        html += `
            <div class="bg-gray-50 rounded-xl p-3 ${isCover ? 'ring-2 ring-green-500 bg-green-50/50' : ''}">
                <div class="flex justify-between items-center flex-wrap gap-2">
                    <div class="flex items-center gap-3 flex-1 min-w-0">
                        <div class="text-2xl flex-shrink-0">${icon}</div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-800 truncate" title="${attachment.file_name}">${escapeHtml(attachment.file_name)}</p>
                            <p class="text-xs text-gray-500">${formatFileSize(attachment.file_size)}</p>
                            ${isCover ? '<p class="text-xs text-green-600 font-medium mt-0.5">✓ Cover image</p>' : ''}
                        </div>
                    </div>
                    <div class="flex gap-1 flex-shrink-0">
                        <a href="/attachments/${attachment.id}/download" class="text-blue-500 hover:text-blue-700 p-1.5 rounded-lg hover:bg-blue-50 transition" title="Download">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                            </svg>
                        </a>
                    </div>
                </div>
                ${previewHtml}
            </div>
        `;
    });
    
    container.innerHTML = html;
}

function formatCommentDate(dateString) {
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

function renderArchivedComments(comments) {
    const container = document.getElementById('archivedCommentsList');
    const countSpan = document.getElementById('archivedCommentsCount');
    
    if (countSpan) countSpan.textContent = comments?.length || 0;
    if (!container) return;
    
    if (!comments || comments.length === 0) {
        container.innerHTML = '<div class="text-center text-gray-400 text-sm py-4">No comments</div>';
        return;
    }
    
    let html = '';
    comments.forEach(comment => {
        let formattedContent = escapeHtml(comment.content);
        formattedContent = formattedContent.replace(/@(\w+)/g, '<span class="text-blue-600 font-medium">@$1</span>');
        
        html += `
            <div class="bg-gray-50 rounded-xl p-3">
                <div class="flex gap-3">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-r from-gray-400 to-gray-500 flex items-center justify-center text-white text-xs font-bold shadow-sm">
                            ${comment.user ? escapeHtml(comment.user.name.charAt(0)) : '?'}
                        </div>
                    </div>
                    <div class="flex-1">
                        <div class="flex justify-between items-start flex-wrap gap-1">
                            <div>
                                <span class="font-medium text-sm text-gray-800">${comment.user ? escapeHtml(comment.user.name) : 'Unknown'}</span>
                                <span class="text-xs text-gray-400 ml-2">${formatCommentDate(comment.created_at)}</span>
                            </div>
                        </div>
                        <div class="text-sm text-gray-700 mt-1">${formattedContent}</div>
                        <div class="flex items-center gap-2 mt-2">
                            <span class="flex items-center gap-1 text-xs text-gray-500">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                </svg>
                                <span>${comment.likes_count || 0}</span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        `;
    });
    
    container.innerHTML = html;
}

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchArchivedTask');
    const filterListSelect = document.getElementById('filterList');
    const filterDateSelect = document.getElementById('filterDate');
    
    if (searchInput) searchInput.addEventListener('keyup', filterArchivedTasks);
    if (filterListSelect) filterListSelect.addEventListener('change', filterArchivedTasks);
    if (filterDateSelect) filterDateSelect.addEventListener('change', filterArchivedTasks);
});

document.addEventListener('click', function(event) {
    const modal = document.getElementById('archivedTaskDetailModal');
    if (event.target === modal) closeArchivedTaskDetailModal();
});
</script>
@endsection