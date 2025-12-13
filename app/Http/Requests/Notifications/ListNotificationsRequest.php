<?php

namespace App\Http\Requests\Notifications;

use Illuminate\Foundation\Http\FormRequest;

class ListNotificationsRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        return (bool) $user && (
            $user->can('manage-notifications') ||
            $user->can('view-notifications')
        );
    }

    public function rules(): array
    {
        return [];
    }
}
