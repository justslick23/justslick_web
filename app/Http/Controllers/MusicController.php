<?php

namespace App\Http\Controllers;

use App\Enums\MediaType;
use App\Models\ArtistProfile;
use App\Models\MediaItem;
use App\Models\Photo;
use App\Models\Release;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;
class MusicController extends Controller
{
    public function index(): View
    {
        $releases = Release::published()
            ->orderByDesc('release_date')
            ->orderByDesc('id')
            ->paginate(12);

        return view('music.index', array_merge(
            $this->pageData(),
            compact('releases'),
        ));
    }

    public function show(string $slug): View
    {
        $release = Release::published()
            ->with([
                'links',
                'mediaItems' => fn ($query) => $query->published()->ordered(),
            ])
            ->where('slug', $slug)
            ->firstOrFail();

        return view('music.show', array_merge(
            $this->pageData(),
            compact('release'),
        ));
    }

    private function pageData(): array
    {
        $profile = ArtistProfile::current();

        // These links point to sections on the homepage.
        $showWatch = MediaItem::published()
            ->whereIn('type', [
                MediaType::MusicVideo->value,
                MediaType::Visualizer->value,
                MediaType::Performance->value,
            ])
            ->exists();

        $showAbout = filled($profile->biography)
            || filled($profile->short_bio)
            || Photo::published()
                ->whereIn('placement', ['about', 'gallery'])
                ->exists();

        return compact('profile', 'showWatch', 'showAbout');
    }
}