<x-mail::message>
# @lang('emails.welcome.greeting', ['username' => $user->name])

@lang('emails.welcome.msg_upper', ['appname' => config('app.name')])

<x-mail::button :url="route('dashboard')">
@lang('emails.welcome.btn')
</x-mail::button>

@lang('emails.welcome.msg_lower')
<br /><br />
@lang('emails.welcome.goodbye')
<br />
{{ config('app.name') }}
</x-mail::message>
