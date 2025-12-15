<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

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
        'deleted_at',
    ];

    protected $casts = [
        'read_at'      => 'datetime',
        'dismissed_at' => 'datetime',
        'deleted_at'   => 'datetime',
    ];

    public function notification()
    {
        return $this->belongsTo(AppNotification::class, 'app_notification_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
