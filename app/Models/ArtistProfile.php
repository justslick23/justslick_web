<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArtistProfile extends Model
{
    public const SOCIAL_PLATFORMS = [
        'instagram' => 'Instagram',
        'facebook' => 'Facebook',
        'tiktok' => 'TikTok',
        'x' => 'X',
        'youtube' => 'YouTube',
        'soundcloud' => 'SoundCloud',
        'spotify' => 'Spotify',
        'apple_music' => 'Apple Music',
    ];

    protected $fillable = [
        'artist_name',
        'tagline',
        'location',
        'short_bio',
        'biography',
        'award',
        'booking_email',
        'social_links',
    ];

    protected function casts(): array
    {
        return [
            'social_links' => 'array',
        ];
    }

    public static function defaults(): array
    {
        return [
            'artist_name' => 'Just Slick',
            'tagline' => 'Producer & DJ',
            'location' => 'Lesotho',
            'award' => 'LNIG Hit Factory Best Producer 2026',
            'booking_email' => 'hello@tokelofoso.online',
            'social_links' => [
                'instagram' => 'https://www.instagram.com/slkstrgrm/',
                'facebook' => 'https://www.facebook.com/slkstrville',
                'tiktok' => 'https://www.tiktok.com/@slxcktok',
                'x' => 'https://x.com/slkstr_',
                'soundcloud' => 'https://soundcloud.com/justslick23',
            ],
        ];
    }

    public static function current(): self
    {
        return static::find(1) ?? new static(static::defaults());
    }
}