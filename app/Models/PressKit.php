<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PressKit extends Model
{
    protected $fillable = [
        'real_name',
        'nickname',
        'active_since',
        'genres',
        'awards',
        'influences',
        'collaborators',
    ];

    protected function casts(): array
    {
        return [
            'active_since' => 'integer',
        ];
    }

    /** Used until the record is first saved in the admin. */
    public static function defaults(): array
    {
        return [
            'real_name' => 'Tokelo Foso',
            'nickname' => 'Slickster',
            'active_since' => 2013,
            'genres' => 'Hip-hop, R&B, Trap soul, Amapiano, Deep house',
            'awards' => "Ultimate Music Award Best Producer, 2015\nLNIG Hit Factory Best Producer, 2026",
            'influences' => "Metro Boomin\nLex Luger\nBlack Steel\nS-Jizzle Beats",
            'collaborators' => "T-Mech\nSkebz D\nSnrd\nThorii\nKizzy Keith\nSCOTT (producer collaboration, amapiano)",
        ];
    }

    public static function current(): self
    {
        return static::find(1) ?? new static(static::defaults());
    }

    /** One entry per line, blanks removed. */
    public function lines(string $field): array
    {
        $parts = preg_split('/\R/', (string) $this->{$field}) ?: [];

        return array_values(array_filter(array_map('trim', $parts), fn ($line) => $line !== ''));
    }
}