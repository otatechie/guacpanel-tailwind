<x-mail::message>

<h1>
@lang('emails.restored.greeting', [ 'username' => $user->name])
</h1>

<p>
@lang('emails.restored.line_one', [ 'appname' => config('app.name'), 'date' => $restoreDateParsed ])
</p>

<x-mail::button :url="route('dashboard')">
@lang('emails.restored.btn')
</x-mail::button>

<p>
@lang('emails.restored.line_two', [ 'email' => config('guacpanel.admin.support_email') ])
</p>

<p>
@lang('emails.restored.goodbye')
</p>

<p>
{{ config('app.name') }}
</p>

<x-slot:subcopy>
@lang(
    "If you're having trouble clicking the \":actionText\" button, copy and paste the URL below\n".
    'into your web browser:',
    [
        'actionText' => __('emails.restored.btn'),
    ]
) <span class="break-all">[{{ route('dashboard') }}]({{ route('dashboard') }})</span>
</x-slot:subcopy>

</x-mail::message>
