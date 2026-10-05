<?php

namespace App\Http\Controllers;

use App\Enums\MediaType;
use App\Models\ArtistProfile;
use App\Models\MediaItem;
use App\Models\Photo;
use App\Models\Release;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $profile = ArtistProfile::current();

        $featured = Release::published()
            ->with('links')
            ->where('is_featured', true)
            ->orderByDesc('release_date')
            ->orderByDesc('id')
            ->first();

        $releases = Release::published()
            ->with('links')
            ->when($featured, fn ($query) => $query->where('id', '!=', $featured->id))
            ->orderByDesc('release_date')
            ->orderByDesc('id')
            ->limit(4)
            ->get();

        $heroPhoto = Photo::published()
            ->where('placement', 'hero')
            ->ordered()
            ->first();

        $aboutPhoto = Photo::published()
            ->where('placement', 'about')
            ->ordered()
            ->first();

        $galleryPhotos = Photo::published()
            ->where('placement', 'gallery')
            ->ordered()
            ->limit(6)
            ->get();

        $latestVideo = MediaItem::published()
            ->whereIn('type', [
                MediaType::MusicVideo->value,
                MediaType::Visualizer->value,
                MediaType::Performance->value,
            ])
            ->orderByDesc('published_on')
            ->orderByDesc('id')
            ->first();

        $platforms = collect(ArtistProfile::SOCIAL_PLATFORMS)
            ->map(fn ($name, $key) => [
                'name' => $name,
                'url' => $profile->social_links[$key] ?? null,
            ])
            ->filter(fn ($platform) => filled($platform['url']))
            ->values();

        return view('home', compact(
            'profile',
            'featured',
            'releases',
            'heroPhoto',
            'aboutPhoto',
            'galleryPhotos',
            'latestVideo',
            'platforms',
        ));
    }
}