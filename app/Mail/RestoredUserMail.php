<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RestoredUserMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public User $user;
    public string $restoreDateRaw;
    public string $restoreDateParsed;

    public function __construct(User $user)
    {
        $this->user = $user;
        $this->restoreDateRaw = $this->user?->restore_date;
        $this->restoreDateParsed = $this->user?->restore_date->format('F d, Y @ g:i A');
    }

    public function build()
    {
        return $this->subject(__('emails.restored.subject', ['appname' => config('app.name')]))->markdown(
            'emails.account-restored',
        );
    }
}
