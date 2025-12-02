<x-mail::message>
# @lang('emails.goodbye.greeting', ['username' => $user->name])

@lang('emails.goodbye.message')

## @lang('emails.goodbye.goodbye'),
{{ config('app.name') }}
</x-mail::message>
