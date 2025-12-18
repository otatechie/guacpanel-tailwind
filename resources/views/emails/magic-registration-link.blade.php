<x-mail::message>
    <h1>
        @lang('emails.general.greeting', ['username' => $user->name])
    </h1>

    <p>Complete Your Registration Welcome! Click the button below to verify your email and access your account.</p>

    <x-mail::button :url="$url">
        @lang('emails.general.access')
    </x-mail::button>

    <p>If you did not request this registration, no action is required.</p>

    <p>
        @lang('emails.general.goodbye')
    </p>

    <p>
        {{ config('app.name') }}
    </p>

    <x-slot:subcopy>
        @lang(
            "If you're having trouble clicking the \":actionText\" button, copy and paste the URL below\n" .
            'into your web browser:',
            [
                'actionText' => __('emails.general.access'),
            ]
        )
        <span class="break-all">[{{ $url }}]({{ $url }})</span>
    </x-slot>
</x-mail::message>
