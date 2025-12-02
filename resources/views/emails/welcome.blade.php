@component('mail::message')
# @lang('emails.welcome.greeting', ['username' => $user->name])

@lang('emails.welcome.msg_upper', ['appname' => config('app.name')])

@component('mail::button', ['url' => route('dashboard')])
@lang('emails.welcome.btn')
@endcomponent

@lang('emails.welcome.msg_lower')

@lang('emails.welcome.goodbye')
<br>
{{ config('app.name') }}
@endcomponent
