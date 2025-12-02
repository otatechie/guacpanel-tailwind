<?php

namespace App\Observers;

use App\Models\Personalisation;
use App\Traits\PersonalisationsHelper;

class PersonalisationObserver
{
    use PersonalisationsHelper;

    /**
     * Handle the Personalisation "created" event.
     */
    public function created(Personalisation $personalisation): void
    {
        $this->clearPersonalisationCache();
    }

    /**
     * Handle the Personalisation "updated" event.
     */
    public function updated(Personalisation $personalisation): void
    {
        $this->clearPersonalisationCache();
    }

    /**
     * same as "updated" event.
     */
    public function saved(Personalisation $personalisation): void
    {
        // $this->clearPersonalisationCache();
    }

    /**
     * Handle the Personalisation "deleted" event.
     */
    public function deleted(Personalisation $personalisation): void
    {
        $this->clearPersonalisationCache();
    }

    /**
     * Handle the Personalisation "restored" event.
     */
    public function restored(Personalisation $personalisation): void
    {
        $this->clearPersonalisationCache();
    }

    /**
     * Handle the Personalisation "force deleted" event.
     */
    public function forceDeleted(Personalisation $personalisation): void
    {
        $this->clearPersonalisationCache();
    }
}
