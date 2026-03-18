<x-mail::message>
    <h1>
        @lang('emails.verify.greeting', ['username' => $user->name])
    </h1>

    <p>
        @lang('emails.verify.msg_upper_one', ['appname' => config('app.name')])
    </p>

    <p>
        @lang('emails.verify.msg_upper_two')
    </p>

    <x-mail::button :url="$verificationUrl">
        @lang('emails.verify.btn')
    </x-mail::button>

    <p>
        @lang('emails.verify.msg_lower')
    </p>

    <p>
        @lang('emails.verify.goodbye')
    </p>

    <p>
        {{ config('app.name') }}
    </p>

    <x-slot:subcopy>
        @lang(
            "If you're having trouble clicking the \":actionText\" button, copy and paste the URL below\n" .
            'into your web browser:',
            [
                'actionText' => __('emails.verify.btn'),
            ]
        )
        <span class="break-all">[{{ $verificationUrl }}]({{ $verificationUrl }})</span>
    </x-slot>
</x-mail::message>
