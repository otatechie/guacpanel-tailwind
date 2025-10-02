<?php

namespace App\Models;

 
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Scout\Searchable;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable implements Auditable
{
    use HasApiTokens, HasFactory, Notifiable, TwoFactorAuthenticatable, SoftDeletes;
    use HasUlids;
    use \OwenIt\Auditing\Auditable;
    use HasRoles;
    use Searchable;

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
        'deleted_at' => 'datetime',
    ];

    protected $appends = ['created_at_formatted'];

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


    public function formatDateStyle(?Carbon $date = null): string
    {
        $date = $date ?? $this->created_at;

        if (!$date) {
            return '';
        }

        if ($date->diffInMinutes() < 5) {
            return 'Just now';
        }

        if ($date->isToday()) {
            return $date->diffForHumans(['short' => false, 'parts' => 1]);
        }

        if ($date->isYesterday()) {
            return 'Yesterday';
        }

        if ($date->isCurrentYear()) {
            return $date->format('F j');
        }

        return $date->format('F j, Y');
    }


    public function getCreatedAtFormattedAttribute(): string
    {
        return $this->formatDateStyle($this->created_at);
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


    public function isSuperUser(): bool
    {
        return $this->hasRole('superuser');
    }


    public function canBeDeleted(): bool
    {
        return !$this->isSuperUser();
    }


    public function canChangeRole(): bool
    {
        return !$this->isSuperUser();
    }


    public function canChangeAccountStatus(): bool
    {
        return !$this->isSuperUser();
    }


    public function toSearchableArray(): array
    {
        return array_merge($this->toArray(), [
            'id' => (string) $this->id,
            'created_at' => $this->created_at->timestamp,
            'collection_name' => 'users',
        ]);
    }
}
