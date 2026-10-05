<?php

namespace App\Enums;

enum MediaType: string
{
    case MusicVideo = 'music_video';
    case Visualizer = 'visualizer';
    case DjMix = 'dj_mix';
    case Performance = 'performance';

    public function label(): string
    {
        return match ($this) {
            self::MusicVideo => 'Music video',
            self::Visualizer => 'Visualizer',
            self::DjMix => 'DJ mix',
            self::Performance => 'Live performance',
        };
    }
}