<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

class NotificationsCleanupReport extends Mailable
{
    use Queueable;
    use SerializesModels;

    public int $deleted;

    public Carbon $cutoff;

    public int $days;

    public function __construct(int $deleted, Carbon $cutoff, int $days)
    {
        $this->deleted = $deleted;
        $this->cutoff = $cutoff;
        $this->days = $days;
    }

    public function build(): self
    {
        return $this
            ->subject(trans('emails.notifications_cleanup.subject', [
                'appname' => config('app.name'),
            ]))
            ->markdown('emails.notifications.cleanup-report');
    }
}
