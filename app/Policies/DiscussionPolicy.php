<?php

namespace App\Policies;

use App\Models\Discussion;
use App\Models\User;

class DiscussionPolicy
{
    public function update(User $user, Discussion $discussion = null)
    {
        if (!$discussion) {
            return false;
        }
        return $user->id === $discussion->user_id;
    }
}
