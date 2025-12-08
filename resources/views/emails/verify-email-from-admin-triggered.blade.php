<x-mail::message>

<h1>
@lang('emails.verify_from_admin.greeting', ['username' => $notifiable->name])
</h1>

<p>
@lang('emails.verify_from_admin.msg_upper_one')
</p>

<x-mail::button :url="$verificationUrl">
@lang('emails.verify_from_admin.btn')
</x-mail::button>

<p>
@lang('emails.verify_from_admin.msg_lower')
</p>

<p>
@lang('emails.verify_from_admin.goodbye')
</p>

<p>
{{ config('app.name') }}
</p>

<x-slot:subcopy>
@lang(
    "If you're having trouble clicking the \":actionText\" button, copy and paste the URL below\n".
    'into your web browser:',
    [
        'actionText' => __('emails.verify_from_admin.btn'),
    ]
) <span class="break-all">[{{ $verificationUrl }}]({{ $verificationUrl }})</span>
</x-slot:subcopy>

</x-mail::message>
