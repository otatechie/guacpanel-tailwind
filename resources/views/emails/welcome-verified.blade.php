<x-mail::message>
    <h1>
        @lang('emails.welcome-verified.greeting', ['username' => $user->name])
    </h1>

    <p>
        @lang('emails.welcome-verified.line_one', ['appname' => config('app.name')])
    </p>

    <x-mail::button :url="route('dashboard')">
        @lang('emails.welcome-verified.btn')
    </x-mail::button>

    <p>
        @lang('emails.welcome-verified.line_two')
    </p>

    <p>
        @lang('emails.welcome-verified.goodbye')
    </p>

    <p>
        {{ config('app.name') }}
    </p>

    <x-slot:subcopy>
        @lang(
            "If you're having trouble clicking the \":actionText\" button, copy and paste the URL below\n" .
            'into your web browser:',
            [
                'actionText' => __('emails.welcome-verified.btn'),
            ]
        )
        <span class="break-all">[{{ route('dashboard') }}]({{ route('dashboard') }})</span>
    </x-slot>
</x-mail::message>
