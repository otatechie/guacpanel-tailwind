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
        'created_by',
        'scope',
        'type',
        'title',
        'message',
        'data',
        'auto_expire_on',
        'scheduled_on',
        'sent_as_scheduled',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'data' => 'array',
        'sent_as_scheduled' => 'boolean',
        'scheduled_on' => 'datetime',
        'auto_expire_on' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function reads()
    {
        return $this->hasMany(AppNotificationRead::class);
    }
}
