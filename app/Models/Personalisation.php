<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Personalisation extends Model
{
    use HasUlids, HasFactory;

    protected $guarded = ['id'];

    protected static function booted()
    {
        static::saved(function ($settings) {
            cache()->forget('system_settings');
        });
    }
}
