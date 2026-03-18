<x-mail::message>
    <h1>
        @lang('emails.notifications_cleanup.greeting')
    </h1>

    <p>
        @lang('emails.notifications_cleanup.line_one', ['appname' => config('app.name')])
    </p>

    <ul>
        <li>@lang('emails.notifications_cleanup.deleted', ['count' => $deleted])</li>
        <li>@lang('emails.notifications_cleanup.cutoff', ['date' => $cutoff->format('m/j/Y @ g:i A')])</li>
        <li>@lang('emails.notifications_cleanup.days', ['days' => $days])</li>
    </ul>

    <p>
        @lang('emails.notifications_cleanup.goodbye')
    </p>

    <p>
        {{ config('app.name') }}
    </p>
</x-mail::message>
