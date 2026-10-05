<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ArtistProfileRequest;
use App\Models\ArtistProfile;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class ArtistProfileController extends Controller
{
    public function edit(): View
    {
        return view('admin.profile.edit', [
            'profile' => ArtistProfile::current(),
            'platforms' => ArtistProfile::SOCIAL_PLATFORMS,
        ]);
    }

    public function update(ArtistProfileRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $data['social_links'] = array_filter(
            $data['social_links'],
            fn ($value) => filled($value)
        );

        $profile = ArtistProfile::find(1) ?? new ArtistProfile();
        $profile->id = 1;
        $profile->fill($data);
        $profile->save();

        return redirect()
            ->route('admin.profile.edit')
            ->with('status', 'Profile and contact details saved.');
    }
}