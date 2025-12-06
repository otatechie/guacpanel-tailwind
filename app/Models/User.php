<?php

namespace App\Models;

use App\Notifications\VerifyEmailFromAdminTriggered;
use App\Observers\UserObserver;
use Carbon\Carbon;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Scout\Searchable;
use Laravolt\Avatar\Facade as Avatar;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Permission\Traits\HasRoles;

#[ObservedBy(UserObserver::class)]
class User extends Authenticatable implements Auditable, MustVerifyEmail
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use SoftDeletes;
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
        'profile_image_type',
        'restore_token',
        'auto_destroy',
        'auto_destroy_date',
        'restore_date',
    ];

    protected $casts = [
        'email_verified_at'     => 'datetime',
        'password'              => 'hashed',
        'password_expiry_at'    => 'datetime',
        'password_changed_at'   => 'datetime',
        'force_password_change' => 'boolean',
        'disable_account'       => 'boolean',
        'profile_image_type'    => 'string', // This is a string to later allow other types
        'restore_token'         => 'string',
        'auto_destroy'          => 'boolean',
        'created_at'            => 'datetime',
        'updated_at'            => 'datetime',
        'deleted_at'            => 'datetime',
        'auto_destroy_date'     => 'datetime',
        'restore_date'          => 'datetime',
    ];

    protected $appends = ['created_at_formatted'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            $user->user_slug = 'user-'.Str::random(12);
            if (!$user->password) {
                $user->password = null;
            }
        });
    }

    public function scopeWithDeleted(Builder $query): Builder
    {
        return $query->withTrashed();
    }

    public function scopeOnlyDeleted(Builder $query): Builder
    {
        return $query->onlyTrashed();
    }

    protected function avatar(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                return Avatar::create($this->name)
                    ->setFontSize(48)
                    ->setDimension(200, 200)
                    ->setTheme('pastel')
                    ->toBase64();
            },
        );
    }

    protected function gravatar(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                if (!$this->email) {
                    return $this->avatar;
                }

                return Avatar::create($this->email)->toGravatar([
                    's' => 200,          // size
                    'd' => 'identicon',  // default image
                    'r' => 'g',          // rating
                ]);
            },
        );
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

        if ($date->diffInHours() < 24) {
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

    public function createdAtFull(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                if ($this->created_at) {
                    return $this->created_at->format('m/j/Y @ g:i A');
                }

                return null;
            },
        );
    }

    public function createdAtFormatted(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                return $this->formatDateStyle($this->created_at);
            },
        );
    }

    public function deletedAtFormatted(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                return $this->formatDateStyle($this->deleted_at);
            },
        );
    }

    public function deletedAtFull(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                if ($this->deleted_at) {
                    return $this->deleted_at->format('m/j/Y @ g:i A');
                }

                return null;
            },
        );
    }

    public function autoDestroyDateFormatted(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                if ($this->auto_destroy && $this->auto_destroy_date) {
                    return $this->auto_destroy_date->format('m/j/Y');
                }

                return null;
            },
        );
    }

    public function autoDestroyDateFull(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                if ($this->auto_destroy && $this->auto_destroy_date) {
                    return $this->auto_destroy_date->format('m/j/Y @ g:i A');
                }

                return null;
            },
        );
    }

    public function restoreDateFormatted(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                if ($this->restore_date) {
                    return $this->restore_date->format('m/j/Y');
                }

                return null;
            },
        );
    }

    public function restoreDateFull(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                if ($this->restore_date) {
                    return $this->restore_date->format('m/j/Y @ g:i A');
                }

                return null;
            },
        );
    }

    public function emailVerifiedAtFormatted(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                if ($this->email_verified_at) {
                    return $this->email_verified_at->format('m/j/Y');
                }

                return null;
            },
        );
    }

    public function emailVerifiedAtFull(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                if ($this->email_verified_at) {
                    return $this->email_verified_at->format('m/j/Y @ g:i A');
                }

                return null;
            },
        );
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
            'id'              => (string) $this->id,
            'created_at'      => $this->created_at->timestamp,
            'collection_name' => 'users',
        ]);
    }

    public function sendEmailVerificationNotification(): void
    {
        if (! config('guacpanel.email_verification_enabled')) {
            return;
        }

        $this->notify(new VerifyEmail());
    }

    public function sendUserEmailVerificationFromAdmin(): void
    {
        if (! config('guacpanel.email_verification_enabled')) {
            return;
        }

        $this->notify(new VerifyEmailFromAdminTriggered());
    }

}
