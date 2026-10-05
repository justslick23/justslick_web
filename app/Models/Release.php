<?php

namespace App\Models;

use App\Enums\ReleaseType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
class Release extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'type',
        'release_date',
        'description',
        'credits',
        'embed_url',
        'audio_path',
        'cover_path',
        'is_published',
        'is_featured',
    ];

    protected function casts(): array
    {
        return [
            'type' => ReleaseType::class,
            'release_date' => 'date',
            'is_published' => 'boolean',
            'is_featured' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        // The slug is created once and stays stable, so public URLs never change.
        static::creating(function (Release $release) {
            $base = Str::slug($release->title) ?: 'release';
            $slug = $base;
            $i = 2;

            while (static::where('slug', $slug)->exists()) {
                $slug = $base.'-'.$i++;
            }

            $release->slug = $slug;
        });
    }

    public function links(): HasMany
    {
        return $this->hasMany(ReleaseLink::class)->orderBy('sort_order');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    protected function coverUrl(): Attribute
    {
        return Attribute::get(
            fn () => $this->cover_path ? asset('uploads/'.$this->cover_path) : null
        );
    }

    public function mediaItems(): HasMany
{
    return $this->hasMany(MediaItem::class);
}

protected function audioUrl(): Attribute
{
    return Attribute::get(
        fn () => $this->audio_path
            ? Storage::disk('public')->url($this->audio_path)
            : null
    );
}
}