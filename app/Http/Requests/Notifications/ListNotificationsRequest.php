<?php

namespace App\Http\Requests\Notifications;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ListNotificationsRequest extends FormRequest
{
    /**
     * Every ordering the page can ask for.
     *
     * `newest`/`oldest` are the date column's two directions under the names
     * they have always had, so existing links keep meaning what they meant.
     */
    public const SORTS = [
        'newest',
        'oldest',
        'title_asc',
        'title_desc',
        'scope_asc',
        'scope_desc',
        'type_asc',
        'type_desc',
        'read_asc',
        'read_desc',
        'dismissed_asc',
        'dismissed_desc',
    ];

    public function authorize(): bool
    {
        $user = $this->user();

        return (bool) $user && $user->can('view-notifications');
    }

    public function rules(): array
    {
        return [
            'scope' => ['sometimes', 'string', 'in:all,user,system,release'],
            'read' => ['sometimes', 'string', 'in:all,read,unread'],
            'dismissed' => ['sometimes', 'string', 'in:all,dismissed,undismissed'],
            'type' => ['sometimes', 'string', 'in:all,info,success,warning,error'],
            'search' => ['sometimes', 'nullable', 'string', 'max:255'],
            'sort' => ['sometimes', 'string', Rule::in(self::SORTS)],
            'per_page' => [
                'sometimes',
                function ($attribute, $value, $fail) {
                    if ($value === 'all') {
                        return;
                    }

                    if (!is_numeric($value)) {
                        $fail('The per_page must be an integer or "all".');

                        return;
                    }

                    $n = (int) $value;

                    if ($n < 1 || $n > 1000) {
                        $fail('The per_page must be between 1 and 1000, or "all".');
                    }
                },
            ],
        ];
    }
}
