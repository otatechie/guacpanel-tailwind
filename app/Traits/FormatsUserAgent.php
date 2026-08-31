<?php

namespace App\Traits;

use Jenssegers\Agent\Agent;

trait FormatsUserAgent
{
    /**
     * The parser reports the platform names the vendors used when the strings
     * were written, so it still says "OS X" nine years after Apple renamed it.
     * Anything not listed passes through as the parser gave it.
     */
    protected const PLATFORM_LABELS = [
        'OS X' => 'macOS',
        'AndroidOS' => 'Android',
        'ChromeOS' => 'ChromeOS',
        'Windows NT' => 'Windows',
    ];

    protected function formatAgent($userAgent)
    {
        if (empty($userAgent)) {
            return ['device' => 'Unknown', 'browser' => 'Unknown', 'platform' => 'Unknown'];
        }

        $agent = new Agent();
        $agent->setUserAgent($userAgent);

        $platform = $agent->platform() ?: 'Unknown';

        return [
            'device' => $agent->device() ?: ($agent->isDesktop() ? 'Desktop' : 'Unknown'),
            'platform' => self::PLATFORM_LABELS[$platform] ?? $platform,
            'browser' => $agent->browser() ?: 'Unknown',
        ];
    }
}
