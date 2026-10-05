<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PressAsset;
use App\Models\PressKit;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PressKitController extends Controller
{
    public function edit(): View
    {
        return view('admin.press.edit', [
            'press' => PressKit::current(),
            'assets' => PressAsset::ordered()->get(),
            'kinds' => PressAsset::KINDS,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'real_name' => ['nullable', 'string', 'max:120'],
            'nickname' => ['nullable', 'string', 'max:120'],
            'active_since' => ['nullable', 'integer', 'between:1990,'.now()->year],
            'genres' => ['nullable', 'string', 'max:255'],
            'awards' => ['nullable', 'string', 'max:2000'],
            'influences' => ['nullable', 'string', 'max:2000'],
            'collaborators' => ['nullable', 'string', 'max:2000'],
        ]);

        $press = PressKit::find(1) ?? new PressKit();
        $press->fill($data)->save();

        return redirect()
            ->route('admin.press.edit')
            ->with('status', 'Press kit details saved.');
    }
}