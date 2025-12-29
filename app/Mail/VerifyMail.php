<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerifyMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public User $user;
    public string $verificationUrl;

    public function __construct(User $user, string $verificationUrl)
    {
        $this->user = $user;
        $this->verificationUrl = $verificationUrl;
    }

    public function build(): self
    {
        return $this->subject(
            trans('emails.verify.subject', [
                'appname' => config('app.name'),
            ]),
        )->markdown('emails.verify', [
            'user' => $this->user,
            'verificationUrl' => $this->verificationUrl,
        ]);
    }
}
