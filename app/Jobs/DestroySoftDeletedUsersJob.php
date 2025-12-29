<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DestroySoftDeletedUsersJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function handle(): int
    {
        $deletedCount = 0;

        User::autoDestroyable()->chunkById(100, function (Collection $users) use (&$deletedCount): void {
            foreach ($users as $user) {
                $user->forceDelete();
                $deletedCount++;
            }
        });

        return $deletedCount;
    }
}
