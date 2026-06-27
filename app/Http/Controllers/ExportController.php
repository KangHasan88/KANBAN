<?php

namespace App\Http\Controllers;

use App\Models\Board;
use App\Models\Task;
use App\Exports\TasksExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ExportController extends Controller
{
    /**
     * Show export modal/options
     */
    public function index(Board $board)
    {
        if (!$board->hasAccess(auth()->id())) {
            abort(403);
        }
        
        $lists = $board->lists;
        $sharedUsers = $board->sharedUsers()->get();
        $labels = $board->labels;
        
        return view('boards.partials.export-modal', compact('board', 'lists', 'sharedUsers', 'labels'));
    }
    
    /**
     * Export tasks to CSV (with semicolon delimiter for Excel compatibility)
     */
    public function csv(Request $request, Board $board)
    {
        if (!$board->hasAccess(auth()->id())) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $tasks = $this->getFilteredTasks($request, $board);
        
        // Build CSV content with semicolon delimiter
        $headers = [
            'ID',
            'Title',
            'Description',
            'List',
            'Priority',
            'Due Date',
            'Assignees',
            'Labels',
            'Status',
            'Created At',
            'Updated At',
            'Total Time (hours)'
        ];
        
        $rows = [];
        foreach ($tasks as $task) {
            $assignees = $task->assignees->pluck('name')->implode(', ');
            $labels = $task->labels->pluck('name')->implode(', ');
            $status = $task->isArchived() ? 'Archived' : 'Active';
            $totalHours = round($task->total_time / 3600, 2);
            
            $rows[] = [
                $task->id,
                $task->title,
                strip_tags($task->description ?? ''),
                $task->taskList->name ?? 'Unknown',
                ucfirst($task->priority),
                $task->due_date ? $task->due_date->format('Y-m-d') : '',
                $assignees ?: '-',
                $labels ?: '-',
                $status,
                $task->created_at ? $task->created_at->format('Y-m-d H:i') : '',
                $task->updated_at ? $task->updated_at->format('Y-m-d H:i') : '',
                $totalHours,
            ];
        }
        
        // Buat konten CSV dengan delimiter semicolon (;)
        $output = fopen('php://temp', 'r+');
        
        fputcsv($output, $headers, ';', '"');
        
        foreach ($rows as $row) {
            fputcsv($output, $row, ';', '"');
        }
        
        rewind($output);
        $csvContent = stream_get_contents($output);
        fclose($output);
        
        // Add UTF-8 BOM untuk support karakter Indonesia
        $csvContent = "\xEF\xBB\xBF" . $csvContent;
        
        return response($csvContent)
            ->withHeaders([
                'Content-Type' => 'text/csv; charset=utf-8',
                'Content-Disposition' => 'attachment; filename="' . $board->name . '_tasks_' . date('Y-m-d') . '.csv"',
            ]);
    }
    
    /**
     * Export tasks to Excel
     */
    public function excel(Request $request, Board $board)
    {
        if (!$board->hasAccess(auth()->id())) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $tasks = $this->getFilteredTasks($request, $board);
        
        $export = new TasksExport($tasks, $board->name);
        $filename = $board->name . '_tasks_' . date('Y-m-d') . '.xlsx';
        
        return Excel::download($export, $filename, \Maatwebsite\Excel\Excel::XLSX);
    }
    
    /**
     * Export tasks to PDF
     */
    public function pdf(Request $request, Board $board)
    {
        if (!$board->hasAccess(auth()->id())) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $tasks = $this->getFilteredTasks($request, $board);
        $boardName = $board->name;
        $exportDate = date('d M Y H:i:s');
        
        $pdf = Pdf::loadView('exports.tasks-pdf', compact('tasks', 'boardName', 'exportDate'));
        $pdf->setPaper('A4', 'landscape');
        
        return $pdf->download($board->name . '_tasks_' . date('Y-m-d') . '.pdf');
    }
    
    /**
     * Get filtered tasks based on request
     */
    private function getFilteredTasks($request, $board)
    {
        $query = Task::whereIn('task_list_id', $board->lists->pluck('id'))
            ->with(['taskList', 'assignees', 'labels']);
        
        if ($request->list_id && $request->list_id !== 'all') {
            $query->where('task_list_id', $request->list_id);
        }
        
        if ($request->priority && $request->priority !== 'all') {
            $query->where('priority', $request->priority);
        }
        
        if ($request->status === 'active') {
            $query->active();
        } elseif ($request->status === 'archived') {
            $query->archived();
        }
        
        if ($request->assignee_id && $request->assignee_id !== 'all') {
            $query->whereHas('assignees', function($q) use ($request) {
                $q->where('user_id', $request->assignee_id);
            });
        }
        
        if ($request->label_id && $request->label_id !== 'all') {
            $query->whereHas('labels', function($q) use ($request) {
                $q->where('label_id', $request->label_id);
            });
        }
        
        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        if ($request->due_date_from) {
            $query->whereDate('due_date', '>=', $request->due_date_from);
        }
        if ($request->due_date_to) {
            $query->whereDate('due_date', '<=', $request->due_date_to);
        }
        
        if ($request->search) {
            $query->where('title', 'LIKE', '%' . $request->search . '%');
        }
        
        return $query->orderBy('created_at', 'desc')->get();
    }
}