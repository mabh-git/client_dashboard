<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class CleanupExports extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'exports:cleanup {--days=7 : Number of days to keep files}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up old export files';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = $this->option('days');
        $cutoff = Carbon::now()->subDays($days);

        $this->info("Cleaning up export files older than {$days} days...");

        $disk = Storage::disk('exports');
        $files = $disk->files();
        $count = 0;

        foreach ($files as $file) {
            $lastModified = Carbon::createFromTimestamp($disk->lastModified($file));

            if ($lastModified->lt($cutoff)) {
                $disk->delete($file);
                $count++;
                $this->line("Deleted: {$file}");
            }
        }

        $this->info("Cleanup completed. Removed {$count} files.");

        return Command::SUCCESS;
    }
}
