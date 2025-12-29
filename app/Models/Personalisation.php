<?php

namespace App\Models;

use App\Observers\PersonalisationObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[ObservedBy(PersonalisationObserver::class)]
class Personalisation extends Model
{
    use HasUlids;
    use HasFactory;

    protected $guarded = ['id'];

    protected $fillable = ['app_name', 'app_logo', 'app_logo_dark', 'favicon', 'copyright_text'];

    protected $casts = [
        'app_name' => 'string',
        'app_logo' => 'string',
        'app_logo_dark' => 'string',
        'favicon' => 'string',
        'copyright_text' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected static function booted()
    {
        // Handled in Observer
        static::saved(function ($settings) {
            //
        });
    }
}
