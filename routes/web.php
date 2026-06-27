<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\BoardController;
use App\Http\Controllers\TaskListController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\LabelController;
use App\Http\Controllers\ChecklistController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\TaskTemplateController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\UnsplashController;
use App\Http\Controllers\TaskWatcherController;
use App\Http\Controllers\CustomFieldController;
use App\Http\Controllers\RecurringTaskController;
use App\Http\Controllers\TimeTrackingController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\DashboardController;
use Illuminate\Http\Request;

// ==============================================
// GUEST ROUTES (Not Authenticated)
// ==============================================

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store']);
});

// ==============================================
// AUTH ROUTES (Logout)
// ==============================================

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// ==============================================
// PROTECTED ROUTES (Authenticated Users)
// ==============================================

Route::middleware(['auth'])->group(function () {
    
    // Home & Profile
    Route::get('/', [BoardController::class, 'index'])->name('home');
    Route::get('/profile', function () {
        return view('profile');
    })->name('profile');
    
    // ==============================================
    // SEARCH ROUTES
    // ==============================================
    
    Route::get('/search', [SearchController::class, 'index'])->name('search.index');
    Route::get('/search/api', [SearchController::class, 'api'])->name('search.api');
    
    // ==============================================
    // BOARD ROUTES
    // ==============================================
    
    Route::resource('boards', BoardController::class);
    
    // ==============================================
    // BOARD COVER SETTINGS
    // ==============================================
    
    Route::post('boards/{board}/cover-settings', [BoardController::class, 'updateCoverSettings'])
        ->name('boards.cover-settings');
    

    // ==============================================
    // GANTT CHART ROUTES
    // ==============================================
    
    Route::get('boards/{board}/gantt', [App\Http\Controllers\GanttController::class, 'index'])->name('boards.gantt');
    Route::get('boards/{board}/gantt/tasks', [App\Http\Controllers\GanttController::class, 'getTasks'])->name('boards.gantt.tasks');
    Route::post('boards/{board}/gantt/update', [App\Http\Controllers\GanttController::class, 'updateTask'])->name('boards.gantt.update');
    Route::post('boards/{board}/gantt/progress', [App\Http\Controllers\GanttController::class, 'updateProgress'])->name('boards.gantt.progress');
    Route::post('boards/{board}/gantt/settings', [App\Http\Controllers\GanttController::class, 'saveSettings'])->name('boards.gantt.settings');
    Route::get('boards/{board}/gantt/export', [App\Http\Controllers\GanttController::class, 'export'])->name('boards.gantt.export');


    // ==============================================
    // CALENDAR ROUTES
    // ==============================================
    
    Route::get('boards/{board}/calendar', [CalendarController::class, 'index'])->name('boards.calendar');
    Route::get('boards/{board}/calendar/api', [CalendarController::class, 'getTasks'])->name('boards.calendar.api');
    Route::post('boards/{board}/calendar/update-due-date', [CalendarController::class, 'updateDueDate'])->name('boards.calendar.update-due-date');
    
    // ==============================================
    // ACTIVITY LOG ROUTES
    // ==============================================
    
    Route::get('boards/{board}/activity-log', [ActivityLogController::class, 'index'])->name('boards.activity-log');
    Route::get('boards/{board}/activity-log/api', [ActivityLogController::class, 'api'])->name('boards.activity-log.api');
    
    // ==============================================
    // AUTO ARCHIVE SETTINGS ROUTE
    // ==============================================
    
    Route::post('boards/{board}/auto-archive-settings', [BoardController::class, 'updateAutoArchiveSettings'])
        ->name('boards.auto-archive-settings');
    
    // ==============================================
    // SHARE ROUTES
    // ==============================================
    
    Route::post('boards/{board}/share', [BoardController::class, 'share'])->name('boards.share');
    Route::delete('boards/{board}/unshare/{user}', [BoardController::class, 'unshare'])->name('boards.unshare');
    Route::put('boards/{board}/permission/{user}', [BoardController::class, 'updatePermission'])->name('boards.update-permission');
    
    // ==============================================
    // TASK LIST ROUTES
    // ==============================================
    
    Route::post('boards/{board}/lists', [TaskListController::class, 'store'])->name('lists.store');
    Route::put('lists/{taskList}', [TaskListController::class, 'update'])->name('lists.update');
    Route::delete('lists/{taskList}', [TaskListController::class, 'destroy'])->name('lists.destroy');
    Route::post('lists/reorder', [TaskListController::class, 'reorder'])->name('lists.reorder');
    Route::post('lists/save-widths', [TaskListController::class, 'saveWidths'])->name('lists.save-widths');
    
    // ==============================================
    // TASK ROUTES
    // ==============================================
    
    Route::post('lists/{taskList}/tasks', [TaskController::class, 'store'])->name('tasks.store');
    Route::put('tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
    Route::delete('tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');
    Route::post('tasks/reorder', [TaskController::class, 'reorder'])->name('tasks.reorder');
    Route::post('tasks/{task}/move', [TaskController::class, 'move'])->name('tasks.move');
    Route::get('tasks/{task}/edit', [TaskController::class, 'edit'])->name('tasks.edit');
    Route::get('tasks/{task}/activities', [TaskController::class, 'activities'])->name('tasks.activities');
    Route::get('tasks/{task}/assignable-users', [TaskController::class, 'getAssignableUsers'])->name('tasks.assignable-users');
    
    // ==============================================
    // BULK ARCHIVE ROUTES
    // ==============================================
    
    Route::post('tasks/bulk-archive', [TaskController::class, 'bulkArchive'])->name('tasks.bulk-archive');
    Route::post('tasks/bulk-restore', [TaskController::class, 'bulkRestore'])->name('tasks.bulk-restore');
    
    // ==============================================
    // ARCHIVE ROUTES
    // ==============================================
    
    Route::post('tasks/{task}/archive', [TaskController::class, 'archive'])->name('tasks.archive');
    Route::post('tasks/{task}/unarchive', [TaskController::class, 'unarchive'])->name('tasks.unarchive');
    Route::get('boards/{board}/archived', [TaskController::class, 'archived'])->name('boards.archived');
    Route::delete('tasks/{task}/force-delete', [TaskController::class, 'forceDelete'])->name('tasks.force-delete');
    Route::delete('boards/{board}/archived/clear', [TaskController::class, 'clearArchived'])->name('boards.archived.clear');
    
    // ==============================================
    // ASSIGN ROUTES
    // ==============================================
    
    Route::post('tasks/{task}/assign', [TaskController::class, 'assign'])->name('tasks.assign');
    
    // ==============================================
    // DELETE TASK (Admin only)
    // ==============================================
    
    Route::delete('tasks/{task}/delete', [TaskController::class, 'deleteTask'])->name('tasks.delete');
    
    // ==============================================
    // LABEL ROUTES
    // ==============================================
    
    Route::get('boards/{board}/labels', [LabelController::class, 'index'])->name('labels.index');
    Route::post('boards/{board}/labels', [LabelController::class, 'store'])->name('labels.store');
    Route::put('labels/{label}', [LabelController::class, 'update'])->name('labels.update');
    Route::delete('labels/{label}', [LabelController::class, 'destroy'])->name('labels.destroy');
    Route::post('tasks/{task}/labels', [LabelController::class, 'assign'])->name('tasks.labels.assign');
    Route::delete('tasks/{task}/labels/{label}', [LabelController::class, 'remove'])->name('tasks.labels.remove');
    Route::get('labels/{label}/tasks', [LabelController::class, 'tasks'])->name('labels.tasks');
    
    // ==============================================
    // CHECKLIST ROUTES
    // ==============================================
    
    Route::post('tasks/{task}/checklists', [ChecklistController::class, 'store'])->name('checklists.store');
    Route::put('checklists/{checklist}', [ChecklistController::class, 'update'])->name('checklists.update');
    Route::delete('checklists/{checklist}', [ChecklistController::class, 'destroy'])->name('checklists.destroy');
    Route::post('checklists/{checklist}/items', [ChecklistController::class, 'addItem'])->name('checklists.items.store');
    Route::put('checklist-items/{item}', [ChecklistController::class, 'updateItem'])->name('checklist-items.update');
    Route::patch('checklist-items/{item}/toggle', [ChecklistController::class, 'toggleItem'])->name('checklist-items.toggle');
    Route::delete('checklist-items/{item}', [ChecklistController::class, 'deleteItem'])->name('checklist-items.destroy');
    
    // ==============================================
    // COMMENT ROUTES
    // ==============================================
    
    Route::post('tasks/{task}/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::put('comments/{comment}', [CommentController::class, 'update'])->name('comments.update');
    Route::delete('comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');
    Route::post('comments/{comment}/like', [CommentController::class, 'like'])->name('comments.like');
    Route::get('tasks/{task}/comments', [CommentController::class, 'index'])->name('comments.index');
    
    // ==============================================
    // NOTIFICATION ROUTES
    // ==============================================
    
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount'])->name('notifications.unread-count');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
    
    // ==============================================
    // ATTACHMENT ROUTES
    // ==============================================
    
    Route::post('tasks/{task}/attachments', [AttachmentController::class, 'upload'])->name('attachments.upload');
    Route::get('attachments/{attachment}/download', [AttachmentController::class, 'download'])->name('attachments.download');
    Route::delete('attachments/{attachment}', [AttachmentController::class, 'destroy'])->name('attachments.destroy');
    Route::post('attachments/{attachment}/set-cover', [AttachmentController::class, 'setCover'])->name('attachments.set-cover');
    Route::delete('tasks/{task}/remove-cover', [AttachmentController::class, 'removeCover'])->name('attachments.remove-cover');
    
    // ==============================================
    // TASK TEMPLATE ROUTES
    // ==============================================
    
    Route::get('boards/{board}/templates', [TaskTemplateController::class, 'index'])->name('templates.index');
    Route::post('boards/{board}/templates', [TaskTemplateController::class, 'store'])->name('templates.store');
    Route::put('templates/{taskTemplate}', [TaskTemplateController::class, 'update'])->name('templates.update');
    Route::delete('templates/{taskTemplate}', [TaskTemplateController::class, 'destroy'])->name('templates.destroy');
    Route::post('templates/{taskTemplate}/create-task', [TaskTemplateController::class, 'createTask'])->name('templates.create-task');
    
    // ==============================================
    // UNSPLASH ROUTES - DISABLED
    // ==============================================
    
    // Route::get('unsplash/search', [UnsplashController::class, 'search'])->name('unsplash.search');
    // Route::post('tasks/{task}/unsplash-cover', [UnsplashController::class, 'applyCover'])->name('tasks.unsplash-cover');
    
    // ==============================================
    // TASK WATCHER ROUTES
    // ==============================================
    
    Route::post('tasks/{task}/watch/toggle', [TaskWatcherController::class, 'toggle'])->name('tasks.watch.toggle');
    Route::get('tasks/{task}/watchers', [TaskWatcherController::class, 'index'])->name('tasks.watchers');
    
    // ==============================================
    // CUSTOM FIELDS ROUTES
    // ==============================================
    
    Route::get('boards/{board}/custom-fields', [CustomFieldController::class, 'index'])->name('custom-fields.index');
    Route::post('boards/{board}/custom-fields', [CustomFieldController::class, 'store'])->name('custom-fields.store');
    Route::put('custom-fields/{customField}', [CustomFieldController::class, 'update'])->name('custom-fields.update');
    Route::delete('custom-fields/{customField}', [CustomFieldController::class, 'destroy'])->name('custom-fields.destroy');
    Route::post('boards/{board}/custom-fields/reorder', [CustomFieldController::class, 'reorder'])->name('custom-fields.reorder');
    
    Route::get('tasks/{task}/custom-fields', [CustomFieldController::class, 'getTaskValues'])->name('tasks.custom-fields');
    Route::post('tasks/{task}/custom-fields', [CustomFieldController::class, 'saveTaskValues'])->name('tasks.save-custom-fields');
    
    // ==============================================
    // RECURRING TASKS ROUTES
    // ==============================================
    
    Route::get('tasks/{task}/recurring', [RecurringTaskController::class, 'show'])->name('tasks.recurring.show');
    Route::post('tasks/{task}/recurring', [RecurringTaskController::class, 'store'])->name('tasks.recurring.store');
    Route::delete('tasks/{task}/recurring', [RecurringTaskController::class, 'destroy'])->name('tasks.recurring.destroy');
    
    // ==============================================
    // TIME TRACKING ROUTES
    // ==============================================
    
    Route::get('tasks/{task}/time-status', [TimeTrackingController::class, 'status'])->name('tasks.time.status');
    Route::post('tasks/{task}/time-start', [TimeTrackingController::class, 'start'])->name('tasks.time.start');
    Route::post('tasks/{task}/time-pause', [TimeTrackingController::class, 'pause'])->name('tasks.time.pause');
    Route::post('tasks/{task}/time-stop', [TimeTrackingController::class, 'stop'])->name('tasks.time.stop');
    Route::get('tasks/{task}/time-history', [TimeTrackingController::class, 'history'])->name('tasks.time.history');

    // ==============================================
    // EXPORT ROUTES
    // ==============================================

    Route::get('boards/{board}/export', [ExportController::class, 'index'])->name('boards.export');
    Route::post('boards/{board}/export/csv', [ExportController::class, 'csv'])->name('boards.export.csv');
    Route::post('boards/{board}/export/excel', [ExportController::class, 'excel'])->name('boards.export.excel');
    Route::post('boards/{board}/export/pdf', [ExportController::class, 'pdf'])->name('boards.export.pdf');

    // ==============================================
    // DASHBOARD STATISTICS ROUTES
    // ==============================================

    Route::get('boards/{board}/dashboard', [DashboardController::class, 'index'])->name('boards.dashboard');
    Route::post('boards/{board}/dashboard/stats', [DashboardController::class, 'getStats'])->name('boards.dashboard.stats');
});

// ==============================================
// ADMIN ROUTES (User Management)
// ==============================================

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
    
    Route::get('/users/trashed', [UserController::class, 'trashed'])->name('users.trashed');
    Route::post('/users/{id}/restore', [UserController::class, 'restore'])->name('users.restore');
    Route::delete('/users/{id}/force-delete', [UserController::class, 'forceDelete'])->name('users.force-delete');
});

