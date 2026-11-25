<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SubActivityPlan extends Model
{
    protected $fillable = [
        'year',
        'organization_unit',
        'sub_organization_unit',
        'program_code',
        'program_name',
        'activity_code',
        'activity_name',
        'sub_activity_code',
        'sub_activity_name',
        'spm',
        'service_type',
        'funding_source',
        'location',
        'implementation_time',
        'target_group',
        'output',
        'user_id',
    ];

    protected function casts(): array
    {
        return ['year' => 'integer'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function indicators(): HasMany
    {
        return $this->hasMany(PlanIndicator::class)->orderBy('sort_order');
    }

    public function budgetLines(): HasMany
    {
        return $this->hasMany(PlanBudgetLine::class)->orderBy('sort_order');
    }

    public function report(): HasOne
    {
        return $this->hasOne(Report::class);
    }

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query
            ->when($filters['year'] ?? null, fn (Builder $query, string $year) => $query->where('year', $year))
            ->when($filters['search'] ?? null, function (Builder $query, string $search) {
                $query->where(function (Builder $query) use ($search) {
                    $query->where('sub_activity_name', 'like', "%{$search}%")
                        ->orWhere('sub_activity_code', 'like', "%{$search}%")
                        ->orWhere('program_name', 'like', "%{$search}%")
                        ->orWhere('activity_name', 'like', "%{$search}%");
                });
            });
    }

    public function getTotalBeforeAttribute(): float
    {
        return (float) $this->budgetLines->sum(fn (PlanBudgetLine $line) => $line->before_total);
    }

    public function getTotalAfterAttribute(): float
    {
        return (float) $this->budgetLines->sum(fn (PlanBudgetLine $line) => $line->after_total);
    }

    public function canBeEditedBy(User $user): bool
    {
        return $user->isAdmin()
            || ($user->canManageData()
                && $this->user_id === $user->id
                && $this->report?->status !== 'approved');
    }
}
