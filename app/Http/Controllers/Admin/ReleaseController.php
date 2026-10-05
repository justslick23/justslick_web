<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ReleaseType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ReleaseRequest;
use App\Models\Release;
use App\Support\ImageUploader;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ReleaseController extends Controller
{
    public function index(): View
    {
        $releases = Release::withCount('links')
            ->orderByDesc('release_date')
            ->orderByDesc('id')
            ->paginate(20);

        return view('admin.releases.index', compact('releases'));
    }

    public function create(): View
    {
        $release = new Release([
            'type' => ReleaseType::Official,
        ]);

        return view('admin.releases.create', [
            'release' => $release,
            'types' => ReleaseType::cases(),
            'linkRows' => $this->linkRows(null),
        ]);
    }

    public function store(ReleaseRequest $request): RedirectResponse
    {
        $release = DB::transaction(function () use ($request) {
            $data = $request->releaseData();

            /*
            |--------------------------------------------------------------------------
            | Cover
            |--------------------------------------------------------------------------
            */

            if ($request->hasFile('cover')) {
                $data['cover_path'] = ImageUploader::storeCover(
                    $request->file('cover')
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Audio
            |--------------------------------------------------------------------------
            */

            if ($request->hasFile('audio')) {
                $data['audio_path'] = $this->storeAudio(
                    $request->file('audio')
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Release
            |--------------------------------------------------------------------------
            */

            $release = Release::create($data);

            $this->syncLinks(
                $release,
                $request->linkRows()
            );

            $this->enforceSingleFeatured($release);

            return $release;
        });

        return redirect()
            ->route('admin.releases.edit', $release)
            ->with('status', 'Release created.');
    }

    public function edit(Release $release): View
    {
        $release->load('links');

        return view('admin.releases.edit', [
            'release' => $release,
            'types' => ReleaseType::cases(),
            'linkRows' => $this->linkRows($release),
        ]);
    }

    public function update(
        ReleaseRequest $request,
        Release $release
    ): RedirectResponse {
        DB::transaction(function () use ($request, $release) {
            $data = $request->releaseData();

            /*
            |--------------------------------------------------------------------------
            | Cover
            |--------------------------------------------------------------------------
            */

            if ($request->hasFile('cover')) {
                ImageUploader::delete(
                    $release->cover_path
                );

                $data['cover_path'] = ImageUploader::storeCover(
                    $request->file('cover')
                );
            } elseif ($request->boolean('remove_cover')) {
                ImageUploader::delete(
                    $release->cover_path
                );

                $data['cover_path'] = null;
            }

            /*
            |--------------------------------------------------------------------------
            | Audio
            |--------------------------------------------------------------------------
            */

            if ($request->hasFile('audio')) {
                $this->deleteAudio(
                    $release->audio_path
                );

                $data['audio_path'] = $this->storeAudio(
                    $request->file('audio')
                );
            } elseif ($request->boolean('remove_audio')) {
                $this->deleteAudio(
                    $release->audio_path
                );

                $data['audio_path'] = null;
            }

            /*
            |--------------------------------------------------------------------------
            | Save
            |--------------------------------------------------------------------------
            */

            $release->update($data);

            $this->syncLinks(
                $release,
                $request->linkRows()
            );

            $this->enforceSingleFeatured($release);
        });

        return redirect()
            ->route('admin.releases.edit', $release)
            ->with('status', 'Release saved.');
    }

    public function destroy(Release $release): RedirectResponse
    {
        /*
        |--------------------------------------------------------------------------
        | Delete uploaded files
        |--------------------------------------------------------------------------
        */

        ImageUploader::delete(
            $release->cover_path
        );

        $this->deleteAudio(
            $release->audio_path
        );

        /*
        |--------------------------------------------------------------------------
        | Delete release
        |--------------------------------------------------------------------------
        */

        $release->delete();

        return redirect()
            ->route('admin.releases.index')
            ->with('status', 'Release deleted.');
    }

    private function syncLinks(
        Release $release,
        array $rows
    ): void {
        $release->links()->delete();

        foreach ($rows as $position => $row) {
            $release->links()->create(
                $row + [
                    'sort_order' => $position,
                ]
            );
        }
    }

    private function enforceSingleFeatured(
        Release $release
    ): void {
        if ($release->is_featured) {
            Release::whereKeyNot($release->id)
                ->update([
                    'is_featured' => false,
                ]);
        }
    }

    /**
     * Store an uploaded audio file.
     */
    private function storeAudio($file): string
    {
        return $file->store(
            'releases/audio',
            'public'
        );
    }

    /**
     * Delete an existing audio file.
     */
    private function deleteAudio(?string $path): void
    {
        if (! $path) {
            return;
        }

        Storage::disk('public')->delete($path);
    }

    /**
     * Existing links (or submitted input)
     * padded with blank rows for the form.
     */
    private function linkRows(
        ?Release $release
    ): array {
        $rows = old('links');

        if (! is_array($rows)) {
            $rows = $release
                ? $release->links
                    ->map(fn ($link) => [
                        'label' => $link->label,
                        'url' => $link->url,
                    ])
                    ->all()
                : [];
        }

        $rows = array_values($rows);

        $total = min(
            10,
            max(4, count($rows) + 2)
        );

        while (count($rows) < $total) {
            $rows[] = [
                'label' => '',
                'url' => '',
            ];
        }

        return $rows;
    }
}