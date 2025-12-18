<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppNotificationRead extends Model
{
    use HasUlids;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'app_notification_id',
        'user_id',
        'read_at',
        'dismissed_at',
        'u_del_notif_at',
        'deleted_at',
    ];

    protected $casts = [
        'read_at'           => 'datetime',
        'dismissed_at'      => 'datetime',
        'u_del_notif_at'    => 'datetime',
        'deleted_at'        => 'datetime',
    ];

    public function notification(): BelongsTo
    {
        return $this->belongsTo(AppNotification::class, 'app_notification_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