// ==============================================
// FIX 403 FOR ATTACHMENTS - SERVE FILES DIRECTLY
// ==============================================

// Route untuk file baru (disimpan di storage/app/public/attachments)
Route::get('/attachments/{taskId}/{filename}', function ($taskId, $filename) {
    $fullPath = storage_path('app/public/attachments/' . $taskId . '/' . $filename);
    
    if (!file_exists($fullPath)) {
        $fullPath = public_path('storage/attachments/' . $taskId . '/' . $filename);
        if (!file_exists($fullPath)) {
            abort(404, 'File not found');
        }
    }
    
    $realPath = realpath($fullPath);
    $allowedBase = realpath(storage_path('app/public/attachments'));
    $allowedBase2 = realpath(public_path('storage/attachments'));
    
    if ($allowedBase && strpos($realPath, $allowedBase) !== 0) {
        if (!$allowedBase2 || strpos($realPath, $allowedBase2) !== 0) {
            abort(403, 'Invalid file path');
        }
    }
    
    $extension = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));
    $mimeTypes = [
        'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png',
        'gif' => 'image/gif', 'webp' => 'image/webp', 'svg' => 'image/svg+xml',
        'pdf' => 'application/pdf', 'zip' => 'application/zip', 'mp4' => 'video/mp4',
        'doc' => 'application/msword',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'xls' => 'application/vnd.ms-excel',
        'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    ];
    
    $mimeType = $mimeTypes[$extension] ?? 'application/octet-stream';
    
    return response()->file($realPath, [
        'Content-Type' => $mimeType,
        'Cache-Control' => 'public, max-age=86400',
    ]);
})->where('filename', '.*');

