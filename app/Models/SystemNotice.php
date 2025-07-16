<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SystemNotice extends Model
{
    use HasUlids, HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'visible_from' => 'datetime',
        'expires_at' => 'datetime',
    ];

}
