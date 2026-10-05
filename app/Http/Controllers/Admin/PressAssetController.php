<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PressAsset;
use App\Support\DocumentUploader;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PressAssetController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validateWithBag('upload', [
            'title' => ['required', 'string', 'max:120'],
            'kind' => ['required', Rule::in(array_keys(PressAsset::KINDS))],
            'file' => ['required', 'file', 'mimes:pdf,zip,jpg,jpeg,png,webp', 'max:51200'],
        ], [], ['file' => 'download file']);

        $stored = DocumentUploader::store($request->file('file'));

        PressAsset::create([
            'title' => $data['title'],
            'kind' => $data['kind'],
            'file_path' => $stored['path'],
            'original_name' => $stored['original_name'],
            'size_bytes' => $stored['size'],
            'is_published' => true,
            'sort_order' => ((int) PressAsset::max('sort_order')) + 1,
        ]);

        return redirect()
            ->route('admin.press.edit')
            ->with('status', 'Download added.');
    }

    public function update(Request $request, PressAsset $asset): RedirectResponse
    {
        $data = $request->validateWithBag('asset-'.$asset->id, [
            'title' => ['required', 'string', 'max:120'],
            'kind' => ['required', Rule::in(array_keys(PressAsset::KINDS))],
            'sort_order' => ['nullable', 'integer', 'between:0,9999'],
        ]);

        $asset->update([
            'title' => $data['title'],
            'kind' => $data['kind'],
            'sort_order' => $data['sort_order'] ?? $asset->sort_order,
            'is_published' => $request->boolean('is_published'),
        ]);

        return redirect()
            ->route('admin.press.edit')
            ->with('status', 'Download saved.');
    }

    public function destroy(PressAsset $asset): RedirectResponse
    {
        DocumentUploader::delete($asset->file_path);
        $asset->delete();

        return redirect()
            ->route('admin.press.edit')
            ->with('status', 'Download deleted.');
    }
}