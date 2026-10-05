<?php

namespace App\Http\Requests\Admin;

use App\Models\ArtistProfile;
use Illuminate\Foundation\Http\FormRequest;

class ArtistProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        $platforms = implode(',', array_keys(ArtistProfile::SOCIAL_PLATFORMS));

        $rules = [
            'artist_name' => ['required', 'string', 'max:100'],
            'tagline' => ['nullable', 'string', 'max:150'],
            'location' => ['nullable', 'string', 'max:100'],
            'short_bio' => ['nullable', 'string', 'max:500'],
            'biography' => ['nullable', 'string', 'max:10000'],
            'award' => ['nullable', 'string', 'max:200'],
            'booking_email' => ['required', 'email', 'max:254'],
            'social_links' => ['required', 'array:'.$platforms],
        ];

        foreach (ArtistProfile::SOCIAL_PLATFORMS as $key => $label) {
            $rules["social_links.$key"] = [
                'nullable',
                'string',
                'url:https',
                'max:500',
            ];
        }

        return $rules;
    }

    public function attributes(): array
    {
        $attributes = [
            'artist_name' => 'artist name',
            'short_bio' => 'short biography',
            'booking_email' => 'booking email',
        ];

        foreach (ArtistProfile::SOCIAL_PLATFORMS as $key => $label) {
            $attributes["social_links.$key"] = "$label link";
        }

        return $attributes;
    }
}