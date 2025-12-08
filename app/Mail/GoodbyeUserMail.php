<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class GoodbyeUserMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public User $user;
    public string $url;
    public bool $restoreEnabled;
    public int $daysToRestore;
    public bool $autoDestroyEnabled;
    public $autoDestroyDateRaw;
    public $autoDestroyDateParsed;

    public function __construct(User $user, string $url = null)
    {
        $this->user = $user;
        $this->url = $url;
        $this->restoreEnabled = boolval(config('guacpanel.user.account.restore_enabled'));
        $this->daysToRestore = intval(config('guacpanel.user.account.days_to_restore'));
        $this->autoDestroyEnabled = $this->user?->auto_destroy;
        $this->autoDestroyDateRaw = $this->user?->auto_destroy_date;
        $this->autoDestroyDateParsed = $this->user?->auto_destroy_date->format('F d, Y');
    }

    public function build()
    {
        return $this
                ->subject(__('emails.goodbye.subject'))
                ->markdown('emails.goodbye-user-mail');
    }
}
