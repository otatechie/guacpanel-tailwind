<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class LoginHistory extends Model
{
    protected $table = 'login_history';

    protected $fillable = [
        'user_type',
        'user_id',
        'ip_address',
        'user_agent',
        'login_successful',
        'login_at',
        'logout_at',
    ];

    protected $casts = [
        'login_successful' => 'boolean',
        'login_at'         => 'datetime',
        'logout_at'        => 'datetime',
    ];

    public function user(): MorphTo
    {
        return $this->morphTo();
    }
}
