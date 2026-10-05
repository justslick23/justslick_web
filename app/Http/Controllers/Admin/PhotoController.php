<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PhotoRequest;
use App\Models\Photo;
use App\Support\PhotoUploader;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Throwable;

class PhotoController extends Controller
{
    public function index(): View
    {
        return view('admin.photos.index', [
            'photos' => Photo::ordered()->paginate(12),
            'placements' => Photo::PLACEMENTS,
        ]);
    }

    public function create(): View
    {
        return $this->form(new Photo([
            'placement' => 'gallery',
            'sort_order' => 0,
            'is_published' => false,
            'in_press_kit' => false,
        ]));
    }

    public function store(PhotoRequest $request): RedirectResponse
    {
        $upload = PhotoUploader::store($request->file('image'));

        try {
            $photo = Photo::create(
                array_merge($request->photoData(), $upload)
            );
        } catch (Throwable $exception) {
            PhotoUploader::delete($upload['image_path']);
            throw $exception;
        }

        return redirect()
            ->route('admin.photos.edit', $photo)
            ->with('status', 'Photo uploaded.');
    }

    public function edit(Photo $photo): View
    {
        return $this->form($photo);
    }

    public function update(PhotoRequest $request, Photo $photo): RedirectResponse
    {
        $oldPath = $photo->image_path;
        $upload = null;

        if ($request->hasFile('image')) {
            $upload = PhotoUploader::store($request->file('image'));
        }

        try {
            $photo->update(
                array_merge($request->photoData(), $upload ?? [])
            );
        } catch (Throwable $exception) {
            if ($upload) {
                PhotoUploader::delete($upload['image_path']);
            }

            throw $exception;
        }

        // Remove the previous file only after the database update succeeds.
        if ($upload) {
            PhotoUploader::delete($oldPath);
        }

        return redirect()
            ->route('admin.photos.edit', $photo)
            ->with('status', 'Photo saved.');
    }

    public function destroy(Photo $photo): RedirectResponse
    {
        $path = $photo->image_path;
        $photo->delete();
        PhotoUploader::delete($path);

        return redirect()
            ->route('admin.photos.index')
            ->with('status', 'Photo deleted.');
    }

    private function form(Photo $photo): View
    {
        return view('admin.photos.form', [
            'photo' => $photo,
            'placements' => Photo::PLACEMENTS,
        ]);
    }
}