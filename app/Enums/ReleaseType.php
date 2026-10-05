<?php

namespace App\Enums;

enum ReleaseType: string
{
    case Official = 'official';
    case Remix = 'remix';
    case Bootleg = 'bootleg';
    case Mix = 'mix';

    public function label(): string
    {
        return match ($this) {
            self::Official => 'Official release',
            self::Remix => 'Remix',
            self::Bootleg => 'Bootleg',
            self::Mix => 'Mix',
        };
    }

    /** Bootlegs are shown publicly as unofficial. */
    public function isOfficial(): bool
    {
        return $this !== self::Bootleg;
    }
}