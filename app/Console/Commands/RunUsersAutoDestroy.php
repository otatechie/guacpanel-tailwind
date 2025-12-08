<?php

namespace App\Console\Commands;

use App\Jobs\DestroySoftDeletedUsersJob;
use Illuminate\Console\Command;

class RunUsersAutoDestroy extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:auto-destroy-run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Force delete soft-deleted users whose restore window has passed.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Running DestroySoftDeletedUsersJob...');

        $job = new DestroySoftDeletedUsersJob();

        $deletedCount = $job->handle();

        $this->info("Deleted {$deletedCount} user(s).");

        return self::SUCCESS;
    }
}
