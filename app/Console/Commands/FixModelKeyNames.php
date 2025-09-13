<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class FixModelKeyNames extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:model-keys';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix getKeyName method visibility in all models';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $modelsPath = app_path('Models');
        $files = File::files($modelsPath);
        
        $fixedCount = 0;
        
        foreach ($files as $file) {
            $content = File::get($file);
            
            // Replace protected getKeyName with public getKeyName
            $updatedContent = preg_replace(
                '/protected function getKeyName\(\)/',
                'public function getKeyName()',
                $content
            );
            
            if ($content !== $updatedContent) {
                File::put($file, $updatedContent);
                $this->line("✅ Fixed: " . basename($file));
                $fixedCount++;
            }
        }
        
        $this->info("🎉 Fixed {$fixedCount} model files");
        
        return Command::SUCCESS;
    }
}
