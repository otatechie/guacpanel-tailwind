<x-mail::message>
# @lang('emails.verify_from_admin.greeting', ['username' => $notifiable->name])

@lang('emails.verify_from_admin.msg_upper_one')

<x-mail::button :url="$verificationUrl">
@lang('emails.verify_from_admin.btn')
</x-mail::button>

@lang('emails.verify_from_admin.msg_lower')
<br /><br />
@lang('emails.verify_from_admin.goodbye')
<br />
{{ config('app.name') }}
</x-mail::message>
