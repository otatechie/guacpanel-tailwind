<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AppNotificationRequested
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public ?string $userId, // ULID string, nullable for system
        public string $message,
        public array $data = [],
        public string $scope = 'user', // user|system|release
        public string $type = 'info', // info|success|warning|error
        public ?string $title = null,
    ) {}
}
