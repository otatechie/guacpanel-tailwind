<?php

namespace App\Providers;

use App\Mail\VerifyMail;
use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Notifications\VerifyEmail;

class VerifyCustomEmailServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        VerifyEmail::toMailUsing(function (object $notifiable, string $url) {
            return (new VerifyMail($notifiable, $url))
                ->to($notifiable->email, $notifiable->name ?? null);
        });
    }
}
