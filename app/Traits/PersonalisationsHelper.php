<?php

namespace App\Traits;

use App\Models\Personalisation;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

trait PersonalisationsHelper
{
    private $cacheName = 'system_settings';

    protected function getPersonalisations(bool $useCaching = true): Personalisation
    {
        $loader = fn() => tap(
            Personalisation::first() ?? new Personalisation(),
            function (Personalisation $personalisation) {
                if ($personalisation->favicon && !Storage::disk('public')->exists($personalisation->favicon)) {
                    $personalisation->favicon = null;
                }
            }
        );

        if (!$useCaching) {
            return $loader();
        }

        try {
            return Cache::rememberForever($this->cacheName, $loader);
        } catch (\Exception $e) {
            // If cache fails (e.g., table doesn't exist during migration), fall back to direct query
            return $loader();
        }
    }

    protected function clearPersonalisationCache(): void
    {
        try {
            Cache::forget($this->cacheName);
        } catch (\Exception $e) {
            // Silently fail if cache is not available
        }
    }
}
