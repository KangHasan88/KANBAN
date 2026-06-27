<?php
// app/Console/Commands/AutoArchiveCommand.php

namespace App\Console\Commands;

use App\Models\Board;
use App\Jobs\AutoArchiveTaskJob;
use Illuminate\Console\Command;

class AutoArchiveCommand extends Command
{
    protected $signature = 'kanban:auto-archive';
    protected $description = 'Auto archive tasks from Done list based on board settings';

    public function handle()
    {
        $boards = Board::where('auto_archive_enabled', true)->get();
        
        $this->info("Found {$boards->count()} boards with auto archive enabled");
        
        foreach ($boards as $board) {
            AutoArchiveTaskJob::dispatch($board->id);
            $this->line("Dispatched auto archive job for board: {$board->name}");
        }
        
        $this->info("Auto archive jobs dispatched successfully!");
        
        return Command::SUCCESS;
    }
}