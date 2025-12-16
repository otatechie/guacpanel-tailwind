<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AppNotification extends Model
{
    use HasFactory;
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
        'read_at',
        'dismissed_at',
        'auto_expire_on',
        'scheduled_on',
        'sent_as_scheduled',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'id'                => 'string',
        'user_id'           => 'string',
        'scope'             => 'string',
        'type'              => 'string',
        'title'             => 'string',
        'message'           => 'string',
        'data'              => 'array',
        'sent_as_scheduled' => 'boolean',
        'scheduled_on'      => 'datetime',
        'auto_expire_on'    => 'datetime',
        'read_at'           => 'datetime',
        'dismissed_at'      => 'datetime',
        'created_at'        => 'datetime',
        'updated_at'        => 'datetime',
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
