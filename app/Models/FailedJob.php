<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Laravel writes this table but ships no model for it.
 *
 * An Eloquent model rather than DB::table() because DataTableService::process
 * declares Builder|QueryBuilder but its applySearch only accepts the Eloquent
 * one, so a query builder fails at runtime.
 */
class FailedJob extends Model
{
    protected $table = 'failed_jobs';

    public $timestamps = false;

    protected $guarded = [];

    protected $casts = [
        'failed_at' => 'datetime',
    ];
}
