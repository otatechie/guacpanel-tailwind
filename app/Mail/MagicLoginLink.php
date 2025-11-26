<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MagicLoginLink extends Mailable
{
    use Queueable;
    use SerializesModels;

    public $url;
    public $isNewUser;

    public function __construct($url, $isNewUser = false)
    {
        $this->url = $url;
        $this->isNewUser = $isNewUser;
    }

    public function build()
    {
        return $this->markdown('emails.magic-link')
                    ->subject($this->isNewUser ? 'Complete Your Registration' : 'Your Magic Login Link');
    }
}
