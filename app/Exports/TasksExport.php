<?php

namespace App\Exports;

use App\Models\Task;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TasksExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $tasks;
    protected $boardName;

    public function __construct($tasks, $boardName)
    {
        $this->tasks = $tasks;
        $this->boardName = $boardName;
    }

    public function collection()
    {
        return $this->tasks;
    }

    public function headings(): array
    {
        return [
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
    }

    public function map($task): array
    {
        // Format assignees
        $assignees = $task->assignees->pluck('name')->implode(', ');
        
        // Format labels
        $labels = $task->labels->pluck('name')->implode(', ');
        
        // Format status
        $status = $task->isArchived() ? 'Archived' : 'Active';
        
        // Format total time (dari detik ke jam)
        $totalHours = round($task->total_time / 3600, 2);
        
        return [
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

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}