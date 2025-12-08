<x-mail::message>

<h1>
@lang('emails.general.greeting', [ 'username' => $user->name])
</h1>

<p>
{{ $isNewUser ? 'Complete Your Registration' : 'Login to Your Account' }}
</p>

<p>
@if ($isNewUser)
Welcome! Click the button below to verify your email and access your account.
@else
Click the button below to login to your account. This link will expire in 10 minutes.
@endif
</p>

<x-mail::button :url="$url">
{{ $isNewUser ? 'Access Your Account' : 'Login Now' }}
</x-mail::button>

<p>
If you did not request this link, no action is required.
</p>

<p>
@lang('emails.general.goodbye')
</p>

<p>
{{ config('app.name') }}
</p>

<x-slot:subcopy>
@lang(
    "If you're having trouble clicking the \":actionText\" button, copy and paste the URL below\n".
    'into your web browser:',
    [
        'actionText' => ($isNewUser ? 'Access Your Account' : 'Login Now')),
    ]
) <span class="break-all">[{{ $url }}]({{ $url }})</span>
</x-slot:subcopy>

</x-mail::message>
