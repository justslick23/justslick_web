<?php

namespace App\Http\Controllers\Admin;

use App\Enums\MediaType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MediaItemRequest;
use App\Models\MediaItem;
use App\Models\Release;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class MediaItemController extends Controller
{
    public function index(): View
    {
        $items = MediaItem::with('release:id,title')
            ->ordered()
            ->paginate(15);

        return view('admin.media.index', compact('items'));
    }

    public function create(): View
    {
        $mediaItem = new MediaItem([
            'type' => MediaType::MusicVideo,
            'is_published' => false,
            'sort_order' => 0,
        ]);

        return $this->form($mediaItem);
    }

    public function store(MediaItemRequest $request): RedirectResponse
    {
        $mediaItem = MediaItem::create($request->validated());

        return redirect()
            ->route('admin.media.edit', $mediaItem)
            ->with('status', 'Media item created.');
    }

    public function edit(MediaItem $mediaItem): View
    {
        return $this->form($mediaItem);
    }

    public function update(
        MediaItemRequest $request,
        MediaItem $mediaItem
    ): RedirectResponse {
        $mediaItem->update($request->validated());

        return redirect()
            ->route('admin.media.edit', $mediaItem)
            ->with('status', 'Media item saved.');
    }

    public function destroy(MediaItem $mediaItem): RedirectResponse
    {
        $mediaItem->delete();

        return redirect()
            ->route('admin.media.index')
            ->with('status', 'Media item deleted.');
    }

    private function form(MediaItem $mediaItem): View
    {
        return view('admin.media.form', [
            'mediaItem' => $mediaItem,
            'types' => MediaType::cases(),
            'releases' => Release::orderBy('title')->get(['id', 'title']),
        ]);
    }
}