<x-mail::message>

<h1>
@lang('emails.welcome.greeting', ['username' => $user->name])
</h1>

<p>
@lang('emails.welcome.msg_upper', ['appname' => config('app.name')])
</p>

<x-mail::button :url="route('dashboard')">
@lang('emails.welcome.btn')
</x-mail::button>

<p>
@lang('emails.welcome.msg_lower')
</p>

<p>
@lang('emails.welcome.goodbye')
</p>

<p>
{{ config('app.name') }}
</p>

<x-slot:subcopy>
@lang(
    "If you're having trouble clicking the \":actionText\" button, copy and paste the URL below\n".
    'into your web browser:',
    [
        'actionText' => __('emails.welcome.btn'),
    ]
) <span class="break-all">[{{ route('dashboard') }}]({{ route('dashboard') }})</span>
</x-slot:subcopy>

</x-mail::message>
