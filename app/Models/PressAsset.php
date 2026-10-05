<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class PressAsset extends Model
{
    public const KINDS = [
        'one_sheet' => 'One-sheet',
        'photo_pack' => 'Photo pack',
        'logo' => 'Logo files',
        'other' => 'File',
    ];

    protected $fillable = [
        'title',
        'kind',
        'file_path',
        'original_name',
        'size_bytes',
        'is_published',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'size_bytes' => 'integer',
            'is_published' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    protected function fileUrl(): Attribute
    {
        return Attribute::get(
            fn () => $this->file_path ? asset('uploads/'.$this->file_path) : null
        );
    }

    protected function kindLabel(): Attribute
    {
        return Attribute::get(fn () => self::KINDS[$this->kind] ?? 'File');
    }

    protected function humanSize(): Attribute
    {
        return Attribute::get(function () {
            $bytes = (int) $this->size_bytes;

            if ($bytes <= 0) {
                return null;
            }

            $units = ['B', 'KB', 'MB', 'GB'];
            $i = min((int) floor(log($bytes, 1024)), count($units) - 1);

            return round($bytes / (1024 ** $i), $i > 1 ? 1 : 0).' '.$units[$i];
        });
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderByDesc('id');
    }
}