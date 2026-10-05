<?php

namespace App\Http\Requests\Admin;

use App\Enums\MediaType;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MediaItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:150'],
            'type' => ['required', Rule::enum(MediaType::class)],
            'release_id' => ['nullable', 'integer', 'exists:releases,id'],
            'description' => ['nullable', 'string', 'max:2000'],
            'published_on' => ['nullable', 'date_format:Y-m-d'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:99999'],
            'is_published' => ['required', 'boolean'],
            'source_url' => [
                'bail',
                'required',
                'string',
                'max:500',
                'url:https',
                function (string $attribute, mixed $value, Closure $fail): void {
                    $parts = parse_url($value);
                    $host = strtolower($parts['host'] ?? '');

                    $allowed = [
                        'youtube.com',
                        'www.youtube.com',
                        'm.youtube.com',
                        'youtu.be',
                        'soundcloud.com',
                        'www.soundcloud.com',
                        'open.spotify.com',
                        'mixcloud.com',
                        'www.mixcloud.com',
                    ];

                    if (
                        ! in_array($host, $allowed, true)
                        || isset($parts['user'])
                        || isset($parts['pass'])
                        || (isset($parts['port']) && $parts['port'] !== 443)
                    ) {
                        $fail('Use an HTTPS link from YouTube, SoundCloud, Spotify or Mixcloud.');
                    }
                },
            ],
        ];
    }

    public function attributes(): array
    {
        return [
            'source_url' => 'media link',
            'release_id' => 'linked release',
            'published_on' => 'media date',
            'sort_order' => 'display order',
        ];
    }
}