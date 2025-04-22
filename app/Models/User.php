<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Sanctum\HasApiTokens;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements Auditable
{
    use HasApiTokens, HasFactory, Notifiable, TwoFactorAuthenticatable;

    use HasUlids;

    use \OwenIt\Auditing\Auditable;

    use HasRoles;

    protected $guarded = ['id'];

    protected $fillable = [
        'name',
        'email',
        'password',
        'email_verified_at',
        'password_expiry_at',
        'password_changed_at',
        'force_password_change',
        'disable_account',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'password_expiry_at' => 'datetime',
        'password_changed_at' => 'datetime',
        'force_password_change' => 'boolean',
        'disable_account' => 'boolean',
    ];


    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            $user->user_slug = 'user-' . Str::random(12);
            if (!$user->password) {
                $user->password = null;
            }
        });
    }


    public function isPasswordExpired(): bool
    {
        if (!$this->password_expiry_at) {
            return false;
        }

        return $this->password_expiry_at->isPast();
    }


    public function daysUntilPasswordExpiry(): int
    {
        if (!$this->password_expiry_at) {
            return 0;
        }

        $expiryDate = Carbon::createFromTimestamp($this->password_expiry_at);
        return max(0, now()->diffInDays($expiryDate));
    }


    public function loginHistory()
    {
        return $this->morphMany(LoginHistory::class, 'user');
    }


    public function latestLogin()
    {
        return $this->morphOne(LoginHistory::class, 'user')->latestOfMany('login_at');
    }


    public function isLoggedIn(): bool
    {
        return $this->latestLogin?->logout_at === null;
    }
}
