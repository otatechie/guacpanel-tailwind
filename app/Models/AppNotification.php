<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AppNotification extends Model
{
    use HasUlids;
    use SoftDeletes;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'scope',
        'type',
        'title',
        'message',
        'data',
        'auto_expire_on',
        'read_at',
        'dismissed_at',
        'deleted_at',
    ];

    protected $casts = [
        'data'              => 'array',
        'auto_expire_on'    => 'datetime',
        'read_at'           => 'datetime',
        'dismissed_at'      => 'datetime',
        'deleted_at'        => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reads()
    {
        return $this->hasMany(AppNotificationRead::class);
    }
}
