<?php

namespace App\Http\Controllers;

use App\Enums\ReleaseType;
use App\Models\ArtistProfile;
use App\Models\Photo;
use App\Models\PressAsset;
use App\Models\PressKit;
use App\Models\Release;
use Illuminate\Contracts\View\View;

class PressKitController extends Controller
{
    public function __invoke(): View
    {
        $profile = ArtistProfile::current();

        // Bootlegs are unofficial, so they stay out of the press kit.
        $releases = Release::published()
            ->where('type', '!=', ReleaseType::Bootleg->value)
            ->orderByDesc('is_featured')
            ->orderByDesc('release_date')
            ->limit(6)
            ->get();

        return view('press', [
            'profile' => $profile,
            'press' => PressKit::current(),
            'biography' => $this->paragraphs($profile->biography),
            'releases' => $releases,
            'photos' => Photo::published()->where('in_press_kit', true)->ordered()->get(),
            'assets' => PressAsset::published()->ordered()->get(),
            'socials' => collect($profile->social_links ?? [])->filter()->all(),
        ]);
    }

    private function paragraphs(?string $text): array
    {
        $parts = preg_split('/\R{2,}/', trim((string) $text)) ?: [];

        return array_values(array_filter(array_map('trim', $parts), fn ($p) => $p !== ''));
    }
}