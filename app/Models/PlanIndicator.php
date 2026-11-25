<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlanIndicator extends Model
{
    protected $fillable = [
        'category',
        'indicator_before',
        'benchmark_before',
        'target_before',
        'indicator_after',
        'benchmark_after',
        'target_after',
        'sort_order',
    ];

    public function plan(): BelongsTo
    {
        return $this->belongsTo(SubActivityPlan::class, 'sub_activity_plan_id');
    }
}
