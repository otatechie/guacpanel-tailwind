<?php

namespace App\Models;

use Laravel\Scout\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class FinancialMetric extends Model
{
    use HasUlids;

    use Searchable;

    protected $guarded = ['id'];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2'
    ];


    public function toSearchableArray(): array
    {
        return array_merge($this->toArray(), [
            'id' => (string) $this->id,
            'created_at' => $this->created_at->timestamp,
            'collection_name' => 'financial_metrics',
            'amount' => (float) $this->amount,
            'category' => $this->category,
        ]);
    }
}
