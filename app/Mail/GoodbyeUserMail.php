<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class GoodbyeUserMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public User $user;
    public ?string $url;
    public bool $restoreEnabled;
    public int $daysToRestore;
    public bool $autoDestroyEnabled;
    public $autoDestroyDateRaw;
    public $autoDestroyDateParsed;

    public function __construct(User $user, ?string $url = null)
    {
        $this->user = $user;
        $this->url = $url;
        $this->restoreEnabled = (bool) config('guacpanel.user.account.restore_enabled', false);
        $this->daysToRestore = (int) config('guacpanel.user.account.days_to_restore', 60);
        $this->autoDestroyEnabled = (bool) ($user->auto_destroy ?? false);
        $this->autoDestroyDateRaw = $user->auto_destroy_date;
        $this->autoDestroyDateParsed = $user->auto_destroy_formatted;
    }

    public function build(): self
    {
        return $this
            ->subject(__('emails.goodbye.subject'))
            ->markdown('emails.goodbye-user-mail');
    }
}
