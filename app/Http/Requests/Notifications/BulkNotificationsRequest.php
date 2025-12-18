<?php

namespace App\Http\Requests\Notifications;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BulkNotificationsRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        return (bool) $user && (
            $user->can('edit-notifications') ||
            $user->can('delete-notifications')
        );
    }

    public function rules(): array
    {
        return [
            'action' => ['required', 'string', Rule::in(['read', 'unread', 'dismiss', 'undismiss', 'delete'])],
            'ids'    => ['required', 'array', 'min:1', 'max:500'],
            'ids.*'  => ['string'],
        ];
    }
}
