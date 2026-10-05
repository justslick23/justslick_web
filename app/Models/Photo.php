<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Photo extends Model
{
    public const PLACEMENTS = [
        'hero' => 'Homepage hero',
        'about' => 'About',
        'gallery' => 'Gallery',
    ];

    protected $fillable = [
        'title',
        'alt_text',
        'image_path',
        'width',
        'height',
        'placement',
        'credit',
        'in_press_kit',
        'is_published',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'width' => 'integer',
            'height' => 'integer',
            'in_press_kit' => 'boolean',
            'is_published' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    protected function imageUrl(): Attribute
    {
        return Attribute::get(
            fn () => $this->image_path
                ? asset('uploads/'.$this->image_path)
                : null
        );
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query
            ->orderBy('sort_order')
            ->orderByDesc('id');
    }
}