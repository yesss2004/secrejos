<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportAttachment extends Model
{
    protected $fillable = [
        'report_id',
        'file_path',
        'file_name',
        'file_size',
        'mime_type',
    ];

    protected function casts(): array
    {
        return [
            'file_size' => 'integer',
        ];
    }

    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class);
    }

    public function getHumanSizeAttribute(): string
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];

        $power = $bytes > 0 ? floor(log($bytes, 1024)) : 0;
        $power = min($power, count($units) - 1);

        return number_format($bytes / (1024 ** $power), $power > 1 ? 1 : 0).' '.$units[$power];
    }

    public function getExtensionAttribute(): string
    {
        return strtoupper(pathinfo($this->file_name, PATHINFO_EXTENSION) ?: 'FILE');
    }

    public function getIconToneAttribute(): string
    {
        return match ($this->extension) {
            'PDF' => 'rose',
            'DOC', 'DOCX' => 'sky',
            'XLS', 'XLSX', 'CSV' => 'emerald',
            'JPG', 'JPEG', 'PNG' => 'amber',
            default => 'slate',
        };
    }
}
