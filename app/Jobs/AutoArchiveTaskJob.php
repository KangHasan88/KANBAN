<?php
// app/Jobs/AutoArchiveTaskJob.php

namespace App\Jobs;

use App\Models\Board;
use App\Models\Task;
use App\Models\TaskActivity;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class AutoArchiveTaskJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, SerializesModels;

    protected $boardId;

    public function __construct($boardId)
    {
        $this->boardId = $boardId;
    }

    public function handle()
    {
        $board = Board::find($this->boardId);
        
        if (!$board || !$board->auto_archive_enabled) {
            return;
        }

        $targetListName = $board->auto_archive_list_name ?? 'Done';
        $daysThreshold = $board->auto_archive_days ?? 7;
        
        // Cari list dengan nama target (misal: Done)
        $targetList = $board->lists()->where('name', $targetListName)->first();
        
        if (!$targetList) {
            Log::warning("Auto Archive: List '{$targetListName}' not found in board {$board->id}");
            return;
        }
        
        // Hitung batas tanggal
        $archiveThreshold = now()->subDays($daysThreshold);
        
        // Cari task yang sudah di list Done lebih dari X hari dan belum di-archive
        $tasksToArchive = $targetList->tasks()
            ->whereNull('archived_at')
            ->where('updated_at', '<=', $archiveThreshold)
            ->get();
        
        $archivedCount = 0;
        
        foreach ($tasksToArchive as $task) {
            // Skip jika sudah di-archive
            if ($task->isArchived()) continue;
            
            $task->archive();
            
            TaskActivity::create([
                'action' => 'auto_archived',
                'user_id' => $board->user_id, // owner board
                'task_id' => $task->id,
                'field' => 'auto_archive',
                'old_value' => null,
                'new_value' => "Auto archived after {$daysThreshold} days in '{$targetListName}' list"
            ]);
            
            $archivedCount++;
        }
        
        if ($archivedCount > 0) {
            Log::info("Auto Archive: Archived {$archivedCount} tasks from board '{$board->name}'");
        }
    }
}