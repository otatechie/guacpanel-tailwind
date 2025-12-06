<x-mail::message>
# @lang('emails.verify.greeting', ['username' => $user->name])

@lang('emails.verify.msg_upper_one', ['appname' => config('app.name')])

@lang('emails.verify.msg_upper_two')

<x-mail::button :url="$verificationUrl">
@lang('emails.verify.btn')
</x-mail::button>

@lang('emails.verify.msg_lower')
<br /><br />
@lang('emails.verify.goodbye')
<br />
{{ config('app.name') }}
</x-mail::message>
