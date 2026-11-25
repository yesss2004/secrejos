<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Report extends Model
{
    protected $fillable = [
        'title',
        'type',
        'year',
        'month',
        'description',
        'anyflip_url',
        'status',
        'user_id',
        'reviewer_id',
        'review_note',
        'reviewed_at',
        'sub_activity_plan_id',
    ];

    protected function casts(): array
    {
        return [
            'year' => 'integer',
            'month' => 'integer',
            'reviewed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(ReportAttachment::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(SubActivityPlan::class, 'sub_activity_plan_id');
    }

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query
            ->when($filters['search'] ?? null, function (Builder $query, string $search) {
                $query->where(function (Builder $query) use ($search) {
                    $query->where('title', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when($filters['type'] ?? null, fn (Builder $query, string $type) => $query->where('type', $type))
            ->when($filters['status'] ?? null, fn (Builder $query, string $status) => $query->where('status', $status))
            ->when($filters['year'] ?? null, fn (Builder $query, string $year) => $query->where('year', $year));
    }

    public function getTypeLabelAttribute(): string
    {
        return $this->type === 'tahunan' ? 'Tahunan' : 'Bulanan';
    }

    public function getPeriodLabelAttribute(): string
    {
        if ($this->type === 'tahunan' || ! $this->month) {
            return 'Tahun '.$this->year;
        }

        $month = Carbon::createFromDate($this->year, $this->month, 1)->locale('id')->translatedFormat('F');

        return "{$month} {$this->year}";
    }

    public function getShortPeriodAttribute(): string
    {
        if ($this->type === 'tahunan' || ! $this->month) {
            return (string) $this->year;
        }

        return Carbon::createFromDate($this->year, $this->month, 1)->locale('id')->translatedFormat('M Y');
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            default => 'Menunggu Review',
        };
    }

    public function getStatusToneAttribute(): string
    {
        return match ($this->status) {
            'approved' => 'emerald',
            'rejected' => 'rose',
            default => 'amber',
        };
    }

    public function getStatusIconAttribute(): string
    {
        return match ($this->status) {
            'approved' => 'check',
            'rejected' => 'x',
            default => 'clock',
        };
    }

    public function getExcerptAttribute(): string
    {
        return Str::limit(strip_tags($this->description), 120);
    }

    public function getTotalAttachmentSizeAttribute(): int
    {
        return (int) $this->attachments->sum('file_size');
    }

    public function canBeEditedBy(User $user): bool
    {
        return $user->isAdmin()
            || ($user->canManageData() && $this->user_id === $user->id && $this->status === 'pending');
    }
}
