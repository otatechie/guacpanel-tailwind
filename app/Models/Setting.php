<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasUlids;

    protected $fillable = [
        'password_expiry',
        'passwordless_login',
        'two_factor_authentication',
    ];

    protected $casts = [
        'force_password_change' => 'boolean',
        'password_expiry' => 'boolean',
        'passwordless_login' => 'boolean',
        'two_factor_authentication' => 'boolean',
    ];
}
