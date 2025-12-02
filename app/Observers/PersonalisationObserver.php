<?php

namespace App\Observers;

use App\Models\Personalisation;

class PersonalisationObserver
{
    /**
     * Handle the Personalisation "created" event.
     */
    public function created(Personalisation $personalisation): void
    {
        cache()->forget('system_settings');
    }

    /**
     * Handle the Personalisation "updated" event.
     */
    public function updated(Personalisation $personalisation): void
    {
        cache()->forget('system_settings');
    }

    /**
     * same as "updated" event.
     */
    public function saved(Personalisation $personalisation): void
    {
        // cache()->forget('system_settings');
    }

    /**
     * Handle the Personalisation "deleted" event.
     */
    public function deleted(Personalisation $personalisation): void
    {
        cache()->forget('system_settings');
    }

    /**
     * Handle the Personalisation "restored" event.
     */
    public function restored(Personalisation $personalisation): void
    {
        cache()->forget('system_settings');
    }

    /**
     * Handle the Personalisation "force deleted" event.
     */
    public function forceDeleted(Personalisation $personalisation): void
    {
        cache()->forget('system_settings');
    }
}
