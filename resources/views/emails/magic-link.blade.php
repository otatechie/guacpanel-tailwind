@component('mail::message')
# {{ $isNewUser ? 'Complete Your Registration' : 'Login to Your Account' }}

@if($isNewUser)
Welcome! Click the button below to verify your email and access your account.
@else
Click the button below to login to your account. This link will expire in 10 minutes.
@endif

@component('mail::button', ['url' => $url])
{{ $isNewUser ? 'Access Your Account' : 'Login Now' }}
@endcomponent

If you did not request this link, no action is required.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
