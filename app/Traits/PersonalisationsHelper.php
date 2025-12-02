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
        $loader = fn () => tap(
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

        return Cache::rememberForever($this->cacheName, $loader);
    }

    protected function clearPersonalisationCache(): void
    {
        Cache::forget($this->cacheName);
    }
}
