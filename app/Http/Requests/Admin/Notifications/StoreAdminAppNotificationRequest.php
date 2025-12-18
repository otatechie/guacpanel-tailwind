<?php

namespace App\Http\Requests\Admin\Notifications;

use Illuminate\Foundation\Http\FormRequest;

class StoreAdminAppNotificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        return (bool) $user && $user->can('manage-notifications');
    }

    public function rules(): array
    {
        return [
            'scope'          => ['required', 'in:user,system,release'],
            'user_id'        => ['nullable', 'string', 'required_if:scope,user'],
            'type'           => ['required', 'in:success,info,warning,danger'],
            'title'          => ['required', 'string', 'max:255'],
            'message'        => ['required', 'string'],
            'scheduled_on'   => ['nullable', 'date'],
            'auto_expire_on' => ['nullable', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required_if' => 'A user is required when scope is set to user.',
        ];
    }
}