// ==============================================
// FIX 403 FOR OLD FILES - REDIRECT TO NEW ROUTE
// ==============================================

Route::get('/storage/attachments/{taskId}/{filename}', function ($taskId, $filename) {
    return redirect("/attachments/{$taskId}/{$filename}", 301);
})->where('filename', '.*');

Route::get('/storage/attachments/{filename}', function ($filename) {
    $searchPaths = [
        storage_path('app/public/attachments/'),
        public_path('storage/attachments/')
    ];
    
    foreach ($searchPaths as $basePath) {
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($basePath));
        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getFilename() === $filename) {
                $relativePath = str_replace($basePath, '', $file->getPathname());
                $parts = explode(DIRECTORY_SEPARATOR, $relativePath);
                $taskId = $parts[0] ?? 'unknown';
                return redirect("/attachments/{$taskId}/{$filename}", 301);
            }
        }
    }
    
    abort(404, 'File not found');
});

// ==============================================
// SIMPLE COVER TOGGLE ENDPOINT
// ==============================================

Route::post('/api/toggle-cover/{boardId}', function ($boardId, \Illuminate\Http\Request $request) {
    $board = \App\Models\Board::find($boardId);
    
    if (!$board) {
        return response()->json(['error' => 'Board not found'], 404);
    }
    
    if (!auth()->check()) {
        return response()->json(['error' => 'Unauthorized'], 401);
    }
    
    if ($board->user_id !== auth()->id()) {
        return response()->json(['error' => 'Only board owner can change cover settings'], 403);
    }
    
    $board->cover_enabled = $request->input('cover_enabled', false);
    $board->save();
    
    return response()->json([
        'success' => true,
        'cover_enabled' => $board->cover_enabled
    ]);
});