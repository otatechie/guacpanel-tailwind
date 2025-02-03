<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Spatie\Health\Checks\Checks\BackupCheck;
use Spatie\Health\Checks\Checks\BackupsCheck;
use Spatie\Health\Checks\Checks\CacheCheck;
use Spatie\Health\Checks\Checks\DatabaseCheck;
use Spatie\Health\Checks\Checks\DebugModeCheck;
use Spatie\Health\Checks\Checks\EnvironmentCheck;
use Spatie\Health\Checks\Checks\MeiliSearchCheck;
use Spatie\Health\Checks\Checks\OptimizedAppCheck;
use Spatie\Health\Checks\Checks\QueueCheck;
use Spatie\Health\Checks\Checks\RedisCheck;
use Spatie\Health\Checks\Checks\ScheduleCheck;
use Spatie\Health\Checks\Checks\UsedDiskSpaceCheck;
use Spatie\Health\Facades\Health;

class LaravelHealthProvider extends ServiceProvider
{
   public function register(): void
   {
       //
   }

   public function boot(): void
   {
       Health::checks([
           UsedDiskSpaceCheck::new()
               ->failWhenUsedSpaceIsAbovePercentage(90),
           DatabaseCheck::new(),
           CacheCheck::new(),
           DebugModeCheck::new(),
           EnvironmentCheck::new(),
           ScheduleCheck::new(),
           QueueCheck::new(),
           BackupsCheck::new()->locatedAt('storage/app/public/backups'),
           OptimizedAppCheck::new(),
       ]);
   }
}
