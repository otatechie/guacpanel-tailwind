<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Carbon\Carbon;
use App\Models\Charity;
use App\Models\Donation;
use App\Models\BankAccount;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use OwenIt\Auditing\Contracts\Auditable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements Auditable
{
    use HasApiTokens, HasFactory, Notifiable, TwoFactorAuthenticatable;

    use HasUlids;

    use \OwenIt\Auditing\Auditable;

    use HasRoles;

    protected $guarded = ['id'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'password_expiry_at' => 'datetime',
    ];


    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            $user->user_slug = 'user-' . Str::random(12);
        });
    }


    public function isPasswordExpired(): bool
    {
        if (!$this->password_expiry_at) {
            return false;
        }
        return now()->gte(Carbon::parse($this->password_expiry_at));
    }


    public function daysUntilPasswordExpiry(): int
    {
        if (!$this->password_expiry_at) {
            return 0;
        }

        $expiryDate = Carbon::createFromTimestamp($this->password_expiry_at);
        return max(0, now()->diffInDays($expiryDate));
    }
}
