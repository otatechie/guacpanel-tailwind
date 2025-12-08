<x-mail::message>

<h1>
@lang('emails.goodbye.greeting', [ 'username' => $user->name ])
</h1>

<p>
@lang('emails.goodbye.common_line_one')
</p>

@if($restoreEnabled)
<p>
@lang('emails.goodbye.restore_line_one', [ 'days' => $daysToRestore ])
</p>

<x-mail::button :url="$url">
@lang('emails.goodbye.restore_btn_text')
</x-mail::button>
@endif


<p>
@if($user->auto_destroy && $user->auto_destroy_date)
@lang('emails.goodbye.common_line_two', [ 'date' => $autoDestroyDateParsed, 'days' => $daysToRestore ])
@endif

@lang('emails.goodbye.common_line_three')
</p>

<p>
<strong>
@lang('emails.goodbye.goodbye')
</strong>
</p>

<p>
{{ config('app.name') }}
</p>

{{-- Subcopy --}}
@if($restoreEnabled)
@isset($url)
<x-slot:subcopy>
@lang(
    "If you're having trouble clicking the \":actionText\" button, copy and paste the URL below\n".
    'into your web browser:',
    [
        'actionText' => __('emails.goodbye.restore_btn_text'),
    ]
) <span class="break-all">[{{ $url }}]({{ $url }})</span>
</x-slot:subcopy>
@endisset
@endif
</x-mail::message>
