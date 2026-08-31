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

    /**
     * The state columns belong here.
     *
     * Every read, unread, dismiss and undismiss writes them through
     * updateOrCreate(), whose second argument goes through fill() -- and fill()
     * honours $fillable. Without these four listed, the row was created and the
     * timestamps were silently dropped, so nothing ever persisted: the page
     * updated optimistically and reverted on the next load.
     */
    protected $fillable = ['app_notification_id', 'user_id', 'read_at', 'dismissed_at', 'u_del_notif_at', 'deleted_at'];

    protected $casts = [
        'read_at' => 'datetime',
        'dismissed_at' => 'datetime',
        'u_del_notif_at' => 'datetime',
        'deleted_at' => 'datetime',
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
