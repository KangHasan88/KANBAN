<?php

namespace App\Console\Commands;

use App\Models\RecurringTask;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class RecurringTasksGenerate extends Command
{
    protected $signature = 'recurring-tasks:generate';
    protected $description = 'Generate new tasks from recurring patterns';
    
    public function handle()
    {
        $this->info('Starting recurring tasks generation...');
        
        $recurringTasks = RecurringTask::where('is_active', true)->get();
        $generated = 0;
        
        foreach ($recurringTasks as $recurring) {
            try {
                $nextDueDate = $recurring->getNextDate();
                
                if ($nextDueDate) {
                    $newTask = $recurring->generateNextTask();
                    if ($newTask) {
                        $generated++;
                        $this->info("Generated new task #{$newTask->id} from recurring #{$recurring->id}");
                        Log::info("Recurring task generated", [
                            'recurring_id' => $recurring->id,
                            'new_task_id' => $newTask->id,
                            'due_date' => $nextDueDate
                        ]);
                    }
                }
            } catch (\Exception $e) {
                $this->error("Error processing recurring #{$recurring->id}: " . $e->getMessage());
                Log::error("Recurring task generation error", [
                    'recurring_id' => $recurring->id,
                    'error' => $e->getMessage()
                ]);
            }
        }
        
        $this->info("Generated {$generated} new recurring tasks");
        
        return Command::SUCCESS;
    }
}