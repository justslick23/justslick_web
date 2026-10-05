<?php

return [
    // While true, "Sample content" badges and placeholder notes are shown.
    // Set SITE_SAMPLE_CONTENT=false in .env once real content is in place.
    'sample_content' => env('SITE_SAMPLE_CONTENT', true),

    // Where the hero photo is anchored when cropped (CSS object-position).
    'hero_focus' => env('SITE_HERO_FOCUS', '60% 25%'),

    // Streaming and social profiles. Add URLs in .env; managed in the admin from Stage 4.
    'platforms' => [
        ['name' => 'Spotify', 'url' => env('SITE_SPOTIFY_URL')],
        ['name' => 'Apple Music', 'url' => env('SITE_APPLE_MUSIC_URL')],
        ['name' => 'YouTube', 'url' => env('SITE_YOUTUBE_URL')],
        ['name' => 'SoundCloud', 'url' => env('SITE_SOUNDCLOUD_URL')],
        ['name' => 'Instagram', 'url' => env('SITE_INSTAGRAM_URL')],
    ],
];