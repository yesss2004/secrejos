<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlanBudgetLine extends Model
{
    protected $fillable = [
        'account_code',
        'description',
        'coefficient_before',
        'unit_before',
        'unit_price_before',
        'tax_before',
        'coefficient_after',
        'unit_after',
        'unit_price_after',
        'tax_after',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'coefficient_before' => 'float',
            'unit_price_before' => 'float',
            'tax_before' => 'float',
            'coefficient_after' => 'float',
            'unit_price_after' => 'float',
            'tax_after' => 'float',
        ];
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(SubActivityPlan::class, 'sub_activity_plan_id');
    }

    public function getBeforeTotalAttribute(): float
    {
        return round(($this->coefficient_before * $this->unit_price_before) + $this->tax_before, 2);
    }

    public function getAfterTotalAttribute(): float
    {
        return round(($this->coefficient_after * $this->unit_price_after) + $this->tax_after, 2);
    }

    public function getDifferenceAttribute(): float
    {
        return $this->after_total - $this->before_total;
    }
}
