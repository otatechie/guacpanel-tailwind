<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class Personalisation extends Model
{
    use HasUlids;

    protected $guarded = ['id'];


    protected static function booted()
    {
        static::saved(function ($settings) {
            cache()->forget('system_settings');
        });
    }
}
