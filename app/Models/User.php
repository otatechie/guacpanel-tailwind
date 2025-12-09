<?php

namespace App\Models;

use App\Notifications\VerifyEmailFromAdminTriggered;
use App\Observers\UserObserver;
use App\Traits\UserAccountRestoreTrait;
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
use Laravel\Fortify\Features;
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
    use HasRoles;
    use HasUlids;
    use Notifiable;
    use \OwenIt\Auditing\Auditable;
    use Searchable;
    use SoftDeletes;
    use TwoFactorAuthenticatable;
    use UserAccountRestoreTrait;

    protected $guarded = ['id'];

    protected $fillable = [
        'name',
        'email',
        'password',
        'email_verified_at',
        'password_expiry_at',
        'password_changed_at',
        'force_password_change',
        'profile_image_type',
        'disable_account',          // This is the user disabling their account.
        'account_locked',           // This is plug of kicking the user out of the app.
        'restore_token',
        'auto_destroy',
        'restore_date',
    ];

    protected $casts = [
        'email_verified_at'     => 'datetime',
        'password'              => 'hashed',
        'password_expiry_at'    => 'datetime',
        'password_changed_at'   => 'datetime',
        'force_password_change' => 'boolean',
        'disable_account'       => 'boolean',   // This is the user disabling their account.
        'profile_image_type'    => 'string',    // This is a string to later allow other types.
        'account_locked'        => 'boolean',   // This is plug of kicking the user out of the app.
        'restore_token'         => 'string',
        'auto_destroy'          => 'boolean',
        'created_at'            => 'datetime',
        'updated_at'            => 'datetime',
        'deleted_at'            => 'datetime',
        'restore_date'          => 'datetime',
    ];

    protected $appends = ['created_at_formatted'];

    public function scopeWithDeleted(Builder $query): Builder
    {
        return $query->withTrashed();
    }

    public function scopeOnlyDeleted(Builder $query): Builder
    {
        return $query->onlyTrashed();
    }

    public function scopeAutoDestroyable(Builder $query): Builder
    {
        $thresholdDate = $this->generateDestroyThresholdTimestamp();

        return $query
            ->onlyTrashed()
            ->where('auto_destroy', true)
            ->where('deleted_at', '<=', $thresholdDate);
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
                    's' => 200,
                    'd' => 'identicon',
                    'r' => 'g',
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

    public function autoDestroyDate(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->calculateAutoDestroyDate(),
        );
    }

    public function autoDestroyDateFormatted(): Attribute
    {
        return Attribute::make(
            get: function () {
                $date = $this->calculateAutoDestroyDate();

                if (!$this->auto_destroy || !$date) {
                    return null;
                }

                return $date->format('m/j/Y');
            },
        );
    }

    public function autoDestroyDateFull(): Attribute
    {
        return Attribute::make(
            get: function () {
                $date = $this->calculateAutoDestroyDate();

                if (!$this->auto_destroy || !$date) {
                    return null;
                }

                return $date->format('m/j/Y @ g:i A');
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

        $expiryDate = $this->password_expiry_at instanceof Carbon
            ? $this->password_expiry_at
            : Carbon::parse($this->password_expiry_at);

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

    // There is Fortify/Laravel conflict that we cannot resolve on the app side.
    // Per-request guard: only send once for this user
    public function sendEmailVerificationNotification(): void
    {
        if (
            ! config('guacpanel.email_verification_enabled')
            || ! Features::enabled(Features::emailVerification())
        ) {
            return;
        }

        $key = 'guacpanel.verification_sent_for';

        if (app()->bound($key) && app($key) === $this->getKey()) {
            return;
        }

        app()->instance($key, $this->getKey());

        $this->notify(new VerifyEmail());
    }

    public function sendUserEmailVerificationFromAdmin(): void
    {
        if (!config('guacpanel.email_verification_enabled')) {
            return;
        }

        $this->notify(new VerifyEmailFromAdminTriggered());
    }
}
