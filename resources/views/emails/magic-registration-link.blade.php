@component('mail::message')
# Complete Your Registration

Welcome! Click the button below to verify your email and access your account.

@component('mail::button', ['url' => $url])
Access Your Account
@endcomponent

If you did not request this registration, no action is required.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
